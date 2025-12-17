</main>

<footer class="bg-primary text-white relative overflow-hidden border-t border-gray-800">
    <div class="container mx-auto">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 py-16 border-b border-gray-800/50">
            <!-- Company Info -->
            <div class="lg:col-span-5">
                <?php
                $logo_footer_id = get_option('salon_auto_logo_footer_id', 0);
                // Fallback to header logo if footer logo not set
                if (!$logo_footer_id) {
                    $logo_footer_id = get_option('salon_auto_logo_header_id', 0);
                }
                $logo_footer_url = $logo_footer_id ? wp_get_attachment_image_url($logo_footer_id, 'full') : get_stylesheet_directory_uri() . '/assets/images/logo.svg';
                $company_name = get_option('salon_auto_company_name', get_bloginfo('name'));
                ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-block mb-3 group">
                    <img src="<?php echo esc_url($logo_footer_url); ?>" alt="<?php echo esc_attr($company_name); ?>" style="height: 206px; width: auto; max-width: 100%; object-fit: contain; filter: brightness(0) invert(1);" width="206" height="206" loading="lazy">
                </a>
                <?php
                $footer_description = salon_auto_get_option('footer_description', 'Samochody używane premium oraz fabrycznie nowe. Sprzedaż i leasing środków trwałych.');
                $footer_experience_text = salon_auto_get_option('footer_experience_text', '28 lat doświadczenia');
                $footer_loza_text = salon_auto_get_option('footer_loza_text', 'Loża Przedsiębiorców');
                $footer_loza_url = salon_auto_get_option('cert_loza_url', 'https://lozaprzedsiebiorcow.pl');
                $footer_rzetelna_text = salon_auto_get_option('footer_rzetelna_text', 'Uczestnik Programu RZETELNA Firma');
                // Link usunięty - pozostaje tylko tekst
                ?>
                <p class="text-gray-400 leading-relaxed mb-4 text-base">
                    <?php echo esc_html($footer_description); ?>
                </p>
                <div class="flex items-center space-x-2 text-gray-400 mb-3">
                    <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-semibold text-white"><?php echo esc_html($footer_experience_text); ?></span>
                </div>
                <div class="flex items-center space-x-2 text-gray-400 mb-3">
                    <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <a href="<?php echo esc_url($footer_loza_url); ?>" target="_blank" rel="noopener" class="text-white font-semibold focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent rounded underline" style="color: #ffffff !important; text-decoration: underline !important;">
                        <?php echo esc_html($footer_loza_text); ?>
                    </a>
                </div>
                <div class="flex items-center space-x-2 text-gray-400">
                    <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex flex-col">
                        <span class="text-white font-semibold">
                            <?php echo esc_html($footer_rzetelna_text); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="lg:col-span-2">
                <h4 class="font-bold text-lg mb-6 text-white">Nawigacja</h4>
                <?php
                // Get pages URLs - show links even if pages don't exist (like header)
                $about_page_f = get_page_by_path('o-nas');
                $leasing_page_f = get_page_by_path('leasing');
                $loans_page_f = get_page_by_path('pozyczki');
                $insurance_page_f = get_page_by_path('ubezpieczenia');
                $contact_page_f = get_page_by_path('kontakt');
                
                $about_url_f = $about_page_f ? get_permalink($about_page_f->ID) : home_url('/o-nas/');
                $leasing_url_f = $leasing_page_f ? get_permalink($leasing_page_f->ID) : home_url('/leasing/');
                $loans_url_f = $loans_page_f ? get_permalink($loans_page_f->ID) : home_url('/pozyczki/');
                $insurance_url_f = $insurance_page_f ? get_permalink($insurance_page_f->ID) : home_url('/ubezpieczenia/');
                $contact_url_f = $contact_page_f ? get_permalink($contact_page_f->ID) : home_url('/kontakt/');
                ?>
                <ul class="space-y-3">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>" class="text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">Strona główna</a></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link('car')); ?>" class="text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">Katalog samochodów</a></li>
                    <li><a href="<?php echo esc_url($about_url_f); ?>" class="text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">O nas</a></li>
                    <li><a href="<?php echo esc_url($leasing_url_f); ?>" class="text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">Leasing</a></li>
                    <li><a href="<?php echo esc_url($loans_url_f); ?>" class="text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">Pożyczki</a></li>
                    <li><a href="<?php echo esc_url($insurance_url_f); ?>" class="text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">Ubezpieczenia</a></li>
                    <li><a href="<?php echo esc_url($contact_url_f); ?>" class="text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">Kontakt</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="lg:col-span-3">
                <h4 class="font-bold text-lg mb-6 text-white">Kontakt</h4>
                <div class="space-y-4">
                    <?php
                    $phone = salon_auto_get_option('phone', '502 42 82 82');
                    ?>
                    <a href="tel:+48<?php echo esc_attr(preg_replace('/[^0-9]/', '', $phone)); ?>" class="flex items-center space-x-3 text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent rounded-lg">
                        <div class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Telefon</div>
                            <div class="font-semibold text-white"><?php echo esc_html($phone); ?></div>
                        </div>
                    </a>
                    <?php
                    $email = salon_auto_get_option('email', 'biuro@piekneauta.pl');
                    ?>
                    <a href="mailto:<?php echo esc_attr($email); ?>" class="flex items-center space-x-3 text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent rounded-lg">
                        <div class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">email</div>
                            <div class="text-sm text-white"><?php echo esc_html($email); ?></div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Legal & Social -->
            <div class="lg:col-span-2">
                <h4 class="font-bold text-lg mb-6 text-white">Social Media</h4>
                <div class="flex space-x-3 mb-8">
                    <?php
                    $facebook = salon_auto_get_option('social_facebook', 'https://www.facebook.com/Apmleasing');
                    if ($facebook) :
                    ?>
                    <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" aria-label="Facebook - otwórz w nowej karcie">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path>
                        </svg>
                    </a>
                    <?php endif; ?>
                    
                    <?php
                    $otomoto = salon_auto_get_option('social_otomoto', 'https://piekneauta.otomoto.pl');
                    if ($otomoto) :
                    ?>
                    <a href="<?php echo esc_url($otomoto); ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" aria-label="OtoMoto - otwórz w nowej karcie">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                        </svg>
                    </a>
                    <?php endif; ?>
                    
                    <?php
                    $instagram = salon_auto_get_option('social_instagram', 'https://www.instagram.com/piekne_auta_i_leasing/');
                    if ($instagram) :
                    ?>
                    <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" aria-label="Instagram - otwórz w nowej karcie">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path>
                        </svg>
                    </a>
                    <?php endif; ?>
                    
                    <?php
                    $social_tiktok = salon_auto_get_option('social_tiktok', '');
                    if ($social_tiktok) :
                    ?>
                    <a href="<?php echo esc_url($social_tiktok); ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" aria-label="TikTok - otwórz w nowej karcie">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"></path>
                        </svg>
                    </a>
                    <?php endif; ?>
                </div>
                <div class="text-xs text-gray-500 space-y-1">
                    <?php
                    $company_nip = salon_auto_get_option('company_nip', '6731525915');
                    $company_regon = salon_auto_get_option('company_regon', '330558443');
                    ?>
                    <?php if ($company_nip) : ?>
                    <p>NIP: <?php echo esc_html($company_nip); ?></p>
                    <?php endif; ?>
                    <?php if ($company_regon) : ?>
                    <p>REGON: <?php echo esc_html($company_regon); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="py-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
                <div class="text-gray-400">
                    <?php
                    $footer_copyright_name = salon_auto_get_option('footer_copyright_name', 'Artur Kurzydłowski');
                    $company_name = salon_auto_get_option('company_name', get_bloginfo('name'));
                    ?>
                    © <?php echo esc_html(date('Y')); ?> <?php echo esc_html($company_name); ?> — <?php echo esc_html($footer_copyright_name); ?>. Wszelkie prawa zastrzeżone.
                </div>
                <div class="flex items-center gap-6 whitespace-nowrap">
                    <?php
                    $regulamin_page = get_page_by_path('regulamin');
                    if ($regulamin_page) :
                    ?>
                    <a href="<?php echo esc_url(get_permalink($regulamin_page->ID)); ?>" class="text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent whitespace-nowrap">Regulamin</a>
                    <span class="text-gray-700" aria-hidden="true">·</span>
                    <?php endif; ?>
                    <?php
                    $privacy_page = get_page_by_path('polityka-prywatnosci');
                    if ($privacy_page) :
                    ?>
                    <a href="<?php echo esc_url(get_permalink($privacy_page->ID)); ?>" class="text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent whitespace-nowrap">Polityka prywatności</a>
                    <span class="text-gray-700" aria-hidden="true">·</span>
                    <?php endif; ?>
                    <button onclick="showCookieBanner()" class="text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent cursor-pointer whitespace-nowrap">Ustawienia cookies</button>
                </div>
            </div>
            <?php
            $footer_developer_text = salon_auto_get_option('footer_developer_text', 'Projekt i wykonanie');
            $footer_developer_link = salon_auto_get_option('footer_developer_link', 'https://www.instagram.com/codingmaks?igsh=MThzY2Roc3Npc201MA%3D%3D&utm_source=qr');
            if ($footer_developer_text || $footer_developer_link) :
            ?>
            <div class="mt-4 text-center text-gray-400 text-sm">
                <p><?php echo esc_html($footer_developer_text); ?> <?php if ($footer_developer_link) : ?><a href="<?php echo esc_url($footer_developer_link); ?>" target="_blank" rel="noopener" class="text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent underline">codingmaks</a><?php endif; ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</footer>

<!-- Cookie Banner -->
<div id="cookieBanner" class="fixed bottom-0 left-0 right-0 z-50 transform translate-y-full transition-all duration-300 ease-out" style="display: none; background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%); box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1), 0 -2px 8px rgba(0, 0, 0, 0.05); border-top: 1px solid rgba(0, 0, 0, 0.08);">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 lg:gap-6">
            <div class="flex items-start gap-4 flex-1 min-w-0">
                <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-gradient-to-br from-amber-100 to-amber-50 flex items-center justify-center shadow-sm" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-amber-600" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        <circle cx="8.5" cy="9.5" r="1.5"/>
                        <circle cx="15.5" cy="9.5" r="1.5"/>
                        <circle cx="12" cy="15" r="1.5"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-primary mb-2 text-lg sm:text-xl" style="color: #212121 !important; font-weight: 700;">Używamy plików cookies</h3>
                    <p class="text-sm sm:text-base leading-relaxed text-gray-700" style="color: #374151 !important; line-height: 1.6;">
                        Ta strona wykorzystuje pliki cookies do zapewnienia prawidłowego działania, analityki oraz personalizacji treści. Możesz zarządzać swoimi preferencjami lub zaakceptować wszystkie.
                        <?php
                        $privacy_page = get_page_by_path('polityka-prywatnosci');
                        if ($privacy_page) :
                        ?>
                        <a href="<?php echo esc_url(get_permalink($privacy_page->ID)); ?>" class="underline font-medium hover:text-primary transition-colors whitespace-nowrap" style="color: #212121 !important; text-decoration-thickness: 1.5px; text-underline-offset: 3px;">Polityka prywatności</a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 flex-shrink-0 w-full lg:w-auto">
                <button onclick="rejectAllCookies()" class="px-5 sm:px-6 py-2.5 sm:py-3 border-2 rounded-xl font-semibold transition-all duration-200 text-sm sm:text-base whitespace-nowrap flex-1 sm:flex-none hover:scale-[1.02] active:scale-[0.98] cursor-pointer" style="color: #6b7280 !important; border-color: #d1d5db !important; background: #ffffff !important; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                    Odrzuć wszystkie
                </button>
                <button onclick="manageCookies()" class="px-5 sm:px-6 py-2.5 sm:py-3 border-2 rounded-xl font-semibold transition-all duration-200 text-sm sm:text-base whitespace-nowrap flex-1 sm:flex-none hover:scale-[1.02] active:scale-[0.98] cursor-pointer" style="color: #212121 !important; border-color: #212121 !important; background: transparent !important; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                    Ustawienia
                </button>
                <button onclick="acceptAllCookies()" class="px-6 sm:px-8 py-2.5 sm:py-3 rounded-xl font-semibold transition-all duration-200 text-sm sm:text-base whitespace-nowrap flex-1 sm:flex-none hover:scale-[1.02] active:scale-[0.98] shadow-md hover:shadow-lg cursor-pointer" style="background: linear-gradient(135deg, #212121 0%, #374151 100%) !important; color: #ffffff !important; border: none !important;">
                    Akceptuj wszystkie
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cookie Preferences Modal -->
<div id="cookieModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-3 sm:p-4" style="display: none;">
    <div class="bg-white rounded-xl sm:rounded-2xl max-w-3xl w-full max-h-[95vh] sm:max-h-[90vh] overflow-y-auto p-4 sm:p-6 md:p-10 lg:p-12" style="background-color: #ffffff !important;">
        <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-primary mb-3 sm:mb-4 md:mb-6" style="font-family: 'Instrument Serif', serif; font-weight: 400; text-transform: uppercase; letter-spacing: 0.04em; color: #212121;">Ustawienia cookies</h2>
        <p class="text-sm sm:text-base md:text-lg mb-4 sm:mb-6 md:mb-8 leading-relaxed" style="color: #212121 !important;">
            Możesz wybrać, które kategorie plików cookies chcesz zaakceptować.
        </p>
        <div class="space-y-3 sm:space-y-4 md:space-y-5 mb-4 sm:mb-6 md:mb-8">
            <!-- Necessary Cookies (always enabled) -->
            <div class="flex items-start space-x-3 sm:space-x-4 p-4 sm:p-5 md:p-6 bg-gray-50 rounded-lg sm:rounded-xl border-2 border-gray-200" style="background-color: #f9f8f6 !important; border-color: #e8e5e0 !important;">
                <input type="checkbox" checked disabled class="mt-1 w-5 h-5 sm:w-6 sm:h-6 cursor-not-allowed flex-shrink-0" style="width: 20px; height: 20px;">
                <div class="flex-1 min-w-0">
                    <h3 class="text-base sm:text-lg md:text-xl font-bold mb-1 sm:mb-2" style="font-family: 'Avenir Next Condensed', 'Roboto Condensed', sans-serif; font-weight: 600; color: #212121 !important;">Niezbędne</h3>
                    <p class="text-xs sm:text-sm md:text-base leading-relaxed" style="color: #212121 !important;">
                        Wymagane do podstawowego działania strony. Nie można ich wyłączyć.
                    </p>
                </div>
            </div>
            <!-- Analytics Cookies -->
            <label for="analyticsCookies" class="flex items-start space-x-3 sm:space-x-4 p-4 sm:p-5 md:p-6 bg-white rounded-lg sm:rounded-xl border-2 border-gray-200 cursor-pointer transition-all" style="background-color: #ffffff !important; border-color: #e8e5e0 !important;">
                <input type="checkbox" id="analyticsCookies" class="mt-1 w-5 h-5 sm:w-6 sm:h-6 text-accent border-gray-300 rounded focus:ring-2 focus:ring-accent cursor-pointer flex-shrink-0" style="width: 20px; height: 20px;">
                <div class="flex-1 min-w-0">
                    <h3 class="text-base sm:text-lg md:text-xl font-bold mb-1 sm:mb-2" style="font-family: 'Avenir Next Condensed', 'Roboto Condensed', sans-serif; font-weight: 600; color: #212121 !important;">Analityczne</h3>
                    <p class="text-xs sm:text-sm md:text-base leading-relaxed" style="color: #212121 !important;">
                        Google Analytics 4 - pomaga nam zrozumieć, jak użytkownicy korzystają ze strony.
                    </p>
                </div>
            </label>
            <!-- Marketing Cookies -->
            <label for="marketingCookies" class="flex items-start space-x-3 sm:space-x-4 p-4 sm:p-5 md:p-6 bg-white rounded-lg sm:rounded-xl border-2 border-gray-200 cursor-pointer transition-all" style="background-color: #ffffff !important; border-color: #e8e5e0 !important;">
                <input type="checkbox" id="marketingCookies" class="mt-1 w-5 h-5 sm:w-6 sm:h-6 text-accent border-gray-300 rounded focus:ring-2 focus:ring-accent cursor-pointer flex-shrink-0" style="width: 20px; height: 20px;">
                <div class="flex-1 min-w-0">
                    <h3 class="text-base sm:text-lg md:text-xl font-bold mb-1 sm:mb-2" style="font-family: 'Avenir Next Condensed', 'Roboto Condensed', sans-serif; font-weight: 600; color: #212121 !important;">Marketingowe</h3>
                    <p class="text-xs sm:text-sm md:text-base leading-relaxed" style="color: #212121 !important;">
                        Personalizacja reklam i śledzenie konwersji.
                    </p>
                </div>
            </label>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-4 pt-3 sm:pt-4 border-t border-gray-200">
            <button onclick="savePreferences()" class="flex-1 px-4 sm:px-6 md:px-8 py-3 sm:py-4 bg-primary text-white rounded-lg sm:rounded-xl font-semibold text-sm sm:text-base md:text-lg transition-all shadow-lg cursor-pointer" style="background-color: #212121 !important; color: #ffffff !important;">
                Zapisz preferencje
            </button>
            <button onclick="closeModal()" class="flex-1 sm:flex-none px-4 sm:px-6 md:px-8 py-3 sm:py-4 border-2 border-gray-300 text-gray-700 rounded-lg sm:rounded-xl font-semibold text-sm sm:text-base md:text-lg transition-all cursor-pointer" style="border-color: #d1d5db !important; color: #374151 !important;">
                Anuluj
            </button>
        </div>
    </div>
</div>

<?php wp_footer(); ?>
<script>
// Universal mobile typography - consistent h1 sizing on mobile
(function() {
  function adjustMobileHeadings() {
    if (window.innerWidth <= 640) {
      // Find all main h1 headings in hero sections
      const headings = document.querySelectorAll('section h1, main h1, h1.hero-title, h1.hero-title-mobile');
      headings.forEach(title => {
        if (title) {
          const currentSize = parseInt(title.style.fontSize) || parseInt(window.getComputedStyle(title).fontSize);
          if (currentSize > 36) {
            title.style.setProperty('font-size', '36px', 'important');
          }
        }
      });
    }
  }
  
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', adjustMobileHeadings);
  } else {
    adjustMobileHeadings();
  }
  
  window.addEventListener('resize', adjustMobileHeadings);
})();
</script>
<!-- Hero & Car Sliders - EXACT COPY FROM STATIC VERSION -->
<style>
/* Hero Slider - Desktop i Mobile */
.hero-slider,
.hero-slider-mobile {
  position: relative;
  width: 100%;
  height: 100%;
  background: #f5f5f5;
}

.hero-slider .slider-image,
.hero-slider-mobile .slider-image {
  position: absolute;
  inset: 0;
  opacity: 0;
  transition: opacity 2.5s ease-in-out;
}

.hero-slider .slider-image.active,
.hero-slider-mobile .slider-image.active {
  opacity: 1;
}

.hero-slider .slider-image img,
.hero-slider-mobile .slider-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Car Card Sliders - EXACT COPY FROM STATIC VERSION */
.car-slider {
  position: relative;
  width: 100%;
  height: 100%;
  background: #f5f5f5;
}

.car-slider .car-slide {
  position: absolute;
  inset: 0;
  opacity: 0;
  transition: opacity 1.5s ease-in-out;
}

.car-slider .car-slide.active {
  opacity: 1;
}

.car-slider .car-slide img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
</style>

<script>
// Hero Slider - Desktop i Mobile
(function() {
  const sliders = document.querySelectorAll('.hero-slider, .hero-slider-mobile');
  if (!sliders.length) return;
  
  sliders.forEach(slider => {
    const slides = slider.querySelectorAll('.slider-image');
    if (!slides.length) return;
    
    let currentSlide = 0;
    
    function nextSlide() {
      slides[currentSlide].classList.remove('active');
      currentSlide = (currentSlide + 1) % slides.length;
      slides[currentSlide].classList.add('active');
    }
    
    // Każdy slider ma własny interval
    setInterval(nextSlide, 5000);
  });
})();

// Car Card Sliders - EXACT COPY FROM STATIC VERSION
(function() {
  const carSliders = document.querySelectorAll('.car-slider');
  
  carSliders.forEach(slider => {
    const slides = slider.querySelectorAll('.car-slide');
    if (!slides.length || slides.length === 1) return;
    let currentSlide = 0;
    
    function nextSlide() {
      slides[currentSlide].classList.remove('active');
      currentSlide = (currentSlide + 1) % slides.length;
      slides[currentSlide].classList.add('active');
    }
    
    // Każdy slider zmienia się w losowym odstępie (3-4s) dla naturalnego efektu
    const interval = 3000 + Math.random() * 1000;
    setInterval(nextSlide, interval);
  });
})();
</script>

<!-- Cookie Consent Scripts (RODO/GDPR) -->
<script>
/**
 * Cookie Consent Manager
 * Zarządza zgodami cookies zgodnie z wymaganiami RODO/UOKiK
 * Przechowuje zgody w localStorage (wystarczające dla RODO)
 */
(function() {
  'use strict';
  
  /**
   * Generuje unikalny Device ID dla urządzenia użytkownika
   */
  function getDeviceId() {
    let deviceId = localStorage.getItem('deviceId');
    
    if (!deviceId) {
      // Prosty fingerprint dla identyfikacji urządzenia
      const fingerprint = [
        navigator.userAgent,
        navigator.language,
        screen.width + 'x' + screen.height,
        new Date().getTimezoneOffset()
      ].join('|');
      
      // Simple hash
      let hash = 0;
      for (let i = 0; i < fingerprint.length; i++) {
        const char = fingerprint.charCodeAt(i);
        hash = ((hash << 5) - hash) + char;
        hash = hash & hash;
      }
      
      deviceId = 'dev-' + Math.abs(hash).toString(36) + '-' + Date.now().toString(36);
      localStorage.setItem('deviceId', deviceId);
    }
    
    return deviceId;
  }
  
  /**
   * Zapisuje zgodę cookies (tylko localStorage - wystarczające dla RODO)
   */
  function sendConsentToBackend(consent) {
    // Zgoda jest już zapisana w localStorage przez funkcje acceptAllCookies/rejectAllCookies/savePreferences
    // Ta funkcja służy jako placeholder dla kompatybilności
    console.log('Cookie consent saved:', consent);
  }
  
  // Eksportuj funkcje globalnie
  window.CookieConsentAPI = {
    sendConsent: sendConsentToBackend,
    getDeviceId: getDeviceId
  };
})();

// Cookie Banner Functions
function hideBanner() {
  const banner = document.getElementById('cookieBanner');
  if (banner) {
    banner.classList.add('translate-y-full');
    setTimeout(() => {
      banner.style.display = 'none';
    }, 300);
  }
}

window.acceptAllCookies = function() {
  const consent = {
    necessary: true,
    analytics: true,
    marketing: true,
    timestamp: new Date().toISOString()
  };
  
  localStorage.setItem('cookieConsent', JSON.stringify(consent));
  
  if (window.CookieConsentAPI) {
    window.CookieConsentAPI.sendConsent(consent);
  }
  
  hideBanner();
  if (typeof initializeAnalytics === 'function') {
    initializeAnalytics(true);
  }
};

window.rejectAllCookies = function() {
  const consent = {
    necessary: true,
    analytics: false,
    marketing: false,
    timestamp: new Date().toISOString()
  };
  
  localStorage.setItem('cookieConsent', JSON.stringify(consent));
  
  if (window.CookieConsentAPI) {
    window.CookieConsentAPI.sendConsent(consent);
  }
  
  hideBanner();
  if (typeof initializeAnalytics === 'function') {
    initializeAnalytics(false);
  }
};

window.showCookieBanner = function() {
  const banner = document.getElementById('cookieBanner');
  if (banner) {
    banner.style.display = 'block';
    setTimeout(() => {
      banner.classList.remove('translate-y-full');
    }, 100);
    // Automatycznie otwórz modal z ustawieniami
    setTimeout(() => {
      manageCookies();
    }, 300);
  }
};

window.manageCookies = function() {
  const modal = document.getElementById('cookieModal');
  if (modal) {
    modal.style.display = 'flex';
    
    // Załaduj zapisane preferencje do checkboxów
    const savedConsent = localStorage.getItem('cookieConsent');
    if (savedConsent) {
      try {
        const consent = JSON.parse(savedConsent);
        const analyticsCheckbox = document.getElementById('analyticsCookies');
        const marketingCheckbox = document.getElementById('marketingCookies');
        if (analyticsCheckbox) analyticsCheckbox.checked = consent.analytics || false;
        if (marketingCheckbox) marketingCheckbox.checked = consent.marketing || false;
      } catch (e) {
        console.error('Błąd ładowania preferencji:', e);
      }
    }
  }
};

window.closeModal = function() {
  const modal = document.getElementById('cookieModal');
  if (modal) {
    modal.style.display = 'none';
  }
};

window.savePreferences = function() {
  const analyticsCheckbox = document.getElementById('analyticsCookies');
  const marketingCheckbox = document.getElementById('marketingCookies');
  
  if (!analyticsCheckbox || !marketingCheckbox) {
    console.error('Nie znaleziono checkboxów cookies');
    return;
  }
  
  const consent = {
    necessary: true,
    analytics: analyticsCheckbox.checked,
    marketing: marketingCheckbox.checked,
    timestamp: new Date().toISOString()
  };
  
  localStorage.setItem('cookieConsent', JSON.stringify(consent));
  
  if (window.CookieConsentAPI) {
    window.CookieConsentAPI.sendConsent(consent);
  }
  
  closeModal();
  hideBanner();
  
  if (typeof initializeAnalytics === 'function') {
    initializeAnalytics(consent.analytics);
  }
};

// Sprawdź czy użytkownik już wyraził zgodę - jeśli nie, pokaż banner
(function() {
  const savedConsent = localStorage.getItem('cookieConsent');
  if (!savedConsent) {
    // Pokaż banner po załadowaniu strony
    window.addEventListener('load', function() {
      setTimeout(function() {
        const banner = document.getElementById('cookieBanner');
        if (banner) {
          banner.style.display = 'block';
          setTimeout(() => {
            banner.classList.remove('translate-y-full');
          }, 100);
        }
      }, 1000); // Poczekaj 1 sekundę po załadowaniu
    });
  }
})();
</script>

<!-- Cookie Banner Mobile Styles -->
<style>
#cookieBanner {
  will-change: transform;
}

@media (max-width: 1023px) {
  #cookieBanner {
    padding: 0 !important;
    box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.15), 0 -4px 16px rgba(0, 0, 0, 0.1) !important;
    border-top: 2px solid rgba(0, 0, 0, 0.1) !important;
  }
  
  #cookieBanner > div {
    padding: 16px !important;
  }
  
  #cookieBanner .flex.items-start.gap-4 {
    gap: 12px !important;
    margin-bottom: 16px !important;
  }
  
  #cookieBanner h3 {
    font-size: 16px !important;
    margin-bottom: 8px !important;
    line-height: 1.3 !important;
  }
  
  #cookieBanner p {
    font-size: 13px !important;
    line-height: 1.5 !important;
    margin-bottom: 0 !important;
  }
  
  #cookieBanner .flex.flex-wrap {
    flex-direction: column !important;
    gap: 10px !important;
    width: 100% !important;
  }
  
  #cookieBanner button {
    width: 100% !important;
    padding: 14px 16px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    border-radius: 12px !important;
    min-height: 48px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
  }
  
  #cookieBanner button:active {
    transform: scale(0.97) !important;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15) !important;
  }
  
  #cookieBanner button[onclick="acceptAllCookies()"] {
    order: -1 !important;
    background: linear-gradient(135deg, #212121 0%, #374151 100%) !important;
    box-shadow: 0 4px 12px rgba(33, 33, 33, 0.3) !important;
  }
  
  #cookieBanner button[onclick="rejectAllCookies()"] {
    order: 2 !important;
  }
  
  #cookieBanner button[onclick="manageCookies()"] {
    order: 1 !important;
  }
}
</style>

<!-- Back to Top Button / Przycisk powrotu do góry -->
<button id="backToTop" 
    aria-label="Powrót na górę strony" 
    title="Powrót do góry"
    class="fixed z-50 flex items-center justify-center transition-all duration-300 ease-out opacity-0 pointer-events-none"
    style="
        bottom: 24px;
        right: 24px;
        width: 52px;
        height: 52px;
        background: #212121;
        border: 2px solid #212121;
        border-radius: 16px;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25), 0 2px 8px rgba(0, 0, 0, 0.15);
        transform: translateY(20px) scale(0.9);
    ">
    <svg class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 32px; height: 32px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"></path>
    </svg>
</button>

<style>
#backToTop {
    will-change: opacity, transform;
    z-index: 9999 !important;
}
#backToTop.visible {
    opacity: 1 !important;
    pointer-events: auto !important;
    transform: translateY(0) scale(1) !important;
}
#backToTop:hover {
    background: #2d2d2d !important;
    border-color: #2d2d2d !important;
    transform: translateY(-2px) scale(1.05) !important;
    box-shadow: 0 6px 28px rgba(0, 0, 0, 0.35), 0 4px 12px rgba(0, 0, 0, 0.25) !important;
}
#backToTop:active {
    transform: translateY(0) scale(0.95) !important;
}
#backToTop svg {
    color: #ffffff !important;
    stroke: #ffffff !important;
    fill: none !important;
    width: 32px !important;
    height: 32px !important;
    stroke-width: 2.5 !important;
}
@media (max-width: 768px) {
    #backToTop {
        bottom: 20px !important;
        right: 20px !important;
        width: 68px !important;
        height: 68px !important;
        z-index: 9999 !important;
        border-radius: 20px !important;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.4), 0 3px 12px rgba(0, 0, 0, 0.25) !important;
    }
    #backToTop svg {
        width: 44px !important;
        height: 44px !important;
        stroke-width: 4 !important;
    }
}
</style>

<script>
(function() {
    var btn = document.getElementById('backToTop');
    if (!btn) return;
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 400) {
            btn.classList.add('visible');
        } else {
            btn.classList.remove('visible');
        }
    }, { passive: true });
    
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();
</script>

<?php
// Schema.org markup dla organizacji (Organization schema)
$company_name = get_option('salon_auto_company_name', get_bloginfo('name'));
$company_phone = salon_auto_get_option('company_phone', '502 42 82 82');
$company_email = salon_auto_get_option('company_email', 'biuro@piekneauta.pl');
$company_address = salon_auto_get_option('company_address', '');
$footer_loza_url = salon_auto_get_option('cert_loza_url', 'https://lozaprzedsiebiorcow.pl');
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "<?php echo esc_js($company_name); ?>",
  "url": "<?php echo esc_js(home_url('/')); ?>",
  <?php if ($company_phone) : ?>
  "telephone": "<?php echo esc_js($company_phone); ?>",
  <?php endif; ?>
  <?php if ($company_email) : ?>
  "email": "<?php echo esc_js($company_email); ?>",
  <?php endif; ?>
  <?php if ($company_address) : ?>
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "<?php echo esc_js($company_address); ?>"
  },
  <?php endif; ?>
  "sameAs": [
    <?php
    $social_links = array();
    if ($footer_loza_url) $social_links[] = '"' . esc_js($footer_loza_url) . '"';
    echo !empty($social_links) ? implode(',', $social_links) : '';
    ?>
  ]
}
</script>

<?php wp_footer(); ?>
</body>
</html>


