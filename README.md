# Airport Taxi Limburg — PHP versie

Volledige PHP herbouw van het Airport Taxi Limburg boekingsplatform.
Draait op **elke standaard PHP hosting** (PHP 7.4+ / 8.x) met **SQLite** — er is geen aparte database server nodig.

## Functies

- Publieke boekingssite met vaste-prijs berekening per regio en luchthaven
- Volledige admin panel met login (`/admin.php`)
  - Boekingen bekijken/beheren/verwijderen, status wijzigen
  - Regio's beheren (met gemeenten en plaatsen)
  - Luchthavens beheren
  - Prijzenmatrix (regio × luchthaven × personenauto/taxibus)
  - CMS voor content pagina's (rich text HTML)
  - Algemene instellingen (bedrijfsgegevens, prijs-per-km, retourfactor, betaalnotitie, SMTP)
  - **Bewerkbare bevestigingsmail-sjablonen** (klant + bedrijf, HTML, met placeholders)
- E-mailbevestiging (klant + bedrijf) via SMTP (PHPMailer) of PHP `mail()` als fallback
- Volledig Nederlandstalige UI, Euro-formaat "€ 1.234,56"
- Zelfde donker/goud/vliegtuig-thema als het originele project

## Vereisten

- PHP 7.4 of nieuwer, met de extensies: `pdo_sqlite`, `openssl`
- Schrijfrechten in de map `data/` (voor `zlat.sqlite`)
- (Optioneel maar aanbevolen) **Composer** voor PHPMailer.
  Zonder Composer valt de mailer terug op PHP `mail()`.

## Installatie

1. Upload alle bestanden naar de webroot van je hosting (bv. `public_html/`).
2. (Aanbevolen) installeer PHPMailer:
   ```bash
   composer install
   ```
   Zonder Composer werkt de site ook, maar dan wordt `mail()` gebruikt.
3. Open in de browser: `https://jouwdomein.nl/install.php`
   Dit maakt `data/zlat.sqlite` aan en seedt regio's, luchthavens, pagina's, mail-sjablonen en de standaard admin.
4. **Verwijder `install.php`** na een succesvolle installatie (belangrijk!).
5. Log in op de admin: `https://jouwdomein.nl/admin.php`
   - Gebruiker: `admin`
   - Wachtwoord: `admin`
   Wijzig direct het wachtwoord via **Instellingen**.

## SMTP configureren

Ga naar **Admin → Instellingen** en vul de SMTP velden in
(host, poort, gebruiker, wachtwoord, versleuteling, afzendernaam, afzenderadres).
Test daarna met een echte boeking; e-mails worden gelogd in `data/mail.log` bij fouten.

## Bewerken bevestigingsmail

**Admin → E-mailsjablonen**. Er zijn twee sjablonen:

- **Klantbevestiging** — HTML/tekst gestuurd naar de klant
- **Bedrijfsnotificatie** — HTML/tekst gestuurd naar het bedrijf

Beschikbare placeholders (worden automatisch vervangen):

`{reference}` `{customer_name}` `{customer_email}` `{customer_phone}`
`{address}` `{postcode}` `{house_number}` `{region_name}` `{airport_name}`
`{direction}` `{trip_type}` `{passengers}` `{luggage}` `{vehicle}`
`{pickup_date}` `{pickup_time}` `{return_date}` `{return_time}`
`{flight_number}` `{payment_method}` `{notes}` `{price}` `{payment_note}`
`{company_name}` `{company_phone}` `{company_email}`

## Bestandsstructuur

```
/                       front-controller (index.php), .htaccess, admin.php, install.php
/includes/              db, auth, functions, mailer, seed data
/pages/                 publieke views (home, airport, region, content, success)
/api/                   ajax endpoints (quote, book)
/admin_views/           admin views (bookings, regions, airports, prices, pages, settings, email templates)
/assets/                CSS, JS, favicon
/data/                  zlat.sqlite (SQLite DB, wordt automatisch gemaakt)
composer.json           optioneel: PHPMailer
README.md               dit bestand
```

## Veiligheid

- Wachtwoorden worden gehasht met `password_hash` (bcrypt).
- Sessie-gebaseerde admin login met CSRF-tokens.
- Prepared statements overal (PDO).
- Verwijder **altijd** `install.php` na installatie.
- Zet de map `data/` bij voorkeur buiten de webroot; standaard blokkeert een `.htaccess` de directe toegang.
