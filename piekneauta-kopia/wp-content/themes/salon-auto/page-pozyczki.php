<?php
/**
 * Template Name: Pożyczki
 *
 * This template is used to display the "Pożyczki" page.
 * It directly embeds the static HTML content from the original site,
 * including the contact form.
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
    <section class="py-12 sm:py-16 md:py-20 lg:py-24 border-b border-gray-100" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="font-bold text-primary mb-6 tracking-tight hero-title fade-in-up" style="font-size: 48px !important;">
                    <?php echo esc_html(get_option('salon_auto_page_pozyczki_hero_title', 'Pożyczki i finansowanie dla firm')); ?>
                </h1>
                <div class="w-24 h-1.5 bg-primary mx-auto mb-8 progress-shine"></div>
                <p class="text-lg sm:text-xl md:text-2xl text-gray-600 leading-relaxed font-light mb-8 hero-subtitle fade-in-up stagger-2">
                    <?php echo esc_html(get_option('salon_auto_page_pozyczki_hero_subtitle', 'Finansowanie nie może być barierą ani blokadą. Potrzebujesz kapitału dostępnego wtedy, gdy pojawia się okazja, a nie kilka tygodni po niej.')); ?>
                </p>
                <div class="grid grid-cols-2 sm:flex sm:flex-row sm:flex-wrap gap-2 sm:gap-4 justify-center items-center hero-content">
                    <div class="flex items-center space-x-2 text-sm sm:text-base fade-in-up stagger-1 animate-on-scroll">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-success flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span><?php echo esc_html(get_option('salon_auto_page_pozyczki_hero_check1', 'Decyzja w 24h')); ?></span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm sm:text-base fade-in-up stagger-2 animate-on-scroll">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-success flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span><?php echo esc_html(get_option('salon_auto_page_pozyczki_hero_check2', 'Środki od razu')); ?></span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm sm:text-base fade-in-up stagger-3 animate-on-scroll col-span-2 sm:col-span-1">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-success flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-center sm:text-left whitespace-normal"><?php echo esc_html(get_option('salon_auto_page_pozyczki_hero_check3', 'Minimum 6 miesięcy działalności')); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dla kogo -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary mb-3 sm:mb-4 fade-in-up">
                    <?php echo esc_html(get_option('salon_auto_page_pozyczki_who_title', 'Dla kogo jest nasze finansowanie?')); ?>
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto fade-in-up stagger-1">
                    <?php echo esc_html(get_option('salon_auto_page_pozyczki_who_subtitle', 'Znasz ten moment, kiedy rozwój przyspiesza? Rozwijasz firmę, zdobywasz nowe kontrakty, wchodzisz na większe wolumeny — i wiesz, że teraz trzeba działać szybko.')); ?>
                </p>
            </div>
            <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-6 md:gap-8 items-center">
                <!-- Lewa strona - Zdjęcie -->
                <div class="fade-in-left parallax-slow order-2 md:order-1">
                    <?php 
                    $who_image_id = get_option('salon_auto_page_pozyczki_who_image', '');
                    if ($who_image_id) {
                        echo wp_get_attachment_image($who_image_id, 'large', false, array('class' => 'w-full h-auto max-w-[90%] sm:max-w-[80%] mx-auto rounded-2xl shadow-sm border border-gray-200', 'alt' => 'Finansowanie dla firm'));
                    } else {
                        echo '<img src="' . esc_url(get_stylesheet_directory_uri() . '/assets/images/nowe.jpg') . '" alt="Finansowanie dla firm" class="w-full h-auto max-w-[90%] sm:max-w-[80%] mx-auto rounded-2xl shadow-sm border border-gray-200">';
                    }
                    ?>
                </div>
                <!-- Prawa strona - Lista -->
                <div class="space-y-4 md:space-y-6 fade-in-right order-1 md:order-2">
                    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 card-animate hover-lift animate-on-scroll stagger-1">
                        <h3 class="text-lg sm:text-xl font-bold text-primary mb-2"><?php echo esc_html(get_option('salon_auto_page_pozyczki_who_card1_title', 'Duże zlecenia')); ?></h3>
                        <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                            <?php echo esc_html(get_option('salon_auto_page_pozyczki_who_card1_text', 'Masz szansę na realizację dużego zlecenia, ale zanim klient zapłaci, musisz wyłożyć środki na produkcję lub zatowarowanie.')); ?>
                        </p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 card-animate hover-lift animate-on-scroll stagger-2">
                        <h3 class="text-lg sm:text-xl font-bold text-primary mb-2"><?php echo esc_html(get_option('salon_auto_page_pozyczki_who_card2_title', 'Rozwój firmy')); ?></h3>
                        <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                            <?php echo esc_html(get_option('salon_auto_page_pozyczki_who_card2_text', 'Chcesz zwiększyć moce produkcyjne, dokupić maszyny, rozbudować flotę lub zainwestować w nową halę.')); ?>
                        </p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 card-animate hover-lift animate-on-scroll stagger-3">
                        <h3 class="text-lg sm:text-xl font-bold text-primary mb-2"><?php echo esc_html(get_option('salon_auto_page_pozyczki_who_card3_title', 'Budowa zespołu')); ?></h3>
                        <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                            <?php echo esc_html(get_option('salon_auto_page_pozyczki_who_card3_text', 'Budujesz zespół, bo pojawia się więcej projektów i nowych klientów.')); ?>
                        </p>
                    </div>
                    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 card-animate hover-lift animate-on-scroll stagger-4">
                        <h3 class="text-lg sm:text-xl font-bold text-primary mb-2"><?php echo esc_html(get_option('salon_auto_page_pozyczki_who_card4_title', 'Sezonowy wzrost')); ?></h3>
                        <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                            <?php echo esc_html(get_option('salon_auto_page_pozyczki_who_card4_text', 'Przygotowujesz firmę na sezonowy wzrost sprzedaży i chcesz mieć gotowy magazyn, zanim ruszy konkurencja.')); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Oferujemy kredyty -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary mb-3 sm:mb-4 fade-in-up">
                    <?php echo esc_html(get_option('salon_auto_page_pozyczki_offer_title', 'Oferujemy kredyty firmom, które chcą rosnąć')); ?>
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto fade-in-up stagger-1">
                    <?php echo esc_html(get_option('salon_auto_page_pozyczki_offer_subtitle', 'Finansowanie nie może być barierą ani blokadą. Potrzebujesz kapitału dostępnego wtedy, gdy pojawia się okazja, a nie kilka tygodni po niej.')); ?>
                </p>
            </div>
            <div class="max-w-4xl mx-auto">
                <div class="bg-primary text-white rounded-xl p-5 sm:p-6 text-center shadow-sm max-w-2xl mx-auto scale-in">
                    <p class="text-base sm:text-lg md:text-xl font-semibold leading-relaxed text-white">
                        <?php echo wp_kses_post(get_option('salon_auto_page_pozyczki_offer_text', 'Dla firm działających od <span class="font-bold underline" style="color: #ffffff !important;">minimum 6 miesięcy</span>.')); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Jak to działa -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary mb-3 sm:mb-4 fade-in-up">
                    <?php echo esc_html(get_option('salon_auto_page_pozyczki_how_title', 'Zdobądź środki dla swojej firmy w 3 prostych krokach')); ?>
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto fade-in-up stagger-1">
                    <?php echo esc_html(get_option('salon_auto_page_pozyczki_how_subtitle', 'Robimy wszystko, aby ułatwić Ci cały proces.')); ?>
                </p>
            </div>
            <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-6 md:gap-8">
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-md border-l-4 border-accent hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 card-animate fade-in-up stagger-1">
                    <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-accent to-secondary rounded-2xl flex items-center justify-center font-bold text-2xl sm:text-3xl text-white shadow-lg mx-auto sm:mx-0">
                                1
                            </div>
                        </div>
                        <div class="text-center sm:text-left">
                            <h3 class="text-lg sm:text-xl font-bold text-primary mb-2 sm:mb-3"><?php echo esc_html(get_option('salon_auto_page_pozyczki_step1_title', 'Wniosek online')); ?></h3>
                            <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                                <?php echo esc_html(get_option('salon_auto_page_pozyczki_step1_text', 'Wypełniasz formularz w kilka minut.')); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-md border-l-4 border-accent hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 card-animate fade-in-up stagger-2">
                    <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-accent to-secondary rounded-2xl flex items-center justify-center font-bold text-2xl sm:text-3xl text-white shadow-lg mx-auto sm:mx-0">
                                2
                            </div>
                        </div>
                        <div class="text-center sm:text-left">
                            <h3 class="text-lg sm:text-xl font-bold text-primary mb-2 sm:mb-3"><?php echo esc_html(get_option('salon_auto_page_pozyczki_step2_title', 'Weryfikacja i decyzja')); ?></h3>
                            <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                                <?php echo esc_html(get_option('salon_auto_page_pozyczki_step2_text', 'Kontaktujemy się z Tobą – decyzja w 24h.')); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-md border-l-4 border-accent hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 md:col-span-2 card-animate fade-in-up stagger-3">
                    <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-accent to-secondary rounded-2xl flex items-center justify-center font-bold text-2xl sm:text-3xl text-white shadow-lg mx-auto sm:mx-0">
                                3
                            </div>
                        </div>
                        <div class="text-center sm:text-left">
                            <h3 class="text-lg sm:text-xl font-bold text-primary mb-2 sm:mb-3"><?php echo esc_html(get_option('salon_auto_page_pozyczki_step3_title', 'Wypłata środków')); ?></h3>
                            <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                                <?php echo esc_html(get_option('salon_auto_page_pozyczki_step3_text', 'Po podpisaniu umowy – pieniądze od razu na koncie.')); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Formularz -->
    <section id="formularz" class="py-20 sm:py-24 md:py-32 lg:py-36" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-2xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-2xl sm:text-3xl font-bold text-primary mb-4 fade-in-up">
                        <?php echo esc_html(get_option('salon_auto_page_pozyczki_form_title', 'Złóż prosty wniosek o kredyt online dla Twojej firmy')); ?>
                    </h2>
                    <p class="text-lg text-gray-600 fade-in-up stagger-1">
                        <?php echo esc_html(get_option('salon_auto_page_pozyczki_form_subtitle', 'Wypełnij krótki formularz i załóż darmowe konto, dzięki któremu szybko i prosto uzyskasz kredyt dla swojego biznesu!')); ?>
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
                    <input type="hidden" name="_subject" value="Wniosek o kredyt firmowy - piekneauta.pl">
                    <input type="hidden" name="_captcha" value="false">
                    <input type="hidden" name="_template" value="box">
                    <div x-show="!submitted">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Imię <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="firstname" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nazwisko <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="lastname" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                NIP firmy <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nip" required pattern="[0-9]{10}" minlength="10" maxlength="10" placeholder="0000000000" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                            <p class="text-xs text-gray-500 mt-1">Podaj 10-cyfrowy numer NIP</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Numer telefonu <span class="text-danger">*</span>
                            </label>
                            <input type="tel" name="phone" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Adres e-mail <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                        </div>
                        <div class="space-y-3">
                            <label class="flex items-start space-x-3 text-sm">
                                <input type="checkbox" name="privacy" required class="mt-0.5 w-5 h-5 text-accent border-gray-300 rounded focus:ring-2 focus:ring-accent cursor-pointer">
                                <span class="text-gray-700">
                                    Akceptuję <a href="<?php echo esc_url($privacy_url); ?>" class="text-accent hover:underline">politykę prywatności</a>
                                    i wyrażam zgodę na przetwarzanie moich danych osobowych w celu udzielenia odpowiedzi 
                                    na zapytanie <span class="text-danger">*</span>
                                </span>
                            </label>
                        </div>
                        <button type="submit" x-bind:disabled="loading" class="mt-8 inline-flex items-center justify-center font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 bg-primary text-white hover:bg-secondary focus:ring-accent px-8 py-4 text-lg rounded-xl w-full justify-center shadow-lg ripple-button hover-scale animate-transition">
                            <span x-show="!loading">Wyślij wniosek</span>
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
                            <h3 class="text-2xl md:text-3xl font-bold text-primary mb-4">Wniosek został wysłany pomyślnie!</h3>
                            <p class="text-lg text-gray-700 mb-3 font-medium">Dziękujemy za złożenie wniosku w <span class="text-primary font-bold">Piękne auta</span>.</p>
                            <p class="text-sm text-gray-500 mb-6">Skontaktujemy się z Tobą w ciągu <strong class="text-primary">24 godzin roboczych</strong>.</p>
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
                        <p class="text-red-700 font-semibold mb-1">Wystąpił błąd podczas wysyłania wniosku.</p>
                        <p class="text-sm text-red-600">Spróbuj ponownie lub skontaktuj się bezpośrednio: <a href="tel:+48<?php echo esc_attr($phone_clean); ?>" class="underline"><?php echo esc_html($phone); ?></a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="py-24 sm:py-28 md:py-32 lg:py-36 bg-white fade-in-up">
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

