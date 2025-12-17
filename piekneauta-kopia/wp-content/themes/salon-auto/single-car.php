<?php
/**
 * Single Car Template
 * 
 * Wyświetla szczegóły pojedynczego samochodu z pełną galerią, formularzami i animacjami
 */

get_header();

while (have_posts()) : the_post();
    $post_id = get_the_ID();
    $post_slug = get_post_field('post_name', $post_id);
    
    // Use custom fields system
    $price = salon_auto_get_car_field($post_id, 'price');
    $year = salon_auto_get_car_field($post_id, 'year');
    $mileage = salon_auto_get_car_field($post_id, 'mileage');
    $gearbox = salon_auto_get_car_field($post_id, 'gearbox');
    $fuel = salon_auto_get_car_field($post_id, 'fuel');
    $trim = salon_auto_get_car_field($post_id, 'trim');
    $brand = salon_auto_get_car_field($post_id, 'brand');
    $model = salon_auto_get_car_field($post_id, 'model');
    $color = salon_auto_get_car_field($post_id, 'color');
    $body_type = salon_auto_get_car_field($post_id, 'body_type');
    $power_hp = salon_auto_get_car_field($post_id, 'power_hp');
    $engine_cc = salon_auto_get_car_field($post_id, 'engine_cc');
    $drivetrain = salon_auto_get_car_field($post_id, 'drivetrain');
    $accident_free = salon_auto_get_car_field($post_id, 'accident_free');
    $service_history = salon_auto_get_car_field($post_id, 'service_history');
    $origin = salon_auto_get_car_field($post_id, 'origin');
    $owners = salon_auto_get_car_field($post_id, 'owners');
    $vin_masked = salon_auto_get_car_field($post_id, 'vin_masked');
    $lease_from_pln = salon_auto_get_car_field($post_id, 'lease_from_pln');
    $lease_cession = salon_auto_get_car_field($post_id, 'lease_cession');
    $lease_capital = salon_auto_get_car_field($post_id, 'lease_capital');
    $lease_down_payment = salon_auto_get_car_field($post_id, 'lease_down_payment');
    $brochure_url = salon_auto_get_car_field($post_id, 'brochure_url');
    $video_url = salon_auto_get_car_field($post_id, 'video');
    $car_location = salon_auto_get_car_field($post_id, 'car_location') ?: 'Hala ekspozycyjna · 53.70573, 16.69825 (Szczecinek)';
    $description = salon_auto_get_car_field($post_id, 'description');
    
    // Status samochodu
    $car_status = salon_auto_get_car_field($post_id, 'status') ?: 'available';
    
    // Mapowanie statusu na tekst i kolory
    $status_config = array(
        'available' => array(
            'text' => 'DOSTĘPNY',
            'bg_class' => 'bg-green-500',
            'text_class' => 'text-white'
        ),
        'reserved' => array(
            'text' => 'ZAREZERWOWANY',
            'bg_class' => 'bg-amber-500',
            'text_class' => 'text-white'
        ),
        'sold' => array(
            'text' => 'SPRZEDANY',
            'bg_class' => 'bg-red-500',
            'text_class' => 'text-white'
        )
    );
    $current_status = isset($status_config[$car_status]) ? $status_config[$car_status] : $status_config['available'];
    
    // Equipment categories
    $equipment_audio = salon_auto_get_car_field($post_id, 'equipment_audio');
    $equipment_comfort = salon_auto_get_car_field($post_id, 'equipment_comfort');
    $equipment_assistance = salon_auto_get_car_field($post_id, 'equipment_assistance');
    $equipment_performance = salon_auto_get_car_field($post_id, 'equipment_performance');
    $equipment_safety = salon_auto_get_car_field($post_id, 'equipment_safety');
    
    // Szczegóły - Specyfikacja
    $engine_spec = salon_auto_get_car_field($post_id, 'engine_spec');
    $gearbox_spec = salon_auto_get_car_field($post_id, 'gearbox_spec');
    $drivetrain_spec = salon_auto_get_car_field($post_id, 'drivetrain_spec');
    $tires = salon_auto_get_car_field($post_id, 'tires');
    $wheels = salon_auto_get_car_field($post_id, 'wheels');
    
    // Build gallery array - używa nowej funkcji pomocniczej
    $gallery = array();
    $total_images = 0;
    $alt_text = ($brand ? esc_html($brand) . ' ' : '') . ($model ? esc_html($model) . ' ' : '') . ($trim ? esc_html($trim) . ' ' : '') . ($year ? esc_html($year) : '');
    if (empty($alt_text)) $alt_text = get_the_title();
    
    // Add video as first item if exists
    if ($video_url) {
        $youtube_id = '';
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $video_url, $matches)) {
            $youtube_id = $matches[1];
        }
        if ($youtube_id) {
        $gallery[] = array(
            'type' => 'video',
            'url' => $video_url,
                'youtube_id' => $youtube_id,
                'thumbnail' => 'https://img.youtube.com/vi/' . $youtube_id . '/maxresdefault.jpg',
        );
        $total_images++;
    }
    }
    
    // Pobierz zdjęcia z nowej funkcji (obsługuje gallery_files i gallery)
    $car_images = salon_auto_get_car_gallery($post_id);
    foreach ($car_images as $img) {
        $gallery[] = array(
            'type' => 'image',
            'url' => $img['url'],
            'thumbnail' => $img['thumbnail'],
            'alt' => $alt_text,
        );
        $total_images++;
            }
    
    // FALLBACK: If gallery is empty, try to load images from theme folder
    if (count($gallery) <= ($video_url ? 1 : 0)) {
        $image_prefixes = array(
            'audi-sq8-2023' => 'audi-sq8',
            'audi-rs5-2023-450hp-individual' => 'audi-rs5',
            'audi-a8-2019-50tdi-quattro' => 'audi-a8',
            'bmw-seria-7-2018-730d-xdrive' => 'bmw-7',
            'audi-a6-limousine' => 'audi-a6',
            'cupra-formentor-2023-tfsi' => 'cupra-formentor',
        );
        
        if (isset($image_prefixes[$post_slug])) {
            $prefix = $image_prefixes[$post_slug];
            $theme_dir = get_stylesheet_directory();
            $theme_uri = get_stylesheet_directory_uri();
            $default_alt = ($brand ? esc_html($brand) . ' ' : '') . ($model ? esc_html($model) . ' ' : '') . ($trim ? esc_html($trim) : '');
            
            // Special handling for RS5 - skip audi-rs5-01.jpg (start from 02)
            $start_index = ($post_slug === 'audi-rs5-2023-450hp-individual') ? 2 : 1;
            
            // Count images (check up to 60 images)
            for ($i = $start_index; $i <= 60; $i++) {
                $num = str_pad($i, 2, '0', STR_PAD_LEFT);
                $filename = $prefix . '-' . $num . '.jpg';
                $filepath = $theme_dir . '/images/' . $filename;
                
                if (file_exists($filepath)) {
                    $img_url = $theme_uri . '/images/' . $filename;
            $gallery[] = array(
                'type' => 'image',
                        'id' => 0,
                        'url' => $img_url,
                        'thumbnail' => $img_url,
                        'alt' => $default_alt . ' - zdjęcie ' . $i,
            );
            $total_images++;
                }
        }
        }
    }
    
    // Get contact info - sanitized
    $phone = sanitize_text_field(salon_auto_get_option('phone', '502 42 82 82'));
    $phone_clean = preg_replace('/[^0-9+]/', '', $phone);
    $email = sanitize_email(salon_auto_get_option('email', 'biuro@piekneauta.pl'));
    
    // Get contact page URL - try multiple methods for WordPress
    $contact_url = '';
    
    // Method 1: Try by slug 'kontakt'
    $contact_page = get_page_by_path('kontakt');
    if ($contact_page) {
        $contact_url = get_permalink($contact_page->ID);
    }
    
    // Method 2: Try by title 'Kontakt'
    if (empty($contact_url)) {
        $contact_page = get_page_by_title('Kontakt');
        if ($contact_page) {
            $contact_url = get_permalink($contact_page->ID);
        }
    }
    
    // Method 3: Search for page with kontakt template
    if (empty($contact_url)) {
        $pages = get_posts(array(
            'post_type' => 'page',
            'posts_per_page' => 1,
            'meta_key' => '_wp_page_template',
            'meta_value' => 'page-kontakt.php'
        ));
        if (!empty($pages)) {
            $contact_url = get_permalink($pages[0]->ID);
        }
    }
    
    // Method 4: Search by slug containing 'kontakt'
    if (empty($contact_url)) {
        $pages = get_posts(array(
            'post_type' => 'page',
            'posts_per_page' => 1,
            'name' => 'kontakt'
        ));
        if (!empty($pages)) {
            $contact_url = get_permalink($pages[0]->ID);
        }
    }
    
    // Final fallback - use home_url with /kontakt/ or /index.php/kontakt/
    if (empty($contact_url)) {
        // Check if using pretty permalinks
        $permalink_structure = get_option('permalink_structure');
        if (!empty($permalink_structure)) {
            $contact_url = home_url('/kontakt/');
        } else {
            $contact_url = home_url('/index.php/kontakt/');
        }
    }
    
    // Car name for forms - sanitized with fallback to title
    $car_name = trim(($brand ? sanitize_text_field($brand) . ' ' : '') . 
                     ($model ? sanitize_text_field($model) . ' ' : '') . 
                     ($trim ? sanitize_text_field($trim) . ' ' : '') . 
                     ($year ? absint($year) : ''));
    
    // Fallback to post title if car name is empty
    if (empty($car_name)) {
        $car_name = get_the_title();
    }
    $car_name_encoded = urlencode($car_name);
    
    // Schema.org Product/Vehicle dla SEO
    $schema_images = array();
    if (!empty($gallery)) {
        foreach (array_slice($gallery, 0, 10) as $item) {
            if ($item['type'] === 'image' && !empty($item['url'])) {
                $schema_images[] = $item['url'];
            }
        }
    }
    
    $schema_availability = 'https://schema.org/InStock';
    if ($car_status === 'sold') {
        $schema_availability = 'https://schema.org/SoldOut';
    } elseif ($car_status === 'reserved') {
        $schema_availability = 'https://schema.org/LimitedAvailability';
    }
    
    $schema_data = array(
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        '@id' => get_permalink($post_id),
        'name' => ($brand ? $brand . ' ' : '') . ($model ? $model : '') . ($trim ? ' ' . $trim : ''),
        'description' => $description ? wp_trim_words(strip_tags($description), 50, '...') : 
            sprintf('%s %s %s %s, %s km, %s, %s. Cena: %s zł.', 
                $brand, $model, $trim, $year, 
                number_format($mileage, 0, ',', ' '),
                $fuel, $gearbox,
                number_format($price, 0, ',', ' ')
            ),
        'image' => $schema_images,
        'brand' => array(
            '@type' => 'Brand',
            'name' => $brand
        ),
        'category' => 'Samochody premium używane',
        'offers' => array(
            '@type' => 'Offer',
            'url' => get_permalink($post_id),
            'priceCurrency' => 'PLN',
            'price' => $price,
            'priceValidUntil' => date('Y-12-31'),
            'availability' => $schema_availability,
            'itemCondition' => 'https://schema.org/UsedCondition',
            'seller' => array(
                '@type' => 'AutoDealer',
                'name' => 'Piękne auta - Artur Kurzydłowski',
                'telephone' => '+48502428282',
                'url' => home_url('/')
            )
        ),
        'vehicleIdentificationNumber' => $vin_masked,
        'mileageFromOdometer' => array(
            '@type' => 'QuantitativeValue',
            'value' => $mileage,
            'unitCode' => 'KMT'
        ),
        'vehicleModelDate' => $year,
        'fuelType' => $fuel,
        'vehicleTransmission' => $gearbox,
        'driveWheelConfiguration' => $drivetrain,
        'vehicleEngine' => array(
            '@type' => 'EngineSpecification',
            'engineDisplacement' => array(
                '@type' => 'QuantitativeValue',
                'value' => $engine_cc,
                'unitCode' => 'CMQ'
            ),
            'enginePower' => array(
                '@type' => 'QuantitativeValue',
                'value' => $power_hp,
                'unitCode' => 'BHP'
            )
        ),
        'color' => $color,
        'bodyType' => $body_type
    );
    
    // Usuń puste wartości
    $schema_data = array_filter($schema_data, function($v) {
        return !empty($v) && $v !== array();
    });
?>

<!-- Schema.org Product/Vehicle dla SEO -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<!-- Schema.org BreadcrumbList -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "Strona główna",
            "item": "<?php echo esc_url(home_url('/')); ?>"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "Samochody",
            "item": "<?php echo esc_url(home_url('/samochody/')); ?>"
        },
        {
            "@type": "ListItem",
            "position": 3,
            "name": "<?php echo esc_html($car_name ?: get_the_title()); ?>",
            "item": "<?php echo esc_url(get_permalink($post_id)); ?>"
        }
    ]
}
</script>

<style>
/* Gallery Styles */
.thumb-strip {
  display: flex;
  gap: 0.65rem;
  overflow-x: auto;
  scroll-behavior: smooth;
  padding-bottom: 0.25rem;
  scrollbar-width: thin;
  scrollbar-color: #d1d5db transparent;
}

.thumb-strip::-webkit-scrollbar {
  height: 6px;
}

.thumb-strip::-webkit-scrollbar-track {
  background: transparent;
}

.thumb-strip::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 9999px;
}

.thumb-strip button {
  flex: 0 0 7rem;
  min-width: 7rem;
  height: 5.5rem;
  padding: 0;
  box-sizing: border-box;
  position: relative;
}

.thumb-strip button img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
  transform: scale(1.1);
  transform-origin: center;
}

.gallery-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 2.75rem;
  height: 2.75rem;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(255, 255, 255, 0.95);
  border: 1px solid #e5e7eb;
  color: #111827;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
  transition: all 0.2s ease;
  z-index: 10;
}

.gallery-nav:hover {
  color: #be123c;
  border-color: #be123c;
}

.gallery-nav svg {
  width: 1.1rem;
  height: 1.1rem;
  display: block;
  flex-shrink: 0;
}

.gallery-nav-left {
  left: 0.75rem;
}

.gallery-nav-right {
  right: 0.75rem;
}

.gallery-counter {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.5rem 0.95rem;
  border-radius: 9999px;
  background-color: rgba(17, 24, 39, 0.92);
  color: #f9fafb;
  font-weight: 600;
  font-size: 0.85rem;
  letter-spacing: 0.02em;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
  border: 1px solid rgba(255, 255, 255, 0.2);
  pointer-events: none;
  z-index: 10;
}

.gallery-counter svg {
  width: 1rem;
  height: 1rem;
}

.youtube-thumb {
  position: relative;
}

.youtube-thumb-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  pointer-events: none;
}

.youtube-thumb-overlay::before {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.35);
  border-radius: inherit;
}

.youtube-play-button {
  width: clamp(3rem, 12vw, 4.75rem);
  height: auto;
  filter: drop-shadow(0 12px 25px rgba(0, 0, 0, 0.35));
  position: relative;
}

.thumb-strip .youtube-play-button {
  width: clamp(1.45rem, 5vw, 2.5rem);
}

.youtube-play-button path[data-part="bg"] {
  fill: #ff0000;
}

.youtube-play-button path[data-part="triangle"] {
  fill: #ffffff;
}

body.lightbox-open header,
body.lightbox-open .sticky-lightbox-hide {
  opacity: 0 !important;
  pointer-events: none !important;
}

details summary::-webkit-details-marker {
  display: none;
}

details summary svg {
  width: 1.1rem;
  height: 1.1rem;
  flex-shrink: 0;
  transition: transform 0.2s ease;
}

details[open] summary svg {
  transform: rotate(180deg);
}

.details-icon-list li {
  position: relative;
  padding-left: 1.5rem;
  margin-bottom: 0.65rem;
  line-height: 1.65;
}

.details-icon-list li::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0.4rem;
  width: 0.35rem;
  height: 0.7rem;
  border-right: 2px solid #111827;
  border-bottom: 2px solid #111827;
  transform: rotate(45deg);
}

/* Larger gallery nav buttons on mobile */
@media (max-width: 1023px) {
  .gallery-nav {
    width: 3rem;
    height: 3rem;
  }
  
  .gallery-nav svg {
    width: 1.5rem;
    height: 1.5rem;
  }
  
  .gallery-nav-left {
    left: 0.5rem;
  }
  
  .gallery-nav-right {
    right: 0.5rem;
  }
}

@media (min-width: 1024px) {
  .thumb-strip button {
    flex-basis: 8rem;
    min-width: 8rem;
    height: 6.5rem;
    padding: 0;
  }
}

@media (max-width: 768px) {
  .thumb-strip button {
    flex-basis: 6rem;
    min-width: 6rem;
    height: 5rem;
    padding: 0;
  }
}

/* Responsive styles for entire page - mobile only */
@media (max-width: 1023px) {
  /* Main container adjustments */
  main .container {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }
  
  /* Grid layout - single column on mobile */
  main .container > .grid {
    display: flex !important;
    flex-direction: column;
    gap: 1.5rem !important;
  }
  
  /* Left column - extract gallery separately on mobile */
  main .container > .grid > div:first-child {
    width: 100% !important;
    display: contents !important;
  }
  
  /* Gallery - first on mobile (extracted from left column) */
  main .container > .grid > div:first-child > div:first-child {
    order: 1;
    width: 100% !important;
  }
  
  /* Sidebar - second on mobile (right after gallery) */
  main .container > .grid > div:last-child {
    width: 100% !important;
    order: 2;
    margin-top: 0 !important;
  }
  
  /* Sticky sidebar - not sticky on mobile */
  main .container > .grid > div:last-child .sticky {
    position: static !important;
    top: auto !important;
  }
  
  /* Rest of sections in left column - after sidebar on mobile */
  main .container > .grid > div:first-child > section,
  main .container > .grid > div:first-child > div:not(:first-child) {
    order: 3;
    width: 100% !important;
  }
  
  /* Sections padding on mobile */
  section {
    padding: 1.5rem !important;
  }
}
</style>

<?php
// BREADCRUMBS - czysta logika od zera
$breadcrumb_home_url = home_url('/');
$breadcrumb_cars_url = home_url('/samochody/');
$breadcrumb_car_title = !empty($car_name) ? $car_name : get_the_title();
?>

<main class="bg-gray-50">
    <!-- Breadcrumbs -->
    <nav class="container mx-auto px-4 sm:px-6 pt-4" aria-label="Breadcrumb">
        <ol class="flex items-center text-sm text-gray-500 space-x-2">
            <li>
                <a href="<?php echo esc_url($breadcrumb_home_url); ?>" class="hover:text-primary transition-colors">Strona główna</a>
            </li>
            <li class="flex items-center">
                <svg class="w-4 h-4 mx-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <a href="<?php echo esc_url($breadcrumb_cars_url); ?>" class="hover:text-primary transition-colors">Samochody</a>
            </li>
            <li class="flex items-center">
                <svg class="w-4 h-4 mx-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="text-gray-900 font-medium"><?php echo esc_html($breadcrumb_car_title); ?></span>
            </li>
        </ol>
    </nav>
    
    <div class="container mx-auto py-6 sm:py-8 md:py-12 px-4 sm:px-6">
        <div class="grid lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- Left Column - Images & Details -->
            <div class="lg:col-span-2 space-y-12" 
                 x-data="galleryState()" 
                 @keydown.window="handleKeydown($event)">
                
                <!-- Gallery -->
                <?php if (!empty($gallery)) : 
                    // Build mapping: currentImage (0, 1, 2...) -> gallery index
                    $image_map = array();
                    $current_idx = 0;
                    if ($video_url) {
                        $image_map[0] = 0; // Video is always index 0
                        $current_idx = 1;
                    }
                    foreach ($gallery as $idx => $item) {
                        if ($item['type'] === 'image') {
                            $image_map[$current_idx] = $idx;
                            $current_idx++;
                        }
                    }
                ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="relative aspect-[4/3] bg-black cursor-zoom-in" 
                         role="button" 
                         tabindex="0" 
                         @click="openLightbox()" 
                         @keydown.enter.prevent="openLightbox()" 
                         @keydown.space.prevent="openLightbox()">
                        <!-- Film YouTube (index 0) -->
                        <?php if ($video_url) : 
                            // Extract YouTube ID
                            $video_id = '';
                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $video_url, $vid_matches)) {
                                $video_id = $vid_matches[1];
                            }
                        ?>
                        <?php if ($video_id) : ?>
                        <div class="w-full h-full absolute inset-0" x-show="currentImage === 0" x-cloak>
                            <iframe width="100%" height="100%" 
                                    src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>?rel=0" 
                                    title="<?php echo esc_attr(get_the_title()); ?> - Film prezentacyjny" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                    allowfullscreen 
                                    class="w-full h-full"></iframe>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                        
                        <!-- Zdjęcia (index 1+) -->
                        <?php 
                        $img_display_idx = 1;
                        foreach ($gallery as $idx => $item) {
                            if ($item['type'] === 'image') {
                        ?>
                        <img src="<?php echo esc_url($item['url']); ?>" 
                             alt="<?php echo esc_attr($item['alt']); ?>" 
                             class="car-image w-full h-full object-cover absolute inset-0" 
                             x-show="currentImage === <?php echo $img_display_idx; ?>" 
                             x-cloak
                             width="<?php echo isset($item['width']) ? esc_attr($item['width']) : '1920'; ?>"
                             height="<?php echo isset($item['height']) ? esc_attr($item['height']) : '1080'; ?>"
                             style="aspect-ratio: 16/9;">
                        <?php 
                                $img_display_idx++;
                            }
                        }
                        ?>
                        
                        <div class="gallery-counter">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7a2 2 0 012-2h2l1-1h6l1 1h2a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V7z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 11l3 3 3-3"></path>
                            </svg>
                            <span x-text="hasVideo ? (currentImage === 0 ? 1 : currentImage) : currentImage"></span>/<span x-text="hasVideo ? images.length + 1 : images.length"></span>
                        </div>
                        
                        <button type="button" @click.stop="showPrev()" class="gallery-nav gallery-nav-left" aria-label="Poprzednie zdjęcie">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button type="button" @click.stop="showNext()" class="gallery-nav gallery-nav-right" aria-label="Następne zdjęcie">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="p-2 sm:p-4">
                        <div class="thumb-strip" x-ref="thumbStrip">
                            <?php if ($video_url && $video_id) : ?>
                            <button data-index="0" 
                                    @click="currentImage = 0; scrollThumbIntoView()" 
                                    class="rounded-lg overflow-hidden border-2 transition-colors relative youtube-thumb" 
                                    :class="currentImage === 0 ? 'border-primary' : 'border-gray-200'">
                                <img src="https://img.youtube.com/vi/<?php echo esc_attr($video_id); ?>/maxresdefault.jpg" 
                                     alt="Miniatura filmu <?php echo esc_attr(get_the_title()); ?>" 
                                     class="w-full h-full object-cover" 
                                     @error="$el.src='https://img.youtube.com/vi/<?php echo esc_attr($video_id); ?>/hqdefault.jpg'" 
                                     loading="lazy"
                                     width="150"
                                     height="100"
                                     style="aspect-ratio: 3/2;">
                                <div class="youtube-thumb-overlay" aria-hidden="true">
                                    <svg class="youtube-play-button" viewBox="0 0 68 48" focusable="false">
                                        <path data-part="bg" d="M66.52 7.02a8 8 0 00-5.65-5.66C56.08 0 34 0 34 0S11.92 0 7.13 1.36a8 8 0 00-5.65 5.66C0 11.81 0 24 0 24s0 12.19 1.48 16.98a8 8 0 005.65 5.66C11.92 48 34 48 34 48s22.08 0 26.87-1.36a8 8 0 005.65-5.66C68 36.19 68 24 68 24s0-12.19-1.48-16.98z" />
                                        <path data-part="triangle" d="M45 24L27 14v20z" />
                                    </svg>
                                </div>
                            </button>
                            <?php endif; ?>
                            
                            <?php 
                            $img_display_idx = 1;
                            foreach ($gallery as $idx => $item) {
                                if ($item['type'] === 'image') {
                            ?>
                            <button data-index="<?php echo $img_display_idx; ?>" 
                                    @click="currentImage = <?php echo $img_display_idx; ?>; scrollThumbIntoView()" 
                                    class="rounded-lg overflow-hidden border-2 transition-colors" 
                                    :class="currentImage === <?php echo $img_display_idx; ?> ? 'border-primary' : 'border-gray-200'">
                                <img src="<?php echo esc_url($item['thumbnail']); ?>" 
                                     alt="<?php echo esc_attr($item['alt']); ?>" 
                                     class="w-full h-full object-cover" 
                                     loading="lazy"
                                     width="150"
                                     height="100"
                                     style="aspect-ratio: 3/2;">
                            </button>
                            <?php 
                                    $img_display_idx++;
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Lightbox -->
                <template x-teleport="body">
                    <div x-show="lightboxOpen" 
                         x-cloak 
                         x-transition.opacity 
                         class="fixed inset-0 bg-black/95 flex flex-col gap-6 px-4 items-center justify-center" 
                         style="z-index: 99999; padding-top: 80px; padding-bottom: 40px; backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);" 
                         @click.self="closeLightbox()">
                        <div class="relative w-full max-w-4xl mx-auto">
                            <button type="button" 
                                    class="absolute top-4 left-4 text-white hover:text-white/80 transition z-10 bg-black/30 rounded-full p-2 backdrop-blur-sm lightbox-close-btn" 
                                    @click="closeLightbox()" 
                                    aria-label="Zamknij podgląd">
                                <svg class="w-8 h-8 lightbox-close-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <div class="relative bg-black rounded-2xl overflow-hidden shadow-2xl flex items-center justify-center w-full aspect-[4/3] max-h-[80vh]">
                                <?php if ($video_url && $video_id) : ?>
                                <template x-if="currentImage === 0">
                                    <iframe width="100%" height="100%" 
                                            src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>?rel=0" 
                                            title="<?php echo esc_attr(get_the_title()); ?> - Film prezentacyjny" 
                                            frameborder="0" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                            allowfullscreen 
                                            class="absolute inset-0 w-full h-full"></iframe>
                                </template>
                                <?php endif; ?>
                                
                                <?php 
                                $img_display_idx = 1;
                                foreach ($gallery as $idx => $item) {
                                    if ($item['type'] === 'image') {
                                ?>
                                <template x-if="currentImage === <?php echo $img_display_idx; ?>">
                                    <img :src="'<?php echo esc_url($item['url']); ?>'" 
                                         :alt="'<?php echo esc_attr($item['alt']); ?>'" 
                                         class="car-image absolute inset-0 w-full h-full object-cover"
                                         width="1920"
                                         height="1080"
                                         style="aspect-ratio: 16/9;" />
                                </template>
                                <?php 
                                        $img_display_idx++;
                                    }
                                }
                                ?>
                                
                                <div class="gallery-counter">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7a2 2 0 012-2h2l1-1h6l1 1h2a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V7z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 11l3 3 3-3"></path>
                                    </svg>
                                    <span x-text="hasVideo ? (currentImage === 0 ? 1 : currentImage) : currentImage"></span>/<span x-text="hasVideo ? images.length + 1 : images.length"></span>
                                </div>
                                
                                <button type="button" @click.stop="showPrev()" class="gallery-nav gallery-nav-left" aria-label="Poprzednie zdjęcie">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <button type="button" @click.stop="showNext()" class="gallery-nav gallery-nav-right" aria-label="Następne zdjęcie">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="w-full max-w-4xl mx-auto">
                            <div class="p-2 sm:p-4">
                                <div class="thumb-strip" x-ref="thumbStripLightbox">
                                    <?php if ($video_url) : 
                                        $video_id = basename(parse_url($video_url, PHP_URL_PATH));
                                    ?>
                                    <button data-index="0" 
                                            @click="currentImage = 0; scrollThumbIntoView(true)" 
                                            class="rounded-lg overflow-hidden border-2 transition-colors relative youtube-thumb" 
                                            :class="currentImage === 0 ? 'border-primary' : 'border-gray-200'">
                                        <img src="https://img.youtube.com/vi/<?php echo esc_attr($video_id); ?>/maxresdefault.jpg" 
                                             alt="Miniatura filmu <?php echo esc_attr(get_the_title()); ?>" 
                                             class="w-full h-full object-cover" 
                                             @error="$el.src='https://img.youtube.com/vi/<?php echo esc_attr($video_id); ?>/hqdefault.jpg'" 
                                             loading="lazy">
                                        <div class="youtube-thumb-overlay" aria-hidden="true">
                                            <svg class="youtube-play-button" viewBox="0 0 68 48" focusable="false">
                                                <path data-part="bg" d="M66.52 7.02a8 8 0 00-5.65-5.66C56.08 0 34 0 34 0S11.92 0 7.13 1.36a8 8 0 00-5.65 5.66C0 11.81 0 24 0 24s0 12.19 1.48 16.98a8 8 0 005.65 5.66C11.92 48 34 48 34 48s22.08 0 26.87-1.36a8 8 0 005.65-5.66C68 36.19 68 24 68 24s0-12.19-1.48-16.98z" />
                                                <path data-part="triangle" d="M45 24L27 14v20z" />
                                            </svg>
                                        </div>
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    $img_display_idx = 1;
                                    foreach ($gallery as $idx => $item) {
                                        if ($item['type'] === 'image') {
                                    ?>
                                    <button data-index="<?php echo $img_display_idx; ?>" 
                                            @click="currentImage = <?php echo $img_display_idx; ?>; scrollThumbIntoView(true)" 
                                            class="rounded-lg overflow-hidden border-2 transition-colors" 
                                            :class="currentImage === <?php echo $img_display_idx; ?> ? 'border-primary' : 'border-gray-200'">
                                        <img src="<?php echo esc_url($item['thumbnail']); ?>" 
                                             alt="<?php echo esc_attr($item['alt']); ?>" 
                                             class="w-full h-full object-cover" 
                                             loading="lazy" />
                                    </button>
                                    <?php 
                                            $img_display_idx++;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Key Facts -->
                <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 sm:p-10 lg:p-12">
                    <h2 class="text-2xl sm:text-3xl font-bold text-primary mb-8">Najważniejsze</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm text-gray-700">
                        <?php if ($mileage) : ?>
                        <div class="flex justify-between border-b border-gray-100 pb-4">
                            <dt>Przebieg</dt>
                            <dd class="font-semibold text-gray-900"><?php echo number_format($mileage, 0, ',', ' '); ?> km</dd>
                        </div>
                        <?php endif; ?>
                        <?php if ($fuel) : ?>
                        <div class="flex justify-between border-b border-gray-100 pb-4">
                            <dt>Rodzaj paliwa</dt>
                            <dd class="font-semibold text-gray-900"><?php echo esc_html($fuel); ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if ($gearbox) : ?>
                        <div class="flex justify-between border-b border-gray-100 pb-4">
                            <dt>Skrzynia biegów</dt>
                            <dd class="font-semibold text-gray-900"><?php echo esc_html($gearbox); ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if ($body_type) : ?>
                        <div class="flex justify-between border-b border-gray-100 pb-4">
                            <dt>Typ nadwozia</dt>
                            <dd class="font-semibold text-gray-900"><?php echo esc_html($body_type); ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if ($engine_cc) : ?>
                        <div class="flex justify-between border-b border-gray-100 pb-4">
                            <dt>Pojemność skokowa</dt>
                            <dd class="font-semibold text-gray-900"><?php echo number_format($engine_cc, 0, ',', ' '); ?> cm3</dd>
                        </div>
                        <?php endif; ?>
                        <?php if ($power_hp) : ?>
                        <div class="flex justify-between pb-1">
                            <dt>Moc</dt>
                            <dd class="font-semibold text-gray-900"><?php echo esc_html($power_hp); ?> KM</dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                </section>
                
                <!-- Description -->
                <?php if (!empty($description)) : ?>
                <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 sm:p-10 lg:p-12" x-data="{ expanded: false }">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 border-b border-gray-100 pb-6">
                        <h2 class="text-2xl sm:text-3xl font-bold text-primary">Opis pojazdu</h2>
                        <button x-show="!expanded" @click="expanded = true" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition-colors">
                            Pokaż pełny opis
                        </button>
                    </div>
                    <div class="space-y-6 text-gray-700 leading-relaxed">
                        <?php 
                        $paragraphs = explode("\n", $description);
                        $paragraphs = array_filter(array_map('trim', $paragraphs));
                        $first_paragraphs = array_slice($paragraphs, 0, 2);
                        $rest_paragraphs = array_slice($paragraphs, 2);
                        ?>
                        <div class="space-y-4">
                            <?php foreach ($first_paragraphs as $p) : ?>
                            <p><?php echo esc_html($p); ?></p>
                            <?php endforeach; ?>
                        </div>
                        <?php if (!empty($rest_paragraphs)) : ?>
                        <div class="space-y-4" x-show="expanded" x-transition>
                            <?php foreach ($rest_paragraphs as $p) : ?>
                            <p><?php echo esc_html($p); ?></p>
                            <?php endforeach; ?>
                            <button @click="expanded = false" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:border-primary hover:text-primary transition-colors">
                                Ukryj pełny opis
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php endif; ?>
                
                <!-- Details -->
                <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 sm:p-10 lg:p-12">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 border-b border-gray-100 pb-6">
                        <h2 class="text-2xl sm:text-3xl font-bold text-primary">Szczegóły</h2>
                    </div>
                    <div class="space-y-6">
                        <details class="border border-gray-200 rounded-xl bg-gray-50">
                            <summary class="flex items-center justify-between px-4 py-3 cursor-pointer select-none text-sm font-semibold text-gray-900">
                                <span>Podstawowe</span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"></path>
                                </svg>
                            </summary>
                            <div class="px-4 pb-4">
                                <dl class="space-y-3 text-sm text-gray-700">
                                    <?php if ($brand) : ?>
                                    <div class="flex justify-between">
                                        <dt>Marka pojazdu</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($brand); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($model) : ?>
                                    <div class="flex justify-between">
                                        <dt>Model pojazdu</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($model); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($color) : ?>
                                    <div class="flex justify-between">
                                        <dt>Kolor</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($color); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                    <div class="flex justify-between">
                                        <dt>Liczba drzwi</dt>
                                        <dd class="font-semibold text-gray-900">5</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt>Liczba miejsc</dt>
                                        <dd class="font-semibold text-gray-900">5</dd>
                                    </div>
                                    <?php if ($year) : ?>
                                    <div class="flex justify-between">
                                        <dt>Rok produkcji</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($year); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($vin_masked) : ?>
                                    <div class="flex justify-between">
                                        <dt>VIN</dt>
                                        <dd class="font-semibold text-gray-900 font-mono"><?php echo esc_html($vin_masked); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                </dl>
                            </div>
                        </details>
                        
                        <details class="border border-gray-200 rounded-xl bg-gray-50">
                            <summary class="flex items-center justify-between px-4 py-3 cursor-pointer select-none text-sm font-semibold text-gray-900">
                                <span>Specyfikacja</span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"></path>
                                </svg>
                            </summary>
                            <div class="px-4 pb-4">
                                <dl class="space-y-3 text-sm text-gray-700">
                                    <?php if ($fuel) : ?>
                                    <div class="flex justify-between">
                                        <dt>Rodzaj paliwa</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($fuel); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($engine_cc) : ?>
                                    <div class="flex justify-between">
                                        <dt>Pojemność skokowa</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo number_format($engine_cc, 0, ',', ' '); ?> cm3</dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($power_hp) : ?>
                                    <div class="flex justify-between">
                                        <dt>Moc</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($power_hp); ?> KM</dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($body_type) : ?>
                                    <div class="flex justify-between">
                                        <dt>Typ nadwozia</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($body_type); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($color) : ?>
                                    <div class="flex justify-between">
                                        <dt>Rodzaj koloru</dt>
                                        <dd class="font-semibold text-gray-900">Metalik</dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($gearbox) : ?>
                                    <div class="flex justify-between">
                                        <dt>Skrzynia biegów</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($gearbox); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($drivetrain) : ?>
                                    <div class="flex justify-between">
                                        <dt>Napęd</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($drivetrain); ?> (stały)</dd>
                                    </div>
                                    <?php endif; ?>
                                </dl>
                            </div>
                        </details>
                        
                        <details class="border border-gray-200 rounded-xl bg-gray-50">
                            <summary class="flex items-center justify-between px-4 py-3 cursor-pointer select-none text-sm font-semibold text-gray-900">
                                <span>Stan i historia</span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"></path>
                                </svg>
                            </summary>
                            <div class="px-4 pb-4">
                                <dl class="space-y-3 text-sm text-gray-700">
                                    <?php if ($origin) : ?>
                                    <div class="flex justify-between">
                                        <dt>Kraj pochodzenia</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($origin === 'PL' ? 'Polska' : $origin); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($mileage) : ?>
                                    <div class="flex justify-between">
                                        <dt>Przebieg</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo number_format($mileage, 0, ',', ' '); ?> km</dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php 
                                    // Okres gwarancji - wyciągnij z service_history jeśli zawiera datę
                                    $warranty_date = '';
                                    $warranty_km = '';
                                    if ($service_history) {
                                        // Format: "Gwarancja Audi do 13.06.2027"
                                        if (preg_match('/Gwarancja.*?do\s+(\d{2})\.(\d{2})\.(\d{4})/i', $service_history, $matches)) {
                                            $warranty_date = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
                                        }
                                        // Format: "120 000 km" lub "120000 km"
                                        if (preg_match('/(\d+\s*\d*)\s*km/i', $service_history, $matches)) {
                                            $warranty_km = number_format(intval(str_replace(' ', '', $matches[1])), 0, ',', ' ');
                                        }
                                    }
                                    ?>
                                    <?php if ($warranty_date) : ?>
                                    <div class="flex justify-between">
                                        <dt>Okres gwarancji producenta</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($warranty_date); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($warranty_km) : ?>
                                    <div class="flex justify-between">
                                        <dt>lub do (przebieg km)</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($warranty_km); ?> km</dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php 
                                    $registration_number = salon_auto_get_car_field($post_id, 'registration_number');
                                    if (empty($registration_number)) {
                                        // Fallback - spróbuj wyciągnąć z innych pól
                                        $registration_number = '';
                                    }
                                    ?>
                                    <?php if ($registration_number) : ?>
                                    <div class="flex justify-between">
                                        <dt>Numer rejestracyjny pojazdu</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($registration_number); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                    <div class="flex justify-between">
                                        <dt>Stan</dt>
                                        <dd class="font-semibold text-gray-900">Używany</dd>
                                    </div>
                                    <?php 
                                    $first_registration = salon_auto_get_car_field($post_id, 'first_registration');
                                    if (empty($first_registration) && $year) {
                                        // Fallback - użyj roku jako daty
                                        $first_registration = $year;
                                    }
                                    ?>
                                    <?php if ($first_registration) : ?>
                                    <div class="flex justify-between">
                                        <dt>Data pierwszej rejestracji w historii pojazdu</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($first_registration); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($origin === 'PL') : ?>
                                    <div class="flex justify-between">
                                        <dt>Zarejestrowany w Polsce</dt>
                                        <dd class="font-semibold text-gray-900">Tak</dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($owners == 1) : ?>
                                    <div class="flex justify-between">
                                        <dt>Pierwszy właściciel (od nowości)</dt>
                                        <dd class="font-semibold text-gray-900">Tak</dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($registration_number) : ?>
                                    <div class="flex justify-between">
                                        <dt>Ma numer rejestracyjny</dt>
                                        <dd class="font-semibold text-gray-900">Tak</dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($service_history && (stripos($service_history, 'ASO') !== false || stripos($service_history, 'serwis') !== false)) : ?>
                                    <div class="flex justify-between">
                                        <dt>Serwisowany w ASO</dt>
                                        <dd class="font-semibold text-gray-900">Tak</dd>
                                    </div>
                                    <?php endif; ?>
                                </dl>
                            </div>
                        </details>
                        
                        <?php if ($price || $lease_from_pln || $lease_cession || $lease_capital || $lease_down_payment || true) : ?>
                        <details class="border border-gray-200 rounded-xl bg-gray-50">
                            <summary class="flex items-center justify-between px-4 py-3 cursor-pointer select-none text-sm font-semibold text-gray-900">
                                <span>Finansowanie i koszty</span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"></path>
                                </svg>
                            </summary>
                            <div class="px-4 pb-4">
                                <dl class="space-y-3 text-sm text-gray-700">
                                    <?php if ($price) : ?>
                                    <div class="flex justify-between">
                                        <dt>Cena</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo salon_auto_format_price($price); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($lease_from_pln) : ?>
                                    <div class="flex justify-between">
                                        <dt>Leasing od</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo number_format((float)$lease_from_pln, 0, ',', ' '); ?> PLN/miesiąc</dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($lease_cession) : ?>
                                    <div class="flex justify-between">
                                        <dt>Cesja leasingu dla firm</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($lease_cession); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($lease_capital) : ?>
                                    <div class="flex justify-between">
                                        <dt>Kapitał do spłaty z wykupem</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($lease_capital); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($lease_down_payment) : ?>
                                    <div class="flex justify-between">
                                        <dt>Odstępne</dt>
                                        <dd class="font-semibold text-gray-900"><?php echo esc_html($lease_down_payment); ?></dd>
                                    </div>
                                    <?php endif; ?>
                                </dl>
                            </div>
                        </details>
                        <?php endif; ?>
                    </div>
                </section>
                
                <!-- Equipment -->
                <?php 
                $has_equipment = !empty($equipment_audio) || !empty($equipment_comfort) || !empty($equipment_assistance) || !empty($equipment_performance) || !empty($equipment_safety);
                if ($has_equipment) : 
                ?>
                <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 sm:p-10 lg:p-12">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 border-b-2 border-gray-100 pb-6">
                        <h2 class="text-2xl sm:text-3xl font-bold text-primary">Wyposażenie</h2>
                    </div>
                    <div class="space-y-6">
                        <?php if (!empty($equipment_audio)) : 
                            $audio_list = is_array($equipment_audio) ? $equipment_audio : explode("\n", $equipment_audio);
                            $audio_list = array_filter(array_map('trim', $audio_list));
                        ?>
                        <details class="border border-gray-200 rounded-xl bg-gray-50">
                            <summary class="flex items-center justify-between px-4 py-3 cursor-pointer select-none text-sm font-semibold text-gray-900">
                                <span>Audio i multimedia</span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"></path></svg>
                            </summary>
                            <div class="px-4 pb-4">
                                <ul class="details-icon-list text-sm text-gray-700">
                                    <?php foreach ($audio_list as $item) : ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?>
                                </ul>
                            </div>
                        </details>
                        <?php endif; ?>
                        
                        <?php if (!empty($equipment_comfort)) : 
                            $comfort_list = is_array($equipment_comfort) ? $equipment_comfort : explode("\n", $equipment_comfort);
                            $comfort_list = array_filter(array_map('trim', $comfort_list));
                        ?>
                        <details class="border border-gray-200 rounded-xl bg-gray-50">
                            <summary class="flex items-center justify-between px-4 py-3 cursor-pointer select-none text-sm font-semibold text-gray-900">
                                <span>Komfort i dodatki</span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"></path></svg>
                            </summary>
                            <div class="px-4 pb-4">
                                <ul class="details-icon-list text-sm text-gray-700">
                                    <?php foreach ($comfort_list as $item) : ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?>
                                </ul>
                    </div>
                        </details>
                <?php endif; ?>
                        
                        <?php if (!empty($equipment_assistance)) : 
                            $assist_list = is_array($equipment_assistance) ? $equipment_assistance : explode("\n", $equipment_assistance);
                            $assist_list = array_filter(array_map('trim', $assist_list));
                        ?>
                        <details class="border border-gray-200 rounded-xl bg-gray-50">
                            <summary class="flex items-center justify-between px-4 py-3 cursor-pointer select-none text-sm font-semibold text-gray-900">
                                <span>Systemy wspomagania kierowcy</span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"></path></svg>
                            </summary>
                            <div class="px-4 pb-4">
                                <ul class="details-icon-list text-sm text-gray-700">
                                    <?php foreach ($assist_list as $item) : ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?>
                                </ul>
                            </div>
                        </details>
                <?php endif; ?>
                        
                        <?php if (!empty($equipment_performance)) : 
                            $perf_list = is_array($equipment_performance) ? $equipment_performance : explode("\n", $equipment_performance);
                            $perf_list = array_filter(array_map('trim', $perf_list));
                        ?>
                        <details class="border border-gray-200 rounded-xl bg-gray-50">
                            <summary class="flex items-center justify-between px-4 py-3 cursor-pointer select-none text-sm font-semibold text-gray-900">
                                <span>Osiągi i tuning</span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"></path></svg>
                            </summary>
                            <div class="px-4 pb-4">
                                <ul class="details-icon-list text-sm text-gray-700">
                                    <?php foreach ($perf_list as $item) : ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?>
                                </ul>
                            </div>
                        </details>
                        <?php endif; ?>
                        
                        <?php if (!empty($equipment_safety)) : 
                            $safety_list = is_array($equipment_safety) ? $equipment_safety : explode("\n", $equipment_safety);
                            $safety_list = array_filter(array_map('trim', $safety_list));
                        ?>
                        <details class="border border-gray-200 rounded-xl bg-gray-50">
                            <summary class="flex items-center justify-between px-4 py-3 cursor-pointer select-none text-sm font-semibold text-gray-900">
                                <span>Bezpieczeństwo</span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"></path></svg>
                            </summary>
                            <div class="px-4 pb-4">
                                <ul class="details-icon-list text-sm text-gray-700">
                                    <?php foreach ($safety_list as $item) : ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?>
                                </ul>
                            </div>
                        </details>
                        <?php endif; ?>
                    </div>
                </section>
                <?php endif; ?>
                
                <!-- Location - zawsze pokazuj -->
                <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 sm:p-10 lg:p-12">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8 border-b-2 border-gray-100 pb-6">
                        <div>
                            <h2 class="text-2xl sm:text-3xl font-bold text-primary">Lokalizacja pojazdu</h2>
                            <p class="text-sm text-gray-500"><?php echo esc_html($car_location); ?></p>
                        </div>
                        <a href="https://www.google.com/maps/search/?api=1&query=53.70573%2C16.69825" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition-colors">
                            <span>Otwórz trasę</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <div class="aspect-video rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                        <iframe src="https://maps.google.com/maps?q=53.70573,16.69825&z=14&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full"></iframe>
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&query=53.70573%2C16.69825" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm font-semibold text-primary mt-4">
                        Zobacz w Mapach Google
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </section>
                
                <!-- Brochure - zawsze pokazuj -->
                <?php 
                // Broszura z folderu motywu /reports/
                $brochure_display_url = '';
                if (!empty($brochure_url)) {
                    // Jeśli to nazwa pliku - dodaj ścieżkę motywu
                    if (strpos($brochure_url, '/') === false) {
                        $brochure_display_url = get_template_directory_uri() . '/reports/' . $brochure_url;
                    } else {
                        $brochure_display_url = $brochure_url;
                    }
                } else {
                    // Generuj domyślny URL broszury
                    $brand_slug = sanitize_title($brand ?: 'auto');
                    $model_slug = sanitize_title($model ?: 'samochod');
                    $brochure_display_url = get_template_directory_uri() . '/reports/' . $brand_slug . '-' . $model_slug . '-broszura.html';
                }
                ?>
                <div class="bg-primary rounded-xl shadow-lg p-10 lg:p-12 border-2 border-gray-800">
                    <div class="flex items-start space-x-4">
                        <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold mb-3" style="color: #e5e7eb !important;">Broszura pojazdu</h3>
                            <p class="text-gray-300 mb-6 leading-relaxed">
                                Pobierz szczegółową broszurę z pełną specyfikacją, zdjęciami i opisem tego pojazdu.
                            </p>
                            <a href="<?php echo esc_url($brochure_display_url); ?>" target="_blank" rel="noopener" class="inline-flex items-center justify-center font-semibold transition-all duration-200 bg-white text-primary hover:bg-gray-50 px-6 py-3 text-base rounded-xl shadow-lg">
                                Pobierz broszurę PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Sticky Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6 sticky-lightbox-hide">
                    <!-- Price Card -->
                    <div class="bg-white rounded-xl shadow-sm p-8 border border-gray-200">
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1.5 rounded-full text-xs font-bold <?php echo esc_attr($current_status['bg_class']); ?> <?php echo esc_attr($current_status['text_class']); ?> shadow-lg uppercase tracking-wide">
                                    <?php echo esc_html($current_status['text']); ?>
                                </span>
                            </div>
                            <h1 class="text-2xl font-bold text-primary mb-2"><?php the_title(); ?></h1>
                            <p class="text-gray-500 text-sm mb-1">Używany<?php if ($year) : ?> · <?php echo esc_html($year); ?><?php endif; ?></p>
                            <?php if ($trim) : ?>
                            <p class="text-gray-600"><?php echo esc_html($trim); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="pt-6 mb-6">
                            <div class="text-3xl font-bold text-primary mb-2"><?php echo salon_auto_format_price($price); ?></div>
                        </div>
                        <?php
                        // URL do strony kontakt - bez parametrów (używamy localStorage)
                        $contact_page_url = function_exists('salon_auto_get_contact_url') 
                            ? salon_auto_get_contact_url() 
                            : home_url('/kontakt/');
                        
                        // Upewnij się że URL nie ma już parametrów query
                        $contact_page_url = strtok($contact_page_url, '?');
                        
                        // Sprawdź czy URL jest poprawny (nie jest pusty)
                        if (empty($contact_page_url) || $contact_page_url === home_url('/')) {
                            $contact_page_url = home_url('/kontakt/');
                        }
                        
                        // Używamy localStorage zamiast parametrów URL - bardziej niezawodne
                        $napisz_url = esc_url($contact_page_url);
                        $rezerwacja_url = esc_url($contact_page_url);
                        ?>
                        
                        <!-- JavaScript do przekazania danych przez localStorage -->
                        <script>
                        (function() {
                            'use strict';
                            
                            const carData = {
                                name: <?php echo json_encode($car_name, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
                                action: null
                            };
                            
                            const contactUrl = <?php echo json_encode($contact_page_url, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
                            
                            // Funkcja do przekazania danych i przekierowania
                            function goToContact(action) {
                                try {
                                    carData.action = action;
                                    localStorage.setItem('salon_auto_contact_data', JSON.stringify(carData));
                                    window.location.href = contactUrl;
                                } catch(e) {
                                    console.error('Błąd zapisu do localStorage:', e);
                                    // Fallback: użyj parametrów URL
                                    const separator = contactUrl.indexOf('?') !== -1 ? '&' : '?';
                                    const carParam = encodeURIComponent(carData.name);
                                    window.location.href = contactUrl + separator + 'action=' + action + '&car=' + carParam;
                                }
                            }
                            
                            // Przypisz funkcje do przycisków - działa od razu i po załadowaniu DOM
                            function attachHandlers() {
                                const napiszButtons = document.querySelectorAll('[data-action="napisz"]');
                                const rezerwacjaButtons = document.querySelectorAll('[data-action="rezerwacja"]');
                                
                                napiszButtons.forEach(function(btn) {
                                    // Usuń stare handlery jeśli istnieją
                                    btn.removeEventListener('click', btn._napiszHandler);
                                    // Dodaj nowy handler
                                    btn._napiszHandler = function(e) {
                                        e.preventDefault();
                                        goToContact('napisz');
                                    };
                                    btn.addEventListener('click', btn._napiszHandler);
                                });
                                
                                rezerwacjaButtons.forEach(function(btn) {
                                    // Usuń stare handlery jeśli istnieją
                                    btn.removeEventListener('click', btn._rezerwacjaHandler);
                                    // Dodaj nowy handler
                                    btn._rezerwacjaHandler = function(e) {
                                        e.preventDefault();
                                        goToContact('rezerwacja');
                                    };
                                    btn.addEventListener('click', btn._rezerwacjaHandler);
                                });
                            }
                            
                            // Uruchom od razu (jeśli DOM już załadowany)
                            if (document.readyState === 'loading') {
                                document.addEventListener('DOMContentLoaded', attachHandlers);
                            } else {
                                attachHandlers();
                            }
                        })();
                        </script>
                        
                        <?php if ($car_status === 'sold') : ?>
                        <!-- SPRZEDANY - pokazuje tylko informację (ciemne tło, biały tekst) -->
                        <div class="bg-gray-900 border-2 border-gray-700 rounded-xl p-6 text-center">
                            <svg class="w-12 h-12 mx-auto mb-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h4 class="font-bold text-white text-lg mb-2">Ten samochód został sprzedany</h4>
                            <p class="text-gray-300 text-sm mb-4">Sprawdź nasze inne dostępne oferty</p>
                            <a href="<?php echo esc_url(get_post_type_archive_link('car')); ?>" class="inline-flex items-center justify-center font-semibold bg-white text-gray-900 hover:bg-gray-100 px-6 py-3 rounded-lg">
                                Zobacz inne samochody →
                            </a>
                        </div>
                        
                        <?php elseif ($car_status === 'reserved') : ?>
                        <!-- ZAREZERWOWANY - pokazuje info i kontakt -->
                        <div class="bg-amber-50 border-2 border-amber-200 rounded-xl p-6 text-center mb-4">
                            <svg class="w-10 h-10 mx-auto mb-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h4 class="font-bold text-amber-700 text-lg mb-1">Samochód zarezerwowany</h4>
                            <p class="text-amber-600 text-sm">Zadzwoń aby zapytać o dostępność</p>
                        </div>
                        <div class="space-y-4">
                            <a href="tel:+48<?php echo esc_attr($phone_clean); ?>" class="inline-flex items-center justify-center font-semibold transition-all duration-200 bg-primary text-white hover:bg-primary/90 px-6 py-3 text-base rounded-lg h-14 w-full shadow-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                Zadzwoń i zapytaj
                            </a>
                            <a href="<?php echo esc_url($napisz_url); ?>" data-action="napisz" class="inline-flex items-center justify-center font-semibold transition-all duration-200 border-2 border-gray-300 text-gray-700 hover:border-primary hover:text-primary px-6 py-3 text-base rounded-lg h-14 w-full">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Napisz wiadomość
                            </a>
                        </div>
                        
                        <?php else : ?>
                        <!-- DOSTĘPNY - pełne opcje -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <a href="tel:+48<?php echo esc_attr($phone_clean); ?>" class="inline-flex items-center justify-center font-semibold transition-all duration-200 bg-primary text-white hover:bg-primary/90 px-6 py-3 text-base rounded-lg h-14 w-full shadow-sm">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    Zadzwoń
                                </a>
                                <a href="<?php echo esc_url($napisz_url); ?>" data-action="napisz" class="inline-flex items-center justify-center font-semibold transition-all duration-200 border-2 border-gray-300 text-gray-700 hover:border-primary hover:text-primary px-6 py-3 text-base rounded-lg h-14 w-full">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Napisz
                                </a>
                            </div>
                            <a href="<?php echo esc_url($rezerwacja_url); ?>" data-action="rezerwacja" class="inline-flex items-center justify-center font-semibold transition-all duration-200 bg-accent text-white hover:bg-accent/90 px-6 py-3 text-base rounded-lg h-14 w-full shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                Zarezerwuj
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Contact Info -->
                    <div class="bg-gray-50 rounded-xl p-8 border border-gray-200">
                        <h3 class="font-bold text-primary mb-5 text-lg">KONTAKT</h3>
                        <div class="space-y-4 text-sm">
                            <a href="tel:+48<?php echo esc_attr($phone_clean); ?>" class="flex items-center space-x-3 text-gray-700 hover:text-accent transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span><?php echo esc_html($phone); ?></span>
                            </a>
                            <a href="mailto:<?php echo esc_attr($email); ?>" class="flex items-center space-x-3 text-gray-700 hover:text-accent transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span><?php echo esc_html($email); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
<!-- Podobne oferty -->
<section class="py-12 sm:py-16 md:py-20 lg:py-24 bg-gray-50 border-t border-gray-200 px-4 sm:px-6">
            <div class="container mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary mb-3 sm:mb-4">Podobne oferty</h2>
                    <div class="w-20 h-1 bg-primary mx-auto"></div>
                </div>
<div id="similar-cars-container" data-car-slug="<?php echo esc_attr($post_slug); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 md:gap-10 max-w-6xl mx-auto">
                    <!-- Podobne oferty będą załadowane automatycznie przez load-similar-cars.js -->
                </div>
                <div class="text-center mt-16">
<a href="<?php echo esc_url(get_post_type_archive_link('car')); ?>" class="inline-flex items-center justify-center font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 border-2 border-primary text-primary hover:bg-primary hover:text-white focus:ring-accent px-8 py-4 text-lg rounded-xl transform hover:scale-[1.01] transition-all">
                        Zobacz wszystkie samochody →
                    </a>
                </div>
            </div>
        </section>
    </div>

<!-- Gallery State Script - MUST be before Alpine.js -->
<script>
// Definiujemy galleryState jako globalną funkcję PRZED Alpine.js
    <?php 
    // Liczymy tylko zdjęcia (bez wideo)
    $image_count = 0;
    foreach ($gallery as $item) {
        if ($item['type'] === 'image') {
            $image_count++;
        }
    }
?>
window.galleryState = function() {
  const images = [];
  <?php 
    for ($i = 1; $i <= $image_count; $i++) {
      echo "images.push($i);\n  ";
    }
    ?>
    
    const hasVideo = <?php echo $video_url ? 'true' : 'false'; ?>;
    
    return {
      currentImage: <?php echo $video_url ? 0 : ($image_count > 0 ? 1 : 0); ?>,
      lightboxOpen: false,
      images: images,
      hasVideo: hasVideo,
      
      init() {
        this.$watch('currentImage', () => {
          this.scrollThumbIntoView();
        });
      },
      
      showNext() {
        if (this.currentImage < this.images.length) {
          this.currentImage++;
        } else {
          this.currentImage = this.hasVideo ? 0 : 1;
        }
        this.scrollThumbIntoView();
      },
      
      showPrev() {
        const minIndex = this.hasVideo ? 0 : 1;
        if (this.currentImage > minIndex) {
          this.currentImage--;
        } else {
          this.currentImage = this.images.length;
        }
        this.scrollThumbIntoView();
      },
      
      openLightbox() {
        this.lightboxOpen = true;
        document.body.style.overflow = 'hidden';
        document.body.classList.add('lightbox-open');
        this.$nextTick(() => {
          this.scrollThumbIntoView(true);
        });
      },
      
      closeLightbox() {
        this.lightboxOpen = false;
        document.body.style.overflow = '';
        document.body.classList.remove('lightbox-open');
      },
      
      scrollThumbIntoView(isLightbox = false) {
        this.$nextTick(() => {
          const stripRef = isLightbox ? this.$refs.thumbStripLightbox : this.$refs.thumbStrip;
          if (!stripRef) return;
          const activeButton = stripRef.querySelector(`button[data-index="${this.currentImage}"]`);
          if (activeButton) {
            activeButton.scrollIntoView({
              behavior: 'smooth',
              block: 'nearest',
              inline: 'center'
            });
          }
        });
      },
      
      handleKeydown(event) {
        if (!this.lightboxOpen) return;
        if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
          event.preventDefault();
          this.showNext();
        } else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
          event.preventDefault();
          this.showPrev();
        } else if (event.key === 'Escape') {
          event.preventDefault();
          this.closeLightbox();
        }
      }
    };
};

// Rejestruj również dla Alpine.data (jeśli Alpine załaduje się później)
document.addEventListener('alpine:init', () => {
  if (typeof Alpine !== 'undefined' && Alpine.data) {
    Alpine.data('galleryState', window.galleryState);
  }
});
</script>

<?php
// Schema.org markup dla samochodu (Product schema) - SEO
if ($brand && $model && $price) {
    $car_name = $brand . ' ' . $model . ($year ? ' ' . $year : '');
    $car_url = get_permalink();
    $car_image = has_post_thumbnail() ? get_the_post_thumbnail_url($post_id, 'large') : '';
    ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Product",
      "name": "<?php echo esc_js($car_name); ?>",
      "description": "<?php echo esc_js($description ? wp_strip_all_tags($description) : $car_name . ' - Sprawdzone auto premium'); ?>",
      "url": "<?php echo esc_js($car_url); ?>",
      <?php if ($car_image) : ?>
      "image": "<?php echo esc_js($car_image); ?>",
      <?php endif; ?>
      "brand": {
        "@type": "Brand",
        "name": "<?php echo esc_js($brand); ?>"
      },
      "offers": {
        "@type": "Offer",
        "price": "<?php echo esc_js($price); ?>",
        "priceCurrency": "PLN",
        "availability": "https://schema.org/<?php echo ($car_status === 'available') ? 'InStock' : 'OutOfStock'; ?>",
        "url": "<?php echo esc_js($car_url); ?>"
      },
      <?php if ($year) : ?>
      "productionDate": "<?php echo esc_js($year); ?>",
      <?php endif; ?>
      "vehicleIdentificationNumber": "<?php echo esc_js($vin_masked ?: ''); ?>"
    }
    </script>
    <?php
}
endwhile;
get_footer();
?>
