<?php
/**
 * SEO & Sitemap - Dynamiczny system dla Google
 * 
 * - Dynamiczny sitemap.xml (auto-aktualizowany przy zmianach w autach)
 * - Wirtualny robots.txt
 * - Ping do Google/Bing przy zmianach
 * - Lastmod tracking
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * =====================================================
 * DYNAMICZNY SITEMAP.XML
 * =====================================================
 */

/**
 * Rewrite rules dla sitemap.xml i robots.txt
 */
function salon_auto_seo_rewrite_rules() {
    add_rewrite_rule('^sitemap\.xml$', 'index.php?salon_auto_sitemap=1', 'top');
    add_rewrite_rule('^sitemap-cars\.xml$', 'index.php?salon_auto_sitemap=cars', 'top');
    add_rewrite_rule('^sitemap-pages\.xml$', 'index.php?salon_auto_sitemap=pages', 'top');
}
add_action('init', 'salon_auto_seo_rewrite_rules');

/**
 * Query vars
 */
function salon_auto_seo_query_vars($vars) {
    $vars[] = 'salon_auto_sitemap';
    return $vars;
}
add_filter('query_vars', 'salon_auto_seo_query_vars');

/**
 * Obsługa sitemap
 */
function salon_auto_handle_sitemap() {
    $sitemap = get_query_var('salon_auto_sitemap');
    
    if ($sitemap) {
        header('Content-Type: application/xml; charset=utf-8');
        header('X-Robots-Tag: noindex, follow');
        
        if ($sitemap === 'cars') {
            echo salon_auto_generate_cars_sitemap();
        } elseif ($sitemap === 'pages') {
            echo salon_auto_generate_pages_sitemap();
        } else {
            echo salon_auto_generate_sitemap_index();
        }
        exit;
    }
}
add_action('template_redirect', 'salon_auto_handle_sitemap');

/**
 * Sitemap Index - główny plik
 */
function salon_auto_generate_sitemap_index() {
    $site_url = home_url('/');
    $cars_lastmod = salon_auto_get_cars_lastmod();
    $pages_lastmod = salon_auto_get_pages_lastmod();
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // Sitemap samochodów
    $xml .= '  <sitemap>' . "\n";
    $xml .= '    <loc>' . esc_url($site_url . 'sitemap-cars.xml') . '</loc>' . "\n";
    $xml .= '    <lastmod>' . $cars_lastmod . '</lastmod>' . "\n";
    $xml .= '  </sitemap>' . "\n";
    
    // Sitemap stron
    $xml .= '  <sitemap>' . "\n";
    $xml .= '    <loc>' . esc_url($site_url . 'sitemap-pages.xml') . '</loc>' . "\n";
    $xml .= '    <lastmod>' . $pages_lastmod . '</lastmod>' . "\n";
    $xml .= '  </sitemap>' . "\n";
    
    $xml .= '</sitemapindex>';
    
    return $xml;
}

/**
 * Sitemap samochodów
 */
function salon_auto_generate_cars_sitemap() {
    $site_url = home_url('/');
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
    $xml .= 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
    
    // Archiwum samochodów
    $xml .= '  <url>' . "\n";
    $xml .= '    <loc>' . esc_url($site_url . 'samochody/') . '</loc>' . "\n";
    $xml .= '    <lastmod>' . salon_auto_get_cars_lastmod() . '</lastmod>' . "\n";
    $xml .= '    <changefreq>daily</changefreq>' . "\n";
    $xml .= '    <priority>0.9</priority>' . "\n";
    $xml .= '  </url>' . "\n";
    
    // Pojedyncze samochody
    $cars = get_posts(array(
        'post_type' => 'car',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'modified',
        'order' => 'DESC'
    ));
    
    foreach ($cars as $car) {
        $status = get_post_meta($car->ID, '_car_status', true);
        
        // Samochody sprzedane mają niższy priorytet
        $priority = ($status === 'sold') ? '0.4' : '0.8';
        $changefreq = ($status === 'sold') ? 'monthly' : 'weekly';
        
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . esc_url(get_permalink($car->ID)) . '</loc>' . "\n";
        $xml .= '    <lastmod>' . get_the_modified_date('Y-m-d', $car->ID) . '</lastmod>' . "\n";
        $xml .= '    <changefreq>' . $changefreq . '</changefreq>' . "\n";
        $xml .= '    <priority>' . $priority . '</priority>' . "\n";
        
        // Dodaj główne zdjęcie do sitemap
        $gallery = get_post_meta($car->ID, '_car_gallery', true);
        if (!empty($gallery) && is_array($gallery)) {
            $first_image_id = reset($gallery);
            $image_url = wp_get_attachment_image_url($first_image_id, 'full');
            $brand = get_post_meta($car->ID, '_car_brand', true);
            $model = get_post_meta($car->ID, '_car_model', true);
            
            if ($image_url) {
                $xml .= '    <image:image>' . "\n";
                $xml .= '      <image:loc>' . esc_url($image_url) . '</image:loc>' . "\n";
                $xml .= '      <image:title>' . esc_html($brand . ' ' . $model) . '</image:title>' . "\n";
                $xml .= '    </image:image>' . "\n";
            }
        }
        
        $xml .= '  </url>' . "\n";
    }
    
    $xml .= '</urlset>';
    
    return $xml;
}

/**
 * Sitemap stron statycznych
 */
function salon_auto_generate_pages_sitemap() {
    $site_url = home_url('/');
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // Strona główna
    $xml .= '  <url>' . "\n";
    $xml .= '    <loc>' . esc_url($site_url) . '</loc>' . "\n";
    $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
    $xml .= '    <changefreq>weekly</changefreq>' . "\n";
    $xml .= '    <priority>1.0</priority>' . "\n";
    $xml .= '  </url>' . "\n";
    
    // Strony statyczne
    $pages_config = array(
        'kontakt' => array('priority' => '0.8', 'changefreq' => 'monthly'),
        'o-nas' => array('priority' => '0.7', 'changefreq' => 'monthly'),
        'leasing' => array('priority' => '0.8', 'changefreq' => 'weekly'),
        'ubezpieczenia' => array('priority' => '0.8', 'changefreq' => 'weekly'),
        'pozyczki' => array('priority' => '0.7', 'changefreq' => 'weekly'),
        'regulamin' => array('priority' => '0.3', 'changefreq' => 'yearly'),
        'polityka-prywatnosci' => array('priority' => '0.3', 'changefreq' => 'yearly'),
    );
    
    foreach ($pages_config as $slug => $config) {
        $page = get_page_by_path($slug);
        if ($page && $page->post_status === 'publish') {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . esc_url(get_permalink($page->ID)) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . get_the_modified_date('Y-m-d', $page->ID) . '</lastmod>' . "\n";
            $xml .= '    <changefreq>' . $config['changefreq'] . '</changefreq>' . "\n";
            $xml .= '    <priority>' . $config['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
    }
    
    $xml .= '</urlset>';
    
    return $xml;
}

/**
 * Ostatnia modyfikacja samochodów
 */
function salon_auto_get_cars_lastmod() {
    $latest = get_posts(array(
        'post_type' => 'car',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'orderby' => 'modified',
        'order' => 'DESC'
    ));
    
    if (!empty($latest)) {
        return get_the_modified_date('Y-m-d', $latest[0]->ID);
    }
    
    return date('Y-m-d');
}

/**
 * Ostatnia modyfikacja stron
 */
function salon_auto_get_pages_lastmod() {
    $latest = get_posts(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'orderby' => 'modified',
        'order' => 'DESC'
    ));
    
    if (!empty($latest)) {
        return get_the_modified_date('Y-m-d', $latest[0]->ID);
    }
    
    return date('Y-m-d');
}

/**
 * =====================================================
 * ROBOTS.TXT
 * =====================================================
 */

/**
 * Wirtualny robots.txt
 */
function salon_auto_robots_txt($output, $public) {
    $site_url = home_url('/');
    
    $output = "# Robots.txt dla PiękneAuta.pl\n";
    $output .= "# Wygenerowany automatycznie przez WordPress\n\n";
    
    $output .= "User-agent: *\n";
    
    if ($public) {
        // Zezwól na indeksowanie
        $output .= "Allow: /\n\n";
        
        // Blokuj obszary admina i wrażliwe
        $output .= "# Blokowane obszary\n";
        $output .= "Disallow: /wp-admin/\n";
        $output .= "Disallow: /wp-includes/\n";
        $output .= "Disallow: /wp-content/plugins/\n";
        $output .= "Disallow: /wp-content/cache/\n";
        $output .= "Disallow: /wp-content/uploads/temp/\n";
        $output .= "Disallow: /*?*\n";
        $output .= "Disallow: /*/feed/\n";
        $output .= "Disallow: /tag/\n";
        $output .= "Disallow: /author/\n";
        $output .= "Disallow: /trackback/\n";
        $output .= "Disallow: /xmlrpc.php\n";
        $output .= "Disallow: /wp-json/\n\n";
        
        // Zezwól na AJAX i zasoby
        $output .= "# Zezwolone zasoby\n";
        $output .= "Allow: /wp-admin/admin-ajax.php\n";
        $output .= "Allow: /wp-content/uploads/\n";
        $output .= "Allow: /wp-content/themes/salon-auto/assets/\n\n";
        
        // Crawl delay
        $output .= "# Crawl delay (opcjonalnie)\n";
        $output .= "Crawl-delay: 1\n\n";
    } else {
        // Strona niepubliczna - zablokuj wszystko
        $output .= "Disallow: /\n\n";
    }
    
    // Sitemap
    $output .= "# Sitemap\n";
    $output .= "Sitemap: " . esc_url($site_url . 'sitemap.xml') . "\n";
    
    return $output;
}
add_filter('robots_txt', 'salon_auto_robots_txt', 10, 2);

/**
 * Flush rewrite rules po aktywacji motywu
 */
function salon_auto_flush_rewrite_rules() {
    salon_auto_seo_rewrite_rules();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'salon_auto_flush_rewrite_rules');

/**
 * =====================================================
 * PING DO WYSZUKIWAREK
 * =====================================================
 */

/**
 * Ping Google i Bing po dodaniu/zmianie/usunięciu samochodu
 */
function salon_auto_ping_search_engines($post_id, $post = null, $update = false) {
    // Tylko dla samochodów
    if (get_post_type($post_id) !== 'car') {
        return;
    }
    
    // Tylko dla opublikowanych postów
    $post_status = get_post_status($post_id);
    if ($post_status !== 'publish') {
        return;
    }
    
    // Nie pinguj zbyt często (max raz na 5 minut dla danego posta)
    $last_ping = get_transient('salon_auto_ping_' . $post_id);
    if ($last_ping) {
        return;
    }
    
    // Ustaw transient na 5 minut
    set_transient('salon_auto_ping_' . $post_id, time(), 5 * MINUTE_IN_SECONDS);
    
    // Zaplanuj ping w tle (nie blokuje zapisywania)
    wp_schedule_single_event(time() + 10, 'salon_auto_do_ping_search_engines');
}
add_action('save_post_car', 'salon_auto_ping_search_engines', 99, 3);
add_action('delete_post', 'salon_auto_ping_search_engines', 99);

/**
 * Wykonaj ping w tle
 */
function salon_auto_do_ping_search_engines() {
    $sitemap_url = home_url('/sitemap.xml');
    
    // Google
    $google_ping = 'https://www.google.com/ping?sitemap=' . urlencode($sitemap_url);
    wp_remote_get($google_ping, array('timeout' => 5, 'blocking' => false));
    
    // Bing (teraz IndexNow - ale ping nadal działa)
    $bing_ping = 'https://www.bing.com/ping?sitemap=' . urlencode($sitemap_url);
    wp_remote_get($bing_ping, array('timeout' => 5, 'blocking' => false));
    
    // Log
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[Salon Auto SEO] Pinged search engines with sitemap: ' . $sitemap_url);
    }
}
add_action('salon_auto_do_ping_search_engines', 'salon_auto_do_ping_search_engines');

/**
 * Ping przy zmianie statusu samochodu
 */
function salon_auto_ping_on_status_change($new_status, $old_status, $post) {
    if ($post->post_type !== 'car') {
        return;
    }
    
    // Ping gdy status się zmienia na/z 'publish'
    if ($new_status === 'publish' || $old_status === 'publish') {
        salon_auto_ping_search_engines($post->ID);
    }
}
add_action('transition_post_status', 'salon_auto_ping_on_status_change', 10, 3);

/**
 * =====================================================
 * FLUSH REWRITE RULES
 * =====================================================
 */

/**
 * Flush rewrite rules przy aktywacji motywu
 */
function salon_auto_seo_flush_rules() {
    salon_auto_seo_rewrite_rules();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'salon_auto_seo_flush_rules');

/**
 * Sprawdź i napraw rewrite rules
 */
function salon_auto_seo_check_rules() {
    $rules = get_option('rewrite_rules');
    
    if (!isset($rules['^sitemap\.xml$'])) {
        flush_rewrite_rules();
    }
}
add_action('admin_init', 'salon_auto_seo_check_rules');

/**
 * =====================================================
 * META TAGI SEO
 * =====================================================
 */

/**
 * Canonical URL
 */
function salon_auto_canonical_url() {
    if (is_singular('car')) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '" />' . "\n";
    } elseif (is_post_type_archive('car')) {
        echo '<link rel="canonical" href="' . esc_url(home_url('/samochody/')) . '" />' . "\n";
    } elseif (is_front_page()) {
        echo '<link rel="canonical" href="' . esc_url(home_url('/')) . '" />' . "\n";
    }
}
add_action('wp_head', 'salon_auto_canonical_url', 1);

/**
 * Open Graph meta tagi dla samochodów
 */
function salon_auto_og_meta() {
    if (!is_singular('car')) {
        return;
    }
    
    global $post;
    $post_id = $post->ID;
    
    $brand = get_post_meta($post_id, '_car_brand', true);
    $model = get_post_meta($post_id, '_car_model', true);
    $year = get_post_meta($post_id, '_car_year', true);
    $price = get_post_meta($post_id, '_car_price', true);
    $trim = get_post_meta($post_id, '_car_trim', true);
    
    $title = $brand . ' ' . $model . ($trim ? ' ' . $trim : '') . ($year ? ' ' . $year : '');
    $description = 'Kup ' . $brand . ' ' . $model . ' w PiękneAuta.pl. ';
    if ($price) {
        $description .= 'Cena: ' . number_format($price, 0, '', ' ') . ' zł. ';
    }
    $description .= '28 lat doświadczenia, gwarancja jakości.';
    
    // Zdjęcie
    $image_url = '';
    $gallery = get_post_meta($post_id, '_car_gallery', true);
    if (!empty($gallery) && is_array($gallery)) {
        $first_image_id = reset($gallery);
        $image_url = wp_get_attachment_image_url($first_image_id, 'large');
    }
    
    echo '<meta property="og:type" content="product" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . ' | PiękneAuta.pl" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '" />' . "\n";
    echo '<meta property="og:site_name" content="PiękneAuta.pl" />' . "\n";
    
    if ($image_url) {
        echo '<meta property="og:image" content="' . esc_url($image_url) . '" />' . "\n";
    }
    
    // Twitter Cards
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($description) . '" />' . "\n";
    
    if ($image_url) {
        echo '<meta name="twitter:image" content="' . esc_url($image_url) . '" />' . "\n";
    }
}
add_action('wp_head', 'salon_auto_og_meta', 5);

/**
 * =====================================================
 * ADMIN - STATUS SEO
 * =====================================================
 */

/**
 * Dodaj stronę statusu SEO w adminie
 */
function salon_auto_add_seo_status_page() {
    add_submenu_page(
        'salon-auto-homepage',
        'SEO & Sitemap',
        '🔍 SEO & Sitemap',
        'manage_options',
        'salon-auto-seo',
        'salon_auto_seo_status_page'
    );
}
add_action('admin_menu', 'salon_auto_add_seo_status_page');

/**
 * Strona statusu SEO
 */
function salon_auto_seo_status_page() {
    $site_url = home_url('/');
    $sitemap_url = $site_url . 'sitemap.xml';
    $robots_url = $site_url . 'robots.txt';
    
    // Sprawdź statusy
    $sitemap_response = wp_remote_head($sitemap_url, array('timeout' => 5));
    $sitemap_status = (!is_wp_error($sitemap_response) && wp_remote_retrieve_response_code($sitemap_response) === 200);
    
    // Policz samochody
    $cars_count = wp_count_posts('car');
    $published_cars = isset($cars_count->publish) ? $cars_count->publish : 0;
    
    // Policz strony
    $pages_count = wp_count_posts('page');
    $published_pages = isset($pages_count->publish) ? $pages_count->publish : 0;
    
    ?>
    <div class="wrap salon-auto-options">
        <h1 style="font-size: 24px; margin-bottom: 20px;">🔍 SEO & Sitemap</h1>
        
        <div class="salon-auto-seo-grid" style="margin-top: 20px;">
            
            <!-- Status Sitemap -->
            <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0; font-size: 18px;">📄 Sitemap XML</h2>
                
                <p><strong>Status:</strong> 
                    <?php if ($sitemap_status): ?>
                        <span style="color: #46b450;">✅ Aktywny</span>
                    <?php else: ?>
                        <span style="color: #dc3232;">❌ Błąd</span>
                    <?php endif; ?>
                </p>
                
                <p><strong>URL główny:</strong><br>
                    <a href="<?php echo esc_url($sitemap_url); ?>" target="_blank"><?php echo esc_html($sitemap_url); ?></a>
                </p>
                
                <p><strong>Sitemap samochodów:</strong><br>
                    <a href="<?php echo esc_url($site_url . 'sitemap-cars.xml'); ?>" target="_blank"><?php echo esc_html($site_url . 'sitemap-cars.xml'); ?></a>
                </p>
                
                <p><strong>Sitemap stron:</strong><br>
                    <a href="<?php echo esc_url($site_url . 'sitemap-pages.xml'); ?>" target="_blank"><?php echo esc_html($site_url . 'sitemap-pages.xml'); ?></a>
                </p>
                
                <p style="margin-top: 15px;">
                    <strong>Zawartość:</strong><br>
                    • <?php echo $published_cars; ?> samochodów<br>
                    • <?php echo $published_pages; ?> stron
                </p>
            </div>
            
            <!-- Robots.txt -->
            <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;">🤖 Robots.txt</h2>
                
                <p><strong>Status:</strong> 
                    <span style="color: #46b450;">✅ Wirtualny (generowany dynamicznie)</span>
                </p>
                
                <p><strong>URL:</strong><br>
                    <a href="<?php echo esc_url($robots_url); ?>" target="_blank"><?php echo esc_html($robots_url); ?></a>
                </p>
                
                <p style="margin-top: 15px;">
                    <strong>Indeksowanie:</strong><br>
                    <?php if (get_option('blog_public')): ?>
                        <span style="color: #46b450;">✅ Strona widoczna dla wyszukiwarek</span>
                    <?php else: ?>
                        <span style="color: #dc3232;">⚠️ Strona ukryta przed wyszukiwarkami</span><br>
                        <small>Zmień w Ustawienia → Czytanie</small>
                    <?php endif; ?>
                </p>
            </div>
            
            <!-- Ping wyszukiwarek -->
            <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;">🔔 Powiadomienia Google/Bing</h2>
                
                <p><strong>Status:</strong> 
                    <span style="color: #46b450;">✅ Automatyczne</span>
                </p>
                
                <p style="margin-top: 10px;">
                    Wyszukiwarki są automatycznie powiadamiane gdy:
                </p>
                <ul style="margin-left: 20px;">
                    <li>✓ Dodasz nowy samochód</li>
                    <li>✓ Edytujesz samochód</li>
                    <li>✓ Zmienisz status (dostępny/zarezerwowany/sprzedany)</li>
                    <li>✓ Usuniesz samochód</li>
                </ul>
                
                <form method="post" style="margin-top: 15px;">
                    <?php wp_nonce_field('salon_auto_manual_ping'); ?>
                    <button type="submit" name="salon_auto_manual_ping" class="button button-secondary">
                        🔄 Wyślij ping ręcznie
                    </button>
                </form>
                
                <?php
                if (isset($_POST['salon_auto_manual_ping']) && check_admin_referer('salon_auto_manual_ping')) {
                    salon_auto_do_ping_search_engines();
                    echo '<p style="color: #46b450; margin-top: 10px;">✅ Ping wysłany do Google i Bing!</p>';
                }
                ?>
            </div>
            
            <!-- Zgłoś do Google Search Console -->
            <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;">🌐 Google Search Console</h2>
                
                <p>Aby Google szybciej indeksował Twoje samochody:</p>
                
                <ol style="margin-left: 20px;">
                    <li>Zaloguj się do <a href="https://search.google.com/search-console" target="_blank">Google Search Console</a></li>
                    <li>Dodaj swoją stronę (jeśli jeszcze nie dodana)</li>
                    <li>Przejdź do "Mapy witryn"</li>
                    <li>Dodaj: <code><?php echo esc_html($sitemap_url); ?></code></li>
                </ol>
                
                <p style="margin-top: 15px;">
                    <a href="https://search.google.com/search-console/sitemaps?resource_id=<?php echo urlencode($site_url); ?>" target="_blank" class="button button-primary">
                        Otwórz Google Search Console →
                    </a>
                </p>
            </div>
            
        </div>
        
        <!-- Ostatnie zmiany -->
        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-top: 20px;">
            <h2 style="margin-top: 0;">📊 Ostatnie zmiany w samochodach</h2>
            
            <?php
            $recent_cars = get_posts(array(
                'post_type' => 'car',
                'post_status' => array('publish', 'draft', 'trash'),
                'posts_per_page' => 10,
                'orderby' => 'modified',
                'order' => 'DESC'
            ));
            
            if ($recent_cars):
            ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Samochód</th>
                        <th>Status</th>
                        <th>Ostatnia zmiana</th>
                        <th>W sitemap</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_cars as $car): 
                        $car_status = get_post_meta($car->ID, '_car_status', true) ?: 'available';
                        $brand = get_post_meta($car->ID, '_car_brand', true);
                        $model = get_post_meta($car->ID, '_car_model', true);
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($brand . ' ' . $model); ?></strong>
                            <?php if ($car->post_status === 'publish'): ?>
                                <br><a href="<?php echo get_permalink($car->ID); ?>" target="_blank">Zobacz →</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $status_labels = array(
                                'available' => '<span style="color: #46b450;">Dostępny</span>',
                                'reserved' => '<span style="color: #ffb900;">Zarezerwowany</span>',
                                'sold' => '<span style="color: #dc3232;">Sprzedany</span>'
                            );
                            echo isset($status_labels[$car_status]) ? $status_labels[$car_status] : $car_status;
                            ?>
                            <br>
                            <small>Post: <?php echo $car->post_status; ?></small>
                        </td>
                        <td><?php echo get_the_modified_date('d.m.Y H:i', $car->ID); ?></td>
                        <td>
                            <?php if ($car->post_status === 'publish'): ?>
                                <span style="color: #46b450;">✅ Tak</span>
                            <?php else: ?>
                                <span style="color: #999;">❌ Nie</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>Brak samochodów.</p>
            <?php endif; ?>
        </div>
        
    </div>
    <?php
}

