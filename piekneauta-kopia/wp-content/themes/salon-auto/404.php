<?php
/**
 * 404 Page Template
 * Wyświetlana gdy strona nie została znaleziona
 */
get_header();
?>

<main class="min-h-screen bg-gray-50 flex items-center justify-center py-20">
    <div class="max-w-2xl mx-auto px-4 text-center">
        
        <!-- 404 Icon -->
        <div class="mb-8">
            <svg class="w-32 h-32 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <!-- Error Number -->
        <h1 class="font-serif text-8xl md:text-9xl font-light text-primary mb-4 tracking-tight">
            404
        </h1>
        
        <!-- Title -->
        <h2 class="text-2xl md:text-3xl font-semibold text-gray-800 mb-4">
            Strona nie znaleziona
        </h2>
        
        <!-- Description -->
        <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
            Przepraszamy, ale strona której szukasz nie istnieje lub została przeniesiona.
        </p>
        
        <!-- Suggestions -->
        <div class="bg-white rounded-2xl p-6 mb-8 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-700 mb-4">Co możesz zrobić:</h3>
            <ul class="text-gray-600 space-y-2 text-left max-w-sm mx-auto">
                <li class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-accent flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Sprawdź czy adres URL jest poprawny
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-accent flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Wróć na stronę główną
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-accent flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Zobacz dostępne samochody
                </li>
            </ul>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center justify-center px-8 py-4 bg-primary text-white font-semibold rounded-xl hover:bg-primary/90 transition-all duration-200 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Strona główna
            </a>
            <a href="<?php echo esc_url(home_url('/samochody/')); ?>" class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary font-semibold rounded-xl border-2 border-primary hover:bg-primary hover:text-white transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Zobacz samochody
            </a>
        </div>
        
        <!-- Contact -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <p class="text-gray-500 mb-2">Potrzebujesz pomocy?</p>
            <?php 
            $phone = salon_auto_get_option('phone', '502 42 82 82');
            $phone_clean = preg_replace('/[^0-9+]/', '', $phone);
            ?>
            <a href="tel:+48<?php echo esc_attr($phone_clean); ?>" class="inline-flex items-center text-accent font-semibold hover:underline">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                <?php echo esc_html($phone); ?>
            </a>
        </div>
        
    </div>
</main>

<?php get_footer(); ?>

