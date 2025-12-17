<?php
/**
 * Template Name: Kontakt
 * 
 * Dedykowany szablon dla strony "Kontakt" z pełną treścią
 */

get_header();
?>
<style>
/* Logo - mniejsze na mobile dla podstron */
@media (max-width: 1023px) {
  header img[alt="Piekne auta"] { height: 4rem !important; }
  footer img[alt="Piekne auta"] { height: 4rem !important; }
}
@media (max-width: 768px) {
  header img[alt="Piekne auta"] { height: 3rem !important; }
  footer img[alt="Piekne auta"] { height: 3rem !important; }
}
@media (max-width: 640px) {
  header img[alt="Piekne auta"] { height: 2.5rem !important; }
  footer img[alt="Piekne auta"] { height: 2.5rem !important; }
}
</style>
<?php
// Get phone number and email
$phone = salon_auto_get_option('phone', '502 42 82 82');
$phone_clean = preg_replace('/[^0-9]/', '', $phone);
$email = salon_auto_get_option('email', 'biuro@piekneauta.pl');

// Get privacy policy page URL
$privacy_page = get_page_by_path('polityka-prywatnosci');
$privacy_url = $privacy_page ? get_permalink($privacy_page->ID) : home_url('/polityka-prywatnosci/');
?>

<main class="min-h-screen">
    <!-- Hero -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32 border-b border-gray-100" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto text-center">
            <h1 class="font-bold text-primary mb-6 tracking-tight hero-title fade-in-up" style="font-size: 48px !important;"><?php echo esc_html(get_option('salon_auto_page_kontakt_hero_title', 'Kontakt')); ?></h1>
            <div class="w-24 h-1.5 bg-primary mx-auto mb-8 fade-in-up stagger-1"></div>
            <p class="text-lg sm:text-xl md:text-2xl text-gray-600 font-light hero-subtitle fade-in-up stagger-2">
                <?php echo esc_html(get_option('salon_auto_page_kontakt_hero_subtitle', 'Chętnie odpowiemy na wszystkie Twoje pytania')); ?>
            </p>
        </div>
    </section>

    <!-- Contact Info + Form -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-2 gap-8 sm:gap-10 lg:gap-12">
                <!-- Contact Info -->
                <div class="fade-in-left animate-on-scroll">
                    <h2 class="text-2xl sm:text-3xl font-bold text-primary mb-8 fade-in-up"><?php echo esc_html(get_option('salon_auto_page_kontakt_info_title', 'Dane kontaktowe')); ?></h2>
                    <div class="space-y-6 mb-8">
                        <div class="flex items-start space-x-4 card-animate hover-lift fade-in-up stagger-1 animate-on-scroll">
                            <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center flex-shrink-0 border-2 border-gray-200">
                                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Telefon</h3>
                                <a href="tel:+48<?php echo esc_attr($phone_clean); ?>" class="text-primary hover:text-secondary text-xl font-bold">
                                    <?php echo esc_html($phone); ?>
                                </a>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4 card-animate hover-lift fade-in-up stagger-2 animate-on-scroll">
                            <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center flex-shrink-0 border-2 border-gray-200">
                                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">email</h3>
                                <a href="mailto:<?php echo esc_attr($email); ?>" class="text-primary hover:text-secondary">
                                    <?php echo esc_html($email); ?>
                                </a>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4 card-animate hover-lift fade-in-up stagger-3 animate-on-scroll">
                            <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center flex-shrink-0 border-2 border-gray-200">
                                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/><path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Dane firmy</h3>
                                <p class="text-gray-700">
                                    <?php 
                                    $footer_copyright_name = salon_auto_get_option('footer_copyright_name', 'Artur Kurzydłowski');
                                    echo esc_html($footer_copyright_name); 
                                    ?><br>
                                    <?php 
                                    $company_nip = salon_auto_get_option('company_nip', '6731525915');
                                    $company_regon = salon_auto_get_option('company_regon', '330558443');
                                    if ($company_nip) echo 'NIP: ' . esc_html($company_nip); 
                                    if ($company_nip && $company_regon) echo '<br>';
                                    if ($company_regon) echo 'REGON: ' . esc_html($company_regon);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-6 mb-8 card-animate hover-lift fade-in-up stagger-4 animate-on-scroll">
                        <h3 class="font-semibold text-gray-900 mb-4"><?php echo esc_html(get_option('salon_auto_page_kontakt_hours_title', 'Godziny kontaktu')); ?></h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600"><?php echo esc_html(get_option('salon_auto_page_kontakt_hours_mon_fri', 'Poniedziałek - Piątek:')); ?></span>
                                <span class="font-semibold text-gray-900"><?php echo esc_html(get_option('salon_auto_page_kontakt_hours_mon_fri_time', '9:00 - 18:00')); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600"><?php echo esc_html(get_option('salon_auto_page_kontakt_hours_sat', 'Sobota:')); ?></span>
                                <span class="font-semibold text-gray-900"><?php echo esc_html(get_option('salon_auto_page_kontakt_hours_sat_time', '9:00 - 18:00')); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600"><?php echo esc_html(get_option('salon_auto_page_kontakt_hours_sun', 'Niedziela:')); ?></span>
                                <span class="font-semibold text-gray-900"><?php echo esc_html(get_option('salon_auto_page_kontakt_hours_sun_time', '9:00 - 18:00')); ?></span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-4">
                            <?php echo esc_html(get_option('salon_auto_page_kontakt_hours_note', '* Wizyty poza godzinami otwarcia możliwe po wcześniejszym umówieniu')); ?>
                        </p>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="fade-in-right animate-on-scroll">
                    <h2 class="text-2xl sm:text-3xl font-bold text-primary mb-8 fade-in-up"><?php echo esc_html(get_option('salon_auto_page_kontakt_form_title', 'Napisz do nas')); ?></h2>
                    <form id="contact-form" action="https://formsubmit.co/<?php echo esc_attr($email); ?>" method="POST" class="space-y-6 bg-white rounded-2xl p-8 shadow-sm border border-gray-100 scale-in animate-on-scroll" x-data="{ submitted: false, error: false, loading: false, hideTimeout: null }" x-init="submitted = false; error = false; loading = false;" @submit.prevent="
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
                            // Scroll to success message
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
                        <!-- Hidden fields for formsubmit.co -->
                        <input type="hidden" name="_subject" value="Nowa wiadomość z formularza kontaktowego - piekneauta.pl">
                        <input type="hidden" name="_captcha" value="false">
                        <input type="hidden" name="_template" value="box">
                        <input type="hidden" name="_next" value="<?php echo esc_url(home_url('/kontakt/?success=1')); ?>">
                        
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
                                    NIP <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nip" required pattern="[0-9]{10}" minlength="10" maxlength="10" placeholder="0000000000" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                                <p class="text-xs text-gray-500 mt-1">Podaj 10-cyfrowy numer NIP</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Telefon
                                </label>
                                <input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Temat
                                </label>
                                <select name="subject" id="subject" x-on:change="
                                    const carSelectDiv = document.getElementById('car-select-container');
                                    const carInfoDiv = document.getElementById('car-info');
                                    const selectedValue = $event.target.value;
                                    const showCarSelect = ['Rezerwacja samochodu', 'Pytanie o konkretne auto', 'Leasing', 'Rezerwacja auta'].includes(selectedValue);
                                    
                                    if (carSelectDiv) {
                                      carSelectDiv.style.display = showCarSelect ? 'block' : 'none';
                                    }
                                    
                                    if (carInfoDiv) {
                                      carInfoDiv.style.display = 'none';
                                    }
                                  " class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                                    <option value="Ogólne zapytanie">Ogólne zapytanie</option>
                                    <option value="Rezerwacja samochodu">Rezerwacja samochodu</option>
                                    <option value="Pytanie o konkretne auto">Pytanie o konkretne auto</option>
                                    <option value="Leasing">Leasing</option>
                                    <option value="Rezerwacja auta">Rezerwacja auta</option>
                                    <option value="Ubezpieczenia">Ubezpieczenia</option>
                                    <option value="Inne">Inne</option>
                                </select>
                            </div>
                            <div id="car-select-container" style="display: none;" class="animate-fadeIn">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Interesujące cię auto (opcjonalnie)
                                </label>
                                <select name="car" id="car-select" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                                    <option value="">Ładowanie listy aut...</option>
                                </select>
                            </div>
                            <div id="car-info" style="display: none;" class="bg-accent/5 border-2 border-accent/30 rounded-2xl p-6 animate-fadeIn">
                                <div class="flex items-start space-x-3 mb-3">
                                    <svg class="w-6 h-6 text-accent flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-accent mb-1">Rezerwacja samochodu</h3>
                                        <p class="text-sm text-gray-600">Skontaktujemy się z Tobą w ciągu 24h w celu omówienia szczegółów</p>
                                    </div>
                                </div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Wybrany samochód
                                </label>
                                <input type="text" name="car" id="car-name" readonly class="w-full px-4 py-3 border-2 border-accent/50 bg-white rounded-xl text-gray-900 font-bold text-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Wiadomość <span class="text-danger">*</span>
                                </label>
                                <textarea name="message" rows="5" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent"></textarea>
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
                            <button type="submit" x-bind:disabled="loading" class="mt-8 inline-flex items-center justify-center font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 bg-primary text-white hover:bg-secondary focus:ring-accent px-8 py-4 text-lg rounded-xl w-full justify-center shadow-lg">
                                <span x-show="!loading">Wyślij wiadomość</span>
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
                                <h3 class="text-2xl md:text-3xl font-bold text-primary mb-4">Wiadomość została wysłana pomyślnie!</h3>
                                <p class="text-lg text-gray-700 mb-3 font-medium">Dziękujemy za kontakt z <span class="text-primary font-bold">Piękne auta</span>.</p>
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
                            <p class="text-red-700 font-semibold mb-1">Wystąpił błąd podczas wysyłania wiadomości.</p>
                            <p class="text-sm text-red-600">Spróbuj ponownie lub skontaktuj się bezpośrednio: <a href="tel:+48<?php echo esc_attr($phone_clean); ?>" class="underline"><?php echo esc_html($phone); ?></a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Map -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold fade-in-up text-primary mb-4">Odwiedź nas</h2>
                <p class="text-gray-600">Hala ekspozycyjna · 53.70573, 16.69825 (Szczecinek)</p>
            </div>
            <div class="max-w-4xl mx-auto rounded-xl overflow-hidden" style="height: 500px !important; min-height: 500px !important;">
                <iframe src="https://maps.google.com/maps?q=53.70573,16.69825&z=14&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full" style="height: 500px !important; min-height: 500px !important; border: none;"></iframe>
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
                    <a href="#contact-form" class="inline-flex items-center justify-center gap-2 bg-white text-primary border-2 border-primary px-8 py-4 rounded-xl font-semibold shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
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

<script>
// Auto-fill reservation form from URL parameters OR localStorage
(function() {
  'use strict';
  
  function initContactForm() {
    // Najpierw sprawdź localStorage (nowa metoda - bardziej niezawodna)
    let action = null;
    let car = null;
    
    try {
      const storedData = localStorage.getItem('salon_auto_contact_data');
      if (storedData) {
        const data = JSON.parse(storedData);
        action = data.action;
        car = data.name;
        // Usuń dane z localStorage po odczytaniu
        localStorage.removeItem('salon_auto_contact_data');
        console.log('Dane z localStorage:', { action: action, car: car });
      }
    } catch(e) {
      console.warn('Błąd odczytu localStorage:', e);
    }
    
    // Fallback: sprawdź parametry URL (stara metoda - dla kompatybilności)
    if (!action || !car) {
    const urlParams = new URLSearchParams(window.location.search);
      action = action || urlParams.get('action');
      car = car || urlParams.get('car');
    }
    
    // Debug - sprawdź czy parametry są odczytane
    if (action || car) {
      console.log('Parametry (URL lub localStorage):', { action: action, car: car });
    }
    
    const subjectSelect = document.getElementById('subject');
    const carSelectContainer = document.getElementById('car-select-container');
    const carSelect = document.getElementById('car-select');
    const carInfo = document.getElementById('car-info');
    
    if (!subjectSelect) {
      console.warn('Nie znaleziono selecta subject');
      return;
    }
    
    // Obsługa parametrów URL
    if (action === 'rezerwacja' || action === 'napisz') {
      console.log('Obsługuję akcję:', action);
      
      // Ustaw temat
      let subjectValue = '';
      if (action === 'rezerwacja') {
        subjectValue = 'Rezerwacja samochodu';
      } else if (action === 'napisz') {
        subjectValue = 'Pytanie o konkretne auto';
      }
      
      if (subjectValue) {
        subjectSelect.value = subjectValue;
        console.log('Ustawiono temat:', subjectValue);
        
        // Ręcznie wywołaj logikę (pokazanie selecta z samochodami)
        const showCarSelect = ['Rezerwacja samochodu', 'Pytanie o konkretne auto', 'Leasing', 'Rezerwacja auta'].includes(subjectValue);
        if (carSelectContainer) {
          carSelectContainer.style.display = showCarSelect ? 'block' : 'none';
          console.log('Pokazano select z samochodami:', showCarSelect);
        }
        if (carInfo) {
          carInfo.style.display = 'none';
        }
        
        // Wywołaj również event change dla Alpine.js
        try {
          subjectSelect.dispatchEvent(new Event('change', { bubbles: true }));
        } catch(e) {
          console.warn('Błąd przy wywoływaniu eventu change:', e);
        }
      }
    
      // Ustaw samochód - czekaj na załadowanie opcji
      if (car && carSelect) {
        console.log('🚗 Próbuję ustawić samochód z localStorage:', car);
        // Nie dekoduj jeśli już jest stringiem (z localStorage) - localStorage już ma dekodowane wartości
        const decodedCar = (typeof car === 'string' && !car.includes('%')) ? car.trim() : decodeURIComponent(car).trim();
        console.log('🚗 Zdekodowana nazwa samochodu:', decodedCar);
        let attempts = 0;
        const maxAttempts = 100; // 100 prób = 20 sekund (200ms * 100) - zwiększone dla pewności
        
        // Funkcja normalizująca tekst (usuwa wielokrotne spacje, trim)
        function normalizeText(text) {
          return text.trim().toLowerCase().replace(/\s+/g, ' ');
        }
        
        // Funkcja do ustawienia wartości selecta
        function setCarValue() {
          attempts++;
          console.log(`🔄 Próba ${attempts}/${maxAttempts} ustawienia samochodu:`, decodedCar);
          
          // Sprawdź czy select istnieje i jest widoczny
          if (!carSelect || !carSelectContainer) {
            console.warn('⚠️ Select nie istnieje');
            return;
          }
          
          // Upewnij się że select jest widoczny
          if (carSelectContainer.style.display === 'none') {
            carSelectContainer.style.display = 'block';
          }
          
          // Sprawdź czy opcje są już załadowane (nie ma "Ładowanie listy aut...")
          const hasLoadingText = carSelect.options.length > 0 && 
                                 (carSelect.options[0].textContent.includes('Ładowanie') || 
                                  carSelect.options[0].textContent.includes('Błąd') ||
                                  carSelect.options[0].textContent.includes('Loading'));
          const hasValidOptions = carSelect.options.length > 1 && 
                                  !carSelect.options[1].textContent.includes('Ładowanie') &&
                                  !carSelect.options[1].textContent.includes('Błąd') &&
                                  !carSelect.options[1].textContent.includes('Loading');
          
          console.log('📊 Status opcji:', {
            totalOptions: carSelect.options.length,
            hasLoadingText: hasLoadingText,
            hasValidOptions: hasValidOptions,
            firstOption: carSelect.options[0] ? carSelect.options[0].textContent : 'brak'
          });
          
          if (hasLoadingText || !hasValidOptions) {
            // Opcje jeszcze nie załadowane
            if (attempts < maxAttempts) {
              setTimeout(setCarValue, 200);
              return;
            } else {
              // Timeout - dodaj jako nową opcję OD RAZU (nie czekaj dalej)
              console.warn('⏱️ Timeout podczas ładowania opcji, dodaję samochód ręcznie:', decodedCar);
              // Wyczyść select jeśli ma tylko "Ładowanie..."
              if (carSelect.options.length === 1 && (carSelect.options[0].textContent.includes('Ładowanie') || carSelect.options[0].textContent.includes('Loading') || carSelect.options[0].textContent.includes('Błąd'))) {
                carSelect.innerHTML = '';
              }
              // Sprawdź czy opcja już nie istnieje
              let optionExists = false;
              for (let i = 0; i < carSelect.options.length; i++) {
                if (carSelect.options[i].value === decodedCar || carSelect.options[i].textContent === decodedCar) {
                  carSelect.selectedIndex = i;
                  optionExists = true;
                  console.log('✅ Znaleziono istniejącą opcję:', decodedCar);
                  break;
                }
              }
              if (!optionExists) {
              const newOption = document.createElement('option');
              newOption.value = decodedCar;
              newOption.textContent = decodedCar;
              newOption.selected = true;
                carSelect.insertBefore(newOption, carSelect.firstChild);
                console.log('✅ Dodano samochód ręcznie jako nową opcję:', decodedCar);
              }
              if (carSelectContainer) {
                carSelectContainer.style.display = 'block';
              }
              return;
            }
          }
          
          // Opcje załadowane - szukaj pasującej opcji
          let found = false;
          const normalizedDecodedCar = normalizeText(decodedCar);
          console.log('🔍 Szukam pasującej opcji dla:', decodedCar, '(znormalizowane:', normalizedDecodedCar, ')');
          
          for (let i = 0; i < carSelect.options.length; i++) {
            const option = carSelect.options[i];
            if (!option.value || option.value === '') continue; // Pomiń opcję domyślną
            
            const optionValue = normalizeText(option.value);
            const optionText = normalizeText(option.textContent);
            
            console.log(`  Opcja ${i}:`, option.value, '|', option.textContent);
            
            // Dokładne dopasowanie (wartość lub tekst)
            if (optionValue === normalizedDecodedCar || optionText === normalizedDecodedCar) {
              carSelect.selectedIndex = i;
              found = true;
              console.log('✅ Znaleziono dokładnie pasującą opcję:', option.textContent);
              break;
            }
            
            // Sprawdź czy zawiera pełną nazwę (case-insensitive)
            if (optionValue.includes(normalizedDecodedCar) || normalizedDecodedCar.includes(optionValue) ||
                optionText.includes(normalizedDecodedCar) || normalizedDecodedCar.includes(optionText)) {
              carSelect.selectedIndex = i;
              found = true;
              console.log('✅ Znaleziono opcję zawierającą nazwę:', option.textContent);
              break;
            }
            
            // Częściowe dopasowanie (sprawdź czy zawiera kluczowe słowa)
            const decodedWords = normalizedDecodedCar.split(' ').filter(w => w.length > 2);
            const optionWords = optionText.split(' ').filter(w => w.length > 2);
            const matchCount = decodedWords.filter(word => optionWords.includes(word)).length;
            
            // Jeśli większość słów się zgadza, to prawdopodobnie ten samochód
            if (decodedWords.length > 0 && matchCount >= Math.min(decodedWords.length, optionWords.length) * 0.6) {
              carSelect.selectedIndex = i;
              found = true;
              console.log('✅ Znaleziono częściowo pasującą opcję (60%+ słów):', option.textContent);
              break;
            }
          }
          
          // Jeśli nie znaleziono, dodaj jako nową opcję i wybierz
          if (!found && decodedCar) {
            console.warn('⚠️ Nie znaleziono pasującej opcji w selectcie, dodaję ręcznie:', decodedCar);
            // Wyczyść opcję "Wybierz auto (opcjonalnie)" lub "Ładowanie..." jeśli istnieje
            if (carSelect.options.length > 0) {
              const firstOption = carSelect.options[0];
              if (firstOption.value === '' || firstOption.textContent.includes('Ładowanie') || firstOption.textContent.includes('Loading')) {
              carSelect.remove(0);
              }
            }
            const newOption = document.createElement('option');
            newOption.value = decodedCar;
            newOption.textContent = decodedCar;
            newOption.selected = true;
            carSelect.insertBefore(newOption, carSelect.firstChild);
            console.log('✅ Dodano samochód jako nową opcję:', decodedCar);
          } else if (found) {
            console.log('✅ Ustawiono samochód w selectcie:', decodedCar);
          }
          
          // Pokaż kontener z selectem
          if (carSelectContainer) {
            carSelectContainer.style.display = 'block';
          }
        }
        
        // Wypełnij wiadomość gotowym tekstem OD RAZU (nie czekaj na select)
        function fillMessage() {
          const messageTextarea = document.querySelector('textarea[name="message"]');
          if (messageTextarea && !messageTextarea.value && car) {
            const actionText = action === 'rezerwacja' ? 'rezerwacją' : 'pytaniem o';
            const messageText = `Witam,\n\nJestem zainteresowany/a ${actionText} samochodu: ${decodedCar}.\n\nProszę o kontakt w celu omówienia szczegółów.\n\nPozdrawiam`;
            messageTextarea.value = messageText;
            console.log('✅ Wypełniono wiadomość gotowym tekstem');
          }
        }
        
        // Wypełnij wiadomość od razu
        fillMessage();
        // I jeszcze raz po krótkim czasie (na wypadek gdyby textarea nie był jeszcze w DOM)
        setTimeout(fillMessage, 100);
        setTimeout(fillMessage, 300);
        
        // Rozpocznij próby ustawienia wartości - poczekaj na załadowanie DOM i skryptów
        // Użyj MutationObserver do wykrycia gdy opcje się załadują
        let observer = null;
        if (typeof MutationObserver !== 'undefined' && carSelect) {
          observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
              if (mutation.type === 'childList' && carSelect.options.length > 1) {
                const firstOption = carSelect.options[0];
                if (firstOption && !firstOption.textContent.includes('Ładowanie') && !firstOption.textContent.includes('Loading') && !firstOption.textContent.includes('Błąd')) {
                  console.log('👀 Wykryto załadowanie opcji przez MutationObserver');
                  if (observer) observer.disconnect();
                  setCarValue();
                }
              }
            });
          });
          
          // Obserwuj zmiany w selectcie
          observer.observe(carSelect, { childList: true, subtree: true });
        }
        
        // Rozpocznij próby ustawienia wartości (również bez MutationObserver jako fallback)
        // Uruchom szybciej - pierwsza próba już po 50ms
        setTimeout(setCarValue, 50);
        setTimeout(setCarValue, 150);
        setTimeout(setCarValue, 300);
        setTimeout(setCarValue, 600);
        setTimeout(setCarValue, 1000);
        setTimeout(setCarValue, 2000);
        
        // Dodatkowo: nasłuchuj na event gdy select się zmieni (gdy opcje się załadują)
        if (carSelect) {
          const checkInterval = setInterval(function() {
            if (carSelect.options.length > 1) {
              const firstOption = carSelect.options[0];
              if (firstOption && !firstOption.textContent.includes('Ładowanie') && !firstOption.textContent.includes('Loading')) {
                clearInterval(checkInterval);
                if (observer) observer.disconnect();
                setCarValue();
              }
            }
          }, 100); // Szybsze sprawdzanie - co 100ms (było 150ms)
          
          // Zatrzymaj po 8 sekundach (zmniejszone z 10)
          setTimeout(function() {
            clearInterval(checkInterval);
            if (observer) observer.disconnect();
          }, 8000);
        }
      }
      
      // Scroll do formularza po załadowaniu - z offsetem dla sticky header
      setTimeout(function() {
        const formElement = document.querySelector('form[action*="formsubmit"]');
        if (formElement) {
          const headerHeight = document.querySelector('header')?.offsetHeight || 100;
          const elementPosition = formElement.getBoundingClientRect().top + window.pageYOffset;
          const offsetPosition = elementPosition - headerHeight - 20;
          
          window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
          });
        }
      }, 1000);
    }
  }
  
  // Uruchom po załadowaniu DOM
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initContactForm);
  } else {
    // DOM już załadowany, ale poczekaj chwilę na Alpine.js i inne skrypty
    setTimeout(initContactForm, 100);
  }
})();
</script>

<?php get_footer(); ?>

