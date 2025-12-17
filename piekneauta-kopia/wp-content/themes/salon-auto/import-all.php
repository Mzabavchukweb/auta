<?php
/**
 * IMPORT DANYCH - SZYBKI (bez kopiowania zdjęć)
 * Zdjęcia są używane bezpośrednio z motywu
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', function() {
    add_submenu_page(
        'edit.php?post_type=car',
        'Import danych',
        'Import danych',
        'manage_options',
        'salon-import-all',
        'salon_auto_reset_import_page'
    );
}, 99);

function salon_auto_reset_import_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Brak uprawnien');
    }
    
    $json_path = get_template_directory() . '/data/cars.json';
    $images_dir = get_template_directory() . '/images/';
    
    echo '<div class="wrap">';
    echo '<h1>Import samochodów</h1>';
    
    // DEBUG INFO
    echo '<div style="background:#e8f4f8;padding:15px;margin:10px 0;border-radius:5px;">';
    echo '<strong>DEBUG:</strong><br>';
    echo 'JSON path: ' . esc_html($json_path) . ' - ' . (file_exists($json_path) ? '✅ EXISTS' : '❌ NOT FOUND') . '<br>';
    echo 'Images dir: ' . esc_html($images_dir) . ' - ' . (is_dir($images_dir) ? '✅ EXISTS' : '❌ NOT FOUND') . '<br>';
    
    // Count existing cars
    $existing = get_posts(['post_type' => 'car', 'posts_per_page' => -1, 'post_status' => 'any']);
    echo 'Existing cars in DB: ' . count($existing) . '<br>';
    echo '</div>';
    
    if (!file_exists($json_path)) {
        echo '<div class="notice notice-error"><p>BŁĄD: Brak pliku data/cars.json!</p></div></div>';
        return;
    }
    
    $cars_data = json_decode(file_get_contents($json_path), true);
    if (!$cars_data) {
        echo '<div class="notice notice-error"><p>BŁĄD: Nie można odczytać cars.json!</p></div></div>';
        return;
    }
    
    echo '<div style="background:#d4edda;padding:10px;margin:10px 0;border-radius:5px;">';
    echo 'Cars in JSON: ' . count($cars_data) . '<br>';
    echo '</div>';
    
    // Obsługa akcji
    if (isset($_POST['action_type']) && wp_verify_nonce($_POST['_wpnonce'], 'import_nonce')) {
        $action = $_POST['action_type'];
        
        echo '<div style="background:#1a1a2e;color:#0f0;padding:20px;margin:20px 0;font-family:monospace;font-size:13px;border-radius:8px;max-height:500px;overflow-y:auto;">';
        
        if ($action === 'delete_all') {
            $cars = get_posts(['post_type' => 'car', 'posts_per_page' => -1, 'post_status' => 'any']);
            foreach ($cars as $car) {
                wp_delete_post($car->ID, true);
                echo "❌ Usunięto: {$car->post_title} (ID: {$car->ID})<br>";
            }
            echo "<br><strong>✅ Usunięto " . count($cars) . " samochodów</strong>";
        }
        elseif ($action === 'import_all') {
            $count = 0;
            $errors = 0;
            foreach ($cars_data as $i => $car) {
                echo "<br>--- Importuję " . ($i+1) . "/" . count($cars_data) . ": {$car['brand']} {$car['model']} ---<br>";
                $result = salon_auto_quick_import_car($car, $images_dir);
                if ($result) {
                    $count++;
                    echo "✅ OK (ID: $result)<br>";
                } else {
                    $errors++;
                    echo "❌ BŁĄD<br>";
                }
            }
            echo "<br><strong>============<br>✅ Zaimportowano: $count<br>❌ Błędy: $errors</strong>";
        }
        elseif (strpos($action, 'import_') === 0) {
            $index = intval(str_replace('import_', '', $action));
            if (isset($cars_data[$index])) {
                $car = $cars_data[$index];
                echo "--- Importuję: {$car['brand']} {$car['model']} ---<br>";
                $result = salon_auto_quick_import_car($car, $images_dir);
                if ($result) {
                    echo "<br>✅ SUKCES (ID: $result)<br>";
                    
                    // Sprawdź zapisane dane
                    echo "<br>--- WERYFIKACJA ---<br>";
                    $gallery_files = get_post_meta($result, 'gallery_files', true);
                    echo "gallery_files: " . ($gallery_files ? substr($gallery_files, 0, 100) . '...' : 'PUSTE!') . "<br>";
                    $brand = get_post_meta($result, 'brand', true);
                    echo "brand: " . ($brand ?: 'PUSTE!') . "<br>";
                } else {
                    echo "❌ BŁĄD importu";
                }
            }
        }
        
        echo '</div>';
    }
    
    // Formularz
    echo '<form method="post">';
    wp_nonce_field('import_nonce');
    
    echo '<div style="margin:20px 0;display:flex;gap:10px;flex-wrap:wrap;">';
    echo '<button type="submit" name="action_type" value="delete_all" class="button" style="background:#dc3545;color:white;border:none;" onclick="return confirm(\'Usunąć wszystkie samochody?\')">🗑️ Usuń wszystkie</button>';
    echo '<button type="submit" name="action_type" value="import_all" class="button button-primary" style="background:#28a745;border-color:#28a745;">🚀 Importuj wszystkie naraz</button>';
    echo '</div>';
    
    // Tabela
    echo '<table class="widefat striped" style="margin-top:20px;">';
    echo '<thead><tr style="background:#23282d;color:white;"><th>#</th><th>Samochód</th><th>Slug</th><th>Zdjęcia</th><th>Status w DB</th><th>Akcja</th></tr></thead>';
    echo '<tbody>';
    
    foreach ($cars_data as $i => $car) {
        $title = $car['brand'] . ' ' . $car['model'];
        $slug = $car['slug'];
        $img_count = count($car['images']);
        
        // Sprawdź czy istnieje
        $existing_post = get_page_by_path($slug, OBJECT, 'car');
        if (!$existing_post) {
            $posts = get_posts(['post_type' => 'car', 'name' => $slug, 'posts_per_page' => 1, 'post_status' => 'any']);
            $existing_post = !empty($posts) ? $posts[0] : null;
        }
        
        if ($existing_post) {
            $status_html = '<span style="color:#28a745;font-weight:bold;">✅ ID: '.$existing_post->ID.'</span>';
            $gallery_files = get_post_meta($existing_post->ID, 'gallery_files', true);
            if ($gallery_files) {
                $files_count = count(explode(',', $gallery_files));
                $status_html .= '<br><small>📷 ' . $files_count . ' zdjęć</small>';
            } else {
                $status_html .= '<br><small style="color:red;">⚠️ brak gallery_files!</small>';
            }
        } else {
            $status_html = '<span style="color:#dc3545;">❌ Brak w DB</span>';
        }
        
        // Sprawdź czy pierwsze zdjęcie istnieje
        $first_img = $images_dir . $car['images'][0];
        $img_status = file_exists($first_img) ? '✅' : '❌';
        
        echo '<tr>';
        echo '<td><strong>' . ($i + 1) . '</strong></td>';
        echo '<td><strong>' . esc_html($title) . '</strong></td>';
        echo '<td><code style="font-size:11px;">' . esc_html($slug) . '</code></td>';
        echo '<td>' . $img_status . ' ' . $img_count . '</td>';
        echo '<td>' . $status_html . '</td>';
        echo '<td><button type="submit" name="action_type" value="import_' . $i . '" class="button button-primary button-small">Import</button></td>';
        echo '</tr>';
    }
    
    echo '</tbody></table>';
    echo '</form>';
    echo '</div>';
}

/**
 * Szybki import - bez kopiowania zdjęć
 */
function salon_auto_quick_import_car($car, $images_dir) {
    $slug = $car['slug'];
    $title = $car['brand'] . ' ' . $car['model'];
    
    echo "Slug: $slug<br>";
    echo "Title: $title<br>";
    
    // Sprawdź czy istnieje
    $existing = get_page_by_path($slug, OBJECT, 'car');
    if (!$existing) {
        $posts = get_posts(['post_type' => 'car', 'name' => $slug, 'posts_per_page' => 1, 'post_status' => 'any']);
        $existing = !empty($posts) ? $posts[0] : null;
    }
    
    if ($existing) {
        $post_id = $existing->ID;
        wp_update_post([
            'ID' => $post_id,
            'post_title' => $title,
            'post_status' => 'publish',
        ]);
        echo "Aktualizacja (ID: $post_id)<br>";
    } else {
        $post_id = wp_insert_post([
            'post_title' => $title,
            'post_name' => $slug,
            'post_type' => 'car',
            'post_status' => 'publish',
        ]);
        
        if (is_wp_error($post_id)) {
            echo "BŁĄD wp_insert_post: " . $post_id->get_error_message() . "<br>";
            return false;
        }
        echo "Utworzono nowy (ID: $post_id)<br>";
    }
    
    // Zapisz metadane
    $transmission = $car['transmission'] ?? '';
    $meta = [
        'brand' => $car['brand'],
        'model' => $car['model'],
        'trim' => $car['trim'] ?? '',
        'year' => $car['year'],
        'mileage' => $car['mileage_km'],
        'fuel' => $car['fuel'] ?? '',
        'transmission' => $transmission,
        'gearbox' => $transmission, // alias dla single-car.php
        'drivetrain' => $car['drivetrain'] ?? '',
        'engine_cc' => $car['engine_cc'] ?? '',
        'power_hp' => $car['power_hp'] ?? '',
        'color' => $car['color'] ?? '',
        'body_type' => $car['body_type'] ?? 'SUV',
        'vin' => $car['vin_masked'] ?? '',
        'vin_masked' => $car['vin_masked'] ?? '',
        'origin' => $car['origin'] ?? '',
        'owners' => $car['owners'] ?? '',
        'service_history' => $car['service_history'] ?? '',
        'accident_free' => !empty($car['accident_free']) ? '1' : '',
        'price' => $car['price_pln_brutto'],
        'lease_from' => $car['lease_from_pln'] ?? '',
        'lease_cession' => $car['lease_cession'] ?? '',
        'lease_capital' => $car['lease_capital'] ?? '',
        'lease_down_payment' => $car['lease_down_payment'] ?? '',
        'status' => $car['status'] ?? 'available',
        'video' => $car['video'] ?? '',
        'video_url' => $car['video'] ?? '',
        'description' => $car['description'] ?? '',
        'car_location' => $car['car_location'] ?? 'Hala ekspozycyjna · 53.70573, 16.69825 (Szczecinek)',
        'brochure_url' => $car['brochure_url'] ?? '',
    ];
    
    foreach ($meta as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }
    echo "Metadane zapisane<br>";
    
    // Wyposażenie - 5 kategorii
    $equipment_fields = [
        'equipment_audio' => $car['equipment_audio'] ?? '',
        'equipment_comfort' => $car['equipment_comfort'] ?? '',
        'equipment_assistance' => $car['equipment_assistance'] ?? '',
        'equipment_performance' => $car['equipment_performance'] ?? '',
        'equipment_safety' => $car['equipment_safety'] ?? '',
    ];
    
    $eq_count = 0;
    foreach ($equipment_fields as $key => $value) {
        if (!empty($value)) {
            update_post_meta($post_id, $key, $value);
            $lines = count(array_filter(explode("\n", $value)));
            echo "$key: $lines pozycji<br>";
            $eq_count += $lines;
        }
    }
    echo "Wyposażenie łącznie: $eq_count pozycji<br>";
    
    // ZDJĘCIA - importuj wszystkie do biblioteki mediów
    if (!empty($car['images']) && is_array($car['images'])) {
        // Sprawdź które pliki istnieją
        $valid_images = [];
        $imported_attachment_ids = [];
        
        foreach ($car['images'] as $img) {
            $image_path = $images_dir . $img;
            if (file_exists($image_path)) {
                $valid_images[] = $img;
                
                // Importuj każde zdjęcie do Media Library
                $attachment_id = salon_auto_import_image_to_media($image_path, $post_id, $title . ' - ' . pathinfo($img, PATHINFO_FILENAME));
                if ($attachment_id) {
                    $imported_attachment_ids[] = $attachment_id;
                }
            }
        }
        
        if (!empty($valid_images)) {
            // Zapisz nazwy plików (dla kompatybilności wstecznej)
            $gallery_string = implode(',', $valid_images);
            update_post_meta($post_id, 'gallery_files', $gallery_string);
            
            // Zapisz również ID attachmentów
            if (!empty($imported_attachment_ids)) {
                update_post_meta($post_id, 'gallery_attachment_ids', implode(',', $imported_attachment_ids));
            }
            
            echo "Zdjęcia: " . count($valid_images) . "/" . count($car['images']) . " (zaimportowano " . count($imported_attachment_ids) . " do biblioteki mediów)<br>";
            
            // Ustaw pierwsze zdjęcie jako Featured Image
            $current_thumbnail = get_post_thumbnail_id($post_id);
            if (!$current_thumbnail && !empty($imported_attachment_ids)) {
                set_post_thumbnail($post_id, $imported_attachment_ids[0]);
                echo "🖼️ Featured image ustawione (ID: {$imported_attachment_ids[0]})<br>";
            } else if ($current_thumbnail) {
                echo "🖼️ Featured image już istnieje<br>";
            }
        } else {
            echo "⚠️ Brak zdjęć w folderze!<br>";
        }
    }
    
    // Generuj broszurę
    if (function_exists('salon_auto_generate_brochure')) {
        $brochure_result = salon_auto_generate_brochure($post_id);
        if ($brochure_result) {
            echo "📄 Broszura wygenerowana<br>";
        } else {
            echo "⚠️ Nie udało się wygenerować broszury<br>";
        }
    }
    
    return $post_id;
}

/**
 * Import pojedynczego zdjęcia do Media Library
 */
function salon_auto_import_image_to_media($file_path, $post_id, $title) {
    if (!file_exists($file_path)) {
        return false;
    }
    
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    
    $filename = basename($file_path);
    $file_basename = pathinfo($filename, PATHINFO_FILENAME);
    
    // Sprawdź czy już istnieje w bibliotece (unikaj duplikatów)
    // Sprawdź po nazwie pliku w _wp_attached_file
    $existing = get_posts(array(
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
    
    if (!empty($existing)) {
        return $existing[0]->ID;
    }
    
    // Sprawdź również po tytule
    $existing_by_title = get_posts(array(
        'post_type' => 'attachment',
        'posts_per_page' => 1,
        'post_status' => 'inherit',
        'title' => $file_basename
    ));
    
    if (!empty($existing_by_title)) {
        return $existing_by_title[0]->ID;
    }
    
    // Wczytaj zawartość pliku
    $file_contents = file_get_contents($file_path);
    if ($file_contents === false) {
        return false;
    }
    
    // Użyj wp_upload_bits() do poprawnego importu
    $upload = wp_upload_bits($filename, null, $file_contents);
    
    if ($upload['error']) {
        return false;
    }
    
    // Przygotuj dane attachment
    $filetype = wp_check_filetype($filename);
    $attachment = array(
        'post_mime_type' => $filetype['type'],
        'post_title'     => sanitize_file_name($file_basename),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );
    
    $attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
    
    if (!is_wp_error($attach_id)) {
        $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
        wp_update_attachment_metadata($attach_id, $attach_data);
        return $attach_id;
    }
    
    return false;
}
