# Customer Portal Systeem - De Glaswand

## 📋 Overzicht

Het klantportaal systeem maakt het mogelijk voor klanten om:
- Automatisch een account te krijgen bij het aanvragen van een offerte
- Al hun offertes te bekijken op een persoonlijk dashboard
- Offertes goed te keuren of af te wijzen
- De configuratie details van elke offerte te bekijken

Admins kunnen:
- De status van elke offerte volgen
- Zien wanneer een klant een offerte heeft goedgekeurd/afgewezen
- Direct naar het klant account en de klant weergave navigeren

---

## 🚀 Hoe Het Werkt

### Voor Klanten

#### 1. Offerte Aanvragen
1. Klant configureert een glazen wand in de 3D configurator
2. Klant vult contactgegevens in (naam, email, telefoon)
3. Klant verstuurt de offerte aanvraag

#### 2. Account Aanmaak (Automatisch)
- **Automatisch**: Systeem controleert of email al bestaat
- **Nieuwe klant**: Account wordt automatisch aangemaakt
  - Username: naam zonder spaties + timestamp
  - Wachtwoord: automatisch gegenereerd (12 karakters)
  - Role: "customer" (beperkte rechten)
- **Bestaande klant**: Offerte wordt gekoppeld aan bestaand account
- **Welkomst email**: Klant ontvangt inloggegevens + directe link naar offerte

#### 3. Inloggen & Dashboard
- URL: `https://deglaswand.nl/mijn-account/`
- **Dashboard toont**:
  - Alle offertes van de klant
  - Status badges (In behandeling / Goedgekeurd / Afgewezen)
  - Aanvraagdatum
  - Configuratie samenvatting
  - Geschatte prijs
  - "Details" knop per offerte

#### 4. Offerte Bekijken
- URL: `https://deglaswand.nl/mijn-account/offerte/{ID}/`
- **Klant ziet**:
  - Complete configuratie details
  - Alle opties en accessoires
  - Geschatte prijs (ex. BTW en incl. BTW)
  - Eventueel bericht dat ze hebben toegevoegd
  - **Actieknoppen** (als status = "In behandeling"):
    - "Offerte goedkeuren" (groene knop)
    - "Offerte afwijzen" (rode knop)

#### 5. Offerte Goedkeuren/Afwijzen
- **Goedkeuren**:
  - Bevestiging popup verschijnt
  - Status wordt "Goedgekeurd door klant"
  - Admin ontvangt notificatie email
  - Datum van goedkeuring wordt opgeslagen
- **Afwijzen**:
  - Bevestiging popup verschijnt
  - Status wordt "Afgewezen door klant"
  - Admin ontvangt notificatie email
  - Datum van afwijzing wordt opgeslagen

---

### Voor Admins

#### 1. Offerte Overzicht
- Locatie: **WordPress Admin → Glaswand Offertes**
- Toont lijst met alle offertes
- Sorteerbaar en filterbaar

#### 2. Offerte Details
- Klik op een offerte om details te bekijken
- **3 metaboxen** (rechterkant):

  **A. Status & Acties** (bovenaan):
  - Gekleurde status badge
    - 🟡 In behandeling (oranje)
    - 🟢 Goedgekeurd (groen)
    - 🔴 Afgewezen (rood)
  - Datum van goedkeuring/afwijzing (indien van toepassing)
  - Link naar klant account
  - "Klant weergave" knop (opent klant perspectief in nieuw tabblad)

  **B. Klantgegevens**:
  - Naam
  - Email (klikbaar - opent email client)
  - Telefoon (klikbaar - belt direct)

  **C. Configuratie Details** (hoofdgedeelte):
  - Volledige tabel met alle specs
  - Afmetingen
  - Rails, kleuren, glastype
  - Alle opties en accessoires
  - Steellook type (indien van toepassing)
  - Geschatte prijs
  - Klant bericht (indien toegevoegd)

#### 3. Email Notificaties
Admin ontvangt email bij:
- Nieuwe offerte aanvraag
- Klant keurt offerte goed
- Klant wijst offerte af

---

## 🔧 Technische Details

### Database Structuur

**Custom Post Type**: `offerte`

**Post Meta Fields**:
```php
// Configuratie
'_cgc_width'                => int (mm)
'_cgc_height'               => int (mm)
'_cgc_track_count'          => int (2-6)
'_cgc_frame_color'          => string (RAL9005 / RAL7016)
'_cgc_glass_type'           => string (helder / getint)
'_cgc_design'               => string (frameless / steellook)
'_cgc_steellook_type'       => string (amsterdam / barcelona / cairo / dublin)
'_cgc_handle_type'          => string (rond / rechthoekig)
'_cgc_has_u_profiles'       => bool
'_cgc_has_funderingskoker'  => bool
'_cgc_has_hardhout_palen'   => bool
'_cgc_meeneemers_type'      => string
'_cgc_has_tochtstrippen'    => bool
'_cgc_has_montage'          => bool
'_cgc_price_estimate'       => float

// Klantgegevens
'_cgc_customer_name'        => string
'_cgc_customer_email'       => string
'_cgc_customer_phone'       => string
'_cgc_customer_message'     => text
'_cgc_customer_user_id'     => int (WordPress User ID)

// Status tracking
'_cgc_quote_status'         => string (pending / approved / rejected)
'_cgc_quote_approved_date'  => datetime
'_cgc_quote_rejected_date'  => datetime
```

### User Role

**Role**: `customer`

**Capabilities**:
```php
'read' => true               // Can read content
'edit_posts' => false        // Cannot edit posts
'delete_posts' => false      // Cannot delete posts
```

### URL Routes

**Rewrite Rules**:
```php
'/mijn-account/'                    => Dashboard pagina
'/mijn-account/offerte/{ID}/'       => Offerte detail pagina
```

### AJAX Endpoints

```php
// Offerte goedkeuren
action: 'cgc_approve_quote'
nonce:  'cgc_approve_quote'
params: quote_id (int)

// Offerte afwijzen
action: 'cgc_reject_quote'
nonce:  'cgc_reject_quote'
params: quote_id (int)
```

---

## 📧 Email Templates

### 1. Welkomst Email (Bij Account Aanmaak)

**Onderwerp**: Welkom bij De Glaswand - Uw account is aangemaakt

**Inhoud**:
- Begroeting met naam
- Inloggegevens (email + wachtwoord)
- Link naar inlogpagina
- Directe link naar offerte
- Tip om wachtwoord te wijzigen

### 2. Admin Notificatie (Bij Goedkeuring)

**Onderwerp**: Offerte #{ID} goedgekeurd door klant

**Inhoud**:
- Klant heeft offerte goedgekeurd
- Link naar offerte in admin

### 3. Admin Notificatie (Bij Afwijzing)

**Onderwerp**: Offerte #{ID} afgewezen door klant

**Inhoud**:
- Klant heeft offerte afgewezen
- Link naar offerte in admin

---

## 🎨 Styling

Het customer portal gebruikt inline CSS voor maximale compatibiliteit.

**Kleurenschema**:
- Primary: `#1f3d58` (donkerblauw)
- Accent: `#eb512f` (oranje)
- Success: `#10b981` (groen)
- Warning: `#f59e0b` (oranje/geel)
- Danger: `#ef4444` (rood)

**Typografie**:
- Font: Questrial (zelfde als website)
- Heading sizes: 18px - 42px
- Body: 14px - 16px

---

## 🔒 Beveiliging

### Nonce Verificatie
Alle AJAX calls gebruiken WordPress nonces voor CSRF protectie.

### User Verificatie
- Klanten kunnen alleen hun eigen offertes bekijken
- Offerte ID wordt geverifieerd tegen `_cgc_customer_user_id`
- Admin bypass via `current_user_can('manage_options')`

### Password Generatie
- 12 karakters minimum
- Hoofdletters, kleine letters, cijfers en speciale tekens
- Via `wp_generate_password(12, true, true)`

---

## 🚦 Status Flow

```
[Offerte aanvraag]
        ↓
    PENDING (In behandeling)
        ↓
   ┌────┴────┐
   ↓         ↓
APPROVED  REJECTED
```

**Status transities**:
- `pending` → `approved` (via klant goedkeuring)
- `pending` → `rejected` (via klant afwijzing)
- Geen terugkeer mogelijk (finaal)

---

## 📝 Gebruik Instructies

### Voor Website Beheerder

1. **Navigatie toevoegen**:
   - Ga naar **Weergave → Menu's**
   - Voeg custom link toe: `/mijn-account/`
   - Label: "Mijn Account"
   - Plaats in hoofdnavigatie

2. **Offertes bekijken**:
   - Ga naar **Glaswand Offertes** in admin menu
   - Klik op offerte voor details
   - Check status in sidebar
   - Klik "Klant weergave" om te zien wat klant ziet

3. **Klant accounts beheren**:
   - Ga naar **Gebruikers**
   - Filter op role "Klant"
   - Bekijk/bewerk klant gegevens

### Voor Klanten

1. **Eerste keer inloggen**:
   - Check welkomst email voor inloggegevens
   - Ga naar link in email
   - Log in met email + wachtwoord
   - **Optioneel**: Wijzig wachtwoord in profiel

2. **Dashboard gebruiken**:
   - Overzicht van alle offertes
   - Klik "Details" om specifieke offerte te bekijken
   - Klik "Nieuwe offerte aanvragen" voor nieuwe configuratie

3. **Offerte goedkeuren**:
   - Open offerte detail
   - Controleer configuratie en prijs
   - Klik "Offerte goedkeuren"
   - Bevestig in popup
   - Wacht op contact van De Glaswand

---

## 🐛 Troubleshooting

### Klant kan niet inloggen
- **Check**: Welkomst email in spam folder?
- **Fix**: Wachtwoord reset via WordPress login pagina

### Offerte niet zichtbaar in dashboard
- **Check**: `_cgc_customer_user_id` meta field correct ingesteld?
- **Fix**: Handmatig User ID toevoegen in admin

### "Mijn Account" pagina geeft 404
- **Fix**: Flush rewrite rules:
  ```bash
  wp rewrite flush --path="/pad/naar/wp"
  ```

### AJAX calls falen
- **Check**: JavaScript console voor errors
- **Check**: Nonce verificatie
- **Fix**: Plugin deactiveren + reactiveren

---

## ✨ Toekomstige Uitbreidingen

Mogelijke features voor later:
- [ ] PDF offerte generatie
- [ ] Status "In productie"
- [ ] Order tracking
- [ ] Facturatie systeem
- [ ] Klant reviews/feedback
- [ ] Opgeslagen configuraties (concept)
- [ ] Multiple addresses per klant
- [ ] Notificatie voorkeuren

---

## 📞 Support

Voor vragen of problemen:
- **Email**: info@deglaswand.nl
- **Tel**: 06 15 24 63 83
- **Developer**: Clickwave (Roy)

---

**Versie**: 1.0.0
**Laatst bijgewerkt**: December 2025
**WordPress Versie**: 5.8+
**PHP Versie**: 7.4+
