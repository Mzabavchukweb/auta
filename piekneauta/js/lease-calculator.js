/**
 * Lease Calculator - Alpine.js Component
 * Kalkulator leasingowy dla PiękneAuta.pl
 */

document.addEventListener('alpine:init', () => {
  Alpine.data('leaseCalculator', () => ({
    // Parametry
    carPrice: 200000,
    downPaymentPercent: 10,
    leaseTerm: 48,
    residualPercent: 1,
    apr: 9.9,
    
    // Inicjalizacja
    async init() {
      try {
        const response = await fetch('/data/lease_config.json');
        const config = await response.json();
        
        this.downPaymentPercent = config.default.down_payment_pct;
        this.leaseTerm = config.default.term_months;
        this.residualPercent = config.default.residual_pct;
        this.apr = config.apr_pct;
      } catch (error) {
        console.warn('Używam wartości domyślnych');
      }
    },
    
    // Obliczenia
    get downPaymentAmount() {
      return Math.round(this.carPrice * (this.downPaymentPercent / 100));
    },
    
    get residualValue() {
      return Math.round(this.carPrice * (this.residualPercent / 100));
    },
    
    get financedAmount() {
      return this.carPrice - this.downPaymentAmount;
    },
    
    get monthlyPayment() {
      const P = this.financedAmount;
      const r = this.apr / 100 / 12;
      const n = this.leaseTerm;
      const FV = this.residualValue;
      
      if (r === 0) {
        return Math.round((P - FV) / n);
      }
      
      const pow = Math.pow(1 + r, n);
      const numerator = P * r * pow - FV * r;
      const denominator = pow - 1;
      
      return Math.round(numerator / denominator);
    },
    
    // Formatowanie
    formatCurrency(amount) {
      return new Intl.NumberFormat('pl-PL', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(amount);
    }
  }));
});

