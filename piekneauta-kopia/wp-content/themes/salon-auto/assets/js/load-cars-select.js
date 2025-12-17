/**
 * Dynamiczne ładowanie listy aut do selecta w formularzach
 * Pobiera dane z WordPress REST API i wypełnia select dostępnymi autami
 */

(function() {
  'use strict';

  // Funkcja formatująca nazwę auta dla selecta
  function formatCarName(car) {
    const name = `${car.brand} ${car.model}`;
    const trim = car.trim ? ` ${car.trim}` : '';
    const year = car.year ? ` ${car.year}` : '';
    return `${name}${trim}${year}`;
  }

  // Funkcja wypełniająca select autami
  async function loadCarsToSelect(selectElement) {
    if (!selectElement) {
      console.warn('Element select nie został znaleziony');
      return;
    }

    try {
      // Pobierz dane samochodów z WordPress REST API
      let apiUrl = (window.salonAuto && window.salonAuto.home_url) || window.location.origin;
      if (!apiUrl.endsWith('/')) apiUrl += '/';
      const response = await fetch(`${apiUrl}wp-json/salon-auto/v1/cars?status=available`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const cars = await response.json();
      
      // Filtruj: tylko dostępne auta (dodatkowe sprawdzenie)
      const availableCars = cars.filter(car => car.status === 'available');
      
      // Sortuj alfabetycznie po marce i modelu
      availableCars.sort((a, b) => {
        const nameA = `${a.brand} ${a.model}`;
        const nameB = `${b.brand} ${b.model}`;
        return nameA.localeCompare(nameB, 'pl');
      });

      // Dodaj opcję "Wybierz auto (opcjonalnie)"
      const defaultOption = document.createElement('option');
      defaultOption.value = '';
      defaultOption.textContent = 'Wybierz auto (opcjonalnie)';
      selectElement.appendChild(defaultOption);

      // Dodaj auta do selecta
      availableCars.forEach(car => {
        const option = document.createElement('option');
        option.value = formatCarName(car);
        option.textContent = formatCarName(car);
        selectElement.appendChild(option);
      });

    } catch (error) {
      console.error('Błąd podczas ładowania listy aut:', error);
      // W przypadku błędu, pozostaw pole tekstowe lub wyświetl komunikat
      const errorOption = document.createElement('option');
      errorOption.value = '';
      errorOption.textContent = 'Błąd ładowania listy aut';
      selectElement.appendChild(errorOption);
    }
  }

  // Funkcja inicjalizująca wszystkie selecty aut
  function initCarSelects() {
    const carSelects = document.querySelectorAll('select[name="car"], select[id="car-select"]');
    carSelects.forEach(select => {
      loadCarsToSelect(select);
    });
  }

  // Uruchom po załadowaniu DOM
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCarSelects);
  } else {
    initCarSelects();
  }
})();

