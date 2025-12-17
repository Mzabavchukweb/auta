/**
 * UX Improvements - PiękneAuta.pl
 * Ulepszenia doświadczenia użytkownika
 */

(function() {
  'use strict';

  // Usunięto: Back to top button i Scroll progress indicator

  // ===================================
  // 📝 FORM LOADING INDICATORS
  // ===================================
  
  function initFormLoadingStates() {
    // Znajdź wszystkie formularze z Alpine.js
    document.querySelectorAll('form[x-data*="submitted"]').forEach(form => {
      const submitBtn = form.querySelector('button[type="submit"]');
      if (!submitBtn) return;

      form.addEventListener('submit', function(e) {
        // Sprawdź czy to jest formularz z Alpine.js (nie zapobiegaj domyślnemu submit)
        if (form.hasAttribute('@submit.prevent')) {
          // Dodaj loading state do przycisku
          const originalText = submitBtn.innerHTML;
          submitBtn.disabled = true;
          submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Wysyłanie...
          `;

          // Przywróć po 10 sekundach (timeout)
          setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          }, 10000);
        }
      });
    });
  }


  // ===================================
  // ✅ CLIENT-SIDE FORM VALIDATION
  // ===================================
  
  function initFormValidation() {
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', function(e) {
        // Sprawdź tylko formularze bez @submit.prevent (standardowe)
        if (!form.hasAttribute('@submit.prevent')) {
          const requiredFields = form.querySelectorAll('[required]');
          let isValid = true;
          const errors = [];

          requiredFields.forEach(field => {
            // Usuń poprzednie błędy
            field.classList.remove('border-red-500');
            const errorMsg = field.parentElement.querySelector('.field-error');
            if (errorMsg) errorMsg.remove();

            // Walidacja
            if (!field.value.trim()) {
              isValid = false;
              field.classList.add('border-red-500');
              const error = document.createElement('p');
              error.className = 'field-error text-red-600 text-sm mt-1';
              error.textContent = 'To pole jest wymagane';
              field.parentElement.appendChild(error);
            }

            // Walidacja email
            if (field.type === 'email' && field.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
              isValid = false;
              field.classList.add('border-red-500');
              const error = document.createElement('p');
              error.className = 'field-error text-red-600 text-sm mt-1';
              error.textContent = 'Podaj poprawny adres email';
              field.parentElement.appendChild(error);
            }

            // Walidacja NIP (10 cyfr)
            if (field.name === 'nip' && field.value && !/^\d{10}$/.test(field.value.replace(/\s/g, ''))) {
              isValid = false;
              field.classList.add('border-red-500');
              const error = document.createElement('p');
              error.className = 'field-error text-red-600 text-sm mt-1';
              error.textContent = 'NIP musi składać się z 10 cyfr';
              field.parentElement.appendChild(error);
            }
          });

          if (!isValid) {
            e.preventDefault();
            // Przewiń do pierwszego błędu
            const firstError = form.querySelector('.border-red-500');
            if (firstError) {
              firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
              firstError.focus();
            }
          }
        }
      });

      // Real-time validation
      form.querySelectorAll('input, textarea, select').forEach(field => {
        field.addEventListener('blur', function() {
          if (this.hasAttribute('required') && !this.value.trim()) {
            this.classList.add('border-red-500');
          } else {
            this.classList.remove('border-red-500');
            const errorMsg = this.parentElement.querySelector('.field-error');
            if (errorMsg) errorMsg.remove();
          }
        });
      });
    });
  }

  // ===================================
  // 🎯 KEYBOARD SHORTCUTS
  // ===================================
  
  function initKeyboardShortcuts() {
    // ESC - zamknij lightbox/modal
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        // Zamknij lightbox galerii
        const lightbox = document.querySelector('[x-show*="lightbox"]');
        if (lightbox && window.Alpine) {
          const alpineData = Alpine.$data(lightbox);
          if (alpineData && typeof alpineData.closeLightbox === 'function') {
            alpineData.closeLightbox();
          }
        }

        // Zamknij cookie modal
        const cookieModal = document.getElementById('cookieModal');
        if (cookieModal && cookieModal.style.display !== 'none') {
          if (typeof closeModal === 'function') {
            closeModal();
          }
        }
      }
    });
  }

  // ===================================
  // 📱 STICKY HEADER - WYŁĄCZONE
  // ===================================
  // Usunięto - powodowało layout shifts/skakanie
  // Header jest sticky przez CSS (sticky top-0), nie potrzeba JS
  
  function initStickyHeader() {
    // Pusta funkcja - header jest sticky przez CSS
    // Nie modyfikujemy headera przez JS - to powoduje skoki
  }


  // ===================================
  // 🚀 INITIALIZATION
  // ===================================
  
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      initFormLoadingStates();
      initFormValidation();
      initKeyboardShortcuts();
      initStickyHeader();
    });
  } else {
    initFormLoadingStates();
    initFormValidation();
    initKeyboardShortcuts();
    initStickyHeader();
  }

})();

