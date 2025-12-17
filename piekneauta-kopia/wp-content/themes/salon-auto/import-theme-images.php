<?php
/**
 * Import Theme Images to Media Library
 * This script imports all images from the theme's /images/ folder to WordPress Media Library
 * 
 * Access via: ?import_theme_images=1 in admin
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Import theme images to media library
 */
function salon_auto_import_theme_images() {
    if (!current_user_can('manage_options')) {
        return array('error' => 'Brak uprawnień');
    }
    
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    
    // Importuj z obu folderów: /images/ i /assets/images/ (rekurencyjnie)
    $theme_images_dirs = array(
        get_stylesheet_directory() . '/images/',
        get_stylesheet_directory() . '/assets/images/'
    );
    
    $imported = 0;
    $skipped = 0;
    $errors = 0;
    $results = array();
    
    // Helper function to recursively find all image files
    $find_images_recursive = function($dir, $extensions) use (&$find_images_recursive) {
        $files = array();
        if (!is_dir($dir)) {
            return $files;
        }
        
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $path = $dir . $item;
            if (is_dir($path)) {
                // Rekurencyjnie przeszukaj podfoldery
                $subfiles = $find_images_recursive($path . '/', $extensions);
                $files = array_merge($files, $subfiles);
            } else {
                // Sprawdź czy to plik obrazu
                $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                if (in_array($ext, $extensions)) {
                    $files[] = $path;
                }
            }
        }
        return $files;
    };
    
    // Get all image files from all directories (rekurencyjnie)
    $image_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    $files = array();
    
    foreach ($theme_images_dirs as $theme_images_dir) {
        if (!is_dir($theme_images_dir)) {
            continue; // Pomiń jeśli folder nie istnieje
        }
        
        $found = $find_images_recursive($theme_images_dir, $image_extensions);
        $files = array_merge($files, $found);
    }
    
    if (empty($files)) {
        return array('error' => 'Nie znaleziono żadnych zdjęć w folderach images/ i assets/images/');
    }
    
    // Sort files
    sort($files);
    
    foreach ($files as $file_path) {
        $filename = basename($file_path);
        
        // Skip non-car images
        if (in_array($filename, array('logo.png', 'logo.svg', 'og-default.svg', 'zdjecie-o-nas.jpg'))) {
            $skipped++;
            continue;
        }
        
        // Check if image already exists in media library
        // Sprawdź po pełnej ścieżce w _wp_attached_file
        $file_basename = pathinfo($filename, PATHINFO_FILENAME);
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Sprawdź po nazwie pliku w _wp_attached_file (może być w podfolderze)
        global $wpdb;
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT p.ID FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'attachment'
            AND p.post_status = 'inherit'
            AND pm.meta_key = '_wp_attached_file'
            AND pm.meta_value LIKE %s
            LIMIT 1",
            '%' . $wpdb->esc_like($filename) . '%'
        ));
        
        // Jeśli nie znaleziono, sprawdź po tytule (tylko jeśli nazwa jest unikalna)
        if (!$existing) {
            $existing_by_title = get_posts(array(
                'post_type' => 'attachment',
                'posts_per_page' => 1,
                'post_status' => 'inherit',
                'title' => $file_basename,
                'meta_query' => array(
                    array(
                        'key' => '_wp_attached_file',
                        'value' => '.' . $file_ext,
                        'compare' => 'LIKE'
                    )
                )
            ));
            if (!empty($existing_by_title)) {
                $existing = $existing_by_title[0]->ID;
            }
        }
        
        if ($existing) {
            $skipped++;
            $results[] = array(
                'file' => $filename,
                'status' => 'skipped',
                'id' => $existing,
                'message' => 'Już istnieje w bibliotece mediów'
            );
            continue;
        }
        
        // Read file and upload to WordPress
        $file_contents = file_get_contents($file_path);
        if ($file_contents === false) {
            $errors++;
            $results[] = array(
                'file' => $filename,
                'status' => 'error',
                'message' => 'Nie można odczytać pliku'
            );
            continue;
        }
        
        // Upload file
        $upload = wp_upload_bits($filename, null, $file_contents);
        
        if ($upload['error']) {
            $errors++;
            $results[] = array(
                'file' => $filename,
                'status' => 'error',
                'message' => $upload['error']
            );
            continue;
        }
        
        // Create attachment
        $file_type = wp_check_filetype($filename);
        $attachment = array(
            'post_mime_type' => $file_type['type'],
            'post_title' => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        $attach_id = wp_insert_attachment($attachment, $upload['file']);
        
        if (is_wp_error($attach_id)) {
            $errors++;
            $results[] = array(
                'file' => $filename,
                'status' => 'error',
                'message' => $attach_id->get_error_message()
            );
            continue;
        }
        
        // Generate attachment metadata
        $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
        wp_update_attachment_metadata($attach_id, $attach_data);
        
        $imported++;
        $results[] = array(
            'file' => $filename,
            'status' => 'imported',
            'id' => $attach_id,
            'message' => 'Zaimportowano pomyślnie'
        );
    }
    
    return array(
        'imported' => $imported,
        'skipped' => $skipped,
        'errors' => $errors,
        'total' => count($files),
        'results' => $results
    );
}

/**
 * Auto-fill homepage and catalog cars with correct images
 */
function salon_auto_setup_cars_with_images() {
    if (!current_user_can('manage_options')) {
        return array('error' => 'Brak uprawnień');
    }
    
    $results = array();
    
    // Define car configurations matching static site
    $homepage_config = array(
        array(
            'slug' => 'audi-sq8-2023',
            'image_prefix' => 'audi-sq8-',
            'image_count' => 15
        ),
        array(
            'slug' => 'audi-rs5-2023-450hp-individual',
            'image_prefix' => 'rs5-new-',
            'main_image' => 'rs5-new-37.jpg',
            'image_count' => 15
        ),
        array(
            'slug' => 'audi-a8-2019-50tdi-quattro',
            'image_prefix' => 'audi-a8-',
            'image_count' => 5
        )
    );
    
    $catalog_config = array(
        array(
            'slug' => 'audi-rs5-2023-450hp-individual',
            'trim' => '450 KM Individual & Exclusive',
            'main_image' => 'rs5-new-37.jpg'
        ),
        array(
            'slug' => 'audi-sq8-2023',
            'trim' => '4.0 TFSI Quattro',
            'main_image' => 'audi-sq8-01.jpg'
        ),
        array(
            'slug' => 'audi-a6-limousine',
            'trim' => 'Limousine',
            'main_image' => 'audi-a6-01.jpg'
        ),
        array(
            'slug' => 'audi-a8-2019-50tdi-quattro',
            'trim' => '50 TDI mHEV Quattro Tiptronic',
            'main_image' => 'audi-a8-01.jpg'
        ),
        array(
            'slug' => 'bmw-seria-7-2018-730d-xdrive',
            'trim' => '740Li xDrive iPerformance',
            'main_image' => 'bmw-7-01.jpg'
        ),
        array(
            'slug' => 'cupra-formentor-2023-tfsi',
            'trim' => 'TFSI',
            'main_image' => 'cupra-formentor-01.jpg'
        )
    );
    
    // Helper function to find image ID by filename
    $find_image_id = function($filename) {
        // Check by filename in _wp_attached_file meta
        $attachments = get_posts(array(
            'post_type' => 'attachment',
            'posts_per_page' => 1,
            'post_status' => 'inherit',
            'meta_query' => array(
                array(
                    'key' => '_wp_attached_file',
                    'value' => $filename,
                    'compare' => 'LIKE'
                )
            )
        ));
        
        if (!empty($attachments)) {
            return $attachments[0]->ID;
        }
        
        // Check by title
        $title = pathinfo($filename, PATHINFO_FILENAME);
        $attachments = get_posts(array(
            'post_type' => 'attachment',
            'posts_per_page' => 1,
            'post_status' => 'inherit',
            'title' => $title
        ));
        
        if (!empty($attachments)) {
            return $attachments[0]->ID;
        }
        
        return 0;
    };
    
    // Helper to find images by prefix
    $find_images_by_prefix = function($prefix, $count = 15) use ($find_image_id) {
        $image_ids = array();
        for ($i = 1; $i <= min($count, 50); $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $filename = $prefix . $num . '.jpg';
            $id = $find_image_id($filename);
            if ($id > 0) {
                $image_ids[] = $id;
            }
            if (count($image_ids) >= $count) break;
        }
        return $image_ids;
    };
    
    // Setup homepage cars
    $homepage_cars_setup = array();
    foreach ($homepage_config as $config) {
        $post = get_page_by_path($config['slug'], OBJECT, 'car');
        if (!$post) {
            $posts = get_posts(array('post_type' => 'car', 'name' => $config['slug'], 'posts_per_page' => 1));
            $post = !empty($posts) ? $posts[0] : null;
        }
        
        if ($post) {
            // Get slider images
            $slider_image_ids = array();
            
            // If there's a specific main image, put it first
            if (isset($config['main_image'])) {
                $main_id = $find_image_id($config['main_image']);
                if ($main_id > 0) {
                    $slider_image_ids[] = $main_id;
                }
            }
            
            // Get other images by prefix
            if (isset($config['image_prefix'])) {
                $prefix_images = $find_images_by_prefix($config['image_prefix'], $config['image_count'] ?? 15);
                foreach ($prefix_images as $img_id) {
                    if (!in_array($img_id, $slider_image_ids)) {
                        $slider_image_ids[] = $img_id;
                    }
                }
            }
            
            // Limit to configured count
            $slider_image_ids = array_slice($slider_image_ids, 0, $config['image_count'] ?? 15);
            
            $homepage_cars_setup[] = array(
                'car_id' => $post->ID,
                'slider_images' => implode(',', $slider_image_ids),
                'youtube_url' => ''
            );
            
            $results['homepage'][] = array(
                'car' => $config['slug'],
                'id' => $post->ID,
                'images_count' => count($slider_image_ids),
                'image_ids' => $slider_image_ids
            );
        }
    }
    
    if (!empty($homepage_cars_setup)) {
        update_option('salon_auto_homepage_cars', $homepage_cars_setup);
    }
    
    // Setup catalog cars
    $catalog_cars_setup = array();
    foreach ($catalog_config as $config) {
        $post = get_page_by_path($config['slug'], OBJECT, 'car');
        if (!$post) {
            $posts = get_posts(array('post_type' => 'car', 'name' => $config['slug'], 'posts_per_page' => 1));
            $post = !empty($posts) ? $posts[0] : null;
        }
        
        if ($post) {
            // Find main image
            $main_image_id = 0;
            if (isset($config['main_image'])) {
                $main_image_id = $find_image_id($config['main_image']);
            }
            
            // Fallback to featured image
            if ($main_image_id === 0 && has_post_thumbnail($post->ID)) {
                $main_image_id = get_post_thumbnail_id($post->ID);
            }
            
            $catalog_cars_setup[] = array(
                'car_id' => $post->ID,
                'custom_image_id' => $main_image_id,
                'custom_caption' => $config['trim']
            );
            
            $results['catalog'][] = array(
                'car' => $config['slug'],
                'id' => $post->ID,
                'main_image_id' => $main_image_id,
                'trim' => $config['trim']
            );
        }
    }
    
    if (!empty($catalog_cars_setup)) {
        update_option('salon_auto_catalog_cars', $catalog_cars_setup);
    }
    
    return $results;
}

/**
 * Admin page for importing theme images
 */
function salon_auto_import_images_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Brak uprawnień');
    }
    
    $import_results = null;
    $setup_results = null;
    
    // Handle import action
    if (isset($_POST['import_theme_images']) && wp_verify_nonce($_POST['_wpnonce'], 'import_theme_images')) {
        $import_results = salon_auto_import_theme_images();
    }
    
    // Handle setup action
    if (isset($_POST['setup_cars']) && wp_verify_nonce($_POST['_wpnonce'], 'setup_cars')) {
        $setup_results = salon_auto_setup_cars_with_images();
    }
    
    // Get theme images count from both directories (rekurencyjnie)
    $theme_images_dirs = array(
        get_stylesheet_directory() . '/images/',
        get_stylesheet_directory() . '/assets/images/'
    );
    
    // Helper function to recursively count images
    $count_images_recursive = function($dir) use (&$count_images_recursive) {
        $count = 0;
        if (!is_dir($dir)) {
            return $count;
        }
        
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $path = $dir . $item;
            if (is_dir($path)) {
                $count += $count_images_recursive($path . '/');
            } else {
                $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp'))) {
                    $count++;
                }
            }
        }
        return $count;
    };
    
    $theme_images_count = 0;
    foreach ($theme_images_dirs as $theme_images_dir) {
        if (is_dir($theme_images_dir)) {
            $theme_images_count += $count_images_recursive($theme_images_dir);
        }
    }
    
    ?>
    <div class="wrap">
        <h1>Import zdjęć z motywu</h1>
        
        <?php if ($import_results) : ?>
        <div class="notice notice-<?php echo isset($import_results['error']) ? 'error' : 'success'; ?> is-dismissible">
            <?php if (isset($import_results['error'])) : ?>
            <p><strong>Błąd:</strong> <?php echo esc_html($import_results['error']); ?></p>
            <?php else : ?>
            <p>
                <strong>✅ Import zakończony!</strong><br>
                Zaimportowano: <strong><?php echo $import_results['imported']; ?></strong> zdjęć<br>
                Pominięto (już istnieją): <?php echo $import_results['skipped']; ?><br>
                Błędy: <?php echo $import_results['errors']; ?><br>
                Łącznie znalezionych plików: <?php echo $import_results['total']; ?>
            </p>
            <?php if (!empty($import_results['results']) && ($import_results['errors'] > 0 || $import_results['imported'] > 0)) : ?>
            <details style="margin-top: 15px;">
                <summary style="cursor: pointer; font-weight: bold;">Pokaż szczegóły importu</summary>
                <div style="max-height: 400px; overflow-y: auto; margin-top: 10px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
                    <table class="widefat" style="margin: 0;">
                        <thead>
                            <tr>
                                <th>Plik</th>
                                <th>Status</th>
                                <th>ID</th>
                                <th>Wiadomość</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($import_results['results'] as $result) : ?>
                            <tr>
                                <td><code><?php echo esc_html($result['file']); ?></code></td>
                                <td>
                                    <?php if ($result['status'] === 'imported') : ?>
                                        <span style="color: green;">✅ Zaimportowano</span>
                                    <?php elseif ($result['status'] === 'skipped') : ?>
                                        <span style="color: orange;">⏭️ Pominięto</span>
                                    <?php else : ?>
                                        <span style="color: red;">❌ Błąd</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo isset($result['id']) ? $result['id'] : '-'; ?></td>
                                <td><?php echo esc_html($result['message']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </details>
            <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($setup_results) : ?>
        <div class="notice notice-success">
            <p><strong>Ustawienia samochodów zaktualizowane!</strong></p>
            <?php if (isset($setup_results['homepage'])) : ?>
            <p>Strona główna (<?php echo count($setup_results['homepage']); ?> samochodów):</p>
            <ul>
                <?php foreach ($setup_results['homepage'] as $car) : ?>
                <li><?php echo esc_html($car['car']); ?> - <?php echo $car['images_count']; ?> zdjęć</li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <?php if (isset($setup_results['catalog'])) : ?>
            <p>Katalog (<?php echo count($setup_results['catalog']); ?> samochodów):</p>
            <ul>
                <?php foreach ($setup_results['catalog'] as $car) : ?>
                <li><?php echo esc_html($car['car']); ?> - zdjęcie ID: <?php echo $car['main_image_id']; ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
            <h2>Krok 1: Import zdjęć do biblioteki mediów</h2>
            <p>Ten krok zaimportuje wszystkie zdjęcia z folderów motywu do biblioteki mediów WordPress:</p>
            <ul style="list-style: disc; margin-left: 20px;">
                <li><code>/wp-content/themes/salon-auto/images/</code></li>
                <li><code>/wp-content/themes/salon-auto/assets/images/</code></li>
            </ul>
            <p><strong>Znalezione zdjęcia w motywie:</strong> <?php echo $theme_images_count; ?></p>
            
            <form method="post">
                <?php wp_nonce_field('import_theme_images'); ?>
                <button type="submit" name="import_theme_images" class="button button-primary">
                    📷 Importuj zdjęcia do biblioteki mediów
                </button>
            </form>
        </div>
        
        <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
            <h2>Krok 2: Automatyczne wypełnienie ustawień samochodów</h2>
            <p>Ten krok automatycznie wypełni:</p>
            <ul style="list-style: disc; margin-left: 20px;">
                <li><strong>Strona główna → Dostępne samochody</strong> (3 samochody: SQ8, RS5, A8 ze zdjęciami)</li>
                <li><strong>Samochody → Samochody w katalogu</strong> (6 samochodów z głównymi zdjęciami)</li>
            </ul>
            <p>RS5 będzie miał zdjęcie <code>rs5-new-37.jpg</code> jako główne.</p>
            
            <form method="post">
                <?php wp_nonce_field('setup_cars'); ?>
                <button type="submit" name="setup_cars" class="button button-primary">
                    🚗 Wypełnij ustawienia samochodów
                </button>
            </form>
        </div>
        
        <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
            <h2>Krok 3: Zapisz ustawienia ręcznie</h2>
            <p>Po wykonaniu powyższych kroków, przejdź do:</p>
            <ul style="list-style: disc; margin-left: 20px;">
                <li><a href="<?php echo admin_url('admin.php?page=salon-auto-homepage'); ?>">Strona Główna → Strona Główna</a> - sprawdź sekcję "Dostępne samochody"</li>
                <li><a href="<?php echo admin_url('admin.php?page=salon-auto-page-samochody'); ?>">Strona Główna → Samochody</a> - sprawdź sekcję "Samochody w katalogu"</li>
            </ul>
        </div>
    </div>
    <?php
}

// Add menu item
add_action('admin_menu', function() {
    add_submenu_page(
        'salon-auto-homepage',
        'Import zdjęć z motywu',
        '📷 Import zdjęć',
        'manage_options',
        'salon-auto-import-images',
        'salon_auto_import_images_page'
    );
});

