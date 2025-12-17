<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Cache Control -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <?php
    // === SEO META TAGS ===
    $site_name = get_bloginfo('name') ?: 'Piękne Auta';
    $site_description = get_bloginfo('description') ?: 'Sprawdzone samochody premium od ekspertów. Sprzedaż, leasing, pożyczki, ubezpieczenia.';
    $page_title = wp_get_document_title();
    $current_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
    // Default OG image - use first car image as default (1200x630 recommended for FB)
    $og_image = get_stylesheet_directory_uri() . '/assets/images/audi-rs5-01.jpg';
    
    // === CUSTOM SEO DESCRIPTIONS PER PAGE TYPE ===
    if (is_singular('car')) {
        // Strona samochodu - dynamiczny opis z danymi auta
        $car_id = get_the_ID();
        $brand = get_post_meta($car_id, 'brand', true);
        $model = get_post_meta($car_id, 'model', true);
        $year = get_post_meta($car_id, 'year', true);
        $price = get_post_meta($car_id, 'price', true);
        $site_description = sprintf('%s %s %s - Cena: %s zł. Sprawdzone auto z gwarancją. Leasing od 0%%, kredyt, gotówka. Dostawa: Warszawa, Kraków, Wrocław, Poznań, Gdańsk, cała Polska.', 
            $brand, $model, $year, number_format($price, 0, ',', ' '));
        if (has_post_thumbnail()) {
            $og_image = get_the_post_thumbnail_url($car_id, 'large');
        }
    } elseif (is_post_type_archive('car') || is_page_template('page-samochody.php')) {
        // Katalog samochodów
        $site_description = 'Samochody premium na sprzedaż: Audi, BMW, Mercedes, Porsche. Dostawa: Warszawa, Kraków, Wrocław, Poznań, Gdańsk, Katowice. Leasing od 0%. Sprawdzone auta z gwarancją.';
    } elseif (is_page_template('page-leasing.php')) {
        // Leasing
        $site_description = 'Leasing samochodów premium w całej Polsce: Warszawa, Kraków, Wrocław, Poznań, Gdańsk. Od 0% wpłaty, decyzja w 24h. 28 lat doświadczenia. Bezpłatna kalkulacja!';
    } elseif (is_page_template('page-pozyczki.php')) {
        // Pożyczki
        $site_description = 'Pożyczki na samochody premium - cała Polska. Finansowanie bez BIK. Warszawa, Kraków, Wrocław, Poznań. Szybka decyzja, minimum formalności.';
    } elseif (is_page_template('page-ubezpieczenia.php')) {
        // Ubezpieczenia
        $site_description = 'Ubezpieczenia samochodów premium: OC, AC, Assistance. Obsługujemy całą Polskę: Warszawa, Kraków, Wrocław, Gdańsk, Katowice. Bezpłatna wycena w 5 minut!';
    } elseif (is_page_template('page-kontakt.php')) {
        // Kontakt
        $site_description = 'Kontakt - Piękne Auta: tel. 502 42 82 82, email: biuro@piekneauta.pl. Obsługujemy całą Polskę - Warszawa, Kraków, Wrocław, Poznań, Gdańsk, Katowice, Łódź.';
    } elseif (is_page_template('page-o-nas.php')) {
        // O nas
        $site_description = 'Piękne Auta - 28 lat doświadczenia w sprzedaży samochodów premium. Członek Loży Przedsiębiorców. Rzetelna Firma. Obsługujemy klientów z całej Polski.';
    } elseif (is_front_page()) {
        // Strona główna
        $site_description = 'Samochody premium: Audi, BMW, Mercedes, Porsche. Sprzedaż, leasing od 0%, pożyczki. Dostawa: Warszawa, Kraków, Wrocław, Poznań, Gdańsk, Katowice. Tel: 502 42 82 82';
    }
    
    // For pages with featured image (fallback)
    if (is_page() && has_post_thumbnail() && strpos($og_image, 'og-default') !== false) {
        $og_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
    }
    ?>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo esc_attr($site_description); ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="googlebot" content="index, follow">
    <meta name="author" content="Artur Kurzydłowski - Piękne Auta">
    <?php
    // Keywords meta tag - rozbudowane słowa kluczowe jak na stronie statycznej
    $keywords = 'samochody premium, auta premium używane, sprzedaż aut premium, dealer samochodów premium, samochody używane premium, auta premium polska, premium car dealer, luksusowe samochody używane, sprzedaż aut luksusowych, BMW używane, Audi używane, Mercedes używane, samochody luksusowe, komis aut luksusowych, salon aut używanych premium, auto używane z gwarancją, leasing samochodów, leasing aut premium, ubezpieczenie aut premium, Piękne Auta Szczecinek, samochody premium Szczecinek, auto w leasing, leasing konsumencki';
    if (is_singular('car')) {
        $brand = get_post_meta(get_the_ID(), 'brand', true);
        $model = get_post_meta(get_the_ID(), 'model', true);
        $year = get_post_meta(get_the_ID(), 'year', true);
        $keywords = "$brand $model używane, $brand $model $year używane, $brand $model sprzedaż, $brand $model na sprzedaż, używane $brand $model, $brand używane szczecinek, $keywords";
    }
    ?>
    <meta name="keywords" content="<?php echo esc_attr($keywords); ?>">
    
    <!-- Geo Meta Tags (lokalne SEO - Szczecinek) -->
    <meta name="geo.region" content="PL-ZP">
    <meta name="geo.placename" content="Szczecinek">
    <meta name="geo.position" content="53.70573;16.69825">
    <meta name="ICBM" content="53.70573, 16.69825">
    
    <link rel="canonical" href="<?php echo esc_url($current_url); ?>">
    <link rel="sitemap" type="application/xml" href="<?php echo esc_url(home_url('/sitemap.xml')); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo is_singular('car') ? 'product' : 'website'; ?>">
    <meta property="og:url" content="<?php echo esc_url($current_url); ?>">
    <meta property="og:title" content="<?php echo esc_attr($page_title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($site_description); ?>">
    <meta property="og:image" content="<?php echo esc_url($og_image); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="<?php echo esc_attr($site_name); ?>">
    <meta property="og:locale" content="pl_PL">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($page_title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($site_description); ?>">
    <meta name="twitter:image" content="<?php echo esc_url($og_image); ?>">
    
    <?php
    // === JSON-LD STRUCTURED DATA (dla Google) ===
    $company_name = salon_auto_get_option('company_name', 'Piękne Auta');
    $phone = salon_auto_get_option('phone', '502 42 82 82');
    $email = salon_auto_get_option('email', 'biuro@piekneauta.pl');
    $address = salon_auto_get_option('company_address', 'Polska');
    ?>
    
    <!-- JSON-LD: Organization / LocalBusiness -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "AutoDealer",
      "@id": "<?php echo esc_url(home_url('/')); ?>#organization",
      "name": "<?php echo esc_js($company_name); ?>",
      "alternateName": "Piękne Auta - Artur Kurzydłowski",
      "description": "Sprawdzone samochody premium od ekspertów z 28-letnim doświadczeniem. Członek Loży Przedsiębiorców. Uczestnik Programu Rzetelna Firma. Sprzedaż, leasing, pożyczki, ubezpieczenia samochodowe. Audi, BMW, Mercedes, Porsche. Dostawa do: Warszawa, Kraków, Wrocław, Poznań, Gdańsk, Łódź, Katowice, Lublin, Szczecin.",
      "url": "<?php echo esc_url(home_url('/')); ?>",
      "logo": "<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/logo.svg'); ?>",
      "image": "<?php echo esc_url($og_image); ?>",
      "telephone": "+48502428282",
      "email": "biuro@piekneauta.pl",
      "foundingDate": "1997",
      "founder": {
        "@type": "Person",
        "name": "Artur Kurzydłowski"
      },
      "address": {
        "@type": "PostalAddress",
        "addressCountry": "PL",
        "addressRegion": "Zachodniopomorskie",
        "addressLocality": "Szczecinek",
        "streetAddress": "Hala ekspozycyjna",
        "postalCode": "78-400"
      },
      "taxID": "6731525915",
      "areaServed": [
        {"@type": "City", "name": "Warszawa"},
        {"@type": "City", "name": "Kraków"},
        {"@type": "City", "name": "Wrocław"},
        {"@type": "City", "name": "Poznań"},
        {"@type": "City", "name": "Gdańsk"},
        {"@type": "City", "name": "Gdynia"},
        {"@type": "City", "name": "Sopot"},
        {"@type": "City", "name": "Łódź"},
        {"@type": "City", "name": "Katowice"},
        {"@type": "City", "name": "Lublin"},
        {"@type": "City", "name": "Szczecin"},
        {"@type": "City", "name": "Bydgoszcz"},
        {"@type": "City", "name": "Białystok"},
        {"@type": "City", "name": "Rzeszów"},
        {"@type": "City", "name": "Toruń"},
        {"@type": "City", "name": "Kielce"},
        {"@type": "City", "name": "Olsztyn"},
        {"@type": "City", "name": "Opole"},
        {"@type": "City", "name": "Zielona Góra"},
        {"@type": "City", "name": "Gorzów Wielkopolski"},
        {"@type": "Country", "name": "Polska"}
      ],
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "53.70573",
        "longitude": "16.69825"
      },
      "openingHours": "Mo-Fr 09:00-18:00, Sa 09:00-18:00, Su 09:00-18:00",
      "priceRange": "$$",
      "currenciesAccepted": "PLN, EUR",
      "paymentAccepted": "Gotówka, Przelew, Leasing, Kredyt",
      "openingHoursSpecification": [
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
          "opens": "09:00",
          "closes": "18:00"
        },
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": "Saturday",
          "opens": "10:00",
          "closes": "14:00"
        }
      ],
      "sameAs": [
        "https://www.facebook.com/Apmleasing",
        "https://www.instagram.com/piekne_auta_i_leasing/",
        "https://piekneauta.otomoto.pl",
        "https://www.tiktok.com/@top.cars.mleasing",
        "https://lozaprzedsiebiorcow.pl",
      ],
      "award": [
        "Członek Loży Przedsiębiorców",
        "Uczestnik Programu Rzetelna Firma"
      ],
      "memberOf": [
        {
          "@type": "Organization",
          "name": "Loża Przedsiębiorców",
          "url": "https://lozaprzedsiebiorcow.pl"
        },
        {
          "@type": "Organization", 
          "name": "Program Rzetelna Firma"
        }
      ],
      "hasCredential": [
        {
          "@type": "EducationalOccupationalCredential",
          "credentialCategory": "certificate",
          "name": "Rzetelna Firma"
        }
      ],
      "slogan": "28 lat doświadczenia w sprzedaży samochodów premium",
      "knowsAbout": ["Samochody premium", "Leasing samochodów", "Pożyczki samochodowe", "Ubezpieczenia samochodowe", "Audi", "BMW", "Mercedes-Benz", "Porsche", "Cupra"],
      "makesOffer": [
        {
          "@type": "Offer",
          "itemOffered": {
            "@type": "Service",
            "name": "Sprzedaż samochodów premium"
          }
        },
        {
          "@type": "Offer",
          "itemOffered": {
            "@type": "Service",
            "name": "Leasing samochodów"
          }
        },
        {
          "@type": "Offer",
          "itemOffered": {
            "@type": "Service",
            "name": "Pożyczki na samochody"
          }
        },
        {
          "@type": "Offer",
          "itemOffered": {
            "@type": "Service",
            "name": "Ubezpieczenia samochodowe"
          }
        }
      ],
      "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "Samochody premium na sprzedaż",
        "itemListElement": [
          {"@type": "OfferCatalog", "name": "Audi"},
          {"@type": "OfferCatalog", "name": "BMW"},
          {"@type": "OfferCatalog", "name": "Mercedes-Benz"},
          {"@type": "OfferCatalog", "name": "Porsche"}
        ]
      }
    }
    </script>
    
    <?php if (is_singular('car')) : 
        // JSON-LD dla pojedynczego samochodu
        $car_id = get_the_ID();
        $brand = get_post_meta($car_id, 'brand', true) ?: '';
        $model = get_post_meta($car_id, 'model', true) ?: '';
        $trim = get_post_meta($car_id, 'trim', true) ?: '';
        $year = get_post_meta($car_id, 'year', true) ?: '';
        $price = get_post_meta($car_id, 'price', true) ?: 0;
        $mileage = get_post_meta($car_id, 'mileage', true) ?: '';
        $fuel = get_post_meta($car_id, 'fuel', true) ?: '';
        $color = get_post_meta($car_id, 'color', true) ?: '';
        $vin = get_post_meta($car_id, 'vin', true) ?: '';
        $engine = get_post_meta($car_id, 'engine', true) ?: '';
        $power = get_post_meta($car_id, 'power', true) ?: '';
        $transmission = get_post_meta($car_id, 'transmission', true) ?: '';
        $drive = get_post_meta($car_id, 'drive', true) ?: '';
        $body_type = get_post_meta($car_id, 'body_type', true) ?: '';
        $car_status_seo = get_post_meta($car_id, 'status', true) ?: 'available';
        // Mapowanie statusu na schema.org
        $availability_map = array(
            'available' => 'https://schema.org/InStock',
            'reserved' => 'https://schema.org/LimitedAvailability',
            'sold' => 'https://schema.org/SoldOut'
        );
        $seo_availability = isset($availability_map[$car_status_seo]) ? $availability_map[$car_status_seo] : $availability_map['available'];
        $car_image = has_post_thumbnail() ? get_the_post_thumbnail_url($car_id, 'large') : $og_image;
        $car_description = wp_strip_all_tags(get_post_meta($car_id, 'description', true) ?: get_the_excerpt());
        
        // Wszystkie zdjęcia z galerii
        $gallery_ids = get_post_meta($car_id, 'gallery', true);
        $gallery_images = array($car_image);
        if (is_string($gallery_ids)) $gallery_ids = explode(',', $gallery_ids);
        if (is_array($gallery_ids)) {
            foreach (array_slice($gallery_ids, 0, 10) as $img_id) {
                $img_url = wp_get_attachment_image_url(intval($img_id), 'large');
                if ($img_url) $gallery_images[] = $img_url;
            }
        }
    ?>
    <!-- JSON-LD: Vehicle / Car Product - dla wyszukiwarek typu "Audi RS5 na sprzedaż" -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Car",
      "@id": "<?php echo esc_url(get_permalink()); ?>",
      "name": "<?php echo esc_js($brand . ' ' . $model . ' ' . $year); ?>",
      "description": "<?php echo esc_js($brand . ' ' . $model . ' ' . $trim . ' ' . $year . '. ' . wp_trim_words($car_description, 40)); ?>",
      "brand": {
        "@type": "Brand",
        "name": "<?php echo esc_js($brand); ?>"
      },
      "manufacturer": {
        "@type": "Organization",
        "name": "<?php echo esc_js($brand); ?>"
      },
      "model": "<?php echo esc_js($model); ?>",
      "vehicleModelDate": "<?php echo esc_js($year); ?>",
      "modelDate": "<?php echo esc_js($year); ?>",
      "productionDate": "<?php echo esc_js($year); ?>",
      "color": "<?php echo esc_js($color); ?>",
      "vehicleIdentificationNumber": "<?php echo esc_js($vin); ?>",
      "mileageFromOdometer": {
        "@type": "QuantitativeValue",
        "value": "<?php echo esc_js(preg_replace('/[^0-9]/', '', $mileage)); ?>",
        "unitCode": "KMT",
        "unitText": "km"
      },
      "fuelType": "<?php echo esc_js($fuel); ?>",
      "vehicleConfiguration": "<?php echo esc_js($trim); ?>",
      "vehicleTransmission": "<?php echo esc_js($transmission); ?>",
      "driveWheelConfiguration": "<?php echo esc_js($drive); ?>",
      "bodyType": "<?php echo esc_js($body_type); ?>",
      <?php if ($engine) : ?>"vehicleEngine": {
        "@type": "EngineSpecification",
        "name": "<?php echo esc_js($engine); ?>"
      },<?php endif; ?>
      <?php if ($power) : ?>"vehicleSeatingCapacity": "5",
      "accelerationTime": "<?php echo esc_js($power); ?> KM",<?php endif; ?>
      "numberOfDoors": "4",
      "vehicleInteriorColor": "Ciemny",
      "steeringPosition": "https://schema.org/LeftHandDriving",
      "knownVehicleDamages": "Brak uszkodzeń",
      "image": [
        <?php echo '"' . implode('", "', array_map('esc_url', array_slice($gallery_images, 0, 5))) . '"'; ?>
      ],
      "url": "<?php echo esc_url(get_permalink()); ?>",
      "offers": {
        "@type": "Offer",
        "@id": "<?php echo esc_url(get_permalink()); ?>#offer",
        "price": "<?php echo esc_js($price); ?>",
        "priceCurrency": "PLN",
        "priceValidUntil": "<?php echo date('Y-m-d', strtotime('+30 days')); ?>",
        "availability": "<?php echo esc_js($seo_availability); ?>",
        "itemCondition": "https://schema.org/UsedCondition",
        "url": "<?php echo esc_url(get_permalink()); ?>",
        "seller": {
          "@type": "AutoDealer",
          "@id": "<?php echo esc_url(home_url('/')); ?>#organization",
          "name": "Piękne Auta - Artur Kurzydłowski",
          "telephone": "+48502428282",
          "email": "biuro@piekneauta.pl",
          "url": "<?php echo esc_url(home_url('/')); ?>",
          "memberOf": [
            {"@type": "Organization", "name": "Loża Przedsiębiorców"},
            {"@type": "Organization", "name": "Program Rzetelna Firma"}
          ]
        },
        "shippingDetails": {
          "@type": "OfferShippingDetails",
          "shippingDestination": {
            "@type": "DefinedRegion",
            "addressCountry": "PL"
          },
          "deliveryTime": {
            "@type": "ShippingDeliveryTime",
            "handlingTime": {
              "@type": "QuantitativeValue",
              "minValue": "1",
              "maxValue": "3",
              "unitCode": "DAY"
            }
          }
        }
      }
    }
    </script>
    
    <!-- JSON-LD: Product (dodatkowy format dla Google Shopping) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Product",
      "name": "<?php echo esc_js($brand . ' ' . $model . ' ' . $trim . ' ' . $year); ?>",
      "description": "<?php echo esc_js($brand . ' ' . $model . ' ' . $trim . ' rocznik ' . $year . '. Sprawdzony samochód premium z gwarancją. Leasing, kredyt, gotówka. Członek Loży Przedsiębiorców, Rzetelna Firma.'); ?>",
      "image": "<?php echo esc_url($car_image); ?>",
      "sku": "<?php echo esc_js($vin ?: 'CAR-' . $car_id); ?>",
      "mpn": "<?php echo esc_js($vin); ?>",
      "brand": {
        "@type": "Brand",
        "name": "<?php echo esc_js($brand); ?>"
      },
      "category": "Samochody > <?php echo esc_js($brand); ?> > <?php echo esc_js($model); ?>",
      "offers": {
        "@type": "Offer",
        "price": "<?php echo esc_js($price); ?>",
        "priceCurrency": "PLN",
        "availability": "<?php echo esc_js($seo_availability); ?>",
        "itemCondition": "https://schema.org/UsedCondition",
        "seller": {
          "@type": "AutoDealer",
          "name": "Piękne Auta - Artur Kurzydłowski",
          "telephone": "+48502428282"
        }
      }
    }
    </script>
    <?php endif; ?>
    
    <?php if (is_front_page()) : ?>
    <!-- JSON-LD: WebSite with SearchAction -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "<?php echo esc_js($company_name); ?>",
      "url": "<?php echo esc_url(home_url('/')); ?>",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "<?php echo esc_url(home_url('/')); ?>?s={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>
    
    <!-- JSON-LD: FAQ Schema - najczęściej zadawane pytania (pomaga w wynikach Google) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Czy mogę kupić samochód na leasing?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak, oferujemy leasing dla firm i osób prywatnych. Wpłata od 0%, szybka decyzja w 24h. Współpracujemy z najlepszymi firmami leasingowymi w Polsce."
          }
        },
        {
          "@type": "Question",
          "name": "Czy samochody są sprawdzone?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Każdy samochód w naszej ofercie przechodzi szczegółową weryfikację. Sprawdzamy historię serwisową, przebieg, stan techniczny. Jesteśmy członkiem Loży Przedsiębiorców i uczestnikiem Programu Rzetelna Firma."
          }
        },
        {
          "@type": "Question",
          "name": "Jakie marki samochodów oferujecie?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Specjalizujemy się w samochodach premium: Audi, BMW, Mercedes-Benz, Porsche, Land Rover, Cupra. Wszystkie auta są starannie wyselekcjonowane i w doskonałym stanie."
          }
        },
        {
          "@type": "Question",
          "name": "Czy oferujecie finansowanie zakupu samochodu?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak, oferujemy pełne wsparcie finansowe: leasing, kredyt samochodowy, pożyczki. Pomagamy też w uzyskaniu korzystnego ubezpieczenia OC/AC. 28 lat doświadczenia na rynku."
          }
        },
        {
          "@type": "Question",
          "name": "Jak mogę zarezerwować samochód?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Możesz zarezerwować samochód telefonicznie pod numerem 502 42 82 82, przez formularz kontaktowy na stronie lub osobiście. Rezerwacja jest bezpłatna i niezobowiązująca."
          }
        }
      ]
    }
    </script>
    <?php endif; ?>
    
    <?php if (is_post_type_archive('car') || is_page_template('page-samochody.php')) : 
        // Lista wszystkich samochodów dla Google
        $cars_query = new WP_Query(array(
            'post_type' => 'car',
            'posts_per_page' => 20,
            'post_status' => 'publish'
        ));
        $car_items = array();
        $position = 1;
        if ($cars_query->have_posts()) :
            while ($cars_query->have_posts()) : $cars_query->the_post();
                $c_brand = get_post_meta(get_the_ID(), 'brand', true);
                $c_model = get_post_meta(get_the_ID(), 'model', true);
                $c_year = get_post_meta(get_the_ID(), 'year', true);
                $c_price = get_post_meta(get_the_ID(), 'price', true);
                $car_items[] = array(
                    'position' => $position,
                    'name' => $c_brand . ' ' . $c_model . ' ' . $c_year,
                    'url' => get_permalink(),
                    'image' => get_the_post_thumbnail_url(get_the_ID(), 'large'),
                    'price' => $c_price
                );
                $position++;
            endwhile;
            wp_reset_postdata();
        endif;
    ?>
    <!-- JSON-LD: ItemList - Katalog samochodów (dla wyszukań "samochody premium na sprzedaż") -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ItemList",
      "name": "Samochody premium na sprzedaż - Piękne Auta",
      "description": "Sprawdzone samochody premium: Audi, BMW, Mercedes, Porsche. Członek Loży Przedsiębiorców, Rzetelna Firma. Leasing od 0% wpłaty.",
      "url": "<?php echo esc_url(get_post_type_archive_link('car')); ?>",
      "numberOfItems": <?php echo count($car_items); ?>,
      "itemListElement": [
        <?php 
        $items_json = array();
        foreach ($car_items as $item) {
            $items_json[] = sprintf('{
          "@type": "ListItem",
          "position": %d,
          "item": {
            "@type": "Car",
            "name": "%s",
            "url": "%s",
            "image": "%s",
            "offers": {
              "@type": "Offer",
              "price": "%s",
              "priceCurrency": "PLN",
              "availability": "https://schema.org/OnlineOnly"
            }
          }
        }', $item['position'], esc_js($item['name']), esc_url($item['url']), esc_url($item['image']), esc_js($item['price']));
        }
        echo implode(",\n        ", $items_json);
        ?>
      ]
    }
    </script>
    <?php endif; ?>
    
    <?php if (is_page_template('page-leasing.php')) : ?>
    <!-- JSON-LD: Leasing - FinancialService + FAQ -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FinancialService",
      "name": "Leasing samochodów premium - Piękne Auta",
      "alternateName": "Leasing samochodowy Warszawa Kraków Wrocław",
      "description": "Leasing samochodów używanych i nowych dla firm, osób fizycznych i rolników. Leasing operacyjny i finansowy. Wpłata od 0%, decyzja w 24h. Korzyści podatkowe: raty i odsetki w koszty firmy, odliczenie VAT. Obsługujemy całą Polskę: Warszawa, Kraków, Wrocław, Poznań, Gdańsk, Katowice, Łódź.",
      "url": "<?php echo esc_url(get_permalink()); ?>",
      "telephone": "+48502428282",
      "email": "biuro@piekneauta.pl",
      "provider": {
        "@type": "AutoDealer",
        "name": "Piękne Auta - Artur Kurzydłowski",
        "telephone": "+48502428282",
        "foundingDate": "1997",
        "memberOf": [
          {"@type": "Organization", "name": "Loża Przedsiębiorców"},
          {"@type": "Organization", "name": "Program Rzetelna Firma"}
        ]
      },
      "areaServed": [
        {"@type": "City", "name": "Warszawa"},
        {"@type": "City", "name": "Kraków"},
        {"@type": "City", "name": "Wrocław"},
        {"@type": "City", "name": "Poznań"},
        {"@type": "City", "name": "Gdańsk"},
        {"@type": "City", "name": "Katowice"},
        {"@type": "City", "name": "Łódź"},
        {"@type": "City", "name": "Lublin"},
        {"@type": "City", "name": "Szczecin"},
        {"@type": "Country", "name": "Polska"}
      ],
      "serviceType": ["Leasing operacyjny", "Leasing finansowy", "Leasing konsumencki", "Leasing dla firm", "Leasing dla osób fizycznych", "Leasing dla rolników"],
      "offers": {
        "@type": "Offer",
        "description": "Leasing samochodów premium od 0% wpłaty własnej",
        "eligibleRegion": {"@type": "Country", "name": "Polska"}
      }
    }
    </script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Kto może wziąć leasing samochodu?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Leasing samochodów oferujemy dla: firm (jednoosobowa działalność gospodarcza, spółki), osób fizycznych (leasing konsumencki), rolników. Minimum formalności, szybka decyzja w 24 godziny."
          }
        },
        {
          "@type": "Question",
          "name": "Jaka jest minimalna wpłata własna przy leasingu?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Oferujemy leasing już od 0% wpłaty własnej. Wysokość wpłaty zależy od oceny zdolności leasingowej i wybranego samochodu. Skontaktuj się po indywidualną wycenę."
          }
        },
        {
          "@type": "Question",
          "name": "Czy można wziąć leasing na samochód używany?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak, oferujemy leasing zarówno na samochody nowe jak i używane. Specjalizujemy się w samochodach premium: Audi, BMW, Mercedes, Porsche. Każde auto jest sprawdzone."
          }
        }
      ]
    }
    </script>
    <?php endif; ?>
    
    <?php if (is_page_template('page-pozyczki.php')) : ?>
    <!-- JSON-LD: Pożyczki - FinancialService + FAQ -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FinancialService",
      "name": "Pożyczki dla firm - Piękne Auta",
      "alternateName": "Finansowanie dla firm Warszawa Kraków Wrocław",
      "description": "Pożyczki i finansowanie dla firm. Decyzja w 24 godziny, środki od razu. Minimum 6 miesięcy działalności. Finansowanie na duże zlecenia, rozwój firmy, budowę zespołu, zakup maszyn i floty. Obsługujemy całą Polskę: Warszawa, Kraków, Wrocław, Poznań, Gdańsk, Katowice.",
      "url": "<?php echo esc_url(get_permalink()); ?>",
      "telephone": "+48502428282",
      "email": "biuro@piekneauta.pl",
      "provider": {
        "@type": "AutoDealer",
        "name": "Piękne Auta - Artur Kurzydłowski",
        "telephone": "+48502428282",
        "foundingDate": "1997",
        "memberOf": [
          {"@type": "Organization", "name": "Loża Przedsiębiorców"},
          {"@type": "Organization", "name": "Program Rzetelna Firma"}
        ]
      },
      "areaServed": [
        {"@type": "City", "name": "Warszawa"},
        {"@type": "City", "name": "Kraków"},
        {"@type": "City", "name": "Wrocław"},
        {"@type": "City", "name": "Poznań"},
        {"@type": "City", "name": "Gdańsk"},
        {"@type": "City", "name": "Katowice"},
        {"@type": "Country", "name": "Polska"}
      ],
      "serviceType": ["Pożyczki dla firm", "Finansowanie działalności", "Kredyt obrotowy", "Finansowanie floty"],
      "offers": {
        "@type": "Offer",
        "description": "Pożyczki dla firm - decyzja w 24h, środki od razu",
        "eligibleRegion": {"@type": "Country", "name": "Polska"}
      }
    }
    </script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Kto może otrzymać pożyczkę dla firmy?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Pożyczki oferujemy firmom z minimum 6-miesięczną historią działalności. Finansujemy jednoosobowe działalności gospodarcze oraz spółki. Decyzja w 24 godziny."
          }
        },
        {
          "@type": "Question",
          "name": "Na co można przeznaczyć pożyczkę?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Pożyczka może być przeznaczona na: realizację dużych zleceń, rozwój firmy, zakup maszyn i urządzeń, rozbudowę floty, budowę zespołu, zatowarowanie, inwestycje w nową halę lub biuro."
          }
        },
        {
          "@type": "Question",
          "name": "Jak szybko otrzymam środki?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Decyzja o przyznaniu pożyczki zapada w ciągu 24 godzin. Po pozytywnej decyzji środki są wypłacane od razu na konto firmowe."
          }
        }
      ]
    }
    </script>
    <?php endif; ?>
    
    <?php if (is_page_template('page-ubezpieczenia.php')) : ?>
    <!-- JSON-LD: Ubezpieczenia - InsuranceAgency + FAQ -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "InsuranceAgency",
      "name": "Ubezpieczenia samochodowe premium - Piękne Auta",
      "alternateName": "Ubezpieczenia OC AC Warszawa Kraków Wrocław",
      "description": "Ubezpieczenia samochodowe premium: OC, AC Autocasco, Assistance, NNW. Ochrona do 50 mln zł w UE, Zielona Karta gratis, możliwość rat 0%. Współpracujemy z najlepszymi towarzystwami ubezpieczeniowymi. Obsługujemy całą Polskę: Warszawa, Kraków, Wrocław, Poznań, Gdańsk, Katowice.",
      "url": "<?php echo esc_url(get_permalink()); ?>",
      "telephone": "+48502428282",
      "email": "biuro@piekneauta.pl",
      "provider": {
        "@type": "AutoDealer",
        "name": "Piękne Auta - Artur Kurzydłowski",
        "telephone": "+48502428282",
        "foundingDate": "1997",
        "memberOf": [
          {"@type": "Organization", "name": "Loża Przedsiębiorców"},
          {"@type": "Organization", "name": "Program Rzetelna Firma"}
        ]
      },
      "areaServed": [
        {"@type": "City", "name": "Warszawa"},
        {"@type": "City", "name": "Kraków"},
        {"@type": "City", "name": "Wrocław"},
        {"@type": "City", "name": "Poznań"},
        {"@type": "City", "name": "Gdańsk"},
        {"@type": "City", "name": "Katowice"},
        {"@type": "Country", "name": "Polska"}
      ],
      "serviceType": ["Ubezpieczenie OC", "Ubezpieczenie AC Autocasco", "Assistance", "NNW", "Zielona Karta"],
      "offers": [
        {
          "@type": "Offer",
          "name": "OC - Ubezpieczenie obowiązkowe",
          "description": "Ochrona do 50 mln zł w UE, Zielona Karta gratis, raty 0%"
        },
        {
          "@type": "Offer",
          "name": "AC - Autocasco",
          "description": "Ochrona od kradzieży, kolizji, wypadku i zdarzeń losowych"
        }
      ]
    }
    </script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Jakie ubezpieczenia samochodowe oferujecie?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Oferujemy pełen zakres ubezpieczeń: OC (obowiązkowe), AC Autocasco (od kradzieży i uszkodzeń), Assistance (pomoc drogowa), NNW (następstwa nieszczęśliwych wypadków), Zielona Karta (podróże zagraniczne)."
          }
        },
        {
          "@type": "Question",
          "name": "Czy mogę ubezpieczyć samochód premium?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak, specjalizujemy się w ubezpieczeniach samochodów premium: Audi, BMW, Mercedes, Porsche, Land Rover. Współpracujemy z towarzystwami, które oferują pełną ochronę dla aut z wyższej półki."
          }
        },
        {
          "@type": "Question",
          "name": "Czy można płacić za ubezpieczenie w ratach?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak, oferujemy możliwość rozłożenia płatności na raty 0%. Bezpłatna wycena ubezpieczenia w 5 minut. Zadzwoń: 502 42 82 82."
          }
        }
      ]
    }
    </script>
    <?php endif; ?>
    
    <?php 
    // === BREADCRUMBS JSON-LD ===
    if (!is_front_page()) :
        $breadcrumbs = array();
        $breadcrumbs[] = array('name' => 'Strona główna', 'url' => home_url('/'));
        
        if (is_singular('car')) {
            $breadcrumbs[] = array('name' => 'Samochody', 'url' => get_post_type_archive_link('car'));
            $breadcrumbs[] = array('name' => get_the_title(), 'url' => get_permalink());
        } elseif (is_post_type_archive('car') || is_page_template('page-samochody.php')) {
            $breadcrumbs[] = array('name' => 'Samochody', 'url' => get_permalink());
        } elseif (is_page()) {
            $breadcrumbs[] = array('name' => get_the_title(), 'url' => get_permalink());
        }
    ?>
    <!-- JSON-LD: BreadcrumbList -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [
        <?php 
        $items = array();
        foreach ($breadcrumbs as $i => $crumb) {
            $items[] = sprintf('{
          "@type": "ListItem",
          "position": %d,
          "name": "%s",
          "item": "%s"
        }', $i + 1, esc_js($crumb['name']), esc_url($crumb['url']));
        }
        echo implode(",\n        ", $items);
        ?>
      ]
    }
    </script>
    <?php endif; ?>
    
    <?php
    // Favicon - use custom logo if set, otherwise default
    $logo_header_id = get_option('salon_auto_logo_header_id', 0);
    $favicon_url = $logo_header_id ? wp_get_attachment_image_url($logo_header_id, 'full') : get_stylesheet_directory_uri() . '/assets/images/logo.svg';
    ?>
    <link rel="icon" type="image/svg+xml" href="<?php echo esc_url($favicon_url); ?>">
    <link rel="apple-touch-icon" href="<?php echo esc_url($favicon_url); ?>">
    
    <?php
    // Load Google Fonts ONLY on frontend, NOT in admin panel
    if (!is_admin()) :
    ?>
    <!-- Google Fonts - Only on frontend -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&family=Roboto+Condensed:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Preload Critical CSS -->
    <link rel="preload" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/main.css" as="style">
    <link rel="preload" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/nacja-rebrand.css" as="style">
    
    <?php 
    // Preload LCP image for car pages
    if (is_singular('car')) {
        $car_gallery = function_exists('salon_auto_get_car_gallery') ? salon_auto_get_car_gallery(get_the_ID()) : array();
        if (!empty($car_gallery) && isset($car_gallery[0]['url'])) {
            echo '<link rel="preload" href="' . esc_url($car_gallery[0]['url']) . '" as="image" fetchpriority="high">';
        }
    }
    // Preload hero image on front page
    if (is_front_page()) {
        $hero_images = function_exists('get_field') ? get_field('hero_images', 'option') : null;
        if (!empty($hero_images) && isset($hero_images[0]['url'])) {
            echo '<link rel="preload" href="' . esc_url($hero_images[0]['url']) . '" as="image" fetchpriority="high">';
        }
    }
    ?>
    
    <!-- Additional Styles from New Static Version -->
    <style>
    /* Alpine.js cloak - ukryj elementy do momentu gdy Alpine.js się załaduje */
    [x-cloak] { display: none !important; }
    
    /* Logo - zdecydowanie większe na mobile, dominujące - IDENTYCZNIE JAK STRONA STATYCZNA */
    @media (max-width: 1023px) {
      header img[alt="Piekne auta"] { height: 12rem !important; }
      footer img[alt="Piekne auta"] { height: 10rem !important; }
    }
    @media (max-width: 768px) {
      header img[alt="Piekne auta"] { height: 11rem !important; }
      footer img[alt="Piekne auta"] { height: 9rem !important; }
    }
    @media (max-width: 640px) {
      header img[alt="Piekne auta"] { height: 10rem !important; }
      footer img[alt="Piekne auta"] { height: 8rem !important; }
    }

    /* Responsywność dla tabletów i iPadów */
    @media (min-width: 640px) and (max-width: 1023px) {
      /* Tablety małe i duże */
      .container { padding-left: 1.5rem; padding-right: 1.5rem; }
      h1:not([style*="font-size"]) { font-size: 1.75rem !important; line-height: 1.2; }
      h2 { font-size: 1.5rem !important; }
      header nav a { padding: 0.5rem 1rem !important; font-size: 0.875rem !important; }
      header img[alt*="Piekne auta"], header img[alt*="Piękne Auta"] { height: 180px !important; }
    }

    /* iPady (768px - 1024px) */
    @media (min-width: 768px) and (max-width: 1024px) {
      .container { padding-left: 2rem; padding-right: 2rem; }
      h1:not([style*="font-size"]) { font-size: 1.875rem !important; }
      h2 { font-size: 1.625rem !important; }
      header nav a { padding: 0.625rem 1.25rem !important; }
      header img[alt*="Piekne auta"], header img[alt*="Piękne Auta"] { height: 190px !important; }
    }

    /* iPady Pro (1024px - 1366px) */
    @media (min-width: 1024px) and (max-width: 1366px) {
      .container { padding-left: 2.5rem; padding-right: 2.5rem; }
      h1:not([style*="font-size"]) { font-size: 2rem !important; }
      h2 { font-size: 1.75rem !important; }
    }

    /* Mobile header layout - TYLKO MOBILE, desktop bez zmian - IDENTYCZNIE JAK STRONA STATYCZNA */
    @media (max-width: 1023px) {
      header nav > div:first-of-type {
        position: relative !important;
      }
      header nav > div:first-of-type > a[href*="/"] {
        position: absolute !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        z-index: 10 !important;
      }
      header nav > div:first-of-type > button[aria-label="Toggle menu"] {
        position: absolute !important;
        right: 0 !important;
        z-index: 10 !important;
      }
    }

    /* Dropdown icon rotation - Usługi menu */
    [x-data] button[class*="flex items-center gap-2"] svg,
    button[class*="flex items-center gap-2"] svg,
    div[x-data*="open"] button svg {
      transform-origin: center !important;
      transition: transform 0.2s ease-in-out !important;
      display: inline-block !important;
    }

    svg.rotate-180,
    button[class*="flex items-center gap-2"] svg.rotate-180,
    [x-data] button[class*="flex items-center gap-2"] svg.rotate-180,
    div[x-data*="open"] button svg.rotate-180,
    div[x-data*="open"] button[class*="flex items-center gap-2"] svg.rotate-180 {
      transform: rotate(180deg) !important;
    }

    div[x-data*="open"] button[class*="flex items-center gap-2"] svg.rotate-180 {
      transform: rotate(180deg) !important;
      transform-origin: center !important;
    }

    svg.rotate-180 {
      transform: rotate(180deg) !important;
    }
    
    /* IDENTYCZNE JAK STRONA STATYCZNA - stabilne sticky header */
    header {
      /* Nie używamy will-change ani transform - to powoduje layout shifts */
      /* Header jest sticky przez class="sticky top-0", CSS robi resztę */
    }
    
    /* Zapobieganie skakaniu przy scroll przez sekcje */
    main, section, .container {
      /* Bez transition na transform - to powoduje skakanie */
    }
    </style>
    
    <script>
    // Universal mobile typography - consistent h1 sizing on mobile
    (function() {
      function adjustMobileHeadings() {
        if (window.innerWidth <= 640) {
          const headings = document.querySelectorAll('section h1, main h1, h1.hero-title, h1.hero-title-mobile');
          headings.forEach(title => {
            if (title) {
              const currentSize = parseInt(title.style.fontSize) || parseInt(window.getComputedStyle(title).fontSize);
              if (currentSize > 36) {
                title.style.setProperty('font-size', '36px', 'important');
              }
            }
          });
        }
      }
      
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', adjustMobileHeadings);
      } else {
        adjustMobileHeadings();
      }
      
      window.addEventListener('resize', adjustMobileHeadings);
    })();
    </script>
    
    <?php endif; ?>
    
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<a href="#main-content" class="skip-link screen-reader-text">Przejdź do treści</a>

<?php
// Get all pages once at the top for use in both desktop and mobile menus
// Always show links even if pages don't exist yet
$about_page = get_page_by_path('o-nas');
$leasing_page = get_page_by_path('leasing');
$loans_page = get_page_by_path('pozyczki');
$insurance_page = get_page_by_path('ubezpieczenia');
$contact_page = get_page_by_path('kontakt');

// Create URLs even if pages don't exist
$about_url = $about_page ? get_permalink($about_page->ID) : home_url('/o-nas/');
$leasing_url = $leasing_page ? get_permalink($leasing_page->ID) : home_url('/leasing/');
$loans_url = $loans_page ? get_permalink($loans_page->ID) : home_url('/pozyczki/');
$insurance_url = $insurance_page ? get_permalink($insurance_page->ID) : home_url('/ubezpieczenia/');
$contact_url = $contact_page ? get_permalink($contact_page->ID) : home_url('/kontakt/');
?>

<header class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm backdrop-blur-sm bg-white/95">
    <nav class="container mx-auto px-4" x-data="{ mobileMenuOpen: false, servicesDropdownOpen: false }">
        <div class="flex items-center h-24">
            <!-- Logo -->
            <?php
            $logo_header_id = get_option('salon_auto_logo_header_id', 0);
            $logo_url = $logo_header_id ? wp_get_attachment_image_url($logo_header_id, 'full') : get_stylesheet_directory_uri() . '/assets/images/logo.svg';
            $company_name = get_option('salon_auto_company_name', get_bloginfo('name'));
            ?>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center flex-shrink-0 justify-center lg:justify-start">
                <img src="<?php echo esc_url($logo_url); ?>" alt="Piekne auta" style="height: 206px; width: auto;">
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center justify-center flex-1 space-x-2">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-700">
                    Strona główna
                </a>
                <a href="<?php echo esc_url(get_post_type_archive_link('car')); ?>" class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-700">
                    Samochody
                </a>
                <a href="<?php echo esc_url($about_url); ?>" class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-700">
                    O nas
                </a>
                
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all duration-200 flex items-center gap-2" :class="{ 'bg-gray-50': open }">
                        <span>Usługi</span>
                        <svg class="w-4 h-4 text-gray-600 transition-transform duration-200 flex-shrink-0" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute top-full left-1/2 transform -translate-x-1/2 mt-3 w-56 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50" style="display: none;">
                        <a href="<?php echo esc_url($leasing_url); ?>" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition-all duration-200 group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">Leasing</span>
                        </a>
                        
                        <a href="<?php echo esc_url($loans_url); ?>" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition-all duration-200 group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">Pożyczki</span>
                        </a>
                        
                        <a href="<?php echo esc_url($insurance_url); ?>" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition-all duration-200 group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <span class="font-medium">Ubezpieczenia</span>
                        </a>
                    </div>
                </div>
                
                <a href="<?php echo esc_url($contact_url); ?>" class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-700">
                    Kontakt
                </a>
            </div>

            <!-- Contact Info - Desktop -->
            <div class="hidden lg:flex items-center space-x-4 flex-shrink-0 ml-auto">
                <?php
                $phone = salon_auto_get_option('phone', '502 42 82 82');
                ?>
                <a href="tel:+48<?php echo esc_attr(preg_replace('/[^0-9]/', '', $phone)); ?>" class="flex items-center space-x-2 text-primary transition-all px-5 py-2.5 border-2 border-primary rounded-lg font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span><?php echo esc_html($phone); ?></span>
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2.5 rounded-lg text-primary ml-auto" aria-label="Toggle menu">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                    <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition class="lg:hidden border-t border-gray-200 py-4 bg-white">
            <div class="flex flex-col space-y-2">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="px-4 py-3 rounded-lg text-base font-medium text-gray-700">
                    Strona główna
                </a>
                <a href="<?php echo esc_url(get_post_type_archive_link('car')); ?>" class="px-4 py-3 rounded-lg text-base font-medium text-gray-700">
                    Samochody
                </a>
                <a href="<?php echo esc_url($about_url); ?>" class="px-4 py-3 rounded-lg text-base font-medium text-gray-700">
                    O nas
                </a>
                
                <div x-data="{ open: false }" class="space-y-2">
                    <button @click="open = !open" class="w-full px-4 py-3 rounded-lg text-base font-medium text-gray-700 flex items-center justify-between hover:bg-gray-50 transition-all duration-200 group">
                        <span>Usługi</span>
                        <svg class="w-5 h-5 transition-all duration-300 ease-in-out text-gray-500 group-hover:text-gray-700" :class="{ 'rotate-180 scale-110': open, 'scale-100': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="pl-4 space-y-1">
                        <a href="<?php echo esc_url($leasing_url); ?>" class="block px-4 py-2 rounded-lg text-base font-medium text-gray-600 hover:bg-gray-50">Leasing</a>
                        <a href="<?php echo esc_url($loans_url); ?>" class="block px-4 py-2 rounded-lg text-base font-medium text-gray-600 hover:bg-gray-50">Pożyczki</a>
                        <a href="<?php echo esc_url($insurance_url); ?>" class="block px-4 py-2 rounded-lg text-base font-medium text-gray-600 hover:bg-gray-50">Ubezpieczenia</a>
                    </div>
                </div>
                
                <a href="<?php echo esc_url($contact_url); ?>" class="px-4 py-3 rounded-lg text-base font-medium text-gray-700">
                    Kontakt
                </a>
                
                <a href="tel:+48<?php echo esc_attr(preg_replace('/[^0-9]/', '', $phone)); ?>" class="px-4 py-3 rounded-lg text-base font-semibold text-primary border-2 border-primary flex items-center space-x-2 mt-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span><?php echo esc_html($phone); ?></span>
                </a>
            </div>
        </div>
    </nav>
</header>

<main id="main-content">

