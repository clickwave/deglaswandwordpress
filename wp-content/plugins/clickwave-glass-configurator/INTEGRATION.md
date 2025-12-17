# Integration Guide for 3D Engineer

## Overview

This WordPress plugin provides the backend infrastructure for the glass configurator. Your React/Three.js app will integrate via the REST API and shortcode.

## Quick Start

1. Your React app will be mounted to: `#glass-configurator-root`
2. Configuration data is available at: `window.cgcConfig`
3. Build output should go to: `/assets/js/configurator.js`

## API Integration

### Accessing Configuration

```typescript
// TypeScript types for the config
interface CGCConfig {
  restUrl: string;          // Base REST API URL
  nonce: string;            // Security nonce
  homeUrl: string;          // WordPress home URL
  assetsUrl: string;        // Plugin assets URL
  translations: {
    submitSuccess: string;
    submitError: string;
  };
}

// Access the config
const config: CGCConfig = window.cgcConfig;
```

### Submitting a Quote

```typescript
interface QuoteData {
  // Dimensions
  width: number;                    // 1000-10000 mm
  height: number;                   // 1000-3000 mm
  trackCount: number;               // 2-6 tracks

  // Design
  frameColor: string;               // e.g., "RAL 9005 (Zwart)"
  glassType: string;                // e.g., "Gehard glas 10mm"
  design: string;                   // "clean" or "steellook"
  steellookType?: string;           // Required if design is "steellook"

  // Options (all optional)
  hasUProfiles?: boolean;
  hasFunderingskoker?: boolean;
  hasHardhoutPalen?: boolean;
  meeneemersType?: string;
  hasTochtstrippen?: boolean;
  handleType: string;
  hasMontage?: boolean;

  // Pricing
  priceEstimate: number;

  // Customer info
  customerName: string;
  customerEmail: string;
  customerPhone?: string;
  customerMessage?: string;
}

async function submitQuote(data: QuoteData) {
  const response = await fetch(`${window.cgcConfig.restUrl}/quote`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': window.cgcConfig.nonce
    },
    body: JSON.stringify(data)
  });

  if (!response.ok) {
    throw new Error('Failed to submit quote');
  }

  return await response.json();
}
```

### Response Format

```typescript
interface QuoteResponse {
  success: boolean;
  offerte_id: number;
  message: string;
  emails_sent: {
    admin: boolean;
    customer: boolean;
  };
}
```

### Error Handling

```typescript
try {
  const result = await submitQuote(quoteData);
  
  if (result.success) {
    // Show success message
    alert(window.cgcConfig.translations.submitSuccess);
  }
} catch (error) {
  // Show error message
  alert(window.cgcConfig.translations.submitError);
  console.error('Quote submission failed:', error);
}
```

### Refreshing Nonce (if needed)

If your app is a long-running SPA, you may need to refresh the nonce:

```typescript
async function refreshNonce() {
  const response = await fetch(`${window.cgcConfig.restUrl}/nonce`);
  const data = await response.json();
  
  // Update the nonce
  window.cgcConfig.nonce = data.nonce;
}

// Refresh every 12 hours
setInterval(refreshNonce, 12 * 60 * 60 * 1000);
```

## Development Setup

### Option 1: Vite Dev Server (Recommended)

When `/assets/js/configurator.js` doesn't exist, the plugin will load from:
`http://localhost:5173/src/main.tsx`

Start your Vite dev server:
```bash
npm run dev
```

### Option 2: Production Build

Build to `/assets/js/configurator.js`:
```bash
npm run build
```

### Vite Config Example

```typescript
// vite.config.ts
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

export default defineConfig({
  plugins: [react()],
  
  // Development server
  server: {
    port: 5173,
    cors: true
  },
  
  // Production build
  build: {
    outDir: path.resolve(__dirname, '../assets/js'),
    emptyOutDir: false,
    lib: {
      entry: path.resolve(__dirname, 'src/main.tsx'),
      name: 'GlassConfigurator',
      fileName: () => 'configurator.js',
      formats: ['iife']
    },
    rollupOptions: {
      output: {
        inlineDynamicImports: true
      }
    }
  }
})
```

## Data Validation

The API will validate:
- Width: 1000-10000 mm
- Height: 1000-3000 mm  
- Track count: 2-6
- Email format
- Required fields

Client-side validation is recommended but not required.

## Testing

### Test the API manually:

```bash
# Get a nonce
curl http://your-site.local/wp-json/clickwave-glass/v1/nonce

# Submit a quote
curl -X POST http://your-site.local/wp-json/clickwave-glass/v1/quote \
  -H "Content-Type: application/json" \
  -H "X-WP-Nonce: YOUR_NONCE_HERE" \
  -d '{
    "width": 5000,
    "height": 2400,
    "trackCount": 4,
    "frameColor": "RAL 9005 (Zwart)",
    "glassType": "Gehard glas 10mm",
    "design": "clean",
    "handleType": "recessed",
    "priceEstimate": 4850.00,
    "customerName": "Test User",
    "customerEmail": "test@example.com"
  }'
```

## Security Notes

1. Always include the nonce in the `X-WP-Nonce` header
2. All inputs are sanitized server-side
3. Email validation is performed server-side
4. No need to escape HTML on client - server handles it

## Support

For questions about the WordPress integration, contact the backend team.
For React/Three.js issues, contact the 3D Engineer team.

## Troubleshooting

**Issue**: "Invalid nonce" error
- Solution: Refresh the page or call the `/nonce` endpoint

**Issue**: Configuration not loading
- Solution: Check that `window.cgcConfig` is defined after page load

**Issue**: CORS errors in development
- Solution: Ensure Vite dev server has `cors: true` in config

**Issue**: Build not loading
- Solution: Verify `/assets/js/configurator.js` exists and is valid JavaScript
