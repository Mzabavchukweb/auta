/**
 * Cookie Consent API Client
 * Wysyła zgody cookies do backendu zgodnie z wymaganiami RODO/UOKiK
 */

(function() {
  'use strict';
  
  // URL backendu API
  const API_BASE_URL = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? 'http://localhost:7080/api' 
    : '/api'; // Produkcja - używa tego samego hosta co strona
  
  /**
   * Generuje unikalny Device ID dla urządzenia użytkownika
   * Przechowywany w localStorage
   */
  function getDeviceId() {
    let deviceId = localStorage.getItem('deviceId');
    
    if (!deviceId) {
      // Generuj unikalny ID na podstawie różnych właściwości przeglądarki
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');
      ctx.textBaseline = 'top';
      ctx.font = '14px Arial';
      ctx.fillText('Device ID generation', 2, 2);
      
      const fingerprint = [
        navigator.userAgent,
        navigator.language,
        screen.width + 'x' + screen.height,
        new Date().getTimezoneOffset(),
        canvas.toDataURL(),
        navigator.hardwareConcurrency || '',
        navigator.platform
      ].join('|');
      
      // Hash fingerprint aby uzyskać unikalny ID
      deviceId = btoa(fingerprint).substring(0, 32);
      
      // Dodaj timestamp aby zwiększyć unikalność
      deviceId += '-' + Date.now().toString(36);
      
      localStorage.setItem('deviceId', deviceId);
    }
    
    return deviceId;
  }
  
  /**
   * Wysyła zgodę cookies do backendu API
   */
  function sendConsentToBackend(consent) {
    const deviceId = getDeviceId();
    
    const payload = {
      device_id: deviceId,
      necessary: consent.necessary || true,
      analytics: consent.analytics || false,
      marketing: consent.marketing || false,
      timestamp: consent.timestamp || new Date().toISOString()
    };
    
    // Wysyłaj asynchronicznie (nie blokuj UI)
    fetch(`${API_BASE_URL}/cookie-consent`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload),
      // Nie czekaj na odpowiedź - wysyłaj w tle
      keepalive: true
    })
    .then(response => {
      if (!response.ok) {
        console.warn('Błąd podczas wysyłania zgody do backendu:', response.statusText);
      } else {
        console.log('Zgoda została wysłana do backendu');
      }
    })
    .catch(error => {
      // Cicha obsługa błędów - nie przeszkadzaj użytkownikowi
      console.warn('Nie udało się wysłać zgody do backendu (może być offline):', error.message);
    });
  }
  
  /**
   * Pobiera zgodę z backendu dla tego urządzenia
   */
  function getConsentFromBackend(callback) {
    const deviceId = getDeviceId();
    
    fetch(`${API_BASE_URL}/cookie-consent/${deviceId}`)
      .then(response => response.json())
      .then(data => {
        if (data.success && data.consent) {
          callback(data.consent);
        } else {
          callback(null);
        }
      })
      .catch(error => {
        console.warn('Nie udało się pobrać zgody z backendu:', error.message);
        callback(null);
      });
  }
  
  // Eksportuj funkcje globalnie
  window.CookieConsentAPI = {
    sendConsent: sendConsentToBackend,
    getConsent: getConsentFromBackend,
    getDeviceId: getDeviceId
  };
})();

