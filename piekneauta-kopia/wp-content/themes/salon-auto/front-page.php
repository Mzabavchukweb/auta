<?php
/**
 * Template Name: Front Page
 * 
 * Strona główna - wyświetla hero, dostępne samochody, sekcje "Dlaczego my", opinie
 */

get_header();
?>

<!-- Hero Section -->
<section class="relative hero-custom-bg overflow-hidden fade-in-up">
    <div class="container mx-auto relative z-10">
        <div class="grid lg:grid-cols-2 gap-8 sm:gap-12 md:gap-16 items-start py-12 sm:py-16 md:py-20 lg:py-24 xl:py-40">
            <!-- Content -->
            <div class="space-y-8">
                <div class="space-y-6 fade-in-up stagger-1 text-center lg:text-left">
                    <?php
                    $hero_title = salon_auto_get_option('hero_title', 'Sprawdzone samochody premium i kompleksowa usługa leasingowa.');
                    ?>
                    <h1 class="font-bold text-primary mb-6 tracking-tight hero-title-mobile">
                        <?php echo esc_html($hero_title); ?>
                    </h1>
                    <style>
                    h1.hero-title-mobile {
                      font-size: 48px !important;
                      line-height: 1.2 !important;
                    }
                    @media (max-width: 1024px) {
                      h1.hero-title-mobile {
                        font-size: 46px !important;
                        line-height: 1.2 !important;
                      }
                    }
                    @media (max-width: 768px) {
                      h1.hero-title-mobile {
                        font-size: 44px !important;
                        line-height: 1.3 !important;
                      }
                    }
                    @media (max-width: 640px) {
                      h1.hero-title-mobile {
                        font-size: 40px !important;
                        line-height: 1.3 !important;
                      }
                    }
                    @media (max-width: 480px) {
                      h1.hero-title-mobile {
                        font-size: 36px !important;
                        line-height: 1.3 !important;
                      }
                    }
                    </style>
                    <?php
                    $hero_subtitle = salon_auto_get_option('hero_subtitle', 'Dealer aut premium z 28-letnim doświadczeniem.');
                    ?>
                    <p class="text-lg md:text-xl text-gray-600 font-normal leading-relaxed">
                        <?php echo esc_html($hero_subtitle); ?>
                    </p>
                </div>

                <!-- Hero Image Slider - Mobile -->
                <div class="relative lg:hidden">
                    <div class="aspect-[4/3] rounded-xl bg-white shadow-xl overflow-hidden border border-gray-200">
                        <div class="hero-slider-mobile w-full h-full relative">
                            <?php
                            // Get hero images from options
                            $hero_images_ids = get_option('salon_auto_hero_images', '');
                            $hero_images = array();
                            
                            if ($hero_images_ids) {
                                $ids = explode(',', $hero_images_ids);
                                foreach ($ids as $img_id) {
                                    $img_id = intval($img_id);
                                    if ($img_id > 0) {
                                        $img_url = wp_get_attachment_image_url($img_id, 'full');
                                        $img_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
                                        if ($img_url) {
                                            $hero_images[] = array(
                                                'url' => $img_url,
                                                'alt' => $img_alt ? $img_alt : get_bloginfo('name')
                                            );
                                        }
                                    }
                                }
                            }
                            
                            // Fallback to ACF if exists
                            if (empty($hero_images) && function_exists('get_field')) {
                                $acf_hero_images = get_field('hero_images', 'option');
                                if ($acf_hero_images && is_array($acf_hero_images)) {
                                    $hero_images = $acf_hero_images;
                                }
                            }
                            
                            // Fallback to default images if nothing configured
                            if (empty($hero_images)) {
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
                                    // Only add if file exists
                                    if (file_exists($img_full_path)) {
                                    $hero_images[] = array('url' => get_stylesheet_directory_uri() . $img_path);
                                    }
                                }
                            }
                            
                            foreach ($hero_images as $index => $img) :
                                $img_url = is_array($img) ? ($img['url'] ?? '') : $img;
                                $img_alt = is_array($img) ? ($img['alt'] ?? get_bloginfo('name')) : get_bloginfo('name');
                                // Pobierz wymiary obrazu jeśli jest w Media Library
                                $img_width = 1920;
                                $img_height = 1080;
                                if (is_array($img) && isset($img['id'])) {
                                    $img_meta = wp_get_attachment_image_src($img['id'], 'full');
                                    if ($img_meta) {
                                        $img_width = $img_meta[1];
                                        $img_height = $img_meta[2];
                                    }
                                }
                            ?>
                            <div class="slider-image <?php echo esc_attr($index === 0 ? 'active' : ''); ?>">
                                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" class="w-full h-full object-cover" width="<?php echo esc_attr($img_width); ?>" height="<?php echo esc_attr($img_height); ?>" style="aspect-ratio: <?php echo esc_attr($img_width); ?>/<?php echo esc_attr($img_height); ?>;">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Zaufanie -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                    <div class="flex items-center space-x-1 mb-3">
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-primary text-lg mb-1"><?php echo esc_html(salon_auto_get_option('trust_title', 'Jesteśmy z Wami od 1997 roku')); ?></p>
                        <p class="text-sm text-gray-600"><?php 
                            $trust_text = salon_auto_get_option('trust_text', '• 28 lat doświadczenia w sprzedaży samochodów premium<br>• 10.000 zrealizowanych leasingów<br>• Pełna gama zadowolonych Klientów VIP');
                            // Normalizuj formatowanie: usuń wszystkie <br> i białe znaki na początku/końcu
                            $trust_text = trim($trust_text);
                            $trust_text = str_replace('<br>', '', $trust_text);
                            $trust_text = str_replace('<br />', '', $trust_text);
                            $trust_text = str_replace("\n", '', $trust_text);
                            $trust_text = str_replace("\r", '', $trust_text);
                            // Podziel tekst na punkty (każdy zaczyna się od •)
                            $points = preg_split('/(?=•)/', $trust_text, -1, PREG_SPLIT_NO_EMPTY);
                            // Wyczyść każdy punkt z białych znaków
                            $points = array_map('trim', $points);
                            // Usuń puste punkty
                            $points = array_filter($points);
                            // Połącz punkty z <br> przed każdym oprócz pierwszego
                            $formatted_text = '';
                            foreach ($points as $index => $point) {
                                if (!empty($point)) {
                                    if ($index > 0) {
                                        $formatted_text .= '<br>';
                                    }
                                    $formatted_text .= $point;
                                }
                            }
                            echo wp_kses_post($formatted_text); 
                        ?></p>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <?php
                    $cta_button_cars_text = salon_auto_get_option('cta_button_cars_text', 'Zobacz samochody');
                    ?>
                    <a href="<?php echo esc_url(get_post_type_archive_link('car')); ?>" class="inline-flex items-center justify-center font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 bg-primary text-white hover:bg-secondary focus:ring-accent px-8 py-4 text-lg rounded-xl shadow-lg hover:shadow-lg transform hover:scale-[1.01] transition-all">
                        <?php echo esc_html($cta_button_cars_text); ?>
                    </a>
                    <?php
                    $leasing_page = get_page_by_path('leasing');
                    if ($leasing_page) :
                        $cta_button_leasing_text = salon_auto_get_option('cta_button_leasing_text', 'Wycena leasingu');
                    ?>
                    <a href="<?php echo esc_url(get_permalink($leasing_page->ID)); ?>" class="inline-flex items-center justify-center font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 border-2 border-primary text-primary hover:bg-primary hover:text-white focus:ring-accent px-8 py-4 text-lg rounded-xl transform hover:scale-[1.01] transition-all">
                        <?php echo esc_html($cta_button_leasing_text); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Hero Image Slider - Desktop -->
            <div class="relative hidden lg:block">
                <div class="aspect-[4/3] rounded-3xl bg-white shadow-xl overflow-hidden border border-gray-200">
                    <div class="hero-slider w-full h-full relative">
                        <?php
                        // Use same hero_images array as mobile
                        foreach ($hero_images as $index => $img) :
                            $img_url = is_array($img) ? ($img['url'] ?? '') : $img;
                            $img_alt = is_array($img) ? ($img['alt'] ?? get_bloginfo('name')) : get_bloginfo('name');
                            // Pobierz wymiary obrazu jeśli jest w Media Library
                            $img_width = 1920;
                            $img_height = 1080;
                            if (is_array($img) && isset($img['id'])) {
                                $img_meta = wp_get_attachment_image_src($img['id'], 'full');
                                if ($img_meta) {
                                    $img_width = $img_meta[1];
                                    $img_height = $img_meta[2];
                                }
                            }
                        ?>
                        <div class="slider-image <?php echo esc_attr($index === 0 ? 'active' : ''); ?>">
                            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" class="w-full h-full object-cover" width="<?php echo esc_attr($img_width); ?>" height="<?php echo esc_attr($img_height); ?>" style="aspect-ratio: <?php echo esc_attr($img_width); ?>/<?php echo esc_attr($img_height); ?>;">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Certifications -->
<section class="py-16 bg-primary text-white fade-in-up">
    <div class="container mx-auto">
        <div class="max-w-4xl mx-auto">
            <div class="p-8 bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-300">
                <div class="flex flex-col md:flex-row items-center justify-center gap-4 mb-4">
                    <?php
                    $cert_loza_text = salon_auto_get_option('cert_loza_text', 'Członek Loży Przedsiębiorców');
                    $cert_loza_url = salon_auto_get_option('cert_loza_url', 'https://lozaprzedsiebiorcow.pl');
                    ?>
                    <div class="flex items-center space-x-3">
                        <a href="<?php echo esc_url($cert_loza_url); ?>" target="_blank" rel="noopener" class="text-xl font-semibold text-white underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent rounded">
                            <?php echo esc_html($cert_loza_text); ?>
                        </a>
                    </div>
                    <span class="hidden md:block text-gray-500">•</span>
                    <?php
                    $cert_rzetelna_text = salon_auto_get_option('cert_rzetelna_text', 'Uczestnik Programu RZETELNA Firma');
                    // Link usunięty - pozostaje tylko tekst
                    ?>
                    <div class="flex items-center space-x-3">
                        <div class="flex flex-col">
                        <span class="text-xl font-semibold text-white">
                            <?php echo esc_html($cert_rzetelna_text); ?>
                        </span>
                        </div>
                    </div>
                </div>
                <?php
                $cert_description = salon_auto_get_option('cert_description', 'Gwarancja najwyższych standardów obsługi i transparentności');
                ?>
                <p class="text-gray-300 text-center text-base">
                    <?php echo esc_html($cert_description); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Available Cars - EXACT COPY FROM STATIC VERSION (piekneauta-kopia 2) -->
<section class="py-28 bg-white fade-in-up">
    <div class="container mx-auto">
        <div class="text-center mb-20 fade-in-up stagger-1">
            <?php
            $cars_section_title = salon_auto_get_option('cars_section_title', 'Dostępne samochody');
            ?>
            <h2 class="text-2xl md:text-3xl font-bold text-primary mb-6 tracking-tight">
                <?php echo esc_html($cars_section_title); ?>
            </h2>
            <div class="w-24 h-1.5 bg-accent mx-auto mb-12"></div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 md:gap-10 max-w-6xl mx-auto">
            <?php
            // Pobierz samochody z opcji salon_auto_homepage_cars (ustawione w panelu administracyjnym)
            $homepage_cars = get_option('salon_auto_homepage_cars', array());
            
            if (!empty($homepage_cars)) :
            foreach ($homepage_cars as $car_config) :
                $car_id = isset($car_config['car_id']) ? intval($car_config['car_id']) : 0;
                if ($car_id <= 0) continue;
                
                $car_post = get_post($car_id);
                if (!$car_post || $car_post->post_status !== 'publish') continue;
                
                $brand = salon_auto_get_car_field($car_id, 'brand');
                $model = salon_auto_get_car_field($car_id, 'model');
                $trim = salon_auto_get_car_field($car_id, 'trim');
                $price = salon_auto_get_car_field($car_id, 'price');
                $status = salon_auto_get_car_field($car_id, 'status');
                
                $car_name = ($brand && $model) ? $brand . ' ' . $model : $car_post->post_title;
                $car_url = get_permalink($car_id);
                
                // Video - użyj youtube_url z opcji, jeśli jest dostępne
                $youtube_video_id = '';
                $youtube_url = isset($car_config['youtube_url']) ? $car_config['youtube_url'] : '';
                if (!empty($youtube_url) && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $youtube_url, $matches)) {
                    $youtube_video_id = $matches[1];
                }
                
                // Zdjęcia - użyj slider_images z opcji, jeśli są dostępne
                $slider_images = array();
                $slider_images_config = isset($car_config['slider_images']) ? $car_config['slider_images'] : '';
                
                if (!empty($slider_images_config)) {
                    // NOWY FORMAT: używamy ||| jako separatora (obsługuje URL z przecinkami)
                    // Identyfikatory mogą być:
                    // - liczbami (ID z biblioteki mediów)
                    // - "url:https://..." (zdjęcia z galerii motywu)
                    
                    $image_identifiers = strpos($slider_images_config, '|||') !== false 
                        ? explode('|||', $slider_images_config) 
                        : explode(',', $slider_images_config); // Stary format dla kompatybilności
                    
                    foreach ($image_identifiers as $identifier) {
                        $identifier = trim($identifier);
                        if (empty($identifier)) continue;
                        
                        if (strpos($identifier, 'url:') === 0) {
                            // Zdjęcie z URL (bez ID w bibliotece mediów)
                            $img_url = substr($identifier, 4); // Usuń prefix "url:"
                            if (!empty($img_url)) {
                                $slider_images[] = array(
                                    'type' => 'image',
                                    'url' => $img_url,
                                    'alt' => $car_name
                                );
                            }
                        } else {
                            // Zdjęcie z ID w bibliotece mediów
                            $img_id = intval($identifier);
                            if ($img_id > 0) {
                                $img_url = wp_get_attachment_image_url($img_id, 'full');
                                if ($img_url) {
                                    $slider_images[] = array(
                                        'type' => 'image',
                                        'url' => $img_url,
                                        'alt' => $car_name
                                    );
                                }
                            }
                        }
                    }
                } else {
                    // Fallback: użyj całej galerii samochodu
                    $car_gallery = salon_auto_get_car_gallery($car_id);
                    foreach ($car_gallery as $img) {
                        $slider_images[] = array(
                            'type' => 'image',
                            'url' => is_array($img) ? $img['url'] : $img,
                            'alt' => $car_name
                        );
                    }
                }
            ?>
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-accent/30 transition-all duration-200 overflow-hidden group">
                <a href="<?php echo esc_url($car_url); ?>" class="block focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent rounded-xl">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                        <?php if ($youtube_video_id || !empty($slider_images)) : ?>
                        <div class="car-slider w-full h-full relative">
                            <?php if ($youtube_video_id) : ?>
                            <div class="car-slide active">
                                <div class="w-full h-full relative">
                                    <iframe src="https://www.youtube.com/embed/<?php echo esc_attr($youtube_video_id); ?>?rel=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="absolute inset-0 w-full h-full"></iframe>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php foreach ($slider_images as $img_index => $img) : 
                                // Pobierz wymiary obrazu
                                $img_width = 800;
                                $img_height = 600;
                                if (isset($img['id'])) {
                                    $img_meta = wp_get_attachment_image_src($img['id'], 'full');
                                    if ($img_meta) {
                                        $img_width = $img_meta[1];
                                        $img_height = $img_meta[2];
                                    }
                                }
                            ?>
                            <div class="car-slide <?php echo (!$youtube_video_id && $img_index === 0) ? 'active' : ''; ?>">
                                <img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" class="w-full h-full object-cover" loading="lazy" width="<?php echo esc_attr($img_width); ?>" height="<?php echo esc_attr($img_height); ?>" style="aspect-ratio: 4/3;">
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else : ?>
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400">Brak zdjęć</span>
                        </div>
                        <?php endif; ?>
                        <div class="absolute top-3 right-3 z-10">
                            <?php if ($status === 'reserved') : ?>
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-amber-500 text-white shadow-lg">ZAREZERWOWANY</span>
                            <?php elseif ($status === 'sold') : ?>
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-red-500 text-white shadow-lg">SPRZEDANY</span>
                            <?php else : ?>
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-accent text-white shadow-lg">DOSTĘPNY</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-primary mb-1"><?php echo esc_html($car_name); ?></h3>
                        <p class="text-sm text-gray-500 mb-6"><?php echo esc_html($trim ? $trim : ''); ?></p>
                        <div class="flex items-end justify-between">
                            <div class="text-2xl font-bold text-primary"><?php echo esc_html($price ? number_format($price, 0, ',', ' ') . ' zł' : 'Cena do uzgodnienia'); ?></div>
                            <div class="text-accent font-bold hover:translate-x-1 transition-transform" aria-hidden="true">→</div>
                        </div>
                    </div>
                </a>
            </article>
            <?php endforeach; endif; ?>
        </div>
        <div class="text-center mt-16">
            <?php
            $cta_button_all_cars_text = salon_auto_get_option('cta_button_all_cars_text', 'Sprawdź wszystkie oferty');
            ?>
            <a href="<?php echo esc_url(get_post_type_archive_link('car')); ?>" class="inline-flex items-center justify-center font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 border-2 border-primary text-primary hover:bg-primary hover:text-white focus:ring-accent px-8 py-4 text-lg rounded-xl transform hover:scale-[1.01] transition-all">
                <?php echo esc_html($cta_button_all_cars_text); ?>
            </a>
        </div>
    </div>
</section>

<!-- Why Us Section -->
<section class="py-28 bg-gray-50 fade-in-up">
    <div class="container mx-auto">
        <div class="text-center mb-20 fade-in-up stagger-1">
            <?php
            $why_us_title = salon_auto_get_option('why_us_title', 'Dlaczego my?');
            ?>
            <h2 class="text-2xl md:text-3xl font-bold text-primary mb-6 tracking-tight">
                <?php echo esc_html($why_us_title); ?>
            </h2>
            <div class="w-24 h-1.5 bg-accent mx-auto mb-12"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 max-w-6xl mx-auto">
            <?php
            // Get from options
            $why_us_items = get_option('salon_auto_why_us_items', array());
            
            // Default items if not configured
            if (empty($why_us_items)) {
                $why_us_items = array(
                    array(
                        'title' => 'Bezpieczeństwo transakcji',
                        'description' => 'Każdy oferowany przez nas samochód posiada pewną pisemną historię od nowości.'
                    ),
                    array(
                        'title' => 'Artur osobiście',
                        'description' => 'Bezpośredni kontakt z właścicielem. Indywidualne podejście do każdego Klienta.'
                    ),
                    array(
                        'title' => '28 lat doświadczenia',
                        'description' => 'Od 1997 roku na rynku. Prawie trzy dekady budowania zaufania i relacji z Klientami. <strong class="text-primary">10.000+ zrealizowanych leasingów</strong> i tysiące zadowolonych Klientów.'
                    )
                );
            }
            
            // Fallback to ACF if exists
            if (empty($why_us_items) && function_exists('get_field')) {
                $acf_items = get_field('why_us_items', 'option');
                if ($acf_items && is_array($acf_items)) {
                    $why_us_items = $acf_items;
                }
            }

            foreach ($why_us_items as $index => $item) :
                $title = is_array($item) ? ($item['title'] ?? '') : (isset($item->title) ? $item->title : '');
                $description = is_array($item) ? ($item['description'] ?? '') : (isset($item->description) ? $item->description : '');
            ?>
            <div class="bg-white rounded-xl p-8 border-l-4 border-accent shadow-md hover:shadow-xl transition-all duration-300 fade-in-up stagger-2">
                <div class="w-16 h-16 bg-accent/10 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-accent" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-primary mb-3"><?php echo esc_html($title); ?></h3>
                <p class="text-gray-600 leading-relaxed">
                    <?php echo wp_kses_post($description); ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<style>
#reviews-container article::before,
#reviews-container article::after,
#reviews-container article *::before,
#reviews-container article *::after {
  display: none !important;
  content: none !important;
  background: none !important;
  border: none !important;
  box-shadow: none !important;
}
#reviews-container article {
  overflow: visible !important;
}
#reviews-container article > * {
  background: transparent !important;
}
</style>
<section class="py-28 bg-white fade-in-up">
    <div class="container mx-auto">
        <div class="text-center mb-20 fade-in-up stagger-1">
            <?php
            $reviews_title = salon_auto_get_option('reviews_title', 'Opinie Klientów');
            ?>
            <h2 class="text-2xl md:text-3xl font-bold text-primary mb-6 tracking-tight">
                <?php echo esc_html($reviews_title); ?>
            </h2>
            <div class="w-24 h-1.5 bg-accent mx-auto mb-12"></div>
        </div>
        <div id="reviews-container" class="grid grid-cols-1 lg:grid-cols-3 gap-12 max-w-7xl mx-auto">
            <?php
            // Get reviews from options
            $reviews = get_option('salon_auto_reviews', array());
            
            // Fallback to ACF if exists
            if (empty($reviews) && function_exists('get_field')) {
                $acf_reviews = get_field('reviews', 'option');
                if ($acf_reviews && is_array($acf_reviews)) {
                    $reviews = $acf_reviews;
                }
            }
            
            // Fallback: load from JSON if nothing configured
            if (empty($reviews)) {
                // Try theme directory first, then root
                $reviews_json = get_stylesheet_directory() . '/data/reviews.json';
                if (!file_exists($reviews_json)) {
                    $reviews_json = dirname(dirname(dirname(get_stylesheet_directory()))) . '/data/reviews.json';
                }
                if (file_exists($reviews_json)) {
                    $reviews_data = json_decode(file_get_contents($reviews_json), true);
                    if ($reviews_data) {
                        $reviews = array_slice($reviews_data, 0, 3);
                    }
                }
            }
            
            // Fallback reviews if no data
            if (empty($reviews)) {
                $reviews = array(
                    array(
                        'name' => 'Jan Kowalski',
                        'content' => 'Profesjonalna obsługa i doskonała jakość samochodów. Polecam!',
                        'source' => 'Google',
                        'rating' => 5
                    ),
                    array(
                        'name' => 'Anna Nowak',
                        'content' => 'Kupiłam tutaj swój wymarzony samochód. Wszystko przebiegło sprawnie i bezproblemowo.',
                        'source' => 'Facebook',
                        'rating' => 5
                    ),
                    array(
                        'name' => 'Piotr Wiśniewski',
                        'content' => 'Świetna firma, uczciwi ludzie. Leasing załatwiony szybko i profesjonalnie.',
                        'source' => 'OtoMoto',
                        'rating' => 5
                    )
                );
            }
            
            // Display reviews
            if (!empty($reviews)) :
                foreach ($reviews as $review) :
                    $name = is_array($review) ? ($review['name'] ?? '') : (isset($review->name) ? $review->name : '');
                    $content = is_array($review) ? ($review['content'] ?? '') : (isset($review->content) ? $review->content : '');
                    $source = is_array($review) ? ($review['source'] ?? '') : (isset($review->source) ? $review->source : '');
                    $rating = is_array($review) ? ($review['rating'] ?? 5) : (isset($review->rating) ? $review->rating : 5);
            ?>
            <article class="bg-white p-8 rounded-xl shadow-soft border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-500">
                <div class="flex gap-1 mb-4">
                    <?php for ($i = 0; $i < $rating; $i++) : ?>
                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="#FDB022" aria-hidden="true" style="fill: #FDB022 !important; stroke: none !important;">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" fill="#FDB022" stroke="none" style="fill: #FDB022 !important; stroke: none !important;"/>
                    </svg>
                    <?php endfor; ?>
                </div>
                <p class="text-gray-700 mb-6 leading-relaxed text-lg">
                    "<?php echo esc_html($content); ?>"
                </p>
                <div class="flex items-center justify-between border-t-2 border-gray-100 pt-5">
                    <div class="font-bold text-primary text-lg"><?php echo esc_html($name); ?></div>
                    <?php if ($source) : ?>
                    <div class="text-sm text-gray-500 font-medium uppercase tracking-wide"><?php echo esc_html($source); ?></div>
                    <?php endif; ?>
                </div>
            </article>
            <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-white fade-in-up">
    <div class="container mx-auto text-center">
        <div class="max-w-3xl mx-auto fade-in-up stagger-1">
            <?php
            $cta_title = salon_auto_get_option('cta_title', 'Serdecznie Zapraszamy');
            ?>
            <h2 class="text-2xl md:text-3xl font-bold mb-4" style="color: #212121 !important; text-transform: none !important;">
                <?php echo esc_html($cta_title); ?>
            </h2>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <?php
                $phone = salon_auto_get_option('phone', '502 42 82 82');
                $cta_button_phone_text = salon_auto_get_option('cta_button_phone_text', 'Zadzwoń');
                ?>
                <a href="tel:+48<?php echo esc_attr(preg_replace('/[^0-9]/', '', $phone)); ?>" class="inline-flex items-center justify-center gap-2 bg-white text-primary border-2 border-primary px-8 py-4 rounded-xl font-semibold shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                    </svg>
                    <span><?php echo esc_html($cta_button_phone_text); ?>: <?php echo esc_html($phone); ?></span>
                </a>
                <?php
                $contact_page = get_page_by_path('kontakt');
                if ($contact_page) :
                    $cta_button_contact_text = salon_auto_get_option('cta_button_contact_text', 'Formularz kontaktowy');
                ?>
                <a href="<?php echo esc_url(get_permalink($contact_page->ID)); ?>" class="inline-flex items-center justify-center gap-2 bg-white text-primary border-2 border-primary px-8 py-4 rounded-xl font-semibold shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                    </svg>
                    <span><?php echo esc_html($cta_button_contact_text); ?></span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>

