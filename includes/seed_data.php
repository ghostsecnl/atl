<?php
declare(strict_types=1);

// Regions (id, name, representative_place, gemeenten, plaatsen)
function seed_regions_data(): array {
    return [
        ['sittard-born',        'Regio Sittard – Born',        'Sittard, Netherlands',    ['Sittard-Geleen'],                ['Sittard','Born','Limbricht','Guttecoven','Einighausen','Munstergeleen']],
        ['geleen-beek',         'Regio Geleen – Beek',         'Geleen, Netherlands',     ['Beek'],                          ['Geleen','Beek','Spaubeek','Neerbeek','Elsloo','Stein']],
        ['landgraaf-kerkrade',  'Regio Landgraaf – Kerkrade',  'Kerkrade, Netherlands',   ['Landgraaf','Kerkrade'],          ['Landgraaf','Kerkrade','Ubach over Worms','Eygelshoven']],
        ['heerlen-hoensbroek',  'Regio Heerlen – Hoensbroek',  'Heerlen, Netherlands',    ['Heerlen'],                       ['Heerlen','Hoensbroek','Heerlerheide']],
        ['simpelveld-voerendaal','Regio Simpelveld – Voerendaal','Voerendaal, Netherlands',['Simpelveld','Voerendaal'],      ['Simpelveld','Voerendaal','Bocholtz','Klimmen']],
        ['schinveld-brunssum',  'Regio Schinveld – Brunssum',  'Brunssum, Netherlands',   ['Brunssum','Beekdaelen'],         ['Brunssum','Schinveld','Jabeek','Merkelbeek']],
        ['eijsden-margraten',   'Regio Eijsden – Margraten',   'Margraten, Netherlands',  ['Eijsden-Margraten'],             ['Eijsden','Margraten','Cadier en Keer','Gronsveld']],
        ['maastricht',          'Regio Maastricht',            'Maastricht, Netherlands', ['Maastricht'],                    ['Maastricht','Amby','Heer','Wolder']],
        ['vaals-gulpen',        'Regio Vaals – Gulpen',        'Gulpen, Netherlands',     ['Vaals','Gulpen-Wittem'],         ['Vaals','Gulpen','Wittem','Wijlre','Mechelen']],
        ['valkenburg',          'Regio Valkenburg',            'Valkenburg, Netherlands', ['Valkenburg aan de Geul'],        ['Valkenburg','Berg en Terblijt','Houthem','Schin op Geul']],
        ['echt-susteren',       'Regio Echt – Susteren',       'Echt, Netherlands',       ['Echt-Susteren'],                 ['Echt','Susteren','Sint Joost','Pey','Koningsbosch']],
        ['maasgouw',            'Regio Maasgouw',              'Maasbracht, Netherlands', ['Maasgouw'],                      ['Maasbracht','Thorn','Wessem','Heel','Linne']],
        ['roermond',            'Regio Roermond',              'Roermond, Netherlands',   ['Roermond'],                      ['Roermond','Herten','Swalmen','Maasniel']],
        ['roerdalen',           'Regio Roerdalen',             'Sint Odiliënberg, Netherlands',['Roerdalen'],                ['Sint Odiliënberg','Herkenbosch','Melick','Vlodrop','Montfort']],
       
    ];
}

function seed_airports_data(): array {
    return [
        ['dus','Vliegveld Düsseldorf',              'Flughafen Düsseldorf, Germany'],
        ['cgn','Vliegveld Köln Bonn',               'Köln Bonn Airport, Germany'],
        ['bru','Vliegveld Brussel',                 'Brussels Airport, Zaventem, Belgium'],
        ['ein','Vliegveld Eindhoven',               'Eindhoven Airport, Netherlands'],
        ['nrn','Vliegveld Weeze',                   'Airport Weeze, Germany'],
        ['ams','Vliegveld Schiphol',                'Amsterdam Airport Schiphol, Netherlands'],
        ['crl','Vliegveld Charleroi',               'Brussels South Charleroi Airport, Belgium'],
        ['maa','Vliegveld Maastricht Aachen Airport','Maastricht Aachen Airport, Netherlands'],
        ['lgg','Vliegveld Luik',                    'Liège Airport, Belgium'],
        ['fra','Vliegveld Frankfurt',               'Frankfurt Airport, Germany'],
    ];
}

// Default per-region default prices (car, van) — indicatief; admin overschrijft in de admin.
function seed_default_prices(): array {
    // arbitrary reasonable defaults, so quotes are non-zero out of the box.
    // Keys: region_id => [airport_id => [car, van]]
    $regions = ['sittard-born','geleen-beek','landgraaf-kerkrade','heerlen-hoensbroek','simpelveld-voerendaal','schinveld-brunssum','eijsden-margraten','maastricht','vaals-gulpen','valkenburg','echt-susteren','maasgouw','roermond','roerdalen','weert'];
    // approximate km from region to airport
    $km = [
        'dus'=>[95,90,80,85,82,88,110,105,95,95,90,95,85,80,90],
        'cgn'=>[130,125,115,120,115,120,135,130,120,120,120,115,110,110,120],
        'bru'=>[170,175,180,180,180,180,150,145,180,165,180,180,180,180,180],
        'ein'=>[75,70,90,85,85,90,80,80,95,80,65,70,70,70,45],
        'nrn'=>[95,95,90,95,90,95,120,115,95,100,90,90,80,75,90],
        'ams'=>[210,215,215,215,215,220,220,215,220,215,210,215,205,205,180],
        'crl'=>[210,215,220,215,215,220,180,175,220,205,220,220,220,220,220],
        'maa'=>[30,30,25,25,25,25,20,10,25,20,40,55,60,60,80],
        'lgg'=>[70,70,60,60,60,65,50,45,65,55,80,90,95,95,115],
        'fra'=>[280,275,260,265,265,270,290,285,260,265,275,275,275,275,285],
    ];
    $per_km_car = 1.95; $per_km_van = 2.75; $fee = 10; $step = 5;
    $round = function($v) use($step) { return (int)(round($v/$step)*$step); };
    $out = [];
    foreach ($regions as $i => $rid) {
        $out[$rid] = [];
        foreach ($km as $aid => $arr) {
            $d = $arr[$i] ?? 100;
            $out[$rid][$aid] = [
                'car' => $round($d*$per_km_car + $fee),
                'van' => $round($d*$per_km_van + $fee),
                'distance_km' => $d,
            ];
        }
    }
    return $out;
}

function seed_faq(): array {
    return [
        ['Kan ik ook per telefoon een taxi boeken?','Ja, telefonische boekingen zijn mogelijk op 06 22 33 45 66.'],
        ['Hoe boek ik een airport taxi?','In drie stappen: ritgegevens (ophaaladres, luchthaven, aantal personen), vertrekgegevens en betaalmethode kiezen en bevestigen. Direct na het boeken ontvangt u een bevestiging.'],
        ['Zijn de prijzen inclusief of exclusief btw?','Alle prijzen op de website zijn inclusief btw.'],
        ['Rijden jullie ook op feestdagen?','Ja, onze taxi\'s rijden 24/7 het hele jaar door.'],
        ['Hoe kort voor vertrek kan ik online boeken?','Online boeken kan tot 48 uur voor vertrek. Korter? Bel ons even.'],
        ['Moeten wij de taxi delen?','Nee, u wordt altijd rechtstreeks naar uw bestemming gebracht.'],
        ['Kan de taxi op meerdere adressen ophalen?','Ja, met een kleine toeslag voor een tweede ophaaladres.'],
        ['Spreekt de chauffeur Nederlands?','Ja, alle chauffeurs zijn in Nederland opgeleid en spreken Nederlands plus Engels of Duits.'],
        ['Kan een taxi ons ophalen op het vliegveld?','Ja, u treft de chauffeur bij het Meeting Point van de luchthaven.'],
        ['Kan ik kinderen meenemen?','Kinderen zijn van harte welkom. Eigen kinderstoeltjes kunt u vermelden bij de boeking.'],
        ['Kan ik een rolstoel vervoeren?','Opvouwbare rolstoelen nemen wij mee, geef dit aan bij de boeking.'],
        ['Mijn vlucht is vertraagd of eerder, wat nu?','Met uw vluchtnummer monitoren wij de vlucht en passen wij de ophaaltijd aan.'],
        ['Waar geef ik mijn vluchtnummer op?','Tijdens het boeken bij Reisgegevens.'],
        ['Hebben jullie taxibusjes?','Ja, tot maximaal 8 personen in een keer.'],
        ['Wanneer ontvang ik mijn bevestiging?','Direct na de boeking per e-mail. Controleer ook uw spamfolder.'],
        ['Hoe pas ik een boeking aan?','Neem contact met ons op voor wijzigingen.'],
        ['Ik ben te laat bij het ophaalpunt, wacht de chauffeur?','Onze chauffeurs wachten tot minimaal 20 minuten na het afgesproken tijdstip.'],
    ];
}

function seed_pages_data(): array {
    $faq_html = '<div class="faq">';
    foreach (seed_faq() as [$q,$a]) {
        $faq_html .= '<details class="faq__item"><summary>' . htmlspecialchars($q) . '</summary><div>' . htmlspecialchars($a) . '</div></details>';
    }
    $faq_html .= '</div>';

    return [
        [
            'slug'=>'airport-taxis','title'=>'Airport taxi\'s','in_nav'=>1,'sort_order'=>1,
            'meta_title'=>'Airport taxi\'s | Van en naar alle luchthavens',
            'meta_description'=>'Airport taxi naar Schiphol, Eindhoven, Dusseldorf, Brussel, Weeze en meer. Vaste tarieven en 24/7 service vanuit heel Zuid- en Midden-Limburg.',
            'h1'=>'Airport taxi\'s van en naar elke luchthaven',
            'body'=>'<p>Wij verzorgen betrouwbaar luchthavenvervoer van en naar alle grote luchthavens in de regio en daarbuiten. Kies uw luchthaven en boek eenvoudig online tegen een vast tarief.</p><p>Bekijk de tarieven per luchthaven via het menu op de homepage of ga direct naar het boekingsformulier.</p>',
        ],
        [
            'slug'=>'onze-service','title'=>'Onze service','in_nav'=>1,'sort_order'=>2,
            'meta_title'=>'Onze service | Comfortabel en betrouwbaar luchthavenvervoer',
            'meta_description'=>'Deur-tot-deur service met vluchtmonitoring, luxe voertuigen met airco en vaste transparante prijzen.',
            'h1'=>'Onze service',
            'body'=>'<p>Comfortabel, betrouwbaar en op tijd. Wij brengen u veilig naar de luchthaven. Deur-tot-deur service met vluchtmonitoring bij aankomst en 24/7 beschikbaarheid.</p><p>Onze luxe voertuigen zijn voorzien van airco en optioneel wifi. U rijdt tegen vaste, transparante prijzen zonder verborgen kosten en met hulp bij uw bagage. Boeken kan online, telefonisch of per e-mail.</p>',
        ],
        [
            'slug'=>'zakelijk-vervoer','title'=>'Zakelijk vervoer','in_nav'=>1,'sort_order'=>3,
            'meta_title'=>'Zakelijk luchthavenvervoer | Airport Taxi Limburg',
            'meta_description'=>'Zakelijk airport vervoer met vaste tarieven, facturatie en betrouwbare chauffeurs.',
            'h1'=>'Zakelijk vervoer',
            'body'=>'<p>Voor zakelijke reizigers bieden wij betrouwbaar en representatief luchthavenvervoer. Vaste tarieven, facturatie op rekening en professionele Nederlandstalige chauffeurs. Wij zorgen dat u en uw gasten altijd op tijd op de luchthaven aankomen.</p>',
        ],
        [
            'slug'=>'veelgestelde-vragen','title'=>'Veelgestelde vragen','in_nav'=>1,'sort_order'=>4,
            'meta_title'=>'Veelgestelde vragen | Airport Taxi Limburg',
            'meta_description'=>'Antwoorden op veelgestelde vragen over boeken, tarieven, vluchtmonitoring, bagage en meer.',
            'h1'=>'Veelgestelde vragen',
            'body'=>$faq_html,
        ],
        [
            'slug'=>'contact','title'=>'Contact','in_nav'=>1,'sort_order'=>5,
            'meta_title'=>'Contact | Airport Taxi Limburg',
            'meta_description'=>'Neem contact op met Airport Taxi Limburg. Bel 06 22 33 45 66 of mail naar info@airport-taxi-limburg.nl. 24/7 bereikbaar.',
            'h1'=>'Contact',
            'body'=>'<p>Heeft u een vraag of wilt u telefonisch boeken? Wij zijn 24/7 bereikbaar.</p><ul><li>Telefoon: <a href="tel:+31622334566">06 22 33 45 66</a></li><li>E-mail: <a href="mailto:info@airport-taxi-limburg.nl">info@airport-taxi-limburg.nl</a></li><li>Regio: Zuid- en Midden-Limburg, Nederland</li></ul>',
        ],
        [
            'slug'=>'privacyverklaring','title'=>'Privacyverklaring','in_nav'=>0,'sort_order'=>6,
            'meta_title'=>'Privacyverklaring | Airport Taxi Limburg',
            'meta_description'=>'Lees hoe Airport Taxi Limburg omgaat met uw persoonsgegevens.',
            'h1'=>'Privacyverklaring',
            'body'=>'<p>Airport Taxi Limburg respecteert uw privacy. Wij verwerken uw persoonsgegevens uitsluitend om uw boeking uit te voeren en met u te communiceren. Uw gegevens worden niet aan derden verkocht.</p><p>U kunt op elk moment inzage, correctie of verwijdering van uw gegevens aanvragen via info@airport-taxi-limburg.nl.</p>',
        ],
        [
            'slug'=>'cookies','title'=>'Cookies','in_nav'=>0,'sort_order'=>7,
            'meta_title'=>'Cookiebeleid | Airport Taxi Limburg',
            'meta_description'=>'Informatie over het gebruik van cookies op deze website.',
            'h1'=>'Cookiebeleid',
            'body'=>'<p>Deze website gebruikt functionele cookies om de site goed te laten werken. Door de site te gebruiken gaat u akkoord met ons cookiebeleid.</p>',
        ],
    ];
}

function default_email_templates(): array {
    return [
        'customer_subject' => 'Boekingsbevestiging {reference} - {company_name}',
        'customer_html' => '<h1 style="margin:0 0 6px;font-size:22px;color:#0f172a;font-family:Arial,sans-serif;">Bedankt voor uw boeking</h1>
<p style="margin:0 0 16px;color:#556274;font-family:Arial,sans-serif;font-size:15px;">Beste {customer_name}, uw taxirit is ontvangen en bevestigd. Hieronder vindt u alle gegevens. Bewaar deze bevestiging goed.</p>
<p style="background:#f6f8fc;border:1px solid #e6eaf2;border-radius:10px;padding:10px 16px;font-family:Arial,sans-serif;font-size:13px;color:#556274;display:inline-block;">Referentie&nbsp;<strong style="color:#0f172a;font-size:15px;letter-spacing:.5px;">{reference}</strong></p>
<h2 style="margin:16px 0 4px;font-size:15px;color:#0f172a;font-family:Arial,sans-serif;">Uw rit</h2>
<table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;font-family:Arial,sans-serif;">
<tr><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#556274;font-size:13px;width:42%;">Ophaaladres</td><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#0f172a;font-size:14px;font-weight:600;">{address}</td></tr>
<tr><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#556274;font-size:13px;">Luchthaven</td><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#0f172a;font-size:14px;font-weight:600;">{airport_name}</td></tr>
<tr><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#556274;font-size:13px;">Regio</td><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#0f172a;font-size:14px;font-weight:600;">{region_name}</td></tr>
<tr><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#556274;font-size:13px;">Richting</td><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#0f172a;font-size:14px;font-weight:600;">{direction}</td></tr>
<tr><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#556274;font-size:13px;">Type rit</td><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#0f172a;font-size:14px;font-weight:600;">{trip_type}</td></tr>
<tr><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#556274;font-size:13px;">Ophalen</td><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#0f172a;font-size:14px;font-weight:600;">{pickup_date} {pickup_time}</td></tr>
<tr><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#556274;font-size:13px;">Personen / bagage</td><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#0f172a;font-size:14px;font-weight:600;">{passengers} / {luggage}</td></tr>
<tr><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#556274;font-size:13px;">Voertuig</td><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#0f172a;font-size:14px;font-weight:600;">{vehicle}</td></tr>
<tr><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#556274;font-size:13px;">Vluchtnummer</td><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#0f172a;font-size:14px;font-weight:600;">{flight_number}</td></tr>
<tr><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#556274;font-size:13px;">Betaling</td><td style="padding:8px 0;border-bottom:1px solid #e6eaf2;color:#0f172a;font-size:14px;font-weight:600;">{payment_method}</td></tr>
</table>
<p style="margin:18px 0 4px;background:#16264a;color:#fff;padding:16px 22px;border-radius:14px;font-family:Arial,sans-serif;">Totaalprijs (incl. btw): <strong style="font-size:22px;">{price}</strong></p>
<p style="background:#f6f8fc;border-radius:12px;padding:14px 18px;color:#0f172a;font-family:Arial,sans-serif;font-size:14px;">💳 {payment_note}</p>
<p style="margin:22px 0 0;color:#556274;font-family:Arial,sans-serif;font-size:14px;">Vragen? Bel {company_phone} of mail {company_email}.<br><strong>{company_name}</strong></p>',
        'customer_text' => "Bedankt voor uw boeking\n\nBeste {customer_name}, uw taxirit is bevestigd.\n\nReferentie: {reference}\n\nOphaaladres: {address}\nLuchthaven: {airport_name}\nRegio: {region_name}\nRichting: {direction}\nType rit: {trip_type}\nOphalen: {pickup_date} {pickup_time}\nPersonen / bagage: {passengers} / {luggage}\nVoertuig: {vehicle}\nVluchtnummer: {flight_number}\nBetaling: {payment_method}\n\nTotaalprijs (incl. btw): {price}\n\n{payment_note}\n\nVragen? Bel {company_phone}.\n{company_name}",
        'company_subject' => 'Nieuwe boeking {reference} - {region_name}',
        'company_html' => '<h1 style="font-family:Arial,sans-serif;">Nieuwe boeking</h1>
<p style="font-family:Arial,sans-serif;">Er is een nieuwe rit geboekt via de website.</p>
<p style="font-family:Arial,sans-serif;"><strong>Referentie:</strong> {reference}</p>
<h2 style="font-family:Arial,sans-serif;">Rit</h2>
<table cellpadding="6" cellspacing="0" style="border-collapse:collapse;font-family:Arial,sans-serif;">
<tr><td>Ophaaladres</td><td><strong>{address}</strong></td></tr>
<tr><td>Postcode / huisnr</td><td><strong>{postcode} {house_number}</strong></td></tr>
<tr><td>Luchthaven</td><td><strong>{airport_name}</strong></td></tr>
<tr><td>Regio</td><td><strong>{region_name}</strong></td></tr>
<tr><td>Richting</td><td><strong>{direction}</strong></td></tr>
<tr><td>Type rit</td><td><strong>{trip_type}</strong></td></tr>
<tr><td>Ophalen</td><td><strong>{pickup_date} {pickup_time}</strong></td></tr>
<tr><td>Retour</td><td><strong>{return_date} {return_time}</strong></td></tr>
<tr><td>Personen / bagage</td><td><strong>{passengers} / {luggage}</strong></td></tr>
<tr><td>Voertuig</td><td><strong>{vehicle}</strong></td></tr>
<tr><td>Vluchtnummer</td><td><strong>{flight_number}</strong></td></tr>
<tr><td>Betaling</td><td><strong>{payment_method}</strong></td></tr>
<tr><td>Prijs</td><td><strong>{price}</strong></td></tr>
<tr><td>Opmerkingen</td><td>{notes}</td></tr>
</table>
<h2 style="font-family:Arial,sans-serif;">Klant</h2>
<p style="font-family:Arial,sans-serif;">
Naam: <strong>{customer_name}</strong><br>
E-mail: <a href="mailto:{customer_email}">{customer_email}</a><br>
Telefoon: <a href="tel:{customer_phone}">{customer_phone}</a>
</p>',
        'company_text' => "Nieuwe boeking {reference}\n\nOphaaladres: {address}\nLuchthaven: {airport_name}\nRegio: {region_name}\nRichting: {direction}\nType rit: {trip_type}\nOphalen: {pickup_date} {pickup_time}\nRetour: {return_date} {return_time}\nPersonen / bagage: {passengers} / {luggage}\nVoertuig: {vehicle}\nVluchtnummer: {flight_number}\nBetaling: {payment_method}\nPrijs: {price}\nOpmerkingen: {notes}\n\nKlant:\n  Naam: {customer_name}\n  E-mail: {customer_email}\n  Telefoon: {customer_phone}",
    ];
}
