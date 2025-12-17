<?php
/**
 * Archive Template for Car Post Type
 * 
 * Wyświetla listę wszystkich dostępnych samochodów
 */

get_header();

// Edytowalne pola (panel: Strona Główna -> Samochody)
$hero_title    = get_option('salon_auto_page_samochody_hero_title', 'Dostępne samochody');
$hero_subtitle = get_option('salon_auto_page_samochody_hero_subtitle', 'Wszystkie auta sprawdzone i gotowe do odbioru');
$hero_content  = get_option('salon_auto_page_samochody_content', '');
?>

<!-- Breadcrumbs -->
<nav class="container mx-auto px-4 sm:px-6 pt-4" aria-label="Breadcrumb">
    <ol class="flex items-center text-sm text-gray-500 space-x-2 overflow-x-auto" style="flex-wrap: nowrap !important; white-space: nowrap !important;">
        <li style="white-space: nowrap !important; flex-shrink: 0;">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-primary transition-colors whitespace-nowrap">Strona główna</a>
        </li>
        <li class="flex items-center" style="white-space: nowrap !important; flex-shrink: 0;">
            <svg class="w-4 h-4 mx-1 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="text-gray-900 font-medium whitespace-nowrap">Samochody</span>
        </li>
    </ol>
</nav>

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
            "item": "<?php echo esc_url(get_post_type_archive_link('car')); ?>"
        }
    ]
}
</script>

<!-- Page Header - EXACT COPY FROM STATIC VERSION -->
<section class="py-16 bg-gray-50 border-b border-gray-200" style="scroll-margin-top: 100px;">
    <div class="container mx-auto">
        <h1 class="text-2xl md:text-3xl font-bold mb-4 text-primary">
            <?php echo esc_html($hero_title); ?>
        </h1>
        <?php if (!empty($hero_subtitle)) : ?>
        <p class="text-lg text-gray-600">
            <?php echo esc_html($hero_subtitle); ?>
        </p>
        <?php endif; ?>
        <?php if (!empty($hero_content)) : ?>
        <div class="prose prose-lg text-gray-700 mt-6">
            <?php echo wp_kses_post(wpautop($hero_content)); ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Catalog bez filtrów - EXACT COPY FROM STATIC VERSION -->
<section class="py-12">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 md:gap-12 max-w-7xl mx-auto" id="cars-catalog-container">
            <?php
            // Pobierz samochody z opcji salon_auto_catalog_cars (ustawione w panelu administracyjnym)
            $catalog_cars = get_option('salon_auto_catalog_cars', array());
            
            // Konwersja starego formatu do nowego (jeśli potrzeba)
            $catalog_cars = array_map(function($item) {
                if (is_array($item)) return $item;
                return array('car_id' => intval($item), 'custom_image_id' => 0, 'custom_caption' => '');
            }, $catalog_cars);
            
            if (!empty($catalog_cars)) :
            foreach ($catalog_cars as $car_config) :
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
                
                // Użyj niestandardowego podpisu, jeśli jest dostępny, w przeciwnym razie użyj trim
                $card_caption = !empty($car_config['custom_caption']) ? $car_config['custom_caption'] : $trim;
                
                // Użyj niestandardowego zdjęcia, jeśli jest dostępne, w przeciwnym razie użyj pierwszego zdjęcia z galerii
                $image_alt = $car_name;
                $image_url = '';
                
                $custom_image_id = isset($car_config['custom_image_id']) ? intval($car_config['custom_image_id']) : 0;
                if ($custom_image_id > 0) {
                    $image_url = wp_get_attachment_image_url($custom_image_id, 'full');
                }
                
                // Fallback: użyj pierwszego zdjęcia z galerii samochodu
                if (empty($image_url)) {
                    $car_gallery = salon_auto_get_car_gallery($car_id);
                    if (!empty($car_gallery)) {
                        $first_image = is_array($car_gallery[0]) ? $car_gallery[0]['url'] : $car_gallery[0];
                        $image_url = $first_image;
                    }
                }
                
                // Jeśli nadal brak zdjęcia, użyj domyślnego
                if (empty($image_url)) {
                    $image_url = get_stylesheet_directory_uri() . '/images/og-default.svg';
                }
            ?>
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-accent/30 transition-all duration-200 overflow-hidden group">
                <a href="<?php echo esc_url($car_url); ?>" class="block focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent rounded-xl">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                        <!-- Single main image - używa custom_image_id z opcji lub domyślnego zdjęcia samochodu -->
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" class="w-full h-full object-cover" loading="lazy" style="aspect-ratio: 4/3;" width="800" height="600">
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
                        <p class="text-sm text-gray-500 mb-6"><?php echo esc_html($card_caption ? $card_caption : ''); ?></p>
                        <div class="flex items-end justify-between">
                            <div class="text-2xl font-bold text-primary"><?php echo esc_html($price ? number_format($price, 0, ',', ' ') . ' zł' : 'Cena do uzgodnienia'); ?></div>
                            <div class="text-accent font-bold hover:translate-x-1 transition-transform" aria-hidden="true">→</div>
                        </div>
                    </div>
                </a>
            </article>
            <?php endforeach; endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>

