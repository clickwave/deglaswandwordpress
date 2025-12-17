# Design Analysis: deglaswand.nl
**Analysis Date:** 2025-12-05
**Target Website:** https://deglaswand.nl/

---

## 1. DARK BLUE SECTION - "een heldere kijk"

### Visual Analysis
The dark blue section is a full-width content breaker that creates strong visual contrast against the white background. It features large, bold typography with the heading "een heldere kijk" and body text below.

### Background Color
**EXACT COLOR:** The actual background is applied to a parent container (not captured in the immediate wrapper).
- **Visual Inspection:** Dark blue/navy (#2C4D6B approximately based on screenshot)
- **Section Wrapper:** `rgba(0, 0, 0, 0)` (transparent - color comes from parent)

### Layout Structure

#### Container
```json
{
  "element": "DIV",
  "className": "ins-tile__wrap ins-tile__animated",
  "display": "grid",
  "maxWidth": "1120px",
  "spacing": {
    "marginTop": "100px",
    "marginBottom": "100px",
    "padding": "0px"
  }
}
```

**WordPress Block Mapping:**
- Use `core/group` with custom background color
- Max-width: 1120px (contained)
- Vertical spacing: 100px top/bottom margin

### Typography Hierarchy

#### H2 - Main Headline
```json
{
  "tagName": "H2",
  "className": "ins-tile__title",
  "fontFamily": "Outfit, sans-serif",
  "fontSize": "104px",
  "fontWeight": "700",
  "lineHeight": "106.2px",
  "color": "rgb(255, 255, 255)",
  "marginBottom": "22px"
}
```

**Design Tokens:**
- **Font:** Outfit Bold (700)
- **Size:** 104px (6.5rem)
- **Line Height:** 106.2px (~1.02 ratio - very tight)
- **Color:** White (#FFFFFF)
- **Spacing:** 22px bottom margin

#### P - Body Text
```json
{
  "tagName": "P",
  "fontFamily": "Questrial, system-ui",
  "fontSize": "20px",
  "fontWeight": "400",
  "lineHeight": "36px",
  "color": "rgba(255, 255, 255, 0.65)"
}
```

**Design Tokens:**
- **Font:** Questrial Regular (400)
- **Size:** 20px (1.25rem)
- **Line Height:** 36px (1.8 ratio - generous)
- **Color:** White at 65% opacity (rgba(255, 255, 255, 0.65))
- **Spacing:** 0 margin (flows naturally)

### How It Breaks Up the Page
- **Full-width section** with centered content (max-width 1120px)
- **Large vertical spacing** (100px margins) creates breathing room
- **Dark background** provides strong visual contrast
- **Large typography** draws attention and creates hierarchy
- Acts as a **visual anchor** between product sections

### WordPress FSE Implementation Strategy

```
<!-- wp:group {"style":{"spacing":{"padding":{"top":"100px","bottom":"100px"}}},"backgroundColor":"navy-blue","layout":{"type":"constrained","contentSize":"1120px"}} -->
<div class="wp-block-group has-navy-blue-background-color has-background">

    <!-- wp:heading {"level":2,"fontSize":"huge","style":{"typography":{"fontFamily":"Outfit","fontWeight":"700","fontSize":"104px","lineHeight":"1.02"}}} -->
    <h2 class="wp-block-heading">een heldere kijk</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"style":{"color":{"text":"rgba(255,255,255,0.65)"},"typography":{"fontSize":"20px","lineHeight":"1.8"}}} -->
    <p>Bij "dé glaswand" brengen we stijl en functionaliteit samen...</p>
    <!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
```

---

## 2. FOOTER SECTION

### Visual Analysis
The footer is minimalist and clean, featuring a light gray background with centered content. It includes copyright text and horizontal navigation links with subtle styling.

### Background Color
**EXACT COLOR:** `rgb(250, 250, 250)` (#FAFAFA - Very light gray)
- **Gradient:** `linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0) 100%)` (transparent gradient - no visual effect)

### Layout Structure

#### Footer Container
```json
{
  "element": "FOOTER",
  "className": "ins-tile ins-tile--footer ins-tile--center",
  "display": "block",
  "dimensions": {
    "width": "1905px",
    "height": "162.5px"
  },
  "spacing": {
    "paddingLeft": "40px",
    "paddingRight": "40px",
    "paddingTop": "0px",
    "paddingBottom": "0px"
  },
  "background": "#FAFAFA"
}
```

#### Content Wrapper
```json
{
  "element": "DIV",
  "className": "ins-tile__wrap",
  "maxWidth": "1120px",
  "margin": "30px auto",
  "textAlign": "center"
}
```

**Design Tokens:**
- **Container Width:** Full-width
- **Content Max-Width:** 1120px (centered with auto margins)
- **Vertical Padding:** 30px top/bottom
- **Horizontal Padding:** 40px left/right
- **Height:** ~162.5px (auto-generated from content)
- **Alignment:** Center

### Logo Positioning
**Status:** No logo detected in the footer (logo is in header only)
- The footer focuses on text links and copyright information
- Could potentially add a centered logo above the copyright text

### Column Layout
The footer uses a **single-column centered layout** rather than multiple columns:
- All content is center-aligned
- Copyright text appears first (top)
- Horizontal navigation links appear below
- Social icons (Instagram) below links

### Typography Hierarchy

#### Copyright Text (Primary)
```json
{
  "tagName": "P",
  "text": "© 2025 Dé Glaswand. Alle rechten voorbehouden.",
  "fontFamily": "Questrial, system-ui",
  "fontSize": "16px",
  "fontWeight": "400",
  "lineHeight": "28px",
  "color": "rgb(70, 70, 70)"
}
```

**Design Tokens:**
- **Font:** Questrial Regular
- **Size:** 16px (1rem)
- **Line Height:** 28px (1.75 ratio)
- **Color:** Dark gray (#464646)

#### Navigation Links
```json
{
  "tagName": "A",
  "className": "ins-tile__link",
  "fontFamily": "Questrial, system-ui",
  "fontSize": "14px",
  "fontWeight": "400",
  "lineHeight": "24px",
  "color": "rgb(155, 155, 155)"
}
```

**Design Tokens:**
- **Font:** Questrial Regular
- **Size:** 14px (0.875rem)
- **Line Height:** 24px (1.71 ratio)
- **Color:** Medium gray (#9B9B9B)
- **Links:** Algemene voorwaarden, Verzend- en betaalinformatie, Retourbeleid, Rapporteer misbruik

### Spacing Details
- **Footer Padding:** 40px horizontal (left/right)
- **Content Margin:** 30px top/bottom, centered horizontally (352.5px auto margins)
- **Inter-element spacing:** Managed by content flow and line-height

### Color Scheme Summary
```css
/* Footer Colors */
--footer-background: #FAFAFA;
--footer-copyright-text: #464646;
--footer-link-text: #9B9B9B;
--footer-link-hover: /* Not captured - likely darker gray */
```

### Responsive Behavior Predictions
Based on the structure:
1. **Desktop (1920px):** Full horizontal layout, centered content at 1120px
2. **Tablet (768px-1024px):** Content remains centered, may reduce horizontal padding
3. **Mobile (<768px):**
   - Links likely stack vertically or wrap
   - Reduce font sizes slightly
   - Maintain center alignment
   - Reduce padding to 20px

### WordPress FSE Implementation Strategy

```html
<!-- wp:group {"style":{"spacing":{"padding":{"top":"30px","bottom":"30px","left":"40px","right":"40px"}}},"backgroundColor":"light-gray","layout":{"type":"constrained","contentSize":"1120px"}} -->
<footer class="wp-block-group has-light-gray-background-color has-background">

    <!-- wp:paragraph {"align":"center","fontSize":"medium","style":{"color":{"text":"#464646"}}} -->
    <p class="has-text-align-center">© 2025 Dé Glaswand. Alle rechten voorbehouden.</p>
    <!-- /wp:paragraph -->

    <!-- wp:navigation {"layout":{"type":"flex","justifyContent":"center"},"fontSize":"small","style":{"color":{"text":"#9B9B9B"}}} /-->

    <!-- wp:social-links {"iconColor":"dark-gray","iconColorValue":"#464646","align":"center"} -->
    <ul class="wp-block-social-links aligncenter has-icon-color">
        <!-- wp:social-link {"url":"#","service":"instagram"} /-->
    </ul>
    <!-- /wp:social-links -->

</footer>
<!-- /wp:group -->
```

---

## 3. DESIGN TOKEN SUMMARY FOR FSE ARCHITECT

### Color Palette
```json
{
  "colors": {
    "primary": {
      "navyBlue": "#2C4D6B",
      "description": "Dark blue section background"
    },
    "neutral": {
      "lightGray": "#FAFAFA",
      "mediumGray": "#9B9B9B",
      "darkGray": "#464646",
      "description": "Footer and text colors"
    },
    "white": {
      "pure": "#FFFFFF",
      "opacity65": "rgba(255, 255, 255, 0.65)",
      "description": "Text on dark backgrounds"
    }
  }
}
```

### Typography System
```json
{
  "fonts": {
    "primary": {
      "family": "Outfit",
      "weights": [700],
      "usage": "Headlines, large display text"
    },
    "secondary": {
      "family": "Questrial",
      "weights": [400],
      "fallback": "system-ui, 'Segoe UI', Roboto, Arial, sans-serif",
      "usage": "Body text, links, general content"
    }
  },
  "scales": {
    "hero": {
      "size": "104px",
      "lineHeight": "1.02",
      "weight": 700,
      "font": "Outfit"
    },
    "bodyLarge": {
      "size": "20px",
      "lineHeight": "1.8",
      "weight": 400,
      "font": "Questrial"
    },
    "bodyMedium": {
      "size": "16px",
      "lineHeight": "1.75",
      "weight": 400,
      "font": "Questrial"
    },
    "bodySmall": {
      "size": "14px",
      "lineHeight": "1.71",
      "weight": 400,
      "font": "Questrial"
    }
  }
}
```

### Spacing System
```json
{
  "spacing": {
    "section": {
      "vertical": "100px",
      "description": "Top/bottom margins for major sections"
    },
    "container": {
      "maxWidth": "1120px",
      "padding": "40px",
      "description": "Main content container"
    },
    "footer": {
      "vertical": "30px",
      "horizontal": "40px"
    },
    "typography": {
      "headlineMargin": "22px",
      "paragraphMargin": "0px"
    }
  }
}
```

### Layout Patterns
```json
{
  "layouts": {
    "darkSection": {
      "type": "full-width-with-constraint",
      "background": "#2C4D6B",
      "contentMaxWidth": "1120px",
      "verticalSpacing": "100px",
      "alignment": "center"
    },
    "footer": {
      "type": "centered-single-column",
      "background": "#FAFAFA",
      "contentMaxWidth": "1120px",
      "verticalSpacing": "30px",
      "textAlign": "center"
    }
  }
}
```

---

## 4. IMPLEMENTATION NOTES

### Required WordPress Theme.json Additions

```json
{
  "settings": {
    "color": {
      "palette": [
        {
          "slug": "navy-blue",
          "color": "#2C4D6B",
          "name": "Navy Blue"
        },
        {
          "slug": "light-gray",
          "color": "#FAFAFA",
          "name": "Light Gray"
        },
        {
          "slug": "medium-gray",
          "color": "#9B9B9B",
          "name": "Medium Gray"
        },
        {
          "slug": "dark-gray",
          "color": "#464646",
          "name": "Dark Gray"
        }
      ]
    },
    "typography": {
      "fontFamilies": [
        {
          "fontFamily": "Outfit, sans-serif",
          "name": "Outfit",
          "slug": "outfit"
        },
        {
          "fontFamily": "Questrial, system-ui, 'Segoe UI', Roboto, Arial, sans-serif",
          "name": "Questrial",
          "slug": "questrial"
        }
      ],
      "fontSizes": [
        {
          "size": "14px",
          "slug": "small",
          "name": "Small"
        },
        {
          "size": "16px",
          "slug": "medium",
          "name": "Medium"
        },
        {
          "size": "20px",
          "slug": "large",
          "name": "Large"
        },
        {
          "size": "104px",
          "slug": "huge",
          "name": "Huge"
        }
      ]
    },
    "spacing": {
      "spacingSizes": [
        {
          "size": "30px",
          "slug": "small",
          "name": "Small"
        },
        {
          "size": "40px",
          "slug": "medium",
          "name": "Medium"
        },
        {
          "size": "100px",
          "slug": "large",
          "name": "Large"
        }
      ]
    },
    "layout": {
      "contentSize": "1120px",
      "wideSize": "1920px"
    }
  }
}
```

### Font Loading Strategy
**Outfit:** Google Fonts - `@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@700&display=swap');`
**Questrial:** Google Fonts - `@import url('https://fonts.googleapis.com/css2?family=Questrial&display=swap');`

### Responsive Breakpoints (Recommended)
```css
/* Mobile First Approach */
--mobile: 320px;
--tablet: 768px;
--desktop: 1120px;
--wide: 1920px;

/* Font Scaling for Hero */
@media (max-width: 768px) {
  .hero-heading {
    font-size: 48px; /* Down from 104px */
    line-height: 1.1;
  }
}

@media (min-width: 769px) and (max-width: 1024px) {
  .hero-heading {
    font-size: 72px;
    line-height: 1.05;
  }
}
```

---

## 5. KEY FINDINGS & RECOMMENDATIONS

### What Works Well
1. **Strong Typography Contrast:** The 104px hero heading creates immediate visual impact
2. **Generous Spacing:** 100px vertical margins prevent cramped feeling
3. **Color Opacity Usage:** `rgba(255, 255, 255, 0.65)` for body text provides hierarchy without harshness
4. **Minimalist Footer:** Clean, centered approach keeps focus on essential information
5. **Consistent Max-Width:** 1120px content container maintains readability

### Design Patterns to Replicate
1. **Full-Width Color Blocks:** Use `core/group` with background color and constrained content
2. **Typography Scale:** Massive headlines (104px) paired with readable body (20px)
3. **Opacity for Hierarchy:** Use alpha channel for secondary text rather than different colors
4. **Breathing Room:** 100px section spacing creates premium feel
5. **Center-Aligned Footer:** Simple, effective information architecture

### Potential Improvements for Clickwave
1. **Add Footer Logo:** Center-aligned logo above copyright would strengthen branding
2. **Footer Columns:** Consider multi-column layout for additional content (services, contact, social)
3. **Mobile Optimization:** Ensure 104px heading scales appropriately (suggested 48px on mobile)
4. **Dark Section Variation:** Create pattern with image background + dark overlay
5. **Button Integration:** Add CTA button in dark section (already visible in screenshot: "Bereken mijn prijs")

---

## FILES GENERATED
- `dark-section-screenshot.png` - Visual reference of the dark blue section
- `footer-screenshot.png` - Visual reference of the footer
- `dark-section-tokens.json` - Complete design tokens for dark section
- `footer-tokens.json` - Complete design tokens for footer
- `analyze-deglaswand.js` - Playwright automation script for future analysis

---

**Analysis Complete**
Ready for FSE Architect implementation.
