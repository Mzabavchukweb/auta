<?php
/**
 * Template Name: Katalog Samochodów
 * 
 * Edytowalna strona "Samochody" - EXACT COPY FROM STATIC VERSION
 * Umożliwia edycję treści strony w WordPressie
 */

get_header();
?>

<!-- Page Header - EXACT COPY FROM STATIC VERSION -->
<section class="py-16 bg-gray-50 border-b border-gray-200 fade-in-up">
    <div class="container mx-auto">
        <h1 class="text-2xl md:text-3xl font-bold mb-4 text-primary fade-in-up stagger-1">Dostępne samochody</h1>
        <p class="text-lg text-gray-600 fade-in-up-delay-2">
            Wszystkie auta sprawdzone i gotowe do odbioru
        </p>
    </div>
</section>

<!-- Catalog bez filtrów - EXACT COPY FROM STATIC VERSION -->
<section class="py-12 fade-in-up">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 md:gap-12 max-w-7xl mx-auto" id="cars-catalog-container">
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <a href="<?php echo esc_url(get_post_type_archive_link('car') . 'audi-a8-2019-50tdi-quattro/'); ?>" class="block">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 car-image-loading">
                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/images/audi-a8-01.jpg'); ?>" alt="Audi A8" class="w-full h-full object-cover car-image" loading="lazy">
                        <div class="absolute top-3 right-3 z-10">
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-accent text-white shadow-lg">DOSTĘPNY</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-primary mb-1">Audi A8</h3>
                        <p class="text-sm text-gray-500 mb-6">50 TDI mHEV Quattro Tiptronic</p>
                        <div class="flex items-end justify-between">
                            <div class="text-2xl font-bold text-primary">185 000 zł</div>
                            <div class="text-accent font-bold">→</div>
                        </div>
                    </div>
                </a>
            </article>

            <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <a href="<?php echo esc_url(get_post_type_archive_link('car') . 'bmw-seria-7-2018-730d-xdrive/'); ?>" class="block">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 car-image-loading">
                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/images/bmw-7-01.jpg'); ?>" alt="BMW Seria 7 Long" class="w-full h-full object-cover car-image" loading="lazy">
                        <div class="absolute top-3 right-3 z-10">
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-accent text-white shadow-lg">DOSTĘPNY</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-primary mb-1">BMW Seria 7 Long</h3>
                        <p class="text-sm text-gray-500 mb-6">740Li xDrive iPerformance</p>
                        <div class="flex items-end justify-between">
                            <div class="text-2xl font-bold text-primary">163 000 zł</div>
                            <div class="text-accent font-bold">→</div>
                        </div>
                    </div>
                </a>
            </article>

            <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <a href="<?php echo esc_url(get_post_type_archive_link('car') . 'audi-sq8-2023/'); ?>" class="block">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 car-image-loading">
                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/images/audi-sq8-01.jpg'); ?>" alt="Audi SQ8" class="w-full h-full object-cover car-image" loading="lazy">
                        <div class="absolute top-3 right-3 z-10">
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-accent text-white shadow-lg">DOSTĘPNY</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-primary mb-1">Audi SQ8</h3>
                        <p class="text-sm text-gray-500 mb-6">4.0 TFSI Quattro</p>
                        <div class="flex items-end justify-between">
                            <div class="text-2xl font-bold text-primary">288 000 zł</div>
                            <div class="text-accent font-bold">→</div>
                        </div>
                    </div>
                </a>
            </article>

            <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <a href="<?php echo esc_url(get_post_type_archive_link('car') . 'audi-a6-limousine/'); ?>" class="block">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 car-image-loading">
                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/images/audi-a6-01.jpg'); ?>" alt="Audi A6 Limousine" class="w-full h-full object-cover car-image" loading="lazy">
                        <div class="absolute top-3 right-3 z-10">
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-accent text-white shadow-lg">DOSTĘPNY</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-primary mb-1">Audi A6 Limousine</h3>
                        <p class="text-sm text-gray-500 mb-6">Limousine</p>
                        <div class="flex items-end justify-between">
                            <div class="text-2xl font-bold text-primary">117 000 zł</div>
                            <div class="text-accent font-bold">→</div>
                        </div>
                    </div>
                </a>
            </article>
        </div>
    </div>
</section>

<?php get_footer(); ?>

