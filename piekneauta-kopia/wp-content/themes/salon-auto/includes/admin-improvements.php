<?php
/**
 * Admin Panel Improvements
 * Ulepszenia panelu administracyjnego dla wygody administratora
 */

if (!defined('ABSPATH')) {
    exit;
}

// ===================================
// 📊 CUSTOM COLUMNS IN CAR LIST
// ===================================

add_filter('manage_car_posts_columns', 'salon_auto_car_columns');
function salon_auto_car_columns($columns) {
    // Reorganizuj kolumny
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['car_thumbnail'] = 'Zdjęcie';
    $new_columns['title'] = 'Tytuł';
    $new_columns['car_brand'] = 'Marka';
    $new_columns['car_model'] = 'Model';
    $new_columns['car_year'] = 'Rok';
    $new_columns['car_price'] = 'Cena';
    $new_columns['car_status'] = 'Status';
    $new_columns['car_featured'] = 'Wyróżnione';
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}

add_action('manage_car_posts_custom_column', 'salon_auto_car_column_content', 10, 2);
function salon_auto_car_column_content($column, $post_id) {
    switch ($column) {
        case 'car_thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, 'thumbnail', array('style' => 'width:60px;height:45px;object-fit:cover;border-radius:4px;'));
            } else {
                echo '<span style="display:inline-block;width:60px;height:45px;background:#f0f0f0;border-radius:4px;text-align:center;line-height:45px;color:#999;font-size:10px;">Brak</span>';
            }
            break;
            
        case 'car_brand':
            $brand = get_post_meta($post_id, 'brand', true);
            echo $brand ? esc_html($brand) : '<span style="color:#999;">—</span>';
            break;
            
        case 'car_model':
            $model = get_post_meta($post_id, 'model', true);
            echo $model ? esc_html($model) : '<span style="color:#999;">—</span>';
            break;
            
        case 'car_year':
            $year = get_post_meta($post_id, 'year', true);
            echo $year ? esc_html($year) : '<span style="color:#999;">—</span>';
            break;
            
        case 'car_price':
            $price = get_post_meta($post_id, 'price', true);
            if ($price) {
                echo '<strong style="color:#28a745;">' . number_format($price, 0, ',', ' ') . ' PLN</strong>';
            } else {
                echo '<span style="color:#999;">—</span>';
            }
            break;
            
        case 'car_status':
            $status = get_post_meta($post_id, 'status', true) ?: 'available';
            $status_labels = array(
                'available' => array('label' => 'Dostępny', 'color' => '#28a745'),
                'reserved' => array('label' => 'Zarezerwowany', 'color' => '#ffc107'),
                'sold' => array('label' => 'Sprzedany', 'color' => '#dc3545'),
            );
            $status_info = isset($status_labels[$status]) ? $status_labels[$status] : array('label' => $status, 'color' => '#6c757d');
            echo '<span style="display:inline-block;padding:4px 8px;background:' . $status_info['color'] . ';color:white;border-radius:3px;font-size:11px;font-weight:bold;">' . esc_html($status_info['label']) . '</span>';
            break;
            
        case 'car_featured':
            $featured = get_post_meta($post_id, 'is_featured', true);
            if ($featured) {
                echo '<span style="color:#ffc107;font-size:18px;" title="Wyróżnione">⭐</span>';
            } else {
                echo '<span style="color:#ddd;">—</span>';
            }
            break;
    }
}

// Sortable columns
add_filter('manage_edit-car_sortable_columns', 'salon_auto_car_sortable_columns');
function salon_auto_car_sortable_columns($columns) {
    $columns['car_brand'] = 'brand';
    $columns['car_year'] = 'year';
    $columns['car_price'] = 'price';
    $columns['car_status'] = 'status';
    return $columns;
}

// Handle sorting
add_action('pre_get_posts', 'salon_auto_car_column_orderby');
function salon_auto_car_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    $orderby = $query->get('orderby');
    if ($orderby === 'brand' || $orderby === 'year' || $orderby === 'price' || $orderby === 'status') {
        $query->set('meta_key', $orderby);
        $query->set('orderby', 'meta_value');
    }
}

// ===================================
// 🔍 FILTERS IN CAR LIST
// ===================================

add_action('restrict_manage_posts', 'salon_auto_car_filters');
function salon_auto_car_filters() {
    global $typenow;
    if ($typenow === 'car') {
        // Status filter
        $status = isset($_GET['car_status']) ? $_GET['car_status'] : '';
        echo '<select name="car_status">';
        echo '<option value="">Wszystkie statusy</option>';
        echo '<option value="available"' . selected($status, 'available', false) . '>Dostępny</option>';
        echo '<option value="reserved"' . selected($status, 'reserved', false) . '>Zarezerwowany</option>';
        echo '<option value="sold"' . selected($status, 'sold', false) . '>Sprzedany</option>';
        echo '</select>';
        
        // Brand filter
        $brands = get_posts(array(
            'post_type' => 'car',
            'posts_per_page' => -1,
            'meta_key' => 'brand',
            'orderby' => 'meta_value',
            'order' => 'ASC',
        ));
        $unique_brands = array();
        foreach ($brands as $post) {
            $brand = get_post_meta($post->ID, 'brand', true);
            if ($brand && !in_array($brand, $unique_brands)) {
                $unique_brands[] = $brand;
            }
        }
        sort($unique_brands);
        
        $selected_brand = isset($_GET['car_brand']) ? $_GET['car_brand'] : '';
        echo '<select name="car_brand">';
        echo '<option value="">Wszystkie marki</option>';
        foreach ($unique_brands as $brand) {
            echo '<option value="' . esc_attr($brand) . '"' . selected($selected_brand, $brand, false) . '>' . esc_html($brand) . '</option>';
        }
        echo '</select>';
        
        // Featured filter
        $featured = isset($_GET['car_featured']) ? $_GET['car_featured'] : '';
        echo '<select name="car_featured">';
        echo '<option value="">Wszystkie</option>';
        echo '<option value="1"' . selected($featured, '1', false) . '>Wyróżnione</option>';
        echo '<option value="0"' . selected($featured, '0', false) . '>Niewyróżnione</option>';
        echo '</select>';
    }
}

// Apply filters
add_filter('parse_query', 'salon_auto_car_filter_query');
function salon_auto_car_filter_query($query) {
    global $pagenow, $typenow;
    
    if ($pagenow === 'edit.php' && $typenow === 'car' && $query->is_main_query()) {
        // Status filter
        if (isset($_GET['car_status']) && $_GET['car_status'] !== '') {
            $query->query_vars['meta_key'] = 'status';
            $query->query_vars['meta_value'] = sanitize_text_field($_GET['car_status']);
        }
        
        // Brand filter
        if (isset($_GET['car_brand']) && $_GET['car_brand'] !== '') {
            $query->query_vars['meta_key'] = 'brand';
            $query->query_vars['meta_value'] = sanitize_text_field($_GET['car_brand']);
        }
        
        // Featured filter
        if (isset($_GET['car_featured']) && $_GET['car_featured'] !== '') {
            $query->query_vars['meta_key'] = 'is_featured';
            $query->query_vars['meta_value'] = sanitize_text_field($_GET['car_featured']);
        }
    }
}

// ===================================
// ⚡ BULK ACTIONS
// ===================================

add_filter('bulk_actions-edit-car', 'salon_auto_car_bulk_actions');
function salon_auto_car_bulk_actions($actions) {
    $actions['mark_available'] = 'Oznacz jako dostępny';
    $actions['mark_reserved'] = 'Oznacz jako zarezerwowany';
    $actions['mark_sold'] = 'Oznacz jako sprzedany';
    $actions['mark_featured'] = 'Wyróżnij';
    $actions['unmark_featured'] = 'Usuń wyróżnienie';
    return $actions;
}

add_filter('handle_bulk_actions-edit-car', 'salon_auto_car_bulk_action_handler', 10, 3);
function salon_auto_car_bulk_action_handler($redirect_url, $action, $post_ids) {
    if ($action === 'mark_available') {
        foreach ($post_ids as $post_id) {
            update_post_meta($post_id, 'status', 'available');
        }
        $redirect_url = add_query_arg('bulk_updated', count($post_ids), $redirect_url);
    } elseif ($action === 'mark_reserved') {
        foreach ($post_ids as $post_id) {
            update_post_meta($post_id, 'status', 'reserved');
        }
        $redirect_url = add_query_arg('bulk_updated', count($post_ids), $redirect_url);
    } elseif ($action === 'mark_sold') {
        foreach ($post_ids as $post_id) {
            update_post_meta($post_id, 'status', 'sold');
        }
        $redirect_url = add_query_arg('bulk_updated', count($post_ids), $redirect_url);
    } elseif ($action === 'mark_featured') {
        foreach ($post_ids as $post_id) {
            update_post_meta($post_id, 'is_featured', '1');
        }
        $redirect_url = add_query_arg('bulk_updated', count($post_ids), $redirect_url);
    } elseif ($action === 'unmark_featured') {
        foreach ($post_ids as $post_id) {
            update_post_meta($post_id, 'is_featured', '0');
        }
        $redirect_url = add_query_arg('bulk_updated', count($post_ids), $redirect_url);
    }
    
    return $redirect_url;
}

// Show admin notice after bulk action
add_action('admin_notices', 'salon_auto_car_bulk_action_notice');
function salon_auto_car_bulk_action_notice() {
    if (!empty($_REQUEST['bulk_updated'])) {
        $count = intval($_REQUEST['bulk_updated']);
        echo '<div class="notice notice-success is-dismissible"><p>';
        echo sprintf('Zaktualizowano %d samochodów.', $count);
        echo '</p></div>';
    }
}

// ===================================
// ✏️ QUICK EDIT
// ===================================

add_action('quick_edit_custom_box', 'salon_auto_car_quick_edit', 10, 2);
function salon_auto_car_quick_edit($column_name, $post_type) {
    if ($post_type !== 'car') return;
    
    static $printNonce = true;
    if ($printNonce) {
        $printNonce = false;
        wp_nonce_field('salon_auto_quick_edit', 'salon_auto_quick_edit_nonce');
    }
    
    ?>
    <fieldset class="inline-edit-col-right">
        <div class="inline-edit-col">
            <div class="inline-edit-group">
                <label>
                    <span class="title">Status</span>
                    <select name="car_status">
                        <option value="available">Dostępny</option>
                        <option value="reserved">Zarezerwowany</option>
                        <option value="sold">Sprzedany</option>
                    </select>
                </label>
            </div>
            <div class="inline-edit-group">
                <label>
                    <span class="title">Wyróżnione</span>
                    <input type="checkbox" name="car_featured" value="1">
                </label>
            </div>
            <div class="inline-edit-group">
                <label>
                    <span class="title">Cena (PLN)</span>
                    <input type="number" name="car_price" value="">
                </label>
            </div>
        </div>
    </fieldset>
    <?php
}

add_action('save_post', 'salon_auto_car_quick_edit_save');
function salon_auto_car_quick_edit_save($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'car') return;
    
    if (isset($_POST['salon_auto_quick_edit_nonce']) && wp_verify_nonce($_POST['salon_auto_quick_edit_nonce'], 'salon_auto_quick_edit')) {
        if (isset($_POST['car_status'])) {
            update_post_meta($post_id, 'status', sanitize_text_field($_POST['car_status']));
        }
        if (isset($_POST['car_featured'])) {
            update_post_meta($post_id, 'is_featured', '1');
        } else {
            update_post_meta($post_id, 'is_featured', '0');
        }
        if (isset($_POST['car_price']) && $_POST['car_price'] !== '') {
            update_post_meta($post_id, 'price', intval($_POST['car_price']));
        }
    }
}

// Populate quick edit fields
add_action('admin_footer', 'salon_auto_car_quick_edit_populate');
function salon_auto_car_quick_edit_populate() {
    global $post_type;
    if ($post_type !== 'car') return;
    ?>
    <script type="text/javascript">
    jQuery(function($) {
        var $wp_inline_edit = inlineEditPost.edit;
        inlineEditPost.edit = function(id) {
            $wp_inline_edit.call(this, id);
            var $post_id = 0;
            if (typeof(id) == 'object') {
                $post_id = parseInt(this.getId(id));
            } else {
                $post_id = id;
            }
            
            if ($post_id > 0) {
                var $edit_row = $('#edit-' + $post_id);
                var $post_row = $('#post-' + $post_id);
                
                // Get data from row
                var status = $post_row.find('.column-car_status span').text().trim();
                var status_map = {'Dostępny': 'available', 'Zarezerwowany': 'reserved', 'Sprzedany': 'sold'};
                var status_value = status_map[status] || 'available';
                
                var featured = $post_row.find('.column-car_featured span').text().trim();
                var featured_value = featured === '⭐' ? '1' : '0';
                
                var price = $post_row.find('.column-car_price strong').text().replace(/[^0-9]/g, '');
                
                // Set values
                $edit_row.find('select[name="car_status"]').val(status_value);
                $edit_row.find('input[name="car_featured"]').prop('checked', featured_value === '1');
                $edit_row.find('input[name="car_price"]').val(price);
            }
        };
    });
    </script>
    <?php
}

// ===================================
// 📊 DASHBOARD WIDGET
// ===================================

add_action('wp_dashboard_setup', 'salon_auto_dashboard_widget');
function salon_auto_dashboard_widget() {
    wp_add_dashboard_widget(
        'salon_auto_stats',
        '📊 Statystyki Samochodów',
        'salon_auto_dashboard_widget_content'
    );
}

function salon_auto_dashboard_widget_content() {
    $available = get_posts(array(
        'post_type' => 'car',
        'posts_per_page' => -1,
        'meta_query' => array(
            array('key' => 'status', 'value' => 'available', 'compare' => '=')
        ),
        'fields' => 'ids'
    ));
    
    $reserved = get_posts(array(
        'post_type' => 'car',
        'posts_per_page' => -1,
        'meta_query' => array(
            array('key' => 'status', 'value' => 'reserved', 'compare' => '=')
        ),
        'fields' => 'ids'
    ));
    
    $sold = get_posts(array(
        'post_type' => 'car',
        'posts_per_page' => -1,
        'meta_query' => array(
            array('key' => 'status', 'value' => 'sold', 'compare' => '=')
        ),
        'fields' => 'ids'
    ));
    
    $featured = get_posts(array(
        'post_type' => 'car',
        'posts_per_page' => -1,
        'meta_query' => array(
            array('key' => 'is_featured', 'value' => '1', 'compare' => '=')
        ),
        'fields' => 'ids'
    ));
    
    $total = count($available) + count($reserved) + count($sold);
    
    ?>
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:15px;margin-bottom:15px;">
        <div style="padding:15px;background:#28a745;color:white;border-radius:5px;text-align:center;">
            <div style="font-size:32px;font-weight:bold;"><?php echo count($available); ?></div>
            <div style="font-size:12px;opacity:0.9;">Dostępne</div>
        </div>
        <div style="padding:15px;background:#ffc107;color:white;border-radius:5px;text-align:center;">
            <div style="font-size:32px;font-weight:bold;"><?php echo count($reserved); ?></div>
            <div style="font-size:12px;opacity:0.9;">Zarezerwowane</div>
        </div>
        <div style="padding:15px;background:#dc3545;color:white;border-radius:5px;text-align:center;">
            <div style="font-size:32px;font-weight:bold;"><?php echo count($sold); ?></div>
            <div style="font-size:12px;opacity:0.9;">Sprzedane</div>
        </div>
        <div style="padding:15px;background:#17a2b8;color:white;border-radius:5px;text-align:center;">
            <div style="font-size:32px;font-weight:bold;"><?php echo count($featured); ?></div>
            <div style="font-size:12px;opacity:0.9;">Wyróżnione</div>
        </div>
    </div>
    <div style="padding:10px;background:#f8f9fa;border-radius:5px;text-align:center;">
        <strong>Łącznie: <?php echo $total; ?> samochodów</strong>
    </div>
    <div style="margin-top:15px;padding-top:15px;border-top:1px solid #ddd;">
        <a href="<?php echo admin_url('edit.php?post_type=car'); ?>" class="button button-primary" style="width:100%;text-align:center;">Zarządzaj samochodami</a>
    </div>
    <?php
}

// ===================================
// 📋 DUPLICATE CAR
// ===================================

add_filter('post_row_actions', 'salon_auto_car_duplicate_link', 10, 2);
function salon_auto_car_duplicate_link($actions, $post) {
    if ($post->post_type === 'car' && current_user_can('edit_posts')) {
        $actions['duplicate'] = '<a href="' . wp_nonce_url(admin_url('admin.php?action=duplicate_car&post=' . $post->ID), 'duplicate_car_' . $post->ID) . '" title="Duplikuj ten samochód">Duplikuj</a>';
    }
    return $actions;
}

add_action('admin_action_duplicate_car', 'salon_auto_duplicate_car');
function salon_auto_duplicate_car() {
    if (!isset($_GET['post']) || !isset($_GET['_wpnonce'])) {
        wp_die('Brak danych');
    }
    
    $post_id = intval($_GET['post']);
    check_admin_referer('duplicate_car_' . $post_id, '_wpnonce');
    
    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'car') {
        wp_die('Nieprawidłowy post');
    }
    
    $new_post = array(
        'post_title' => $post->post_title . ' (kopia)',
        'post_content' => $post->post_content,
        'post_status' => 'draft',
        'post_type' => 'car',
        'post_author' => get_current_user_id(),
    );
    
    $new_post_id = wp_insert_post($new_post);
    
    // Copy all meta fields
    $meta_keys = get_post_custom_keys($post_id);
    if ($meta_keys) {
        foreach ($meta_keys as $meta_key) {
            if (strpos($meta_key, '_') !== 0) { // Skip private fields
                $meta_values = get_post_custom_values($meta_key, $post_id);
                foreach ($meta_values as $meta_value) {
                    add_post_meta($new_post_id, $meta_key, $meta_value);
                }
            }
        }
    }
    
    // Copy featured image
    $thumbnail_id = get_post_thumbnail_id($post_id);
    if ($thumbnail_id) {
        set_post_thumbnail($new_post_id, $thumbnail_id);
    }
    
    wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
    exit;
}

// ===================================
// ✅ FIELD VALIDATION
// ===================================

add_action('admin_enqueue_scripts', 'salon_auto_admin_validation_scripts');
function salon_auto_admin_validation_scripts($hook) {
    global $post;
    if (($hook === 'post.php' || $hook === 'post-new.php') && isset($post) && $post->post_type === 'car') {
        wp_enqueue_script('jquery');
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#publish, #save-post').on('click', function(e) {
                var errors = [];
                
                // Check required fields
                if (!$('#brand').val().trim()) {
                    errors.push('Marka jest wymagana');
                }
                if (!$('#model').val().trim()) {
                    errors.push('Model jest wymagany');
                }
                if (!$('#price').val().trim()) {
                    errors.push('Cena jest wymagana');
                } else if (isNaN($('#price').val()) || parseInt($('#price').val()) <= 0) {
                    errors.push('Cena musi być liczbą większą od 0');
                }
                if (!$('#year').val().trim()) {
                    errors.push('Rok produkcji jest wymagany');
                } else if (isNaN($('#year').val()) || parseInt($('#year').val()) < 1900 || parseInt($('#year').val()) > new Date().getFullYear() + 1) {
                    errors.push('Rok produkcji jest nieprawidłowy');
                }
                
                if (errors.length > 0) {
                    e.preventDefault();
                    alert('Błędy w formularzu:\n\n' + errors.join('\n'));
                    return false;
                }
            });
        });
        </script>
        <?php
    }
}

// ===================================
// 🎨 ADMIN STYLES - RESPONSYWNE DLA IPAD
// ===================================

add_action('admin_head', 'salon_auto_admin_styles');
function salon_auto_admin_styles() {
    ?>
    <style>
    /* ===================================
       DESKTOP STYLES
       =================================== */
    
    /* WAŻNE: Kolumna tytułu musi mieć normalną szerokość */
    .wp-list-table .column-title {
        width: auto !important;
        min-width: 200px !important;
    }
    .wp-list-table .column-title .row-title,
    .wp-list-table .column-title .row-actions {
        white-space: normal !important;
        word-wrap: break-word !important;
        display: block !important;
    }
    
    /* Custom columns styling */
    .column-car_thumbnail {
        width: 80px;
    }
    .column-car_brand,
    .column-car_model,
    .column-car_year {
        width: 100px;
    }
    .column-car_price {
        width: 120px;
    }
    .column-car_status {
        width: 100px;
    }
    .column-car_featured {
        width: 70px;
        text-align: center;
    }
    
    /* Quick edit styling */
    .inline-edit-col-right {
        width: 30%;
    }
    
    /* Dashboard widget */
    #salon_auto_stats .inside {
        padding: 15px;
    }
    
    /* Admin menu highlight */
    #adminmenu .toplevel_page_salon-auto-homepage .wp-menu-name {
        font-weight: bold;
    }
    
    /* Quick status button */
    .salon-auto-quick-status {
        display: inline-flex;
        gap: 5px;
        margin-left: 10px;
    }
    .salon-auto-quick-status a {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 3px;
        text-decoration: none;
        font-size: 11px;
        font-weight: 600;
    }
    .salon-auto-quick-status .status-available { background: #28a745; color: white; }
    .salon-auto-quick-status .status-reserved { background: #ffc107; color: white; }
    .salon-auto-quick-status .status-sold { background: #dc3545; color: white; }
    .salon-auto-quick-status a:hover { opacity: 0.8; }
    
    /* ===================================
       MOBILE / TABLET - MINIMALNE POPRAWKI
       WordPress ma własne responsywne style, nie nadpisujemy zbyt agresywnie
       =================================== */
    @media screen and (max-width: 782px) {
        /* Pola formularza - zapobieganie zoomowi na iOS */
        input[type="text"],
        input[type="email"],
        input[type="url"],
        input[type="password"],
        input[type="number"],
        input[type="tel"],
        textarea,
        select {
            font-size: 16px !important;
        }
        
        /* Ukryj mniej ważne kolumny */
        .column-car_year,
        .column-car_featured,
        .column-car_brand,
        .column-car_model {
            display: none !important;
        }
        
        /* Quick status - NIE zmieniamy układu, tylko rozmiary */
        .salon-auto-quick-status a {
            padding: 6px 10px !important;
            font-size: 11px !important;
            white-space: nowrap !important;
        }
    }
    
    /* ===================================
       TOUCH DEVICE OPTIMIZATIONS
       =================================== */
    @media (pointer: coarse) {
        /* Lepsze scrollowanie */
        .wp-list-table {
            -webkit-overflow-scrolling: touch;
        }
    }
    
    /* ===================================
       MNIEJSZE DESKTOPY (do 1400px)
       =================================== */
    @media screen and (min-width: 783px) and (max-width: 1400px) {
        /* Ukryj mniej ważne kolumny aby dać więcej miejsca dla tytułu */
        .column-car_featured,
        .column-car_year {
            display: none !important;
        }
        
        .column-car_brand,
        .column-car_model {
            width: 80px !important;
        }
        
        .column-car_price {
            width: 100px !important;
        }
    }
    
    /* ===================================
       IPAD PRO (1024px+) LANDSCAPE
       =================================== */
    @media screen and (min-width: 1025px) and (max-width: 1366px) {
        /* Optymalne dla iPad Pro w landscape */
        .column-car_thumbnail {
            width: 70px !important;
        }
        
        /* Wszystkie kolumny widoczne ale węższe */
        .column-car_brand,
        .column-car_model {
            width: 70px !important;
        }
        
        /* Formularze - optymalna szerokość */
        #post-body-content textarea {
            min-height: 200px;
        }
    }
    
    /* ===================================
       CUSTOM STYLES FOR CAR EDIT PAGE
       =================================== */
    .post-type-car #postbox-container-2 .postbox {
        margin-bottom: 20px;
    }
    
    /* Tabs w meta boxie samochodu */
    .car-meta-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-bottom: 15px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
    }
    .car-meta-tabs .tab-button {
        padding: 12px 20px;
        background: #f0f0f1;
        border: none;
        border-radius: 4px 4px 0 0;
        cursor: pointer;
        font-size: 14px;
        min-height: 44px;
    }
    .car-meta-tabs .tab-button.active {
        background: #2271b1;
        color: white;
    }
    
    @media screen and (max-width: 782px) {
        .car-meta-tabs {
            flex-direction: column;
        }
        .car-meta-tabs .tab-button {
            width: 100%;
            text-align: left;
            border-radius: 4px;
        }
    }
    
    /* ===================================
       GALLERY UPLOAD AREA
       =================================== */
    .car-gallery-upload-area {
        border: 2px dashed #c3c4c7;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        margin-bottom: 20px;
        transition: all 0.2s;
        min-height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }
    .car-gallery-upload-area:hover,
    .car-gallery-upload-area.dragover {
        border-color: #2271b1;
        background: #f0f7fc;
    }
    .car-gallery-upload-area .upload-button {
        min-height: 50px;
        padding: 15px 30px;
        font-size: 16px;
    }
    
    /* ===================================
       OPTIONS PAGES RESPONSIVE
       =================================== */
    .wrap.salon-auto-options {
        max-width: 100%;
        padding: 10px;
    }
    
    @media screen and (max-width: 782px) {
        .wrap.salon-auto-options .form-table th {
            padding: 15px 0 5px 0;
        }
        .wrap.salon-auto-options .form-table td {
            padding: 10px 0;
        }
        .wrap.salon-auto-options .form-table input[type="text"],
        .wrap.salon-auto-options .form-table textarea,
        .wrap.salon-auto-options .form-table select {
            width: 100% !important;
            max-width: 100% !important;
        }
    }
    
    /* ===================================
       SEO PAGE RESPONSIVE
       =================================== */
    .salon-auto-seo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }
    
    @media screen and (max-width: 782px) {
        .salon-auto-seo-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* Status indicator dots */
    .status-dot {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
    }
    .status-dot.available { background: #28a745; }
    .status-dot.reserved { background: #ffc107; }
    .status-dot.sold { background: #dc3545; }
    </style>
    <?php
}

// ===================================
// ⚡ SZYBKA ZMIANA STATUSU (AJAX)
// ===================================

add_action('wp_ajax_salon_auto_quick_status', 'salon_auto_ajax_quick_status');
function salon_auto_ajax_quick_status() {
    if (!current_user_can('edit_posts')) {
        wp_die('Brak uprawnień');
    }
    
    check_ajax_referer('salon_auto_quick_status_nonce', 'nonce');
    
    $post_id = intval($_POST['post_id']);
    $new_status = sanitize_text_field($_POST['status']);
    
    if (!in_array($new_status, array('available', 'reserved', 'sold'))) {
        wp_die('Nieprawidłowy status');
    }
    
    update_post_meta($post_id, 'status', $new_status);
    update_post_meta($post_id, '_car_status', $new_status);
    
    wp_send_json_success(array(
        'message' => 'Status zmieniony',
        'status' => $new_status
    ));
}

// Dodaj przyciski szybkiej zmiany statusu w akcjach wiersza
add_filter('post_row_actions', 'salon_auto_quick_status_buttons', 20, 2);
function salon_auto_quick_status_buttons($actions, $post) {
    if ($post->post_type !== 'car') {
        return $actions;
    }
    
    $current_status = get_post_meta($post->ID, 'status', true) ?: 'available';
    $nonce = wp_create_nonce('salon_auto_quick_status_nonce');
    
    $status_buttons = '<span class="salon-auto-quick-status">';
    
    if ($current_status !== 'available') {
        $status_buttons .= '<a href="#" class="status-available" data-status="available" data-post="' . $post->ID . '" data-nonce="' . $nonce . '">✓ Dostępny</a>';
    }
    if ($current_status !== 'reserved') {
        $status_buttons .= '<a href="#" class="status-reserved" data-status="reserved" data-post="' . $post->ID . '" data-nonce="' . $nonce . '">◎ Zarezerwuj</a>';
    }
    if ($current_status !== 'sold') {
        $status_buttons .= '<a href="#" class="status-sold" data-status="sold" data-post="' . $post->ID . '" data-nonce="' . $nonce . '">✕ Sprzedaj</a>';
    }
    
    $status_buttons .= '</span>';
    
    $actions['quick_status'] = $status_buttons;
    
    return $actions;
}

// JavaScript dla szybkiej zmiany statusu
add_action('admin_footer', 'salon_auto_quick_status_js');
function salon_auto_quick_status_js() {
    global $typenow;
    if ($typenow !== 'car') return;
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.salon-auto-quick-status a').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var postId = $btn.data('post');
            var newStatus = $btn.data('status');
            var nonce = $btn.data('nonce');
            
            $btn.css('opacity', '0.5');
            
            $.post(ajaxurl, {
                action: 'salon_auto_quick_status',
                post_id: postId,
                status: newStatus,
                nonce: nonce
            }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Błąd: ' + response.data);
                    $btn.css('opacity', '1');
                }
            });
        });
    });
    </script>
    <?php
}

// ===================================
// 📱 ADMIN BAR SHORTCUTS
// ===================================

add_action('admin_bar_menu', 'salon_auto_admin_bar_menu', 100);
function salon_auto_admin_bar_menu($admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Główne menu
    $admin_bar->add_node(array(
        'id' => 'salon-auto',
        'title' => '🚗 PiękneAuta',
        'href' => admin_url('edit.php?post_type=car'),
    ));
    
    // Dodaj samochód
    $admin_bar->add_node(array(
        'id' => 'salon-auto-add-car',
        'parent' => 'salon-auto',
        'title' => '➕ Dodaj samochód',
        'href' => admin_url('post-new.php?post_type=car'),
    ));
    
    // Wszystkie samochody
    $admin_bar->add_node(array(
        'id' => 'salon-auto-all-cars',
        'parent' => 'salon-auto',
        'title' => '📋 Wszystkie samochody',
        'href' => admin_url('edit.php?post_type=car'),
    ));
    
    // Strona główna
    $admin_bar->add_node(array(
        'id' => 'salon-auto-homepage',
        'parent' => 'salon-auto',
        'title' => '🏠 Ustawienia strony głównej',
        'href' => admin_url('admin.php?page=salon-auto-homepage'),
    ));
    
    // SEO & Sitemap
    $admin_bar->add_node(array(
        'id' => 'salon-auto-seo',
        'parent' => 'salon-auto',
        'title' => '🔍 SEO & Sitemap',
        'href' => admin_url('admin.php?page=salon-auto-seo'),
    ));
    
    // Separator
    $admin_bar->add_node(array(
        'id' => 'salon-auto-separator',
        'parent' => 'salon-auto',
        'title' => '<hr style="margin: 5px 0; border: 0; border-top: 1px solid #555;">',
        'href' => false,
    ));
    
    // Podgląd strony
    $admin_bar->add_node(array(
        'id' => 'salon-auto-view-site',
        'parent' => 'salon-auto',
        'title' => '👁️ Podgląd strony',
        'href' => home_url('/'),
        'meta' => array('target' => '_blank'),
    ));
}

// ===================================
// 📊 ROZSZERZONE STATYSTYKI W DASHBOARDZIE
// ===================================

add_action('wp_dashboard_setup', 'salon_auto_extended_dashboard_widget', 20);
function salon_auto_extended_dashboard_widget() {
    wp_add_dashboard_widget(
        'salon_auto_quick_actions',
        '⚡ Szybkie akcje',
        'salon_auto_quick_actions_widget'
    );
}

function salon_auto_quick_actions_widget() {
    ?>
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
        <a href="<?php echo admin_url('post-new.php?post_type=car'); ?>" 
           class="button button-primary" 
           style="text-align: center; padding: 15px; height: auto;">
            ➕ Dodaj samochód
        </a>
        <a href="<?php echo admin_url('edit.php?post_type=car'); ?>" 
           class="button" 
           style="text-align: center; padding: 15px; height: auto;">
            📋 Lista samochodów
        </a>
        <a href="<?php echo admin_url('admin.php?page=salon-auto-homepage'); ?>" 
           class="button" 
           style="text-align: center; padding: 15px; height: auto;">
            🏠 Strona główna
        </a>
        <a href="<?php echo admin_url('admin.php?page=salon-auto-seo'); ?>" 
           class="button" 
           style="text-align: center; padding: 15px; height: auto;">
            🔍 SEO & Sitemap
        </a>
    </div>
    
    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
        <h4 style="margin: 0 0 10px 0;">Ostatnio dodane samochody:</h4>
        <?php
        $recent = get_posts(array(
            'post_type' => 'car',
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        if ($recent):
        ?>
        <ul style="margin: 0; padding: 0; list-style: none;">
            <?php foreach ($recent as $car): 
                $brand = get_post_meta($car->ID, '_car_brand', true) ?: get_post_meta($car->ID, 'brand', true);
                $model = get_post_meta($car->ID, '_car_model', true) ?: get_post_meta($car->ID, 'model', true);
                $status = get_post_meta($car->ID, '_car_status', true) ?: get_post_meta($car->ID, 'status', true) ?: 'available';
                $status_colors = array('available' => '#28a745', 'reserved' => '#ffc107', 'sold' => '#dc3545');
            ?>
            <li style="padding: 5px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <a href="<?php echo get_edit_post_link($car->ID); ?>"><?php echo esc_html($brand . ' ' . $model); ?></a>
                <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: <?php echo $status_colors[$status] ?? '#999'; ?>;"></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p>Brak samochodów.</p>
        <?php endif; ?>
    </div>
    <?php
}

// ===================================
// 🔔 POWIADOMIENIA O STATUSIE
// ===================================

add_action('admin_notices', 'salon_auto_status_notices');
function salon_auto_status_notices() {
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'car') {
        return;
    }
    
    // Policz samochody bez zdjęć
    $cars_without_images = get_posts(array(
        'post_type' => 'car',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => '_car_gallery',
                'compare' => 'NOT EXISTS'
            ),
            array(
                'key' => '_car_gallery',
                'value' => '',
                'compare' => '='
            ),
            array(
                'key' => '_car_gallery',
                'value' => 'a:0:{}',
                'compare' => '='
            )
        ),
        'fields' => 'ids'
    ));
    
    if (!empty($cars_without_images)) {
        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p><strong>⚠️ Uwaga:</strong> ' . count($cars_without_images) . ' samochodów nie ma dodanych zdjęć w galerii. ';
        echo '<a href="' . admin_url('edit.php?post_type=car&cars_without_images=1') . '">Zobacz które →</a></p>';
        echo '</div>';
    }
}

