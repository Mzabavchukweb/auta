<?php
/**
 * Salon Auto Theme Functions
 * 
 * Custom WordPress theme for Piękne Auta - premium car dealership
 * Based on existing static HTML/CSS/JS
 */

if (!defined('ABSPATH')) {
    exit;
}

// Load custom fields system - SAFELY with error handling
try {
    if (file_exists(get_template_directory() . '/includes/custom-fields.php')) {
        require_once get_template_directory() . '/includes/custom-fields.php';
    }
} catch (Exception $e) {
    // Silently fail - fallback functions will handle it
}

// Load brochure generator
if (file_exists(get_template_directory() . '/includes/brochure-generator.php')) {
    require_once get_template_directory() . '/includes/brochure-generator.php';
}

// Load options pages
try {
    if (file_exists(get_template_directory() . '/includes/options-pages.php')) {
        require_once get_template_directory() . '/includes/options-pages.php';
    }
} catch (Exception $e) {
    // Silently fail
}

// Load admin improvements
try {
    if (file_exists(get_template_directory() . '/includes/admin-improvements.php')) {
        require_once get_template_directory() . '/includes/admin-improvements.php';
    }
} catch (Exception $e) {
    // Silently fail
}

// Load SEO & Sitemap system
try {
    if (file_exists(get_template_directory() . '/includes/seo-sitemap.php')) {
        require_once get_template_directory() . '/includes/seo-sitemap.php';
    }
} catch (Exception $e) {
    // Silently fail
}

// JEDEN SKRYPT IMPORTU - wszystkie dane i zdjęcia
if (is_admin() && file_exists(get_template_directory() . '/import-all.php')) {
    require_once get_template_directory() . '/import-all.php';
}

// Import theme images to media library
if (is_admin() && file_exists(get_template_directory() . '/import-theme-images.php')) {
    require_once get_template_directory() . '/import-theme-images.php';
}

// Fix: Ensure temp directory exists for media uploads
// UWAGA: Główna naprawa jest w mu-plugins/fix-temp-dir.php (ładuje się wcześniej)
// Ten kod jest tylko backupem na wypadek gdyby mu-plugin nie zadziałał
if (!defined('WP_TEMP_DIR')) {
    $upload_dir = wp_upload_dir();
    $temp_dir = $upload_dir['basedir'] . '/temp';
    
    if (!file_exists($temp_dir)) {
        wp_mkdir_p($temp_dir);
        @chmod($temp_dir, 0777);
    }
    
    if (file_exists($temp_dir) && is_writable($temp_dir)) {
        define('WP_TEMP_DIR', $temp_dir);
    }
}

// Dodatkowe zabezpieczenie - sprawdź katalog temp przed każdym uploadem
add_action('admin_init', 'salon_auto_verify_temp_dir', 1);
function salon_auto_verify_temp_dir() {
    if (!defined('WP_TEMP_DIR')) {
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/temp';
        
        if (!file_exists($temp_dir)) {
            wp_mkdir_p($temp_dir);
            @chmod($temp_dir, 0777);
        }
        
        if (file_exists($temp_dir)) {
            define('WP_TEMP_DIR', $temp_dir);
            @ini_set('upload_tmp_dir', $temp_dir);
        }
    } elseif (defined('WP_TEMP_DIR') && !file_exists(WP_TEMP_DIR)) {
        // Katalog został usunięty - utwórz ponownie
        @mkdir(WP_TEMP_DIR, 0777, true);
        @chmod(WP_TEMP_DIR, 0777);
        @ini_set('upload_tmp_dir', WP_TEMP_DIR);
    } elseif (defined('WP_TEMP_DIR') && file_exists(WP_TEMP_DIR)) {
        // Upewnij się że PHP używa tego katalogu
        @ini_set('upload_tmp_dir', WP_TEMP_DIR);
    }
}

// AUTO-SETUP: Tworzenie stron WordPress - sprawdza i tworzy brakujące
add_action('admin_init', 'salon_auto_create_required_pages', 5);
function salon_auto_create_required_pages() {
    if (!current_user_can('manage_options')) {
        return;
}

    // WAŻNE: Napraw stronę Kontakt (szablon)
    $kontakt = get_page_by_path('kontakt');
    if ($kontakt) {
        $template = get_post_meta($kontakt->ID, '_wp_page_template', true);
        if ($template !== 'page-kontakt.php') {
            update_post_meta($kontakt->ID, '_wp_page_template', 'page-kontakt.php');
        }
}

    // Strony wymagane w systemie
    $pages_to_create = array(
        array('title' => 'Strona główna', 'slug' => 'strona-glowna', 'template' => ''),
        array('title' => 'Samochody', 'slug' => 'samochody', 'template' => 'page-samochody.php'),
        array('title' => 'Kontakt', 'slug' => 'kontakt', 'template' => 'page-kontakt.php'),
        array('title' => 'O nas', 'slug' => 'o-nas', 'template' => 'page-o-nas.php'),
        array('title' => 'Leasing', 'slug' => 'leasing', 'template' => 'page-leasing.php'),
        array('title' => 'Pożyczki', 'slug' => 'pozyczki', 'template' => 'page-pozyczki.php'),
        array('title' => 'Ubezpieczenia', 'slug' => 'ubezpieczenia', 'template' => 'page-ubezpieczenia.php'),
        array('title' => 'Regulamin', 'slug' => 'regulamin', 'template' => 'page-regulamin.php'),
        array('title' => 'Polityka prywatności', 'slug' => 'polityka-prywatnosci', 'template' => 'page-polityka-prywatnosci.php'),
    );
    
    $front_page_id = 0;
    
    foreach ($pages_to_create as $page_info) {
        // Sprawdź czy strona istnieje po slug
        $existing_page = get_page_by_path($page_info['slug']);
        
        // Sprawdź też po tytule (dla "Strona główna")
        if (!$existing_page && $page_info['slug'] === 'strona-glowna') {
            $existing_page = get_page_by_title($page_info['title']);
        }
        
        if (!$existing_page) {
            // Utwórz stronę
            $page_id = wp_insert_post(array(
                'post_title' => $page_info['title'],
                'post_name' => $page_info['slug'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '',
            ));
            
            if ($page_id && !is_wp_error($page_id)) {
                if (!empty($page_info['template'])) {
                    update_post_meta($page_id, '_wp_page_template', $page_info['template']);
                }
                if ($page_info['slug'] === 'strona-glowna') {
                    $front_page_id = $page_id;
                }
            }
        } else {
            // Strona istnieje - upewnij się że ma odpowiedni szablon
            if (!empty($page_info['template'])) {
                $current_template = get_post_meta($existing_page->ID, '_wp_page_template', true);
                if ($current_template !== $page_info['template']) {
                    update_post_meta($existing_page->ID, '_wp_page_template', $page_info['template']);
                }
            }
            if ($page_info['slug'] === 'strona-glowna') {
                $front_page_id = $existing_page->ID;
            }
        }
    }
    
    // Ustaw stronę główną jako Front Page (tylko jeśli nie jest już ustawiona)
    if ($front_page_id > 0) {
        $current_front = get_option('page_on_front');
        if (empty($current_front) || $current_front != $front_page_id) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $front_page_id);
        }
    }
}

// Pomocnicza funkcja do pobierania URL strony kontakt (z fallbackiem)
function salon_auto_get_contact_url() {
    // Method 1: By slug 'kontakt'
    $contact_page = get_page_by_path('kontakt');
    if ($contact_page && $contact_page->post_status === 'publish') {
        $url = get_permalink($contact_page->ID);
        if ($url) return $url;
    }
    
    // Method 2: By title 'Kontakt'
    $contact_page = get_page_by_title('Kontakt');
    if ($contact_page && $contact_page->post_status === 'publish') {
        $url = get_permalink($contact_page->ID);
        if ($url) return $url;
    }
    
    // Method 3: By template
    $pages = get_posts(array(
        'post_type' => 'page',
        'posts_per_page' => 1,
        'post_status' => 'publish',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-kontakt.php'
    ));
    if (!empty($pages)) {
        $url = get_permalink($pages[0]->ID);
        if ($url) return $url;
    }
    
    // Method 4: Search by slug containing 'kontakt'
    $pages = get_posts(array(
        'post_type' => 'page',
        'posts_per_page' => 1,
        'post_status' => 'publish',
        'name' => 'kontakt'
    ));
    if (!empty($pages)) {
        $url = get_permalink($pages[0]->ID);
        if ($url) return $url;
    }
    
    // Ostateczny fallback - sprawdź strukturę permalinków
    $permalink_structure = get_option('permalink_structure');
    if (!empty($permalink_structure)) {
    return home_url('/kontakt/');
    } else {
        return home_url('/?page_id=' . get_option('page_on_front'));
    }
}

/**
 * Pobierz galerię zdjęć samochodu
 * Najpierw sprawdza zdjęcia z motywu (gallery_files), potem z biblioteki (gallery)
 */
function salon_auto_get_car_gallery($post_id) {
    $gallery = [];
    $theme_url = get_template_directory_uri() . '/images/';
    $theme_dir = get_template_directory() . '/images/';
    
    // Pobierz gallery_files - może zawierać nazwy plików LUB ID z biblioteki
    $gallery_files = get_post_meta($post_id, 'gallery_files', true);
    if (!empty($gallery_files)) {
        $items = array_filter(array_map('trim', explode(',', $gallery_files)));
        foreach ($items as $item) {
            if (is_numeric($item)) {
                // To jest ID z biblioteki mediów
                $img_id = intval($item);
                if (wp_attachment_is_image($img_id)) {
                    $img_url = wp_get_attachment_image_url($img_id, 'large');
                    $thumb_url = wp_get_attachment_image_url($img_id, 'medium');
                    if ($img_url) {
                        $gallery[] = [
                            'type' => 'image',
                            'id' => $img_id,
                            'url' => $img_url,
                            'thumbnail' => $thumb_url ?: $img_url,
                            'alt' => get_the_title($post_id),
                            'source' => 'media'
                        ];
                    }
                }
            } else {
                // To jest nazwa pliku z motywu
                if (file_exists($theme_dir . $item)) {
                    $gallery[] = [
                        'type' => 'image',
                        'url' => $theme_url . $item,
                        'thumbnail' => $theme_url . $item,
                        'alt' => get_the_title($post_id),
                        'source' => 'theme'
                    ];
                }
            }
        }
    }
    
    // Opcja 2: Thumbnail jako fallback
    if (empty($gallery) && has_post_thumbnail($post_id)) {
        $thumb_id = get_post_thumbnail_id($post_id);
        $img_url = wp_get_attachment_image_url($thumb_id, 'car-gallery');
        if ($img_url) {
            $gallery[] = [
                'type' => 'image',
                'id' => $thumb_id,
                'url' => $img_url,
                'thumbnail' => wp_get_attachment_image_url($thumb_id, 'car-thumbnail') ?: $img_url,
                'alt' => get_the_title($post_id),
                'source' => 'thumbnail'
            ];
        }
    }
    
    // Opcja 3: FALLBACK na podstawie slug samochodu - szukaj zdjęć w folderze motywu
    if (empty($gallery)) {
        $post_slug = get_post_field('post_name', $post_id);
        $image_prefixes = array(
            'audi-sq8-2023' => 'audi-sq8',
            'audi-rs5-2023-450hp-individual' => 'rs5-new',
            'audi-a8-2019-50tdi-quattro' => 'audi-a8',
            'bmw-seria-7-2018-730d-xdrive' => 'bmw-7',
            'audi-a6-limousine' => 'audi-a6',
            'cupra-formentor-2023-tfsi' => 'cupra-formentor',
        );
        
        if (isset($image_prefixes[$post_slug])) {
            $prefix = $image_prefixes[$post_slug];
            for ($i = 1; $i <= 50; $i++) {
                $num = str_pad($i, 2, '0', STR_PAD_LEFT);
                $filename = $prefix . '-' . $num . '.jpg';
                if (file_exists($theme_dir . $filename)) {
                    $gallery[] = [
                        'type' => 'image',
                        'url' => $theme_url . $filename,
                        'thumbnail' => $theme_url . $filename,
                        'alt' => get_the_title($post_id),
                        'source' => 'theme-fallback'
                    ];
                    if (count($gallery) >= 15) break;
                }
            }
        }
    }
    
    // Opcja 4: Ostateczny fallback - domyślny obrazek
    if (empty($gallery)) {
        $gallery[] = [
            'type' => 'image',
            'url' => $theme_url . 'og-default.svg',
            'thumbnail' => $theme_url . 'og-default.svg',
            'alt' => get_the_title($post_id),
            'source' => 'default'
        ];
    }
    
    return $gallery;
}

/**
 * Pobierz główne zdjęcie samochodu (pierwsze z galerii)
 */
function salon_auto_get_car_main_image($post_id) {
    $gallery = salon_auto_get_car_gallery($post_id);
    return !empty($gallery) ? $gallery[0]['url'] : '';
}

/**
 * Theme Setup
 */
function salon_auto_setup() {
    // Load theme textdomain
    load_theme_textdomain('salon-auto', get_template_directory() . '/languages');
    
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style',
    ));
    add_theme_support('automatic-feed-links');
    add_theme_support('customize-selective-refresh-widgets');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'salon-auto'),
    ));
    
    // Add image sizes for cars
    add_image_size('car-thumbnail', 400, 300, true);
    add_image_size('car-gallery', 1200, 900, true);
}

/**
 * Set JPEG quality to 100% (no compression) - EXACT COPY FROM STATIC VERSION
 */
function salon_auto_jpeg_quality() {
    return 100; // Maximum quality - no compression
}
add_filter('jpeg_quality', 'salon_auto_jpeg_quality');
add_filter('wp_editor_set_quality', 'salon_auto_jpeg_quality');

add_action('after_setup_theme', 'salon_auto_setup');

/**
 * Enqueue Styles and Scripts
 */
function salon_auto_enqueue_assets() {
    // Don't load theme assets in admin panel
    if (is_admin()) {
        return;
    }
    
    $theme_version = wp_get_theme()->get('Version');
    
    // Main CSS - combine all existing CSS files
    if (file_exists(get_stylesheet_directory() . '/assets/css/main.css')) {
        wp_enqueue_style(
            'salon-main',
            get_stylesheet_directory_uri() . '/assets/css/main.css',
            array(),
            $theme_version
        );
    }
    
    // Additional CSS files from new version
    if (file_exists(get_stylesheet_directory() . '/assets/css/custom-premium.css')) {
        wp_enqueue_style(
            'salon-custom-premium',
            get_stylesheet_directory_uri() . '/assets/css/custom-premium.css',
            array('salon-main'),
            $theme_version
        );
    }
    
    if (file_exists(get_stylesheet_directory() . '/assets/css/nacja-rebrand.css')) {
        wp_enqueue_style(
            'salon-nacja-rebrand',
            get_stylesheet_directory_uri() . '/assets/css/nacja-rebrand.css',
            array('salon-main'),
            $theme_version
        );
    }
    
    if (file_exists(get_stylesheet_directory() . '/assets/css/premium-enhancements.css')) {
        wp_enqueue_style(
            'salon-premium-enhancements',
            get_stylesheet_directory_uri() . '/assets/css/premium-enhancements.css',
            array('salon-main'),
            $theme_version
        );
    }
    
    // Auta CSS (if exists) - check specific file first
    $auta_css_file = get_stylesheet_directory() . '/assets/css/auta.CnOC-q7W.css';
    if (file_exists($auta_css_file)) {
        wp_enqueue_style(
            'salon-auta',
            get_stylesheet_directory_uri() . '/assets/css/auta.CnOC-q7W.css',
            array('salon-main'),
            $theme_version
        );
    } else {
        // Fallback: try to find any auta.*.css file
        $auta_css = glob(get_stylesheet_directory() . '/assets/css/auta.*.css');
        if (!empty($auta_css) && is_array($auta_css)) {
            wp_enqueue_style(
                'salon-auta',
                get_stylesheet_directory_uri() . '/assets/css/' . basename($auta_css[0]),
                array('salon-main'),
                $theme_version
            );
        }
    }
    
    // Alpine.js (from CDN as in original)
    wp_enqueue_script(
        'alpinejs',
        'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js',
        array(),
        '3.x.x',
        true
    );
    wp_script_add_data('alpinejs', 'defer', true);
    wp_script_add_data('alpinejs', 'async', true);
    
    // Main JS - only if jQuery is needed
    if (file_exists(get_stylesheet_directory() . '/assets/js/main.js')) {
        wp_enqueue_script(
            'salon-main',
            get_stylesheet_directory_uri() . '/assets/js/main.js',
            array('jquery'),
            $theme_version,
            true
        );
    }
    
    // Cookie Consent API - only if file exists
    if (file_exists(get_stylesheet_directory() . '/assets/js/cookie-consent-api.js')) {
        wp_enqueue_script(
            'cookie-consent-api',
            get_stylesheet_directory_uri() . '/assets/js/cookie-consent-api.js',
            array(),
            $theme_version,
            true
        );
    }
    
    // Custom Animations - only if file exists
    if (file_exists(get_stylesheet_directory() . '/assets/js/custom-animations.js')) {
        wp_enqueue_script(
            'custom-animations',
            get_stylesheet_directory_uri() . '/assets/js/custom-animations.js',
            array(),
            $theme_version,
            true
        );
    }
    
    // Sticky CTA Mobile - only if file exists
    if (file_exists(get_stylesheet_directory() . '/assets/js/sticky-cta-mobile.js')) {
        wp_enqueue_script(
            'sticky-cta-mobile',
            get_stylesheet_directory_uri() . '/assets/js/sticky-cta-mobile.js',
            array(),
            $theme_version,
            true
        );
    }
    
    // Load cars catalog script - DISABLED
    // Cars are now rendered by PHP in archive-car.php and front-page.php
    // This script is no longer needed
    /*
    if (is_page_template('page-cars.php') && file_exists(get_stylesheet_directory() . '/assets/js/load-cars-catalog.js')) {
        wp_enqueue_script(
            'load-cars-catalog',
            get_stylesheet_directory_uri() . '/assets/js/load-cars-catalog.js',
            array(),
            $theme_version,
            true
        );
        wp_localize_script('load-cars-catalog', 'salonAuto', array(
            'home_url' => home_url('/'),
            'template_directory_uri' => get_template_directory_uri(),
        ));
    }
    */
    
    // Load similar cars script on single car pages - only if file exists
    if (is_singular('car') && file_exists(get_stylesheet_directory() . '/assets/js/load-similar-cars.js')) {
        wp_enqueue_script(
            'load-similar-cars',
            get_stylesheet_directory_uri() . '/assets/js/load-similar-cars.js',
            array(),
            $theme_version,
            true
        );
        wp_localize_script('load-similar-cars', 'salonAuto', array(
            'home_url' => trailingslashit(home_url()),
            'template_directory_uri' => get_stylesheet_directory_uri(),
        ));
    }
    
    // Load UX improvements script - always
    if (file_exists(get_stylesheet_directory() . '/assets/js/ux-improvements.js')) {
        wp_enqueue_script(
            'ux-improvements',
            get_stylesheet_directory_uri() . '/assets/js/ux-improvements.js',
            array(),
            $theme_version,
            true
        );
    }
    
    // Load cars select script on pages with forms - only if file exists
    if ((is_page('kontakt') || is_page_template('page-kontakt.php') || is_page('leasing') || is_page_template('page-leasing.php') || is_page('pozyczki') || is_page_template('page-pozyczki.php') || is_page('ubezpieczenia') || is_page_template('page-ubezpieczenia.php')) && file_exists(get_stylesheet_directory() . '/assets/js/load-cars-select.js')) {
        wp_enqueue_script(
            'load-cars-select',
            get_stylesheet_directory_uri() . '/assets/js/load-cars-select.js',
            array(),
            $theme_version,
            true
        );
        wp_localize_script('load-cars-select', 'salonAuto', array(
            'home_url' => trailingslashit(home_url()),
            'template_directory_uri' => get_template_directory_uri(),
        ));
    }
    
    // Load reviews script on front page - only if file exists
    // NOTE: Reviews are now managed via WordPress Options, so this script is optional
    // It will only run if reviews container is empty (fallback)
    if (is_front_page() && file_exists(get_stylesheet_directory() . '/assets/js/load-reviews.js')) {
        wp_enqueue_script(
            'load-reviews',
            get_stylesheet_directory_uri() . '/assets/js/load-reviews.js',
            array('salon-main'), // Requires salon-main for salonAutoThemeUri
            $theme_version,
            true
        );
    }
    
    // Lease Calculator (if needed) - only if file exists
    if ((is_page('leasing') || is_page_template('page-leasing.php')) && file_exists(get_stylesheet_directory() . '/assets/js/lease-calculator.js')) {
        wp_enqueue_script(
            'lease-calculator',
            get_stylesheet_directory_uri() . '/assets/js/lease-calculator.js',
            array(),
            $theme_version,
            true
        );
    }
    
    // Add inline script with theme data for JavaScript
    wp_add_inline_script('salon-main', 'window.salonAutoThemeUri = "' . esc_js(get_stylesheet_directory_uri()) . '"; window.salonAutoApiUrl = "' . esc_js(rest_url('salon-auto/v1/cars')) . '";', 'before');
}
add_action('wp_enqueue_scripts', 'salon_auto_enqueue_assets');

/**
 * Filter car archive query - show all cars (not just available)
 * Status filtering is done in template
 */
function salon_auto_filter_car_archive($query) {
    // Removed - show all cars, filter in template if needed
}
add_action('pre_get_posts', 'salon_auto_filter_car_archive');

/**
 * Register Custom Post Type: Car (Samochody)
 */
function salon_auto_register_car_post_type() {
    // Safety check - don't register during installation
    if (defined('WP_INSTALLING') && WP_INSTALLING) {
        return;
    }
    
    try {
    $labels = array(
        'name'                  => _x('Samochody', 'Post Type General Name', 'salon-auto'),
        'singular_name'         => _x('Samochód', 'Post Type Singular Name', 'salon-auto'),
        'menu_name'             => __('Samochody', 'salon-auto'),
        'name_admin_bar'        => __('Samochód', 'salon-auto'),
        'archives'              => __('Archiwum Samochodów', 'salon-auto'),
        'attributes'            => __('Atrybuty Samochodu', 'salon-auto'),
        'parent_item_colon'     => __('Nadrzędny Samochód:', 'salon-auto'),
        'all_items'             => __('Wszystkie Samochody', 'salon-auto'),
        'add_new_item'          => __('Dodaj Nowy Samochód', 'salon-auto'),
        'add_new'               => __('Dodaj Nowy', 'salon-auto'),
        'new_item'              => __('Nowy Samochód', 'salon-auto'),
        'edit_item'             => __('Edytuj Samochód', 'salon-auto'),
        'update_item'           => __('Aktualizuj Samochód', 'salon-auto'),
        'view_item'             => __('Zobacz Samochód', 'salon-auto'),
        'view_items'            => __('Zobacz Samochody', 'salon-auto'),
        'search_items'          => __('Szukaj Samochodów', 'salon-auto'),
        'not_found'             => __('Nie znaleziono', 'salon-auto'),
        'not_found_in_trash'    => __('Nie znaleziono w koszu', 'salon-auto'),
        'featured_image'        => __('Zdjęcie Główne', 'salon-auto'),
        'set_featured_image'    => __('Ustaw Zdjęcie Główne', 'salon-auto'),
        'remove_featured_image' => __('Usuń Zdjęcie Główne', 'salon-auto'),
        'use_featured_image'    => __('Użyj jako Zdjęcie Główne', 'salon-auto'),
        'insert_into_item'      => __('Wstaw do Samochodu', 'salon-auto'),
        'uploaded_to_this_item' => __('Przesłane do tego Samochodu', 'salon-auto'),
        'items_list'            => __('Lista Samochodów', 'salon-auto'),
        'items_list_navigation' => __('Nawigacja Listy Samochodów', 'salon-auto'),
        'filter_items_list'     => __('Filtruj Listę Samochodów', 'salon-auto'),
    );
    
    $args = array(
        'label'                 => __('Samochód', 'salon-auto'),
        'description'           => __('Samochody premium w ofercie', 'salon-auto'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20, // Po standardowych pozycjach (5=Posts, 10=Media, 15=Links, 20=Pages)
        'menu_icon'             => 'dashicons-car',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'capabilities'          => array(
            'edit_post'          => 'edit_post',
            'read_post'          => 'read_post',
            'delete_post'        => 'delete_post',
            'edit_posts'         => 'edit_posts',
            'edit_others_posts'  => 'edit_others_posts',
            'publish_posts'      => 'publish_posts',
            'read_private_posts' => 'read_private_posts',
        ),
        'map_meta_cap'          => true,
        'show_in_rest'          => true, // Enable Gutenberg
        'rewrite'               => array('slug' => 'samochody', 'with_front' => false),
    );
    
    register_post_type('car', $args);
    } catch (Exception $e) {
        // Log error but don't fail - theme will still work
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Error registering car post type: ' . $e->getMessage());
        }
    }
}
add_action('init', 'salon_auto_register_car_post_type', 0);

/**
 * ACF Configuration Instructions
 * 
 * IMPORTANT: Install Advanced Custom Fields plugin first!
 * 
 * After installing ACF, create the following field groups:
 * 
 * 1. Field Group: "Samochody" (Location: Post Type is equal to car)
 *    Fields:
 *    - price (Text) - Label: "Cena (PLN)"
 *    - year (Number) - Label: "Rok produkcji"
 *    - mileage (Number) - Label: "Przebieg (km)"
 *    - gearbox (Select) - Label: "Skrzynia biegów"
 *      Choices: Automatyczna|Automatyczna, Manualna|Manualna
 *    - fuel (Select) - Label: "Rodzaj paliwa"
 *      Choices: Benzyna|Benzyna, Diesel|Diesel, Hybryda|Hybryda, Elektryczny|Elektryczny
 *    - is_featured (True/False) - Label: "Wyróżnione"
 *    - gallery (Gallery) - Label: "Galeria zdjęć"
 *    - trim (Text) - Label: "Wersja/Wyposażenie"
 *    - brand (Text) - Label: "Marka"
 *    - model (Text) - Label: "Model"
 *    - color (Text) - Label: "Kolor"
 *    - power_hp (Number) - Label: "Moc (KM)"
 *    - engine_cc (Number) - Label: "Pojemność silnika (cm³)"
 *    - drivetrain (Select) - Label: "Napęd"
 *      Choices: 4x4|4x4, FWD|FWD, RWD|RWD
 *    - accident_free (True/False) - Label: "Bezwypadkowe"
 *    - service_history (Textarea) - Label: "Historia serwisowa"
 *    - origin (Text) - Label: "Pochodzenie"
 *    - owners (Number) - Label: "Liczba właścicieli"
 *    - vin_masked (Text) - Label: "VIN (maskowany)"
 *    - lease_from_pln (Number) - Label: "Leasing od (PLN/mies)"
 *    - status (Select) - Label: "Status"
 *      Choices: available|Dostępny, sold|Sprzedany, reserved|Zarezerwowany
 * 
 * 2. Field Group: "Ustawienia Strony Głównej" (Location: Options Page is equal to Strona Główna)
 *    Create ACF Options Page first:
 *    - Page Title: "Ustawienia Strony Głównej"
 *    - Menu Slug: "homepage-settings"
 * 
 *    Fields:
 *    - hero_title (Text) - Label: "Tytuł Hero"
 *    - hero_subtitle (Textarea) - Label: "Podtytuł Hero"
 *    - hero_background_image (Image) - Label: "Tło Hero"
 *    - about_text (Textarea) - Label: "Tekst 'O nas'"
 *    - why_us_title (Text) - Label: "Tytuł sekcji 'Dlaczego my'"
 *    - why_us_items (Repeater) - Label: "Elementy 'Dlaczego my'"
 *      Sub Fields:
 *        - icon (Text) - Label: "Ikona (SVG class)"
 *        - title (Text) - Label: "Tytuł"
 *        - description (Textarea) - Label: "Opis"
 * 
 * 3. Field Group: "Ustawienia Ogólne" (Location: Options Page is equal to Ustawienia Ogólne)
 *    Create ACF Options Page:
 *    - Page Title: "Ustawienia Ogólne"
 *    - Menu Slug: "general-settings"
 * 
 *    Fields:
 *    - phone (Text) - Label: "Telefon"
 *    - email (Text) - Label: "Email"
 *    - address (Textarea) - Label: "Adres"
 *    - social_facebook (URL) - Label: "Facebook"
 *    - social_instagram (URL) - Label: "Instagram"
 *    - social_otomoto (URL) - Label: "OtoMoto"
 */

/**
 * Register ACF Options Pages
 * (Requires ACF Pro or ACF Options Page Add-on)
 */
function salon_auto_register_acf_options_pages() {
    // Safety check - only run if ACF is available
    if (!function_exists('acf_add_options_page')) {
        return;
    }
    
    try {
        acf_add_options_page(array(
            'page_title'    => 'Ustawienia Strony Głównej',
            'menu_title'    => 'Strona Główna',
            'menu_slug'     => 'homepage-settings',
            'capability'    => 'edit_posts',
            'icon_url'      => 'dashicons-admin-home',
        ));
        
        acf_add_options_page(array(
            'page_title'    => 'Ustawienia Ogólne',
            'menu_title'    => 'Ustawienia Ogólne',
            'menu_slug'     => 'general-settings',
            'capability'    => 'edit_posts',
            'icon_url'      => 'dashicons-admin-settings',
        ));
    } catch (Exception $e) {
        // Silently fail - ACF may not be installed
    }
}
// Only add hook if ACF exists
if (function_exists('acf_add_options_page')) {
    add_action('acf/init', 'salon_auto_register_acf_options_pages');
}

/**
 * Auto-register ACF Field Groups via code
 * This creates the field groups programmatically so client doesn't have to set them up manually
 * 
 * IMPORTANT: This requires ACF to be installed and active
 */
function salon_auto_register_acf_field_groups() {
    // Safety check: ACF must be installed
    if (!function_exists('acf_add_local_field_group')) {
        return; // ACF not installed - theme will work with defaults
    }
    
    // Safety check: Don't run if ACF is not fully loaded
    if (!class_exists('ACF')) {
        return;
    }
    
    // Try-catch to prevent fatal errors
    try {
        // Don't register if we're activating theme - can cause 500 errors
        if (defined('WP_INSTALLING') && WP_INSTALLING) {
            return;
        }

    // Field Group 1: Samochody (Car Post Type)
    acf_add_local_field_group(array(
        'key' => 'group_car_fields',
        'title' => 'Samochody',
        'fields' => array(
            array(
                'key' => 'field_price',
                'label' => 'Cena (PLN)',
                'name' => 'price',
                'type' => 'text',
                'instructions' => 'Wpisz cenę w PLN (np. 288000)',
            ),
            array(
                'key' => 'field_year',
                'label' => 'Rok produkcji',
                'name' => 'year',
                'type' => 'number',
            ),
            array(
                'key' => 'field_mileage',
                'label' => 'Przebieg (km)',
                'name' => 'mileage',
                'type' => 'number',
            ),
            array(
                'key' => 'field_gearbox',
                'label' => 'Skrzynia biegów',
                'name' => 'gearbox',
                'type' => 'select',
                'choices' => array(
                    'Automatyczna' => 'Automatyczna',
                    'Manualna' => 'Manualna',
                ),
            ),
            array(
                'key' => 'field_fuel',
                'label' => 'Rodzaj paliwa',
                'name' => 'fuel',
                'type' => 'select',
                'choices' => array(
                    'Benzyna' => 'Benzyna',
                    'Diesel' => 'Diesel',
                    'Hybryda' => 'Hybryda',
                    'Elektryczny' => 'Elektryczny',
                ),
            ),
            array(
                'key' => 'field_trim',
                'label' => 'Wersja/Wyposażenie',
                'name' => 'trim',
                'type' => 'text',
            ),
            array(
                'key' => 'field_brand',
                'label' => 'Marka',
                'name' => 'brand',
                'type' => 'text',
            ),
            array(
                'key' => 'field_model',
                'label' => 'Model',
                'name' => 'model',
                'type' => 'text',
            ),
            array(
                'key' => 'field_color',
                'label' => 'Kolor',
                'name' => 'color',
                'type' => 'text',
            ),
            array(
                'key' => 'field_power_hp',
                'label' => 'Moc (KM)',
                'name' => 'power_hp',
                'type' => 'number',
            ),
            array(
                'key' => 'field_engine_cc',
                'label' => 'Pojemność silnika (cm³)',
                'name' => 'engine_cc',
                'type' => 'number',
            ),
            array(
                'key' => 'field_drivetrain',
                'label' => 'Napęd',
                'name' => 'drivetrain',
                'type' => 'select',
                'choices' => array(
                    '4x4' => '4x4',
                    'FWD' => 'FWD',
                    'RWD' => 'RWD',
                ),
            ),
            array(
                'key' => 'field_accident_free',
                'label' => 'Bezwypadkowe',
                'name' => 'accident_free',
                'type' => 'true_false',
                'default_value' => 0,
            ),
            array(
                'key' => 'field_service_history',
                'label' => 'Historia serwisowa',
                'name' => 'service_history',
                'type' => 'textarea',
            ),
            array(
                'key' => 'field_origin',
                'label' => 'Pochodzenie',
                'name' => 'origin',
                'type' => 'text',
            ),
            array(
                'key' => 'field_owners',
                'label' => 'Liczba właścicieli',
                'name' => 'owners',
                'type' => 'number',
            ),
            array(
                'key' => 'field_vin_masked',
                'label' => 'VIN (maskowany)',
                'name' => 'vin_masked',
                'type' => 'text',
            ),
            array(
                'key' => 'field_lease_from_pln',
                'label' => 'Leasing od (PLN/mies)',
                'name' => 'lease_from_pln',
                'type' => 'number',
            ),
            array(
                'key' => 'field_status',
                'label' => 'Status',
                'name' => 'status',
                'type' => 'select',
                'choices' => array(
                    'available' => 'Dostępny',
                    'sold' => 'Sprzedany',
                    'reserved' => 'Zarezerwowany',
                ),
                'default_value' => 'available',
            ),
            array(
                'key' => 'field_is_featured',
                'label' => 'Wyróżnione',
                'name' => 'is_featured',
                'type' => 'true_false',
                'default_value' => 0,
            ),
            array(
                'key' => 'field_gallery',
                'label' => 'Galeria zdjęć',
                'name' => 'gallery',
                'type' => 'gallery',
                'return_format' => 'array',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'car',
                ),
            ),
        ),
    ));

    // Field Group 2: Ustawienia Strony Głównej (Options Page)
    acf_add_local_field_group(array(
        'key' => 'group_homepage_settings',
        'title' => 'Ustawienia Strony Głównej',
        'fields' => array(
            array(
                'key' => 'field_hero_title',
                'label' => 'Tytuł Hero',
                'name' => 'hero_title',
                'type' => 'text',
                'default_value' => 'Sprawdzone samochody premium i kompleksowa usługa leasingowa.',
            ),
            array(
                'key' => 'field_hero_subtitle',
                'label' => 'Podtytuł Hero',
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'default_value' => 'Dealer aut premium z 28-letnim doświadczeniem.',
            ),
            array(
                'key' => 'field_hero_images',
                'label' => 'Zdjęcia slidera Hero',
                'name' => 'hero_images',
                'type' => 'gallery',
                'instructions' => 'Dodaj zdjęcia do slidera hero (desktop i mobile). Minimum 1 zdjęcie.',
                'return_format' => 'array',
                'min' => 1,
                'max' => 10,
            ),
            array(
                'key' => 'field_about_text',
                'label' => 'Tekst "O nas"',
                'name' => 'about_text',
                'type' => 'textarea',
            ),
            array(
                'key' => 'field_why_us_title',
                'label' => 'Tytuł sekcji "Dlaczego my"',
                'name' => 'why_us_title',
                'type' => 'text',
                'default_value' => 'Dlaczego my?',
            ),
            array(
                'key' => 'field_why_us_items',
                'label' => 'Elementy "Dlaczego my"',
                'name' => 'why_us_items',
                'type' => 'repeater',
                'sub_fields' => array(
                    array(
                        'key' => 'field_why_us_title',
                        'label' => 'Tytuł',
                        'name' => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_why_us_description',
                        'label' => 'Opis',
                        'name' => 'description',
                        'type' => 'textarea',
                    ),
                ),
                'min' => 3,
                'max' => 3,
            ),
            array(
                'key' => 'field_reviews',
                'label' => 'Opinie Klientów',
                'name' => 'reviews',
                'type' => 'repeater',
                'instructions' => 'Dodaj opinie klientów (maksymalnie 6, wyświetlane 3)',
                'sub_fields' => array(
                    array(
                        'key' => 'field_review_name',
                        'label' => 'Imię i nazwisko',
                        'name' => 'name',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_review_content',
                        'label' => 'Treść opinii',
                        'name' => 'content',
                        'type' => 'textarea',
                    ),
                    array(
                        'key' => 'field_review_source',
                        'label' => 'Źródło (Google, Facebook, OtoMoto)',
                        'name' => 'source',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_review_rating',
                        'label' => 'Ocena (1-5)',
                        'name' => 'rating',
                        'type' => 'number',
                        'min' => 1,
                        'max' => 5,
                        'default_value' => 5,
                    ),
                ),
                'min' => 0,
                'max' => 6,
            ),
            array(
                'key' => 'field_cta_title',
                'label' => 'Tytuł sekcji CTA',
                'name' => 'cta_title',
                'type' => 'text',
                'default_value' => 'Serdecznie Zapraszamy',
            ),
            array(
                'key' => 'field_trust_title',
                'label' => 'Tytuł sekcji "Zaufanie"',
                'name' => 'trust_title',
                'type' => 'text',
                'default_value' => 'Jesteśmy z Wami od 1997 roku',
            ),
            array(
                'key' => 'field_trust_text',
                'label' => 'Tekst sekcji "Zaufanie"',
                'name' => 'trust_text',
                'type' => 'textarea',
                'default_value' => '• 28 lat doświadczenia w sprzedaży samochodów premium<br>• 10.000 zrealizowanych leasingów<br>• Pełna gama zadowolonych Klientów VIP',
                'instructions' => 'Możesz używać HTML (np. &lt;br&gt; dla nowych linii)',
            ),
            array(
                'key' => 'field_cert_loza_text',
                'label' => 'Tekst certyfikatu Loża Przedsiębiorców',
                'name' => 'cert_loza_text',
                'type' => 'text',
                'default_value' => 'Członek Loży Przedsiębiorców',
            ),
            array(
                'key' => 'field_cert_loza_url',
                'label' => 'URL certyfikatu Loża Przedsiębiorców',
                'name' => 'cert_loza_url',
                'type' => 'url',
                'default_value' => 'https://lozaprzedsiebiorcow.pl',
            ),
            array(
                'key' => 'field_cert_rzetelna_text',
                'label' => 'Tekst certyfikatu RZETELNA Firma',
                'name' => 'cert_rzetelna_text',
                'type' => 'text',
                'default_value' => 'Uczestnik Programu RZETELNA Firma',
            ),
            // URL certyfikatu RZETELNA Firma - usunięty
            array(
                'key' => 'field_cert_description',
                'label' => 'Opis sekcji certyfikatów',
                'name' => 'cert_description',
                'type' => 'text',
                'default_value' => 'Gwarancja najwyższych standardów obsługi i transparentności',
            ),
            array(
                'key' => 'field_cars_section_title',
                'label' => 'Tytuł sekcji "Dostępne samochody"',
                'name' => 'cars_section_title',
                'type' => 'text',
                'default_value' => 'Dostępne samochody',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'homepage-settings',
                ),
            ),
        ),
    ));

    // Field Group 3: Ustawienia Ogólne (Options Page)
    acf_add_local_field_group(array(
        'key' => 'group_general_settings',
        'title' => 'Ustawienia Ogólne',
        'fields' => array(
            array(
                'key' => 'field_phone',
                'label' => 'Telefon',
                'name' => 'phone',
                'type' => 'text',
                'default_value' => '502 42 82 82',
            ),
            array(
                'key' => 'field_email',
                'label' => 'Email',
                'name' => 'email',
                'type' => 'text',
                'default_value' => 'biuro@piekneauta.pl',
            ),
            array(
                'key' => 'field_address',
                'label' => 'Adres',
                'name' => 'address',
                'type' => 'textarea',
            ),
            array(
                'key' => 'field_social_facebook',
                'label' => 'Facebook',
                'name' => 'social_facebook',
                'type' => 'url',
                'default_value' => 'https://www.facebook.com/Apmleasing',
            ),
            array(
                'key' => 'field_social_instagram',
                'label' => 'Instagram',
                'name' => 'social_instagram',
                'type' => 'url',
                'default_value' => 'https://www.instagram.com/piekne_auta_i_leasing/',
            ),
            array(
                'key' => 'field_social_otomoto',
                'label' => 'OtoMoto',
                'name' => 'social_otomoto',
                'type' => 'url',
                'default_value' => 'https://piekneauta.otomoto.pl',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'general-settings',
                ),
            ),
        ),
    ));
    
    } catch (Exception $e) {
        // Log error but don't break the site
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Salon Auto ACF Error: ' . $e->getMessage());
        }
        return;
    }
}
// Only register ACF fields if ACF is available and we're not in installation mode
if (function_exists('acf_add_local_field_group') && !(defined('WP_INSTALLING') && WP_INSTALLING)) {
    add_action('acf/init', 'salon_auto_register_acf_field_groups');
}

/**
 * Prevent theme styles and fonts from loading in WordPress admin panel
 * This ensures the admin panel looks like standard WordPress
 */
function salon_auto_prevent_theme_styles_in_admin() {
    // Remove all theme styles from admin
    wp_dequeue_style('salon-main');
    wp_dequeue_style('salon-custom-premium');
    wp_dequeue_style('salon-nacja-rebrand');
    wp_dequeue_style('salon-premium-enhancements');
    wp_dequeue_style('salon-auta');
    wp_dequeue_style('google-fonts');
    
    // Deregister to prevent any other code from loading them
    wp_deregister_style('salon-main');
    wp_deregister_style('salon-custom-premium');
    wp_deregister_style('salon-nacja-rebrand');
    wp_deregister_style('salon-premium-enhancements');
    wp_deregister_style('salon-auta');
    wp_deregister_style('google-fonts');
}
add_action('admin_enqueue_scripts', 'salon_auto_prevent_theme_styles_in_admin', 999);
add_action('admin_head', 'salon_auto_prevent_theme_styles_in_admin', 1);

/**
 * Helper function to get car field
 */
function get_car_field($field_name, $post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_field($field_name, $post_id);
}

/**
 * Helper function to format price
 */
function salon_auto_format_price($price) {
    if (!$price || $price === null || $price === '') {
        return esc_html__('Cena do uzgodnienia', 'salon-auto');
    }
    $price = floatval($price);
    return esc_html(number_format($price, 0, ',', ' ') . ' zł');
}

/**
 * Limit Gutenberg blocks - AKTYWNE
 * This ensures client can only use safe blocks and cannot break layout
 */
function salon_auto_allowed_block_types($allowed_blocks, $editor_context) {
    // Default: allow only safe blocks for content editing
    $allowed_blocks = array(
        'core/paragraph',
        'core/heading',
        'core/list',
        'core/image',
        'core/quote',
        'core/separator',
    );
    
    // For car post type, allow even less (only text content)
    if (isset($editor_context->post) && $editor_context->post->post_type === 'car') {
        $allowed_blocks = array(
            'core/paragraph',
            'core/heading',
            'core/list',
        );
    }
    
    // For pages, allow slightly more but still restricted
    if (isset($editor_context->post) && $editor_context->post->post_type === 'page') {
        $allowed_blocks = array(
            'core/paragraph',
            'core/heading',
            'core/list',
            'core/image',
            'core/quote',
            'core/separator',
        );
    }
    
    return $allowed_blocks;
}
add_filter('allowed_block_types_all', 'salon_auto_allowed_block_types', 10, 2);

/**
 * Disable block patterns (prevent client from adding pre-made layouts)
 */
function salon_auto_disable_block_patterns() {
    remove_theme_support('core-block-patterns');
}
add_action('after_setup_theme', 'salon_auto_disable_block_patterns');

/**
 * Disable block directory (prevent installing new blocks)
 */
function salon_auto_disable_block_directory() {
    remove_action('enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets');
}
add_action('admin_init', 'salon_auto_disable_block_directory');

/**
 * Remove layout controls from blocks (prevent adding columns, groups, etc.)
 */
function salon_auto_remove_layout_support() {
    // Remove layout support from blocks that might allow it
    add_filter('block_editor_settings_all', function($settings) {
        $settings['__experimentalLayout'] = false;
        return $settings;
    }, 10, 1);
}
add_action('init', 'salon_auto_remove_layout_support');

/**
 * Add custom body classes
 */
function salon_auto_body_classes($classes) {
    if (is_singular('car')) {
        $classes[] = 'single-car';
    }
    if (is_post_type_archive('car')) {
        $classes[] = 'archive-car';
    }
    return $classes;
}
add_filter('body_class', 'salon_auto_body_classes');

/**
 * Custom excerpt length
 */
function salon_auto_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'salon_auto_excerpt_length');

/**
 * Custom excerpt more
 */
function salon_auto_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'salon_auto_excerpt_more');

/**
 * Add security: Disable file editing in WordPress admin
 */
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

/**
 * Remove WordPress version from head
 */
function salon_auto_remove_version() {
    return '';
}
add_filter('the_generator', 'salon_auto_remove_version');

/**
 * Remove WordPress version from scripts and styles
 */
function salon_auto_remove_script_version($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'salon_auto_remove_script_version', 15, 1);
add_filter('style_loader_src', 'salon_auto_remove_script_version', 15, 1);

/**
 * Disable emoji scripts (performance)
 */
function salon_auto_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'salon_auto_disable_emojis');

/**
 * Remove unnecessary WordPress head elements
 */
function salon_auto_clean_head() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
}
add_action('init', 'salon_auto_clean_head');

/**
 * Sanitize ACF field output helper
 */
function salon_auto_get_field_safe($field_name, $post_id = null, $default = '') {
    $value = function_exists('get_field') ? get_field($field_name, $post_id) : $default;
    if (!$value) {
        return $default;
    }
    return $value;
}

/**
 * Sanitize and format phone number
 */
function salon_auto_sanitize_phone($phone) {
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    return $phone;
}

/**
 * Properly enqueue jQuery (WordPress best practice)
 * Note: WordPress includes jQuery by default, we just ensure it loads in footer
 */
function salon_auto_enqueue_jquery() {
    if (!is_admin()) {
        wp_deregister_script('jquery');
        wp_register_script('jquery', includes_url('/js/jquery/jquery.min.js'), false, null, true);
    }
}
add_action('wp_enqueue_scripts', 'salon_auto_enqueue_jquery', 1);

/**
 * Add preconnect for external resources (performance)
 */
function salon_auto_resource_hints($urls, $relation_type) {
    if ('preconnect' === $relation_type) {
        $urls[] = array(
            'href' => 'https://cdn.jsdelivr.net',
            'crossorigin',
        );
    }
    return $urls;
}
add_filter('wp_resource_hints', 'salon_auto_resource_hints', 10, 2);

/**
 * Add proper image lazy loading support
 */
function salon_auto_lazy_load_images($attr, $attachment, $size) {
    if (!is_admin()) {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'salon_auto_lazy_load_images', 10, 3);

/**
 * Remove query strings from static resources (caching)
 */
function salon_auto_remove_query_strings($src) {
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'salon_auto_remove_query_strings', 15, 1);
add_filter('style_loader_src', 'salon_auto_remove_query_strings', 15, 1);

/**
 * Define import folder path constant if not already defined
 * Can be overridden in wp-config.php: define('SALON_AUTO_IMPORT_FOLDER', '/path/to/folder');
 */
if (!defined('SALON_AUTO_IMPORT_FOLDER')) {
    // Default: look for "piekneauta-kopia 2" folder in parent directory
    $default_path = dirname(dirname(dirname(__FILE__))) . '/piekneauta-kopia 2';
    define('SALON_AUTO_IMPORT_FOLDER', $default_path);
}

/**
 * CRITICAL FIX: Fallback functions if includes don't load properly
 * These ensure the site won't break even if custom-fields.php or options-pages.php fail to load
 */

/**
 * Cache dla opcji strony głównej - poprawia wydajność
 */
if (!function_exists('salon_auto_get_cached_option')) {
    function salon_auto_get_cached_option($option_name, $default = '', $cache_group = 'salon_auto_options') {
        $cache_key = $cache_group . '_' . $option_name;
        $cached = wp_cache_get($cache_key, $cache_group);
        
        if (false !== $cached) {
            return $cached;
        }
        
        $value = get_option('salon_auto_' . $option_name, $default);
        wp_cache_set($cache_key, $value, $cache_group, HOUR_IN_SECONDS);
        
        return $value;
    }
}

/**
 * Wyczyść cache opcji po aktualizacji
 */
if (!function_exists('salon_auto_clear_options_cache')) {
    function salon_auto_clear_options_cache() {
        wp_cache_flush_group('salon_auto_options');
    }
    add_action('update_option', 'salon_auto_clear_options_cache_on_update', 10, 2);
    function salon_auto_clear_options_cache_on_update($option, $old_value) {
        if (strpos($option, 'salon_auto_') === 0) {
            salon_auto_clear_options_cache();
        }
    }
}

/**
 * Cache dla opcji strony głównej - poprawia wydajność
 */
if (!function_exists('salon_auto_get_cached_option')) {
    function salon_auto_get_cached_option($option_name, $default = '', $cache_group = 'salon_auto_options') {
        $cache_key = $cache_group . '_' . $option_name;
        $cached = wp_cache_get($cache_key, $cache_group);
        
        if (false !== $cached) {
            return $cached;
        }
        
        $value = get_option('salon_auto_' . $option_name, $default);
        wp_cache_set($cache_key, $value, $cache_group, HOUR_IN_SECONDS);
        
        return $value;
    }
}

/**
 * Wyczyść cache opcji po aktualizacji
 */
if (!function_exists('salon_auto_clear_options_cache')) {
    function salon_auto_clear_options_cache() {
        wp_cache_flush_group('salon_auto_options');
    }
    add_action('update_option', 'salon_auto_clear_options_cache_on_update', 10, 2);
    function salon_auto_clear_options_cache_on_update($option, $old_value) {
        if (strpos($option, 'salon_auto_') === 0) {
            salon_auto_clear_options_cache();
        }
    }
}

/**
 * Fallback for salon_auto_get_option() if not defined in includes/options-pages.php
 * Używa cache dla lepszej wydajności
 */
if (!function_exists('salon_auto_get_option')) {
    function salon_auto_get_option($field_name, $default = '') {
        // Użyj cache jeśli dostępny
        if (function_exists('salon_auto_get_cached_option')) {
            $value = salon_auto_get_cached_option($field_name, '', 'salon_auto_options');
        } else {
        // Try custom option first
        $value = get_option('salon_auto_' . $field_name, '');
        }
        
        // Fallback to ACF if exists
        if (empty($value) && function_exists('get_field')) {
            $value = get_field($field_name, 'option');
        }
        
        return $value ? $value : $default;
    }
}

/**
 * Fallback for salon_auto_get_car_field() if not defined in includes/custom-fields.php
 */
if (!function_exists('salon_auto_get_car_field')) {
    function salon_auto_get_car_field($post_id, $field_name, $default = '') {
        // Try custom field first (without prefix, as saved in custom-fields.php)
        $value = get_post_meta($post_id, $field_name, true);
        
        // Fallback to old prefixed keys for compatibility
        if (empty($value)) {
            $value = get_post_meta($post_id, 'salon_auto_' . $field_name, true);
        }
        
        // Fallback to ACF if exists
        if (empty($value) && function_exists('get_field')) {
            $value = get_field($field_name, $post_id);
        }
        
        return $value ? $value : $default;
    }
}

/**
 * Helper function to get import folder paths
 * Returns array of possible paths to check for import files
 */
if (!function_exists('salon_auto_get_import_paths')) {
    function salon_auto_get_import_paths() {
        $paths = array();
        
        // Check if constant is defined and path exists
        if (defined('SALON_AUTO_IMPORT_FOLDER') && file_exists(SALON_AUTO_IMPORT_FOLDER)) {
            $paths[] = SALON_AUTO_IMPORT_FOLDER;
        }
        
        // Fallback paths - check directories, not files
        $fallback_dirs = array(
            get_template_directory(),
            dirname(dirname(dirname(__FILE__))),
            dirname(dirname(dirname(__FILE__))) . '/piekneauta-kopia 2',
        );
        
        foreach ($fallback_dirs as $dir) {
            if ($dir && file_exists($dir)) {
                $paths[] = $dir;
            }
        }
        
        return array_unique(array_filter($paths));
    }
}

/**
 * Helper function to validate image attachment
 * Returns true if attachment exists and is an image
 */
function salon_auto_validate_image($attachment_id) {
    if (empty($attachment_id) || !is_numeric($attachment_id)) {
        return false;
    }
    
    $attachment_id = intval($attachment_id);
    
    if ($attachment_id <= 0) {
        return false;
    }
    
    // Check if attachment exists
    $attachment = get_post($attachment_id);
    if (!$attachment || $attachment->post_type !== 'attachment') {
        return false;
    }
    
    // Check if it's an image
    if (!wp_attachment_is_image($attachment_id)) {
        return false;
    }
    
    return true;
}

/**
 * Helper function to get image URL safely with validation
 */
function salon_auto_get_image_url($attachment_id, $size = 'thumbnail') {
    if (!salon_auto_validate_image($attachment_id)) {
        return false;
    }
    
    $image_url = wp_get_attachment_image_url($attachment_id, $size);
    
    return $image_url ? $image_url : false;
}

/**
 * Cache helper for car queries
 * Returns cached query results or executes new query
 */
if (!function_exists('salon_auto_get_cached_cars')) {
    function salon_auto_get_cached_cars($args = array(), $cache_key = '', $expiration = null) {
        // Safety check
        if (!function_exists('get_transient') || !function_exists('set_transient')) {
            return false;
        }
        
        // Use WordPress constant if available, otherwise default to 3600 (1 hour)
        if ($expiration === null) {
            $expiration = defined('HOUR_IN_SECONDS') ? HOUR_IN_SECONDS : 3600;
        }
    // Generate cache key if not provided
    if (empty($cache_key)) {
        $cache_key = 'salon_auto_cars_' . md5(serialize($args));
    }
    
    // Try to get from cache
    $cached = get_transient($cache_key);
    
    if (false !== $cached) {
        return $cached;
    }
    
    // Execute query
    $defaults = array(
        'post_type'      => 'car',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );
    
    $query_args = wp_parse_args($args, $defaults);
    $query = new WP_Query($query_args);
    
    // Prepare results
    $results = array(
        'posts' => array(),
        'found_posts' => $query->found_posts,
    );
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $results['posts'][] = array(
                'ID' => $post_id,
                'title' => get_the_title(),
                'permalink' => get_permalink(),
                'post' => get_post(),
            );
        }
        wp_reset_postdata();
    }
    
    // Cache results
    set_transient($cache_key, $results, $expiration);
    
    return $results;
    }
}

/**
 * Clear car query cache (useful after car updates)
 */
if (!function_exists('salon_auto_clear_cars_cache')) {
    function salon_auto_clear_cars_cache() {
    global $wpdb;
    
    // Delete all transients starting with 'salon_auto_cars_'
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE %s 
            OR option_name LIKE %s",
            $wpdb->esc_like('_transient_salon_auto_cars_') . '%',
            $wpdb->esc_like('_transient_timeout_salon_auto_cars_') . '%'
        )
    );
    }
}

// Clear cache when car post is saved/updated - SAFELY with checks
if (function_exists('salon_auto_clear_cars_cache')) {
    add_action('save_post_car', function($post_id) {
        if (function_exists('salon_auto_clear_cars_cache')) {
            try {
                salon_auto_clear_cars_cache();
            } catch (Exception $e) {
                // Silently fail
            }
        }
    });
    
    add_action('delete_post', function($post_id) {
        if (function_exists('salon_auto_clear_cars_cache')) {
            try {
                $post_type = get_post_type($post_id);
                if ($post_type === 'car') {
                    salon_auto_clear_cars_cache();
                }
            } catch (Exception $e) {
                // Silently fail
            }
        }
    });
}

/**
 * Register REST API endpoint for cars
 */
add_action('rest_api_init', function() {
    register_rest_route('salon-auto/v1', '/cars', array(
        'methods' => 'GET',
        'callback' => 'salon_auto_rest_get_cars',
        'permission_callback' => '__return_true',
    ));
    
    // Register REST API endpoint for car gallery (admin only)
    register_rest_route('salon-auto/v1', '/car-gallery/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'salon_auto_rest_get_car_gallery',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        },
        'args' => array(
            'id' => array(
                'required' => true,
                'validate_callback' => function($param) {
                    return is_numeric($param);
                }
            ),
        ),
    ));
});

/**
 * REST API callback to get cars
 */
if (!function_exists('salon_auto_rest_get_cars')) {
    function salon_auto_rest_get_cars($request) {
        $status = $request->get_param('status');
        $exclude_slug = $request->get_param('exclude_slug');
        $limit = intval($request->get_param('limit')) ?: -1;
        
        // Pobierz więcej niż limit aby po wykluczeniu zostało tyle ile potrzeba
        $fetch_limit = ($limit > 0 && $exclude_slug) ? $limit + 1 : $limit;
        
        $args = array(
            'post_type' => 'car',
            'posts_per_page' => $fetch_limit,
            'post_status' => 'publish',
            'orderby' => 'rand', // Losowa kolejność dla podobnych ofert
            'order' => 'ASC',
        );
        
        // Wyklucz samochód po slug bezpośrednio w query jeśli możliwe
        if ($exclude_slug) {
            $exclude_post = get_page_by_path($exclude_slug, OBJECT, 'car');
            if ($exclude_post) {
                $args['post__not_in'] = array($exclude_post->ID);
                // Pobierz dokładnie tyle ile potrzeba
                $args['posts_per_page'] = $limit;
            }
        }
        
        if ($status) {
            // Filter by status via meta query
            $args['meta_query'] = array(
                array(
                    'key' => 'status',
                    'value' => $status,
                    'compare' => '='
                )
            );
        } else {
            // Domyślnie wyklucz sprzedane samochody z podobnych ofert
            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => 'status',
                    'value' => 'sold',
                    'compare' => '!='
                ),
                array(
                    'key' => 'status',
                    'compare' => 'NOT EXISTS'
                )
            );
        }
        
        $query = new WP_Query($args);
        $cars = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                $post_slug = get_post_field('post_name', $post_id);
                
                // Dodatkowe sprawdzenie - skip excluded slug
                if ($exclude_slug && $post_slug === $exclude_slug) {
                    continue;
                }
                
                // Ogranicz do limitu
                if ($limit > 0 && count($cars) >= $limit) {
                    break;
                }
                
                $brand = salon_auto_get_car_field($post_id, 'brand');
                $model = salon_auto_get_car_field($post_id, 'model');
                $trim = salon_auto_get_car_field($post_id, 'trim');
                $price = salon_auto_get_car_field($post_id, 'price');
                $year = salon_auto_get_car_field($post_id, 'year');
                $car_status = salon_auto_get_car_field($post_id, 'status');
                
                // Get images - UŻYJ DOKŁADNIE TEJ SAMEJ FUNKCJI CO archive-car.php
                $car_gallery = salon_auto_get_car_gallery($post_id);
                $images = array();
                
                // Konwertuj format z funkcji salon_auto_get_car_gallery() do prostych URL-i
                foreach ($car_gallery as $img) {
                    if (is_array($img) && isset($img['url'])) {
                        $images[] = $img['url'];
                    } elseif (is_string($img)) {
                        $images[] = $img;
                    }
                }
                
                // Limit do 15 zdjęć (jak w archive-car.php)
                $images = array_slice($images, 0, 15);
                
                $cars[] = array(
                    'id' => $post_id,
                    'slug' => $post_slug,
                    'brand' => $brand,
                    'model' => $model,
                    'trim' => $trim,
                    'year' => $year,
                    'price' => $price,
                    'price_pln_brutto' => $price,
                    'status' => $car_status ?: 'available',
                    'url' => get_permalink($post_id),
                    'images' => $images, // Zawsze zwracaj tablicę (nawet jeśli pusta)
                );
            }
            wp_reset_postdata();
        }
        
        return new WP_REST_Response($cars, 200);
    }
}

/**
 * REST API callback to get car gallery
 */
if (!function_exists('salon_auto_rest_get_car_gallery')) {
    function salon_auto_rest_get_car_gallery($request) {
        $car_id = intval($request->get_param('id'));
        
        if ($car_id <= 0) {
            return new WP_Error('invalid_id', 'Invalid car ID', array('status' => 400));
        }
        
        $car_post = get_post($car_id);
        if (!$car_post || $car_post->post_type !== 'car') {
            return new WP_Error('not_found', 'Car not found', array('status' => 404));
        }
        
        // Get gallery using the theme function - tak samo jak na single-car.php
        $gallery = salon_auto_get_car_gallery($car_id);
        
        // Get featured image (thumbnail) - powinno być pierwsze
        $featured_image = null;
        if (has_post_thumbnail($car_id)) {
            $thumb_id = get_post_thumbnail_id($car_id);
            $thumb_url = wp_get_attachment_image_url($thumb_id, 'large');
            $thumb_thumbnail = wp_get_attachment_image_url($thumb_id, 'medium');
            
            if ($thumb_url) {
                $featured_image = array(
                    'id' => $thumb_id,
                    'url' => $thumb_url,
                    'thumbnail' => $thumb_thumbnail ?: $thumb_url,
                    'alt' => get_the_title($car_id),
                    'source' => 'featured',
                    'hasId' => true
                );
            }
        }
        
        // Format for JSON response - zwróć wszystkie zdjęcia (tak jak na single-car.php)
        // Dla zdjęć z biblioteki mediów używamy ID, dla zdjęć z motywu - URL jako identyfikator
        $formatted_gallery = array();
        
        // Add featured image first if it exists and is not already in gallery
        if ($featured_image) {
            $featured_in_gallery = false;
            foreach ($gallery as $img) {
                if (is_array($img) && isset($img['id']) && $img['id'] == $featured_image['id']) {
                    $featured_in_gallery = true;
                    break;
                }
            }
            if (!$featured_in_gallery) {
                $formatted_gallery[] = $featured_image;
            }
        }
        
        foreach ($gallery as $img) {
            if (is_array($img) && isset($img['url'])) {
                // Jeśli zdjęcie ma ID (z biblioteki mediów), użyj ID
                // Jeśli nie ma ID (z motywu), użyj URL jako identyfikator
                $img_id = isset($img['id']) && $img['id'] > 0 ? $img['id'] : null;
                
                $formatted_gallery[] = array(
                    'id' => $img_id, // null dla zdjęć z motywu
                    'url' => $img['url'],
                    'thumbnail' => isset($img['thumbnail']) ? $img['thumbnail'] : $img['url'],
                    'alt' => isset($img['alt']) ? $img['alt'] : '',
                    'source' => isset($img['source']) ? $img['source'] : 'unknown',
                    'hasId' => $img_id !== null // Flaga czy można użyć w sliderze (tylko z ID)
                );
            }
        }
        
        return new WP_REST_Response($formatted_gallery, 200);
    }
}

/**
 * Auto-setup homepage and catalog cars based on static version
 * This function ensures correct cars are displayed on homepage and archive
 * Can be triggered manually by adding ?setup_cars=1 to admin URL
 */
add_action('admin_init', function() {
    // Check if manual trigger
    $manual_trigger = isset($_GET['setup_cars']) && $_GET['setup_cars'] === '1';
    
    // Only run if options are empty or on manual trigger
    $homepage_cars = get_option('salon_auto_homepage_cars', array());
    $catalog_cars = get_option('salon_auto_catalog_cars', array());
    
    // Check if we need to setup homepage cars
    $needs_homepage_setup = $manual_trigger || empty($homepage_cars) || count($homepage_cars) < 3;
    
    // Check if we need to setup catalog cars (6 cars on static page)
    $needs_catalog_setup = $manual_trigger || empty($catalog_cars) || count($catalog_cars) < 6;
    
    if ($needs_homepage_setup || $needs_catalog_setup) {
        // Setup homepage cars: SQ8, RS5, A8 (in that order)
        if ($needs_homepage_setup) {
            $homepage_slugs = array('audi-sq8-2023', 'audi-rs5-2023-450hp-individual', 'audi-a8-2019-50tdi-quattro');
            $homepage_cars_setup = array();
            
            foreach ($homepage_slugs as $slug) {
                $post = get_page_by_path($slug, OBJECT, 'car');
                if (!$post) {
                    $posts = get_posts(array('post_type' => 'car', 'name' => $slug, 'posts_per_page' => 1));
                    $post = !empty($posts) ? $posts[0] : null;
                }
                
                if ($post) {
                    $car_id = $post->ID;
                    $slider_images = '';
                    
                    // Get gallery images
                    $gallery_ids = get_post_meta($car_id, 'gallery', true);
                    if (is_string($gallery_ids)) {
                        $gallery_ids = explode(',', $gallery_ids);
                    }
                    if (!is_array($gallery_ids)) {
                        $gallery_ids = array();
                    }
                    
                    // Special handling for RS5 - skip first image (audi-rs5-01.jpg)
                    if ($slug === 'audi-rs5-2023-450hp-individual') {
                        // Find all RS5 images and exclude audi-rs5-01.jpg
                        $rs5_images = array();
                        foreach ($gallery_ids as $img_id) {
                            $img_id = intval($img_id);
                            if ($img_id > 0) {
                                // Get attachment file path
                                $img_path = get_attached_file($img_id);
                                $filename = $img_path ? basename($img_path) : '';
                                
                                // If we can't get from file path, try URL
                                if (empty($filename)) {
                                    $img_url = wp_get_attachment_image_url($img_id, 'full');
                                    if ($img_url) {
                                        $filename = basename(parse_url($img_url, PHP_URL_PATH));
                                    }
                                }
                                
                                // Exclude audi-rs5-01.jpg but include all other RS5 images
                                if (empty($filename) || (strpos($filename, 'audi-rs5') !== false && strpos($filename, 'audi-rs5-01.jpg') === false)) {
                                    $rs5_images[] = $img_id;
                                }
                            }
                        }
                        // Take first 10 images (audi-rs5-02.jpg to audi-rs5-11.jpg)
                        $rs5_images = array_slice($rs5_images, 0, 10);
                        $slider_images = implode(',', $rs5_images);
                    } else {
                        // For other cars, use all gallery images (up to 15 for SQ8, 5 for A8)
                        $max_images = ($slug === 'audi-sq8-2023') ? 15 : (($slug === 'audi-a8-2019-50tdi-quattro') ? 5 : 15);
                        $gallery_ids = array_slice($gallery_ids, 0, $max_images);
                        $slider_images = implode(',', array_filter(array_map('intval', $gallery_ids)));
                    }
                    
                    $homepage_cars_setup[] = array(
                        'car_id' => $car_id,
                        'slider_images' => $slider_images
                    );
                }
            }
            
            if (!empty($homepage_cars_setup)) {
                update_option('salon_auto_homepage_cars', $homepage_cars_setup);
            }
        }
        
        // Setup catalog cars - EXACTLY as on static page piekneauta.pl/pages/samochody/ (6 cars)
        if ($needs_catalog_setup) {
            $catalog_data = array(
                array('slug' => 'audi-rs5-2023-450hp-individual', 'brand' => 'Audi', 'model' => 'RS5', 'trim' => '450 KM Individual & Exclusive', 'price' => 255000, 'year' => 2021),
                array('slug' => 'audi-sq8-2023', 'brand' => 'Audi', 'model' => 'SQ8', 'trim' => '4.0 TFSI Quattro', 'price' => 288000, 'year' => 2023),
                array('slug' => 'audi-a6-limousine', 'brand' => 'Audi', 'model' => 'A6', 'trim' => 'Limousine', 'price' => 117000, 'year' => 2018),
                array('slug' => 'audi-a8-2019-50tdi-quattro', 'brand' => 'Audi', 'model' => 'A8', 'trim' => '50 TDI mHEV Quattro Tiptronic', 'price' => 185000, 'year' => 2020),
                array('slug' => 'bmw-seria-7-2018-730d-xdrive', 'brand' => 'BMW', 'model' => 'Seria 7 Long', 'trim' => '740Li xDrive iPerformance', 'price' => 163000, 'year' => 2018),
                array('slug' => 'cupra-formentor-2023-tfsi', 'brand' => 'Cupra', 'model' => 'Formentor', 'trim' => 'TFSI', 'price' => 95000, 'year' => 2023),
            );
            
            $catalog_cars_setup = array();
            
            foreach ($catalog_data as $car_data) {
                $post = get_page_by_path($car_data['slug'], OBJECT, 'car');
                if (!$post) {
                    $posts = get_posts(array('post_type' => 'car', 'name' => $car_data['slug'], 'posts_per_page' => 1));
                    $post = !empty($posts) ? $posts[0] : null;
                }
                
                if ($post) {
                    // Update car data to match static version
                    update_post_meta($post->ID, 'brand', $car_data['brand']);
                    update_post_meta($post->ID, 'model', $car_data['model']);
                    update_post_meta($post->ID, 'trim', $car_data['trim']);
                    update_post_meta($post->ID, 'price', $car_data['price']);
                    if (isset($car_data['year'])) update_post_meta($post->ID, 'year', $car_data['year']);
                    update_post_meta($post->ID, 'status', 'available');
                    
                    // Get main image from gallery_files (first image)
                    $gallery_files = get_post_meta($post->ID, 'gallery_files', true);
                    $main_image_id = 0;
                    
                    if (!empty($gallery_files)) {
                        // gallery_files can be comma-separated filenames or IDs
                        $files = array_filter(array_map('trim', explode(',', $gallery_files)));
                        if (!empty($files)) {
                            $first_file = $files[0];
                            // If it's numeric, it's an attachment ID
                            if (is_numeric($first_file)) {
                                $main_image_id = intval($first_file);
                            } else {
                                // If it's a filename, try to find attachment by filename
                                $theme_dir = get_stylesheet_directory() . '/images/';
                                $filename = basename($first_file);
                                if (file_exists($theme_dir . $filename)) {
                                    // Try to find attachment by filename
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
                                        $main_image_id = $attachments[0]->ID;
                                    }
                                }
                            }
                        }
                    }
                    
                    // If no main image found, try featured image
                    if ($main_image_id === 0 && has_post_thumbnail($post->ID)) {
                        $main_image_id = get_post_thumbnail_id($post->ID);
                    }
                    
                    $catalog_cars_setup[] = array(
                        'car_id' => $post->ID,
                        'custom_image_id' => $main_image_id,
                        'custom_caption' => $car_data['trim']
                    );
                }
            }
            
            if (!empty($catalog_cars_setup)) {
                update_option('salon_auto_catalog_cars', $catalog_cars_setup);
            }
        }
    }
});

