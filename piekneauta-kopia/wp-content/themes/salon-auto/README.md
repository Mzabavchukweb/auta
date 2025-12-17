# Motyw WordPress: Salon Auto

Custom WordPress theme dla Piękne Auta - salon samochodów premium.

## Instalacja

1. Skopiuj folder `salon-auto` do `wp-content/themes/`
2. Zaloguj się do panelu WordPress
3. Przejdź do **Wygląd → Motywy**
4. Aktywuj motyw **Salon Auto**

## Wymagane wtyczki

### 1. Advanced Custom Fields (ACF)
- **Wymagane!** Bez ACF motyw nie będzie działał poprawnie
- Pobierz z: https://www.advancedcustomfields.com/
- Lub zainstaluj przez panel WordPress: **Wtyczki → Dodaj nową → Szukaj "Advanced Custom Fields"**

### 2. ACF Options Page (opcjonalnie, dla ACF Pro)
- Jeśli masz ACF Pro, możesz użyć Options Pages
- Jeśli masz darmową wersję ACF, użyj wtyczki: **ACF Options Page** (darmowa)

## Konfiguracja ACF

**DOBRA WIADOMOŚĆ:** Pola ACF są automatycznie rejestrowane przez kod motywu! 

Po zainstalowaniu ACF i aktywacji motywu, wszystkie grupy pól są już gotowe. Nie musisz ich tworzyć ręcznie.

### Automatycznie zarejestrowane grupy pól:

1. **"Samochody"** - dla Custom Post Type "car"
2. **"Ustawienia Strony Głównej"** - dla Options Page
3. **"Ustawienia Ogólne"** - dla Options Page

### Jeśli chcesz edytować pola ręcznie:

Pola są zdefiniowane w `functions.php` (funkcja `salon_auto_register_acf_field_groups`). Możesz je modyfikować tam lub przez panel ACF.

### Ręczna konfiguracja (jeśli automatyczna nie działa):

Po zainstalowaniu ACF, możesz utworzyć następujące grupy pól ręcznie:

### Grupa 1: "Samochody" (dla Custom Post Type "car")

**Lokalizacja:** Post Type is equal to car

**Pola:**
- `price` (Text) - Label: "Cena (PLN)"
- `year` (Number) - Label: "Rok produkcji"
- `mileage` (Number) - Label: "Przebieg (km)"
- `gearbox` (Select) - Label: "Skrzynia biegów"
  - Choices: `Automatyczna|Automatyczna, Manualna|Manualna`
- `fuel` (Select) - Label: "Rodzaj paliwa"
  - Choices: `Benzyna|Benzyna, Diesel|Diesel, Hybryda|Hybryda, Elektryczny|Elektryczny`
- `is_featured` (True/False) - Label: "Wyróżnione"
- `gallery` (Gallery) - Label: "Galeria zdjęć"
- `trim` (Text) - Label: "Wersja/Wyposażenie"
- `brand` (Text) - Label: "Marka"
- `model` (Text) - Label: "Model"
- `color` (Text) - Label: "Kolor"
- `power_hp` (Number) - Label: "Moc (KM)"
- `engine_cc` (Number) - Label: "Pojemność silnika (cm³)"
- `drivetrain` (Select) - Label: "Napęd"
  - Choices: `4x4|4x4, FWD|FWD, RWD|RWD`
- `accident_free` (True/False) - Label: "Bezwypadkowe"
- `service_history` (Textarea) - Label: "Historia serwisowa"
- `origin` (Text) - Label: "Pochodzenie"
- `owners` (Number) - Label: "Liczba właścicieli"
- `vin_masked` (Text) - Label: "VIN (maskowany)"
- `lease_from_pln` (Number) - Label: "Leasing od (PLN/mies)"
- `status` (Select) - Label: "Status"
  - Choices: `available|Dostępny, sold|Sprzedany, reserved|Zarezerwowany`

### Grupa 2: "Ustawienia Strony Głównej" (ACF Options Page)

**Najpierw utwórz Options Page:**
1. W panelu ACF: **Custom Fields → Options Pages → Add New**
2. Page Title: "Ustawienia Strony Głównej"
3. Menu Slug: `homepage-settings`
4. Zapisz

**Następnie utwórz grupę pól:**
- **Lokalizacja:** Options Page is equal to Strona Główna

**Pola:**
- `hero_title` (Text) - Label: "Tytuł Hero"
- `hero_subtitle` (Textarea) - Label: "Podtytuł Hero"
- `hero_background_image` (Image) - Label: "Tło Hero"
- `about_text` (Textarea) - Label: "Tekst 'O nas'"
- `why_us_title` (Text) - Label: "Tytuł sekcji 'Dlaczego my'"
- `why_us_items` (Repeater) - Label: "Elementy 'Dlaczego my'"
  - Sub Fields:
    - `icon` (Text) - Label: "Ikona (SVG class)" - opcjonalne
    - `title` (Text) - Label: "Tytuł"
    - `description` (Textarea) - Label: "Opis"

### Grupa 3: "Ustawienia Ogólne" (ACF Options Page)

**Utwórz Options Page:**
1. Page Title: "Ustawienia Ogólne"
2. Menu Slug: `general-settings`

**Pola:**
- `phone` (Text) - Label: "Telefon"
- `email` (Text) - Label: "Email"
- `address` (Textarea) - Label: "Adres"
- `social_facebook` (URL) - Label: "Facebook"
- `social_instagram` (URL) - Label: "Instagram"
- `social_otomoto` (URL) - Label: "OtoMoto"

## Dodawanie samochodów

### Opcja 1: Import automatyczny (zalecane)

1. Upewnij się, że ACF jest zainstalowane i aktywne
2. Zaloguj się jako administrator
3. Wejdź na: `yoursite.com/wp-content/themes/salon-auto/import-cars.php`
4. Sprawdź wyniki importu
5. **WAŻNE:** Usuń plik `import-cars.php` po imporcie!

### Opcja 2: Ręczne dodawanie

1. W panelu WordPress: **Samochody → Dodaj nowy**
2. Wpisz tytuł (np. "Audi SQ8")
3. Uzupełnij pola ACF (cena, rok, przebieg, etc.)
4. Dodaj zdjęcie główne (Featured Image)
5. Opcjonalnie: dodaj galerię zdjęć w polu "Galeria zdjęć"
6. Ustaw status na "Dostępny"
7. Publikuj

## Edycja treści stron

### Strona główna
- Przejdź do **Strona Główna** (w menu ACF Options)
- Edytuj pola: tytuł hero, podtytuł, teksty sekcji "Dlaczego my"

### Podstrony (O nas, Kontakt, Leasing, etc.)
- Przejdź do **Strony → Wszystkie strony**
- Wybierz stronę do edycji
- Edytuj treść w edytorze Gutenberga
- **UWAGA:** Layout jest zablokowany - możesz edytować tylko teksty, nagłówki, listy

## Struktura plików motywu

```
salon-auto/
├── style.css              # Nagłówek motywu
├── functions.php          # Funkcje motywu, CPT, ACF config
├── header.php            # Header (nawigacja)
├── footer.php            # Footer
├── index.php             # Fallback template
├── front-page.php        # Strona główna
├── page.php              # Szablon dla zwykłych stron
├── single-car.php        # Pojedynczy samochód
├── archive-car.php       # Archiwum samochodów
├── assets/
│   ├── css/
│   │   └── main.css      # Wszystkie style CSS
│   ├── js/
│   │   ├── main.js       # Główny JS
│   │   ├── cookie-consent-api.js
│   │   ├── custom-animations.js
│   │   └── ...           # Pozostałe skrypty
│   └── images/           # Obrazki (logo, hero, etc.)
└── README.md             # Ten plik
```

## Ważne informacje

1. **Layout jest zablokowany** - klient może edytować tylko treści, nie może zmieniać układu
2. **Gutenberg z ograniczeniami** - ✅ **AKTYWNE BLOKADY:**
   - Tylko bezpieczne bloki: Paragraf, Nagłówek, Lista, Obrazek, Cytat, Separator
   - **Wyłączone:** Kolumny, Grupy, Cover, Media & Text, wszystkie bloki layoutowe
   - **Wyłączone:** Block Patterns (pre-made layouts)
   - **Wyłączony:** Block Directory (instalowanie nowych bloków)
   - **Usunięte:** Layout controls (padding, margin, kolory, fonty w blokach)
   - Dla samochodów (CPT "car"): tylko Paragraf, Nagłówek, Lista
3. **ACF jest wymagane** - bez ACF motyw nie będzie działał
4. **Pola ACF są automatycznie rejestrowane** - nie musisz ich tworzyć ręcznie
5. **Zdjęcia** - używaj Media Library WordPressa, alt teksty edytuj w Media Library
6. **Samochody** - każdy samochód to osobny wpis typu "car" z polami ACF

## Wsparcie

W razie problemów sprawdź:
1. Czy ACF jest zainstalowane i aktywne
2. Czy grupy pól ACF są poprawnie skonfigurowane
3. Czy permalinki są odświeżone (Ustawienia → Permalinki → Zapisz)

## Changelog

### v1.0.0
- Pierwsza wersja motywu
- Przeniesienie z statycznego HTML/CSS/JS
- Custom Post Type "car"
- Integracja z ACF
- Szablony dla wszystkich stron

