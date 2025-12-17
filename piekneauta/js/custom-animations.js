/**
 * Custom Premium Animations & Interactions
 * PiękneAuta.pl - Custom JavaScript Enhancements
 */

(function() {
  'use strict';

  // ===================================
  // 🎬 SCROLL REVEAL ANIMATIONS
  // ===================================
  
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const animateOnScroll = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, observerOptions);

  // Obserwuj wszystkie elementy z fade-in animations
  document.addEventListener('DOMContentLoaded', () => {
    const fadeElements = document.querySelectorAll(
      '.fade-in-up, .fade-in-up-delay-1, .fade-in-up-delay-2, .fade-in-up-delay-3'
    );
    
    fadeElements.forEach(el => {
      animateOnScroll.observe(el);
    });
  });

  // ===================================
  // 🚗 LAZY LOADING IMAGE ANIMATIONS
  // ===================================
  
  const imageObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.classList.add('loaded');
        imageObserver.unobserve(img);
      }
    });
  });

  document.addEventListener('DOMContentLoaded', () => {
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    lazyImages.forEach(img => {
      imageObserver.observe(img);
    });
  });

  // ===================================
  // ✨ PARALLAX SCROLL EFFECT (subtle)
  // ===================================
  
  let ticking = false;
  
  function updateParallax() {
    const scrolled = window.pageYOffset;
    const parallaxElements = document.querySelectorAll('.trust-badge-float');
    
    parallaxElements.forEach((el, index) => {
      const speed = 0.05 + (index * 0.02); // Różne prędkości dla różnych elementów
      const yPos = -(scrolled * speed);
      el.style.transform = `translateY(${yPos}px)`;
    });
    
    ticking = false;
  }

  window.addEventListener('scroll', () => {
    if (!ticking && window.matchMedia('(min-width: 1024px)').matches) {
      window.requestAnimationFrame(updateParallax);
      ticking = true;
    }
  });

  // ===================================
  // 🎯 SMOOTH SCROLL FOR ANCHOR LINKS
  // ===================================
  
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href === '#') return;
      
      e.preventDefault();
      const target = document.querySelector(href);
      
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });

  // 3D Tilt effect removed - cards now have subtle hover only

  // ===================================
  // 🌊 CURSOR FOLLOW EFFECT (subtle)
  // ===================================
  
  if (window.matchMedia('(min-width: 1024px)').matches) {
    const hero = document.querySelector('.hero-custom-bg');
    
    if (hero) {
      hero.addEventListener('mousemove', (e) => {
        const x = e.clientX / window.innerWidth;
        const y = e.clientY / window.innerHeight;
        
        hero.style.setProperty('--mouse-x', x);
        hero.style.setProperty('--mouse-y', y);
      });
    }
  }

  // ===================================
  // 📞 STICKY CTA ENHANCEMENT
  // ===================================
  
  const stickyCTA = document.querySelector('.sticky-cta-artur');
  
  if (stickyCTA) {
    let scrollPosition = 0;
    
    window.addEventListener('scroll', () => {
      const currentScroll = window.pageYOffset;
      
      // Hide on scroll down (after 200px), show on scroll up
      if (currentScroll > 200) {
        if (currentScroll > scrollPosition) {
          stickyCTA.style.opacity = '0.3';
        } else {
          stickyCTA.style.opacity = '1';
        }
      } else {
        stickyCTA.style.opacity = '1';
      }
      
      scrollPosition = currentScroll;
    });
    
    // Pulse animation on scroll stop
    let scrollTimeout;
    window.addEventListener('scroll', () => {
      clearTimeout(scrollTimeout);
      stickyCTA.style.transform = 'scale(0.95)';
      
      scrollTimeout = setTimeout(() => {
        stickyCTA.style.transform = 'scale(1)';
      }, 150);
    });
  }

  // ===================================
  // 🎨 TEXT GRADIENT ANIMATION
  // ===================================
  
  const gradientTexts = document.querySelectorAll('.text-gradient-premium');
  
  gradientTexts.forEach(text => {
    text.addEventListener('mouseenter', () => {
      text.style.animationPlayState = 'running';
    });
  });

  // ===================================
  // 📊 COUNTER ANIMATION
  // ===================================
  
  function animateValue(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 16); // 60fps
    let current = start;
    
    const timer = setInterval(() => {
      current += increment;
      if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
        current = end;
        clearInterval(timer);
      }
      element.textContent = Math.floor(current).toLocaleString('pl-PL');
    }, 16);
  }

  // Animuj liczby gdy są w viewport
  const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const element = entry.target;
        const finalValue = parseInt(element.getAttribute('data-count'));
        
        if (finalValue && !element.classList.contains('counted')) {
          element.classList.add('counted');
          animateValue(element, 0, finalValue, 2000);
        }
        
        statsObserver.unobserve(element);
      }
    });
  });

  document.querySelectorAll('[data-count]').forEach(el => {
    statsObserver.observe(el);
  });

  // ===================================
  // 🎪 MODAL/LIGHTBOX ENHANCEMENT
  // ===================================
  
  // Handle image clicks for lightbox (if needed in future)
  document.querySelectorAll('.car-card-premium img').forEach(img => {
    img.style.cursor = 'pointer';
    img.addEventListener('click', (e) => {
      e.preventDefault();
      // TODO: Add lightbox functionality if needed
    });
  });

  // ===================================
  // 🔄 LOADING STATE MANAGEMENT
  // ===================================
  
  window.addEventListener('load', () => {
    // Remove skeleton loaders if any
    document.querySelectorAll('.skeleton-loader').forEach(skeleton => {
      skeleton.classList.remove('skeleton-loader');
    });
    
    // Add loaded class to body
    document.body.classList.add('page-loaded');
  });

  // ===================================
  // 📱 MOBILE TOUCH ENHANCEMENTS
  // ===================================
  
  if ('ontouchstart' in window) {
    document.querySelectorAll('.car-card-premium').forEach(card => {
      card.addEventListener('touchstart', () => {
        card.classList.add('touch-active');
      });
      
      card.addEventListener('touchend', () => {
        card.classList.remove('touch-active');
      });
    });
  }

  // ===================================
  // ⚡ PERFORMANCE MONITORING
  // ===================================
  
  if ('PerformanceObserver' in window) {
    try {
      const perfObserver = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
          if (entry.duration > 100) {
            console.log(`⚠️ Slow animation detected: ${entry.name} (${entry.duration}ms)`);
          }
        }
      });
      
      perfObserver.observe({ entryTypes: ['measure'] });
    } catch (e) {
      // Silently fail if PerformanceObserver not supported
    }
  }

  // ===================================
  // 🎯 CONSOLE EASTER EGG
  // ===================================
  
  console.log('%c🚗 PiękneAuta.pl', 'font-size: 24px; font-weight: bold; color: #2663F2;');
  console.log('%c28 lat doświadczenia w branży motoryzacyjnej', 'font-size: 14px; color: #6B7280;');
  console.log('%c✨ Premium customizations by Claude', 'font-size: 12px; color: #16A34A; font-style: italic;');

  // ===================================
  // 🌐 ACCESSIBILITY IMPROVEMENTS
  // ===================================
  
  // Announce page changes to screen readers
  document.addEventListener('DOMContentLoaded', () => {
    const announcer = document.createElement('div');
    announcer.setAttribute('aria-live', 'polite');
    announcer.setAttribute('aria-atomic', 'true');
    announcer.className = 'sr-only';
    document.body.appendChild(announcer);
  });

  // Skip link functionality
  const skipLink = document.querySelector('a[href="#main-content"]');
  if (skipLink) {
    skipLink.addEventListener('click', (e) => {
      e.preventDefault();
      const main = document.getElementById('main-content');
      if (main) {
        main.setAttribute('tabindex', '-1');
        main.focus();
      }
    });
  }

})();

// Export for potential module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = {};
}

