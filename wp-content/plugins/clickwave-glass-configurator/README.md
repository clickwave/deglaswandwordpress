# Clickwave Glass Configurator

A WordPress plugin for a 3D glass sliding wall configurator with integrated quote management system.

## Features

- Custom Post Type for managing quotes (Offertes)
- Secure REST API endpoint for quote submission
- Shortcode for embedding the React 3D configurator
- Automated email notifications (admin & customer)
- Complete quote management in WordPress admin
- Nonce-based security for API requests

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the shortcode `[glass_3d_app]` to any page or post

## Shortcode Usage

```
[glass_3d_app]
```

Optional parameters:
- `height` - Set custom height (default: 80vh)

Example:
```
[glass_3d_app height="100vh"]
```

## REST API Endpoints

### POST `/wp-json/clickwave-glass/v1/quote`

Submit a new quote request.

**Security:** Requires valid nonce in `X-WP-Nonce` header.

## React Integration

The React app will receive configuration via `window.cgcConfig`:

```javascript
{
  restUrl: 'https://example.com/wp-json/clickwave-glass/v1',
  nonce: 'abc123xyz',
  homeUrl: 'https://example.com',
  assetsUrl: 'https://example.com/wp-content/plugins/clickwave-glass-configurator/assets/',
  translations: {
    submitSuccess: 'Uw offerte aanvraag is succesvol verzonden!',
    submitError: 'Er is een fout opgetreden. Probeer het opnieuw.'
  }
}
```

## Security Features

1. **Nonce Verification:** All API requests require a valid WordPress nonce
2. **Input Sanitization:** All user inputs are sanitized using WordPress functions
3. **Email Validation:** Email addresses are validated before processing
4. **Dimension Validation:** Width and height are validated within acceptable ranges
5. **Capability Checks:** Admin functions require proper user capabilities

## License

Proprietary - Copyright 2025 Clickwave
