# Quick Reference Card

## Plugin Activation
1. Go to WordPress Admin > Plugins
2. Find "Clickwave Glass Configurator"
3. Click "Activate"

## Add Configurator to Page
```
[glass_3d_app]
```

## API Endpoints
```
POST /wp-json/clickwave-glass/v1/quote
GET  /wp-json/clickwave-glass/v1/nonce
```

## JavaScript Integration
```javascript
// Access config
const config = window.cgcConfig;

// Submit quote
fetch(`${config.restUrl}/quote`, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-WP-Nonce': config.nonce
  },
  body: JSON.stringify(quoteData)
});
```

## Required Quote Fields
```javascript
{
  width: number,              // 1000-10000 mm
  height: number,             // 1000-3000 mm
  trackCount: number,         // 2-6
  frameColor: string,
  glassType: string,
  design: string,
  handleType: string,
  priceEstimate: number,
  customerName: string,
  customerEmail: string
}
```

## View Quotes
WordPress Admin > Glaswand Offertes

## File Locations
- **Main Plugin:** `/wp-content/plugins/clickwave-glass-configurator/`
- **Build Output:** `/wp-content/plugins/clickwave-glass-configurator/assets/js/configurator.js`
- **React Source:** `/wp-content/plugins/clickwave-glass-configurator/src/`

## Development Mode
Start Vite dev server on port 5173:
```bash
cd src/
npm run dev
```
Plugin will auto-load from: `http://localhost:5173/src/main.tsx`

## Production Build
```bash
cd src/
npm run build
```
Outputs to: `../assets/js/configurator.js`

## Security
- Nonce required in X-WP-Nonce header
- All inputs sanitized server-side
- Email validation enforced
- Dimension ranges validated

## Support
- Backend/WordPress: Backend team
- React/Three.js: 3D Engineer team
- Full docs: See INTEGRATION.md
