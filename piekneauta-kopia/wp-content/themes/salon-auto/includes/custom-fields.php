<?php
/**
 * Custom Fields for Car Post Type
 * Formularz edycji samochodu - zgodny ze stroną statyczną piekneauta.pl
 */

if (!defined('ABSPATH')) {
    exit;
}

// Enqueue admin scripts
add_action('admin_enqueue_scripts', 'salon_auto_admin_scripts');
function salon_auto_admin_scripts($hook) {
    global $post;
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        if (isset($post) && $post->post_type === 'car') {
    wp_enqueue_media();
    wp_enqueue_script('jquery-ui-sortable');
}
    }
}

// Register meta box
add_action('add_meta_boxes', 'salon_auto_add_car_meta_box');
function salon_auto_add_car_meta_box() {
    add_meta_box(
        'salon_auto_car_details',
        'Dane samochodu',
        'salon_auto_car_meta_box_callback',
        'car',
        'normal',
        'high'
    );
}

// Meta box callback
function salon_auto_car_meta_box_callback($post) {
    wp_nonce_field('salon_auto_save_car_meta', 'salon_auto_car_meta_nonce');
    
    // Get all field values
    $brand = get_post_meta($post->ID, 'brand', true);
    $model = get_post_meta($post->ID, 'model', true);
    $trim = get_post_meta($post->ID, 'trim', true);
    $year = get_post_meta($post->ID, 'year', true);
    $price = get_post_meta($post->ID, 'price', true);
    $status = get_post_meta($post->ID, 'status', true) ?: 'available';
    $mileage = get_post_meta($post->ID, 'mileage', true);
    $fuel = get_post_meta($post->ID, 'fuel', true);
    $gearbox = get_post_meta($post->ID, 'gearbox', true);
    $body_type = get_post_meta($post->ID, 'body_type', true);
    $engine_cc = get_post_meta($post->ID, 'engine_cc', true);
    $power_hp = get_post_meta($post->ID, 'power_hp', true);
    $drivetrain = get_post_meta($post->ID, 'drivetrain', true);
    $color = get_post_meta($post->ID, 'color', true);
    $vin_masked = get_post_meta($post->ID, 'vin_masked', true);
    $origin = get_post_meta($post->ID, 'origin', true);
    $owners = get_post_meta($post->ID, 'owners', true);
    $service_history = get_post_meta($post->ID, 'service_history', true);
    $description = get_post_meta($post->ID, 'description', true);
    $video = get_post_meta($post->ID, 'video', true);
    
    // Specyfikacja
    $engine_spec = get_post_meta($post->ID, 'engine_spec', true);
    $gearbox_spec = get_post_meta($post->ID, 'gearbox_spec', true);
    $drivetrain_spec = get_post_meta($post->ID, 'drivetrain_spec', true);
    $tires = get_post_meta($post->ID, 'tires', true);
    $wheels = get_post_meta($post->ID, 'wheels', true);
    
    // Finansowanie
    $lease_from_pln = get_post_meta($post->ID, 'lease_from_pln', true);
    $lease_cession = get_post_meta($post->ID, 'lease_cession', true);
    $lease_capital = get_post_meta($post->ID, 'lease_capital', true);
    $lease_down_payment = get_post_meta($post->ID, 'lease_down_payment', true);
    
    // Dodatkowe pola
    $registration_number = get_post_meta($post->ID, 'registration_number', true);
    $first_registration = get_post_meta($post->ID, 'first_registration', true);
    $car_location = get_post_meta($post->ID, 'car_location', true);
    
    // Equipment categories
    $equipment_audio = get_post_meta($post->ID, 'equipment_audio', true);
    $equipment_comfort = get_post_meta($post->ID, 'equipment_comfort', true);
    $equipment_assistance = get_post_meta($post->ID, 'equipment_assistance', true);
    $equipment_performance = get_post_meta($post->ID, 'equipment_performance', true);
    $equipment_safety = get_post_meta($post->ID, 'equipment_safety', true);
    
    // Gallery
    $gallery_ids = get_post_meta($post->ID, 'gallery', true);
    if (is_string($gallery_ids) && !empty($gallery_ids)) {
        $gallery_ids = array_filter(array_map('intval', explode(',', $gallery_ids)));
    } elseif (!is_array($gallery_ids)) {
        $gallery_ids = array();
    }
    
    // Convert equipment arrays to text
    $equipment_audio = is_array($equipment_audio) ? implode("\n", $equipment_audio) : $equipment_audio;
    $equipment_comfort = is_array($equipment_comfort) ? implode("\n", $equipment_comfort) : $equipment_comfort;
    $equipment_assistance = is_array($equipment_assistance) ? implode("\n", $equipment_assistance) : $equipment_assistance;
    $equipment_performance = is_array($equipment_performance) ? implode("\n", $equipment_performance) : $equipment_performance;
    $equipment_safety = is_array($equipment_safety) ? implode("\n", $equipment_safety) : $equipment_safety;
    
    ?>
    <style>
        .car-form { max-width: 100%; }
        .car-form h3 {
            font-size: 14px;
            font-weight: 600;
            margin: 30px 0 15px 0;
            padding: 10px 0;
            border-bottom: 1px solid #ccc;
        }
        .car-form h3:first-of-type { margin-top: 10px; }
        .car-form h4 {
            font-size: 13px;
            font-weight: 600;
            margin: 20px 0 10px 0;
            color: #444;
        }
        .car-form .row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        .car-form .row.single { display: block; }
        .car-form .col { flex: 1; }
        .car-form .col-2 { flex: 2; }
        .car-form label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
            font-size: 13px;
        }
        .car-form input[type="text"],
        .car-form input[type="number"],
        .car-form input[type="url"],
        .car-form select,
        .car-form textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #8c8f94;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .car-form input:focus,
        .car-form select:focus,
        .car-form textarea:focus {
            border-color: #2271b1;
            box-shadow: 0 0 0 1px #2271b1;
            outline: none;
        }
        .car-form input.field-error,
        .car-form select.field-error {
            border-color: #d63638 !important;
            box-shadow: 0 0 0 1px #d63638 !important;
        }
        .car-form .field-error-msg {
            color: #d63638;
            font-size: 12px;
            margin-top: 4px;
        }
        .car-form textarea { min-height: 100px; }
        .car-form .hint { font-size: 12px; color: #666; margin-top: 4px; }
        .car-form .required { color: #d63638; }
        .car-form .field-valid {
            border-color: #00a32a !important;
        }
        
        /* Gallery */
        .gallery-box {
            border: 1px solid #c3c4c7;
            padding: 15px;
            margin-top: 10px;
        }
        .gallery-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            min-height: 80px;
            margin-bottom: 15px;
        }
        .gallery-item {
            position: relative;
            cursor: move;
        }
        .gallery-item img {
            width: 90px;
            height: 65px;
            object-fit: cover;
            border: 1px solid #c3c4c7;
        }
        .gallery-item .num {
            position: absolute;
            bottom: 2px;
            left: 2px;
            background: rgba(0,0,0,0.6);
            color: #fff;
            font-size: 10px;
            padding: 1px 4px;
        }
        .gallery-item .del {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 18px;
            height: 18px;
            background: #d63638;
            color: #fff;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 12px;
            line-height: 16px;
        }
        .ui-sortable-placeholder {
            width: 90px;
            height: 65px;
            border: 2px dashed #2271b1;
            background: #f0f6fc;
        }
        
        /* ===================================
           MOBILE / TABLET RESPONSIVE STYLES
           Minimalne poprawki - WordPress ma własne responsywne style
           =================================== */
        @media screen and (max-width: 782px) {
            /* Kolumny na pełną szerokość */
            .car-form .row {
                flex-direction: column;
                gap: 15px;
            }
            .car-form .col,
            .car-form .col-2 {
                flex: none;
                width: 100%;
            }
            
            /* Zapobieganie zoomowi na iOS */
            .car-form input[type="text"],
            .car-form input[type="number"],
            .car-form input[type="url"],
            .car-form select,
            .car-form textarea {
                font-size: 16px;
                width: 100%;
                box-sizing: border-box;
            }
            
            /* Galeria - większe przyciski usuwania */
            .gallery-item .del {
                width: 28px;
                height: 28px;
                font-size: 16px;
                line-height: 26px;
            }
        }
    </style>
    
    <div class="car-form">
        
        <!-- ==================== ZDJĘCIA ==================== -->
        <h3>Zdjęcia</h3>
        
        <p><strong>Zdjęcie główne (miniatura):</strong> Ustaw w panelu po prawej → "Zdjęcie wyróżniające"</p>
        
        <?php 
        // Galeria zdjęć - zunifikowana (nazwy plików LUB ID z biblioteki)
        $gallery_files = get_post_meta($post->ID, 'gallery_files', true);
        $files = !empty($gallery_files) ? array_filter(array_map('trim', explode(',', $gallery_files))) : array();
        $theme_url = get_template_directory_uri() . '/images/';
        ?>
        <div class="row single" style="padding:15px;border-radius:8px;margin-bottom:20px;border:1px solid #c3c4c7;">
            <label style="font-weight:bold;font-size:14px;">📷 Galeria zdjęć (<span id="gallery-count"><?php echo count($files); ?></span>)</label>
            <p class="hint">Przeciągnij aby zmienić kolejność. Kliknij × aby usunąć.</p>
            <div id="gallery-sortable" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;max-height:400px;overflow-y:auto;padding:5px;">
                <?php foreach ($files as $i => $filename) : 
                    // Sprawdź czy to ID z biblioteki czy nazwa pliku
                    if (is_numeric($filename)) {
                        $img_url = wp_get_attachment_image_url(intval($filename), 'thumbnail');
                        $data_type = 'media';
                        $data_value = $filename;
                    } else {
                        $img_url = $theme_url . $filename;
                        $data_type = 'file';
                        $data_value = $filename;
                    }
                    if ($img_url) :
                ?>
                <div class="gallery-img-item" data-type="<?php echo $data_type; ?>" data-value="<?php echo esc_attr($data_value); ?>" style="position:relative;cursor:move;">
                    <img src="<?php echo esc_url($img_url); ?>" alt="" style="width:70px;height:52px;object-fit:cover;border-radius:4px;border:1px solid #c3c4c7;">
                    <span class="img-num" style="position:absolute;top:2px;left:2px;background:#2271b1;color:white;font-size:9px;padding:1px 4px;border-radius:2px;"><?php echo $i + 1; ?></span>
                    <button type="button" class="gallery-img-del" style="position:absolute;top:-5px;right:-5px;width:16px;height:16px;background:#d63638;color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:11px;line-height:14px;">×</button>
                </div>
                <?php endif; endforeach; ?>
            </div>
            <input type="hidden" name="gallery_files" id="gallery-files-input" value="<?php echo esc_attr($gallery_files); ?>">
            
            <!-- Przycisk dodawania zdjęć -->
            <div style="margin-top:15px;">
                <button type="button" id="add-gallery-images" class="button button-primary" style="font-size:14px;padding:8px 16px;">
                    📷 Dodaj zdjęcia
                </button>
                <span style="margin-left:10px;color:#666;font-size:12px;">Wybierz z biblioteki lub prześlij nowe</span>
            </div>
        </div>
        <script>
        jQuery(document).ready(function($) {
            var galleryFrame;
            
            function updateGallery() {
                var items = [];
                $('#gallery-sortable .gallery-img-item').each(function(index) {
                    var type = $(this).data('type');
                    var value = $(this).data('value');
                    items.push(value);
                    $(this).find('.img-num').text(index + 1);
                });
                $('#gallery-files-input').val(items.join(','));
                $('#gallery-count').text(items.length);
            }
            
            $('#gallery-sortable').sortable({ update: updateGallery });
            
            $(document).on('click', '.gallery-img-del', function(e) {
                e.preventDefault();
                $(this).closest('.gallery-img-item').remove();
                updateGallery();
            });
            
            // Dodawanie zdjęć z biblioteki mediów
            $('#add-gallery-images').on('click', function(e) {
                e.preventDefault();
                
                if (galleryFrame) {
                    galleryFrame.open();
                    return;
                }
                
                galleryFrame = wp.media({
                    title: 'Wybierz zdjęcia do galerii',
                    button: { text: 'Dodaj do galerii' },
                    library: { type: 'image' },
                    multiple: true
                });
                
                galleryFrame.on('select', function() {
                    var attachments = galleryFrame.state().get('selection').toJSON();
                    var count = $('#gallery-sortable .gallery-img-item').length;
                    
                    attachments.forEach(function(att) {
                        // Sprawdź czy już istnieje
                        if ($('#gallery-sortable .gallery-img-item[data-value="' + att.id + '"]').length > 0) {
                            return;
                        }
                        
                        count++;
                        var thumb = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
                        var html = '<div class="gallery-img-item" data-type="media" data-value="' + att.id + '" style="position:relative;cursor:move;">' +
                            '<img src="' + thumb + '" alt="" style="width:70px;height:52px;object-fit:cover;border-radius:4px;border:1px solid #c3c4c7;">' +
                            '<span class="img-num" style="position:absolute;top:2px;left:2px;background:#2271b1;color:white;font-size:9px;padding:1px 4px;border-radius:2px;">' + count + '</span>' +
                            '<button type="button" class="gallery-img-del" style="position:absolute;top:-5px;right:-5px;width:16px;height:16px;background:#d63638;color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:11px;line-height:14px;">×</button>' +
                            '</div>';
                        $('#gallery-sortable').append(html);
                    });
                    
                    updateGallery();
                });
                
                galleryFrame.open();
            });
        });
        </script>
        
        <div class="row single" style="margin-top: 20px;">
            <label>Film YouTube (opcjonalnie)</label>
            <input type="url" name="video" value="<?php echo esc_attr($video); ?>" placeholder="https://youtu.be/...">
            <p class="hint">Film będzie wyświetlany jako pierwszy element galerii</p>
        </div>
        
        <!-- ==================== PODSTAWOWE DANE ==================== -->
        <h3>Podstawowe dane</h3>
        
        <div class="row">
            <div class="col">
                <label>Marka <span class="required">*</span></label>
                <select name="brand" required>
                    <option value="">— wybierz —</option>
                    <?php
                    $brands = ['Audi', 'BMW', 'Mercedes-Benz', 'Porsche', 'Volkswagen', 'Volvo', 'Lexus', 'Land Rover', 'Jaguar', 'Maserati', 'Ferrari', 'Lamborghini', 'Bentley', 'Rolls-Royce', 'Aston Martin', 'McLaren', 'Alfa Romeo', 'Cupra', 'Tesla', 'Toyota', 'Honda', 'Mazda', 'Nissan', 'Skoda', 'Seat', 'Ford', 'Opel', 'Peugeot', 'Renault', 'Citroen', 'Fiat', 'Hyundai', 'Kia'];
                    foreach ($brands as $b) :
                    ?>
                    <option value="<?php echo esc_attr($b); ?>" <?php selected($brand, $b); ?>><?php echo esc_html($b); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col">
                <label>Model <span class="required">*</span></label>
                <input type="text" name="model" value="<?php echo esc_attr($model); ?>" placeholder="np. RS5, SQ8" required>
            </div>
            <div class="col">
                <label>Wersja</label>
                <input type="text" name="trim" value="<?php echo esc_attr($trim); ?>" placeholder="np. 450 KM Individual & Exclusive">
            </div>
        </div>
        
        <div class="row">
            <div class="col">
                <label>Rok produkcji <span class="required">*</span></label>
                <input type="number" name="year" value="<?php echo esc_attr($year); ?>" min="1990" max="2030" required>
            </div>
            <div class="col">
                <label>Cena (PLN) <span class="required">*</span></label>
                <input type="text" name="price" value="<?php echo esc_attr($price); ?>" placeholder="255000" required>
            </div>
            <div class="col">
                <label>Status</label>
                <select name="status">
                    <option value="available" <?php selected($status, 'available'); ?>>Dostępny</option>
                    <option value="reserved" <?php selected($status, 'reserved'); ?>>Zarezerwowany</option>
                    <option value="sold" <?php selected($status, 'sold'); ?>>Sprzedany</option>
            </select>
            </div>
        </div>
        
        <!-- ==================== NAJWAŻNIEJSZE ==================== -->
        <h3>Najważniejsze (wyświetlane na górze strony)</h3>
        
        <div class="row">
            <div class="col">
                <label>Przebieg (km) <span class="required">*</span></label>
                <input type="number" name="mileage" value="<?php echo esc_attr($mileage); ?>" placeholder="69000" required>
            </div>
            <div class="col">
                <label>Rodzaj paliwa <span class="required">*</span></label>
                <select name="fuel" required>
                    <option value="">— wybierz —</option>
                <option value="Benzyna" <?php selected($fuel, 'Benzyna'); ?>>Benzyna</option>
                <option value="Diesel" <?php selected($fuel, 'Diesel'); ?>>Diesel</option>
                <option value="Hybryda" <?php selected($fuel, 'Hybryda'); ?>>Hybryda</option>
                    <option value="Hybryda Plug-in" <?php selected($fuel, 'Hybryda Plug-in'); ?>>Hybryda Plug-in</option>
                <option value="Elektryczny" <?php selected($fuel, 'Elektryczny'); ?>>Elektryczny</option>
                    <option value="LPG" <?php selected($fuel, 'LPG'); ?>>Benzyna + LPG</option>
                </select>
            </div>
            <div class="col">
                <label>Skrzynia biegów <span class="required">*</span></label>
                <select name="gearbox" required>
                    <option value="">— wybierz —</option>
                    <option value="Automatyczna" <?php selected($gearbox, 'Automatyczna'); ?>>Automatyczna</option>
                    <option value="Manualna" <?php selected($gearbox, 'Manualna'); ?>>Manualna</option>
            </select>
            </div>
        </div>
        
        <div class="row">
            <div class="col">
                <label>Typ nadwozia</label>
                <select name="body_type">
                    <option value="">— wybierz —</option>
                    <option value="Sedan" <?php selected($body_type, 'Sedan'); ?>>Sedan</option>
                    <option value="Kombi" <?php selected($body_type, 'Kombi'); ?>>Kombi</option>
                    <option value="SUV" <?php selected($body_type, 'SUV'); ?>>SUV</option>
                    <option value="Coupe" <?php selected($body_type, 'Coupe'); ?>>Coupe</option>
                    <option value="Kabriolet" <?php selected($body_type, 'Kabriolet'); ?>>Kabriolet</option>
                    <option value="Hatchback" <?php selected($body_type, 'Hatchback'); ?>>Hatchback</option>
                    <option value="Van" <?php selected($body_type, 'Van'); ?>>Van</option>
            </select>
            </div>
            <div class="col">
                <label>Pojemność skokowa (cm³)</label>
                <input type="number" name="engine_cc" value="<?php echo esc_attr($engine_cc); ?>" placeholder="2894">
            </div>
            <div class="col">
                <label>Moc (KM)</label>
                <input type="number" name="power_hp" value="<?php echo esc_attr($power_hp); ?>" placeholder="450">
            </div>
        </div>
        
        <!-- ==================== OPIS POJAZDU ==================== -->
        <h3>Opis pojazdu</h3>
        
        <div class="row single">
            <label>Pełny opis</label>
            <textarea name="description" rows="8" placeholder="Opisz samochód..."><?php echo esc_textarea($description); ?></textarea>
            <p class="hint">Jeśli tekst jest długi, automatycznie pojawi się przycisk "Rozwiń/Zwiń"</p>
        </div>
        
        <!-- ==================== SZCZEGÓŁY ==================== -->
        <h3>Szczegóły</h3>
        
        <h4>Podstawowe</h4>
        <div class="row">
            <div class="col">
                <label>Kolor</label>
                <input type="text" name="color" value="<?php echo esc_attr($color); ?>" placeholder="np. Żółty Individual Exclusive">
            </div>
            <div class="col">
                <label>VIN</label>
                <input type="text" name="vin_masked" value="<?php echo esc_attr($vin_masked); ?>" placeholder="np. WUAZZZF51MA904785">
            </div>
        </div>
        
        <h4>Specyfikacja</h4>
        <div class="row">
            <div class="col">
                <label>Silnik (pełny opis)</label>
                <input type="text" name="engine_spec" value="<?php echo esc_attr($engine_spec); ?>" placeholder="np. 6-cylindrowy 2.9 l biturbo 450 KM">
            </div>
            <div class="col">
                <label>Skrzynia biegów (szczegóły)</label>
                <input type="text" name="gearbox_spec" value="<?php echo esc_attr($gearbox_spec); ?>" placeholder="np. Tiptronic">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label>Napęd</label>
                <input type="text" name="drivetrain_spec" value="<?php echo esc_attr($drivetrain_spec); ?>" placeholder="np. Quattro z samoblokującym się mechanizmem różnicowym">
            </div>
            <div class="col">
                <label>Napęd (skrót)</label>
                <select name="drivetrain">
                    <option value="">— wybierz —</option>
                    <option value="4x4" <?php selected($drivetrain, '4x4'); ?>>4x4</option>
                    <option value="Quattro" <?php selected($drivetrain, 'Quattro'); ?>>Quattro</option>
                    <option value="xDrive" <?php selected($drivetrain, 'xDrive'); ?>>xDrive</option>
                    <option value="4MATIC" <?php selected($drivetrain, '4MATIC'); ?>>4MATIC</option>
                    <option value="AWD" <?php selected($drivetrain, 'AWD'); ?>>AWD</option>
                    <option value="FWD" <?php selected($drivetrain, 'FWD'); ?>>Przedni</option>
                    <option value="RWD" <?php selected($drivetrain, 'RWD'); ?>>Tylny</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label>Opony</label>
                <input type="text" name="tires" value="<?php echo esc_attr($tires); ?>" placeholder="np. 275/30 R20">
            </div>
            <div class="col">
                <label>Felgi</label>
                <input type="text" name="wheels" value="<?php echo esc_attr($wheels); ?>" placeholder="np. Audi Sport 9x20&quot; czarne polerowane">
            </div>
        </div>
        
        <h4>Stan i historia</h4>
        <div class="row">
            <div class="col">
                <label>Kraj pochodzenia</label>
                <input type="text" name="origin" value="<?php echo esc_attr($origin); ?>" placeholder="np. Salon Polska">
            </div>
            <div class="col">
                <label>Liczba właścicieli</label>
                <input type="number" name="owners" value="<?php echo esc_attr($owners); ?>" placeholder="1" min="1">
            </div>
        </div>
        <div class="row single">
            <label>Ostatni przegląd / Historia serwisowa</label>
            <textarea name="service_history" rows="3" placeholder="np. Ostatni duży przegląd w ASO Audi wykonany kilkaset km temu"><?php echo esc_textarea($service_history); ?></textarea>
        </div>
        
        <h4>Finansowanie i koszty</h4>
        <div class="row">
            <div class="col">
                <label>Rata leasingu (PLN/miesiąc)</label>
                <input type="number" name="lease_from_pln" value="<?php echo esc_attr($lease_from_pln); ?>" placeholder="np. 3450">
            </div>
            <div class="col">
                <label>Cesja leasingu dla firm</label>
                <input type="text" name="lease_cession" value="<?php echo esc_attr($lease_cession); ?>" placeholder="np. 3450 PLN na 30 mcy">
            </div>
            <div class="col">
                <label>Kapitał do spłaty z wykupem</label>
                <input type="text" name="lease_capital" value="<?php echo esc_attr($lease_capital); ?>" placeholder="np. ok 155 tys PLN">
            </div>
            <div class="col">
                <label>Odstępne</label>
                <input type="text" name="lease_down_payment" value="<?php echo esc_attr($lease_down_payment); ?>" placeholder="np. 100 tys PLN">
            </div>
        </div>
        
        <h4>Dodatkowe informacje</h4>
        <div class="row">
            <div class="col">
                <label>Numer rejestracyjny pojazdu</label>
                <input type="text" name="registration_number" value="<?php echo esc_attr($registration_number); ?>" placeholder="np. GD9H742">
            </div>
            <div class="col">
                <label>Data pierwszej rejestracji</label>
                <input type="text" name="first_registration" value="<?php echo esc_attr($first_registration); ?>" placeholder="np. 12 czerwca 2023">
            </div>
            <div class="col">
                <label>Lokalizacja pojazdu</label>
                <input type="text" name="car_location" value="<?php echo esc_attr($car_location); ?>" placeholder="Hala ekspozycyjna · 53.70573, 16.69825 (Szczecinek)">
            </div>
        </div>
        
        <!-- ==================== WYPOSAŻENIE ==================== -->
        <h3>Wyposażenie</h3>
        <p class="hint">Każda pozycja w nowej linii</p>
        
        <h4>Audio i multimedia</h4>
        <div class="row single">
            <textarea name="equipment_audio" rows="6" placeholder="Wirtualny kokpit Audi virtual cockpit Plus
Nagłośnienie Bang & Olufsen
Radio cyfrowe"><?php echo esc_textarea($equipment_audio); ?></textarea>
        </div>
        
        <h4>Komfort i dodatki</h4>
        <div class="row single">
            <textarea name="equipment_comfort" rows="8" placeholder="Tapicerka Alcantara/skóra
Fotele sportowe RS
Panoramiczny dach szklany"><?php echo esc_textarea($equipment_comfort); ?></textarea>
        </div>
        
        <h4>Systemy wspomagania kierowcy</h4>
        <div class="row single">
            <textarea name="equipment_assistance" rows="6" placeholder="Asystent parkowania
Kamera cofania
Tempomat adaptacyjny"><?php echo esc_textarea($equipment_assistance); ?></textarea>
        </div>
        
        <h4>Osiągi i tuning</h4>
        <div class="row single">
            <textarea name="equipment_performance" rows="8" placeholder="Sportowy układ wydechowy RS
System Audi drive select
Felgi Audi Sport 20&quot;"><?php echo esc_textarea($equipment_performance); ?></textarea>
        </div>
        
        <h4>Bezpieczeństwo</h4>
        <div class="row single">
            <textarea name="equipment_safety" rows="6" placeholder="Poduszki powietrzne
Uchwyty ISOFIX
Reflektory Matrix LED"><?php echo esc_textarea($equipment_safety); ?></textarea>
        </div>
        
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        var galleryFrame;
        
        // ==========================================
        // WALIDACJA PÓL W CZASIE RZECZYWISTYM
        // ==========================================
        
        // Walidacja ceny - tylko cyfry
        $('input[name="price"]').on('input', function() {
            var val = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(val);
            if (val && parseInt(val) > 0) {
                $(this).removeClass('field-error').addClass('field-valid');
                $(this).siblings('.field-error-msg').remove();
            }
        }).on('blur', function() {
            var val = $(this).val();
            if (val) {
                // Formatuj cenę z spacjami
                var formatted = parseInt(val).toLocaleString('pl-PL').replace(/,/g, ' ');
                // Zapisz surową wartość
                $(this).data('raw-value', val);
            }
        });
        
        // Walidacja roku - 1990-2030
        $('input[name="year"]').on('input', function() {
            var val = $(this).val().replace(/[^0-9]/g, '');
            if (val.length > 4) val = val.substring(0, 4);
            $(this).val(val);
        }).on('blur', function() {
            var val = parseInt($(this).val());
            if (val < 1990) $(this).val(1990);
            if (val > 2030) $(this).val(2030);
            if (val >= 1990 && val <= 2030) {
                $(this).removeClass('field-error').addClass('field-valid');
            }
        });
        
        // Walidacja przebiegu - tylko cyfry
        $('input[name="mileage"]').on('input', function() {
            var val = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(val);
            if (val) {
                $(this).removeClass('field-error').addClass('field-valid');
            }
        });
        
        // Walidacja mocy - tylko cyfry
        $('input[name="power_hp"]').on('input', function() {
            var val = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(val);
        });
        
        // Walidacja pojemności - tylko cyfry
        $('input[name="engine_cc"]').on('input', function() {
            var val = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(val);
        });
        
        // Walidacja przed zapisem
        $('form#post').on('submit', function(e) {
            var errors = [];
            
            // Sprawdź wymagane pola
            if (!$('select[name="brand"]').val()) {
                errors.push('Wybierz markę');
                $('select[name="brand"]').addClass('field-error');
            }
            if (!$('input[name="model"]').val().trim()) {
                errors.push('Wpisz model');
                $('input[name="model"]').addClass('field-error');
            }
            if (!$('input[name="price"]').val()) {
                errors.push('Wpisz cenę');
                $('input[name="price"]').addClass('field-error');
            }
            if (!$('input[name="year"]').val()) {
                errors.push('Wpisz rok produkcji');
                $('input[name="year"]').addClass('field-error');
            }
            
            if (errors.length > 0) {
                e.preventDefault();
                alert('Popraw błędy:\n• ' + errors.join('\n• '));
                // Przewiń do pierwszego błędu
                $('.field-error').first().focus();
                return false;
            }
        });
        
        // Usuń klasę błędu przy focusie
        $('.car-form input, .car-form select, .car-form textarea').on('focus', function() {
            $(this).removeClass('field-error');
            $(this).siblings('.field-error-msg').remove();
        });
        
        // ==========================================
        // GALERIA - Update gallery input - globalna funkcja
        // ==========================================
        window.updateGallery = function() {
            var ids = [];
            $('#gallery-preview .gallery-item').each(function(i) {
                ids.push($(this).data('id'));
                $(this).find('.num').text(i + 1);
            });
            $('#gallery-input').val(ids.join(','));
            console.log('Gallery updated:', ids);
        };
        
        // Remove image - globalna funkcja
        window.removeImg = function(id) {
            $('#gallery-preview .gallery-item[data-id="' + id + '"]').remove();
            updateGallery();
        };
        
        // Sortable gallery - drag & drop
        if ($.fn.sortable) {
            $('#gallery-preview').sortable({
                items: '.gallery-item',
                cursor: 'move',
                tolerance: 'pointer',
                placeholder: 'ui-sortable-placeholder',
                forcePlaceholderSize: true,
                opacity: 0.7,
                revert: 100,
                start: function(e, ui) {
                    ui.placeholder.height(ui.item.height());
                    ui.placeholder.width(ui.item.width());
                },
                stop: function(e, ui) {
                    updateGallery();
            }
            }).disableSelection();
            console.log('Gallery sortable initialized');
        } else {
            console.error('jQuery UI Sortable not loaded!');
        }
        
        // Add images button
        $('#add-gallery-btn').on('click', function(e) {
            e.preventDefault();
            
            if (!wp || !wp.media) {
                alert('Media library not available. Please refresh the page.');
                return;
            }
            
            if (galleryFrame) {
                galleryFrame.open();
                return;
            }
            
            galleryFrame = wp.media({
                title: 'Wybierz zdjęcia do galerii',
                button: { text: 'Dodaj do galerii' },
                library: { type: 'image' },
                multiple: true
            });
            
            galleryFrame.on('select', function() {
                var attachments = galleryFrame.state().get('selection').toJSON();
                var added = 0;
                attachments.forEach(function(att) {
                    if ($('#gallery-preview .gallery-item[data-id="' + att.id + '"]').length === 0) {
                        var thumb = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
                        var count = $('#gallery-preview .gallery-item').length + 1;
                        var html = '<div class="gallery-item" data-id="' + att.id + '">' +
                            '<img src="' + thumb + '" alt="">' +
                            '<span class="num">' + count + '</span>' +
                            '<button type="button" class="del" onclick="removeImg(' + att.id + ')">×</button>' +
                            '</div>';
                        $('#gallery-preview').append(html);
                        added++;
                    }
                });
                if (added > 0) {
                    updateGallery();
                    // Reinitialize sortable after adding new items
                    $('#gallery-preview').sortable('refresh');
                }
            });
            
            galleryFrame.open();
        });
        
        // Initial update
        updateGallery();
        });
    </script>
    
    <!-- ==================== BROSZURA ==================== -->
    <h3 style="margin-top:30px;padding-top:20px;border-top:2px solid #ddd;">📄 Broszura pojazdu</h3>
    
    <?php
    $brochure_url = get_post_meta($post->ID, 'brochure_url', true);
    $brochure_exists = false;
    
    if (!empty($brochure_url)) {
        // Sprawdź czy plik istnieje
        $brochure_file = get_template_directory() . '/reports/' . $brochure_url;
        $brochure_exists = file_exists($brochure_file);
        $brochure_full_url = get_template_directory_uri() . '/reports/' . $brochure_url;
    }
    ?>
    
    <div style="background:#f0f0f1;padding:20px;border-radius:8px;margin-bottom:20px;">
        <p style="margin:0 0 15px 0;color:#50575e;">
            <strong>Automatyczne generowanie:</strong> Broszura generuje się automatycznie przy zapisie samochodu.
        </p>
        
        <?php if ($brochure_exists) : ?>
        <div style="display:flex;align-items:center;gap:15px;background:#fff;padding:15px;border-radius:6px;border:1px solid #c3c4c7;">
            <span style="font-size:24px;">✅</span>
            <div style="flex:1;">
                <strong style="color:#1d2327;">Broszura wygenerowana</strong><br>
                <code style="font-size:12px;color:#50575e;"><?php echo esc_html($brochure_url); ?></code>
            </div>
            <a href="<?php echo esc_url($brochure_full_url); ?>" target="_blank" class="button button-primary">
                Otwórz broszurę
            </a>
        </div>
        <?php else : ?>
        <div style="display:flex;align-items:center;gap:15px;background:#fff3cd;padding:15px;border-radius:6px;border:1px solid #ffc107;">
            <span style="font-size:24px;">⚠️</span>
            <div style="flex:1;">
                <strong style="color:#856404;">Brak broszury</strong><br>
                <span style="font-size:13px;color:#856404;">Wypełnij wymagane pola (marka, model, cena, zdjęcia) i zapisz samochód.</span>
            </div>
        </div>
        <?php endif; ?>
        
        <p style="margin:15px 0 0 0;font-size:12px;color:#50575e;">
            <strong>Wymagania:</strong> Marka, model, cena, min. 1 zdjęcie<br>
            <strong>Broszura zawiera:</strong> Główne zdjęcie + 5 dodatkowych, opis, specyfikację, wyposażenie
        </p>
    </div>
    
    <?php
}

// Save meta box data
add_action('save_post_car', 'salon_auto_save_car_meta');
function salon_auto_save_car_meta($post_id) {
    if (!isset($_POST['salon_auto_car_meta_nonce']) || !wp_verify_nonce($_POST['salon_auto_car_meta_nonce'], 'salon_auto_save_car_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Text fields
    $text_fields = array(
        'brand', 'model', 'trim', 'color', 'vin_masked', 'origin', 
        'status', 'fuel', 'gearbox', 'body_type', 'drivetrain',
        'lease_cession', 'lease_capital', 'lease_down_payment',
        'engine_spec', 'gearbox_spec', 'drivetrain_spec', 'tires', 'wheels',
        'registration_number', 'first_registration', 'car_location'
    );
    
    foreach ($text_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
    
    // Number fields z walidacją
    if (isset($_POST['price'])) {
        // Cena - usuń wszystko poza cyframi
        $price = salon_auto_validate_price($_POST['price']);
        update_post_meta($post_id, 'price', $price);
    }
    
    if (isset($_POST['year'])) {
        // Rok - walidacja 1900-2100
        $year = salon_auto_validate_year($_POST['year']);
        update_post_meta($post_id, 'year', $year);
    }
    
    if (isset($_POST['mileage'])) {
        // Przebieg - tylko dodatnie
        $mileage = salon_auto_validate_mileage($_POST['mileage']);
        update_post_meta($post_id, 'mileage', $mileage);
    }
    
    // Pozostałe pola numeryczne
    $other_number_fields = array('engine_cc', 'power_hp', 'owners', 'lease_from_pln');
    foreach ($other_number_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, max(0, absint($_POST[$field])));
        }
    }
    
    // Textarea fields
    $textarea_fields = array('description', 'service_history');
    foreach ($textarea_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, wp_kses_post($_POST[$field]));
        }
    }
    
    // URL field
    if (isset($_POST['video'])) {
        update_post_meta($post_id, 'video', esc_url_raw($_POST['video']));
    }
    
    // Equipment fields (convert to arrays)
    $equipment_fields = array(
        'equipment_audio', 'equipment_comfort', 'equipment_assistance',
        'equipment_performance', 'equipment_safety'
    );
    
    foreach ($equipment_fields as $field) {
        if (isset($_POST[$field])) {
            $lines = explode("\n", str_replace("\r", '', $_POST[$field]));
            $items = array();
            foreach ($lines as $line) {
                $line = trim(wp_strip_all_tags($line));
                if (!empty($line)) {
                    $items[] = $line;
    }
            }
            update_post_meta($post_id, $field, $items);
        }
    }
    
    // Gallery - stare pole (dla kompatybilności wstecznej)
    if (isset($_POST['gallery'])) {
        update_post_meta($post_id, 'gallery', sanitize_text_field($_POST['gallery']));
}
    
    // Gallery Files - główne pole galerii (nazwy plików lub ID z biblioteki)
    if (isset($_POST['gallery_files'])) {
        $gallery_files = sanitize_text_field($_POST['gallery_files']);
        update_post_meta($post_id, 'gallery_files', $gallery_files);
}

    // Wyczyść cache po aktualizacji samochodu
    wp_cache_delete('salon_auto_all_cars_list', 'salon_auto_cars');
    if (function_exists('salon_auto_clear_cars_cache')) {
        salon_auto_clear_cars_cache();
    }
}

/**
 * Główna funkcja do pobierania pól samochodu
 * GŁÓWNA DEFINICJA - fallback jest w functions.php
 */
if (!function_exists('salon_auto_get_car_field')) {
    function salon_auto_get_car_field($post_id, $field, $default = '') {
        $value = get_post_meta($post_id, $field, true);
        return $value !== '' ? $value : $default;
    }
}

/**
 * Główna funkcja do formatowania ceny
 * GŁÓWNA DEFINICJA
 */
if (!function_exists('salon_auto_format_price')) {
    function salon_auto_format_price($price) {
        if (empty($price)) return '';
        // Upewnij się że cena jest liczbą
        $price = preg_replace('/[^0-9]/', '', strval($price));
        if (empty($price)) return '';
        return number_format((float)$price, 0, ',', ' ') . ' zł';
    }
}

/**
 * Walidacja pola ceny - zwraca tylko cyfry
 */
if (!function_exists('salon_auto_validate_price')) {
    function salon_auto_validate_price($price) {
        return preg_replace('/[^0-9]/', '', strval($price));
    }
}

/**
 * Walidacja pola roku - 1990-2030 (realistyczny zakres)
 */
if (!function_exists('salon_auto_validate_year')) {
    function salon_auto_validate_year($year) {
        $year = absint($year);
        if ($year < 1990) $year = 1990;
        if ($year > 2030) $year = 2030;
        return $year;
    }
}

/**
 * Walidacja pola przebiegu - tylko liczby dodatnie
 */
if (!function_exists('salon_auto_validate_mileage')) {
    function salon_auto_validate_mileage($mileage) {
        return max(0, absint($mileage));
    }
}

