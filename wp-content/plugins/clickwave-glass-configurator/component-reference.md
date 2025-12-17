# Dé Glaswand - Component Reference Guide

This document provides a visual breakdown of all UI components extracted from deglaswand.nl for reconstruction in WordPress FSE.

---

## Color Palette Reference

### Primary Colors
```
Dark Blue (Headings)
#1F3D58
RGB: 31, 61, 88
███████████

Accent Orange (CTAs)
#EB512F
RGB: 235, 81, 47
███████████
```

### Neutral Colors
```
White                  Light Grey             Lightest Grey
#FFFFFF                #F2F2F2                #F8F8F8
███████████            ███████████            ███████████

Dark Grey              Footer Grey            Footer Text
#585A61                #FAFAFA                #464646
███████████            ███████████            ███████████
```

---

## Typography Scale

### Headings
```
Hero (H1)
Font: Questrial, 48px, Bold, #1F3D58
Line-height: 1.2
Example: "Glazen schuifwanden"

Subtitle (H2)
Font: Questrial, 24px, Regular, #1F3D58
Line-height: 1.4
Example: "Onze Voordelen"

Feature Title (H3)
Font: Questrial, 20px, Bold, #1F3D58
Line-height: 1.3
Example: "Gratis inmeten"
```

### Body Text
```
Default Body
Font: Questrial, 18px, Regular, #1F3D58
Line-height: 1.6
Example: "Wij meten uw glazen schuifwand gratis in..."

Small Text
Font: Questrial, 14px, Regular, #464646
Line-height: 1.5
Example: Footer links, meta information
```

---

## Button Components

### Primary Button
```
┌─────────────────────────┐
│  Bereken mijn prijs     │  Background: #EB512F
└─────────────────────────┘  Text: #FFFFFF, 20px
                              Padding: 12px 24px
                              Border-radius: 4px

Hover State:
┌─────────────────────────┐
│  Bereken mijn prijs  ↑  │  Background: #D44527
└─────────────────────────┘  Transform: translateY(-2px)
                              Shadow: 0 4px 12px rgba(235,81,47,0.3)
```

### Secondary Button (Outline)
```
┌─────────────────────────┐
│      Koop nu            │  Background: transparent
└─────────────────────────┘  Border: 2px solid #FFFFFF
                              Text: #FFFFFF, 16px
                              Padding: 12px 24px

Hover State:
┌─────────────────────────┐
│      Koop nu            │  Background: rgba(255,255,255,0.1)
└─────────────────────────┘
```

---

## Card Components

### Feature Card
```
┌─────────────────────────────┐
│                             │
│          [ICON]             │  Icon: 48x48px, #EB512F
│                             │
│      Gratis inmeten         │  Title: 20px, Bold, #1F3D58
│                             │
│  Wij meten uw glazen        │  Description: 16px, Regular
│  schuifwand gratis in...    │
│                             │
└─────────────────────────────┘

Background: #F8F8F8
Padding: 30px
Border-radius: 0px
Box-shadow: none
Alignment: Center
```

### Testimonial Card
```
┌─────────────────────────────┐
│  ★★★★★                      │  Rating: #EB512F
│                             │
│  "Zeer tevreden over        │  Quote: 16px, Italic
│   deze jonge ondernemer.    │
│   Is een aanrader!"         │
│                             │
│  — Daan                     │  Author: 14px, Bold
└─────────────────────────────┘

Background: #FFFFFF
Padding: 20px
Border-radius: 8px
Box-shadow: 0 2px 8px rgba(0,0,0,0.1)
Width: 300px (carousel item)
```

### Product Card
```
┌─────────────────────────────┐
│                             │
│       [PRODUCT IMAGE]       │  Aspect ratio: 15:10
│                             │
│                             │
├─────────────────────────────┤
│                             │
│  Glazen schuifwand 2-rail   │  Title: 20px, Bold
│                             │
│  2-rail inclusief 2 glazen  │  Description: 14px
│  deuren tot 203cm breed     │
│                             │
│  €599,00                    │  Price: 24px, Bold, #EB512F
│                             │
│  ┌───────────────────┐      │
│  │     Koop nu       │      │  CTA Button
│  └───────────────────┘      │
│                             │
└─────────────────────────────┘

Background: #FFFFFF
Layout: Landscape
Frame: Visible
Alignment: Center
```

---

## Section Layouts

### Hero Section
```
┌───────────────────────────────────────────┐
│                                           │
│  [PARALLAX BACKGROUND IMAGE]              │
│  Overlay: rgba(0,0,0,0.32)                │
│                                           │
│        Glazen schuifwanden                │  H1: 48px, White
│                                           │
│  Creëer jouw perfecte buitenruimte        │  Subtitle: 24px, White
│  met glazen schuifwanden                  │
│                                           │
│  ┌─────────────────────────┐              │
│  │  Bereken mijn prijs     │              │  Primary CTA
│  └─────────────────────────┘              │
│                                           │
└───────────────────────────────────────────┘

Min-height: 600px (desktop), 400px (mobile)
Content: Centered vertically and horizontally
Parallax: Enabled
```

### Features Grid Section
```
┌───────────────────────────────────────────────────────┐
│                                                       │
│                   Onze Voordelen                      │  H2: 24px
│                                                       │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐
│  │  [ICON]  │  │  [ICON]  │  │  [ICON]  │  │  [ICON]  │
│  │          │  │          │  │          │  │          │
│  │  Title   │  │  Title   │  │  Title   │  │  Title   │
│  │          │  │          │  │          │  │          │
│  │  Text... │  │  Text... │  │  Text... │  │  Text... │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘
│                                                       │
└───────────────────────────────────────────────────────┘

Background: #F8F8F8
Max-width: 1120px
Grid: 4 columns (desktop), 1 column (mobile)
Gap: 20px
Padding: 60px 20px
```

### Testimonials Carousel
```
┌───────────────────────────────────────────────────────┐
│                                                       │
│            Wat onze klanten zeggen                    │  H2: 24px
│                                                       │
│  ← ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌────────→ │
│    │ ★★★★★   │ │ ★★★★★   │ │ ★★★★★   │ │ ★★★★★  │
│    │         │ │         │ │         │ │        │
│    │ "Quote" │ │ "Quote" │ │ "Quote" │ │ "Quote"│
│    │         │ │         │ │         │ │        │
│    │ — Name  │ │ — Name  │ │ — Name  │ │ — Name │
│    └──────────┘ └──────────┘ └──────────┘ └────────┘
│                                                       │
└───────────────────────────────────────────────────────┘

Layout: Horizontal scroll carousel
Swipeable: Yes
Scroll-snap: Enabled
Card width: 300px
Gap: 20px
Background: #F2F2F2 (container)
Padding: 40px 20px
```

### Contact Section
```
┌───────────────────────────────────────────┐
│                                           │
│          Neem contact op                  │  H2: 24px
│                                           │
│  📞  06 15 24 63 83                       │  Icon + Text
│  ✉️  info@deglaswand.nl                   │
│  🕐  Ma - Za, 8:00 - 17:00                │
│                                           │
│  ┌─────────────────────────┐              │
│  │  Bereken mijn prijs     │              │  Primary CTA
│  └─────────────────────────┘              │
│                                           │
└───────────────────────────────────────────┘

Background: #FFFFFF
Max-width: 600px
Alignment: Center
Padding: 60px 20px
```

---

## Icon Library

### Required Icons (48x48px, #EB512F)

1. **TapeMeasureIcon** - Gratis inmeten
   - Usage: Free measurement service
   - Style: Line icon with tape measure

2. **BulbIcon** - Maatwerk
   - Usage: Custom solutions
   - Style: Light bulb outline

3. **TruckExpressIcon** - Spoedmontage
   - Usage: Fast installation
   - Style: Delivery truck with speed lines

4. **SignEuroIcon** - Gratis levering
   - Usage: Free delivery
   - Style: Euro symbol in circle

### Social Media Icons (24x24px)
- Instagram: Outline style, #585A61
- Facebook: Outline style, #585A61 (if needed)
- LinkedIn: Outline style, #585A61 (if needed)

---

## Header Component

### Desktop Header (70px height)
```
┌─────────────────────────────────────────────────────────┐
│  [LOGO]    Home  Glazen schuifwand  Veranda  Contact │🔍│
└─────────────────────────────────────────────────────────┘

Background: #FFFFFF
Position: Sticky (top: 0)
Z-index: 999
Shadow: 0 2px 4px rgba(0,0,0,0.1)
Logo: Left-aligned, max-height 50px
Menu: Right-aligned, #585A61
Search: Icon button, right-most
```

### Mobile Header (60px height)
```
┌─────────────────────────────────────┐
│  ☰    [LOGO]                    🔍  │
└─────────────────────────────────────┘

Hamburger: Left (opens side drawer)
Logo: Center
Search: Right
```

---

## Footer Component

```
┌───────────────────────────────────────────────────────┐
│                                                       │
│  © 2025 Dé Glaswand                                   │
│                                                       │
│  Algemene Voorwaarden | Verzending & Betaling        │
│  Retourbeleid | Misbruik melden                      │
│                                                       │
│  Powered by Lightspeed                                │
│                                                       │
│  Cookie instellingen                                  │
│                                                       │
└───────────────────────────────────────────────────────┘

Background: #FAFAFA
Text color: #464646
Links: #585A61
Padding: 40px 20px
Alignment: Center
Font-size: 14px
```

---

## Form Components

### Input Field
```
┌─────────────────────────────────┐
│  Naam *                         │
└─────────────────────────────────┘

Border: 1px solid #E0E0E0
Border-radius: 4px
Padding: 12px 16px
Font-size: 16px
Color: #1F3D58

Focus State:
┌─────────────────────────────────┐
│  Naam * |                       │
└─────────────────────────────────┘
Border: 2px solid #EB512F
Outline: none
```

### Textarea
```
┌─────────────────────────────────┐
│  Bericht                        │
│                                 │
│                                 │
│                                 │
└─────────────────────────────────┘

Height: 120px
Resize: Vertical
Other styles: Same as input field
```

### Select Dropdown
```
┌─────────────────────────────────┐
│  Glazen schuifwand 2-rail    ▼ │
└─────────────────────────────────┘

Arrow: Custom SVG, #585A61
Styles: Same as input field
```

---

## Responsive Breakpoints

### Mobile (< 768px)
- Single column layout
- Stacked navigation in hamburger menu
- Hero text: 36px (instead of 48px)
- Hero height: 400px (instead of 600px)
- Feature cards: Full width, stacked
- Testimonials: Single card visible
- Contact info: Stacked vertically

### Tablet (768px - 1024px)
- Two column layout for features
- Hero text: 42px
- Adjusted padding: 40px (instead of 60px)
- Testimonials: 2 cards visible

### Desktop (> 1024px)
- Full four column layout
- Maximum content width: 1120px
- All features visible
- Testimonials: 3-4 cards visible
- Full navigation visible

---

## Animation Specifications

### Page Load Animations
- Fade in: 0.3s ease-in
- Slide up: 0.5s ease-out (delay 0.1s per element)

### Hover Animations
- Button hover: 0.3s ease
- Card lift: transform translateY(-5px), 0.3s ease
- Shadow expand: 0.3s ease

### Scroll Animations
- Parallax: 0.5 speed multiplier, 0.3s ease-out
- Fade in on scroll: When element enters viewport (80% visible)

### Carousel
- Scroll snap: smooth
- Swipe threshold: 50px
- Auto-scroll interval: 5s (optional)

---

## Spacing System

### Margin/Padding Values
```
Extra Small:  8px
Small:        16px
Medium:       20px
Large:        30px
Extra Large:  40px
XXL:          60px
Section:      100px (tile margins)
```

### Grid Gaps
```
Tight:   8px
Normal:  20px
Wide:    30px
```

---

## Shadow System

### Elevation Levels
```
Level 1 (Subtle):
box-shadow: 0 2px 4px rgba(0,0,0,0.1);

Level 2 (Card):
box-shadow: 0 2px 8px rgba(0,0,0,0.1);

Level 3 (Lifted):
box-shadow: 0 4px 12px rgba(0,0,0,0.15);

Level 4 (Modal):
box-shadow: 0 8px 20px rgba(0,0,0,0.2);

Accent (Button hover):
box-shadow: 0 4px 12px rgba(235,81,47,0.3);
```

---

## Border Radius System

```
None:    0px    (Feature cards, header)
Small:   4px    (Buttons, inputs)
Medium:  8px    (Testimonial cards)
Large:   12px   (Product cards)
Round:   50%    (Avatar images, icon badges)
```

---

## Image Specifications

### Logo
- Format: PNG with transparency
- Sizes: 200x200px (standard), 936x200px (full)
- Max display height: 50px (desktop), 40px (mobile)

### Hero Images
- Format: WebP with JPG fallback
- Sizes: 1000x1000, 2000x2000 (full width)
- Optimization: 80% quality
- Overlay: rgba(0,0,0,0.32)

### Product Images
- Format: WebP with JPG fallback
- Aspect ratio: 15:10 (landscape)
- Sizes: 500x500, 1000x1000
- Alt text: Required

### Testimonial Avatars
- Format: JPG or PNG
- Size: 100x100px
- Border-radius: 50% (circular)

### Icon Images
- Format: SVG (preferred) or PNG
- Size: 48x48px
- Color: #EB512F (can be changed with CSS filter)

---

## Content Guidelines

### Tone of Voice
- Professional yet approachable
- Use informal "je/jouw" addressing
- Focus on benefits, not just features
- Emphasize quality and craftsmanship

### Key Messages
1. Gratis inmeten (Free measurement)
2. Maatwerk mogelijk (Custom solutions available)
3. Spoedmontage (Fast installation)
4. Kwaliteit staat voorop (Quality first)
5. Persoonlijke service (Personal service)

### Call-to-Action Variations
- Primary: "Bereken mijn prijs" (Calculate my price)
- Secondary: "Koop nu" (Buy now)
- Tertiary: "Neem contact op" (Contact us)
- Form: "Verstuur" (Send)
- Quote: "Vraag offerte aan" (Request quote)

---

## WordPress Block Mapping Summary

| Component         | WordPress Block(s)              | Custom CSS      |
|-------------------|---------------------------------|-----------------|
| Hero              | core/cover                      | Yes (parallax)  |
| Feature Grid      | core/columns > core/group       | Yes (icons)     |
| Testimonials      | core/query or custom            | Yes (carousel)  |
| Product Card      | woocommerce/product-card        | Minor tweaks    |
| Contact Info      | core/group > core/paragraph     | No              |
| Team Member       | core/media-text                 | No              |
| CTA Section       | core/group > core/buttons       | No              |
| Navigation        | core/navigation                 | Yes (sticky)    |
| Footer            | core/template-part              | Yes (layout)    |

---

## Asset Checklist

### Required Assets
- [ ] Logo (PNG, 936x200px)
- [ ] Hero background image (2000x2000px)
- [ ] 4x Feature icons (SVG, 48x48px)
- [ ] Product images (1000x1000px, 15:10 aspect)
- [ ] Testimonial avatars (100x100px, circular)
- [ ] Team member photo (500x500px)
- [ ] Social media icons (SVG, 24x24px)
- [ ] Favicon (32x32px, ICO)
- [ ] Apple touch icon (180x180px)

### Font Files
- [ ] Questrial (WOFF2 format)
- [ ] Cormorant Garamond (WOFF2 format)
- License: Check Google Fonts license

---

This component reference provides all visual specifications needed to accurately rebuild the Dé Glaswand website in WordPress FSE. Use this alongside the design tokens JSON and implementation guide for complete reconstruction.
