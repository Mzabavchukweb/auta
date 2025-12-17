/**
 * Load Reviews from JSON
 * Dynamiczne ładowanie opinii Klientów
 */

document.addEventListener('DOMContentLoaded', async () => {
  const reviewsContainer = document.getElementById('reviews-container');
  
  if (!reviewsContainer) return;
  
  try {
    // Pobierz ścieżkę do motywu
    const themeUri = window.salonAutoThemeUri || '';
    // Załaduj opinie z JSON
    const response = await fetch(`${themeUri}/data/reviews.json`);
    const reviews = await response.json();
    
    // Weź pierwsze 3 opinie
    const displayedReviews = reviews.slice(0, 3);
    
    // Generuj HTML dla każdej opinii
    const reviewsHTML = displayedReviews.map(review => {
      const stars = Array(review.rating).fill().map(() => `
        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="#FDB022" aria-hidden="true" style="fill: #FDB022 !important; stroke: none !important;">
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" fill="#FDB022" stroke="none" style="fill: #FDB022 !important; stroke: none !important;"></path>
        </svg>
      `).join('');
      
      return `
        <article class="bg-white p-10 rounded-2xl shadow-soft border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-500">
          <!-- Rating Stars -->
          <div class="flex items-center mb-4">
            ${stars}
          </div>
          
          <!-- Content -->
          <p class="text-gray-700 mb-6 italic leading-relaxed text-lg">
            "${review.content}"
          </p>
          
          <!-- Author -->
          <div class="flex items-center justify-between border-t-2 border-gray-100 pt-5">
            <div class="font-bold text-primary text-lg">${review.name}</div>
            <div class="text-sm text-gray-500 font-medium uppercase tracking-wide">${review.source}</div>
          </div>
        </article>
      `;
    }).join('');
    
    // Wstaw HTML do kontenera
    reviewsContainer.innerHTML = reviewsHTML;
    
  } catch (error) {
    console.error('Nie udało się załadować opinii:', error);
    // Pozostaw istniejące opinie hardcoded jako fallback
  }
});

