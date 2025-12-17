/**
 * Automatyczne ładowanie podobnych ofert samochodów
 * Pobiera dane z WordPress REST API i wyświetla dostępne samochody (z wykluczeniem aktualnego)
 */

(function() {
  'use strict';

  // Funkcja formatująca cenę
  function formatPrice(price) {
    return new Intl.NumberFormat('pl-PL', {
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(price) + ' zł';
  }

  // Funkcja generująca badge statusu
  function getStatusBadge(status) {
    switch(status) {
      case 'reserved':
        return '<span class="px-3 py-1.5 rounded-full text-xs font-bold bg-amber-500 text-white shadow-lg">ZAREZERWOWANY</span>';
      case 'sold':
        return '<span class="px-3 py-1.5 rounded-full text-xs font-bold bg-red-500 text-white shadow-lg">SPRZEDANY</span>';
      default:
        return '<span class="px-3 py-1.5 rounded-full text-xs font-bold bg-accent text-white shadow-lg">DOSTĘPNY</span>';
    }
  }

  // Funkcja generująca HTML dla karty samochodu z animacją 15 zdjęć - DOKŁADNIE JAK WERSJA STATYCZNA
  function generateCarCard(car) {
    const carName = `${car.brand} ${car.model}`;
    let apiUrl = (window.salonAuto && window.salonAuto.home_url) || window.location.origin;
    if (!apiUrl.endsWith('/')) apiUrl += '/';
    const carUrl = car.url || `${apiUrl}samochody/${car.slug}/`;
    
    // Pobierz pierwsze 15 zdjęć lub użyj pierwszego zdjęcia jako fallback - DOKŁADNIE JAK WERSJA STATYCZNA
    const images = car.images && car.images.length > 0 
      ? car.images.slice(0, 15)
      : [];
    
    // Generuj slajdy - DOKŁADNIE JAK WERSJA STATYCZNA
    let slides = '';
    if (images.length > 0) {
      slides = images.map((img, index) => {
      const isActive = index === 0 ? 'active' : '';
        // URL może być pełny (z REST API) lub tylko nazwa pliku
        const imgUrl = img.startsWith('http') ? img : ((window.salonAuto && window.salonAuto.template_directory_uri) ? (window.salonAuto.template_directory_uri + '/images/' + img) : ('/images/' + img));
       return `<div class="car-slide ${isActive}" style="position: absolute !important; inset: 0 !important; opacity: ${isActive ? '1' : '0'} !important; transition: opacity 1.5s ease-in-out !important; z-index: ${isActive ? '1' : '0'} !important;">
         <img src="${imgUrl}" alt="${carName}" class="w-full h-full object-cover" style="display: block !important; width: 100% !important; height: 100% !important; object-fit: cover !important; position: relative !important;">
       </div>`;
      }).join('\n      ');
    } else {
      // Fallback - użyj domyślnego obrazka
      const fallbackUrl = (window.salonAuto && window.salonAuto.template_directory_uri) 
        ? (window.salonAuto.template_directory_uri + '/images/og-default.svg')
        : '/images/og-default.svg';
      slides = `<div class="car-slide active">
        <img src="${fallbackUrl}" alt="${carName}" class="w-full h-full object-cover" loading="lazy">
      </div>`;
    }
    
    // Dynamiczny badge statusu
    const statusBadge = getStatusBadge(car.status);
    
    return `
<article class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-accent/30 transition-all duration-200 overflow-hidden group">
  <a href="${carUrl}" class="block focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent rounded-xl">
    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
      <div class="car-slider w-full h-full relative">
      ${slides}
      </div>
      <div class="absolute top-3 right-3 z-10">
        ${statusBadge}
      </div>
    </div>
    <div class="p-6">
      <h3 class="text-xl font-bold text-primary mb-1">${carName}</h3>
      <p class="text-sm text-gray-500 mb-6">${car.trim || ''}</p>
      <div class="flex items-end justify-between">
        <div class="text-2xl font-bold text-primary">${formatPrice(car.price_pln_brutto)}</div>
        <div class="text-accent font-bold hover:translate-x-1 transition-transform" aria-hidden="true">→</div>
      </div>
    </div>
  </a>
</article>`;
  }

  // Główna funkcja ładowania podobnych ofert
  async function loadSimilarCars() {
    const container = document.getElementById('similar-cars-container');
    if (!container) {
      return;
    }

    // Pobierz slug aktualnego samochodu z atrybutu data-car-slug lub z URL
    const currentSlug = container.getAttribute('data-car-slug') || 
                        window.location.pathname.split('/').filter(Boolean).pop();

    try {
      // Pobierz dane samochodów z WordPress REST API
      let apiUrl = (window.salonAuto && window.salonAuto.home_url) || window.location.origin;
      // Upewnij się że URL kończy się na /
      if (!apiUrl.endsWith('/')) apiUrl += '/';
      // Pobierz wszystkie dostępne i zarezerwowane (nie sprzedane)
      const apiEndpoint = `${apiUrl}wp-json/salon-auto/v1/cars?exclude_slug=${encodeURIComponent(currentSlug)}&limit=3`;
      console.log('📦 Podobne oferty - pobieranie z:', apiEndpoint);
      const response = await fetch(apiEndpoint);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const cars = await response.json();
      console.log('📦 Podobne oferty - otrzymano:', cars.length, 'samochodów');
      console.log('📦 Pełna odpowiedź API:', cars);
      
      // Filtruj: wyklucz aktualny samochód (dodatkowe sprawdzenie)
      const availableCars = cars.filter(car => car.slug !== currentSlug);
      
      // Sprawdź czy samochody mają obrazy
      availableCars.forEach((car, idx) => {
        if (!car.images || car.images.length === 0) {
          console.error(`❌ Samochód ${idx + 1} (${car.brand} ${car.model}) NIE MA ZDJĘĆ!`);
        }
      });

      // Jeśli nie ma dostępnych samochodów, ukryj sekcję
      if (availableCars.length === 0) {
        console.log('📦 Brak podobnych ofert do wyświetlenia');
        const section = container.closest('section');
        if (section) {
          section.style.display = 'none';
        }
        return;
      }

      // Wybierz maksymalnie 3 samochody (już ograniczone przez API limit=3)
      const similarCars = availableCars.slice(0, 3);
      
      // Debug: pokaż zdjęcia wszystkich samochodów
      const themeUri = (window.salonAuto && window.salonAuto.template_directory_uri) || '';
      const homeUrl = (window.salonAuto && window.salonAuto.home_url) || window.location.origin + '/';
      similarCars.forEach((car, idx) => {
        console.log(`📦 Samochód ${idx + 1}:`, car.brand, car.model, `(${car.slug})`);
        console.log(`📦 Zdjęcia (${car.images ? car.images.length : 0}):`, car.images);
        if (car.images && car.images.length > 0) {
          console.log(`📦 Pierwsze zdjęcie:`, car.images[0]);
          console.log(`📦 themeUri:`, themeUri);
          console.log(`📦 homeUrl:`, homeUrl);
          // Sprawdź czy URL jest poprawny
          const testUrl = car.images[0];
          if (testUrl && !testUrl.startsWith('http')) {
            console.warn(`⚠️ URL obrazu nie jest pełny:`, testUrl);
          }
        } else {
          console.error(`❌ BRAK ZDJĘĆ dla ${car.brand} ${car.model}! API zwróciło pustą tablicę.`);
          console.error(`❌ Sprawdź czy w bazie danych jest wypełnione pole 'gallery_files' dla tego samochodu.`);
        }
      });

      // Wygeneruj HTML
      const html = similarCars.map(car => generateCarCard(car)).join('\n');
      
      // Wstaw HTML do kontenera
      container.innerHTML = html;

      // Inicjalizuj animacje dla nowo załadowanych kart - DOKŁADNIE JAK WERSJA STATYCZNA
      initCarSliders();

    } catch (error) {
      console.error('Błąd podczas ładowania podobnych ofert:', error);
      // W przypadku błędu, ukryj sekcję lub wyświetl komunikat
      const section = container.closest('section');
      if (section) {
        section.style.display = 'none';
      }
    }
  }

  // Funkcja inicjalizująca animacje car-slider - DOKŁADNIE JAK WERSJA STATYCZNA
  function initCarSliders() {
    const carSliders = document.querySelectorAll('.car-slider');
    
    carSliders.forEach(slider => {
      // Sprawdź, czy slider już ma przypisany interval
      if (slider.dataset.initialized === 'true') {
        return;
      }
      
      const slides = slider.querySelectorAll('.car-slide');
      if (slides.length === 0 || slides.length === 1) return;
      
      let currentSlide = 0;
      
      function nextSlide() {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add('active');
      }
      
      // Każdy slider zmienia się w losowym odstępie (3-4s) dla naturalnego efektu
      const interval = 3000 + Math.random() * 1000;
      setInterval(nextSlide, interval);
      
      // Oznacz jako zainicjalizowany
      slider.dataset.initialized = 'true';
    });
  }

  // Uruchom po załadowaniu DOM - DOKŁADNIE JAK WERSJA STATYCZNA
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      loadSimilarCars();
      initCarSliders();
    });
  } else {
    loadSimilarCars();
    initCarSliders();
  }
})();

