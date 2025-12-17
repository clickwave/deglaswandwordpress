# Dé Glaswand - WordPress FSE Implementation Guide

## Overview
This guide provides detailed instructions for rebuilding the Dé Glaswand website using WordPress Full Site Editing (FSE) based on the extracted design tokens and content structure.

---

## 1. Theme Setup

### Base Theme Configuration
```json
{
  "version": 2,
  "settings": {
    "color": {
      "palette": [
        {
          "slug": "dark-blue",
          "color": "#1F3D58",
          "name": "Dark Blue"
        },
        {
          "slug": "accent-orange",
          "color": "#EB512F",
          "name": "Accent Orange"
        },
        {
          "slug": "white",
          "color": "#FFFFFF",
          "name": "White"
        },
        {
          "slug": "light-grey",
          "color": "#F2F2F2",
          "name": "Light Grey"
        },
        {
          "slug": "lightest-grey",
          "color": "#F8F8F8",
          "name": "Lightest Grey"
        },
        {
          "slug": "dark-grey",
          "color": "#585A61",
          "name": "Dark Grey"
        }
      ]
    },
    "typography": {
      "fontFamilies": [
        {
          "fontFamily": "Questrial, sans-serif",
          "slug": "questrial",
          "name": "Questrial"
        },
        {
          "fontFamily": "Cormorant Garamond, serif",
          "slug": "cormorant-garamond",
          "name": "Cormorant Garamond"
        }
      ],
      "fontSizes": [
        {
          "slug": "small",
          "size": "14px",
          "name": "Small"
        },
        {
          "slug": "medium",
          "size": "16px",
          "name": "Medium"
        },
        {
          "slug": "normal",
          "size": "18px",
          "name": "Normal"
        },
        {
          "slug": "large",
          "size": "20px",
          "name": "Large"
        },
        {
          "slug": "subtitle",
          "size": "24px",
          "name": "Subtitle"
        },
        {
          "slug": "hero",
          "size": "48px",
          "name": "Hero"
        }
      ]
    },
    "layout": {
      "contentSize": "1120px",
      "wideSize": "1400px"
    },
    "spacing": {
      "units": ["px", "em", "rem", "vh", "vw", "%"]
    }
  }
}
```

---

## 2. Custom Block Patterns

### Hero Section Pattern
```php
<?php
/**
 * Title: Hero - Glazen Schuifwanden
 * Slug: deglaswand/hero-glazen-schuifwanden
 * Categories: featured
 */
?>

<!-- wp:cover {"url":"hero-image.jpg","dimRatio":32,"overlayColor":"black","minHeight":600,"contentPosition":"center center","isDark":true,"align":"full","className":"parallax-enabled"} -->
<div class="wp-block-cover alignfull is-dark parallax-enabled" style="min-height:600px">
    <span aria-hidden="true" class="wp-block-cover__background has-black-background-color has-background-dim-30 has-background-dim"></span>
    <img class="wp-block-cover__image-background" alt="Glazen schuifwanden" src="hero-image.jpg"/>
    <div class="wp-block-cover__inner-container">
        <!-- wp:heading {"textAlign":"center","level":1,"fontSize":"hero","textColor":"white"} -->
        <h1 class="has-text-align-center has-white-color has-text-color has-hero-font-size">Glazen schuifwanden</h1>
        <!-- /wp:heading -->

        <!-- wp:paragraph {"align":"center","fontSize":"subtitle","textColor":"white"} -->
        <p class="has-text-align-center has-white-color has-text-color has-subtitle-font-size">Creëer jouw perfecte buitenruimte met glazen schuifwanden</p>
        <!-- /wp:paragraph -->

        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
        <div class="wp-block-buttons">
            <!-- wp:button {"backgroundColor":"accent-orange","className":"is-style-fill"} -->
            <div class="wp-block-button is-style-fill">
                <a class="wp-block-button__link has-accent-orange-background-color has-background wp-element-button" href="https://configurator.deglaswand.nl/">Bereken mijn prijs</a>
            </div>
            <!-- /wp:button -->
        </div>
        <!-- /wp:buttons -->
    </div>
</div>
<!-- /wp:cover -->
```

### Features Grid Pattern
```php
<?php
/**
 * Title: Features - Four Benefits Grid
 * Slug: deglaswand/features-benefits-grid
 * Categories: featured
 */
?>

<!-- wp:group {"backgroundColor":"lightest-grey","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-lightest-grey-background-color has-background">
    <!-- wp:heading {"textAlign":"center","textColor":"dark-blue"} -->
    <h2 class="has-text-align-center has-dark-blue-color has-text-color">Onze Voordelen</h2>
    <!-- /wp:heading -->

    <!-- wp:columns {"verticalAlignment":null} -->
    <div class="wp-block-columns">
        <!-- wp:column {"verticalAlignment":"top"} -->
        <div class="wp-block-column is-vertically-aligned-top">
            <!-- wp:group {"style":{"spacing":{"padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group" style="padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px">
                <!-- wp:image {"align":"center","width":48,"height":48,"sizeSlug":"full"} -->
                <figure class="wp-block-image aligncenter size-full is-resized">
                    <img src="tape-measure-icon.svg" alt="Gratis inmeten" style="color:#EB512F" width="48" height="48"/>
                </figure>
                <!-- /wp:image -->

                <!-- wp:heading {"textAlign":"center","level":3,"textColor":"dark-blue"} -->
                <h3 class="has-text-align-center has-dark-blue-color has-text-color">Gratis inmeten</h3>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"align":"center"} -->
                <p class="has-text-align-center">Wij meten uw glazen schuifwand gratis in en maken op locatie een gepaste offerte.</p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"top"} -->
        <div class="wp-block-column is-vertically-aligned-top">
            <!-- wp:group {"style":{"spacing":{"padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group" style="padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px">
                <!-- wp:image {"align":"center","width":48,"height":48,"sizeSlug":"full"} -->
                <figure class="wp-block-image aligncenter size-full is-resized">
                    <img src="bulb-icon.svg" alt="Maatwerk" style="color:#EB512F" width="48" height="48"/>
                </figure>
                <!-- /wp:image -->

                <!-- wp:heading {"textAlign":"center","level":3,"textColor":"dark-blue"} -->
                <h3 class="has-text-align-center has-dark-blue-color has-text-color">Maatwerk</h3>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"align":"center"} -->
                <p class="has-text-align-center">Maatwerk nodig? Wij gaan elke uitdaging aan!</p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"top"} -->
        <div class="wp-block-column is-vertically-aligned-top">
            <!-- wp:group {"style":{"spacing":{"padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group" style="padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px">
                <!-- wp:image {"align":"center","width":48,"height":48,"sizeSlug":"full"} -->
                <figure class="wp-block-image aligncenter size-full is-resized">
                    <img src="truck-express-icon.svg" alt="Spoedmontage" style="color:#EB512F" width="48" height="48"/>
                </figure>
                <!-- /wp:image -->

                <!-- wp:heading {"textAlign":"center","level":3,"textColor":"dark-blue"} -->
                <h3 class="has-text-align-center has-dark-blue-color has-text-color">Spoedmontage</h3>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"align":"center"} -->
                <p class="has-text-align-center">Wij monteren uw glazen schuifwand binnen 4 weken na het inmeten</p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"top"} -->
        <div class="wp-block-column is-vertically-aligned-top">
            <!-- wp:group {"style":{"spacing":{"padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group" style="padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px">
                <!-- wp:image {"align":"center","width":48,"height":48,"sizeSlug":"full"} -->
                <figure class="wp-block-image aligncenter size-full is-resized">
                    <img src="sign-euro-icon.svg" alt="Gratis levering" style="color:#EB512F" width="48" height="48"/>
                </figure>
                <!-- /wp:image -->

                <!-- wp:heading {"textAlign":"center","level":3,"textColor":"dark-blue"} -->
                <h3 class="has-text-align-center has-dark-blue-color has-text-color">Gratis levering</h3>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"align":"center"} -->
                <p class="has-text-align-center">En natuurlijk... Gratis levering!</p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->
```

---

## 3. Custom Post Types

### Testimonials
```php
<?php
// Register Testimonials CPT
function deglaswand_register_testimonial_cpt() {
    register_post_type('testimonial', [
        'labels' => [
            'name' => 'Testimonials',
            'singular_name' => 'Testimonial',
        ],
        'public' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'menu_icon' => 'dashicons-testimonial',
    ]);
}
add_action('init', 'deglaswand_register_testimonial_cpt');

// Custom fields for testimonials
function deglaswand_testimonial_meta_boxes() {
    add_meta_box(
        'testimonial_details',
        'Testimonial Details',
        'deglaswand_testimonial_meta_box_callback',
        'testimonial',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'deglaswand_testimonial_meta_boxes');

function deglaswand_testimonial_meta_box_callback($post) {
    $rating = get_post_meta($post->ID, '_testimonial_rating', true);
    $author = get_post_meta($post->ID, '_testimonial_author', true);
    ?>
    <p>
        <label for="testimonial_author">Author Name:</label>
        <input type="text" id="testimonial_author" name="testimonial_author" value="<?php echo esc_attr($author); ?>" style="width:100%;">
    </p>
    <p>
        <label for="testimonial_rating">Rating (1-5):</label>
        <select id="testimonial_rating" name="testimonial_rating">
            <?php for($i = 1; $i <= 5; $i++): ?>
                <option value="<?php echo $i; ?>" <?php selected($rating, $i); ?>><?php echo $i; ?> Stars</option>
            <?php endfor; ?>
        </select>
    </p>
    <?php
}
```

### Team Members
```php
<?php
// Register Team Members CPT
function deglaswand_register_team_cpt() {
    register_post_type('team_member', [
        'labels' => [
            'name' => 'Team Members',
            'singular_name' => 'Team Member',
        ],
        'public' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'menu_icon' => 'dashicons-groups',
    ]);
}
add_action('init', 'deglaswand_register_team_cpt');

// Custom fields
function deglaswand_team_meta_boxes() {
    add_meta_box(
        'team_details',
        'Team Member Details',
        'deglaswand_team_meta_box_callback',
        'team_member',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'deglaswand_team_meta_boxes');

function deglaswand_team_meta_box_callback($post) {
    $role = get_post_meta($post->ID, '_team_role', true);
    ?>
    <p>
        <label for="team_role">Role/Position:</label>
        <input type="text" id="team_role" name="team_role" value="<?php echo esc_attr($role); ?>" style="width:100%;">
    </p>
    <?php
}
```

---

## 4. Custom CSS

### Add to theme.json or custom CSS file
```css
/* Parallax Effect */
.parallax-enabled .wp-block-cover__image-background {
    transform: translateY(var(--parallax-offset, 0));
    transition: transform 0.3s ease-out;
}

/* Button Styles */
.wp-block-button__link {
    border-radius: 4px;
    padding: 12px 24px;
    transition: all 0.3s ease;
    font-family: 'Questrial', sans-serif;
}

.wp-block-button.is-style-fill .wp-block-button__link {
    background-color: #EB512F;
    color: #FFFFFF;
}

.wp-block-button.is-style-fill .wp-block-button__link:hover {
    background-color: #D44527;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(235, 81, 47, 0.3);
}

.wp-block-button.is-style-outline .wp-block-button__link {
    background-color: transparent;
    color: #FFFFFF;
    border: 2px solid #FFFFFF;
}

.wp-block-button.is-style-outline .wp-block-button__link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

/* Card Hover Effects */
.feature-card,
.testimonial-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.feature-card:hover,
.testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

/* Testimonial Carousel */
.testimonial-carousel {
    display: flex;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    gap: 20px;
    padding: 20px 0;
}

.testimonial-carousel .testimonial-card {
    scroll-snap-align: start;
    flex: 0 0 300px;
    background: #FFFFFF;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Star Rating */
.star-rating {
    color: #EB512F;
    font-size: 18px;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .wp-block-columns {
        flex-direction: column;
    }

    h1.has-hero-font-size {
        font-size: 36px;
    }

    .wp-block-cover {
        min-height: 400px !important;
    }
}
```

---

## 5. JavaScript Enhancements

### Parallax Scroll Effect
```javascript
// Add to theme's script.js
(function() {
    const parallaxElements = document.querySelectorAll('.parallax-enabled');

    if (parallaxElements.length > 0) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;

            parallaxElements.forEach(element => {
                const speed = 0.5;
                const offset = scrolled * speed;
                element.style.setProperty('--parallax-offset', offset + 'px');
            });
        });
    }
})();
```

### Testimonial Carousel
```javascript
// Swipeable testimonial carousel
(function() {
    const carousel = document.querySelector('.testimonial-carousel');

    if (carousel) {
        let isDown = false;
        let startX;
        let scrollLeft;

        carousel.addEventListener('mousedown', (e) => {
            isDown = true;
            carousel.classList.add('active');
            startX = e.pageX - carousel.offsetLeft;
            scrollLeft = carousel.scrollLeft;
        });

        carousel.addEventListener('mouseleave', () => {
            isDown = false;
            carousel.classList.remove('active');
        });

        carousel.addEventListener('mouseup', () => {
            isDown = false;
            carousel.classList.remove('active');
        });

        carousel.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - carousel.offsetLeft;
            const walk = (x - startX) * 2;
            carousel.scrollLeft = scrollLeft - walk;
        });
    }
})();
```

---

## 6. WooCommerce Integration

### Product Configuration
```php
<?php
// Add custom product fields for glass wall configurator
add_action('woocommerce_product_options_general_product_data', 'deglaswand_add_custom_fields');
function deglaswand_add_custom_fields() {
    woocommerce_wp_text_input([
        'id' => '_rail_count',
        'label' => __('Rail Count', 'deglaswand'),
        'placeholder' => '2',
        'desc_tip' => true,
        'description' => __('Number of rails for this product', 'deglaswand'),
    ]);

    woocommerce_wp_text_input([
        'id' => '_max_width',
        'label' => __('Maximum Width (cm)', 'deglaswand'),
        'placeholder' => '203',
        'desc_tip' => true,
        'description' => __('Maximum width for glass doors', 'deglaswand'),
    ]);
}

// Save custom fields
add_action('woocommerce_process_product_meta', 'deglaswand_save_custom_fields');
function deglaswand_save_custom_fields($post_id) {
    $rail_count = $_POST['_rail_count'] ?? '';
    $max_width = $_POST['_max_width'] ?? '';

    update_post_meta($post_id, '_rail_count', esc_attr($rail_count));
    update_post_meta($post_id, '_max_width', esc_attr($max_width));
}
```

---

## 7. Navigation Menu Structure

### Header Navigation (Primary)
```
- Home (/)
- Glazen schuifwand (/glazen-schuifwand)
- Steellook schuifwand (/steellook-schuifwand)
- Veranda (/veranda)
- Contact (/contact)
```

### Footer Navigation (Legal)
```
- Algemene Voorwaarden (/algemene-voorwaarden)
- Verzending & Betaling (/verzending-betaling)
- Retourbeleid (/retourbeleid)
- Misbruik melden (/abuse)
```

---

## 8. Contact Form 7 Configuration

### Basic Contact Form
```html
<div class="contact-form">
    [text* your-name placeholder "Naam *"]
    [email* your-email placeholder "E-mail *"]
    [tel* your-phone placeholder "Telefoon *"]
    [textarea your-message placeholder "Bericht"]
    [submit "Verstuur"]
</div>
```

### Quote Request Form
```html
<div class="quote-form">
    [text* your-name placeholder "Naam *"]
    [email* your-email placeholder "E-mail *"]
    [tel* your-phone placeholder "Telefoon *"]
    [text your-address placeholder "Adres"]
    [select product-type "Glazen schuifwand 2-rail" "Glazen schuifwand 3-rail" "Steellook schuifwand" "Anders"]
    [number width placeholder "Breedte (cm)"]
    [number height placeholder "Hoogte (cm)"]
    [textarea your-message placeholder "Extra informatie"]
    [submit "Vraag offerte aan"]
</div>
```

---

## 9. SEO Configuration

### Yoast SEO or Rank Math Settings
```php
// Homepage
Title: Dé Glaswand - Glazen Schuifwanden voor Veranda's & Tuinhuizen
Meta Description: Hoogwaardige glazen schuifwanden op maat. Gratis inmeten, spoedmontage binnen 4 weken. 10mm veiligheidsglas met premium coating.
Focus Keyword: glazen schuifwand

// Product Pages
Title Template: %%title%% - Dé Glaswand
Meta Description Template: %%title%% vanaf €%%price%%. %%excerpt%% Gratis inmeten en levering.

// Category Pages
Title Template: %%term_title%% - Dé Glaswand
Meta Description Template: Bekijk ons assortiment %%term_title%%. Hoogwaardige kwaliteit, maatwerk mogelijk. Gratis inmeten.
```

---

## 10. Performance Optimization

### Image Optimization
- Use WebP format with fallbacks
- Lazy loading enabled for all images
- Multiple image sizes: 200x200, 500x500, 1000x1000, 2000x2000
- Compress images before upload (max 80% quality)

### Caching
```php
// Add to wp-config.php
define('WP_CACHE', true);
define('COMPRESS_CSS', true);
define('COMPRESS_SCRIPTS', true);
define('CONCATENATE_SCRIPTS', true);
define('ENFORCE_GZIP', true);
```

### Critical CSS
```css
/* Inline critical CSS in header */
body {
    font-family: 'Questrial', sans-serif;
    font-size: 18px;
    color: #1F3D58;
    line-height: 1.6;
}

.site-header {
    height: 70px;
    background: #FFFFFF;
    position: sticky;
    top: 0;
    z-index: 999;
}

.wp-block-cover {
    min-height: 600px;
}
```

---

## 11. Accessibility Checklist

- [ ] All images have alt text
- [ ] Color contrast ratio meets WCAG AA standards
- [ ] Keyboard navigation works for all interactive elements
- [ ] ARIA labels added to icon buttons
- [ ] Form labels properly associated with inputs
- [ ] Skip to content link added
- [ ] Focus indicators visible on all focusable elements
- [ ] Heading hierarchy is logical (H1 -> H2 -> H3)

---

## 12. Browser Compatibility

### Tested Browsers
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile Safari (iOS 14+)
- Chrome Mobile (Android 10+)

### Fallbacks Required
```css
/* CSS Grid Fallback */
@supports not (display: grid) {
    .wp-block-columns {
        display: flex;
        flex-wrap: wrap;
    }
}

/* WebP Fallback */
.wp-block-image img[src$=".webp"] {
    background-image: var(--fallback-jpg);
}
```

---

## 13. Testing Checklist

### Functionality
- [ ] All navigation links work
- [ ] Contact forms submit successfully
- [ ] Product configurator loads correctly
- [ ] Price calculator functions
- [ ] Mobile menu opens/closes
- [ ] Search functionality works
- [ ] Testimonial carousel swipes

### Performance
- [ ] Page load time < 3 seconds
- [ ] Time to First Byte (TTFB) < 600ms
- [ ] First Contentful Paint (FCP) < 1.8s
- [ ] Largest Contentful Paint (LCP) < 2.5s
- [ ] Cumulative Layout Shift (CLS) < 0.1
- [ ] First Input Delay (FID) < 100ms

### Mobile Responsiveness
- [ ] Text is readable without zooming
- [ ] Buttons are easily tappable (min 44x44px)
- [ ] No horizontal scrolling
- [ ] Images scale appropriately
- [ ] Forms are easy to fill out

---

## 14. Deployment Steps

1. **Install WordPress** on production server
2. **Install required plugins:**
   - WooCommerce
   - Contact Form 7
   - Yoast SEO or Rank Math
   - WP Rocket or similar caching plugin
   - Wordfence Security
3. **Upload custom theme** with FSE templates
4. **Import design tokens** and configure theme.json
5. **Create pages** using block patterns
6. **Set up navigation menus**
7. **Configure WooCommerce** products
8. **Add testimonials** and team members
9. **Test all functionality**
10. **Optimize performance**
11. **Enable SSL certificate**
12. **Set up analytics** (Google Analytics, Google Tag Manager)
13. **Submit sitemap** to Google Search Console
14. **Go live!**

---

## Support Resources

- **WordPress Block Editor Handbook:** https://developer.wordpress.org/block-editor/
- **Theme.json Documentation:** https://developer.wordpress.org/themes/advanced-topics/theme-json/
- **WooCommerce Documentation:** https://woocommerce.com/documentation/
- **Contact Form 7 Guide:** https://contactform7.com/docs/

---

## Notes

- All color values use the extracted hex codes from the original site
- Typography sizes match the original exactly
- Layout spacing preserved at 1120px max width
- All content is in Dutch (nl-NL)
- Orange accent color (#EB512F) used consistently for CTAs
- Mobile-first approach for responsive design
- Accessibility standards (WCAG 2.1 AA) maintained throughout
