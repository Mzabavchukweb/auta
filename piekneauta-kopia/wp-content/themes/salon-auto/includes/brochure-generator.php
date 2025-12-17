<?php
/**
 * Automatyczny generator broszur HTML dla samochodów
 * Broszura generuje się automatycznie przy zapisie samochodu
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Automatycznie generuj broszurę przy zapisie samochodu
 */
add_action('save_post_car', 'salon_auto_auto_generate_brochure_on_save', 20, 2);
function salon_auto_auto_generate_brochure_on_save($post_id, $post) {
    // Nie generuj przy autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Nie generuj przy rewizji
    if (wp_is_post_revision($post_id)) {
        return;
    }
    
    // Sprawdź uprawnienia
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Sprawdź czy mamy minimum wymaganych danych
    $brand = get_post_meta($post_id, 'brand', true);
    $model = get_post_meta($post_id, 'model', true);
    $price = get_post_meta($post_id, 'price', true);
    
    if (empty($brand) || empty($model) || empty($price)) {
        return;
    }
    
    // Generuj broszurę
    salon_auto_generate_brochure($post_id);
}

/**
 * AJAX handler do ręcznego generowania broszury
 */
add_action('wp_ajax_generate_car_brochure', 'salon_auto_ajax_generate_brochure');
function salon_auto_ajax_generate_brochure() {
    check_ajax_referer('generate_brochure_nonce', 'nonce');
    
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Brak uprawnień');
    }
    
    $post_id = intval($_POST['post_id']);
    if (!$post_id) {
        wp_send_json_error('Brak ID posta');
    }
    
    $result = salon_auto_generate_brochure($post_id);
    
    if ($result) {
        wp_send_json_success(array(
            'message' => 'Broszura została wygenerowana!',
            'url' => $result
        ));
    } else {
        wp_send_json_error('Nie udało się wygenerować broszury.');
    }
}

/**
 * Główna funkcja generująca broszurę
 */
function salon_auto_generate_brochure($post_id) {
    $post = get_post($post_id);
    if (!$post) {
        return false;
    }
    
    $slug = $post->post_name;
    $theme_dir = get_stylesheet_directory();
    $theme_uri = get_stylesheet_directory_uri();
    
    // Pobierz wszystkie dane samochodu
    $brand = get_post_meta($post_id, 'brand', true);
    $model = get_post_meta($post_id, 'model', true);
    $trim = get_post_meta($post_id, 'trim', true);
    $year = get_post_meta($post_id, 'year', true);
    $price = get_post_meta($post_id, 'price', true);
    $mileage = get_post_meta($post_id, 'mileage', true);
    $fuel = get_post_meta($post_id, 'fuel', true);
    $gearbox = get_post_meta($post_id, 'gearbox', true) ?: get_post_meta($post_id, 'transmission', true);
    $power_hp = get_post_meta($post_id, 'power_hp', true);
    $engine_cc = get_post_meta($post_id, 'engine_cc', true);
    $drivetrain = get_post_meta($post_id, 'drivetrain', true);
    $color = get_post_meta($post_id, 'color', true);
    $vin_masked = get_post_meta($post_id, 'vin_masked', true);
    $origin = get_post_meta($post_id, 'origin', true);
    $owners = get_post_meta($post_id, 'owners', true);
    $body_type = get_post_meta($post_id, 'body_type', true);
    $description = get_post_meta($post_id, 'description', true);
    
    // Dane leasingu
    $lease_cession = get_post_meta($post_id, 'lease_cession', true);
    $lease_capital = get_post_meta($post_id, 'lease_capital', true);
    $lease_down_payment = get_post_meta($post_id, 'lease_down_payment', true);
    
    // Formatowanie
    $car_name = "{$brand} {$model}";
    $car_subtitle = $trim ? "{$trim} • {$year}" : $year;
    $price_formatted = number_format(intval($price), 0, ',', ' ') . ' zł';
    $mileage_formatted = $mileage ? number_format(intval($mileage), 0, ',', ' ') . ' km' : '';
    $engine_cc_formatted = $engine_cc ? number_format(intval($engine_cc), 0, ',', ' ') . ' cm³' : '';
    
    // Notatka o leasingu
    $lease_note = '';
    if ($lease_cession || $lease_capital || $lease_down_payment) {
        $parts = array();
        if ($lease_cession) $parts[] = "Cesja: {$lease_cession}";
        if ($lease_capital) $parts[] = "Kapitał: {$lease_capital}";
        if ($lease_down_payment) $parts[] = "Odstępne: {$lease_down_payment}";
        $lease_note = implode(' • ', $parts);
    }
    
    // === ZDJĘCIA (pełne HTTPS URL) ===
    $images = array();
    $gallery_files = get_post_meta($post_id, 'gallery_files', true);
    
    if (!empty($gallery_files)) {
        $files = array_filter(array_map('trim', explode(',', $gallery_files)));
        foreach ($files as $filename) {
            if (file_exists($theme_dir . '/images/' . $filename)) {
                // Pełny HTTPS URL
                $images[] = $theme_uri . '/images/' . $filename;
            }
        }
    }
    
    // Fallback: Featured image (pełny URL)
    if (empty($images) && has_post_thumbnail($post_id)) {
        $thumb_url = wp_get_attachment_image_url(get_post_thumbnail_id($post_id), 'large');
        if ($thumb_url) {
            $images[] = $thumb_url;
        }
    }
    
    // Główne zdjęcie = pierwsze z galerii
    $hero_image = !empty($images) ? $images[0] : '';
    
    // Dodatkowe zdjęcia (5 następnych po głównym)
    $gallery_images = array_slice($images, 1, 5);
    
    // === WYPOSAŻENIE ===
    // Pobierz z wszystkich kategorii
    $equipment_all = array();
    
    $equipment_categories = array(
        'equipment_audio' => 'Audio i multimedia',
        'equipment_comfort' => 'Komfort i dodatki',
        'equipment_assistance' => 'Systemy wspomagania kierowcy',
        'equipment_performance' => 'Osiągi i tuning',
        'equipment_safety' => 'Bezpieczeństwo',
    );
    
    foreach ($equipment_categories as $field => $label) {
        $items = get_post_meta($post_id, $field, true);
        if (!empty($items)) {
            // Obsługa zarówno tablicy jak i stringa
            if (is_array($items)) {
                // Jeśli to już tablica, użyj bezpośrednio
                $items_array = array_filter(array_map('trim', $items));
            } else {
                // Jeśli to string, rozdziel po nowych liniach
            $items_array = array_filter(array_map('trim', explode("\n", $items)));
            }
            
            foreach ($items_array as $item) {
                if (!empty($item)) {
                $equipment_all[] = $item;
                }
            }
        }
    }
    
    // === GENERUJ HTML ===
    $html = salon_auto_brochure_html(array(
        'car_name' => $car_name,
        'car_subtitle' => $car_subtitle,
        'year' => $year,
        'price' => $price_formatted,
        'mileage' => $mileage_formatted,
        'fuel' => $fuel ?: 'Benzyna',
        'gearbox' => $gearbox ?: 'Automatyczna',
        'power_hp' => $power_hp,
        'engine_cc' => $engine_cc_formatted,
        'drivetrain' => $drivetrain,
        'body_type' => $body_type,
        'color' => $color,
        'vin' => $vin_masked,
        'origin' => $origin,
        'owners' => $owners,
        'lease_note' => $lease_note,
        'equipment' => $equipment_all,
        'description' => $description,
        'hero_image' => $hero_image,
        'gallery' => $gallery_images,
        'theme_uri' => $theme_uri,
    ));
    
    // === ZAPISZ DO PLIKU ===
    $reports_dir = $theme_dir . '/reports/';
    if (!file_exists($reports_dir)) {
        wp_mkdir_p($reports_dir);
    }
    
    // Nazwa pliku na podstawie marki i modelu
    $brand_slug = sanitize_title($brand);
    $model_slug = sanitize_title($model);
    $filename = "{$brand_slug}-{$model_slug}-broszura.html";
    $filepath = $reports_dir . $filename;
    
    // Zapisz
    $saved = file_put_contents($filepath, $html);
    
    if ($saved) {
        // Zapisz URL broszury w meta
        update_post_meta($post_id, 'brochure_url', $filename);
        return $theme_uri . '/reports/' . $filename;
    }
    
    return false;
}

/**
 * Szablon HTML broszury
 */
function salon_auto_brochure_html($data) {
    ob_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title><?php echo esc_html($data['car_name']); ?> - Broszura | Piękne Auta</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Roboto+Condensed:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: "Avenir Next Condensed", "Roboto Condensed", -apple-system, sans-serif;
      background: #f3f1ee;
      color: #2d2d2d;
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
    }
    .page {
      width: 210mm;
      min-height: 297mm;
      margin: 20px auto;
      background: white;
      box-shadow: 0 2px 12px rgba(33, 33, 33, 0.08);
    }
    .header {
      background: #212121;
      padding: 15px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .logo {
      height: 80px;
      width: auto;
      filter: brightness(0) invert(1);
    }
    .header-tag {
      font-size: 13px;
      color: #b8b8b8;
      font-weight: 500;
    }
    .hero-image {
      width: 100%;
      height: 400px;
      object-fit: cover;
      background: #f5f5f5;
    }
    .content { padding: 40px; }
    .car-title {
      font-family: "Instrument Serif", serif;
      font-size: 48px;
      font-weight: 400;
      font-style: italic;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: #212121;
      margin-bottom: 8px;
    }
    .car-subtitle {
      font-size: 18px;
      color: #6b6b6b;
      margin-bottom: 30px;
    }
    .price-box {
      background: #f9f8f6;
      padding: 25px 30px;
      border-radius: 12px;
      margin-bottom: 35px;
      border-left: 4px solid #212121;
    }
    .price-label {
      font-size: 11px;
      color: #8a8a8a;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      margin-bottom: 8px;
      font-weight: 600;
    }
    .price {
      font-family: "Instrument Serif", serif;
      font-size: 40px;
      font-weight: 400;
      font-style: italic;
      color: #212121;
    }
    .lease-note {
      font-size: 12px;
      color: #4a4a4a;
      padding-top: 10px;
      border-top: 1px solid #e8e5e0;
      margin-top: 15px;
    }
    .specs-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      margin-bottom: 35px;
    }
    .spec {
      padding: 18px;
      background: #f9f8f6;
      border-radius: 10px;
      border: 1px solid #e8e5e0;
    }
    .spec-label {
      font-size: 11px;
      color: #8a8a8a;
      text-transform: uppercase;
      font-weight: 600;
      margin-bottom: 6px;
    }
    .spec-value {
      font-size: 16px;
      font-weight: 700;
      color: #212121;
    }
    .section { margin-bottom: 35px; }
    .section-title {
      font-family: "Instrument Serif", serif;
      font-size: 18px;
      font-weight: 400;
      font-style: italic;
      text-transform: uppercase;
      color: #212121;
      margin-bottom: 18px;
      padding-bottom: 10px;
      border-bottom: 2px solid #212121;
    }
    .description {
      background: #f9f8f6;
      padding: 20px;
      border-radius: 10px;
      border-left: 4px solid #212121;
      margin-bottom: 30px;
    }
    .description p {
      font-size: 13px;
      color: #4a4a4a;
      line-height: 1.7;
      margin-bottom: 10px;
    }
    .description p:last-child { margin-bottom: 0; }
    .gallery {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
      margin-bottom: 35px;
    }
    .gallery img {
      width: 100%;
      height: 120px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #e8e5e0;
    }
    .equipment {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
    }
    .equipment-item {
      font-size: 12px;
      color: #4a4a4a;
      padding: 5px 0;
      display: flex;
      gap: 8px;
      align-items: flex-start;
    }
    .equipment-item::before {
      content: "✓";
      color: #212121;
      font-weight: bold;
      flex-shrink: 0;
    }
    .footer {
      background: #212121;
      padding: 25px 40px;
      text-align: center;
    }
    .contact {
      font-size: 13px;
      color: #b8b8b8;
      line-height: 1.8;
    }
    .contact strong { color: #ffffff; }
    .disclaimer {
      font-size: 10px;
      color: #8a8a8a;
      margin-top: 12px;
    }
    .download-btn {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1000;
      display: flex;
      align-items: center;
      gap: 10px;
      background: #212121;
      color: #fff;
      border: none;
      padding: 14px 28px;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .download-btn:hover { background: #333; }
    @media print {
      body { background: white; }
      .page { margin: 0; box-shadow: none; width: 100%; min-height: auto; }
      .download-btn { display: none !important; }
    }
  </style>
</head>
<body>

<div class="page">
  
  <!-- Header -->
  <div class="header">
    <img src="<?php echo esc_url($data['theme_uri']); ?>/images/logo.svg" alt="Piękne Auta" class="logo">
    <span class="header-tag">Broszura pojazdu</span>
  </div>

  <!-- Hero Image -->
  <?php if (!empty($data['hero_image'])) : ?>
  <img src="<?php echo esc_url($data['hero_image']); ?>" alt="<?php echo esc_attr($data['car_name']); ?>" class="hero-image">
  <?php endif; ?>

  <div class="content">
    
    <h1 class="car-title"><?php echo esc_html($data['car_name']); ?></h1>
    <p class="car-subtitle"><?php echo esc_html($data['car_subtitle']); ?></p>
    
    <!-- Price -->
    <div class="price-box">
      <div class="price-label">Cena</div>
      <div class="price"><?php echo esc_html($data['price']); ?></div>
      <?php if (!empty($data['lease_note'])) : ?>
      <div class="lease-note"><strong>Warunki:</strong> <?php echo esc_html($data['lease_note']); ?></div>
      <?php endif; ?>
    </div>

    <!-- Specs Grid -->
    <div class="specs-grid">
      <?php if (!empty($data['year'])) : ?>
      <div class="spec"><div class="spec-label">Rok</div><div class="spec-value"><?php echo esc_html($data['year']); ?></div></div>
      <?php endif; ?>
      <?php if (!empty($data['mileage'])) : ?>
      <div class="spec"><div class="spec-label">Przebieg</div><div class="spec-value"><?php echo esc_html($data['mileage']); ?></div></div>
      <?php endif; ?>
      <?php if (!empty($data['fuel'])) : ?>
      <div class="spec"><div class="spec-label">Paliwo</div><div class="spec-value"><?php echo esc_html($data['fuel']); ?></div></div>
      <?php endif; ?>
      <?php if (!empty($data['power_hp'])) : ?>
      <div class="spec"><div class="spec-label">Moc</div><div class="spec-value"><?php echo esc_html($data['power_hp']); ?> KM</div></div>
      <?php endif; ?>
      <?php if (!empty($data['gearbox'])) : ?>
      <div class="spec"><div class="spec-label">Skrzynia</div><div class="spec-value"><?php echo esc_html($data['gearbox']); ?></div></div>
      <?php endif; ?>
      <?php if (!empty($data['drivetrain'])) : ?>
      <div class="spec"><div class="spec-label">Napęd</div><div class="spec-value"><?php echo esc_html($data['drivetrain']); ?></div></div>
      <?php endif; ?>
      <?php if (!empty($data['engine_cc'])) : ?>
      <div class="spec"><div class="spec-label">Pojemność</div><div class="spec-value"><?php echo esc_html($data['engine_cc']); ?></div></div>
      <?php endif; ?>
      <?php if (!empty($data['body_type'])) : ?>
      <div class="spec"><div class="spec-label">Nadwozie</div><div class="spec-value"><?php echo esc_html($data['body_type']); ?></div></div>
      <?php endif; ?>
      <?php if (!empty($data['color'])) : ?>
      <div class="spec"><div class="spec-label">Kolor</div><div class="spec-value"><?php echo esc_html($data['color']); ?></div></div>
      <?php endif; ?>
    </div>

    <!-- Description -->
    <?php if (!empty($data['description'])) : ?>
    <div class="section">
      <div class="section-title">Opis pojazdu</div>
      <div class="description">
        <?php echo wpautop(wp_kses_post($data['description'])); ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Gallery (5 additional photos) -->
    <?php if (!empty($data['gallery'])) : ?>
    <div class="section">
      <div class="section-title">Galeria zdjęć</div>
      <div class="gallery">
        <?php foreach ($data['gallery'] as $img) : ?>
        <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($data['car_name']); ?>">
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Equipment -->
    <?php if (!empty($data['equipment'])) : ?>
    <div class="section">
      <div class="section-title">Wyposażenie</div>
      <div class="equipment">
        <?php foreach (array_slice($data['equipment'], 0, 40) as $item) : ?>
        <div class="equipment-item"><?php echo esc_html($item); ?></div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- VIN -->
    <?php if (!empty($data['vin'])) : ?>
    <p style="font-size:12px;color:#8a8a8a;margin-top:20px;text-align:center;">VIN: <?php echo esc_html($data['vin']); ?></p>
    <?php endif; ?>

  </div>

  <!-- Footer -->
  <div class="footer">
    <div class="contact">
      <strong>Piękne Auta</strong> — Artur Kurzydłowski<br>
      Tel: <strong>502 42 82 82</strong> • Email: biuro@piekneauta.pl<br>
      NIP: 6731525915 • REGON: 330558443
    </div>
    <div class="disclaimer">
      Oferta ważna do momentu sprzedaży. Zdjęcia i opis mają charakter poglądowy.<br>
      © <?php echo date('Y'); ?> Piękne Auta — Wszystkie prawa zastrzeżone
    </div>
  </div>

</div>

<!-- Print Button -->
<button onclick="window.print()" class="download-btn">
  <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
  </svg>
  Pobierz PDF
</button>

</body>
</html>
<?php
    return ob_get_clean();
}

/**
 * Regeneruj wszystkie broszury (użyteczne po zmianach szablonu)
 */
function salon_auto_regenerate_all_brochures() {
    $cars = get_posts(array(
        'post_type' => 'car',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ));
    
    $count = 0;
    foreach ($cars as $car) {
        if (salon_auto_generate_brochure($car->ID)) {
            $count++;
        }
    }
    
    return $count;
}
