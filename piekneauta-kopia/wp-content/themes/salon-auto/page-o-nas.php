<?php
/**
 * Template Name: O nas
 * 
 * Dedykowany szablon dla strony "O nas" z pełną treścią
 */

get_header();
?>
<style>
/* Logo - większe na mobile dla strony o-nas */
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
// Get contact page URL
$contact_page = get_page_by_path('kontakt');
$contact_url = $contact_page ? get_permalink($contact_page->ID) : home_url('/kontakt/');

// Get phone number
$phone = salon_auto_get_option('phone', '502 42 82 82');
$phone_clean = preg_replace('/[^0-9]/', '', $phone);

// Get editable content for "O nas" page
$page_slug = 'o-nas';
$about_hero_title = get_option('salon_auto_page_' . $page_slug . '_hero_title', '28 lat pasji i doświadczenia');
$about_hero_text = get_option('salon_auto_page_' . $page_slug . '_hero_text', 'Od 1997 roku pomagamy Klientom wybierać najlepsze samochody oraz inne środki trwałe. Każde auto traktujemy jak swoje własne - z pełną odpowiedzialnością i pełnym zaangażowaniem. Jesteśmy autoryzowanym partnerem czołowych firm w Polsce i w naszym portfolio mamy zrealizowanych ponad 10 tysięcy umów leasingu.');
$about_artur_name = get_option('salon_auto_page_' . $page_slug . '_artur_name', 'Artur Kurzydłowski');
$about_artur_text1 = get_option('salon_auto_page_' . $page_slug . '_artur_text1', 'Moje doświadczenia na rynku motoryzacyjnym to prawie 3 dekady aktywnej działalności w branży samochodowej i finansowej. Realizując ponad 10 tysięcy transakcji zbudowałem markę opartą na wiedzy, zaufaniu, rzetelności i indywidualnym podejściu do każdego Klienta.');
$about_artur_text2 = get_option('salon_auto_page_' . $page_slug . '_artur_text2', '28 lat na rynku motoryzacyjnym. Piękne auta to marka zbudowana na rzetelności i pasji do motoryzacji. Każde auto jest sprawdzone do ostatniej śrubki - bez kompromisów.');
$about_artur_text3 = get_option('salon_auto_page_' . $page_slug . '_artur_text3', 'Specjalizujemy się w sprzedaży samochodów marki premium i leasingów. Każdy samochód przechodzi kontrolę techniczną i wydawany jest Klientowi w stanie możliwie perfekcyjnym.');
$about_artur_text4 = get_option('salon_auto_page_' . $page_slug . '_artur_text4', 'Oferuję kompleksową obsługę - od pomocy w wyborze auta, przez finansowanie leasingowe, po ubezpieczenia i pełną dokumentację. Dbam o to, aby proces zakupu był transparentny i bezpieczny.');
$about_artur_cert = get_option('salon_auto_page_' . $page_slug . '_artur_cert', 'Członek Loży Przedsiębiorców i Uczestnik Programu RZETELNA Firma.');
$about_button_text = get_option('salon_auto_page_' . $page_slug . '_button_text', 'Kontakt');
$about_image_id = get_option('salon_auto_page_' . $page_slug . '_about_image', '');
$about_stat1_value = get_option('salon_auto_page_' . $page_slug . '_stat1_value', '28 lat');
$about_stat1_label = get_option('salon_auto_page_' . $page_slug . '_stat1_label', 'doświadczenia');
$about_stat2_value = get_option('salon_auto_page_' . $page_slug . '_stat2_value', '10 000+');
$about_stat2_label = get_option('salon_auto_page_' . $page_slug . '_stat2_label', 'umów leasingowych');
$about_values_title = get_option('salon_auto_page_' . $page_slug . '_values_title', 'Nasze zasady');
$about_values_subtitle = get_option('salon_auto_page_' . $page_slug . '_values_subtitle', '3 filary, na których opiera się nasza działalność');
$about_value1_title = get_option('salon_auto_page_' . $page_slug . '_value1_title', 'Transparentność');
$about_value1_text = get_option('salon_auto_page_' . $page_slug . '_value1_text', 'Pełna dokumentacja i jawność wszystkich informacji o pojeździe');
$about_value2_title = get_option('salon_auto_page_' . $page_slug . '_value2_title', 'Zaufanie');
$about_value2_text = get_option('salon_auto_page_' . $page_slug . '_value2_text', 'Uczciwe relacje i wieloletnia współpraca z Klientami');
$about_value3_title = get_option('salon_auto_page_' . $page_slug . '_value3_title', 'Kompleksowość');
$about_value3_text = get_option('salon_auto_page_' . $page_slug . '_value3_text', 'Pełna obsługa: auto, leasing, ubezpieczenia, dokumenty');
$about_credentials_title = get_option('salon_auto_page_' . $page_slug . '_credentials_title', 'Certyfikaty i członkostwa');
$about_credentials_subtitle = get_option('salon_auto_page_' . $page_slug . '_credentials_subtitle', 'Dowody wiarygodności i profesjonalizmu');
$about_cred1_title = get_option('salon_auto_page_' . $page_slug . '_cred1_title', '28 lat na rynku');
$about_cred1_text = get_option('salon_auto_page_' . $page_slug . '_cred1_text', 'Najlepsza rekomendacja to zadowoleni Klienci przez prawie trzy dekady działalności');
$about_cred2_title = get_option('salon_auto_page_' . $page_slug . '_cred2_title', 'Loża Przedsiębiorców');
$about_cred2_text = get_option('salon_auto_page_' . $page_slug . '_cred2_text', 'Członek prestiżowej organizacji zrzeszającej najlepszych przedsiębiorców w Polsce');
$about_cred3_title = get_option('salon_auto_page_' . $page_slug . '_cred3_title', 'Uczestnik Programu RZETELNA Firma');
$about_cred3_text = get_option('salon_auto_page_' . $page_slug . '_cred3_text', 'Certyfikat potwierdzający wysoką wiarygodność i rzetelność w biznesie');
$about_cta_title = get_option('salon_auto_page_' . $page_slug . '_cta_title', 'Serdecznie Zapraszamy');
$about_cta_phone_text = get_option('salon_auto_page_' . $page_slug . '_cta_phone_text', 'Zadzwoń');
$about_cta_contact_text = get_option('salon_auto_page_' . $page_slug . '_cta_contact_text', 'Formularz kontaktowy');
?>

<main id="main-content" class="min-h-screen">
    <!-- Hero Section -->
    <section class="py-12 sm:py-16 md:py-20 lg:py-24 border-b border-gray-100 fade-in-up" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="font-bold text-primary mb-6 tracking-tight" style="font-size: 48px !important;">
                    <?php echo esc_html($about_hero_title); ?>
                </h1>
                <div class="w-24 h-1.5 bg-gradient-to-r from-transparent via-gold to-transparent mx-auto mb-8 opacity-60"></div>
                <p class="text-lg sm:text-xl md:text-2xl text-gray-600 leading-relaxed font-light">
                    <?php echo esc_html($about_hero_text); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- About Artur -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32 fade-in-up" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-2 gap-8 sm:gap-10 lg:gap-12 items-center">
                <div class="fade-in-left">
                    <h2 class="text-2xl sm:text-3xl font-bold text-primary mb-6">
                        <?php echo esc_html($about_artur_name); ?>
                    </h2>
                    <div class="space-y-4 text-gray-700 leading-relaxed">
                        <p>
                            <?php echo esc_html($about_artur_text1); ?>
                        </p>
                        <p class="bg-gray-50 border-l-4 border-accent p-4 rounded-r-lg">
                            <strong class="text-primary"><?php echo esc_html($about_artur_text2); ?></strong>
                        </p>
                        <p>
                            <?php echo esc_html($about_artur_text3); ?>
                        </p>
                        <p>
                            <?php echo esc_html($about_artur_text4); ?>
                        </p>
                        <p class="font-semibold text-white bg-primary px-4 py-3 rounded-lg inline-block">
                            <?php echo wp_kses_post($about_artur_cert); ?>
                        </p>
                    </div>
                    <div class="mt-8">
                        <a href="<?php echo esc_url($contact_url); ?>" class="inline-flex items-center justify-center font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 bg-primary text-white hover:bg-secondary focus:ring-accent px-8 py-4 text-lg rounded-xl shadow-lg">
                            <?php echo esc_html($about_button_text); ?>
                        </a>
                    </div>
                </div>
                <div class="relative overflow-hidden fade-in-right">
                    <div class="aspect-[3/4] rounded-2xl bg-gray-200 overflow-hidden shadow-soft relative">
                        <?php
                        // Try to get image from options first, then custom field, then default
                        if ($about_image_id) {
                            $img_url = wp_get_attachment_image_url($about_image_id, 'large');
                            $img_alt = get_post_meta($about_image_id, '_wp_attachment_image_alt', true);
                            if (!$img_alt) $img_alt = 'O nas - Piekne auta';
                        } else {
                            $about_image = get_post_meta(get_the_ID(), 'about_image', true);
                            if ($about_image) {
                                $img_url = wp_get_attachment_image_url($about_image, 'large');
                                $img_alt = get_post_meta($about_image, '_wp_attachment_image_alt', true);
                                if (!$img_alt) $img_alt = 'O nas - Piekne auta';
                            } else {
                                // Fallback to theme image
                                $img_url = get_stylesheet_directory_uri() . '/assets/images/zdjecie-o-nas.jpg';
                                $img_alt = 'O nas - Piekne auta';
                            }
                        }
                        ?>
                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" class="w-full h-full object-cover">
                    </div>
                    <!-- Floating stats -->
                    <div class="absolute -bottom-6 -left-6 rounded-xl p-6 shadow-lg floating-stat-bottom" style="background-color: #f3f1ee !important;">
                        <div class="text-4xl font-bold" style="color: #212121 !important;"><?php echo esc_html($about_stat1_value); ?></div>
                        <div class="text-sm" style="color: #212121 !important;"><?php echo esc_html($about_stat1_label); ?></div>
                    </div>
                    <div class="absolute -top-6 -right-6 rounded-xl p-6 shadow-lg floating-stat-top" style="background-color: #f3f1ee !important;">
                        <div class="text-4xl font-bold" style="color: #212121 !important;"><?php echo esc_html($about_stat2_value); ?></div>
                        <div class="text-sm" style="color: #212121 !important;"><?php echo esc_html($about_stat2_label); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32 bg-gray-50 fade-in-up">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-12 fade-in-up">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary mb-3 sm:mb-4 fade-in-up">
                    <?php echo esc_html($about_values_title); ?>
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto fade-in-up stagger-1">
                    <?php echo esc_html($about_values_subtitle); ?>
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 text-center fade-in-up stagger-1 card-animate hover-lift animate-on-scroll">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 border-2 border-gray-200 flex-shrink-0">
                        <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-primary mb-2"><?php echo esc_html($about_value1_title); ?></h3>
                    <p class="text-sm text-gray-600">
                        <?php echo esc_html($about_value1_text); ?>
                    </p>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 text-center fade-in-up stagger-2 card-animate hover-lift animate-on-scroll">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 border-2 border-gray-200 flex-shrink-0">
                        <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-primary mb-2"><?php echo esc_html($about_value2_title); ?></h3>
                    <p class="text-sm text-gray-600">
                        <?php echo esc_html($about_value2_text); ?>
                    </p>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 text-center fade-in-up stagger-3 card-animate hover-lift animate-on-scroll">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 border-2 border-gray-200 flex-shrink-0">
                        <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/><path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-primary mb-2"><?php echo esc_html($about_value3_title); ?></h3>
                    <p class="text-sm text-gray-600">
                        <?php echo esc_html($about_value3_text); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Credentials -->
    <section class="py-16 sm:py-20 md:py-28 lg:py-32 fade-in-up" style="background-color: #f3f1ee !important;">
        <div class="container mx-auto text-center">
            <h2 class="text-2xl sm:text-3xl font-bold text-primary mb-4 fade-in-up">
                <?php echo esc_html($about_credentials_title); ?>
            </h2>
            <p class="text-lg text-gray-600 mb-12 fade-in-up stagger-1">
                <?php echo esc_html($about_credentials_subtitle); ?>
            </p>
            <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 fade-in-up stagger-1 card-animate hover-lift animate-on-scroll">
                    <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-6 border-2 border-gray-200">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-primary mb-2 text-lg"><?php echo esc_html($about_cred1_title); ?></h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        <?php echo esc_html($about_cred1_text); ?>
                    </p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 fade-in-up stagger-2 card-animate hover-lift animate-on-scroll">
                    <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-6 border-2 border-gray-200">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-primary mb-2 text-lg"><?php echo esc_html($about_cred2_title); ?></h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        <?php echo esc_html($about_cred2_text); ?>
                    </p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 fade-in-up stagger-3 card-animate hover-lift animate-on-scroll">
                    <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-6 border-2 border-gray-200">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-primary mb-2 text-lg"><?php echo esc_html($about_cred3_title); ?></h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        <?php echo esc_html($about_cred3_text); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Serdecznie Zapraszamy -->
    <section class="py-20 bg-white fade-in-up">
        <div class="container mx-auto text-center">
            <div class="max-w-3xl mx-auto fade-in-up stagger-1">
                <h2 class="text-2xl md:text-3xl font-bold mb-4" style="color: #212121 !important; text-transform: none !important;">
                    <?php echo esc_html($about_cta_title); ?>
                </h2>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="tel:+48<?php echo esc_attr($phone_clean); ?>" class="inline-flex items-center justify-center gap-2 bg-white text-primary border-2 border-primary px-8 py-4 rounded-xl font-semibold shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                        </svg>
                        <span><?php echo esc_html($about_cta_phone_text); ?>: <?php echo esc_html($phone); ?></span>
                    </a>
                    <a href="<?php echo esc_url($contact_url); ?>" class="inline-flex items-center justify-center gap-2 bg-white text-primary border-2 border-primary px-8 py-4 rounded-xl font-semibold shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        <span><?php echo esc_html($about_cta_contact_text); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>

