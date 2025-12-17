/**
 * Main JavaScript for Salon Auto Theme
 * 
 * Combines functionality from original static site
 */

(function() {
    'use strict';

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        // Hero & Car Sliders initialization
        initSliders();
        
        // Cookie banner functionality (if not already loaded)
        if (typeof window.acceptAllCookies === 'undefined') {
            initCookieBanner();
        }
    }

    /**
     * Hero & Car Sliders
     */
    function initSliders() {
        // Hero Slider - Desktop i Mobile
        const heroSliders = document.querySelectorAll('.hero-slider, .hero-slider-mobile');
        if (heroSliders.length) {
            heroSliders.forEach(slider => {
                const slides = slider.querySelectorAll('.slider-image');
                if (!slides.length) return;
                
                let currentSlide = 0;
                
                function nextSlide() {
                    slides[currentSlide].classList.remove('active');
                    currentSlide = (currentSlide + 1) % slides.length;
                    slides[currentSlide].classList.add('active');
                }
                
                setInterval(nextSlide, 5000);
            });
        }

        // Car Card Sliders
        const carSliders = document.querySelectorAll('.car-slider');
        if (carSliders.length) {
            carSliders.forEach(slider => {
                const slides = slider.querySelectorAll('.car-slide');
                if (!slides.length) return;
                
                let currentSlide = 0;
                
                function nextSlide() {
                    slides[currentSlide].classList.remove('active');
                    currentSlide = (currentSlide + 1) % slides.length;
                    slides[currentSlide].classList.add('active');
                }
                
                const interval = 3000 + Math.random() * 1000;
                setInterval(nextSlide, interval);
            });
        }
    }

    /**
     * Cookie Banner Functions
     */
    function initCookieBanner() {
        // Functions are defined in footer.php inline script
        // This is just a placeholder for additional functionality if needed
    }
})();

