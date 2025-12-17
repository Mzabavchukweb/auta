<?php
/**
 * Template Name: Leasing
 * 
 * Dedykowany szablon dla strony "Leasing" z pełną treścią
 */

get_header();
?>
<style>
/* Logo - większe na mobile dla strony leasing */
@media (max-width: 1023px) {
  header img[alt="Piekne auta"] { height: 12rem !important; }
  footer img[alt="Piekne auta"] { height: 10rem !important; }
}
@media (max-width: 768px) {
  header img[alt="Piekne auta"] { height: 11rem !important; }
  footer img[alt="Piekne auta"] { height: 9rem !important; }
}
@media (max-width: 640px) {
  header img[alt="Piekne auta"] { height: 10rem !important; }
  footer img[alt="Piekne auta"] { height: 8rem !important; }
}
</style>
<?php
// Get phone number and email
$phone = salon_auto_get_option('phone', '502 42 82 82');
$phone_clean = preg_replace('/[^0-9]/', '', $phone);
$email = salon_auto_get_option('email', 'biuro@piekneauta.pl');

// Get contact page URL
$contact_page = get_page_by_path('kontakt');
$contact_url = $contact_page ? get_permalink($contact_page->ID) : home_url('/kontakt/');
?>

<main id="main-content" class="min-h-screen">
    <!-- Hero -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32 border-b border-gray-100" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="font-bold text-primary mb-6 tracking-tight hero-title fade-in-up" style="font-size: 48px !important;">
                    <?php echo esc_html(get_option('salon_auto_page_leasing_hero_title', 'Leasing samochodów premium')); ?>
                </h1>
                <div class="w-24 h-1.5 bg-primary mx-auto mb-8 fade-in-up stagger-1 progress-shine"></div>
                <p class="text-lg sm:text-xl md:text-2xl text-gray-600 leading-relaxed font-light mb-8 hero-subtitle fade-in-up stagger-2">
                    <?php echo esc_html(get_option('salon_auto_page_leasing_hero_subtitle', 'Leasing samochodów używanych i nowych dla firm, osób fizycznych i rolników. Leasing konsumencki samochodu używanego, leasing samochodu premium dla firmy. Najlepsze warunki leasingu operacyjnego i finansowego.')); ?>
                </p>
                <div class="flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-4 justify-center items-center hero-content">
                    <div class="flex items-center space-x-2 text-sm sm:text-base fade-in-up stagger-1 animate-on-scroll">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-success flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span><?php echo esc_html(get_option('salon_auto_page_leasing_hero_check1', 'Dla firm, osób fizycznych i rolników')); ?></span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm sm:text-base fade-in-up stagger-2 animate-on-scroll">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-success flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span><?php echo esc_html(get_option('salon_auto_page_leasing_hero_check2', 'Nowe i używane')); ?></span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm sm:text-base fade-in-up stagger-3 animate-on-scroll">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-success flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-center sm:text-left whitespace-normal"><?php echo esc_html(get_option('salon_auto_page_leasing_hero_check3', 'Przyspieszona procedura')); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary mb-3 sm:mb-4 fade-in-up">
                    <?php echo esc_html(get_option('salon_auto_page_leasing_benefits_title', 'Leasing samochodów premium – dla firm i osób fizycznych')); ?>
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto fade-in-up stagger-1">
                    <?php echo esc_html(get_option('salon_auto_page_leasing_benefits_subtitle', 'Korzyści finansowania leasingowego dla samochodów używanych i nowych')); ?>
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center bg-white p-8 rounded-2xl shadow-sm border border-gray-100 card-animate hover-lift fade-in-up stagger-1 animate-on-scroll">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-gray-200">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/><path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-primary mb-2"><?php echo esc_html(get_option('salon_auto_page_leasing_benefit1_title', 'Korzyści podatkowe')); ?></h3>
                    <p class="text-sm text-gray-600">
                        <?php echo esc_html(get_option('salon_auto_page_leasing_benefit1_text', 'Raty i odsetki w koszty firmy, odliczenie VAT')); ?>
                    </p>
                </div>
                <div class="text-center bg-white p-8 rounded-2xl shadow-sm border border-gray-100 card-animate hover-lift fade-in-up stagger-2 animate-on-scroll">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-gray-200">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-primary mb-2"><?php echo esc_html(get_option('salon_auto_page_leasing_benefit2_title', 'Płynność finansowa')); ?></h3>
                    <p class="text-sm text-gray-600">
                        <?php echo esc_html(get_option('salon_auto_page_leasing_benefit2_text', 'Niski wkład własny, zachowanie kapitału obrotowego')); ?>
                    </p>
                </div>
                <div class="text-center bg-white p-8 rounded-2xl shadow-sm border border-gray-100 card-animate hover-lift fade-in-up stagger-3 animate-on-scroll">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-gray-200">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-primary mb-2"><?php echo esc_html(get_option('salon_auto_page_leasing_benefit3_title', 'Szybka decyzja')); ?></h3>
                    <p class="text-sm text-gray-600">
                        <?php echo esc_html(get_option('salon_auto_page_leasing_benefit3_text', 'Wstępna akceptacja w 24 godziny, finalizacja w 2-3 dni')); ?>
                    </p>
                </div>
                <div class="text-center bg-white p-8 rounded-2xl shadow-sm border border-gray-100 card-animate hover-lift fade-in-up stagger-4 animate-on-scroll">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-gray-200">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-primary mb-2"><?php echo esc_html(get_option('salon_auto_page_leasing_benefit4_title', 'Elastyczność')); ?></h3>
                    <p class="text-sm text-gray-600">
                        <?php echo esc_html(get_option('salon_auto_page_leasing_benefit4_text', 'Dopasowanie parametrów do twoich potrzeb')); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Partner Link -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-4xl mx-auto text-center bg-white rounded-2xl p-8 shadow-sm border border-gray-100 card-animate hover-lift fade-in-up animate-on-scroll">
                <h2 class="text-2xl sm:text-3xl font-bold text-primary mb-4 fade-in-up">
                    <?php echo esc_html(get_option('salon_auto_page_leasing_partner_title', 'Więcej informacji o leasingu')); ?>
                </h2>
                <p class="text-lg text-gray-600 mb-6">
                    <?php echo esc_html(get_option('salon_auto_page_leasing_partner_text', 'Zapraszamy do odwiedzenia strony Związku Polskiego Leasingu, gdzie znajdziesz wszelkie informacje na temat tej formy finansowania oraz szczegółowe informacje o leasingu.')); ?>
                </p>
                <a href="<?php echo esc_url(get_option('salon_auto_page_leasing_partner_link_url', 'https://leasing.org.pl/')); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center space-x-2 bg-primary text-white hover:bg-secondary px-8 py-4 rounded-xl font-semibold transition-all shadow-lg transform hover:scale-[1.02]">
                    <span><?php echo esc_html(get_option('salon_auto_page_leasing_partner_link_text', 'Odwiedź leasing.org.pl')); ?></span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary mb-3 sm:mb-4 fade-in-up">
                    <?php echo esc_html(get_option('salon_auto_page_leasing_how_title', 'Jak to działa?')); ?>
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto fade-in-up stagger-1">
                    <?php echo esc_html(get_option('salon_auto_page_leasing_how_subtitle', 'Proces leasingu w 4 prostych krokach')); ?>
                </p>
            </div>
            <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-8">
                <div class="bg-white rounded-2xl p-8 shadow-md border-l-4 border-accent hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 card-animate fade-in-up stagger-1">
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 bg-gradient-to-br from-accent to-secondary rounded-2xl flex items-center justify-center font-bold text-3xl text-white shadow-lg">
                                1
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-primary mb-3"><?php echo esc_html(get_option('salon_auto_page_leasing_step1_title', 'WYBIERZMY SAMOCHÓD')); ?></h3>
                            <p class="text-gray-600 leading-relaxed">
                                <?php echo esc_html(get_option('salon_auto_page_leasing_step1_text', 'Wybierz pojazd z naszej oferty lub poproś o sprowadzenie konkretnego modelu.')); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-md border-l-4 border-accent hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 card-animate fade-in-up stagger-2">
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 bg-gradient-to-br from-accent to-secondary rounded-2xl flex items-center justify-center font-bold text-3xl text-white shadow-lg">
                                2
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-primary mb-3"><?php echo esc_html(get_option('salon_auto_page_leasing_step2_title', 'Złóż wniosek')); ?></h3>
                            <p class="text-gray-600 leading-relaxed">
                                <?php echo esc_html(get_option('salon_auto_page_leasing_step2_text', 'Wypełnij prosty wniosek leasingowy - pomożemy Tobie w tym procesie.')); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-md border-l-4 border-accent hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 card-animate fade-in-up stagger-3">
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 bg-gradient-to-br from-accent to-secondary rounded-2xl flex items-center justify-center font-bold text-3xl text-white shadow-lg">
                                3
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-primary mb-3"><?php echo esc_html(get_option('salon_auto_page_leasing_step3_title', 'DECYZJA W 24 godziny')); ?></h3>
                            <p class="text-gray-600 leading-relaxed">
                                <?php echo esc_html(get_option('salon_auto_page_leasing_step3_text', 'Wstępna decyzja w 24 godziny. Po akceptacji podpisujesz umowę leasingową.')); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-md border-l-4 border-accent hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 card-animate fade-in-up stagger-4">
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 bg-gradient-to-br from-accent to-secondary rounded-2xl flex items-center justify-center font-bold text-3xl text-white shadow-lg">
                                4
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-primary mb-3"><?php echo esc_html(get_option('salon_auto_page_leasing_step4_title', 'Odbierz samochód')); ?></h3>
                            <p class="text-gray-600 leading-relaxed">
                                <?php echo esc_html(get_option('salon_auto_page_leasing_step4_text', 'Odbierasz swoje wymarzone auto przygotowane i gotowe do jazdy!')); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Leasing dla osób fizycznych -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary mb-6 text-center whitespace-nowrap fade-in-up">
                    <?php echo esc_html(get_option('salon_auto_page_leasing_private_title', 'Leasing używanego samochodu premium dla osoby prywatnej')); ?>
                </h2>
                <div class="bg-white rounded-2xl p-8 md:p-10 border-l-4 border-accent shadow-sm card-animate hover-lift fade-in-up stagger-1 animate-on-scroll">
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">
                        <?php echo wp_kses_post(get_option('salon_auto_page_leasing_private_text', '<strong>Leasing konsumencki</strong> to coraz popularniejsza forma finansowania samochodów premium używanych dla osób prywatnych. Umożliwia sfinansowanie wymarzonego auta bez angażowania całego kapitału.')); ?>
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-accent flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-gray-700 leading-relaxed">
                                    <?php echo wp_kses_post(get_option('salon_auto_page_leasing_private_check1', '<strong>Leasing na używane auto premium</strong> – oferujemy leasing konsumencki na samochody premium używane do 10 lat (w niektórych przypadkach nawet starsze, jeśli pojazd jest w dobrym stanie).')); ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-accent flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-gray-700 leading-relaxed">
                                    <?php echo wp_kses_post(get_option('salon_auto_page_leasing_private_check2', '<strong>Elastyczne raty</strong> – możliwość dopasowania wysokości rat do swoich możliwości finansowych, z możliwością zmiany parametrów w trakcie trwania umowy.')); ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-accent flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-gray-700 leading-relaxed">
                                    <?php echo wp_kses_post(get_option('salon_auto_page_leasing_private_check3', '<strong>Opcja wykupu</strong> – po zakończeniu leasingu masz możliwość wykupu pojazdu po preferencyjnej cenie, lub zwrotu auta i wyboru nowego modelu.')); ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-accent flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-gray-700 leading-relaxed">
                                    <?php echo wp_kses_post(get_option('salon_auto_page_leasing_private_check4', '<strong>Uproszczona procedura</strong> – leasing konsumencki jest prostszy niż kredyt samochodowy, z mniejszą ilością formalności.')); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form -->
    <section id="formularz" class="py-20 sm:py-24 md:py-32 lg:py-36" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-2xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-2xl sm:text-3xl font-bold text-primary mb-4 fade-in-up">
                        <?php echo esc_html(get_option('salon_auto_page_leasing_form_title', 'Zapytaj o leasing')); ?>
                    </h2>
                    <p class="text-lg text-gray-600 fade-in-up stagger-1">
                        <?php echo esc_html(get_option('salon_auto_page_leasing_form_subtitle', 'Wypełnij formularz, a odezwiemy się do Ciebie w ciągu 24 godziny')); ?>
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
                    <input type="hidden" name="_subject" value="Zapytanie o leasing - piekneauta.pl">
                    <input type="hidden" name="_captcha" value="false">
                    <input type="hidden" name="_template" value="box">
                    
                    <div x-show="!submitted">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Imię i nazwisko <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Telefon
                            </label>
                            <input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Wiadomość <span class="text-danger">*</span>
                            </label>
                            <textarea name="message" rows="5" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent"></textarea>
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
                            <h3 class="text-2xl md:text-3xl font-bold text-primary mb-4">Zapytanie zostało wysłane!</h3>
                            <p class="text-lg text-gray-700 mb-3 font-medium">Dziękujemy za kontakt z <span class="text-primary font-bold">Piękne auta</span>.</p>
                            <p class="text-sm text-gray-500 mb-6">Skontaktujemy się z Tobą w ciągu <strong class="text-primary">24 godzin roboczych</strong>.</p>
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

    <!-- Serdecznie Zapraszamy -->
    <section class="py-20 bg-white fade-in-up">
        <div class="container mx-auto text-center">
            <div class="max-w-3xl mx-auto fade-in-up-delay-1">
                <h2 class="text-2xl md:text-3xl font-bold mb-4" style="color: #212121 !important; text-transform: none !important;">
                    Serdecznie Zapraszamy
                </h2>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="tel:+48<?php echo esc_attr($phone_clean); ?>" class="inline-flex items-center justify-center gap-2 bg-white text-primary border-2 border-primary px-8 py-4 rounded-xl font-semibold shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
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

