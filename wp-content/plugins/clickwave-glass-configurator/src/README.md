# React Source Directory

This directory is for the 3D Engineer to place the React/Three.js source code.

## Expected Structure

```
src/
├── main.tsx              # Entry point
├── App.tsx               # Main app component
├── components/
│   ├── Canvas3D.tsx      # Three.js canvas
│   ├── Sidebar.tsx       # Configuration sidebar
│   └── QuoteForm.tsx     # Quote submission form
├── hooks/
│   └── useConfigurator.ts # Configuration state
└── utils/
    └── api.ts            # API helpers for WordPress REST
```

## Vite Configuration

The Vite build should output to:
- `/assets/js/configurator.js`

Example `vite.config.ts`:

```typescript
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: '../assets/js',
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

## API Integration

Access WordPress config via:

```typescript
declare global {
  interface Window {
    cgcConfig: {
      restUrl: string;
      nonce: string;
      homeUrl: string;
      assetsUrl: string;
      translations: {
        submitSuccess: string;
        submitError: string;
      };
    };
  }
}

// Use in API calls
fetch(`${window.cgcConfig.restUrl}/quote`, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-WP-Nonce': window.cgcConfig.nonce
  },
  body: JSON.stringify(quoteData)
})
```

## Mount Point

The React app should mount to:

```typescript
const root = ReactDOM.createRoot(
  document.getElementById('glass-configurator-root')!
);
root.render(<App />);
```
