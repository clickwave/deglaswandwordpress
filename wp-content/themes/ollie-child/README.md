# Ollie Child Theme - De Glaswand

Custom FSE child theme for De Glaswand based on the Ollie block theme.

## Overview

This child theme implements a modern, high-performance website for De Glaswand using WordPress Full Site Editing (FSE) without relying on heavy page builders.

## Design Tokens

All design tokens are defined in `theme.json` and sourced from the original deglaswand.nl website:

### Colors
- **Primary**: #1f3d58 (Dark blue for hero sections)
- **Secondary**: #232d34 (Dark blue-grey for alternate sections)
- **Accent**: #ee6b4e (Coral/orange for CTAs)
- **Background**: #f9f9f9, #fafafa, #f2f2f2 (Light backgrounds)
- **Text**: #191919 (Primary text color)

### Typography
- **Heading Font**: Outfit (Google Fonts) - 400, 500, 700 weights
- **Body Font**: Questrial (Google Fonts)
- **Font Sizes**: 12px to 104px with semantic naming

### Spacing
- Content width: 1185px
- Wide width: 1400px
- Spacing scale: 8px to 80px (7 steps)

## File Structure

```
ollie-child/
├── style.css                    # Theme header and metadata
├── functions.php                # Theme functionality and enqueues
├── theme.json                   # Design system and FSE settings
├── README.md                    # This file
├── templates/
│   ├── front-page.html         # Homepage template
│   └── page-configurator.html  # 3D Configurator page template
└── parts/
    ├── header.html             # Sticky header with navigation
    └── footer.html             # Footer with columns
```

## Features

### Core Features
- Full Site Editing (FSE) support
- Google Fonts integration (Outfit + Questrial)
- Responsive design tokens
- Custom color palette
- Custom typography scale
- Custom spacing scale
- Shadow presets

### Templates

#### Front Page Template
- Hero section with dark background overlay
- 4-column feature grid
- Product showcase section
- CTA section with primary button
- Alternating section backgrounds

#### Configurator Page Template
- Minimal layout focused on the 3D app
- Full-width container for React integration
- Shortcode placeholder: `[glass_3d_app]`
- Instructions section below configurator

### Template Parts

#### Header
- Sticky positioning
- Transparent background option
- Logo and site title
- Primary navigation menu
- Constrained content width (1185px)

#### Footer
- 4-column layout
- Products, Service, Contact sections
- Site logo and description
- Legal links
- Social media ready

## Installation

### Prerequisites
1. **Install Ollie Parent Theme**
   ```
   wp theme install ollie --activate
   ```
   Or download from: https://wordpress.org/themes/ollie/

2. **Activate Child Theme**
   ```
   wp theme activate ollie-child
   ```

### Manual Installation
1. Upload the `ollie-child` folder to `/wp-content/themes/`
2. Ensure the Ollie parent theme is installed
3. Activate "Ollie Child - De Glaswand" from WordPress admin

## Usage

### Customizing Colors
Edit colors in `theme.json` under `settings.color.palette`:
```json
{
  "slug": "primary",
  "color": "#1f3d58",
  "name": "Primary"
}
```

### Customizing Typography
Fonts are defined in `theme.json` under `settings.typography.fontFamilies`:
```json
{
  "fontFamily": "Outfit, sans-serif",
  "slug": "heading",
  "name": "Outfit"
}
```

### Customizing Templates
1. Navigate to Appearance > Editor in WordPress admin
2. Select Templates or Template Parts
3. Edit using the Block Editor
4. Changes are saved to the database (not files)

### Using the 3D Configurator
The configurator shortcode `[glass_3d_app]` is registered in `functions.php`.
To integrate the React app:
1. Edit the `ollie_child_glass_3d_shortcode()` function
2. Enqueue your React app scripts and styles
3. Mount the React app to `#glass-3d-configurator-root`

## Development

### WordPress Coding Standards
This theme follows WordPress Coding Standards (WPCS).

### Theme.json Version
Uses theme.json version 3 (WordPress 6.1+)

### Browser Support
- Modern evergreen browsers
- ES6+ JavaScript support required for future React integration

## Roadmap

### Phase 1: Foundation (COMPLETED)
- [x] Child theme structure
- [x] Design token implementation
- [x] Core templates (front-page, configurator)
- [x] Header and footer parts

### Phase 2: Content (NEXT)
- [ ] Add actual images and content
- [ ] Create additional page templates
- [ ] Build navigation menus
- [ ] Add custom blocks if needed

### Phase 3: Integration
- [ ] Integrate React 3D configurator
- [ ] Add WooCommerce support if needed
- [ ] Form integrations
- [ ] Analytics setup

### Phase 4: Optimization
- [ ] Performance optimization
- [ ] Image optimization
- [ ] Caching strategy
- [ ] SEO enhancements

## Support

For theme-related questions or issues:
- Developer: Clickwave
- Website: https://clickwave.nl
- Client: De Glaswand (https://deglaswand.nl)

## License

This theme inherits the license from the Ollie parent theme (GPL v2 or later).

## Credits

- Parent Theme: Ollie by Ollie Team
- Fonts: Outfit and Questrial from Google Fonts
- Design System: Based on deglaswand.nl
