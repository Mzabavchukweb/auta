<?php
/**
 * Options Pages - Własny system bez ACF
 * Strony ustawień dla strony głównej i ogólnych
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add options pages to admin menu
 * Only define if not already defined (prevents conflicts)
 */
if (!function_exists('salon_auto_add_options_pages')) {
function salon_auto_add_options_pages() {
    add_menu_page(
        'Ustawienia Strony Głównej',
        'Strona Główna',
        'manage_options',
        'salon-auto-homepage',
        'salon_auto_homepage_options_page',
        'dashicons-admin-home',
        30
    );
    
    add_submenu_page(
        'salon-auto-homepage',
        'Ustawienia Ogólne',
        'Ustawienia Ogólne',
        'manage_options',
        'salon-auto-general',
        'salon_auto_general_options_page'
    );
    
    // Stary import - wyłączony (używamy import-all.php)
    // add_submenu_page(
    //     'salon-auto-homepage',
    //     'Import Danych',
    //     'Import Danych',
    //     'manage_options',
    //     'salon-auto-import',
    //     'salon_auto_import_page_old'
    // );
    
    // Add submenu pages for each page
    $pages = array(
        'o-nas' => 'O nas',
        'samochody' => 'Samochody',
        'kontakt' => 'Kontakt',
        'leasing' => 'Leasing',
        'pozyczki' => 'Pożyczki',
        'ubezpieczenia' => 'Ubezpieczenia',
        'regulamin' => 'Regulamin',
        'polityka-prywatnosci' => 'Polityka prywatności',
    );
    
    foreach ($pages as $slug => $title) {
        add_submenu_page(
            'salon-auto-homepage',
            $title,
            $title,
            'manage_options',
            'salon-auto-page-' . $slug,
            function() use ($slug, $title) {
                salon_auto_page_options_page($slug, $title);
            }
        );
    }
}
}
add_action('admin_menu', 'salon_auto_add_options_pages');

/**
 * Enqueue media uploader scripts for options pages
 * Only define if not already defined (prevents conflicts)
 */
if (!function_exists('salon_auto_enqueue_options_scripts')) {
function salon_auto_enqueue_options_scripts($hook) {
    if (strpos($hook, 'salon-auto') === false) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script('jquery');
}
}
add_action('admin_enqueue_scripts', 'salon_auto_enqueue_options_scripts');

/**
 * Auto-update cars from new folder on admin init (one time)
 * Disabled by default - use manual import instead
 */
if (!function_exists('salon_auto_auto_update_cars')) {
    function salon_auto_auto_update_cars() {
        // Disabled - use manual import instead to avoid errors
        return;
        
        // Only run once, check if already updated
        $update_flag = get_option('salon_auto_cars_updated_v2', false);
        if ($update_flag) {
            return;
        }
        
        // Check if we're in admin and user has permissions
        if (!is_admin() || !current_user_can('manage_options')) {
            return;
        }
        
        // Run import silently
        if (function_exists('salon_auto_import_cars')) {
            $result = salon_auto_import_cars();
            if (!is_wp_error($result) && function_exists('salon_auto_create_pages_from_static')) {
                salon_auto_create_pages_from_static();
                // Mark as updated
                update_option('salon_auto_cars_updated_v2', true);
            }
        }
    }
}
// Disabled - uncomment to enable auto-update
// add_action('admin_init', 'salon_auto_auto_update_cars', 999);

/**
 * Import page - allows manual trigger of import
 */
if (!function_exists('salon_auto_import_page_old')) {
        function salon_auto_import_page_old() {
            if (!current_user_can('manage_options')) {
                wp_die(__('Nie masz uprawnień do dostępu do tej strony.', 'salon-auto'));
            }
            
            $message = '';
            $message_type = '';
            
            // Force update flag reset if user clicks update
            if (isset($_POST['salon_auto_force_update']) && check_admin_referer('salon_auto_force_update_action')) {
                delete_option('salon_auto_cars_updated_v2');
                $message = __('Flaga aktualizacji zresetowana. Odśwież stronę, aby zaktualizować samochody.', 'salon-auto');
                $message_type = 'success';
            }
            
            // Detailed import with progress
            $detailed_import = false;
            $import_details = array();
            
            if (isset($_POST['salon_auto_detailed_import']) && check_admin_referer('salon_auto_import_action')) {
                $detailed_import = true;
                @set_time_limit(300);
                @ini_set('memory_limit', '256M');
                
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');
                
                // Find cars.json - use helper function if available
                $json_file = null;
                
                if (function_exists('salon_auto_get_import_paths')) {
                    $import_paths = salon_auto_get_import_paths();
                    foreach ($import_paths as $path) {
                        $test_file = $path . '/data/cars.json';
                        if (file_exists($test_file)) {
                            $json_file = $test_file;
                            break;
                        }
                    }
                }
                
                // Fallback paths if helper not available
                if (!$json_file) {
                $json_files = array(
                    get_template_directory() . '/data/cars.json',
                    dirname(dirname(dirname(__FILE__))) . '/data/cars.json',
                    );
                    
                    // Add import folder path if defined
                    if (defined('SALON_AUTO_IMPORT_FOLDER')) {
                        $json_files[] = SALON_AUTO_IMPORT_FOLDER . '/data/cars.json';
                    } else {
                        $json_files[] = dirname(dirname(dirname(__FILE__))) . '/piekneauta-kopia 2/data/cars.json';
                    }
                    
                foreach ($json_files as $file) {
                    if (file_exists($file)) {
                        $json_file = $file;
                        break;
                        }
                    }
                }
                
                if ($json_file) {
                    $json_content = file_get_contents($json_file);
                    $cars_data = json_decode($json_content, true);
                    
                    if ($cars_data && is_array($cars_data)) {
                        $imported = 0;
                        $updated = 0;
                        $errors = 0;
                        $images_added = 0;
                        
                        foreach ($cars_data as $car_data) {
                            $car_detail = array(
                                'name' => ($car_data['brand'] ?? '') . ' ' . ($car_data['model'] ?? ''),
                                'status' => '',
                                'images' => 0,
                            );
                            
                            // Check if car exists
                            $existing = get_posts(array(
                                'post_type' => 'car',
                                'name' => $car_data['slug'] ?? '',
                                'posts_per_page' => 1,
                                'post_status' => 'any'
                            ));
                            
                            $post_id = null;
                            
                            if (!empty($existing)) {
                                $post_id = $existing[0]->ID;
                                $post_data = array(
                                    'ID'           => $post_id,
                                    'post_title'   => ($car_data['brand'] ?? '') . ' ' . ($car_data['model'] ?? ''),
                                    'post_content' => $car_data['description'] ?? '',
                                    'post_name'    => $car_data['slug'] ?? sanitize_title($car_data['id'] ?? ''),
                                );
                                wp_update_post($post_data);
                                $updated++;
                                $car_detail['status'] = 'updated';
                            } else {
                                $post_data = array(
                                    'post_title'    => ($car_data['brand'] ?? '') . ' ' . ($car_data['model'] ?? ''),
                                    'post_name'     => $car_data['slug'] ?? sanitize_title($car_data['id'] ?? ''),
                                    'post_content'  => $car_data['description'] ?? '',
                                    'post_status'   => 'publish',
                                    'post_type'     => 'car',
                                );
                                
                                $post_id = wp_insert_post($post_data);
                                if (is_wp_error($post_id) || !$post_id) {
                                    $errors++;
                                    $car_detail['status'] = 'error';
                                    $import_details[] = $car_detail;
                                    continue;
                                }
                                $imported++;
                                $car_detail['status'] = 'imported';
                            }
                            
                            // Save car ID
                            if (!empty($car_data['id'])) {
                                update_post_meta($post_id, 'car_id', $car_data['id']);
                            }
                            
                            // Save all meta fields
                            $meta_fields = array(
                                'price' => $car_data['price_pln_brutto'] ?? 0,
                                'year' => $car_data['year'] ?? date('Y'),
                                'mileage' => $car_data['mileage_km'] ?? 0,
                                'gearbox' => $car_data['transmission'] ?? 'Automatyczna',
                                'fuel' => $car_data['fuel'] ?? 'Benzyna',
                                'trim' => $car_data['trim'] ?? '',
                                'brand' => $car_data['brand'] ?? '',
                                'model' => $car_data['model'] ?? '',
                                'color' => $car_data['color'] ?? '',
                                'power_hp' => $car_data['power_hp'] ?? 0,
                                'engine_cc' => $car_data['engine_cc'] ?? 0,
                                'drivetrain' => $car_data['drivetrain'] ?? 'FWD',
                                'accident_free' => ($car_data['accident_free'] ?? false) ? '1' : '0',
                                'service_history' => $car_data['service_history'] ?? '',
                                'origin' => $car_data['origin'] ?? '',
                                'owners' => $car_data['owners'] ?? 1,
                                'vin_masked' => $car_data['vin_masked'] ?? '',
                                'lease_from_pln' => $car_data['lease_from_pln'] ?? 0,
                                'status' => $car_data['status'] ?? 'available',
                                'is_featured' => '0',
                            );
                            
                            foreach ($meta_fields as $key => $value) {
                                update_post_meta($post_id, $key, $value);
                            }
                            
                            // Video
                            if (!empty($car_data['video'])) {
                                update_post_meta($post_id, 'video', $car_data['video']);
                            }
                            
                            // Equipment
                            if (!empty($car_data['equipment']) && is_array($car_data['equipment'])) {
                                update_post_meta($post_id, 'equipment', $car_data['equipment']);
                            }
                            
                            // Images
                            if (!empty($car_data['images']) && is_array($car_data['images'])) {
                                $gallery_ids = array();
                                
                                // Build image locations - use helper function if available
                                $image_locations = array();
                                
                                if (function_exists('salon_auto_get_import_paths')) {
                                    $import_paths = salon_auto_get_import_paths();
                                    foreach ($import_paths as $path) {
                                        $img_path = $path . '/images/';
                                        if (is_dir($img_path)) {
                                            $image_locations[] = $img_path;
                                        }
                                    }
                                }
                                
                                // Fallback paths
                                $image_locations = array_merge($image_locations, array(
                                    get_template_directory() . '/assets/images/',
                                    dirname(dirname(dirname(__FILE__))) . '/images/',
                                ));
                                
                                // Add import folder path if defined
                                if (defined('SALON_AUTO_IMPORT_FOLDER') && is_dir(SALON_AUTO_IMPORT_FOLDER . '/images/')) {
                                    $image_locations[] = SALON_AUTO_IMPORT_FOLDER . '/images/';
                                } else {
                                    $image_locations[] = dirname(dirname(dirname(__FILE__))) . '/piekneauta-kopia 2/images/';
                                }
                                
                                // Remove empty paths
                                $image_locations = array_filter($image_locations);
                                
                                foreach ($car_data['images'] as $index => $img_name) {
                                    $image_path = null;
                                    
                                    foreach ($image_locations as $location) {
                                        $test_path = $location . $img_name;
                                        if (file_exists($test_path)) {
                                            $image_path = $test_path;
                                            break;
                                        }
                                    }
                                    
                                    if ($image_path && file_exists($image_path)) {
                                        $file_contents = file_get_contents($image_path);
                                        if ($file_contents !== false) {
                                            $upload = wp_upload_bits(basename($image_path), null, $file_contents);
                                            
                                            if (!$upload['error']) {
                                                $attachment = array(
                                                    'post_mime_type' => wp_check_filetype(basename($image_path))['type'],
                                                    'post_title' => sanitize_file_name(basename($image_path)),
                                                    'post_content' => '',
                                                    'post_status' => 'inherit'
                                                );
                                                
                                                $attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
                                                
                                                if (!is_wp_error($attach_id)) {
                                                    $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
                                                    wp_update_attachment_metadata($attach_id, $attach_data);
                                                    
                                                    if ($index === 0) {
                                                        set_post_thumbnail($post_id, $attach_id);
                                                    }
                                                    
                                                    $gallery_ids[] = $attach_id;
                                                    $images_added++;
                                                }
                                            }
                                        }
                                    }
                                }
                                
                                if (!empty($gallery_ids)) {
                                    $gallery_array = array_slice($gallery_ids, 1);
                                    if (!empty($gallery_array)) {
                                        update_post_meta($post_id, 'gallery', $gallery_array);
                                    }
                                }
                                
                                $car_detail['images'] = count($gallery_ids);
                            }
                            
                            $import_details[] = $car_detail;
                        }
                        
                        $message = sprintf(__('Import zakończony: %d nowych, %d zaktualizowanych, %d zdjęć dodanych', 'salon-auto'), $imported, $updated, $images_added);
                        $message_type = ($errors === 0) ? 'success' : 'warning';
                    } else {
                        $message = __('Błąd parsowania pliku JSON', 'salon-auto');
                        $message_type = 'error';
                    }
                } else {
                    $message = __('Nie znaleziono pliku cars.json', 'salon-auto');
                    $message_type = 'error';
                }
            } elseif (isset($_POST['salon_auto_import_now']) && check_admin_referer('salon_auto_import_action')) {
                // Standard import (existing code)
                @set_time_limit(300);
                @ini_set('memory_limit', '256M');
                
                // Check if function exists (import-data.php may not be loaded)
                if (!function_exists('salon_auto_import_cars')) {
                    $message = __('Funkcja importu nie jest dostępna. Proszę skontaktować się z administratorem.', 'salon-auto');
                    $message_type = 'error';
                } else {
                    $imported_count = salon_auto_import_cars();
                    
                    if (is_wp_error($imported_count)) {
                        $message = sprintf(__('Błąd podczas importu samochodów: %s', 'salon-auto'), $imported_count->get_error_message());
                        $message_type = 'error';
                    } else {
                        try {
                            if (function_exists('salon_auto_create_pages_from_static')) {
                                salon_auto_create_pages_from_static();
                            }
                        } catch (Exception $e) {
                            $message = sprintf(__('Zaimportowano %d samochodów, ale wystąpił błąd podczas tworzenia stron: %s', 'salon-auto'), $imported_count, $e->getMessage());
                            $message_type = 'error';
                        }
                        
                        if ($message_type !== 'error') {
                            if ($imported_count > 0) {
                                $message = sprintf(__('Zaimportowano %d samochodów i zaktualizowano strony!', 'salon-auto'), $imported_count);
                            } else {
                                $message = __('Brak nowych samochodów do zaimportowania. Wszystkie samochody są już w bazie.', 'salon-auto');
                            }
                            $message_type = 'success';
                        }
                    }
                }
            }
            
            ?>
            <div class="wrap">
                <h1><?php esc_html_e('Import Danych', 'salon-auto'); ?></h1>
                
                <?php if ($message) : ?>
                    <div class="notice notice-<?php echo esc_attr($message_type); ?> is-dismissible">
                        <p><?php echo esc_html($message); ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="card" style="max-width: 800px;">
                    <h2><?php esc_html_e('Import samochodów i stron', 'salon-auto'); ?></h2>
                    <p><?php esc_html_e('Ta funkcja importuje:', 'salon-auto'); ?></p>
                    <ul style="list-style: disc; margin-left: 20px;">
                        <li><?php esc_html_e('Wszystkie samochody z pliku data/cars.json', 'salon-auto'); ?></li>
                        <li><?php esc_html_e('Treść wszystkich stron z plików HTML w katalogu pages/', 'salon-auto'); ?></li>
                    </ul>
                    
                    <?php
                    // Check if cars.json exists
                    $json_file1 = get_template_directory() . '/data/cars.json';
                    $json_file2 = dirname(dirname(dirname(__FILE__))) . '/data/cars.json';
                    $json_exists = file_exists($json_file1) || file_exists($json_file2);
                    $json_path = file_exists($json_file1) ? $json_file1 : (file_exists($json_file2) ? $json_file2 : '');
                    ?>
                    
                    <div style="background: #f0f0f1; padding: 15px; margin: 20px 0; border-left: 4px solid <?php echo $json_exists ? '#00a32a' : '#d63638'; ?>;">
                        <strong><?php esc_html_e('Status plików:', 'salon-auto'); ?></strong><br>
                        <?php if ($json_exists) : ?>
                            <span style="color: #00a32a;">✓</span> <?php esc_html_e('Plik cars.json znaleziony:', 'salon-auto'); ?> <code><?php echo esc_html($json_path); ?></code><br>
                            <?php if ($json_path && is_readable($json_path)) : ?>
                                <span style="color: #00a32a;">✓</span> <?php esc_html_e('Plik jest czytelny', 'salon-auto'); ?><br>
                                <?php 
                                $json_size = filesize($json_path);
                                $json_data = json_decode(file_get_contents($json_path), true);
                                if ($json_data && is_array($json_data)) {
                                    echo '<span style="color: #00a32a;">✓</span> ' . sprintf(__('Znaleziono %d samochodów w pliku', 'salon-auto'), count($json_data)) . '<br>';
                                } else {
                                    echo '<span style="color: #d63638;">✗</span> ' . __('Błąd parsowania JSON', 'salon-auto') . '<br>';
                                }
                                ?>
                            <?php else : ?>
                                <span style="color: #d63638;">✗</span> <?php esc_html_e('Plik nie jest czytelny - sprawdź uprawnienia', 'salon-auto'); ?><br>
                            <?php endif; ?>
                        <?php else : ?>
                            <span style="color: #d63638;">✗</span> <?php esc_html_e('Plik cars.json nie został znaleziony!', 'salon-auto'); ?><br>
                            <?php esc_html_e('Sprawdzane lokalizacje:', 'salon-auto'); ?><br>
                            <code><?php echo esc_html($json_file1); ?></code><br>
                            <code><?php echo esc_html($json_file2); ?></code>
                        <?php endif; ?>
                    </div>
                    
                    <form method="post" action="">
                        <?php wp_nonce_field('salon_auto_import_action'); ?>
                        <p>
                            <input type="submit" name="salon_auto_import_now" class="button button-primary button-large" value="<?php esc_attr_e('Uruchom import teraz', 'salon-auto'); ?>" <?php echo !$json_exists ? 'disabled' : ''; ?>>
                            <input type="submit" name="salon_auto_detailed_import" class="button button-secondary button-large" value="<?php esc_attr_e('Szczegółowy import z postępem', 'salon-auto'); ?>" <?php echo !$json_exists ? 'disabled' : ''; ?> style="margin-left: 10px;">
                        </p>
                        <p class="description">
                            <?php esc_html_e('"Szczegółowy import" pokaże postęp dla każdego samochodu i wszystkie dodane zdjęcia.', 'salon-auto'); ?>
                        </p>
                    </form>
                    
                    <?php if ($detailed_import && !empty($import_details)) : ?>
                        <div style="background: #fff; padding: 20px; margin: 20px 0; border: 1px solid #ddd; border-radius: 5px;">
                            <h3><?php esc_html_e('Szczegóły importu:', 'salon-auto'); ?></h3>
                            <table class="widefat" style="margin-top: 15px;">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Samochód', 'salon-auto'); ?></th>
                                        <th><?php esc_html_e('Status', 'salon-auto'); ?></th>
                                        <th><?php esc_html_e('Zdjęcia', 'salon-auto'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($import_details as $detail) : ?>
                                        <tr>
                                            <td><strong><?php echo esc_html($detail['name']); ?></strong></td>
                                            <td>
                                                <?php 
                                                if ($detail['status'] === 'imported') {
                                                    echo '<span style="color: #00a32a;">✓ ' . __('Nowy', 'salon-auto') . '</span>';
                                                } elseif ($detail['status'] === 'updated') {
                                                    echo '<span style="color: #2271b1;">↻ ' . __('Zaktualizowany', 'salon-auto') . '</span>';
                                                } else {
                                                    echo '<span style="color: #d63638;">✗ ' . __('Błąd', 'salon-auto') . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo esc_html($detail['images']); ?> <?php esc_html_e('zdjęć', 'salon-auto'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="" style="margin-top: 20px;">
                        <?php wp_nonce_field('salon_auto_force_update_action'); ?>
                        <p>
                            <input type="submit" name="salon_auto_force_update" class="button button-secondary" value="<?php esc_attr_e('Wymuś aktualizację z nowego folderu', 'salon-auto'); ?>">
                            <span class="description"><?php 
                                $import_folder_name = defined('SALON_AUTO_IMPORT_FOLDER') 
                                    ? basename(SALON_AUTO_IMPORT_FOLDER) 
                                    : 'piekneauta-kopia 2';
                                printf(esc_html__('(Zresetuje flagę i zaktualizuje samochody z folderu "%s")', 'salon-auto'), esc_html($import_folder_name)); 
                            ?></span>
                        </p>
                    </form>
                    
                    <p class="description">
                        <?php esc_html_e('Uwaga: Import może zająć kilka minut, w zależności od liczby samochodów i rozmiaru plików HTML.', 'salon-auto'); ?>
                    </p>
                </div>
            </div>
            <?php
        }
        }

/**
 * Homepage options page
 * Only define if not already defined (prevents conflicts)
 */
if (!function_exists('salon_auto_homepage_options_page')) {
function salon_auto_homepage_options_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_die(__('Nie masz uprawnień do dostępu do tej strony.', 'salon-auto'));
    }
    
    if (isset($_POST['salon_auto_save_homepage']) && check_admin_referer('salon_auto_homepage_options')) {
        $fields = array(
            'hero_title',
            'hero_subtitle',
            'about_text',
            'why_us_title',
            'trust_title',
            'trust_text',
            'cta_title',
            'cars_section_title',
            'reviews_title',
            'cert_loza_text',
            'cert_loza_url',
            'cert_rzetelna_text',
            'cert_rzetelna_url',
            'cert_description',
            'cta_button_cars_text',
            'cta_button_leasing_text',
            'cta_button_phone_text',
            'cta_button_contact_text',
            'cta_button_all_cars_text',
        );
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_option('salon_auto_' . $field, sanitize_textarea_field($_POST[$field]));
            }
        }
        
        // Why us items (repeater)
        if (isset($_POST['why_us_items']) && is_array($_POST['why_us_items'])) {
            $items = array();
            foreach ($_POST['why_us_items'] as $item) {
                if (!empty($item['title']) || !empty($item['description'])) {
                    $items[] = array(
                        'title' => sanitize_text_field($item['title'] ?? ''),
                        'description' => sanitize_textarea_field($item['description'] ?? ''),
                    );
                }
            }
            update_option('salon_auto_why_us_items', $items);
        }
        
        // Reviews (repeater)
        if (isset($_POST['reviews']) && is_array($_POST['reviews'])) {
            $reviews = array();
            foreach ($_POST['reviews'] as $review) {
                if (!empty($review['name']) || !empty($review['content'])) {
                    $reviews[] = array(
                        'name' => sanitize_text_field($review['name'] ?? ''),
                        'content' => sanitize_textarea_field($review['content'] ?? ''),
                        'source' => sanitize_text_field($review['source'] ?? ''),
                        'rating' => intval($review['rating'] ?? 5),
                    );
                }
            }
            update_option('salon_auto_reviews', $reviews);
        }
        
        // Hero images - handle media library selection
        if (isset($_POST['hero_images_hidden'])) {
            $images = sanitize_text_field($_POST['hero_images_hidden']);
            update_option('salon_auto_hero_images', $images);
        } else {
            // Keep existing if not submitted
        }
        
        // Homepage cars (max 3)
        // Save global image position
        if (isset($_POST['homepage_cars_image_position'])) {
            update_option('salon_auto_homepage_cars_image_position', sanitize_text_field($_POST['homepage_cars_image_position']));
        }
        
        if (isset($_POST['homepage_cars']) && is_array($_POST['homepage_cars'])) {
            $homepage_cars = array();
            foreach ($_POST['homepage_cars'] as $car_data) {
                if (!empty($car_data['car_id'])) {
                    $homepage_cars[] = array(
                        'car_id' => intval($car_data['car_id']),
                        'slider_images' => sanitize_text_field($car_data['slider_images'] ?? ''),
                        'youtube_url' => esc_url_raw($car_data['youtube_url'] ?? ''),
                    );
                }
            }
            // Limit to 3
            $homepage_cars = array_slice($homepage_cars, 0, 3);
            update_option('salon_auto_homepage_cars', $homepage_cars);
        }
        
        // Archive catalog cars (for Samochody page) - no limit, with custom image and caption
        if (isset($_POST['archive_catalog_cars']) && is_array($_POST['archive_catalog_cars'])) {
            $archive_catalog_cars = array();
            foreach ($_POST['archive_catalog_cars'] as $index => $car_data) {
                if (is_array($car_data)) {
                    $car_id = intval($car_data['car_id'] ?? 0);
                if ($car_id > 0) {
                        $archive_catalog_cars[] = array(
                            'car_id' => $car_id,
                            'custom_image_id' => intval($car_data['custom_image_id'] ?? 0),
                            'custom_caption' => sanitize_text_field($car_data['custom_caption'] ?? ''),
                        );
                }
                } elseif (is_numeric($car_data)) {
                    // Backward compatibility - simple car ID
                    $car_id = intval($car_data);
                    if ($car_id > 0) {
                        $archive_catalog_cars[] = array(
                            'car_id' => $car_id,
                            'custom_image_id' => 0,
                            'custom_caption' => '',
                        );
                    }
                }
            }
            update_option('salon_auto_catalog_cars', $archive_catalog_cars);
        }
        
        // Zapisz timestamp ostatniej aktualizacji
        update_option('salon_auto_last_updated', current_time('mysql'));
        
        // Wyczyść cache opcji po aktualizacji
        if (function_exists('salon_auto_clear_options_cache')) {
            salon_auto_clear_options_cache();
        }
        // Wyczyść cache samochodów (może się zmienić lista)
        wp_cache_delete('salon_auto_all_cars_list', 'salon_auto_cars');
        
        echo '<div class="notice notice-success is-dismissible"><p>✅ Ustawienia zapisane! <a href="' . esc_url(home_url('/')) . '" target="_blank" class="button button-small" style="margin-left:10px;">Zobacz stronę →</a></p></div>';
    }
    
    // Get values
    $hero_title = get_option('salon_auto_hero_title', 'Sprawdzone samochody premium i kompleksowa usługa leasingowa.');
    $hero_subtitle = get_option('salon_auto_hero_subtitle', 'Dealer aut premium z 28-letnim doświadczeniem.');
    
    // Get archive catalog cars for "Samochody" page
    $archive_catalog_cars = get_option('salon_auto_catalog_cars', array());
    $about_text = get_option('salon_auto_about_text', '');
    $why_us_title = get_option('salon_auto_why_us_title', 'Dlaczego my?');
    $why_us_items = get_option('salon_auto_why_us_items', array());
    if (empty($why_us_items)) {
        $why_us_items = array(
            array('title' => 'Bezpieczeństwo transakcji', 'description' => 'Każdy oferowany przez nas samochód posiada pewną pisemną historię od nowości.'),
            array('title' => 'Artur osobiście', 'description' => 'Bezpośredni kontakt z właścicielem. Indywidualne podejście do każdego Klienta.'),
            array('title' => '28 lat doświadczenia', 'description' => 'Od 1997 roku na rynku. Prawie trzy dekady budowania zaufania i relacji z Klientami.'),
        );
    }
    $reviews = get_option('salon_auto_reviews', array());
    $trust_title = get_option('salon_auto_trust_title', 'Jesteśmy z Wami od 1997 roku');
    $trust_text = get_option('salon_auto_trust_text', '• 28 lat doświadczenia w sprzedaży samochodów premium<br>• 10.000 zrealizowanych leasingów<br>• Pełna gama zadowolonych Klientów VIP');
    $cta_title = get_option('salon_auto_cta_title', 'Serdecznie Zapraszamy');
    $cars_section_title = get_option('salon_auto_cars_section_title', 'Dostępne samochody');
    $reviews_title = get_option('salon_auto_reviews_title', 'Opinie Klientów');
    $cert_loza_text = get_option('salon_auto_cert_loza_text', 'Członek Loży Przedsiębiorców');
    $cert_loza_url = get_option('salon_auto_cert_loza_url', 'https://lozaprzedsiebiorcow.pl');
    $cert_rzetelna_text = get_option('salon_auto_cert_rzetelna_text', 'Uczestnik Programu RZETELNA Firma');
    $cert_rzetelna_url = get_option('salon_auto_cert_rzetelna_url', ''); // Link opcjonalny
    $cert_description = get_option('salon_auto_cert_description', 'Gwarancja najwyższych standardów obsługi i transparentności');
    $hero_images_ids = get_option('salon_auto_hero_images', '');
    $cta_button_cars_text = get_option('salon_auto_cta_button_cars_text', 'Zobacz samochody');
    $cta_button_leasing_text = get_option('salon_auto_cta_button_leasing_text', 'Wycena leasingu');
    $cta_button_phone_text = get_option('salon_auto_cta_button_phone_text', 'Zadzwoń');
    $cta_button_contact_text = get_option('salon_auto_cta_button_contact_text', 'Formularz kontaktowy');
    $cta_button_all_cars_text = get_option('salon_auto_cta_button_all_cars_text', 'Sprawdź wszystkie oferty');
    $last_updated = get_option('salon_auto_last_updated', '');
    ?>
    <div class="wrap">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h1 style="margin:0;">Ustawienia Strony Głównej</h1>
            <div style="display:flex;gap:10px;align-items:center;">
                <?php if ($last_updated) : ?>
                <span style="color:#666;font-size:13px;">
                    Ostatnia aktualizacja: <?php echo esc_html(date_i18n('d.m.Y H:i', strtotime($last_updated))); ?>
                </span>
                <?php endif; ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" target="_blank" class="button button-primary">
                    👁️ Podgląd strony
                </a>
            </div>
        </div>
        
        <form method="post" action="">
            <?php wp_nonce_field('salon_auto_homepage_options'); ?>
            
            <table class="form-table">
                <tr>
                    <th><label for="hero_title">Tytuł Hero</label></th>
                    <td><input type="text" id="hero_title" name="hero_title" value="<?php echo esc_attr($hero_title); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="hero_subtitle">Podtytuł Hero</label></th>
                    <td><textarea id="hero_subtitle" name="hero_subtitle" rows="3" class="large-text"><?php echo esc_textarea($hero_subtitle); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="about_text">Tekst "O nas"</label></th>
                    <td><textarea id="about_text" name="about_text" rows="5" class="large-text"><?php echo esc_textarea($about_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="why_us_title">Tytuł sekcji "Dlaczego my?"</label></th>
                    <td><input type="text" id="why_us_title" name="why_us_title" value="<?php echo esc_attr($why_us_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>Elementy "Dlaczego my?"</th>
                    <td>
                        <div id="why_us_items">
                            <?php foreach ($why_us_items as $index => $item) : ?>
                            <div class="why-us-item" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">
                                <label>Tytuł:</label>
                                <input type="text" name="why_us_items[<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title'] ?? ''); ?>" class="regular-text" style="width: 100%; margin-bottom: 10px;">
                                <label>Opis:</label>
                                <textarea name="why_us_items[<?php echo $index; ?>][description]" rows="3" class="large-text" style="width: 100%;"><?php echo esc_textarea($item['description'] ?? ''); ?></textarea>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="button" onclick="addWhyUsItem()">Dodaj element</button>
                    </td>
                </tr>
                <tr>
                    <th><label for="reviews_title">Tytuł sekcji "Opinie Klientów"</label></th>
                    <td><input type="text" id="reviews_title" name="reviews_title" value="<?php echo esc_attr($reviews_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>Opinie Klientów</th>
                    <td>
                        <div id="reviews">
                            <?php foreach ($reviews as $index => $review) : ?>
                            <div class="review-item" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">
                                <label>Imię i nazwisko:</label>
                                <input type="text" name="reviews[<?php echo $index; ?>][name]" value="<?php echo esc_attr($review['name'] ?? ''); ?>" class="regular-text" style="width: 100%; margin-bottom: 10px;">
                                <label>Treść opinii:</label>
                                <textarea name="reviews[<?php echo $index; ?>][content]" rows="3" class="large-text" style="width: 100%; margin-bottom: 10px;"><?php echo esc_textarea($review['content'] ?? ''); ?></textarea>
                                <label>Źródło:</label>
                                <input type="text" name="reviews[<?php echo $index; ?>][source]" value="<?php echo esc_attr($review['source'] ?? ''); ?>" placeholder="Google, Facebook, OtoMoto" class="regular-text" style="width: 100%; margin-bottom: 10px;">
                                <label>Ocena (1-5):</label>
                                <input type="number" name="reviews[<?php echo $index; ?>][rating]" value="<?php echo esc_attr($review['rating'] ?? 5); ?>" min="1" max="5" style="width: 100px;">
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="button" onclick="addReview()">Dodaj opinię</button>
                    </td>
                </tr>
                <tr>
                    <th><label for="trust_title">Tytuł sekcji "Zaufanie"</label></th>
                    <td><input type="text" id="trust_title" name="trust_title" value="<?php echo esc_attr($trust_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="trust_text">Tekst sekcji "Zaufanie"</label></th>
                    <td><textarea id="trust_text" name="trust_text" rows="5" class="large-text"><?php echo esc_textarea($trust_text); ?></textarea>
                    <p class="description">Możesz używać HTML (np. &lt;br&gt; dla nowych linii)</p></td>
                </tr>
                <tr>
                    <th><label for="cta_title">Tytuł sekcji CTA</label></th>
                    <td><input type="text" id="cta_title" name="cta_title" value="<?php echo esc_attr($cta_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="cars_section_title">Tytuł sekcji "Dostępne samochody"</label></th>
                    <td><input type="text" id="cars_section_title" name="cars_section_title" value="<?php echo esc_attr($cars_section_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="cert_loza_text">Tekst certyfikatu Loża Przedsiębiorców</label></th>
                    <td><input type="text" id="cert_loza_text" name="cert_loza_text" value="<?php echo esc_attr($cert_loza_text); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="cert_loza_url">URL certyfikatu Loża Przedsiębiorców</label></th>
                    <td><input type="url" id="cert_loza_url" name="cert_loza_url" value="<?php echo esc_attr($cert_loza_url); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="cert_rzetelna_text">Tekst certyfikatu RZETELNA Firma</label></th>
                    <td><input type="text" id="cert_rzetelna_text" name="cert_rzetelna_text" value="<?php echo esc_attr($cert_rzetelna_text); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="cert_rzetelna_url">URL certyfikatu RZETELNA Firma</label></th>
                    <td><input type="url" id="cert_rzetelna_url" name="cert_rzetelna_url" value="<?php echo esc_attr($cert_rzetelna_url); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="cert_description">Opis sekcji certyfikatów</label></th>
                    <td><input type="text" id="cert_description" name="cert_description" value="<?php echo esc_attr($cert_description); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="cta_button_cars_text">Tekst przycisku "Zobacz samochody" (Hero)</label></th>
                    <td><input type="text" id="cta_button_cars_text" name="cta_button_cars_text" value="<?php echo esc_attr($cta_button_cars_text); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="cta_button_leasing_text">Tekst przycisku "Wycena leasingu" (Hero)</label></th>
                    <td><input type="text" id="cta_button_leasing_text" name="cta_button_leasing_text" value="<?php echo esc_attr($cta_button_leasing_text); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="cta_button_phone_text">Tekst przycisku telefonu (CTA)</label></th>
                    <td><input type="text" id="cta_button_phone_text" name="cta_button_phone_text" value="<?php echo esc_attr($cta_button_phone_text); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="cta_button_contact_text">Tekst przycisku formularza (CTA)</label></th>
                    <td><input type="text" id="cta_button_contact_text" name="cta_button_contact_text" value="<?php echo esc_attr($cta_button_contact_text); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="cta_button_all_cars_text">Tekst przycisku "Wszystkie oferty" (Sekcja samochodów)</label></th>
                    <td><input type="text" id="cta_button_all_cars_text" name="cta_button_all_cars_text" value="<?php echo esc_attr($cta_button_all_cars_text); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label>🖼️ Zdjęcia slidera Hero</label></th>
                    <td>
                        <div class="hero-images-section" style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px;">
                        <?php 
                        // Pobierz aktualną wartość z bazy danych
                        $hero_images_ids_db = get_option('salon_auto_hero_images', '');
                        // Zbierz tylko ID zdjęć z biblioteki mediów (nie domyślne)
                        $valid_hero_ids = array();
                        if (!empty($hero_images_ids_db)) {
                            $ids = explode(',', $hero_images_ids_db);
                            foreach ($ids as $id) {
                                $id = intval(trim($id));
                                if ($id > 0) {
                                    $valid_hero_ids[] = $id;
                                }
                            }
                        }
                        $hero_hidden_value = implode(',', $valid_hero_ids);
                        ?>
                        <input type="hidden" id="hero_images_hidden" name="hero_images_hidden" value="<?php echo esc_attr($hero_hidden_value); ?>">
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                <strong style="font-size: 14px;">🖼️ Wybrane zdjęcia (przeciągnij aby zmienić kolejność):</strong>
                                <button type="button" class="button button-primary" id="hero_images_button">+ Dodaj zdjęcia</button>
                            </div>
                            
                            <div id="hero-gallery-sortable" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;min-height:60px;padding:10px;background:#f6f7f7;border:1px solid #ddd;border-radius:4px;">
                                <?php 
                                // Pobierz zdjęcia - SPRAWDŹ WSZYSTKIE ŹRÓDŁA JAK W FRONT-PAGE.PHP
                                $hero_images_display = array();
                                
                                // 1. Sprawdź opcję salon_auto_hero_images
                                $hero_images_ids_from_option = get_option('salon_auto_hero_images', '');
                                if (!empty($hero_images_ids_from_option)) {
                                    $ids = explode(',', $hero_images_ids_from_option);
                                    foreach ($ids as $img_id_raw) {
                                        $img_id = intval(trim($img_id_raw));
                                        if ($img_id > 0) {
                                            $img_url = wp_get_attachment_image_url($img_id, 'full');
                                            if ($img_url) {
                                                $hero_images_display[] = array(
                                                    'id' => $img_id,
                                                    'url' => $img_url,
                                                    'type' => 'media_library'
                                                );
                                            }
                                        }
                                    }
                                }
                                
                                // 2. Sprawdź ACF (jeśli istnieje)
                                if (empty($hero_images_display) && function_exists('get_field')) {
                                    $acf_hero_images = get_field('hero_images', 'option');
                                    if ($acf_hero_images && is_array($acf_hero_images)) {
                                        foreach ($acf_hero_images as $acf_img) {
                                            if (isset($acf_img['ID'])) {
                                                $img_url = wp_get_attachment_image_url($acf_img['ID'], 'full');
                                                if ($img_url) {
                                                    $hero_images_display[] = array(
                                                        'id' => $acf_img['ID'],
                                                        'url' => $img_url,
                                                        'type' => 'acf'
                                                    );
                                                }
                                            } elseif (isset($acf_img['url'])) {
                                                $hero_images_display[] = array(
                                                    'id' => null,
                                                    'url' => $acf_img['url'],
                                                    'type' => 'acf_url'
                                                );
                                            }
                                        }
                                    }
                                }
                                
                                // 3. Sprawdź domyślne zdjęcia z motywu (tylko jeśli nic nie znaleziono)
                                if (empty($hero_images_display)) {
                                    $default_images = array(
                                        '/assets/images/audi-rs5-02.jpg',
                                        '/assets/images/hero/1.JPG',
                                        '/assets/images/hero/4.JPG',
                                        '/assets/images/hero/5.JPG',
                                        '/assets/images/hero/IMG_3476.JPG',
                                        '/assets/images/hero/IMG_3478.JPG',
                                        '/assets/images/hero/IMG_3601.JPG',
                                        '/assets/images/hero/IMG_3602.JPG',
                                    );
                                    foreach ($default_images as $img_path) {
                                        $img_full_path = get_stylesheet_directory() . $img_path;
                                        if (file_exists($img_full_path)) {
                                            $hero_images_display[] = array(
                                                'id' => null,
                                                'url' => get_stylesheet_directory_uri() . $img_path,
                                                'type' => 'theme_default'
                                            );
                                        }
                                    }
                                }
                                
                                // Wyświetl zdjęcia
                                if (!empty($hero_images_display)) {
                                    foreach ($hero_images_display as $i => $img_data) {
                                        $img_id = $img_data['id'];
                                        $img_url = $img_data['url'];
                                        $img_thumb = $img_id ? wp_get_attachment_image_url($img_id, 'thumbnail') : $img_url;
                                        if (!$img_thumb) $img_thumb = $img_url;
                                        
                                        // Jeśli to domyślne zdjęcie z motywu - pokaż info że można dodać własne
                                        if ($img_data['type'] === 'theme_default') {
                                            $default_url = esc_attr($img_url);
                                            echo '<div class="hero-img-item hero-default-img" data-default-url="' . $default_url . '" style="position:relative;cursor:move;opacity:0.9;" title="Przeciągnij aby zmienić kolejność, kliknij aby zastąpić zdjęciem z biblioteki">
                                                <img src="' . esc_url($img_thumb) . '" alt="" class="hero-default-img-clickable" style="width:80px;height:60px;object-fit:cover;border-radius:4px;border:2px dashed #999;cursor:move;pointer-events:none;">
                                                <span class="hero-img-num" style="position:absolute;top:2px;left:2px;background:#999;color:white;font-size:10px;padding:2px 5px;border-radius:2px;">D</span>
                                                <button type="button" class="hero-default-replace" data-default-url="' . $default_url . '" style="position:absolute;top:-8px;left:-8px;width:26px;height:26px;background:#2271b1;color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:14px;line-height:24px;text-align:center;font-weight:bold;z-index:10;" title="Zastąp zdjęciem z biblioteki">✎</button>
                                                <button type="button" class="hero-default-del" data-default-url="' . $default_url . '" style="position:absolute;top:-8px;right:-8px;width:26px;height:26px;background:#d63638;color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:18px;line-height:24px;text-align:center;font-weight:bold;z-index:10;" title="Usuń zdjęcie">×</button>
                                            </div>';
                                        } else {
                                            // Zdjęcie z biblioteki mediów - można edytować i sortować
                                            echo '<div class="hero-img-item" data-id="' . esc_attr($img_id) . '" style="position:relative;cursor:move;">
                                                <img src="' . esc_url($img_thumb) . '" alt="" class="hero-img-clickable" data-id="' . esc_attr($img_id) . '" style="width:80px;height:60px;object-fit:cover;border-radius:4px;border:2px solid #2271b1;cursor:pointer;" title="Kliknij aby zmienić zdjęcie">
                                                <span class="hero-img-num" style="position:absolute;top:2px;left:2px;background:#2271b1;color:white;font-size:10px;padding:2px 5px;border-radius:2px;">' . ($i + 1) . '</span>
                                                <button type="button" class="hero-img-edit" data-id="' . esc_attr($img_id) . '" style="position:absolute;top:-8px;left:-8px;width:26px;height:26px;background:#2271b1;color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:14px;line-height:24px;text-align:center;font-weight:bold;z-index:10;" title="Zmień zdjęcie">✎</button>
                                                <button type="button" class="hero-img-del" data-id="' . esc_attr($img_id) . '" style="position:absolute;top:-8px;right:-8px;width:26px;height:26px;background:#d63638;color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:18px;line-height:24px;text-align:center;font-weight:bold;z-index:10;">×</button>
                                            </div>';
                                        }
                                    }
                                } else {
                                    echo '<p style="margin:0;color:#666;width:100%;text-align:center;">Brak zdjęć. Kliknij przycisk "Dodaj zdjęcia" aby wybrać.</p>';
                                }
                                ?>
                            </div>
                            
                            <p class="description" style="margin-top: 12px; font-size: 12px;">Te zdjęcia będą wyświetlane w sliderze na górze strony głównej (desktop i mobile).</p>
                        </div>
                    </td>
                </tr>
            </table>
            
            <h2 style="margin-top: 40px; padding-bottom: 10px; border-bottom: 2px solid #ddd;">Sekcja "Dostępne samochody" - Strona główna</h2>
            <p class="description" style="margin-bottom: 20px;">Wybierz maksymalnie 3 samochody do wyświetlenia na stronie głównej. Dla każdego samochodu możesz wybrać zdjęcia do slidera (wielokrotny wybór z galerii). <strong>Przeciągnij samochody, aby zmienić kolejność wyświetlania.</strong></p>
            
            <table class="form-table">
                <tr>
                    <th>Pozycja obrazu (dla wszystkich samochodów)</th>
                    <td>
                        <?php 
                        $global_image_position = get_option('salon_auto_homepage_cars_image_position', 'center');
                        ?>
                        <select name="homepage_cars_image_position" style="width: 300px;">
                            <option value="center" <?php selected($global_image_position, 'center'); ?>>Środek (center)</option>
                            <option value="center top" <?php selected($global_image_position, 'center top'); ?>>Góra (center top)</option>
                            <option value="center bottom" <?php selected($global_image_position, 'center bottom'); ?>>Dół (center bottom)</option>
                            <option value="left center" <?php selected($global_image_position, 'left center'); ?>>Lewo (left center)</option>
                            <option value="right center" <?php selected($global_image_position, 'right center'); ?>>Prawo (right center)</option>
                            <option value="left top" <?php selected($global_image_position, 'left top'); ?>>Lewy górny (left top)</option>
                            <option value="right top" <?php selected($global_image_position, 'right top'); ?>>Prawy górny (right top)</option>
                            <option value="left bottom" <?php selected($global_image_position, 'left bottom'); ?>>Lewy dolny (left bottom)</option>
                            <option value="right bottom" <?php selected($global_image_position, 'right bottom'); ?>>Prawy dolny (right bottom)</option>
                        </select>
                        <p class="description">Pozycja obrazu będzie zastosowana do wszystkich samochodów na stronie głównej.</p>
                    </td>
                </tr>
            </table>
            
            <table class="form-table">
                <tr>
                    <th>Samochody na stronie głównej (max 3)</th>
                    <td>
                        <div id="homepage_cars" class="sortable-homepage-cars">
                            <?php
                            $homepage_cars = get_option('salon_auto_homepage_cars', array());
                            if (empty($homepage_cars)) {
                                // Default: SQ8, RS5, A8 (zgodnie ze statyczną wersją)
                                $homepage_cars = array(
                                    array('car_id' => '', 'slider_images' => ''),
                                    array('car_id' => '', 'slider_images' => ''),
                                    array('car_id' => '', 'slider_images' => ''),
                                );
                            }
                            // Ensure we have max 3
                            $homepage_cars = array_slice($homepage_cars, 0, 3);
                            
                            foreach ($homepage_cars as $index => $car_data) :
                                $car_id = isset($car_data['car_id']) ? intval($car_data['car_id']) : 0;
                                $slider_images = isset($car_data['slider_images']) ? $car_data['slider_images'] : '';
                                $car_post = $car_id > 0 ? get_post($car_id) : null;
                            ?>
                            <div class="homepage-car-item" data-car-index="<?php echo $index; ?>">
                                <!-- Header -->
                                <div class="section-header">
                                    <span class="car-number-badge"><?php echo $index + 1; ?></span>
                                    <h4>Samochód na stronie głównej</h4>
                                </div>
                                
                                <!-- Car selector -->
                                <select name="homepage_cars[<?php echo $index; ?>][car_id]" class="car-select" style="width: 100%; max-width: 400px; margin-bottom: 16px;" data-index="<?php echo $index; ?>">
                                    <option value="">-- Wybierz samochód --</option>
                                    <?php
                                    // Cache dla listy samochodów - poprawia wydajność
                                    $cache_key = 'salon_auto_all_cars_list';
                                    $all_cars = wp_cache_get($cache_key, 'salon_auto_cars');
                                    if (false === $all_cars) {
                                        $all_cars = get_posts(array(
                                            'post_type' => 'car',
                                            'posts_per_page' => -1,
                                            'post_status' => 'publish',
                                            'orderby' => 'title',
                                            'order' => 'ASC'
                                        ));
                                        wp_cache_set($cache_key, $all_cars, 'salon_auto_cars', HOUR_IN_SECONDS);
                                    }
                                    foreach ($all_cars as $car) :
                                        $brand = salon_auto_get_car_field($car->ID, 'brand');
                                        $model = salon_auto_get_car_field($car->ID, 'model');
                                        $car_name = $brand && $model ? $brand . ' ' . $model : $car->post_title;
                                    ?>
                                    <option value="<?php echo $car->ID; ?>" <?php selected($car_id, $car->ID); ?>>
                                        <?php echo esc_html($car_name); ?> (ID: <?php echo $car->ID; ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                
                                <?php if ($car_post) : 
                                    $car_image_url = '';
                                    if (has_post_thumbnail($car_post->ID)) {
                                        $car_image_url = wp_get_attachment_image_url(get_post_thumbnail_id($car_post->ID), 'thumbnail');
                                    } else {
                                        $gallery_ids = get_post_meta($car_post->ID, 'gallery', true);
                                        if (is_string($gallery_ids)) $gallery_ids = explode(',', $gallery_ids);
                                        if (is_array($gallery_ids) && !empty($gallery_ids)) {
                                            $first_img_id = intval($gallery_ids[0]);
                                            if ($first_img_id > 0) $car_image_url = wp_get_attachment_image_url($first_img_id, 'thumbnail');
                                            }
                                        }
                                    $brand = salon_auto_get_car_field($car_post->ID, 'brand');
                                    $model = salon_auto_get_car_field($car_post->ID, 'model');
                                    $price = salon_auto_get_car_field($car_post->ID, 'price');
                                ?>
                                <div class="car-info-compact">
                                        <?php if ($car_image_url) : ?>
                                    <img src="<?php echo esc_url($car_image_url); ?>" alt="<?php echo esc_attr($brand . ' ' . $model); ?>">
                                        <?php endif; ?>
                                    <div class="details">
                                        <strong><?php echo esc_html($brand . ' ' . $model); ?></strong><br>
                                        <?php if ($price) : ?>Cena: <?php echo number_format($price, 0, ',', ' '); ?> zł<?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Galeria zdjęć do wyboru -->
                                <div id="gallery_section_<?php echo $index; ?>" class="gallery-section" style="margin-bottom: 20px; <?php echo !$car_post ? 'display:none;' : ''; ?>">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                        <strong style="font-size: 13px;">📷 Wybierz zdjęcia do slidera:</strong>
                                        <div class="gallery-actions">
                                            <button type="button" class="button button-small select-all-images" data-index="<?php echo $index; ?>">✓ Wszystkie</button>
                                            <button type="button" class="button button-small deselect-all-images" data-index="<?php echo $index; ?>">✕ Odznacz</button>
                                        </div>
                                    </div>
                                    <div id="gallery_grid_<?php echo $index; ?>" class="gallery-grid">
                                        <!-- Zdjęcia ładowane przez AJAX -->
                                        <div class="empty-state" style="grid-column: 1/-1;">
                                            <p>Ładowanie galerii...</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- YouTube -->
                                <div style="margin-bottom: 16px;">
                                    <strong style="font-size: 13px;">🎬 Film YouTube (opcjonalnie):</strong>
                                    <?php $youtube_url = isset($car_data['youtube_url']) ? $car_data['youtube_url'] : ''; ?>
                                    <input type="url" id="youtube_url_<?php echo $index; ?>" name="homepage_cars[<?php echo $index; ?>][youtube_url]" value="<?php echo esc_attr($youtube_url); ?>" placeholder="https://www.youtube.com/watch?v=..." style="width: 100%; max-width: 500px; margin-top: 8px;">
                                    <p class="description" style="margin-top: 4px; font-size: 12px;">Film będzie wyświetlany jako pierwszy element slidera.</p>
                                </div>
                                
                                <!-- Wybrane zdjęcia - podgląd i sortowanie -->
                                <div style="margin-bottom: 16px;">
                                    <strong style="font-size: 13px;">🖼️ Wybrane zdjęcia (przeciągnij aby zmienić kolejność):</strong>
                                <input type="hidden" id="slider_images_<?php echo $index; ?>" name="homepage_cars[<?php echo $index; ?>][slider_images]" value="<?php echo esc_attr($slider_images); ?>">
                                    <div class="selected-images-container" style="margin-top: 8px;">
                                        <div id="slider_preview_<?php echo $index; ?>" class="selected-images-grid sortable-slider-gallery">
                                    <?php
                                    // Show YouTube video as first item if exists
                                    if ($youtube_url) {
                                        // Extract video ID from URL
                                        $video_id = '';
                                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $youtube_url, $matches)) {
                                            $video_id = $matches[1];
                                        }
                                        if ($video_id) {
                                            echo '<div class="selected-image-item youtube-item slider-gallery-item" data-type="youtube" data-url="' . esc_attr($youtube_url) . '">
                                                <div style="width: 100%; height: 100%; background: #000; display: flex; align-items: center; justify-content: center;">
                                                    <span style="color: white; font-size: 28px;">▶</span>
                                                </div>
                                                <span class="order-badge">YT</span>
                                                <button type="button" class="remove-btn remove-youtube-video" data-index="' . $index . '">×</button>
                                            </div>';
                                        }
                                    }
                                    
                                    // Show images
                                    if ($slider_images) {
                                        $identifiers = strpos($slider_images, '|||') !== false 
                                            ? explode('|||', $slider_images) 
                                            : explode(',', $slider_images);
                                        
                                        $start_index = $youtube_url ? 2 : 1;
                                        foreach ($identifiers as $img_index => $identifier) {
                                            $identifier = trim($identifier);
                                            if (empty($identifier)) continue;
                                            
                                            $img_url = '';
                                            $safe_identifier = rawurlencode($identifier);
                                            
                                            if (strpos($identifier, 'url:') === 0) {
                                                $img_url = substr($identifier, 4);
                                            } else {
                                                $img_id = intval($identifier);
                                            if ($img_id > 0) {
                                                $img_url = wp_get_attachment_image_url($img_id, 'thumbnail');
                                                }
                                            }
                                            
                                                if ($img_url) {
                                                $display_index = $start_index + $img_index;
                                                echo '<div class="selected-image-item slider-gallery-item" data-identifier="' . esc_attr($safe_identifier) . '">
                                                    <img src="' . esc_url($img_url) . '" alt="">
                                                    <span class="order-badge">' . $display_index . '</span>
                                                    <button type="button" class="remove-btn remove-slider-img-new" data-index="' . $index . '" data-identifier="' . esc_attr($safe_identifier) . '">×</button>
                                                    </div>';
                                            }
                                        }
                                    }
                                    ?>
                                            <?php if (empty($slider_images) && empty($youtube_url)) : ?>
                                            <div class="empty-state" style="width: 100%;">
                                                <p style="margin: 0; color: #999;">Zaznacz zdjęcia powyżej, aby dodać je do slidera</p>
                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="salon_auto_save_homepage" class="button button-primary" value="Zapisz ustawienia">
            </p>
        </form>
    </div>
    
    <?php wp_enqueue_media(); ?>
    <?php wp_enqueue_script('jquery-ui-sortable'); ?>
    <style>
    /* ============================================
       HOMEPAGE SETTINGS - CLEAN & INTUITIVE UI
       ============================================ */
    
    /* Car items */
    .homepage-car-item {
        background: #fff !important;
        border: 1px solid #e0e0e0 !important;
        border-radius: 8px !important;
        padding: 20px !important;
        margin-bottom: 20px !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08) !important;
        transition: all 0.2s ease !important;
    }
    .homepage-car-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important;
        border-color: #0073aa !important;
    }
    .homepage-car-item-placeholder {
        border: 2px dashed #0073aa !important;
        background: #f0f8ff !important;
        height: 150px !important;
        margin-bottom: 20px !important;
        border-radius: 8px !important;
    }
    
    /* Car number badge */
    .car-number-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #0073aa, #005a87);
        color: white;
        border-radius: 50%;
        font-weight: bold;
        font-size: 14px;
    }
    
    /* Section headers */
    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f0f0f0;
    }
    .section-header h4 {
        margin: 0;
        font-size: 15px;
        color: #1e1e1e;
    }
    
    /* Gallery grid */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
        gap: 8px;
        max-height: 350px;
        overflow-y: auto;
        padding: 12px;
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
    }
    
    /* Gallery item */
    .gallery-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 6px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.15s ease;
        background: #fff;
    }
    .gallery-item:hover {
        border-color: #0073aa;
        transform: scale(1.02);
    }
    .gallery-item.selected {
        border-color: #0073aa;
        box-shadow: 0 0 0 2px rgba(0, 115, 170, 0.3);
    }
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .gallery-item .checkbox-overlay {
        position: absolute;
        top: 4px;
        left: 4px;
        width: 22px;
        height: 22px;
        background: rgba(255,255,255,0.95);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .gallery-item .checkbox-overlay input {
        width: 16px;
        height: 16px;
        margin: 0;
        cursor: pointer;
    }
    .gallery-item .selected-indicator {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 22px;
        height: 22px;
        background: #0073aa;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        opacity: 0;
        transition: opacity 0.15s ease;
    }
    .gallery-item.selected .selected-indicator {
        opacity: 1;
    }
    
    /* Selected images preview */
    .selected-images-container {
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 12px;
        min-height: 100px;
    }
    .selected-images-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        min-height: 94px;
    }
    .selected-image-item {
        position: relative;
        width: 90px;
        height: 90px;
        border-radius: 6px;
        overflow: hidden;
        cursor: move;
        border: 2px solid #e0e0e0;
        background: #fff;
        flex-shrink: 0;
    }
    .selected-image-item:hover {
        border-color: #0073aa;
    }
    .selected-image-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .selected-image-item .order-badge {
        position: absolute;
        top: 4px;
        left: 4px;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        background: rgba(0,0,0,0.75);
        color: white;
        border-radius: 10px;
        font-size: 11px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .selected-image-item .remove-btn {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 24px;
        height: 24px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 1;
    }
    .selected-image-item.youtube-item {
        border-color: #ff0000;
        cursor: default;
    }
    .selected-image-item.youtube-item .order-badge {
        background: #ff0000;
    }
    .slider-gallery-item-placeholder {
        border: 2px dashed #0073aa;
        background: #f0f8ff;
    }
    
    /* Hero gallery items */
    .hero-gallery-item {
        cursor: grab !important;
    }
    .hero-gallery-item:active {
        cursor: grabbing !important;
    }
    .sortable-hero-gallery {
        min-height: 100px;
    }
    .sortable-hero-gallery.ui-sortable-helper {
        opacity: 0.8;
        transform: rotate(2deg);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    
    /* Action buttons */
    .gallery-actions {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
    }
    .gallery-actions .button {
        padding: 4px 12px !important;
        font-size: 12px !important;
    }
    
    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 30px;
        color: #666;
    }
    .empty-state svg {
        width: 48px;
        height: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }
    
    /* Compact info */
    .car-info-compact {
        display: flex;
        gap: 12px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 16px;
    }
    .car-info-compact img {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
    }
    .car-info-compact .details {
        font-size: 13px;
        line-height: 1.5;
    }
    .car-info-compact .details strong {
        color: #1e1e1e;
    }
    
    /* Scrollbar styling */
    .gallery-grid::-webkit-scrollbar {
        width: 8px;
    }
    .gallery-grid::-webkit-scrollbar-track {
        background: #f0f0f0;
        border-radius: 4px;
    }
    .gallery-grid::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }
    .gallery-grid::-webkit-scrollbar-thumb:hover {
        background: #aaa;
    }
    
    /* Hero sortable styles */
    .hero-images-section {
        overflow: visible !important;
    }
    #hero-gallery-sortable {
        min-height: 80px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        position: relative;
        overflow: visible !important;
        padding: 10px !important;
    }
    #hero-gallery-sortable .hero-img-item {
        cursor: move;
        position: relative;
        overflow: visible !important;
    }
    #hero-gallery-sortable .hero-img-item img {
        display: block;
    }
    #hero-gallery-sortable .hero-img-del {
        position: absolute !important;
        top: -8px !important;
        right: -8px !important;
        width: 26px !important;
        height: 26px !important;
        background: #d63638 !important;
        color: #fff !important;
        border: 2px solid #fff !important;
        border-radius: 50% !important;
        cursor: pointer !important;
        font-size: 18px !important;
        line-height: 22px !important;
        text-align: center !important;
        font-weight: bold !important;
        z-index: 9999 !important;
        opacity: 1 !important;
        display: flex !important;
        visibility: visible !important;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    #hero-gallery-sortable .hero-img-del:hover {
        background: #b32d2e !important;
        transform: scale(1.1);
    }
    #hero-gallery-sortable .hero-img-edit,
    #hero-gallery-sortable .hero-default-replace {
        background: #2271b1 !important;
        color: #fff !important;
        border: 2px solid #fff !important;
        border-radius: 50% !important;
        cursor: pointer !important;
        font-size: 14px !important;
        line-height: 24px !important;
        text-align: center !important;
        font-weight: bold !important;
        z-index: 9999 !important;
        opacity: 1 !important;
        display: flex !important;
        visibility: visible !important;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    #hero-gallery-sortable .hero-img-edit:hover,
    #hero-gallery-sortable .hero-default-replace:hover {
        background: #135e96 !important;
        transform: scale(1.1);
    }
    #hero-gallery-sortable .hero-default-del {
        background: #d63638 !important;
        color: #fff !important;
        border: 2px solid #fff !important;
        border-radius: 50% !important;
        cursor: pointer !important;
        font-size: 18px !important;
        line-height: 24px !important;
        text-align: center !important;
        font-weight: bold !important;
        z-index: 9999 !important;
        opacity: 1 !important;
        display: flex !important;
        visibility: visible !important;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    #hero-gallery-sortable .hero-default-del:hover {
        background: #b32d2e !important;
        transform: scale(1.1);
    }
    .hero-sortable-placeholder {
        border: 2px dashed #2271b1;
        background: #e8f4fc;
        width: 80px;
        height: 60px;
    }
    #hero-gallery-sortable .hero-img-item {
        cursor: move !important;
    }
    #hero-gallery-sortable .hero-img-item.ui-sortable-helper {
        cursor: move !important;
        opacity: 0.8;
    }
    #hero-gallery-sortable .hero-default-img img {
        pointer-events: none;
    }
    
    /* ===================================
       IPAD / TABLET RESPONSIVE STYLES
       =================================== */
    @media screen and (max-width: 1024px) {
        /* Większe przyciski */
        .button, .button-primary, .button-secondary {
            min-height: 48px !important;
            padding: 12px 20px !important;
            font-size: 14px !important;
        }
        
        /* Większe pola formularzy */
        .form-table input[type="text"],
        .form-table input[type="url"],
        .form-table input[type="number"],
        .form-table textarea,
        .form-table select {
            min-height: 48px !important;
            font-size: 16px !important;
            padding: 12px 14px !important;
        }
        
        /* Galeria - większe miniatury */
        .gallery-grid {
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)) !important;
            gap: 12px !important;
        }
        
        /* Usunięcie przycisku większe */
        .remove-btn, .hero-default-del {
            width: 36px !important;
            height: 36px !important;
            font-size: 20px !important;
            line-height: 34px !important;
        }
        
        /* Homepage car items */
        .homepage-car-item {
            padding: 25px !important;
        }
        
        /* Section headers */
        .section-header h4 {
            font-size: 16px !important;
        }
        
        /* Car number badge */
        .car-number-badge {
            width: 40px !important;
            height: 40px !important;
            font-size: 16px !important;
        }
        
        /* Hero gallery items */
        .hero-img-item img,
        .hero-default-img img {
            width: 100px !important;
            height: 75px !important;
        }
        
        /* Selected images in slider */
        .selected-images-grid {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)) !important;
            gap: 12px !important;
        }
    }
    
    @media screen and (max-width: 782px) {
        /* Form table responsywna */
        .form-table th,
        .form-table td {
            display: block !important;
            width: 100% !important;
            padding: 10px 0 !important;
        }
        .form-table th {
            padding-bottom: 5px !important;
        }
        
        /* Pełna szerokość pól */
        .form-table input[type="text"],
        .form-table input[type="url"],
        .form-table input[type="number"],
        .form-table textarea,
        .form-table select {
            width: 100% !important;
            max-width: 100% !important;
        }
        
        /* Jeszcze większe miniatury */
        .gallery-grid {
            grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)) !important;
        }
        
        /* Gallery actions stacked */
        .gallery-actions {
            flex-direction: column !important;
        }
        .gallery-actions .button {
            width: 100% !important;
            justify-content: center !important;
        }
        
        /* Hero images większe */
        .hero-img-item img,
        .hero-default-img img {
            width: 120px !important;
            height: 90px !important;
        }
    }
    
    /* Touch device optimizations */
    @media (pointer: coarse) {
        /* Większe cele dotykowe */
        .button, .button-primary, .button-secondary,
        input, select, textarea {
            min-height: 48px !important;
        }
        
        /* Usunięcie hover effects */
        .homepage-car-item:hover {
            transform: none !important;
        }
        
        /* Większe przyciski usuwania */
        .remove-btn, .hero-default-del, .del {
            width: 40px !important;
            height: 40px !important;
            font-size: 22px !important;
        }
        
        /* Lepsze scrollowanie */
        .gallery-grid,
        #gallery-sortable,
        #hero-gallery-sortable {
            -webkit-overflow-scrolling: touch;
        }
    }
    </style>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // ======================================
        // HERO SLIDER - SORTOWANIE I EDYCJA ZDJĘĆ
        // ======================================
        var heroFrame;
        
        function updateHeroGallery() {
            var items = [];
            // Zbierz tylko zdjęcia z biblioteki mediów (z data-id), pomijając domyślne
            $('#hero-gallery-sortable .hero-img-item[data-id]').each(function(index) {
                var id = $(this).data('id');
                if (id && id !== '' && id !== 'null') {
                    items.push(id.toString());
                }
            });
            // Zapisz tylko ID zdjęć z biblioteki (domyślne nie są zapisywane)
            $('#hero_images_hidden').val(items.join(','));
        }
        
        // Inicjalizacja sortable dla Hero gallery - WSZYSTKIE ZDJĘCIA (również domyślne)
        function initHeroSortable() {
            var $gallery = $('#hero-gallery-sortable');
            if ($gallery.length === 0) {
                return;
            }
            
            // Sprawdź czy są jakiekolwiek zdjęcia (z ID lub domyślne)
            var $items = $gallery.find('.hero-img-item');
            if ($items.length === 0) {
                return;
            }
            
            if ($gallery.hasClass('ui-sortable')) {
                try {
                    $gallery.sortable('destroy');
                } catch(e) {}
            }
            
            // Sortable dla wszystkich zdjęć (zarówno z data-id jak i data-default-url)
            $gallery.sortable({
                items: '.hero-img-item',
                cursor: 'move',
                tolerance: 'pointer',
                placeholder: 'hero-sortable-placeholder',
                update: function() {
                    updateHeroGallery();
                    // Zaktualizuj numery dla wszystkich zdjęć
                    $('#hero-gallery-sortable .hero-img-item').each(function(index) {
                        var $num = $(this).find('.hero-img-num');
                        if ($num.length) {
                            // Jeśli to domyślne zdjęcie, zostaw "D", w przeciwnym razie ustaw numer
                            if ($(this).hasClass('hero-default-img')) {
                                // Domyślne zdjęcie - zostaw "D"
                            } else {
                                $num.text(index + 1);
                            }
                        }
                    });
                }
            });
        }
        
        // Inicjalizuj sortable - wielokrotnie żeby na pewno zadziałało
        setTimeout(initHeroSortable, 100);
        setTimeout(initHeroSortable, 500);
        setTimeout(initHeroSortable, 1000);
        
        // Dodatkowa inicjalizacja po pełnym załadowaniu
        $(window).on('load', function() {
            setTimeout(initHeroSortable, 500);
        });
        
        // Upewnij się że przyciski są widoczne - sprawdzaj co sekundę
        function ensureHeroButtonsVisible() {
            $('#hero-gallery-sortable .hero-img-del, #hero-gallery-sortable .hero-img-edit, #hero-gallery-sortable .hero-default-replace, #hero-gallery-sortable .hero-default-del').each(function() {
                var $btn = $(this);
                $btn.css({
                    'opacity': '1',
                    'display': 'flex',
                    'visibility': 'visible',
                    'z-index': '9999'
                });
            });
        }
        
        // Sprawdzaj wielokrotnie
        setTimeout(ensureHeroButtonsVisible, 100);
        setTimeout(ensureHeroButtonsVisible, 500);
        setTimeout(ensureHeroButtonsVisible, 1000);
        setInterval(ensureHeroButtonsVisible, 2000);
        
        // USUWANIE ZDJĘĆ (z biblioteki mediów)
        $(document).on('click', '.hero-img-del', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $item = $(this).closest('.hero-img-item');
            var $gallery = $('#hero-gallery-sortable');
            
            $item.fadeOut(200, function() {
                $(this).remove();
                updateHeroGallery();
                
                // Show empty message if no images
                var $remaining = $gallery.find('.hero-img-item[data-id]').filter(function() {
                    return $(this).attr('data-id') && $(this).attr('data-id') !== '';
                });
                
                if ($remaining.length === 0 && $gallery.find('.hero-default-img').length === 0) {
                    $gallery.html('<p style="margin:0;color:#666;width:100%;text-align:center;">Brak zdjęć. Kliknij przycisk poniżej aby dodać.</p>');
                } else {
                    // Re-inicjalizuj sortable
                    setTimeout(initHeroSortable, 100);
                }
            });
        });
        
        // USUWANIE DOMYŚLNYCH ZDJĘĆ
        $(document).on('click', '.hero-default-del', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $item = $(this).closest('.hero-img-item');
            var $gallery = $('#hero-gallery-sortable');
            
            $item.fadeOut(200, function() {
                $(this).remove();
                
                // Zaktualizuj numery dla pozostałych zdjęć
                $('#hero-gallery-sortable .hero-img-item').each(function(index) {
                    var $num = $(this).find('.hero-img-num');
                    if ($num.length && !$(this).hasClass('hero-default-img')) {
                        $num.text(index + 1);
                    }
                });
                
                // Show empty message if no images
                var $remaining = $gallery.find('.hero-img-item[data-id]').filter(function() {
                    return $(this).attr('data-id') && $(this).attr('data-id') !== '';
                });
                
                if ($remaining.length === 0 && $gallery.find('.hero-default-img').length === 0) {
                    $gallery.html('<p style="margin:0;color:#666;width:100%;text-align:center;">Brak zdjęć. Kliknij przycisk poniżej aby dodać.</p>');
                } else {
                    // Re-inicjalizuj sortable
                    setTimeout(initHeroSortable, 100);
                }
            });
        });
        
        // ZASTĘPOWANIE DOMYŚLNYCH ZDJĘĆ - tylko przycisk, nie zdjęcie (żeby nie kolidowało z sortowaniem)
        $(document).on('click', '.hero-default-replace', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $item = $(this).closest('.hero-img-item');
            var defaultUrl = $item.attr('data-default-url') || $(this).attr('data-default-url');
            
            // Utwórz frame do wyboru zdjęcia
            var replaceFrame = wp.media({
                title: 'Zastąp domyślne zdjęcie',
                button: { text: 'Zastąp zdjęcie' },
                library: { type: 'image' },
                multiple: false
            });
            
            replaceFrame.on('select', function() {
                var selection = replaceFrame.state().get('selection');
                var attachment = selection.first().toJSON();
                var newId = attachment.id.toString();
                
                // Sprawdź czy nowe zdjęcie nie jest już w galerii
                var currentIds = $('#hero_images_hidden').val() ? $('#hero_images_hidden').val().split(',').filter(function(id) { return id.trim() !== ''; }) : [];
                if (currentIds.indexOf(newId) !== -1) {
                    alert('To zdjęcie jest już w sliderze!');
                    return;
                }
                
                // Zastąp domyślne zdjęcie zdjęciem z biblioteki
                var imgUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                
                // Znajdź pozycję elementu w galerii (uwzględniając wszystkie zdjęcia)
                var itemIndex = $item.index();
                
                // Dodaj nowe ID do listy
                currentIds.push(newId);
                $('#hero_images_hidden').val(currentIds.join(','));
                
                // Zamień element domyślny na element z biblioteki
                var html = '<div class="hero-img-item" data-id="' + newId + '" style="position:relative;cursor:move;">' +
                    '<img src="' + imgUrl + '" alt="" class="hero-img-clickable" data-id="' + newId + '" style="width:80px;height:60px;object-fit:cover;border-radius:4px;border:2px solid #2271b1;cursor:pointer;" title="Kliknij aby zmienić zdjęcie">' +
                    '<span class="hero-img-num" style="position:absolute;top:2px;left:2px;background:#2271b1;color:white;font-size:10px;padding:2px 5px;border-radius:2px;">' + (itemIndex + 1) + '</span>' +
                    '<button type="button" class="hero-img-edit" data-id="' + newId + '" style="position:absolute;top:-8px;left:-8px;width:26px;height:26px;background:#2271b1;color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:14px;line-height:24px;text-align:center;font-weight:bold;z-index:10;" title="Zmień zdjęcie">✎</button>' +
                    '<button type="button" class="hero-img-del" data-id="' + newId + '" style="position:absolute;top:-8px;right:-8px;width:26px;height:26px;background:#d63638;color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:18px;line-height:24px;text-align:center;font-weight:bold;z-index:10;">×</button>' +
                '</div>';
                
                $item.replaceWith(html);
                
                // Zaktualizuj numery dla wszystkich zdjęć
                $('#hero-gallery-sortable .hero-img-item').each(function(index) {
                    var $num = $(this).find('.hero-img-num');
                    if ($num.length && !$(this).hasClass('hero-default-img')) {
                        $num.text(index + 1);
                    }
                });
                
                updateHeroGallery();
                
                // Re-inicjalizuj sortable
                setTimeout(function() {
                    initHeroSortable();
                    ensureHeroButtonsVisible();
                }, 100);
            });
            
            replaceFrame.open();
        });
        
        // EDYCJA ZDJĘĆ - kliknięcie na przycisk edycji lub zdjęcie
        $(document).on('click', '.hero-img-edit, .hero-img-clickable', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $item = $(this).closest('.hero-img-item');
            var currentId = $item.attr('data-id');
            
            if (!currentId || currentId === '') {
                return;
            }
            
            // Utwórz frame do edycji pojedynczego zdjęcia
            var editFrame = wp.media({
                title: 'Zmień zdjęcie',
                button: { text: 'Zastąp zdjęcie' },
                library: { type: 'image' },
                multiple: false
            });
            
            editFrame.on('select', function() {
                var selection = editFrame.state().get('selection');
                var attachment = selection.first().toJSON();
                var newId = attachment.id.toString();
                
                // Sprawdź czy nowe zdjęcie nie jest już w galerii
                var currentIds = $('#hero_images_hidden').val() ? $('#hero_images_hidden').val().split(',').filter(function(id) { return id.trim() !== ''; }) : [];
                if (currentIds.indexOf(newId) !== -1 && newId !== currentId) {
                    alert('To zdjęcie jest już w sliderze!');
                    return;
                }
                
                // Zastąp zdjęcie
                var imgUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                var $img = $item.find('img');
                var $num = $item.find('.hero-img-num');
                var currentNum = $num.text();
                
                // Zaktualizuj element
                $item.attr('data-id', newId);
                $img.attr('data-id', newId).attr('src', imgUrl);
                $item.find('.hero-img-edit').attr('data-id', newId);
                $item.find('.hero-img-del').attr('data-id', newId);
                
                // Zaktualizuj ukryte pole
                var index = currentIds.indexOf(currentId);
                if (index !== -1) {
                    currentIds[index] = newId;
                    $('#hero_images_hidden').val(currentIds.join(','));
                }
                
                updateHeroGallery();
            });
            
            editFrame.open();
        });
        
        // DODAWANIE ZDJĘĆ
        $('#hero_images_button').on('click', function(e) {
            e.preventDefault();
            
            // Remove empty message and default images
            $('#hero-gallery-sortable p').remove();
            $('#hero-gallery-sortable .hero-default-img').remove(); // Usuń domyślne zdjęcia
            $('#hero-gallery-sortable .hero-img-item:not([data-id])').remove();
            $('#hero-gallery-sortable .hero-img-item[data-id=""]').remove();
            
            if (heroFrame) {
                heroFrame.open();
                return;
            }
            
            heroFrame = wp.media({
                title: 'Wybierz zdjęcia do slidera Hero',
                button: { text: 'Dodaj do slidera' },
                library: { type: 'image' },
                multiple: true
            });
            
            heroFrame.on('select', function() {
                // Remove empty message and default images when adding new ones
                $('#hero-gallery-sortable p').remove();
                $('#hero-gallery-sortable .hero-default-img').remove(); // Usuń domyślne zdjęcia gdy dodajemy nowe
                
                var currentIds = $('#hero_images_hidden').val() ? $('#hero_images_hidden').val().split(',').filter(function(id) { return id.trim() !== ''; }) : [];
                var selection = heroFrame.state().get('selection');
                
                selection.map(function(attachment) {
                    attachment = attachment.toJSON();
                    var id = attachment.id.toString();
                    
                    // Check if already added
                    if (currentIds.indexOf(id) === -1 && $('#hero-gallery-sortable .hero-img-item[data-id="' + id + '"]').length === 0) {
                        var imgUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                        
                        var html = '<div class="hero-img-item" data-id="' + id + '" style="position:relative;cursor:move;">' +
                            '<img src="' + imgUrl + '" alt="" class="hero-img-clickable" data-id="' + id + '" style="width:80px;height:60px;object-fit:cover;border-radius:4px;border:2px solid #2271b1;cursor:pointer;" title="Kliknij aby zmienić zdjęcie">' +
                            '<span class="hero-img-num" style="position:absolute;top:2px;left:2px;background:#2271b1;color:white;font-size:10px;padding:2px 5px;border-radius:2px;">0</span>' +
                            '<button type="button" class="hero-img-edit" data-id="' + id + '" style="position:absolute;top:-8px;left:-8px;width:26px;height:26px;background:#2271b1;color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:14px;line-height:24px;text-align:center;font-weight:bold;z-index:10;" title="Zmień zdjęcie">✎</button>' +
                            '<button type="button" class="hero-img-del" data-id="' + id + '" style="position:absolute;top:-8px;right:-8px;width:26px;height:26px;background:#d63638;color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:18px;line-height:24px;text-align:center;font-weight:bold;z-index:10;">×</button>' +
                        '</div>';
                        
                        $('#hero-gallery-sortable').append(html);
                        currentIds.push(id);
                    }
                });
                
                // Zaktualizuj numery dla wszystkich zdjęć
                $('#hero-gallery-sortable .hero-img-item').each(function(index) {
                    var $num = $(this).find('.hero-img-num');
                    if ($num.length && !$(this).hasClass('hero-default-img')) {
                        $num.text(index + 1);
                    }
                });
                
                updateHeroGallery();
                // Re-inicjalizuj sortable po dodaniu nowych zdjęć
                setTimeout(function() {
                    initHeroSortable();
                    // Wymuś widoczność przycisków
                    $('#hero-gallery-sortable .hero-img-del, #hero-gallery-sortable .hero-img-edit').css({
                        'opacity': '1 !important',
                        'display': 'flex !important',
                        'visibility': 'visible !important'
                    });
                }, 100);
            });
            
            heroFrame.open();
        });
        
        // Homepage cars - update gallery on car select
        $(document).on('change', '.car-select', function() {
            var $select = $(this);
            var carId = $select.val();
            var index = $select.data('index');
            var $container = $select.closest('.homepage-car-item');
            var $gallerySection = $('#gallery_section_' + index);
            var $galleryGrid = $('#gallery_grid_' + index);
            var $sliderInput = $('#slider_images_' + index);
            var $sliderPreview = $('#slider_preview_' + index);
            
            // Get previous car ID
            var previousCarId = $select.data('previous-car-id');
            
            // If car changed, clear slider
            if (carId && carId > 0 && previousCarId && previousCarId !== carId) {
                    $sliderInput.val('');
                $sliderPreview.find('.selected-image-item:not(.youtube-item)').remove();
                updateSliderOrderNumbers($sliderPreview);
            }
            
            $select.data('previous-car-id', carId);
            
            if (carId && carId > 0) {
                $gallerySection.show();
                $galleryGrid.html('<div class="empty-state" style="grid-column: 1/-1;"><p>Ładowanie galerii...</p></div>');
                
                // Load car gallery via AJAX
                $.ajax({
                    url: '<?php echo esc_url(rest_url('salon-auto/v1/car-gallery/')); ?>' + carId,
                    method: 'GET',
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', '<?php echo wp_create_nonce('wp_rest'); ?>');
                    },
                    success: function(gallery) {
                        if (gallery && gallery.length > 0) {
                            var currentSliderValues = $sliderInput.val() ? $sliderInput.val().split('|||').filter(function(v) { return v.trim() !== ''; }) : [];
                            
                            var galleryHtml = '';
                            gallery.forEach(function(img) {
                                var imageIdentifier = (img.hasId && img.id) ? img.id.toString() : 'url:' + img.url;
                                var isSelected = currentSliderValues.indexOf(imageIdentifier) !== -1;
                                
                                galleryHtml += '<div class="gallery-item' + (isSelected ? ' selected' : '') + '" data-identifier="' + imageIdentifier + '" data-url="' + img.url + '">';
                                galleryHtml += '<div class="checkbox-overlay"><input type="checkbox" class="car-image-checkbox" data-index="' + index + '" data-image-identifier="' + imageIdentifier + '" data-image-url="' + img.url + '"' + (isSelected ? ' checked' : '') + '></div>';
                                galleryHtml += '<img src="' + img.thumbnail + '" alt="">';
                                galleryHtml += '<div class="selected-indicator">✓</div>';
                                galleryHtml += '</div>';
                            });
                            
                            $galleryGrid.html(galleryHtml);
                        } else {
                            $galleryGrid.html('<div class="empty-state" style="grid-column: 1/-1;"><p>Brak zdjęć w galerii</p></div>');
                        }
                    },
                    error: function() {
                        $galleryGrid.html('<div class="empty-state" style="grid-column: 1/-1;"><p>Nie udało się załadować galerii</p></div>');
                    }
                });
            } else {
                $gallerySection.hide();
                    $sliderInput.val('');
                $sliderPreview.html('<div class="empty-state" style="width: 100%;"><p style="margin: 0; color: #999;">Wybierz samochód, aby zobaczyć galerię</p></div>');
            }
        });
        
        // Load galleries on page load for existing cars
        $('.car-select').each(function() {
            var carId = $(this).val();
            if (carId && carId > 0) {
                $(this).trigger('change');
            }
        });
        
        // Handle checkbox change for gallery images
        $(document).on('change', '.car-image-checkbox', function() {
            var $checkbox = $(this);
            var imageIdentifier = $checkbox.data('image-identifier');
            var imageUrl = $checkbox.data('image-url');
            var index = $checkbox.data('index');
            var isChecked = $checkbox.is(':checked');
            
            var $sliderInput = $('#slider_images_' + index);
            var $sliderPreview = $('#slider_preview_' + index);
            var $galleryItem = $checkbox.closest('.gallery-item');
            
            // Use ||| as separator
            var currentValues = $sliderInput.val() ? $sliderInput.val().split('|||').filter(function(v) { return v.trim() !== ''; }) : [];
            
            // Remove empty state message if present
            $sliderPreview.find('.empty-state').remove();
            
            if (isChecked) {
                if (currentValues.indexOf(imageIdentifier) === -1) {
                    currentValues.push(imageIdentifier);
                    $galleryItem.addClass('selected');
                    
                    // Add to selected preview
                    var imgSrc = imageUrl;
                var hasYoutube = $sliderPreview.find('.youtube-item').length > 0;
                    var orderNum = hasYoutube ? (currentValues.length + 1) : currentValues.length;
                    var safeIdentifier = encodeURIComponent(imageIdentifier);
                    
                    var $imgItem = $('<div class="selected-image-item slider-gallery-item" data-identifier="' + safeIdentifier + '">' +
                        '<img src="' + imgSrc + '" alt="">' +
                        '<span class="order-badge">' + orderNum + '</span>' +
                        '<button type="button" class="remove-btn remove-slider-img-new" data-index="' + index + '" data-identifier="' + safeIdentifier + '">×</button>' +
                    '</div>');
                    $sliderPreview.append($imgItem);
                    
                    updateSliderOrderNumbers($sliderPreview);
                    // Initialize sortable for this specific gallery if not already done
                    if (!$sliderPreview.data('sortable-initialized')) {
                        var idx = index;
                        $sliderPreview.sortable({ 
                            items: '.selected-image-item:not(.youtube-item)',
                            update: function() {
                                updateCarSlider(idx);
                            }
                        });
                        $sliderPreview.data('sortable-initialized', true);
                    }
                }
            } else {
                currentValues = currentValues.filter(function(v) { return v !== imageIdentifier; });
                $galleryItem.removeClass('selected');
                
                var safeIdentifier = encodeURIComponent(imageIdentifier);
                $sliderPreview.find('.selected-image-item[data-identifier="' + safeIdentifier + '"]').remove();
                
                updateSliderOrderNumbers($sliderPreview);
                
                // Show empty state if no images
                if ($sliderPreview.find('.selected-image-item').length === 0) {
                    $sliderPreview.html('<div class="empty-state" style="width: 100%;"><p style="margin: 0; color: #999;">Zaznacz zdjęcia powyżej, aby dodać je do slidera</p></div>');
                }
            }
            
            $sliderInput.val(currentValues.join('|||'));
        });
        
        // Helper function to update order numbers
        function updateSliderOrderNumbers($preview) {
            var hasYoutube = $preview.find('.youtube-item').length > 0;
            $preview.find('.selected-image-item:not(.youtube-item)').each(function(idx) {
                var orderNum = hasYoutube ? (idx + 2) : (idx + 1);
                $(this).find('.order-badge').text(orderNum);
            });
        }
        
        // ======================================
        // CAR SLIDER - PROSTA METODA (bez lagowania)
        // ======================================
        function updateCarSlider(index) {
            var $gallery = $('#slider_preview_' + index);
            var $hiddenInput = $('#slider_images_' + index);
            var newOrder = [];
            var $youtubeItem = $gallery.find('.youtube-item');
            
            $gallery.find('.selected-image-item:not(.youtube-item)').each(function(idx) {
                var safeIdentifier = $(this).data('identifier');
                if (safeIdentifier) {
                    newOrder.push(decodeURIComponent(safeIdentifier));
                }
                var orderNum = $youtubeItem.length > 0 ? (idx + 2) : (idx + 1);
                $(this).find('.order-badge').text(orderNum);
            });
            
            $hiddenInput.val(newOrder.join('|||'));
        }
        
        // Initialize sortable for all car sliders
            $('.sortable-slider-gallery').each(function() {
                var $gallery = $(this);
                var index = $gallery.attr('id').replace('slider_preview_', '');
                
                if ($gallery.data('sortable-initialized')) {
                return;
                }
                
                if ($('#slider_images_' + index).length && $gallery.find('.selected-image-item').length > 0) {
                    $gallery.sortable({ 
                        items: '.selected-image-item:not(.youtube-item)',
                        update: function() {
                            updateCarSlider(index);
                        }
                    });
                    $gallery.data('sortable-initialized', true);
                }
            });
        
        // Handle remove button click
        $(document).on('click', '.remove-slider-img-new', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var index = $(this).data('index');
            var safeIdentifier = $(this).data('identifier');
            var identifier = decodeURIComponent(safeIdentifier);
            
            var $sliderInput = $('#slider_images_' + index);
            var $sliderPreview = $('#slider_preview_' + index);
            
            var currentValues = $sliderInput.val() ? $sliderInput.val().split('|||').filter(function(v) { return v.trim() !== ''; }) : [];
            currentValues = currentValues.filter(function(v) { return v !== identifier; });
            $sliderInput.val(currentValues.join('|||'));
            
            $(this).closest('.selected-image-item').remove();
            updateSliderOrderNumbers($sliderPreview);
            
            // Uncheck corresponding checkbox in gallery
            var $galleryItem = $('#gallery_grid_' + index).find('.gallery-item[data-identifier="' + identifier + '"]');
            if ($galleryItem.length) {
                $galleryItem.removeClass('selected');
                $galleryItem.find('.car-image-checkbox').prop('checked', false);
            }
            
            // Show empty state if no images
            if ($sliderPreview.find('.selected-image-item').length === 0) {
                $sliderPreview.html('<div class="empty-state" style="width: 100%;"><p style="margin: 0; color: #999;">Zaznacz zdjęcia powyżej, aby dodać je do slidera</p></div>');
            }
        });
        
        // Select all images
        $(document).on('click', '.select-all-images', function(e) {
            e.preventDefault();
            var index = $(this).data('index');
            $('#gallery_grid_' + index).find('.car-image-checkbox').each(function() {
                if (!$(this).is(':checked')) {
                    $(this).prop('checked', true).trigger('change');
                }
            });
        });
        
        // Deselect all images
        $(document).on('click', '.deselect-all-images', function(e) {
            e.preventDefault();
            var index = $(this).data('index');
            $('#gallery_grid_' + index).find('.car-image-checkbox').each(function() {
                if ($(this).is(':checked')) {
                    $(this).prop('checked', false).trigger('change');
                }
            });
        });
        
        // Function to update gallery thumbnails visual state
        function updateGalleryThumbnailsState(index) {
            var $galleryGrid = $('#gallery_grid_' + index);
            var $sliderInput = $('#slider_images_' + index);
            var currentValues = $sliderInput.val() ? $sliderInput.val().split('|||').filter(function(v) { return v.trim() !== ''; }) : [];
            
            $galleryGrid.find('.gallery-item').each(function() {
                var $item = $(this);
                var identifier = $item.data('identifier');
                var isSelected = currentValues.indexOf(identifier) !== -1;
                
                $item.toggleClass('selected', isSelected);
                $item.find('.car-image-checkbox').prop('checked', isSelected);
            });
        }
        
        // Update gallery thumbnails when slider images are removed via X button
        // Remove any existing handler to avoid conflicts
        $(document).off('click', '.remove-slider-img').on('click', '.remove-slider-img', function(e) {
            e.preventDefault();
            var $button = $(this);
            var index = $button.data('index');
            var imgId = $button.data('img-id');
            var $hiddenInput = $('#slider_images_' + index);
            var $preview = $('#slider_preview_' + index);
            var $container = $button.closest('.homepage-car-item');
            var currentIds = $hiddenInput.val() ? $hiddenInput.val().split(',') : [];
            currentIds = currentIds.filter(function(n) { return n.toString() !== imgId.toString(); });
            $hiddenInput.val(currentIds.join(','));
            $button.closest('.slider-gallery-item').remove();
            // Update order numbers (accounting for YouTube)
            var hasYoutube = $preview.find('.youtube-item').length > 0;
            $preview.find('.slider-gallery-item:not(.youtube-item)').each(function(idx) {
                var orderNum = hasYoutube ? (idx + 2) : (idx + 1);
                $(this).find('.slider-order').text(orderNum);
            });
            // Update gallery thumbnails state
            updateGalleryThumbnailsState(index);
        });
        
        // Store initial car IDs on page load to detect changes
        $('.car-select').each(function() {
            var $select = $(this);
            var carId = $select.val();
            $select.data('previous-car-id', carId);
        });
        
        // Slider images selector for each homepage car
        var sliderFrames = {};
        $(document).on('click', '.select-slider-images', function(e) {
            e.preventDefault();
            var index = $(this).data('index');
            var $hiddenInput = $('#slider_images_' + index);
            var $preview = $('#slider_preview_' + index);
            var currentIds = $hiddenInput.val() ? $hiddenInput.val().split(',') : [];
            
            if (!sliderFrames[index]) {
                sliderFrames[index] = wp.media({
                    title: 'Wybierz zdjęcia do slidera dla samochodu ' + (parseInt(index) + 1),
                    button: { text: 'Użyj wybranych zdjęć' },
                    multiple: true,
                    library: { type: 'image' }
                });
                
                sliderFrames[index].on('select', function() {
                    var selection = sliderFrames[index].state().get('selection');
                    selection.map(function(attachment) {
                        attachment = attachment.toJSON();
                        var id = attachment.id.toString();
                        if (currentIds.indexOf(id) === -1) {
                            currentIds.push(id);
                            var imgUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                            // Calculate order number (accounting for YouTube video if exists)
                            var hasYoutube = $preview.find('.youtube-item').length > 0;
                            var orderNum = hasYoutube ? (currentIds.length + 1) : currentIds.length;
                            var img = $('<div class="slider-gallery-item" data-id="' + id + '" style="position: relative; cursor: move; display: inline-block; margin: 5px;"><img src="' + imgUrl + '" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #ddd; display: block;"><span class="slider-order" style="position: absolute; top: 2px; left: 2px; background: rgba(0,0,0,0.7); color: white; padding: 2px 6px; font-size: 11px; border-radius: 3px;">' + orderNum + '</span><button type="button" class="button-link remove-slider-img" data-index="' + index + '" data-img-id="' + id + '" style="position: absolute; top: 0; right: 0; background: red; color: white; border: none; padding: 2px 5px; cursor: pointer; z-index: 10;">×</button></div>');
                            $preview.append(img);
                        }
                    });
                    $hiddenInput.val(currentIds.join(','));
                    // Update gallery thumbnails state after adding images from media library
                    updateGalleryThumbnailsState(index);
                });
            }
            
            sliderFrames[index].open();
        });
        
        
        // Remove YouTube video
        $(document).on('click', '.remove-youtube-video', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var index = $(this).data('index');
            var $preview = $('#slider_preview_' + index);
            
            $('#youtube_url_' + index).val('');
            $(this).closest('.youtube-item').remove();
            
            updateSliderOrderNumbers($preview);
            
            // Show empty state if no images
            if ($preview.find('.selected-image-item').length === 0) {
                $preview.html('<div class="empty-state" style="width: 100%;"><p style="margin: 0; color: #999;">Zaznacz zdjęcia powyżej, aby dodać je do slidera</p></div>');
            }
        });
        
        // Auto-update YouTube preview when URL changes
        $(document).on('blur', 'input[id^="youtube_url_"]', function() {
            var $input = $(this);
            var index = $input.attr('id').replace('youtube_url_', '');
            var url = $input.val().trim();
            var $preview = $('#slider_preview_' + index);
            var $existingYoutube = $preview.find('.youtube-item');
            
            // Remove empty state if present
            $preview.find('.empty-state').remove();
            
            if (url) {
                var videoId = '';
                var match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
                if (match) {
                    videoId = match[1];
                }
                
                if (videoId) {
                    $existingYoutube.remove();
                    
                    var $youtubeItem = $('<div class="selected-image-item youtube-item slider-gallery-item" data-type="youtube" data-url="' + url + '">' +
                        '<div style="width: 100%; height: 100%; background: #000; display: flex; align-items: center; justify-content: center;">' +
                        '<span style="color: white; font-size: 28px;">▶</span>' +
                        '</div>' +
                        '<span class="order-badge">YT</span>' +
                        '<button type="button" class="remove-btn remove-youtube-video" data-index="' + index + '">×</button>' +
                    '</div>');
                    $preview.prepend($youtubeItem);
                    
                    updateSliderOrderNumbers($preview);
                } else {
                    alert('Nieprawidłowy link YouTube. Użyj formatu: https://www.youtube.com/watch?v=... lub https://youtu.be/...');
                }
            } else {
                $existingYoutube.remove();
                updateSliderOrderNumbers($preview);
                
                // Show empty state if no images
                if ($preview.find('.selected-image-item').length === 0) {
                    $preview.html('<div class="empty-state" style="width: 100%;"><p style="margin: 0; color: #999;">Zaznacz zdjęcia powyżej, aby dodać je do slidera</p></div>');
                }
            }
        });
        
        // Remove slider image handler is already defined above (line ~1400) to include gallery thumbnail updates
        
        // Make homepage cars sortable
        $('#homepage_cars').sortable({
            handle: '.section-header',
            placeholder: 'homepage-car-item-placeholder',
            update: function(event, ui) {
                $('#homepage_cars .homepage-car-item').each(function(index) {
                    // Update badge number
                    $(this).find('.car-number-badge').text(index + 1);
                    // Update data-car-index
                    $(this).attr('data-car-index', index);
                    // Update input names and IDs
                    $(this).find('select, input').each(function() {
                        var name = $(this).attr('name');
                        if (name) {
                            var newName = name.replace(/\[(\d+)\]/, '[' + index + ']');
                            $(this).attr('name', newName);
                            var id = $(this).attr('id');
                            if (id) {
                                var newId = id.replace(/_\d+(_|$)/, '_' + index + '$1');
                                $(this).attr('id', newId);
                            }
                        }
                    });
                    // Update data-index attributes
                    $(this).find('[data-index]').attr('data-index', index);
                    // Update gallery section IDs
                    $(this).find('.gallery-section').attr('id', 'gallery_section_' + index);
                    $(this).find('.gallery-grid').attr('id', 'gallery_grid_' + index);
                    // Update car select data
                    var $carSelect = $(this).find('.car-select');
                    $carSelect.data('previous-car-id', $carSelect.val());
                });
                
                // Re-initialize sortable for car sliders
                $('.sortable-slider-gallery').each(function() {
                    var $gallery = $(this);
                    var idx = $gallery.attr('id').replace('slider_preview_', '');
                    if ($('#slider_images_' + idx).length && !$gallery.data('sortable-initialized')) {
                            $gallery.sortable({ 
                                items: '.selected-image-item:not(.youtube-item)',
                                update: function() {
                                    updateCarSlider(idx);
                                }
                            });
                            $gallery.data('sortable-initialized', true);
                    }
                });
            }
        });
        
        // Make catalog cars sortable
        $('#catalog_cars').sortable({
            handle: '.catalog-car-item',
            placeholder: 'catalog-car-item-placeholder',
            update: function(event, ui) {
                // Update order numbers
                $('#catalog_cars .catalog-car-item').each(function(index) {
                    $(this).find('span').first().text(index + 1);
                });
            }
        });
        
        // Archive catalog cars management (for Samochody page) - with custom image and caption
        var archiveCarIndex = <?php echo isset($archive_catalog_cars) ? count($archive_catalog_cars) : 0; ?>;
        
        // Make archive catalog cars sortable
        $('#archive_catalog_cars').sortable({
            handle: '.archive-catalog-car-item',
            placeholder: 'archive-catalog-car-item-placeholder',
            update: function(event, ui) {
                // Update order numbers and input names for correct order
                $('#archive_catalog_cars .archive-catalog-car-item').each(function(index) {
                    $(this).find('.car-order-number').text(index + 1);
                    // Update input names for correct order
                    $(this).find('select, input').each(function() {
                        var name = $(this).attr('name');
                        if (name) {
                            // Replace index in name attribute (e.g., archive_catalog_cars[0][car_id] -> archive_catalog_cars[1][car_id])
                            var newName = name.replace(/\[(\d+)\]/, '[' + index + ']');
                            $(this).attr('name', newName);
                        }
                    });
                });
            }
        });
        
        // Add new car to archive catalog with image and caption support
        $('#add_archive_car').on('click', function(e) {
            e.preventDefault();
            var $container = $('#archive_catalog_cars');
            var idx = archiveCarIndex++;
            
            var html = '<div class="archive-catalog-car-item" style="margin-bottom: 20px; padding: 20px; border: 2px solid #ddd; background: #fff; border-radius: 8px; cursor: move;">' +
                '<div style="display: flex; gap: 15px; align-items: flex-start; margin-bottom: 15px;">' +
                '<span class="car-order-number" style="display: inline-block; width: 30px; height: 30px; line-height: 30px; text-align: center; background: #0073aa; color: white; border-radius: 50%; font-weight: bold; flex-shrink: 0;">' + ($container.children().length + 1) + '</span>' +
                '<div style="flex: 1;">' +
                '<label><strong>Samochód:</strong></label>' +
                '<select name="archive_catalog_cars[' + idx + '][car_id]" class="archive-catalog-car-select" style="width: 100%; margin-top: 5px;">' +
                '<option value="">-- Wybierz samochód --</option>' +
                <?php
                $all_cars_js = get_posts(array(
                    'post_type' => 'car',
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                    'orderby' => 'title',
                    'order' => 'ASC'
                ));
                $car_options_js = '';
                foreach ($all_cars_js as $car) {
                    $brand = salon_auto_get_car_field($car->ID, 'brand');
                    $model = salon_auto_get_car_field($car->ID, 'model');
                    $car_name = $brand && $model ? $brand . ' ' . $model : $car->post_title;
                    $car_options_js .= '<option value="' . $car->ID . '">' . esc_js($car_name) . '</option>';
                }
                echo "'" . $car_options_js . "'";
                ?> +
                '</select></div>' +
                '<button type="button" class="button remove-archive-car" style="flex-shrink: 0;">Usuń</button>' +
                '</div>' +
                '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; padding-left: 45px;">' +
                '<div>' +
                '<label><strong>Zdjęcie główne karty:</strong></label>' +
                '<p class="description" style="margin: 5px 0;">Pozostaw puste, aby użyć zdjęcia wyróżnionego samochodu</p>' +
                '<input type="hidden" name="archive_catalog_cars[' + idx + '][custom_image_id]" class="catalog-car-image-id" value="">' +
                '<div class="catalog-car-image-preview" style="margin: 10px 0;">' +
                '<div style="width: 120px; height: 80px; background: #f0f0f0; border: 2px dashed #ccc; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #999;">Brak zdjęcia</div>' +
                '</div>' +
                '<button type="button" class="button select-catalog-car-image">Wybierz zdjęcie</button>' +
                '<button type="button" class="button remove-catalog-car-image" style="display:none;">Usuń</button>' +
                '</div>' +
                '<div>' +
                '<label><strong>Podpis karty (opcjonalnie):</strong></label>' +
                '<p class="description" style="margin: 5px 0;">Pozostaw puste, aby użyć wersji/trimu samochodu</p>' +
                '<input type="text" name="archive_catalog_cars[' + idx + '][custom_caption]" class="regular-text" value="" placeholder="np. 4.0 TFSI Quattro" style="width: 100%; margin-top: 10px;">' +
                '</div></div></div>';
            
            $container.append(html);
            $('#archive_catalog_cars').sortable('refresh');
        });
        
        // Remove car from archive catalog
        $(document).on('click', '.remove-archive-car', function(e) {
            e.preventDefault();
            $(this).closest('.archive-catalog-car-item').remove();
            // Update order numbers
            $('#archive_catalog_cars .archive-catalog-car-item').each(function(index) {
                $(this).find('.car-order-number').text(index + 1);
            });
        });
        
        // Select image for catalog car
        $(document).on('click', '.select-catalog-car-image', function(e) {
            e.preventDefault();
            var $button = $(this);
            var $item = $button.closest('.archive-catalog-car-item');
            var $imageInput = $item.find('.catalog-car-image-id');
            var $preview = $item.find('.catalog-car-image-preview');
            var $removeBtn = $item.find('.remove-catalog-car-image');
            
            var frame = wp.media({
                title: 'Wybierz zdjęcie dla karty',
                button: { text: 'Użyj tego zdjęcia' },
                multiple: false,
                library: { type: 'image' }
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $imageInput.val(attachment.id);
                var imgUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                $preview.html('<img src="' + imgUrl + '" style="width: 120px; height: 80px; object-fit: cover; border: 2px solid #ddd; border-radius: 4px;">');
                $removeBtn.show();
            });
            
            frame.open();
        });
        
        // Remove custom image from catalog car
        $(document).on('click', '.remove-catalog-car-image', function(e) {
            e.preventDefault();
            var $item = $(this).closest('.archive-catalog-car-item');
            $item.find('.catalog-car-image-id').val('');
            $item.find('.catalog-car-image-preview').html('<div style="width: 120px; height: 80px; background: #f0f0f0; border: 2px dashed #ccc; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #999;">Brak zdjęcia</div>');
            $(this).hide();
        });
    });
    
    var whyUsIndex = <?php echo absint(count($why_us_items)); ?>;
    function addWhyUsItem() {
        var html = '<div class="why-us-item" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">' +
            '<label>Tytuł:</label>' +
            '<input type="text" name="why_us_items[' + whyUsIndex + '][title]" class="regular-text" style="width: 100%; margin-bottom: 10px;">' +
            '<label>Opis:</label>' +
            '<textarea name="why_us_items[' + whyUsIndex + '][description]" rows="3" class="large-text" style="width: 100%;"></textarea>' +
            '</div>';
        jQuery('#why_us_items').append(html);
        whyUsIndex++;
    }
    
    var reviewIndex = <?php echo absint(count($reviews)); ?>;
    function addReview() {
        var html = '<div class="review-item" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">' +
            '<label>Imię i nazwisko:</label>' +
            '<input type="text" name="reviews[' + reviewIndex + '][name]" class="regular-text" style="width: 100%; margin-bottom: 10px;">' +
            '<label>Treść opinii:</label>' +
            '<textarea name="reviews[' + reviewIndex + '][content]" rows="3" class="large-text" style="width: 100%; margin-bottom: 10px;"></textarea>' +
            '<label>Źródło:</label>' +
            '<input type="text" name="reviews[' + reviewIndex + '][source]" placeholder="Google, Facebook, OtoMoto" class="regular-text" style="width: 100%; margin-bottom: 10px;">' +
            '<label>Ocena (1-5):</label>' +
            '<input type="number" name="reviews[' + reviewIndex + '][rating]" value="5" min="1" max="5" style="width: 100px;">' +
            '</div>';
        jQuery('#reviews').append(html);
        reviewIndex++;
    }
    </script>
    <?php
}
}

/**
 * General options page
 * Only define if not already defined (prevents conflicts)
 */
if (!function_exists('salon_auto_general_options_page')) {
function salon_auto_general_options_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_die(__('Nie masz uprawnień do dostępu do tej strony.', 'salon-auto'));
    }
    
    if (isset($_POST['salon_auto_save_general']) && check_admin_referer('salon_auto_general_options')) {
        $fields = array(
            'phone',
            'email',
            'address',
            'social_facebook',
            'social_instagram',
            'social_otomoto',
            'social_tiktok',
            'footer_description',
            'footer_experience_text',
            'footer_loza_text',
            'footer_rzetelna_text',
            'company_name',
            'company_nip',
            'company_regon',
            'footer_copyright_name',
            'footer_developer_link',
            'footer_developer_text',
        );
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                if ($field === 'footer_description' || $field === 'address') {
                    update_option('salon_auto_' . $field, sanitize_textarea_field($_POST[$field]));
                } else {
                    update_option('salon_auto_' . $field, sanitize_text_field($_POST[$field]));
                }
            }
        }
        
        // Logo upload (header and footer can have different logos)
        if (isset($_POST['logo_header_id'])) {
            update_option('salon_auto_logo_header_id', absint($_POST['logo_header_id']));
        }
        if (isset($_POST['logo_footer_id'])) {
            update_option('salon_auto_logo_footer_id', absint($_POST['logo_footer_id']));
        }
        
        echo '<div class="notice notice-success"><p>Ustawienia zapisane!</p></div>';
    }
    
    // Get values
    $phone = get_option('salon_auto_phone', '502 42 82 82');
    $email = get_option('salon_auto_email', 'biuro@piekneauta.pl');
    $footer_description = get_option('salon_auto_footer_description', 'Samochody używane premium oraz fabrycznie nowe. Sprzedaż i leasing środków trwałych.');
    $footer_experience_text = get_option('salon_auto_footer_experience_text', '28 lat doświadczenia');
    $footer_loza_text = get_option('salon_auto_footer_loza_text', 'Loża Przedsiębiorców');
    $footer_rzetelna_text = get_option('salon_auto_footer_rzetelna_text', 'Uczestnik Programu RZETELNA Firma');
    $address = get_option('salon_auto_address', '');
    $social_facebook = get_option('salon_auto_social_facebook', 'https://www.facebook.com/Apmleasing');
    $social_instagram = get_option('salon_auto_social_instagram', 'https://www.instagram.com/piekne_auta_i_leasing/');
    $social_otomoto = get_option('salon_auto_social_otomoto', 'https://piekneauta.otomoto.pl');
    $social_tiktok = get_option('salon_auto_social_tiktok', 'https://www.tiktok.com/@top.cars.mleasing?_r=1&_t=ZN-91j7GS9LNFm');
    $company_name = get_option('salon_auto_company_name', get_bloginfo('name'));
    $company_nip = get_option('salon_auto_company_nip', '6731525915');
    $company_regon = get_option('salon_auto_company_regon', '330558443');
    $footer_copyright_name = get_option('salon_auto_footer_copyright_name', 'Artur Kurzydłowski');
    $footer_developer_text = get_option('salon_auto_footer_developer_text', 'Projekt i wykonanie');
    $footer_developer_link = get_option('salon_auto_footer_developer_link', 'https://www.instagram.com/codingmaks?igsh=MThzY2Roc3Npc201MA%3D%3D&utm_source=qr');
    $logo_header_id = get_option('salon_auto_logo_header_id', 0);
    $logo_footer_id = get_option('salon_auto_logo_footer_id', 0);
    ?>
    <div class="wrap">
        <h1>Ustawienia Ogólne</h1>
        <form method="post" action="">
            <?php wp_nonce_field('salon_auto_general_options'); ?>
            
            <table class="form-table">
                <tr>
                    <th><label for="phone">Telefon</label></th>
                    <td><input type="text" id="phone" name="phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="email">Email</label></th>
                    <td><input type="email" id="email" name="email" value="<?php echo esc_attr($email); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="address">Adres</label></th>
                    <td><textarea id="address" name="address" rows="3" class="large-text"><?php echo esc_textarea($address); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="social_facebook">Facebook URL</label></th>
                    <td><input type="url" id="social_facebook" name="social_facebook" value="<?php echo esc_attr($social_facebook); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="social_instagram">Instagram URL</label></th>
                    <td><input type="url" id="social_instagram" name="social_instagram" value="<?php echo esc_attr($social_instagram); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="social_otomoto">OtoMoto URL</label></th>
                    <td><input type="url" id="social_otomoto" name="social_otomoto" value="<?php echo esc_attr($social_otomoto); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="social_tiktok">TikTok URL</label></th>
                    <td><input type="url" id="social_tiktok" name="social_tiktok" value="<?php echo esc_attr($social_tiktok); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 10px 0; font-size: 18px; padding-top: 20px; border-top: 2px solid #ddd;">Logo</h2></th>
                </tr>
                <tr>
                    <th><label>Logo w nagłówku (Header)</label></th>
                    <td>
                        <input type="hidden" id="logo_header_id" name="logo_header_id" value="<?php echo esc_attr($logo_header_id); ?>">
                        <button type="button" class="button" id="logo_header_button"><?php echo $logo_header_id ? 'Zmień logo' : 'Wybierz logo'; ?></button>
                        <div id="logo_header_preview" style="margin-top: 10px;">
                            <?php if ($logo_header_id) : 
                                $logo_url = wp_get_attachment_image_url($logo_header_id, 'full');
                                if ($logo_url) :
                            ?>
                                <img src="<?php echo esc_url($logo_url); ?>" style="max-height: 100px; max-width: 300px; border: 1px solid #ddd; padding: 5px; background: white;">
                                <button type="button" class="button-link remove-logo-header" style="color: red; margin-left: 10px;">Usuń</button>
                            <?php endif; endif; ?>
                        </div>
                        <p class="description">Logo wyświetlane w nagłówku strony (górny pasek nawigacji).</p>
                    </td>
                </tr>
                <tr>
                    <th><label>Logo w stopce (Footer)</label></th>
                    <td>
                        <input type="hidden" id="logo_footer_id" name="logo_footer_id" value="<?php echo esc_attr($logo_footer_id); ?>">
                        <button type="button" class="button" id="logo_footer_button"><?php echo $logo_footer_id ? 'Zmień logo' : 'Wybierz logo'; ?></button>
                        <div id="logo_footer_preview" style="margin-top: 10px;">
                            <?php if ($logo_footer_id) : 
                                $logo_url = wp_get_attachment_image_url($logo_footer_id, 'full');
                                if ($logo_url) :
                            ?>
                                <img src="<?php echo esc_url($logo_url); ?>" style="max-height: 100px; max-width: 300px; border: 1px solid #ddd; padding: 5px; background: white; filter: brightness(0) invert(1);">
                                <button type="button" class="button-link remove-logo-footer" style="color: red; margin-left: 10px;">Usuń</button>
                            <?php endif; endif; ?>
                        </div>
                        <p class="description">Logo wyświetlane w stopce (na ciemnym tle, automatycznie odwrócone kolory). Jeśli nie wybrano, użyje logo z nagłówka.</p>
                    </td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 10px 0; font-size: 18px; padding-top: 20px; border-top: 2px solid #ddd;">Dane firmy</h2></th>
                </tr>
                <tr>
                    <th><label for="company_name">Nazwa firmy</label></th>
                    <td><input type="text" id="company_name" name="company_name" value="<?php echo esc_attr($company_name); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="company_nip">NIP</label></th>
                    <td><input type="text" id="company_nip" name="company_nip" value="<?php echo esc_attr($company_nip); ?>" class="regular-text" placeholder="6731525915"></td>
                </tr>
                <tr>
                    <th><label for="company_regon">REGON</label></th>
                    <td><input type="text" id="company_regon" name="company_regon" value="<?php echo esc_attr($company_regon); ?>" class="regular-text" placeholder="330558443"></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 10px 0; font-size: 18px; padding-top: 20px; border-top: 2px solid #ddd;">Ustawienia Stopki</h2></th>
                </tr>
                <tr>
                    <th><label for="footer_description">Opis w stopce</label></th>
                    <td><textarea id="footer_description" name="footer_description" rows="3" class="large-text"><?php echo esc_textarea($footer_description); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="footer_experience_text">Tekst "Doświadczenie" w stopce</label></th>
                    <td><input type="text" id="footer_experience_text" name="footer_experience_text" value="<?php echo esc_attr($footer_experience_text); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="footer_loza_text">Tekst "Loża Przedsiębiorców" w stopce</label></th>
                    <td><input type="text" id="footer_loza_text" name="footer_loza_text" value="<?php echo esc_attr($footer_loza_text); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="footer_rzetelna_text">Tekst "RZETELNA Firma" w stopce</label></th>
                    <td><input type="text" id="footer_rzetelna_text" name="footer_rzetelna_text" value="<?php echo esc_attr($footer_rzetelna_text); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="footer_copyright_name">Imię i nazwisko w stopce (copyright)</label></th>
                    <td><input type="text" id="footer_copyright_name" name="footer_copyright_name" value="<?php echo esc_attr($footer_copyright_name); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="footer_developer_text">Tekst "Projekt i wykonanie" w stopce</label></th>
                    <td><input type="text" id="footer_developer_text" name="footer_developer_text" value="<?php echo esc_attr($footer_developer_text); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="footer_developer_link">Link do developera (w stopce)</label></th>
                    <td><input type="url" id="footer_developer_link" name="footer_developer_link" value="<?php echo esc_attr($footer_developer_link); ?>" class="regular-text"></td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="salon_auto_save_general" class="button button-primary" value="Zapisz ustawienia">
            </p>
        </form>
    </div>
    
    <?php wp_enqueue_media(); ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Logo Header
        var logoHeaderFrame;
        $('#logo_header_button').on('click', function(e) {
            e.preventDefault();
            
            if (logoHeaderFrame) {
                logoHeaderFrame.open();
                return;
            }
            
            logoHeaderFrame = wp.media({
                title: 'Wybierz logo dla nagłówka',
                button: { text: 'Użyj tego logo' },
                multiple: false,
                library: { type: 'image' }
            });
            
            logoHeaderFrame.on('select', function() {
                var attachment = logoHeaderFrame.state().get('selection').first().toJSON();
                $('#logo_header_id').val(attachment.id);
                $('#logo_header_preview').html('<img src="' + attachment.url + '" style="max-height: 100px; max-width: 300px; border: 1px solid #ddd; padding: 5px; background: white;"><button type="button" class="button-link remove-logo-header" style="color: red; margin-left: 10px;">Usuń</button>');
                $('#logo_header_button').text('Zmień logo');
            });
            
            logoHeaderFrame.open();
        });
        
        $(document).on('click', '.remove-logo-header', function(e) {
            e.preventDefault();
            $('#logo_header_id').val('');
            $('#logo_header_preview').html('');
            $('#logo_header_button').text('Wybierz logo');
        });
        
        // Logo Footer
        var logoFooterFrame;
        $('#logo_footer_button').on('click', function(e) {
            e.preventDefault();
            
            if (logoFooterFrame) {
                logoFooterFrame.open();
                return;
            }
            
            logoFooterFrame = wp.media({
                title: 'Wybierz logo dla stopki',
                button: { text: 'Użyj tego logo' },
                multiple: false,
                library: { type: 'image' }
            });
            
            logoFooterFrame.on('select', function() {
                var attachment = logoFooterFrame.state().get('selection').first().toJSON();
                $('#logo_footer_id').val(attachment.id);
                $('#logo_footer_preview').html('<img src="' + attachment.url + '" style="max-height: 100px; max-width: 300px; border: 1px solid #ddd; padding: 5px; background: white; filter: brightness(0) invert(1);"><button type="button" class="button-link remove-logo-footer" style="color: red; margin-left: 10px;">Usuń</button>');
                $('#logo_footer_button').text('Zmień logo');
            });
            
            logoFooterFrame.open();
        });
        
        $(document).on('click', '.remove-logo-footer', function(e) {
            e.preventDefault();
            $('#logo_footer_id').val('');
            $('#logo_footer_preview').html('');
            $('#logo_footer_button').text('Wybierz logo');
        });
    });
    </script>
    <?php
}
}

/**
 * Page options page (for individual pages like O nas, Kontakt, etc.)
 * Only define if not already defined (prevents conflicts)
 */
if (!function_exists('salon_auto_page_options_page')) {
function salon_auto_page_options_page($page_slug, $page_title) {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_die(__('Nie masz uprawnień do dostępu do tej strony.', 'salon-auto'));
    }
    
    if (isset($_POST['salon_auto_save_page']) && check_admin_referer('salon_auto_page_options_' . $page_slug)) {
        // Save content
        if (isset($_POST['page_content'])) {
            update_option('salon_auto_page_' . $page_slug . '_content', wp_kses_post($_POST['page_content']));
        }
        
        // Save gallery images
        if (isset($_POST['page_gallery_hidden'])) {
            $images = sanitize_text_field($_POST['page_gallery_hidden']);
            update_option('salon_auto_page_' . $page_slug . '_gallery', $images);
        }
        
        // Save all custom fields for this page (for dedicated templates)
        // Get all POST fields that start with 'page_field_'
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'page_field_') === 0) {
                $field_name = str_replace('page_field_', '', $key);
                update_option('salon_auto_page_' . $page_slug . '_' . $field_name, wp_kses_post($value));
            }
        }
        
        // Save page images (for dedicated templates)
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'page_image_') === 0) {
                $field_name = str_replace('page_image_', '', $key);
                update_option('salon_auto_page_' . $page_slug . '_' . $field_name, absint($value));
            }
        }
        
        // Save catalog cars for "Samochody" page (archive) - with custom image and caption
        if ($page_slug === 'samochody' && isset($_POST['archive_catalog_cars']) && is_array($_POST['archive_catalog_cars'])) {
            $archive_catalog_cars = array();
            foreach ($_POST['archive_catalog_cars'] as $car_data) {
                if (is_array($car_data)) {
                    $car_id = intval($car_data['car_id'] ?? 0);
                    if ($car_id > 0) {
                        $archive_catalog_cars[] = array(
                            'car_id' => $car_id,
                            'custom_image_id' => intval($car_data['custom_image_id'] ?? 0),
                            'custom_caption' => sanitize_text_field($car_data['custom_caption'] ?? '')
                        );
                    }
                } elseif (intval($car_data) > 0) {
                    // Old format compatibility
                    $archive_catalog_cars[] = array(
                        'car_id' => intval($car_data),
                        'custom_image_id' => 0,
                        'custom_caption' => ''
                    );
                }
            }
            // No limit for archive page - can show all cars
            update_option('salon_auto_catalog_cars', $archive_catalog_cars);
        }
        
        echo '<div class="notice notice-success"><p>Ustawienia zapisane!</p></div>';
    }
    
    // Get values
    $page_content = get_option('salon_auto_page_' . $page_slug . '_content', '');
    $page_gallery_ids = get_option('salon_auto_page_' . $page_slug . '_gallery', '');
    
    // Get archive catalog cars for "Samochody" page
    $archive_catalog_cars = array();
    if ($page_slug === 'samochody') {
        $archive_catalog_cars = get_option('salon_auto_catalog_cars', array());
    }
    
    // Get all custom fields for this page
    $page_fields = array();
    $all_options = wp_load_alloptions();
    foreach ($all_options as $key => $value) {
        if (strpos($key, 'salon_auto_page_' . $page_slug . '_') === 0) {
            $field_name = str_replace('salon_auto_page_' . $page_slug . '_', '', $key);
            if ($field_name !== 'content' && $field_name !== 'gallery') {
                $page_fields[$field_name] = $value;
            }
        }
    }
    ?>
    <div class="wrap">
        <h1>Edycja strony: <?php echo esc_html($page_title); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('salon_auto_page_options_' . $page_slug); ?>
            
            <table class="form-table">
                <tr>
                    <th><label for="page_content">Treść strony</label></th>
                    <td>
                        <?php
                        wp_editor(
                            $page_content,
                            'page_content',
                            array(
                                'textarea_name' => 'page_content',
                                'textarea_rows' => 20,
                                'media_buttons' => true,
                                'teeny' => false,
                                'tinymce' => true,
                            )
                        );
                        ?>
                        <p class="description">Możesz używać edytora wizualnego do formatowania tekstu i dodawania linków.</p>
                    </td>
                </tr>
                <tr>
                    <th><label>Galeria zdjęć</label></th>
                    <td>
                        <input type="hidden" id="page_gallery_hidden_<?php echo esc_attr($page_slug); ?>" name="page_gallery_hidden" value="<?php echo esc_attr($page_gallery_ids); ?>">
                        <button type="button" class="button" id="page_gallery_button_<?php echo esc_attr($page_slug); ?>">Wybierz zdjęcia</button>
                        <div id="page_gallery_preview_<?php echo esc_attr($page_slug); ?>" class="sortable-gallery" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px;">
                            <?php
                            if ($page_gallery_ids) {
                                $ids = explode(',', $page_gallery_ids);
                                foreach ($ids as $img_id) {
                                    $img_id = intval($img_id);
                                    if ($img_id > 0) {
                                        $img_url = wp_get_attachment_image_url($img_id, 'thumbnail');
                                        if ($img_url) {
                                            echo '<div class="gallery-item" data-id="' . esc_attr($img_id) . '" style="position: relative; cursor: move;">
                                                <img src="' . esc_url($img_url) . '" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #ddd; display: block;">
                                                <span class="gallery-order" style="position: absolute; top: 2px; left: 2px; background: rgba(0,0,0,0.7); color: white; padding: 2px 6px; font-size: 11px; border-radius: 3px;">' . (array_search($img_id, $ids) + 1) . '</span>
                                                <button type="button" class="button-link remove-page-img" data-id="' . esc_attr($img_id) . '" data-slug="' . esc_attr($page_slug) . '" style="position: absolute; top: 0; right: 0; background: red; color: white; border: none; padding: 2px 5px; cursor: pointer; z-index: 10;">×</button>
                                            </div>';
                                        }
                                    }
                                }
                            }
                            ?>
                        </div>
                        <p class="description">Wybierz zdjęcia do wyświetlenia na stronie (galeria). Możesz przeciągać zdjęcia, aby zmienić kolejność.</p>
                    </td>
                </tr>
                
                <?php
                // Show additional fields for dedicated templates
                if ($page_slug === 'samochody') {
                    // Get values for "Samochody" page
                    $samochody_hero_title = get_option('salon_auto_page_samochody_hero_title', 'Dostępne samochody');
                    $samochody_hero_subtitle = get_option('salon_auto_page_samochody_hero_subtitle', 'Wszystkie auta sprawdzone i gotowe do odbioru');
                    $archive_catalog_cars = get_option('salon_auto_catalog_cars', array());
                    
                    // Convert old format
                    $archive_catalog_cars = array_map(function($item) {
                        if (is_array($item)) return $item;
                        return array('car_id' => intval($item), 'custom_image_id' => 0, 'custom_caption' => '');
                    }, $archive_catalog_cars);
                ?>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Nagłówek strony</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_hero_title">Tytuł</label></th>
                    <td><input type="text" id="page_field_hero_title" name="page_field_hero_title" value="<?php echo esc_attr($samochody_hero_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hero_subtitle">Podtytuł</label></th>
                    <td><textarea id="page_field_hero_subtitle" name="page_field_hero_subtitle" rows="2" class="large-text"><?php echo esc_textarea($samochody_hero_subtitle); ?></textarea></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Samochody w katalogu</h2></th>
                </tr>
                <tr>
                    <th>Wybierz samochody</th>
                    <td>
                        <p class="description" style="margin-bottom: 15px;">Wybierz samochody do wyświetlenia. Przeciągnij aby zmienić kolejność.</p>
                        <div id="archive_catalog_cars" style="margin-bottom: 20px;">
                            <?php 
                            foreach ($archive_catalog_cars as $index => $car_data) :
                                $car_id = intval($car_data['car_id'] ?? 0);
                                $custom_image_id = intval($car_data['custom_image_id'] ?? 0);
                                $custom_caption = $car_data['custom_caption'] ?? '';
                                $car_post = $car_id > 0 ? get_post($car_id) : null;
                            ?>
                            <div class="archive-catalog-car-item" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9; border-radius: 4px; cursor: move;">
                                <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 10px;">
                                    <span class="car-order-number" style="background: #0073aa; color: white; padding: 5px 10px; border-radius: 3px; font-weight: bold;"><?php echo $index + 1; ?></span>
                                    <select name="archive_catalog_cars[<?php echo $index; ?>][car_id]" class="archive-catalog-car-select" style="flex: 1;">
                                        <option value="">-- Wybierz samochód --</option>
                                        <?php
                                        // Cache dla listy samochodów - poprawia wydajność
                                        $cache_key = 'salon_auto_all_cars_list';
                                        $all_cars = wp_cache_get($cache_key, 'salon_auto_cars');
                                        if (false === $all_cars) {
                                            $all_cars = get_posts(array('post_type' => 'car', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC'));
                                            wp_cache_set($cache_key, $all_cars, 'salon_auto_cars', HOUR_IN_SECONDS);
                                        }
                                        foreach ($all_cars as $car) :
                                            $brand = salon_auto_get_car_field($car->ID, 'brand');
                                            $model = salon_auto_get_car_field($car->ID, 'model');
                                            $car_name = $brand && $model ? $brand . ' ' . $model : $car->post_title;
                                        ?>
                                        <option value="<?php echo $car->ID; ?>" <?php selected($car_id, $car->ID); ?>><?php echo esc_html($car_name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="button remove-archive-car">Usuń</button>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; padding-left: 50px;">
                                    <div>
                                        <label>Zdjęcie karty (opcjonalne):</label>
                                        <input type="hidden" name="archive_catalog_cars[<?php echo $index; ?>][custom_image_id]" class="catalog-car-image-id" value="<?php echo esc_attr($custom_image_id); ?>">
                                        <div class="catalog-car-image-preview" style="margin: 5px 0;">
                                            <?php if ($custom_image_id > 0 && ($preview_url = wp_get_attachment_image_url($custom_image_id, 'thumbnail'))) : ?>
                                            <img src="<?php echo esc_url($preview_url); ?>" style="width: 80px; height: 60px; object-fit: cover; border: 1px solid #ddd;">
                                            <?php else : ?>
                                            <span style="color: #666; font-size: 12px;">Domyślne zdjęcie</span>
                                            <?php endif; ?>
                                        </div>
                                        <button type="button" class="button button-small select-catalog-car-image">Wybierz</button>
                                        <button type="button" class="button button-small remove-catalog-car-image" <?php echo $custom_image_id ? '' : 'style="display:none;"'; ?>>Usuń</button>
                                    </div>
                                    <div>
                                        <label>Podpis karty (opcjonalne):</label>
                                        <input type="text" name="archive_catalog_cars[<?php echo $index; ?>][custom_caption]" value="<?php echo esc_attr($custom_caption); ?>" placeholder="np. 4.0 TFSI Quattro" style="width: 100%; margin-top: 5px;">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add_archive_car">+ Dodaj samochód</button>
                    </td>
                </tr>
                <?php } elseif ($page_slug === 'o-nas') {
                    // Get all fields for "O nas" page
                    $about_hero_title = get_option('salon_auto_page_o-nas_hero_title', '28 lat pasji i doświadczenia');
                    $about_hero_text = get_option('salon_auto_page_o-nas_hero_text', 'Od 1997 roku pomagamy Klientom wybierać najlepsze samochody oraz inne środki trwałe. Każde auto traktujemy jak swoje własne - z pełną odpowiedzialnością i pełnym zaangażowaniem. Jesteśmy autoryzowanym partnerem czołowych firm w Polsce i w naszym portfolio mamy zrealizowanych ponad 10 tysięcy umów leasingu.');
                    $about_artur_name = get_option('salon_auto_page_o-nas_artur_name', 'Artur Kurzydłowski');
                    $about_artur_text1 = get_option('salon_auto_page_o-nas_artur_text1', 'Moje doświadczenia na rynku motoryzacyjnym to prawie 3 dekady aktywnej działalności w branży samochodowej i finansowej. Realizując ponad 10 tysięcy transakcji zbudowałem markę opartą na wiedzy, zaufaniu, rzetelności i indywidualnym podejściu do każdego Klienta.');
                    $about_artur_text2 = get_option('salon_auto_page_o-nas_artur_text2', '28 lat na rynku motoryzacyjnym. Piękne auta to marka zbudowana na rzetelności i pasji do motoryzacji. Każde auto jest sprawdzone do ostatniej śrubki - bez kompromisów.');
                    $about_artur_text3 = get_option('salon_auto_page_o-nas_artur_text3', 'Specjalizujemy się w sprzedaży samochodów marki premium i leasingów. Każdy samochód przechodzi kontrolę techniczną i wydawany jest Klientowi w stanie możliwie perfekcyjnym.');
                    $about_artur_text4 = get_option('salon_auto_page_o-nas_artur_text4', 'Oferuję kompleksową obsługę - od pomocy w wyborze auta, przez finansowanie leasingowe, po ubezpieczenia i pełną dokumentację. Dbam o to, aby proces zakupu był transparentny i bezpieczny.');
                    $about_artur_cert = get_option('salon_auto_page_o-nas_artur_cert', 'Członek Loży Przedsiębiorców i Uczestnik Programu RZETELNA Firma.');
                    $about_button_text = get_option('salon_auto_page_o-nas_button_text', 'Kontakt');
                    $about_image_id = get_option('salon_auto_page_o-nas_about_image', '');
                    $about_stat1_value = get_option('salon_auto_page_o-nas_stat1_value', '28 lat');
                    $about_stat1_label = get_option('salon_auto_page_o-nas_stat1_label', 'doświadczenia');
                    $about_stat2_value = get_option('salon_auto_page_o-nas_stat2_value', '10 000+');
                    $about_stat2_label = get_option('salon_auto_page_o-nas_stat2_label', 'umów leasingowych');
                    $about_values_title = get_option('salon_auto_page_o-nas_values_title', 'Nasze zasady');
                    $about_values_subtitle = get_option('salon_auto_page_o-nas_values_subtitle', '3 filary, na których opiera się nasza działalność');
                    $about_value1_title = get_option('salon_auto_page_o-nas_value1_title', 'Transparentność');
                    $about_value1_text = get_option('salon_auto_page_o-nas_value1_text', 'Pełna dokumentacja i jawność wszystkich informacji o pojeździe');
                    $about_value2_title = get_option('salon_auto_page_o-nas_value2_title', 'Zaufanie');
                    $about_value2_text = get_option('salon_auto_page_o-nas_value2_text', 'Uczciwe relacje i wieloletnia współpraca z Klientami');
                    $about_value3_title = get_option('salon_auto_page_o-nas_value3_title', 'Kompleksowość');
                    $about_value3_text = get_option('salon_auto_page_o-nas_value3_text', 'Pełna obsługa: auto, leasing, ubezpieczenia, dokumenty');
                    $about_credentials_title = get_option('salon_auto_page_o-nas_credentials_title', 'Certyfikaty i członkostwa');
                    $about_credentials_subtitle = get_option('salon_auto_page_o-nas_credentials_subtitle', 'Dowody wiarygodności i profesjonalizmu');
                    $about_cred1_title = get_option('salon_auto_page_o-nas_cred1_title', '28 lat na rynku');
                    $about_cred1_text = get_option('salon_auto_page_o-nas_cred1_text', 'Najlepsza rekomendacja to zadowoleni Klienci przez prawie trzy dekady działalności');
                    $about_cred2_title = get_option('salon_auto_page_o-nas_cred2_title', 'Loża Przedsiębiorców');
                    $about_cred2_text = get_option('salon_auto_page_o-nas_cred2_text', 'Członek prestiżowej organizacji zrzeszającej najlepszych przedsiębiorców w Polsce');
                    $about_cred3_title = get_option('salon_auto_page_o-nas_cred3_title', 'Uczestnik Programu RZETELNA Firma');
                    $about_cred3_text = get_option('salon_auto_page_o-nas_cred3_text', 'Certyfikat potwierdzający wysoką wiarygodność i rzetelność w biznesie');
                    $about_cta_title = get_option('salon_auto_page_o-nas_cta_title', 'Serdecznie Zapraszamy');
                    $about_cta_phone_text = get_option('salon_auto_page_o-nas_cta_phone_text', 'Zadzwoń');
                    $about_cta_contact_text = get_option('salon_auto_page_o-nas_cta_contact_text', 'Formularz kontaktowy');
                ?>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja Hero</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_hero_title">Tytuł Hero</label></th>
                    <td><input type="text" id="page_field_hero_title" name="page_field_hero_title" value="<?php echo esc_attr($about_hero_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hero_text">Tekst Hero</label></th>
                    <td><textarea id="page_field_hero_text" name="page_field_hero_text" rows="3" class="large-text"><?php echo esc_textarea($about_hero_text); ?></textarea></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja "O Arturze"</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_artur_name">Imię i nazwisko</label></th>
                    <td><input type="text" id="page_field_artur_name" name="page_field_artur_name" value="<?php echo esc_attr($about_artur_name); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_artur_text1">Tekst 1</label></th>
                    <td><textarea id="page_field_artur_text1" name="page_field_artur_text1" rows="3" class="large-text"><?php echo esc_textarea($about_artur_text1); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_artur_text2">Tekst 2 (wyróżniony)</label></th>
                    <td><textarea id="page_field_artur_text2" name="page_field_artur_text2" rows="2" class="large-text"><?php echo esc_textarea($about_artur_text2); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_artur_text3">Tekst 3</label></th>
                    <td><textarea id="page_field_artur_text3" name="page_field_artur_text3" rows="2" class="large-text"><?php echo esc_textarea($about_artur_text3); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_artur_text4">Tekst 4</label></th>
                    <td><textarea id="page_field_artur_text4" name="page_field_artur_text4" rows="3" class="large-text"><?php echo esc_textarea($about_artur_text4); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_artur_cert">Tekst certyfikatów (może zawierać HTML)</label></th>
                    <td><textarea id="page_field_artur_cert" name="page_field_artur_cert" rows="2" class="large-text"><?php echo esc_textarea($about_artur_cert); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_button_text">Tekst przycisku</label></th>
                    <td><input type="text" id="page_field_button_text" name="page_field_button_text" value="<?php echo esc_attr($about_button_text); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label>Zdjęcie Artura</label></th>
                    <td>
                        <input type="hidden" id="page_image_about_image" name="page_image_about_image" value="<?php echo esc_attr($about_image_id); ?>">
                        <button type="button" class="button" id="page_image_about_image_button">Wybierz zdjęcie</button>
                        <div id="page_image_about_image_preview" style="margin-top: 10px;">
                            <?php if ($about_image_id) : 
                                $img_url = wp_get_attachment_image_url($about_image_id, 'thumbnail');
                                if ($img_url) :
                            ?>
                            <img src="<?php echo esc_url($img_url); ?>" style="max-width: 150px; height: auto; border: 2px solid #ddd; border-radius: 4px;">
                            <?php endif; endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><label for="page_field_stat1_value">Statystyka 1 - Wartość</label></th>
                    <td><input type="text" id="page_field_stat1_value" name="page_field_stat1_value" value="<?php echo esc_attr($about_stat1_value); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_stat1_label">Statystyka 1 - Etykieta</label></th>
                    <td><input type="text" id="page_field_stat1_label" name="page_field_stat1_label" value="<?php echo esc_attr($about_stat1_label); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_stat2_value">Statystyka 2 - Wartość</label></th>
                    <td><input type="text" id="page_field_stat2_value" name="page_field_stat2_value" value="<?php echo esc_attr($about_stat2_value); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_stat2_label">Statystyka 2 - Etykieta</label></th>
                    <td><input type="text" id="page_field_stat2_label" name="page_field_stat2_label" value="<?php echo esc_attr($about_stat2_label); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja "Nasze zasady"</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_values_title">Tytuł sekcji</label></th>
                    <td><input type="text" id="page_field_values_title" name="page_field_values_title" value="<?php echo esc_attr($about_values_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_values_subtitle">Podtytuł sekcji</label></th>
                    <td><input type="text" id="page_field_values_subtitle" name="page_field_values_subtitle" value="<?php echo esc_attr($about_values_subtitle); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_value1_title">Zasada 1 - Tytuł</label></th>
                    <td><input type="text" id="page_field_value1_title" name="page_field_value1_title" value="<?php echo esc_attr($about_value1_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_value1_text">Zasada 1 - Tekst</label></th>
                    <td><textarea id="page_field_value1_text" name="page_field_value1_text" rows="2" class="large-text"><?php echo esc_textarea($about_value1_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_value2_title">Zasada 2 - Tytuł</label></th>
                    <td><input type="text" id="page_field_value2_title" name="page_field_value2_title" value="<?php echo esc_attr($about_value2_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_value2_text">Zasada 2 - Tekst</label></th>
                    <td><textarea id="page_field_value2_text" name="page_field_value2_text" rows="2" class="large-text"><?php echo esc_textarea($about_value2_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_value3_title">Zasada 3 - Tytuł</label></th>
                    <td><input type="text" id="page_field_value3_title" name="page_field_value3_title" value="<?php echo esc_attr($about_value3_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_value3_text">Zasada 3 - Tekst</label></th>
                    <td><textarea id="page_field_value3_text" name="page_field_value3_text" rows="2" class="large-text"><?php echo esc_textarea($about_value3_text); ?></textarea></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja "Certyfikaty"</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_credentials_title">Tytuł sekcji</label></th>
                    <td><input type="text" id="page_field_credentials_title" name="page_field_credentials_title" value="<?php echo esc_attr($about_credentials_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_credentials_subtitle">Podtytuł sekcji</label></th>
                    <td><input type="text" id="page_field_credentials_subtitle" name="page_field_credentials_subtitle" value="<?php echo esc_attr($about_credentials_subtitle); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_cred1_title">Certyfikat 1 - Tytuł</label></th>
                    <td><input type="text" id="page_field_cred1_title" name="page_field_cred1_title" value="<?php echo esc_attr($about_cred1_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_cred1_text">Certyfikat 1 - Tekst</label></th>
                    <td><textarea id="page_field_cred1_text" name="page_field_cred1_text" rows="2" class="large-text"><?php echo esc_textarea($about_cred1_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_cred2_title">Certyfikat 2 - Tytuł</label></th>
                    <td><input type="text" id="page_field_cred2_title" name="page_field_cred2_title" value="<?php echo esc_attr($about_cred2_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_cred2_text">Certyfikat 2 - Tekst</label></th>
                    <td><textarea id="page_field_cred2_text" name="page_field_cred2_text" rows="2" class="large-text"><?php echo esc_textarea($about_cred2_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_cred3_title">Certyfikat 3 - Tytuł</label></th>
                    <td><input type="text" id="page_field_cred3_title" name="page_field_cred3_title" value="<?php echo esc_attr($about_cred3_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_cred3_text">Certyfikat 3 - Tekst</label></th>
                    <td><textarea id="page_field_cred3_text" name="page_field_cred3_text" rows="2" class="large-text"><?php echo esc_textarea($about_cred3_text); ?></textarea></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja CTA</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_cta_title">Tytuł CTA</label></th>
                    <td><input type="text" id="page_field_cta_title" name="page_field_cta_title" value="<?php echo esc_attr($about_cta_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_cta_phone_text">Tekst przycisku telefonu</label></th>
                    <td><input type="text" id="page_field_cta_phone_text" name="page_field_cta_phone_text" value="<?php echo esc_attr($about_cta_phone_text); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_cta_contact_text">Tekst przycisku formularza</label></th>
                    <td><input type="text" id="page_field_cta_contact_text" name="page_field_cta_contact_text" value="<?php echo esc_attr($about_cta_contact_text); ?>" class="regular-text"></td>
                </tr>
                <?php } ?>
                
                <?php
                // Fields for Kontakt page
                if ($page_slug === 'kontakt') {
                    $contact_hero_title = get_option('salon_auto_page_kontakt_hero_title', 'Kontakt');
                    $contact_hero_subtitle = get_option('salon_auto_page_kontakt_hero_subtitle', 'Chętnie odpowiemy na wszystkie Twoje pytania');
                    $contact_info_title = get_option('salon_auto_page_kontakt_info_title', 'Dane kontaktowe');
                    $contact_company_name = get_option('salon_auto_page_kontakt_company_name', 'Artur Kurzydłowski');
                    $contact_company_nip = get_option('salon_auto_page_kontakt_company_nip', 'NIP: 6731525915');
                    $contact_company_regon = get_option('salon_auto_page_kontakt_company_regon', 'REGON: 330558443');
                    $contact_hours_title = get_option('salon_auto_page_kontakt_hours_title', 'Godziny kontaktu');
                    $contact_hours_mon_fri = get_option('salon_auto_page_kontakt_hours_mon_fri', 'Poniedziałek - Piątek:');
                    $contact_hours_mon_fri_time = get_option('salon_auto_page_kontakt_hours_mon_fri_time', '9:00 - 18:00');
                    $contact_hours_sat = get_option('salon_auto_page_kontakt_hours_sat', 'Sobota:');
                    $contact_hours_sat_time = get_option('salon_auto_page_kontakt_hours_sat_time', '9:00 - 18:00');
                    $contact_hours_sun = get_option('salon_auto_page_kontakt_hours_sun', 'Niedziela:');
                    $contact_hours_sun_time = get_option('salon_auto_page_kontakt_hours_sun_time', '9:00 - 18:00');
                    $contact_hours_note = get_option('salon_auto_page_kontakt_hours_note', '* Wizyty poza godzinami otwarcia możliwe po wcześniejszym umówieniu');
                    $contact_form_title = get_option('salon_auto_page_kontakt_form_title', 'Napisz do nas');
                ?>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja Hero</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_hero_title">Tytuł Hero</label></th>
                    <td><input type="text" id="page_field_hero_title" name="page_field_hero_title" value="<?php echo esc_attr($contact_hero_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hero_subtitle">Podtytuł Hero</label></th>
                    <td><input type="text" id="page_field_hero_subtitle" name="page_field_hero_subtitle" value="<?php echo esc_attr($contact_hero_subtitle); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Dane kontaktowe</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_info_title">Tytuł sekcji</label></th>
                    <td><input type="text" id="page_field_info_title" name="page_field_info_title" value="<?php echo esc_attr($contact_info_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_company_name">Nazwa firmy</label></th>
                    <td><input type="text" id="page_field_company_name" name="page_field_company_name" value="<?php echo esc_attr($contact_company_name); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_company_nip">NIP</label></th>
                    <td><input type="text" id="page_field_company_nip" name="page_field_company_nip" value="<?php echo esc_attr($contact_company_nip); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_company_regon">REGON</label></th>
                    <td><input type="text" id="page_field_company_regon" name="page_field_company_regon" value="<?php echo esc_attr($contact_company_regon); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Godziny kontaktu</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_hours_title">Tytuł sekcji</label></th>
                    <td><input type="text" id="page_field_hours_title" name="page_field_hours_title" value="<?php echo esc_attr($contact_hours_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hours_mon_fri">Poniedziałek - Piątek (etykieta)</label></th>
                    <td><input type="text" id="page_field_hours_mon_fri" name="page_field_hours_mon_fri" value="<?php echo esc_attr($contact_hours_mon_fri); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hours_mon_fri_time">Poniedziałek - Piątek (godziny)</label></th>
                    <td><input type="text" id="page_field_hours_mon_fri_time" name="page_field_hours_mon_fri_time" value="<?php echo esc_attr($contact_hours_mon_fri_time); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hours_sat">Sobota (etykieta)</label></th>
                    <td><input type="text" id="page_field_hours_sat" name="page_field_hours_sat" value="<?php echo esc_attr($contact_hours_sat); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hours_sat_time">Sobota (godziny)</label></th>
                    <td><input type="text" id="page_field_hours_sat_time" name="page_field_hours_sat_time" value="<?php echo esc_attr($contact_hours_sat_time); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hours_sun">Niedziela (etykieta)</label></th>
                    <td><input type="text" id="page_field_hours_sun" name="page_field_hours_sun" value="<?php echo esc_attr($contact_hours_sun); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hours_sun_time">Niedziela (godziny)</label></th>
                    <td><input type="text" id="page_field_hours_sun_time" name="page_field_hours_sun_time" value="<?php echo esc_attr($contact_hours_sun_time); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hours_note">Notatka pod godzinami</label></th>
                    <td><input type="text" id="page_field_hours_note" name="page_field_hours_note" value="<?php echo esc_attr($contact_hours_note); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Formularz kontaktowy</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_form_title">Tytuł formularza</label></th>
                    <td><input type="text" id="page_field_form_title" name="page_field_form_title" value="<?php echo esc_attr($contact_form_title); ?>" class="regular-text"></td>
                </tr>
                <?php } ?>
                
                <?php
                // Fields for Leasing page
                if ($page_slug === 'leasing') {
                    $leasing_hero_title = get_option('salon_auto_page_leasing_hero_title', 'Leasing samochodów premium');
                    $leasing_hero_subtitle = get_option('salon_auto_page_leasing_hero_subtitle', 'Leasing samochodów używanych i nowych dla firm, osób fizycznych i rolników. Leasing konsumencki samochodu używanego, leasing samochodu premium dla firmy. Najlepsze warunki leasingu operacyjnego i finansowego.');
                    $leasing_hero_check1 = get_option('salon_auto_page_leasing_hero_check1', 'Dla firm, osób fizycznych i rolników');
                    $leasing_hero_check2 = get_option('salon_auto_page_leasing_hero_check2', 'Nowe i używane');
                    $leasing_hero_check3 = get_option('salon_auto_page_leasing_hero_check3', 'Przyspieszona procedura');
                    $leasing_benefits_title = get_option('salon_auto_page_leasing_benefits_title', 'Leasing samochodów premium – dla firm i osób fizycznych');
                    $leasing_benefits_subtitle = get_option('salon_auto_page_leasing_benefits_subtitle', 'Korzyści finansowania leasingowego dla samochodów używanych i nowych');
                    $leasing_benefit1_title = get_option('salon_auto_page_leasing_benefit1_title', 'Korzyści podatkowe');
                    $leasing_benefit1_text = get_option('salon_auto_page_leasing_benefit1_text', 'Raty i odsetki w koszty firmy, odliczenie VAT');
                    $leasing_benefit2_title = get_option('salon_auto_page_leasing_benefit2_title', 'Płynność finansowa');
                    $leasing_benefit2_text = get_option('salon_auto_page_leasing_benefit2_text', 'Niski wkład własny, zachowanie kapitału obrotowego');
                    $leasing_benefit3_title = get_option('salon_auto_page_leasing_benefit3_title', 'Szybka decyzja');
                    $leasing_benefit3_text = get_option('salon_auto_page_leasing_benefit3_text', 'Wstępna akceptacja w 24 godziny, finalizacja w 2-3 dni');
                    $leasing_benefit4_title = get_option('salon_auto_page_leasing_benefit4_title', 'Elastyczność');
                    $leasing_benefit4_text = get_option('salon_auto_page_leasing_benefit4_text', 'Dopasowanie parametrów do twoich potrzeb');
                    $leasing_partner_title = get_option('salon_auto_page_leasing_partner_title', 'Więcej informacji o leasingu');
                    $leasing_partner_text = get_option('salon_auto_page_leasing_partner_text', 'Zapraszamy do odwiedzenia strony Związku Polskiego Leasingu, gdzie znajdziesz wszelkie informacje na temat tej formy finansowania oraz szczegółowe informacje o leasingu.');
                    $leasing_partner_link_text = get_option('salon_auto_page_leasing_partner_link_text', 'Odwiedź leasing.org.pl');
                    $leasing_partner_link_url = get_option('salon_auto_page_leasing_partner_link_url', 'https://leasing.org.pl/');
                    $leasing_how_title = get_option('salon_auto_page_leasing_how_title', 'Jak to działa?');
                    $leasing_how_subtitle = get_option('salon_auto_page_leasing_how_subtitle', 'Proces leasingu w 4 prostych krokach');
                    $leasing_step1_title = get_option('salon_auto_page_leasing_step1_title', 'WYBIERZMY SAMOCHÓD');
                    $leasing_step1_text = get_option('salon_auto_page_leasing_step1_text', 'Wybierz pojazd z naszej oferty lub poproś o sprowadzenie konkretnego modelu.');
                    $leasing_step2_title = get_option('salon_auto_page_leasing_step2_title', 'Złóż wniosek');
                    $leasing_step2_text = get_option('salon_auto_page_leasing_step2_text', 'Wypełnij prosty wniosek leasingowy - pomożemy Tobie w tym procesie.');
                    $leasing_step3_title = get_option('salon_auto_page_leasing_step3_title', 'DECYZJA W 24 godziny');
                    $leasing_step3_text = get_option('salon_auto_page_leasing_step3_text', 'Wstępna decyzja w 24 godziny. Po akceptacji podpisujesz umowę leasingową.');
                    $leasing_step4_title = get_option('salon_auto_page_leasing_step4_title', 'Odbierz samochód');
                    $leasing_step4_text = get_option('salon_auto_page_leasing_step4_text', 'Odbierasz swoje wymarzone auto przygotowane i gotowe do jazdy!');
                    $leasing_private_title = get_option('salon_auto_page_leasing_private_title', 'Leasing używanego samochodu premium dla osoby prywatnej');
                    $leasing_private_text = get_option('salon_auto_page_leasing_private_text', '<strong>Leasing konsumencki</strong> to coraz popularniejsza forma finansowania samochodów premium używanych dla osób prywatnych. Umożliwia sfinansowanie wymarzonego auta bez angażowania całego kapitału.');
                    $leasing_private_check1 = get_option('salon_auto_page_leasing_private_check1', '<strong>Leasing na używane auto premium</strong> – oferujemy leasing konsumencki na samochody premium używane do 10 lat (w niektórych przypadkach nawet starsze, jeśli pojazd jest w dobrym stanie).');
                    $leasing_private_check2 = get_option('salon_auto_page_leasing_private_check2', '<strong>Elastyczne raty</strong> – możliwość dopasowania wysokości rat do swoich możliwości finansowych, z możliwością zmiany parametrów w trakcie trwania umowy.');
                    $leasing_private_check3 = get_option('salon_auto_page_leasing_private_check3', '<strong>Opcja wykupu</strong> – po zakończeniu leasingu masz możliwość wykupu pojazdu po preferencyjnej cenie, lub zwrotu auta i wyboru nowego modelu.');
                    $leasing_private_check4 = get_option('salon_auto_page_leasing_private_check4', '<strong>Uproszczona procedura</strong> – leasing konsumencki jest prostszy niż kredyt samochodowy, z mniejszą ilością formalności.');
                    $leasing_form_title = get_option('salon_auto_page_leasing_form_title', 'Zapytaj o leasing');
                    $leasing_form_subtitle = get_option('salon_auto_page_leasing_form_subtitle', 'Wypełnij formularz, a odezwiemy się do Ciebie w ciągu 24 godziny');
                ?>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja Hero</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_hero_title">Tytuł Hero</label></th>
                    <td><input type="text" id="page_field_hero_title" name="page_field_hero_title" value="<?php echo esc_attr($leasing_hero_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hero_subtitle">Podtytuł Hero</label></th>
                    <td><textarea id="page_field_hero_subtitle" name="page_field_hero_subtitle" rows="3" class="large-text"><?php echo esc_textarea($leasing_hero_subtitle); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_hero_check1">Checkmark 1</label></th>
                    <td><input type="text" id="page_field_hero_check1" name="page_field_hero_check1" value="<?php echo esc_attr($leasing_hero_check1); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hero_check2">Checkmark 2</label></th>
                    <td><input type="text" id="page_field_hero_check2" name="page_field_hero_check2" value="<?php echo esc_attr($leasing_hero_check2); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hero_check3">Checkmark 3</label></th>
                    <td><input type="text" id="page_field_hero_check3" name="page_field_hero_check3" value="<?php echo esc_attr($leasing_hero_check3); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja Korzyści</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_benefits_title">Tytuł sekcji</label></th>
                    <td><input type="text" id="page_field_benefits_title" name="page_field_benefits_title" value="<?php echo esc_attr($leasing_benefits_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_benefits_subtitle">Podtytuł sekcji</label></th>
                    <td><input type="text" id="page_field_benefits_subtitle" name="page_field_benefits_subtitle" value="<?php echo esc_attr($leasing_benefits_subtitle); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_benefit1_title">Korzyść 1 - Tytuł</label></th>
                    <td><input type="text" id="page_field_benefit1_title" name="page_field_benefit1_title" value="<?php echo esc_attr($leasing_benefit1_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_benefit1_text">Korzyść 1 - Tekst</label></th>
                    <td><textarea id="page_field_benefit1_text" name="page_field_benefit1_text" rows="2" class="large-text"><?php echo esc_textarea($leasing_benefit1_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_benefit2_title">Korzyść 2 - Tytuł</label></th>
                    <td><input type="text" id="page_field_benefit2_title" name="page_field_benefit2_title" value="<?php echo esc_attr($leasing_benefit2_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_benefit2_text">Korzyść 2 - Tekst</label></th>
                    <td><textarea id="page_field_benefit2_text" name="page_field_benefit2_text" rows="2" class="large-text"><?php echo esc_textarea($leasing_benefit2_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_benefit3_title">Korzyść 3 - Tytuł</label></th>
                    <td><input type="text" id="page_field_benefit3_title" name="page_field_benefit3_title" value="<?php echo esc_attr($leasing_benefit3_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_benefit3_text">Korzyść 3 - Tekst</label></th>
                    <td><textarea id="page_field_benefit3_text" name="page_field_benefit3_text" rows="2" class="large-text"><?php echo esc_textarea($leasing_benefit3_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_benefit4_title">Korzyść 4 - Tytuł</label></th>
                    <td><input type="text" id="page_field_benefit4_title" name="page_field_benefit4_title" value="<?php echo esc_attr($leasing_benefit4_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_benefit4_text">Korzyść 4 - Tekst</label></th>
                    <td><textarea id="page_field_benefit4_text" name="page_field_benefit4_text" rows="2" class="large-text"><?php echo esc_textarea($leasing_benefit4_text); ?></textarea></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja Partner</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_partner_title">Tytuł sekcji</label></th>
                    <td><input type="text" id="page_field_partner_title" name="page_field_partner_title" value="<?php echo esc_attr($leasing_partner_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_partner_text">Tekst sekcji</label></th>
                    <td><textarea id="page_field_partner_text" name="page_field_partner_text" rows="3" class="large-text"><?php echo esc_textarea($leasing_partner_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_partner_link_text">Tekst linku</label></th>
                    <td><input type="text" id="page_field_partner_link_text" name="page_field_partner_link_text" value="<?php echo esc_attr($leasing_partner_link_text); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_partner_link_url">URL linku</label></th>
                    <td><input type="url" id="page_field_partner_link_url" name="page_field_partner_link_url" value="<?php echo esc_attr($leasing_partner_link_url); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja "Jak to działa?"</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_how_title">Tytuł sekcji</label></th>
                    <td><input type="text" id="page_field_how_title" name="page_field_how_title" value="<?php echo esc_attr($leasing_how_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_how_subtitle">Podtytuł sekcji</label></th>
                    <td><input type="text" id="page_field_how_subtitle" name="page_field_how_subtitle" value="<?php echo esc_attr($leasing_how_subtitle); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_step1_title">Krok 1 - Tytuł</label></th>
                    <td><input type="text" id="page_field_step1_title" name="page_field_step1_title" value="<?php echo esc_attr($leasing_step1_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_step1_text">Krok 1 - Tekst</label></th>
                    <td><textarea id="page_field_step1_text" name="page_field_step1_text" rows="2" class="large-text"><?php echo esc_textarea($leasing_step1_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_step2_title">Krok 2 - Tytuł</label></th>
                    <td><input type="text" id="page_field_step2_title" name="page_field_step2_title" value="<?php echo esc_attr($leasing_step2_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_step2_text">Krok 2 - Tekst</label></th>
                    <td><textarea id="page_field_step2_text" name="page_field_step2_text" rows="2" class="large-text"><?php echo esc_textarea($leasing_step2_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_step3_title">Krok 3 - Tytuł</label></th>
                    <td><input type="text" id="page_field_step3_title" name="page_field_step3_title" value="<?php echo esc_attr($leasing_step3_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_step3_text">Krok 3 - Tekst</label></th>
                    <td><textarea id="page_field_step3_text" name="page_field_step3_text" rows="2" class="large-text"><?php echo esc_textarea($leasing_step3_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_step4_title">Krok 4 - Tytuł</label></th>
                    <td><input type="text" id="page_field_step4_title" name="page_field_step4_title" value="<?php echo esc_attr($leasing_step4_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_step4_text">Krok 4 - Tekst</label></th>
                    <td><textarea id="page_field_step4_text" name="page_field_step4_text" rows="2" class="large-text"><?php echo esc_textarea($leasing_step4_text); ?></textarea></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja "Leasing dla osób fizycznych"</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_private_title">Tytuł sekcji</label></th>
                    <td><input type="text" id="page_field_private_title" name="page_field_private_title" value="<?php echo esc_attr($leasing_private_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_private_text">Główny tekst</label></th>
                    <td><textarea id="page_field_private_text" name="page_field_private_text" rows="3" class="large-text"><?php echo esc_textarea($leasing_private_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_private_check1">Checkmark 1 (może zawierać HTML)</label></th>
                    <td><textarea id="page_field_private_check1" name="page_field_private_check1" rows="2" class="large-text"><?php echo esc_textarea($leasing_private_check1); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_private_check2">Checkmark 2 (może zawierać HTML)</label></th>
                    <td><textarea id="page_field_private_check2" name="page_field_private_check2" rows="2" class="large-text"><?php echo esc_textarea($leasing_private_check2); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_private_check3">Checkmark 3 (może zawierać HTML)</label></th>
                    <td><textarea id="page_field_private_check3" name="page_field_private_check3" rows="2" class="large-text"><?php echo esc_textarea($leasing_private_check3); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_private_check4">Checkmark 4 (może zawierać HTML)</label></th>
                    <td><textarea id="page_field_private_check4" name="page_field_private_check4" rows="2" class="large-text"><?php echo esc_textarea($leasing_private_check4); ?></textarea></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Formularz</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_form_title">Tytuł formularza</label></th>
                    <td><input type="text" id="page_field_form_title" name="page_field_form_title" value="<?php echo esc_attr($leasing_form_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_form_subtitle">Podtytuł formularza</label></th>
                    <td><input type="text" id="page_field_form_subtitle" name="page_field_form_subtitle" value="<?php echo esc_attr($leasing_form_subtitle); ?>" class="regular-text"></td>
                </tr>
                <?php } ?>
                
                <?php
                // Fields for Pożyczki page
                if ($page_slug === 'pozyczki') {
                    $loan_hero_title = get_option('salon_auto_page_pozyczki_hero_title', 'Pożyczki i finansowanie dla firm');
                    $loan_hero_subtitle = get_option('salon_auto_page_pozyczki_hero_subtitle', 'Finansowanie nie może być barierą ani blokadą. Potrzebujesz kapitału dostępnego wtedy, gdy pojawia się okazja, a nie kilka tygodni po niej.');
                    $loan_hero_check1 = get_option('salon_auto_page_pozyczki_hero_check1', 'Decyzja w 24h');
                    $loan_hero_check2 = get_option('salon_auto_page_pozyczki_hero_check2', 'Środki od razu');
                    $loan_hero_check3 = get_option('salon_auto_page_pozyczki_hero_check3', 'Minimum 6 miesięcy działalności');
                    $loan_who_title = get_option('salon_auto_page_pozyczki_who_title', 'Dla kogo jest nasze finansowanie?');
                    $loan_who_subtitle = get_option('salon_auto_page_pozyczki_who_subtitle', 'Znasz ten moment, kiedy rozwój przyspiesza? Rozwijasz firmę, zdobywasz nowe kontrakty, wchodzisz na większe wolumeny — i wiesz, że teraz trzeba działać szybko.');
                    $loan_who_image_id = get_option('salon_auto_page_pozyczki_who_image', '');
                    $loan_who_card1_title = get_option('salon_auto_page_pozyczki_who_card1_title', 'Duże zlecenia');
                    $loan_who_card1_text = get_option('salon_auto_page_pozyczki_who_card1_text', 'Masz szansę na realizację dużego zlecenia, ale zanim klient zapłaci, musisz wyłożyć środki na produkcję lub zatowarowanie.');
                    $loan_who_card2_title = get_option('salon_auto_page_pozyczki_who_card2_title', 'Rozwój firmy');
                    $loan_who_card2_text = get_option('salon_auto_page_pozyczki_who_card2_text', 'Chcesz zwiększyć moce produkcyjne, dokupić maszyny, rozbudować flotę lub zainwestować w nową halę.');
                    $loan_who_card3_title = get_option('salon_auto_page_pozyczki_who_card3_title', 'Budowa zespołu');
                    $loan_who_card3_text = get_option('salon_auto_page_pozyczki_who_card3_text', 'Budujesz zespół, bo pojawia się więcej projektów i nowych klientów.');
                    $loan_who_card4_title = get_option('salon_auto_page_pozyczki_who_card4_title', 'Sezonowy wzrost');
                    $loan_who_card4_text = get_option('salon_auto_page_pozyczki_who_card4_text', 'Przygotowujesz firmę na sezonowy wzrost sprzedaży i chcesz mieć gotowy magazyn, zanim ruszy konkurencja.');
                    $loan_offer_title = get_option('salon_auto_page_pozyczki_offer_title', 'Oferujemy kredyty firmom, które chcą rosnąć');
                    $loan_offer_subtitle = get_option('salon_auto_page_pozyczki_offer_subtitle', 'Finansowanie nie może być barierą ani blokadą. Potrzebujesz kapitału dostępnego wtedy, gdy pojawia się okazja, a nie kilka tygodni po niej.');
                    $loan_offer_text = get_option('salon_auto_page_pozyczki_offer_text', 'Dla firm działających od <span class="font-bold underline" style="color: #ffffff !important;">minimum 6 miesięcy</span>.');
                    $loan_how_title = get_option('salon_auto_page_pozyczki_how_title', 'Zdobądź środki dla swojej firmy w 3 prostych krokach');
                    $loan_how_subtitle = get_option('salon_auto_page_pozyczki_how_subtitle', 'Robimy wszystko, aby ułatwić Ci cały proces.');
                    $loan_step1_title = get_option('salon_auto_page_pozyczki_step1_title', 'Wniosek online');
                    $loan_step1_text = get_option('salon_auto_page_pozyczki_step1_text', 'Wypełniasz formularz w kilka minut.');
                    $loan_step2_title = get_option('salon_auto_page_pozyczki_step2_title', 'Weryfikacja i decyzja');
                    $loan_step2_text = get_option('salon_auto_page_pozyczki_step2_text', 'Kontaktujemy się z Tobą – decyzja w 24h.');
                    $loan_step3_title = get_option('salon_auto_page_pozyczki_step3_title', 'Wypłata środków');
                    $loan_step3_text = get_option('salon_auto_page_pozyczki_step3_text', 'Po podpisaniu umowy – pieniądze od razu na koncie.');
                    $loan_form_title = get_option('salon_auto_page_pozyczki_form_title', 'Złóż prosty wniosek o kredyt online dla Twojej firmy');
                    $loan_form_subtitle = get_option('salon_auto_page_pozyczki_form_subtitle', 'Wypełnij krótki formularz i załóż darmowe konto, dzięki któremu szybko i prosto uzyskasz kredyt dla swojego biznesu!');
                ?>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja Hero</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_hero_title">Tytuł Hero</label></th>
                    <td><input type="text" id="page_field_hero_title" name="page_field_hero_title" value="<?php echo esc_attr($loan_hero_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hero_subtitle">Podtytuł Hero</label></th>
                    <td><textarea id="page_field_hero_subtitle" name="page_field_hero_subtitle" rows="3" class="large-text"><?php echo esc_textarea($loan_hero_subtitle); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_hero_check1">Checkmark 1</label></th>
                    <td><input type="text" id="page_field_hero_check1" name="page_field_hero_check1" value="<?php echo esc_attr($loan_hero_check1); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hero_check2">Checkmark 2</label></th>
                    <td><input type="text" id="page_field_hero_check2" name="page_field_hero_check2" value="<?php echo esc_attr($loan_hero_check2); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hero_check3">Checkmark 3</label></th>
                    <td><input type="text" id="page_field_hero_check3" name="page_field_hero_check3" value="<?php echo esc_attr($loan_hero_check3); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja "Dla kogo"</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_who_title">Tytuł sekcji</label></th>
                    <td><input type="text" id="page_field_who_title" name="page_field_who_title" value="<?php echo esc_attr($loan_who_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_who_subtitle">Podtytuł sekcji</label></th>
                    <td><textarea id="page_field_who_subtitle" name="page_field_who_subtitle" rows="2" class="large-text"><?php echo esc_textarea($loan_who_subtitle); ?></textarea></td>
                </tr>
                <tr>
                    <th><label>Zdjęcie sekcji</label></th>
                    <td>
                        <input type="hidden" id="page_image_who_image" name="page_image_who_image" value="<?php echo esc_attr($loan_who_image_id); ?>">
                        <button type="button" class="button" id="page_image_who_image_button">Wybierz zdjęcie</button>
                        <div id="page_image_who_image_preview" style="margin-top: 10px;">
                            <?php if ($loan_who_image_id) : 
                                $img_url = wp_get_attachment_image_url($loan_who_image_id, 'thumbnail');
                                if ($img_url) :
                            ?>
                            <img src="<?php echo esc_url($img_url); ?>" style="max-width: 150px; height: auto; border: 2px solid #ddd; border-radius: 4px;">
                            <?php endif; endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><label for="page_field_who_card1_title">Karta 1 - Tytuł</label></th>
                    <td><input type="text" id="page_field_who_card1_title" name="page_field_who_card1_title" value="<?php echo esc_attr($loan_who_card1_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_who_card1_text">Karta 1 - Tekst</label></th>
                    <td><textarea id="page_field_who_card1_text" name="page_field_who_card1_text" rows="2" class="large-text"><?php echo esc_textarea($loan_who_card1_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_who_card2_title">Karta 2 - Tytuł</label></th>
                    <td><input type="text" id="page_field_who_card2_title" name="page_field_who_card2_title" value="<?php echo esc_attr($loan_who_card2_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_who_card2_text">Karta 2 - Tekst</label></th>
                    <td><textarea id="page_field_who_card2_text" name="page_field_who_card2_text" rows="2" class="large-text"><?php echo esc_textarea($loan_who_card2_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_who_card3_title">Karta 3 - Tytuł</label></th>
                    <td><input type="text" id="page_field_who_card3_title" name="page_field_who_card3_title" value="<?php echo esc_attr($loan_who_card3_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_who_card3_text">Karta 3 - Tekst</label></th>
                    <td><textarea id="page_field_who_card3_text" name="page_field_who_card3_text" rows="2" class="large-text"><?php echo esc_textarea($loan_who_card3_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_who_card4_title">Karta 4 - Tytuł</label></th>
                    <td><input type="text" id="page_field_who_card4_title" name="page_field_who_card4_title" value="<?php echo esc_attr($loan_who_card4_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_who_card4_text">Karta 4 - Tekst</label></th>
                    <td><textarea id="page_field_who_card4_text" name="page_field_who_card4_text" rows="2" class="large-text"><?php echo esc_textarea($loan_who_card4_text); ?></textarea></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja "Oferujemy kredyty"</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_offer_title">Tytuł sekcji</label></th>
                    <td><input type="text" id="page_field_offer_title" name="page_field_offer_title" value="<?php echo esc_attr($loan_offer_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_offer_subtitle">Podtytuł sekcji</label></th>
                    <td><input type="text" id="page_field_offer_subtitle" name="page_field_offer_subtitle" value="<?php echo esc_attr($loan_offer_subtitle); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_offer_text">Tekst w boxie (może zawierać HTML)</label></th>
                    <td><textarea id="page_field_offer_text" name="page_field_offer_text" rows="2" class="large-text"><?php echo esc_textarea($loan_offer_text); ?></textarea></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja "Jak to działa"</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_how_title">Tytuł sekcji</label></th>
                    <td><input type="text" id="page_field_how_title" name="page_field_how_title" value="<?php echo esc_attr($loan_how_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_how_subtitle">Podtytuł sekcji</label></th>
                    <td><input type="text" id="page_field_how_subtitle" name="page_field_how_subtitle" value="<?php echo esc_attr($loan_how_subtitle); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_step1_title">Krok 1 - Tytuł</label></th>
                    <td><input type="text" id="page_field_step1_title" name="page_field_step1_title" value="<?php echo esc_attr($loan_step1_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_step1_text">Krok 1 - Tekst</label></th>
                    <td><textarea id="page_field_step1_text" name="page_field_step1_text" rows="2" class="large-text"><?php echo esc_textarea($loan_step1_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_step2_title">Krok 2 - Tytuł</label></th>
                    <td><input type="text" id="page_field_step2_title" name="page_field_step2_title" value="<?php echo esc_attr($loan_step2_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_step2_text">Krok 2 - Tekst</label></th>
                    <td><textarea id="page_field_step2_text" name="page_field_step2_text" rows="2" class="large-text"><?php echo esc_textarea($loan_step2_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_step3_title">Krok 3 - Tytuł</label></th>
                    <td><input type="text" id="page_field_step3_title" name="page_field_step3_title" value="<?php echo esc_attr($loan_step3_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_step3_text">Krok 3 - Tekst</label></th>
                    <td><textarea id="page_field_step3_text" name="page_field_step3_text" rows="2" class="large-text"><?php echo esc_textarea($loan_step3_text); ?></textarea></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Formularz</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_form_title">Tytuł formularza</label></th>
                    <td><input type="text" id="page_field_form_title" name="page_field_form_title" value="<?php echo esc_attr($loan_form_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_form_subtitle">Podtytuł formularza</label></th>
                    <td><textarea id="page_field_form_subtitle" name="page_field_form_subtitle" rows="2" class="large-text"><?php echo esc_textarea($loan_form_subtitle); ?></textarea></td>
                </tr>
                <?php } ?>
                
                <?php
                // Fields for Ubezpieczenia page
                if ($page_slug === 'ubezpieczenia') {
                    $insurance_hero_title = get_option('salon_auto_page_ubezpieczenia_hero_title', 'Ubezpieczenia samochodowe premium');
                    $insurance_hero_subtitle = get_option('salon_auto_page_ubezpieczenia_hero_subtitle', 'Kompleksowa ochrona Twojego pojazdu. Współpracujemy z najlepszymi towarzystwami ubezpieczeniowymi, aby zapewnić Tobie najbezpieczniejsze i najkorzystniejsze cenowo warunki.');
                    $insurance_services_title = get_option('salon_auto_page_ubezpieczenia_services_title', 'Nasze ubezpieczenia');
                    $insurance_services_subtitle = get_option('salon_auto_page_ubezpieczenia_services_subtitle', 'Pełna ochrona dopasowana do Twoich potrzeb');
                    $insurance_oc_title = get_option('salon_auto_page_ubezpieczenia_oc_title', 'OC - Obowiązkowe');
                    $insurance_oc_text = get_option('salon_auto_page_ubezpieczenia_oc_text', 'Ubezpieczenie odpowiedzialności cywilnej posiadaczy pojazdów mechanicznych. Obowiązkowe dla każdego właściciela pojazdu.');
                    $insurance_oc_item1 = get_option('salon_auto_page_ubezpieczenia_oc_item1', 'Ochrona do 50 mln zł w UE');
                    $insurance_oc_item2 = get_option('salon_auto_page_ubezpieczenia_oc_item2', 'Zielona Karta gratis');
                    $insurance_oc_item3 = get_option('salon_auto_page_ubezpieczenia_oc_item3', 'Możliwość rat 0%');
                    $insurance_ac_title = get_option('salon_auto_page_ubezpieczenia_ac_title', 'AC - Autocasco');
                    $insurance_ac_text = get_option('salon_auto_page_ubezpieczenia_ac_text', 'Ubezpieczenie własnego pojazdu od kradzieży, kolizji, wypadku i zdarzeń losowych. Zalecane dla aut premium.');
                    $insurance_ac_item1 = get_option('salon_auto_page_ubezpieczenia_ac_item1', 'Ochrona przed kradzieżą');
                    $insurance_ac_item2 = get_option('salon_auto_page_ubezpieczenia_ac_item2', 'Kolizja i wypadek');
                    $insurance_ac_item3 = get_option('salon_auto_page_ubezpieczenia_ac_item3', 'Szkody losowe (żywioły, wandalizm)');
                    $insurance_ac_item4 = get_option('salon_auto_page_ubezpieczenia_ac_item4', 'Opcjonalnie: szyby, opony, assistance');
                    $insurance_nnw_title = get_option('salon_auto_page_ubezpieczenia_nnw_title', 'NNW - Następstwa nieszczęśliwych wypadków');
                    $insurance_nnw_text = get_option('salon_auto_page_ubezpieczenia_nnw_text', 'Ochrona kierowcy i pasażerów na wypadek obrażeń ciała, trwałego uszczerbku lub śmierci w wyniku wypadku.');
                    $insurance_nnw_item1 = get_option('salon_auto_page_ubezpieczenia_nnw_item1', 'Świadczenie przy uszkodzeniu zdrowia');
                    $insurance_nnw_item2 = get_option('salon_auto_page_ubezpieczenia_nnw_item2', 'Ochrona kierowcy i pasażerów');
                    $insurance_assistance_title = get_option('salon_auto_page_ubezpieczenia_assistance_title', 'Assistance');
                    $insurance_assistance_text = get_option('salon_auto_page_ubezpieczenia_assistance_text', 'Pomoc drogowa 24/7 w Polsce i za granicą. Holowanie, auto zastępcze, nocleg, pomoc prawna.');
                    $insurance_assistance_item1 = get_option('salon_auto_page_ubezpieczenia_assistance_item1', 'Holowanie 24/7');
                    $insurance_assistance_item2 = get_option('salon_auto_page_ubezpieczenia_assistance_item2', 'Auto zastępcze');
                    $insurance_assistance_item3 = get_option('salon_auto_page_ubezpieczenia_assistance_item3', 'Nocleg, transport');
                    $insurance_benefits_title = get_option('salon_auto_page_ubezpieczenia_benefits_title', 'Dlaczego my?');
                    $insurance_benefit1_title = get_option('salon_auto_page_ubezpieczenia_benefit1_title', 'Najlepsze ceny');
                    $insurance_benefit1_text = get_option('salon_auto_page_ubezpieczenia_benefit1_text', 'Porównujemy oferty różnych TU, znajdziemy dla Ciebie najbardziej korzystne warunki');
                    $insurance_benefit2_title = get_option('salon_auto_page_ubezpieczenia_benefit2_title', 'Szybka wycena');
                    $insurance_benefit2_text = get_option('salon_auto_page_ubezpieczenia_benefit2_text', 'Otrzymasz wycenę w ciągu 24 godzin - bez zbędnych formalności');
                    $insurance_benefit3_title = get_option('salon_auto_page_ubezpieczenia_benefit3_title', 'Kompleksowa obsługa');
                    $insurance_benefit3_text = get_option('salon_auto_page_ubezpieczenia_benefit3_text', 'Pomagamy w wyborze najlepszej oferty i załatwiamy wszystkie formalności');
                    $insurance_form_title = get_option('salon_auto_page_ubezpieczenia_form_title', 'Zapytaj o ubezpieczenie');
                    $insurance_form_subtitle = get_option('salon_auto_page_ubezpieczenia_form_subtitle', 'Wypełnij formularz, a odezwiemy się do Ciebie w ciągu 24 godziny');
                ?>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja Hero</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_hero_title">Tytuł Hero</label></th>
                    <td><input type="text" id="page_field_hero_title" name="page_field_hero_title" value="<?php echo esc_attr($insurance_hero_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_hero_subtitle">Podtytuł Hero</label></th>
                    <td><textarea id="page_field_hero_subtitle" name="page_field_hero_subtitle" rows="3" class="large-text"><?php echo esc_textarea($insurance_hero_subtitle); ?></textarea></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja "Nasze ubezpieczenia"</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_services_title">Tytuł sekcji</label></th>
                    <td><input type="text" id="page_field_services_title" name="page_field_services_title" value="<?php echo esc_attr($insurance_services_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_services_subtitle">Podtytuł sekcji</label></th>
                    <td><input type="text" id="page_field_services_subtitle" name="page_field_services_subtitle" value="<?php echo esc_attr($insurance_services_subtitle); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th colspan="2"><h3 style="margin: 20px 0 10px 0; font-size: 16px;">OC - Obowiązkowe</h3></th>
                </tr>
                <tr>
                    <th><label for="page_field_oc_title">Tytuł</label></th>
                    <td><input type="text" id="page_field_oc_title" name="page_field_oc_title" value="<?php echo esc_attr($insurance_oc_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_oc_text">Opis</label></th>
                    <td><textarea id="page_field_oc_text" name="page_field_oc_text" rows="3" class="large-text"><?php echo esc_textarea($insurance_oc_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_oc_item1">Punkt 1</label></th>
                    <td><input type="text" id="page_field_oc_item1" name="page_field_oc_item1" value="<?php echo esc_attr($insurance_oc_item1); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_oc_item2">Punkt 2</label></th>
                    <td><input type="text" id="page_field_oc_item2" name="page_field_oc_item2" value="<?php echo esc_attr($insurance_oc_item2); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_oc_item3">Punkt 3</label></th>
                    <td><input type="text" id="page_field_oc_item3" name="page_field_oc_item3" value="<?php echo esc_attr($insurance_oc_item3); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th colspan="2"><h3 style="margin: 20px 0 10px 0; font-size: 16px;">AC - Autocasco</h3></th>
                </tr>
                <tr>
                    <th><label for="page_field_ac_title">Tytuł</label></th>
                    <td><input type="text" id="page_field_ac_title" name="page_field_ac_title" value="<?php echo esc_attr($insurance_ac_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_ac_text">Opis</label></th>
                    <td><textarea id="page_field_ac_text" name="page_field_ac_text" rows="3" class="large-text"><?php echo esc_textarea($insurance_ac_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_ac_item1">Punkt 1</label></th>
                    <td><input type="text" id="page_field_ac_item1" name="page_field_ac_item1" value="<?php echo esc_attr($insurance_ac_item1); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_ac_item2">Punkt 2</label></th>
                    <td><input type="text" id="page_field_ac_item2" name="page_field_ac_item2" value="<?php echo esc_attr($insurance_ac_item2); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_ac_item3">Punkt 3</label></th>
                    <td><input type="text" id="page_field_ac_item3" name="page_field_ac_item3" value="<?php echo esc_attr($insurance_ac_item3); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_ac_item4">Punkt 4</label></th>
                    <td><input type="text" id="page_field_ac_item4" name="page_field_ac_item4" value="<?php echo esc_attr($insurance_ac_item4); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th colspan="2"><h3 style="margin: 20px 0 10px 0; font-size: 16px;">NNW - Następstwa nieszczęśliwych wypadków</h3></th>
                </tr>
                <tr>
                    <th><label for="page_field_nnw_title">Tytuł</label></th>
                    <td><input type="text" id="page_field_nnw_title" name="page_field_nnw_title" value="<?php echo esc_attr($insurance_nnw_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_nnw_text">Opis</label></th>
                    <td><textarea id="page_field_nnw_text" name="page_field_nnw_text" rows="3" class="large-text"><?php echo esc_textarea($insurance_nnw_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_nnw_item1">Punkt 1</label></th>
                    <td><input type="text" id="page_field_nnw_item1" name="page_field_nnw_item1" value="<?php echo esc_attr($insurance_nnw_item1); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_nnw_item2">Punkt 2</label></th>
                    <td><input type="text" id="page_field_nnw_item2" name="page_field_nnw_item2" value="<?php echo esc_attr($insurance_nnw_item2); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th colspan="2"><h3 style="margin: 20px 0 10px 0; font-size: 16px;">Assistance</h3></th>
                </tr>
                <tr>
                    <th><label for="page_field_assistance_title">Tytuł</label></th>
                    <td><input type="text" id="page_field_assistance_title" name="page_field_assistance_title" value="<?php echo esc_attr($insurance_assistance_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_assistance_text">Opis</label></th>
                    <td><textarea id="page_field_assistance_text" name="page_field_assistance_text" rows="3" class="large-text"><?php echo esc_textarea($insurance_assistance_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_assistance_item1">Punkt 1</label></th>
                    <td><input type="text" id="page_field_assistance_item1" name="page_field_assistance_item1" value="<?php echo esc_attr($insurance_assistance_item1); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_assistance_item2">Punkt 2</label></th>
                    <td><input type="text" id="page_field_assistance_item2" name="page_field_assistance_item2" value="<?php echo esc_attr($insurance_assistance_item2); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_assistance_item3">Punkt 3</label></th>
                    <td><input type="text" id="page_field_assistance_item3" name="page_field_assistance_item3" value="<?php echo esc_attr($insurance_assistance_item3); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Sekcja "Dlaczego my?"</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_benefits_title">Tytuł sekcji</label></th>
                    <td><input type="text" id="page_field_benefits_title" name="page_field_benefits_title" value="<?php echo esc_attr($insurance_benefits_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_benefit1_title">Korzyść 1 - Tytuł</label></th>
                    <td><input type="text" id="page_field_benefit1_title" name="page_field_benefit1_title" value="<?php echo esc_attr($insurance_benefit1_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_benefit1_text">Korzyść 1 - Tekst</label></th>
                    <td><textarea id="page_field_benefit1_text" name="page_field_benefit1_text" rows="2" class="large-text"><?php echo esc_textarea($insurance_benefit1_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_benefit2_title">Korzyść 2 - Tytuł</label></th>
                    <td><input type="text" id="page_field_benefit2_title" name="page_field_benefit2_title" value="<?php echo esc_attr($insurance_benefit2_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_benefit2_text">Korzyść 2 - Tekst</label></th>
                    <td><textarea id="page_field_benefit2_text" name="page_field_benefit2_text" rows="2" class="large-text"><?php echo esc_textarea($insurance_benefit2_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_field_benefit3_title">Korzyść 3 - Tytuł</label></th>
                    <td><input type="text" id="page_field_benefit3_title" name="page_field_benefit3_title" value="<?php echo esc_attr($insurance_benefit3_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_benefit3_text">Korzyść 3 - Tekst</label></th>
                    <td><textarea id="page_field_benefit3_text" name="page_field_benefit3_text" rows="2" class="large-text"><?php echo esc_textarea($insurance_benefit3_text); ?></textarea></td>
                </tr>
                <tr>
                    <th colspan="2"><h2 style="margin: 30px 0 15px 0; font-size: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Formularz</h2></th>
                </tr>
                <tr>
                    <th><label for="page_field_form_title">Tytuł formularza</label></th>
                    <td><input type="text" id="page_field_form_title" name="page_field_form_title" value="<?php echo esc_attr($insurance_form_title); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="page_field_form_subtitle">Podtytuł formularza</label></th>
                    <td><input type="text" id="page_field_form_subtitle" name="page_field_form_subtitle" value="<?php echo esc_attr($insurance_form_subtitle); ?>" class="regular-text"></td>
                </tr>
                <?php } ?>
            </table>
            
            <p class="submit">
                <input type="submit" name="salon_auto_save_page" class="button button-primary" value="Zapisz ustawienia">
            </p>
        </form>
    </div>
    
    <?php wp_enqueue_media(); ?>
    <?php wp_enqueue_script('jquery-ui-sortable'); ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var pageSlug = '<?php echo esc_js($page_slug); ?>';
        var pageFrame;
        var pageImageIds = <?php echo wp_json_encode($page_gallery_ids ? explode(',', $page_gallery_ids) : array()); ?>;
        
        // Make gallery sortable
        $('#page_gallery_preview_' + pageSlug).sortable({
            handle: 'img',
            placeholder: 'gallery-item-placeholder',
            update: function(event, ui) {
                var newOrder = [];
                $('#page_gallery_preview_' + pageSlug + ' .gallery-item').each(function() {
                    newOrder.push($(this).data('id').toString());
                });
                pageImageIds = newOrder;
                $('#page_gallery_hidden_' + pageSlug).val(pageImageIds.join(','));
                // Update order numbers
                $('#page_gallery_preview_' + pageSlug + ' .gallery-item').each(function(index) {
                    $(this).find('.gallery-order').text(index + 1);
                });
            }
        });
        
        $('#page_gallery_button_' + pageSlug).on('click', function(e) {
            e.preventDefault();
            
            if (pageFrame) {
                pageFrame.open();
                return;
            }
            
            pageFrame = wp.media({
                title: '<?php echo esc_js(__('Wybierz zdjęcia do galerii', 'salon-auto')); ?>',
                button: { text: '<?php echo esc_js(__('Użyj wybranych zdjęć', 'salon-auto')); ?>' },
                multiple: true,
                library: { type: 'image' }
            });
            
            pageFrame.on('select', function() {
                var selection = pageFrame.state().get('selection');
                selection.map(function(attachment) {
                    attachment = attachment.toJSON();
                    var id = attachment.id;
                    if (pageImageIds.indexOf(id.toString()) === -1) {
                        pageImageIds.push(id.toString());
                        var imgUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                        var orderNum = pageImageIds.length;
                        var img = $('<div class="gallery-item" data-id="' + id + '" style="position: relative; cursor: move; display: inline-block; margin: 5px;"><img src="' + imgUrl + '" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #ddd; display: block;"><span class="gallery-order" style="position: absolute; top: 2px; left: 2px; background: rgba(0,0,0,0.7); color: white; padding: 2px 6px; font-size: 11px; border-radius: 3px;">' + orderNum + '</span><button type="button" class="button-link remove-page-img" data-id="' + id + '" data-slug="' + pageSlug + '" style="position: absolute; top: 0; right: 0; background: red; color: white; border: none; padding: 2px 5px; cursor: pointer; z-index: 10;">×</button></div>');
                        $('#page_gallery_preview_' + pageSlug).append(img);
                    }
                });
                $('#page_gallery_hidden_' + pageSlug).val(pageImageIds.join(','));
            });
            
            pageFrame.open();
        });
        
        $(document).on('click', '.remove-page-img[data-slug="' + pageSlug + '"]', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            pageImageIds = pageImageIds.filter(function(n) { return n.toString() !== id.toString(); });
            $('#page_gallery_hidden_' + pageSlug).val(pageImageIds.join(','));
            $(this).closest('.gallery-item').remove();
            // Update order numbers
            $('#page_gallery_preview_' + pageSlug + ' .gallery-item').each(function(index) {
                $(this).find('.gallery-order').text(index + 1);
            });
        });
        
        <?php if ($page_slug === 'samochody') : ?>
        // Archive catalog cars management for Samochody page
        var archiveCarIndex = <?php echo count($archive_catalog_cars); ?>;
        
        // Make archive catalog cars sortable
        $('#archive_catalog_cars').sortable({
            items: '.archive-catalog-car-item',
            cursor: 'move',
            placeholder: 'archive-catalog-car-item-placeholder',
            update: function(event, ui) {
                $('#archive_catalog_cars .archive-catalog-car-item').each(function(index) {
                    $(this).find('.car-order-number').text(index + 1);
                    // Update input names for correct order
                    $(this).find('select, input').each(function() {
                        var name = $(this).attr('name');
                        if (name) {
                            // Replace first index in name attribute (e.g., archive_catalog_cars[0][car_id] -> archive_catalog_cars[1][car_id])
                            var newName = name.replace(/\[(\d+)\]/, '[' + index + ']');
                            $(this).attr('name', newName);
                        }
                    });
                });
            }
        });
        
        // Add new car to catalog
        $('#add_archive_car').on('click', function(e) {
            e.preventDefault();
            var $container = $('#archive_catalog_cars');
            var idx = archiveCarIndex++;
            var orderNum = $container.children().length + 1;
            
            var html = '<div class="archive-catalog-car-item" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9; border-radius: 4px; cursor: move;">' +
                '<div style="display: flex; gap: 15px; align-items: center; margin-bottom: 10px;">' +
                '<span class="car-order-number" style="background: #0073aa; color: white; padding: 5px 10px; border-radius: 3px; font-weight: bold;">' + orderNum + '</span>' +
                '<select name="archive_catalog_cars[' + idx + '][car_id]" class="archive-catalog-car-select" style="flex: 1;">' +
                '<option value="">-- Wybierz samochód --</option>' +
                <?php
                // Cache dla listy samochodów - poprawia wydajność
                $cache_key = 'salon_auto_all_cars_list';
                $all_cars_for_js = wp_cache_get($cache_key, 'salon_auto_cars');
                if (false === $all_cars_for_js) {
                    $all_cars_for_js = get_posts(array('post_type' => 'car', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC'));
                    wp_cache_set($cache_key, $all_cars_for_js, 'salon_auto_cars', HOUR_IN_SECONDS);
                }
                $car_options_html = '';
                foreach ($all_cars_for_js as $car) {
                    $brand = salon_auto_get_car_field($car->ID, 'brand');
                    $model = salon_auto_get_car_field($car->ID, 'model');
                    $car_name = $brand && $model ? $brand . ' ' . $model : $car->post_title;
                    $car_options_html .= '<option value="' . $car->ID . '">' . esc_js($car_name) . '</option>';
                }
                echo "'" . $car_options_html . "'";
                ?> +
                '</select>' +
                '<button type="button" class="button remove-archive-car">Usuń</button>' +
                '</div>' +
                '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; padding-left: 50px;">' +
                '<div>' +
                '<label>Zdjęcie karty (opcjonalne):</label>' +
                '<input type="hidden" name="archive_catalog_cars[' + idx + '][custom_image_id]" class="catalog-car-image-id" value="">' +
                '<div class="catalog-car-image-preview" style="margin: 5px 0;"><span style="color: #666; font-size: 12px;">Domyślne zdjęcie</span></div>' +
                '<button type="button" class="button button-small select-catalog-car-image">Wybierz</button> ' +
                '<button type="button" class="button button-small remove-catalog-car-image" style="display:none;">Usuń</button>' +
                '</div>' +
                '<div>' +
                '<label>Podpis karty (opcjonalne):</label>' +
                '<input type="text" name="archive_catalog_cars[' + idx + '][custom_caption]" value="" placeholder="np. 4.0 TFSI Quattro" style="width: 100%; margin-top: 5px;">' +
                '</div></div></div>';
            
            $container.append(html);
            $('#archive_catalog_cars').sortable('refresh');
        });
        
        // Remove car from catalog
        $(document).on('click', '.remove-archive-car', function(e) {
            e.preventDefault();
            $(this).closest('.archive-catalog-car-item').remove();
            $('#archive_catalog_cars .archive-catalog-car-item').each(function(index) {
                $(this).find('.car-order-number').text(index + 1);
            });
        });
        
        // Select custom image for catalog car
        $(document).on('click', '.select-catalog-car-image', function(e) {
            e.preventDefault();
            var $button = $(this);
            var $item = $button.closest('.archive-catalog-car-item');
            var $imageInput = $item.find('.catalog-car-image-id');
            var $preview = $item.find('.catalog-car-image-preview');
            var $removeBtn = $item.find('.remove-catalog-car-image');
            
            var frame = wp.media({
                title: 'Wybierz zdjęcie dla karty',
                button: { text: 'Użyj tego zdjęcia' },
                multiple: false,
                library: { type: 'image' }
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $imageInput.val(attachment.id);
                var imgUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                $preview.html('<img src="' + imgUrl + '" style="width: 80px; height: 60px; object-fit: cover; border: 1px solid #ddd;">');
                $removeBtn.show();
            });
            
            frame.open();
        });
        
        // Remove custom image
        $(document).on('click', '.remove-catalog-car-image', function(e) {
            e.preventDefault();
            var $item = $(this).closest('.archive-catalog-car-item');
            $item.find('.catalog-car-image-id').val('');
            $item.find('.catalog-car-image-preview').html('<span style="color: #666; font-size: 12px;">Domyślne zdjęcie</span>');
            $(this).hide();
        });
        <?php endif; ?>
        
        <?php if ($page_slug === 'o-nas') : ?>
        // Image uploader for about image
        var aboutImageFrame;
        jQuery('#page_image_about_image_button').on('click', function(e) {
            e.preventDefault();
            
            if (aboutImageFrame) {
                aboutImageFrame.open();
                return;
            }
            
            aboutImageFrame = wp.media({
                title: 'Wybierz zdjęcie Artura',
                button: { text: 'Użyj wybranego zdjęcia' },
                multiple: false,
                library: { type: 'image' }
            });
            
            aboutImageFrame.on('select', function() {
                var attachment = aboutImageFrame.state().get('selection').first().toJSON();
                jQuery('#page_image_about_image').val(attachment.id);
                jQuery('#page_image_about_image_preview').html('<img src="' + attachment.sizes.thumbnail.url + '" style="max-width: 150px; height: auto; border: 2px solid #ddd; border-radius: 4px;">');
            });
            
            aboutImageFrame.open();
        });
        <?php endif; ?>
        
        <?php if ($page_slug === 'pozyczki') : ?>
        // Image uploader for who image
        var whoImageFrame;
        jQuery('#page_image_who_image_button').on('click', function(e) {
            e.preventDefault();
            
            if (whoImageFrame) {
                whoImageFrame.open();
                return;
            }
            
            whoImageFrame = wp.media({
                title: 'Wybierz zdjęcie sekcji "Dla kogo"',
                button: { text: 'Użyj wybranego zdjęcia' },
                multiple: false,
                library: { type: 'image' }
            });
            
            whoImageFrame.on('select', function() {
                var attachment = whoImageFrame.state().get('selection').first().toJSON();
                jQuery('#page_image_who_image').val(attachment.id);
                jQuery('#page_image_who_image_preview').html('<img src="' + attachment.sizes.thumbnail.url + '" style="max-width: 150px; height: auto; border: 2px solid #ddd; border-radius: 4px;">');
            });
            
            whoImageFrame.open();
        });
        <?php endif; ?>
    });
    </script>
    <style>
    .gallery-item-placeholder {
        border: 2px dashed #ddd;
        width: 100px;
        height: 100px;
        display: inline-block;
        margin: 5px;
    }
    .sortable-gallery .gallery-item {
        transition: transform 0.2s;
    }
    .archive-catalog-car-item-placeholder {
        border: 2px dashed #0073aa;
        background: #e7f3ff;
        border-radius: 4px;
        height: 80px;
        margin-bottom: 15px;
    }
    .archive-catalog-car-item {
        transition: box-shadow 0.2s;
    }
    .archive-catalog-car-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .ui-sortable-helper {
        box-shadow: 0 5px 15px rgba(0,0,0,0.2) !important;
    }
    .sortable-gallery .gallery-item:hover {
        transform: scale(1.05);
    }
    </style>
    <?php
}
}

/**
 * Helper function to get option (works with custom fields and ACF)
 * Only define if not already defined (prevents conflicts)
 */
if (!function_exists('salon_auto_get_option')) {
    function salon_auto_get_option($field_name, $default = '') {
        // Try custom option first
        $value = get_option('salon_auto_' . $field_name, '');
        
        // Fallback to ACF if exists
        if (empty($value) && function_exists('get_field')) {
            $value = get_field($field_name, 'option');
        }
        
        return $value ? $value : $default;
    }
}

/**
 * Helper function to get page content
 */
if (!function_exists('salon_auto_get_page_content')) {
    function salon_auto_get_page_content($page_slug) {
        return get_option('salon_auto_page_' . $page_slug . '_content', '');
    }
}

/**
 * Helper function to get page gallery
 */
if (!function_exists('salon_auto_get_page_gallery')) {
    function salon_auto_get_page_gallery($page_slug) {
        $gallery_ids = get_option('salon_auto_page_' . $page_slug . '_gallery', '');
        if (empty($gallery_ids)) {
            return array();
        }
        return array_map('intval', explode(',', $gallery_ids));
    }
}
