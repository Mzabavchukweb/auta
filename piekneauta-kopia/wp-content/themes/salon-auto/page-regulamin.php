<?php
/**
 * Template Name: Regulamin
 * Szablon strony regulaminu
 */
get_header();
?>

<main class="min-h-screen bg-gray-50">
    
    <!-- Hero Section -->
    <section class="bg-primary text-white py-16 md:py-24">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl font-light tracking-tight uppercase italic mb-6">
                    Regulamin
                </h1>
                <p class="text-xl text-gray-300">
                    Regulamin świadczenia usług sprzedaży samochodów i leasingu
                </p>
            </div>
        </div>
    </section>
    
    <!-- Content -->
    <section class="py-16 md:py-24">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl shadow-sm p-8 md:p-12">
                    
                    <!-- Intro -->
                    <div class="prose prose-lg max-w-none mb-12">
                        <p class="text-gray-600 leading-relaxed">
                            Niniejszy Regulamin określa zasady świadczenia usług przez Piekne auta - Artur Kurzydłowski 
                            z siedzibą w Szczecinku, w zakresie sprzedaży samochodów, pośrednictwa leasingowego, 
                            pożyczek oraz ubezpieczeń.
                        </p>
                    </div>
                    
                    <!-- Sections -->
                    <div class="space-y-10">
                        
                        <section>
                            <h2 class="font-serif text-2xl text-primary mb-4 uppercase italic">§1. Postanowienia ogólne</h2>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Regulamin określa zasady korzystania z usług oferowanych przez Piekne auta.</li>
                                <li>Właścicielem firmy jest Artur Kurzydłowski, NIP: 6731525915.</li>
                                <li>Kontakt: telefon 502 42 82 82, email biuro@piekneauta.pl.</li>
                                <li>Siedziba firmy znajduje się w Szczecinku, woj. zachodniopomorskie.</li>
                            </ol>
                        </section>
                        
                        <section>
                            <h2 class="font-serif text-2xl text-primary mb-4 uppercase italic">§2. Zakres usług</h2>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Sprzedaż samochodów używanych i nowych klasy premium.</li>
                                <li>Pośrednictwo w zawieraniu umów leasingowych.</li>
                                <li>Pośrednictwo w uzyskiwaniu pożyczek na zakup pojazdu.</li>
                                <li>Pośrednictwo ubezpieczeniowe.</li>
                                <li>Doradztwo w zakresie doboru pojazdu i finansowania.</li>
                            </ol>
                        </section>
                        
                        <section>
                            <h2 class="font-serif text-2xl text-primary mb-4 uppercase italic">§3. Sprzedaż pojazdów</h2>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Wszystkie pojazdy są sprawdzone technicznie przed sprzedażą.</li>
                                <li>Każdy pojazd posiada pełną dokumentację i historię serwisową.</li>
                                <li>Ceny podane na stronie są cenami brutto w PLN.</li>
                                <li>Rezerwacja pojazdu wymaga wpłaty zaliczki w wysokości ustalonej indywidualnie.</li>
                                <li>Zaliczka jest zwrotna w przypadku rezygnacji z przyczyn leżących po stronie sprzedawcy.</li>
                            </ol>
                        </section>
                        
                        <section>
                            <h2 class="font-serif text-2xl text-primary mb-4 uppercase italic">§4. Leasing i finansowanie</h2>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Piekne auta działa jako pośrednik leasingowy i kredytowy.</li>
                                <li>Decyzja o przyznaniu finansowania należy do instytucji finansującej.</li>
                                <li>Warunki umowy leasingu/pożyczki określa instytucja finansująca.</li>
                                <li>Piekne auta nie pobiera prowizji od klienta za pośrednictwo finansowe.</li>
                            </ol>
                        </section>
                        
                        <section>
                            <h2 class="font-serif text-2xl text-primary mb-4 uppercase italic">§5. Reklamacje</h2>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Reklamacje można składać drogą elektroniczną na adres biuro@piekneauta.pl.</li>
                                <li>Reklamacja zostanie rozpatrzona w terminie 14 dni roboczych.</li>
                                <li>O wyniku rozpatrzenia reklamacji klient zostanie poinformowany drogą elektroniczną.</li>
                            </ol>
                        </section>
                        
                        <section>
                            <h2 class="font-serif text-2xl text-primary mb-4 uppercase italic">§6. Postanowienia końcowe</h2>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Regulamin wchodzi w życie z dniem publikacji na stronie.</li>
                                <li>Piekne auta zastrzega sobie prawo do zmiany regulaminu.</li>
                                <li>W sprawach nieuregulowanych stosuje się przepisy Kodeksu Cywilnego.</li>
                                <li>Sądem właściwym jest sąd siedziby sprzedawcy.</li>
                            </ol>
                        </section>
                        
                    </div>
                    
                    <!-- Footer -->
                    <div class="mt-12 pt-8 border-t border-gray-200 text-center">
                        <p class="text-sm text-gray-500">
                            Ostatnia aktualizacja: <?php echo date('d.m.Y'); ?>
                        </p>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
    
</main>

<?php get_footer(); ?>

