/**
 * Automatyczne ładowanie wszystkich dostępnych samochodów na stronie katalogu
 * Pobiera dane z WordPress REST API i wyświetla wszystkie dostępne samochody
 */

(function() {
  'use strict';

  // Funkcja formatująca cenę
  function formatPrice(price) {
    if (!price || price === null) {
      return 'Cena do uzgodnienia';
    }
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

  // Funkcja generująca HTML dla karty samochodu
  function generateCarCard(car) {
    const carName = `${car.brand} ${car.model}`;
    let apiUrl = (window.salonAuto && window.salonAuto.home_url) || window.location.origin;
    if (!apiUrl.endsWith('/')) apiUrl += '/';
    const carUrl = car.url || `${apiUrl}samochody/${car.slug}/`;
    
    // Pobierz pierwsze zdjęcie lub użyj domyślnego - pełne URL
    let firstImage = '';
    if (car.images && car.images.length > 0) {
      firstImage = car.images[0].startsWith('http') ? car.images[0] : (apiUrl + car.images[0].replace(/^\//, ''));
    }
    
    const trim = car.trim || '';
    const priceText = formatPrice(car.price_pln_brutto);
    const statusBadge = getStatusBadge(car.status);
    
    return `
<article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
  <a href="${carUrl}" class="block">
    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 car-image-loading">
      ${firstImage ? `<img src="${firstImage}" alt="${carName}" class="w-full h-full object-cover car-image" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\\'w-full h-full bg-gray-200 flex items-center justify-center\\'><span class=\\'text-gray-400\\'>Brak zdjęcia</span></div>'">` : '<div class="w-full h-full bg-gray-200 flex items-center justify-center"><span class="text-gray-400">Brak zdjęcia</span></div>'}
      <div class="absolute top-3 right-3 z-10">
        ${statusBadge}
      </div>
    </div>
    <div class="p-6">
      <h3 class="text-xl font-bold text-primary mb-1">${carName}</h3>
      <p class="text-sm text-gray-500 mb-6">${trim}</p>
      <div class="flex items-end justify-between">
        <div class="text-2xl font-bold text-primary">${priceText}</div>
        <div class="text-accent font-bold">→</div>
      </div>
    </div>
  </a>
</article>
`;
  }

  // Główna funkcja ładowania wszystkich samochodów
  async function loadAllCars() {
    const container = document.getElementById('cars-catalog-container') || 
                      document.querySelector('.grid.grid-cols-1');
    if (!container) {
      console.warn('Kontener samochodów nie został znaleziony');
      return;
    }

    // Sprawdź czy katalog jest już wyrenderowany przez PHP (WordPress)
    // Jeśli tak, nie nadpisuj - PHP już wyświetlił samochody
    if (container.children.length > 0 && !container.dataset.dynamicLoad) {
      console.log('Katalog już wyrenderowany przez PHP, pomijam dynamiczne ładowanie');
      handleImageLoading();
      return;
    }

    try {
      // Pobierz dane samochodów z WordPress REST API
      let apiUrl = (window.salonAuto && window.salonAuto.home_url) || window.location.origin;
      if (!apiUrl.endsWith('/')) apiUrl += '/';
      const response = await fetch(`${apiUrl}wp-json/salon-auto/v1/cars`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const cars = await response.json();
      
      // Wyczyść istniejące karty
      container.innerHTML = '';

      // Jeśli nie ma samochodów
      if (cars.length === 0) {
        container.innerHTML = '<p class="col-span-full text-center text-gray-500 py-12">Brak dostępnych samochodów.</p>';
        return;
      }

      // Wygeneruj HTML dla wszystkich samochodów
      const html = cars.map(car => generateCarCard(car)).join('\n');
      
      // Wstaw HTML do kontenera
      container.innerHTML = html;

      // Inicjalizuj obsługę obrazków
      handleImageLoading();

    } catch (error) {
      console.error('Błąd podczas ładowania samochodów:', error);
      // Jeśli PHP już wyrenderował coś, nie nadpisuj błędem
      if (container.children.length === 0) {
      container.innerHTML = '<p class="col-span-full text-center text-red-500 py-12">Błąd podczas ładowania samochodów. Odśwież stronę.</p>';
      }
    }
  }

  // Obsługa loading states dla obrazków
  function handleImageLoading() {
    const images = document.querySelectorAll('img.car-image[loading="lazy"]');
    
    images.forEach(img => {
      const container = img.closest('.car-image-loading');
      
      // Jeśli obrazek już jest załadowany
      if (img.complete && img.naturalHeight !== 0) {
        img.classList.add('loaded');
        if (container) {
          container.classList.remove('car-image-loading');
        }
        return;
      }
      
      // Obsługa załadowania obrazka
      img.addEventListener('load', function() {
        img.classList.add('loaded');
        if (container) {
          container.classList.remove('car-image-loading');
        }
      });
      
      // Obsługa błędu ładowania
      img.addEventListener('error', function() {
        img.style.display = 'none';
        if (container) {
          container.innerHTML = '<div class="w-full h-full flex items-center justify-center bg-gray-200"><p class="text-gray-400 text-sm">Obrazek niedostępny</p></div>';
        }
      });
    });
  }

  // Uruchom po załadowaniu DOM
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadAllCars);
  } else {
    loadAllCars();
  }
})();

