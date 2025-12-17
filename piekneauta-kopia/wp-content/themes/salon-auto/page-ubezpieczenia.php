<?php
/**
 * Template Name: Ubezpieczenia
 *
 * This template is used to display the "Ubezpieczenia" page.
 * It directly embeds the static HTML content from the original site,
 * including the FAQ section and contact form.
 */

get_header();

// Get phone number and email
$phone = salon_auto_get_option('phone', '502 42 82 82');
$phone_clean = preg_replace('/[^0-9]/', '', $phone);
$email = salon_auto_get_option('email', 'biuro@piekneauta.pl');

// Get contact page URL
$contact_page = get_page_by_path('kontakt');
$contact_url = $contact_page ? get_permalink($contact_page->ID) : home_url('/kontakt/');

// Get privacy policy URL
$privacy_page = get_page_by_path('polityka-prywatnosci');
$privacy_url = $privacy_page ? get_permalink($privacy_page->ID) : home_url('/polityka-prywatnosci/');
?>

<main id="main-content" class="min-h-screen">
    <!-- Hero -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32 border-b border-gray-100" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="font-bold text-primary mb-6 tracking-tight hero-title fade-in-up" style="font-size: 48px !important;">
                    <?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_hero_title', 'Ubezpieczenia samochodowe premium')); ?>
                </h1>
                <div class="w-24 h-1.5 bg-primary mx-auto mb-8 fade-in-up stagger-1"></div>
                <p class="text-lg sm:text-xl md:text-2xl text-gray-600 leading-relaxed font-light mb-8 hero-subtitle fade-in-up stagger-2">
                    <?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_hero_subtitle', 'Kompleksowa ochrona Twojego pojazdu. Współpracujemy z najlepszymi towarzystwami ubezpieczeniowymi, aby zapewnić Tobie najbezpieczniejsze i najkorzystniejsze cenowo warunki.')); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary mb-3 sm:mb-4 fade-in-up">
                    <?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_services_title', 'Nasze ubezpieczenia')); ?>
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto fade-in-up stagger-1">
                    <?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_services_subtitle', 'Pełna ochrona dopasowana do Twoich potrzeb')); ?>
                </p>
            </div>
            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <!-- OC -->
                <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm card-animate hover-lift fade-in-up stagger-1 animate-on-scroll">
                    <div class="w-16 h-16 bg-gray-50 rounded-xl flex items-center justify-center mb-4 border-2 border-gray-200">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-primary mb-4"><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_oc_title', 'OC - Obowiązkowe')); ?></h3>
                    <p class="text-gray-700 mb-4">
                        <?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_oc_text', 'Ubezpieczenie odpowiedzialności cywilnej posiadaczy pojazdów mechanicznych. Obowiązkowe dla każdego właściciela pojazdu.')); ?>
                    </p>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-success flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_oc_item1', 'Ochrona do 50 mln zł w UE')); ?></span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-success flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_oc_item2', 'Zielona Karta gratis')); ?></span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-success flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_oc_item3', 'Możliwość rat 0%')); ?></span>
                        </li>
                    </ul>
                </div>
                <!-- AC -->
                <div class="bg-primary text-white rounded-2xl p-8 shadow-sm card-animate hover-lift fade-in-up stagger-2 animate-on-scroll">
                    <div class="w-16 h-16 bg-white/10 rounded-xl flex items-center justify-center mb-4 border-2 border-white/20">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4"><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_ac_title', 'AC - Autocasco')); ?></h3>
                    <p class="text-gray-200 mb-4">
                        <?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_ac_text', 'Ubezpieczenie własnego pojazdu od kradzieży, kolizji, wypadku i zdarzeń losowych. Zalecane dla aut premium.')); ?>
                    </p>
                    <ul class="space-y-2 text-sm text-gray-200">
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_ac_item1', 'Ochrona przed kradzieżą')); ?></span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_ac_item2', 'Kolizja i wypadek')); ?></span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_ac_item3', 'Szkody losowe (żywioły, wandalizm)')); ?></span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_ac_item4', 'Opcjonalnie: szyby, opony, assistance')); ?></span>
                        </li>
                    </ul>
                </div>
                <!-- NNW -->
                <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm card-animate hover-lift fade-in-up stagger-3 animate-on-scroll">
                    <div class="w-16 h-16 bg-gray-50 rounded-xl flex items-center justify-center mb-4 border-2 border-gray-200">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-primary mb-4"><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_nnw_title', 'NNW - Następstwa nieszczęśliwych wypadków')); ?></h3>
                    <p class="text-gray-700 mb-4">
                        <?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_nnw_text', 'Ochrona kierowcy i pasażerów na wypadek obrażeń ciała, trwałego uszczerbku lub śmierci w wyniku wypadku.')); ?>
                    </p>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-success flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_nnw_item1', 'Świadczenie przy uszkodzeniu zdrowia')); ?></span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-success flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_nnw_item2', 'Ochrona kierowcy i pasażerów')); ?></span>
                        </li>
                    </ul>
                </div>
                <!-- Assistance -->
                <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm card-animate hover-lift fade-in-up stagger-4 animate-on-scroll">
                    <div class="w-16 h-16 bg-gray-50 rounded-xl flex items-center justify-center mb-4 border-2 border-gray-200">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-primary mb-4"><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_assistance_title', 'Assistance')); ?></h3>
                    <p class="text-gray-700 mb-4">
                        <?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_assistance_text', 'Pomoc drogowa 24/7 w Polsce i za granicą. Holowanie, auto zastępcze, nocleg, pomoc prawna.')); ?>
                    </p>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-success flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_assistance_item1', 'Holowanie 24/7')); ?></span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-success flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_assistance_item2', 'Auto zastępcze')); ?></span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-success flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_assistance_item3', 'Nocleg, transport')); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-primary mb-4 fade-in-up">
                    <?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_benefits_title', 'Dlaczego my?')); ?>
                </h2>
            </div>
            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <div class="text-center bg-white p-8 rounded-2xl shadow-sm border border-gray-100 card-animate hover-lift fade-in-up stagger-1 animate-on-scroll">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-gray-200">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/><path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-primary mb-2"><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_benefit1_title', 'Najlepsze ceny')); ?></h3>
                    <p class="text-sm text-gray-600">
                        <?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_benefit1_text', 'Porównujemy oferty różnych TU, znajdziemy dla Ciebie najbardziej korzystne warunki')); ?>
                    </p>
                </div>
                <div class="text-center bg-white p-8 rounded-2xl shadow-sm border border-gray-100 card-animate hover-lift fade-in-up stagger-2 animate-on-scroll">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-gray-200">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-primary mb-2"><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_benefit2_title', 'Szybka wycena')); ?></h3>
                    <p class="text-sm text-gray-600">
                        <?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_benefit2_text', 'Otrzymasz wycenę w ciągu 24 godzin - bez zbędnych formalności')); ?>
                    </p>
                </div>
                <div class="text-center bg-white p-8 rounded-2xl shadow-sm border border-gray-100 card-animate hover-lift fade-in-up stagger-3 animate-on-scroll">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-gray-200">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-primary mb-2"><?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_benefit3_title', 'Kompleksowa obsługa')); ?></h3>
                    <p class="text-sm text-gray-600">
                        <?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_benefit3_text', 'Pomagamy w wyborze najlepszej oferty i załatwiamy wszystkie formalności')); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32" style="background-color: #ffffff !important;">
        <style>
            /* Upewnij się, że tylko jedna ikona FAQ jest widoczna */
            [x-cloak] { display: none !important; }
            /* Domyślnie pokazuj plus, ukryj minus przed załadowaniem Alpine.js */
            .faq-icon-container svg:first-child { display: block; }
            .faq-icon-container svg:last-child { display: none; }
        </style>
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary mb-4 fade-in-up">
                        Najczęściej zadawane pytania o ubezpieczenia
                    </h2>
                    <p class="text-lg text-gray-600 fade-in-up stagger-1">
                        Odpowiedzi na pytania dotyczące ubezpieczeń samochodowych dla aut premium
                    </p>
                </div>
                
                <div class="space-y-4" x-data="{ openIndex: null }">
                    <!-- FAQ Item 1 -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                        <button 
                            @click="openIndex = openIndex === 0 ? null : 0"
                            class="w-full flex items-center justify-between px-6 py-5 text-left focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-xl"
                        >
                            <span class="text-lg font-normal text-primary pr-4">
                                Jakie ubezpieczenie wybrać dla auta premium używanego?
                            </span>
                            <!-- Kontener z ikonami - obie w jednym miejscu -->
                            <div class="faq-icon-container relative w-6 h-6 flex-shrink-0">
                                <!-- Plus (gdy zamknięte) -->
                                <svg 
                                    x-show="openIndex !== 0"
                                    class="absolute inset-0 w-6 h-6 text-primary transition-all duration-300"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <!-- Minus (gdy otwarte) -->
                                <svg 
                                    x-show="openIndex === 0"
                                    class="absolute inset-0 w-6 h-6 text-primary transition-all duration-300"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </div>
                        </button>
                        <div 
                            x-show="openIndex === 0"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0"
                            x-transition:enter-end="opacity-100 max-h-screen"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-screen"
                            x-transition:leave-end="opacity-0 max-h-0"
                            class="overflow-hidden"
                        >
                            <div class="px-6 pb-5 border-t border-gray-100 pt-4">
                                <p class="text-gray-700 leading-relaxed">
                                    Dla aut premium używanych rekomendujemy pakiet OC + AC (Autocasco), który zapewnia pełną ochronę przed kradzieżą, kolizją i zdarzeniami losowymi. Dodatkowo warto rozważyć NNW (Następstwa nieszczęśliwych wypadków) oraz Assistance, który zapewnia pomoc drogową 24/7 w Polsce i za granicą.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 2 -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                        <button 
                            @click="openIndex = openIndex === 1 ? null : 1"
                            class="w-full flex items-center justify-between px-6 py-5 text-left focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-xl"
                        >
                            <span class="text-lg font-normal text-primary pr-4">
                                Czy ubezpieczenie AC jest obowiązkowe dla aut premium?
                            </span>
                            <!-- Kontener z ikonami - obie w jednym miejscu -->
                            <div class="faq-icon-container relative w-6 h-6 flex-shrink-0">
                                <!-- Plus (gdy zamknięte) -->
                                <svg 
                                    x-show="openIndex !== 1"
                                    class="absolute inset-0 w-6 h-6 text-primary transition-all duration-300"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <!-- Minus (gdy otwarte) -->
                                <svg 
                                    x-show="openIndex === 1"
                                    class="absolute inset-0 w-6 h-6 text-primary transition-all duration-300"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </div>
                        </button>
                        <div 
                            x-show="openIndex === 1"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0"
                            x-transition:enter-end="opacity-100 max-h-screen"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-screen"
                            x-transition:leave-end="opacity-0 max-h-0"
                            class="overflow-hidden"
                        >
                            <div class="px-6 pb-5 border-t border-gray-100 pt-4">
                                <p class="text-gray-700 leading-relaxed">
                                    Ubezpieczenie AC (Autocasco) nie jest obowiązkowe, ale zdecydowanie zalecane dla aut premium używanych i nowych. Ochrona AC zapewnia bezpieczeństwo przed kradzieżą, uszkodzeniami w wyniku kolizji oraz zdarzeniami losowymi, co jest szczególnie ważne dla pojazdów o wysokiej wartości rynkowej.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 3 -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                        <button 
                            @click="openIndex = openIndex === 2 ? null : 2"
                            class="w-full flex items-center justify-between px-6 py-5 text-left focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-xl"
                        >
                            <span class="text-lg font-normal text-primary pr-4">
                                Ile kosztuje ubezpieczenie OC + AC dla auta premium?
                            </span>
                            <!-- Kontener z ikonami - obie w jednym miejscu -->
                            <div class="faq-icon-container relative w-6 h-6 flex-shrink-0">
                                <!-- Plus (gdy zamknięte) -->
                                <svg 
                                    x-show="openIndex !== 2"
                                    class="absolute inset-0 w-6 h-6 text-primary transition-all duration-300"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <!-- Minus (gdy otwarte) -->
                                <svg 
                                    x-show="openIndex === 2"
                                    class="absolute inset-0 w-6 h-6 text-primary transition-all duration-300"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </div>
                        </button>
                        <div 
                            x-show="openIndex === 2"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0"
                            x-transition:enter-end="opacity-100 max-h-screen"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-screen"
                            x-transition:leave-end="opacity-0 max-h-0"
                            class="overflow-hidden"
                        >
                            <div class="px-6 pb-5 border-t border-gray-100 pt-4">
                                <p class="text-gray-700 leading-relaxed">
                                    Koszt ubezpieczenia OC + AC dla aut premium zależy od wielu czynników: wartości pojazdu, wieku, przebiegu, miejsca rejestracji, historii szkód oraz doświadczenia kierowcy. Współpracujemy z najlepszymi towarzystwami ubezpieczeniowymi, które oferują atrakcyjne warunki dla właścicieli aut premium. Otrzymasz wycenę w ciągu 24 godzin.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 4 -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                        <button 
                            @click="openIndex = openIndex === 3 ? null : 3"
                            class="w-full flex items-center justify-between px-6 py-5 text-left focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-xl"
                        >
                            <span class="text-lg font-normal text-primary pr-4">
                                Czy mogę ubezpieczyć auto premium starsze niż 5 lat?
                            </span>
                            <!-- Kontener z ikonami - obie w jednym miejscu -->
                            <div class="faq-icon-container relative w-6 h-6 flex-shrink-0">
                                <!-- Plus (gdy zamknięte) -->
                                <svg 
                                    x-show="openIndex !== 3"
                                    class="absolute inset-0 w-6 h-6 text-primary transition-all duration-300"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <!-- Minus (gdy otwarte) -->
                                <svg 
                                    x-show="openIndex === 3"
                                    class="absolute inset-0 w-6 h-6 text-primary transition-all duration-300"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </div>
                        </button>
                        <div 
                            x-show="openIndex === 3"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0"
                            x-transition:enter-end="opacity-100 max-h-screen"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-screen"
                            x-transition:leave-end="opacity-0 max-h-0"
                            class="overflow-hidden"
                        >
                            <div class="px-6 pb-5 border-t border-gray-100 pt-4">
                                <p class="text-gray-700 leading-relaxed">
                                    Tak, większość towarzystw ubezpieczeniowych oferuje ubezpieczenie AC dla aut premium starszych niż 5 lat. Warunki ubezpieczenia zależą od stanu technicznego pojazdu, jego wartości oraz historii. Współpracujemy z firmami, które specjalizują się w ubezpieczeniach aut premium, w tym starszych modeli.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 5 -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                        <button 
                            @click="openIndex = openIndex === 4 ? null : 4"
                            class="w-full flex items-center justify-between px-6 py-5 text-left focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-xl"
                        >
                            <span class="text-lg font-normal text-primary pr-4">
                                Co obejmuje pakiet ubezpieczenia Assistance dla aut premium?
                            </span>
                            <!-- Kontener z ikonami - obie w jednym miejscu -->
                            <div class="faq-icon-container relative w-6 h-6 flex-shrink-0">
                                <!-- Plus (gdy zamknięte) -->
                                <svg 
                                    x-show="openIndex !== 4"
                                    class="absolute inset-0 w-6 h-6 text-primary transition-all duration-300"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <!-- Minus (gdy otwarte) -->
                                <svg 
                                    x-show="openIndex === 4"
                                    class="absolute inset-0 w-6 h-6 text-primary transition-all duration-300"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </div>
                        </button>
                        <div 
                            x-show="openIndex === 4"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0"
                            x-transition:enter-end="opacity-100 max-h-screen"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-screen"
                            x-transition:leave-end="opacity-0 max-h-0"
                            class="overflow-hidden"
                        >
                            <div class="px-6 pb-5 border-t border-gray-100 pt-4">
                                <p class="text-gray-700 leading-relaxed">
                                    Assistance dla aut premium obejmuje: holowanie pojazdu 24/7 w Polsce i za granicą, auto zastępcze na czas naprawy, pomoc w przypadku awarii lub wypadku, nocleg w razie konieczności, transport do miejsca docelowego oraz pomoc prawną. To kompleksowa ochrona, która zapewnia spokój podczas podróży autem premium.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 6 -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                        <button 
                            @click="openIndex = openIndex === 5 ? null : 5"
                            class="w-full flex items-center justify-between px-6 py-5 text-left focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-xl"
                        >
                            <span class="text-lg font-normal text-primary pr-4">
                                Jak szybko otrzymam wycenę ubezpieczenia dla auta premium?
                            </span>
                            <!-- Kontener z ikonami - obie w jednym miejscu -->
                            <div class="faq-icon-container relative w-6 h-6 flex-shrink-0">
                                <!-- Plus (gdy zamknięte) -->
                                <svg 
                                    x-show="openIndex !== 5"
                                    class="absolute inset-0 w-6 h-6 text-primary transition-all duration-300"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <!-- Minus (gdy otwarte) -->
                                <svg 
                                    x-show="openIndex === 5"
                                    class="absolute inset-0 w-6 h-6 text-primary transition-all duration-300"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </div>
                        </button>
                        <div 
                            x-show="openIndex === 5"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0"
                            x-transition:enter-end="opacity-100 max-h-screen"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-screen"
                            x-transition:leave-end="opacity-0 max-h-0"
                            class="overflow-hidden"
                        >
                            <div class="px-6 pb-5 border-t border-gray-100 pt-4">
                                <p class="text-gray-700 leading-relaxed">
                                    Po wypełnieniu formularza zapytania o wycenę ubezpieczenia, otrzymasz odpowiedź w ciągu 24 godzin roboczych. Nasz zespół porównuje oferty różnych towarzystw ubezpieczeniowych, aby znaleźć dla Ciebie najbardziej korzystne warunki ubezpieczenia OC + AC dla auta premium.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form -->
    <section id="formularz" class="py-16 sm:py-20 md:py-28 lg:py-32" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-2xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-2xl sm:text-3xl font-bold text-primary mb-4 fade-in-up">
                        <?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_form_title', 'Zapytaj o ubezpieczenie')); ?>
                    </h2>
                    <p class="text-lg text-gray-600 fade-in-up stagger-1">
                        <?php echo esc_html(get_option('salon_auto_page_ubezpieczenia_form_subtitle', 'Wypełnij formularz, a odezwiemy się do Ciebie w ciągu 24 godziny')); ?>
                    </p>
                </div>
                <form action="https://formsubmit.co/<?php echo esc_attr($email); ?>" method="POST" class="space-y-6 bg-white rounded-2xl p-8 shadow-sm border border-gray-100 scale-in animate-on-scroll" x-data="{ submitted: false, error: false, loading: false, hideTimeout: null }" x-init="submitted = false; error = false; loading = false;" @submit.prevent="
                    loading = true;
                    error = false;
                    const formData = new FormData($el);
                    fetch($el.action, {
                      method: 'POST',
                      body: formData,
                      headers: { 'Accept': 'application/json' }
                    })
                    .then(response => {
                      loading = false;
                      if (response.ok) {
                        submitted = true;
                        error = false;
                        $el.reset();
                        if (hideTimeout) clearTimeout(hideTimeout);
                        hideTimeout = setTimeout(() => {
                          submitted = false;
                        }, 4000);
                        setTimeout(() => {
                          $el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }, 100);
                      } else {
                        error = true;
                        $el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                      }
                    })
                    .catch(() => {
                      loading = false;
                      error = true;
                      $el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                  ">
                    <input type="hidden" name="_subject" value="Zapytanie o ubezpieczenie - piekneauta.pl">
                    <input type="hidden" name="_captcha" value="false">
                    <input type="hidden" name="_template" value="box">
                    <div x-show="!submitted">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Imię i nazwisko <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Telefon <span class="text-danger">*</span>
                                </label>
                                <input type="tel" name="phone" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Marka i model pojazdu
                            </label>
                            <input type="text" name="car" placeholder="np. BMW X5 2021" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Rodzaj ubezpieczenia <span class="text-danger">*</span>
                            </label>
                            <select name="insurance_type" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                                <option value="">Wybierz...</option>
                                <option value="OC">OC</option>
                                <option value="OC+AC">OC + AC</option>
                                <option value="AC">Tylko AC</option>
                                <option value="NNW">NNW</option>
                                <option value="Assistance">Assistance</option>
                                <option value="Pakiet">Pakiet kompleksowy</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Dodatkowe informacje
                            </label>
                            <textarea name="message" rows="4" placeholder="np. data rejestracji, historia szkód, preferencje..." class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent"></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-start space-x-3 text-sm">
                                <input type="checkbox" name="privacy" required class="mt-0.5 w-5 h-5 text-accent border-gray-300 rounded focus:ring-2 focus:ring-accent cursor-pointer">
                                <span class="text-gray-700">
                                    Akceptuję <a href="<?php echo esc_url($privacy_url); ?>" class="text-accent hover:underline">politykę prywatności</a>
                                    i wyrażam zgodę na przetwarzanie moich danych osobowych <span class="text-danger">*</span>
                                </span>
                            </label>
                        </div>
                        <button type="submit" x-bind:disabled="loading" class="mt-8 inline-flex items-center justify-center font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 bg-primary text-white hover:bg-secondary focus:ring-accent px-8 py-4 text-lg rounded-xl w-full justify-center shadow-lg">
                            <span x-show="!loading">Wyślij zapytanie</span>
                            <span x-show="loading" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Wysyłanie...
                            </span>
                        </button>
                    </div>
                    <div x-show="submitted" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 md:p-12">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl md:text-3xl font-bold text-primary mb-4">Zapytanie o ubezpieczenie zostało wysłane!</h3>
                            <p class="text-lg text-gray-700 mb-3 font-medium">Dziękujemy za zainteresowanie naszą ofertą ubezpieczeniową.</p>
                            <p class="text-sm text-gray-500 mb-6">Skontaktujemy się z Tobą w ciągu <strong class="text-primary">24 godzin roboczych</strong> z indywidualną ofertą ubezpieczeniową.</p>
                            <div class="pt-6 border-t border-gray-200">
                                <p class="text-sm text-gray-500 mb-2">W razie pilnych spraw:</p>
                                <a href="tel:+48<?php echo esc_attr($phone_clean); ?>" class="inline-flex items-center gap-2 text-primary font-bold text-lg hover:text-secondary transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                    </svg>
                                    <?php echo esc_html($phone); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div x-show="error && !submitted" class="text-center py-8 bg-red-50 border-2 border-red-200 rounded-xl">
                        <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-red-700 font-semibold mb-1">Wystąpił błąd podczas wysyłania wiadomości.</p>
                        <p class="text-sm text-red-600">Spróbuj ponownie lub skontaktuj się bezpośrednio: <a href="tel:+48<?php echo esc_attr($phone_clean); ?>" class="underline"><?php echo esc_html($phone); ?></a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="py-20 bg-white fade-in-up">
        <div class="container mx-auto text-center">
            <div class="max-w-3xl mx-auto fade-in-up-delay-1">
                <h2 class="text-2xl md:text-3xl font-bold mb-4" style="color: #212121 !important; text-transform: none !important;">
                    Serdecznie Zapraszamy
                </h2>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="tel:+48<?php echo esc_attr($phone_clean); ?>" class="inline-flex items-center justify-center gap-2 bg-white text-primary border-2 border-primary px-8 py-4 rounded-xl font-semibold shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.948.684l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                        </svg>
                        <span>Zadzwoń: <?php echo esc_html($phone); ?></span>
                    </a>
                    <a href="<?php echo esc_url($contact_url); ?>" class="inline-flex items-center justify-center gap-2 bg-white text-primary border-2 border-primary px-8 py-4 rounded-xl font-semibold shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        <span>Formularz kontaktowy</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>

