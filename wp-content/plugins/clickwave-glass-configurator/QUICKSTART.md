# Quick Start Guide

Get the 3D Glass Configurator running in 2 minutes.

## Prerequisites

- Node.js 16+ installed
- npm or yarn package manager
- Modern web browser

## Installation

```bash
# Navigate to project directory
cd "/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator"

# Install dependencies (already done)
npm install
```

## Development

Start the development server with hot reload:

```bash
npm run dev
```

Open your browser to: **http://localhost:5173**

You should see:
- Sidebar on the left with configuration controls
- 3D view on the right showing glass panels and rails
- Live price updates as you change options

## Making Changes

### Modify Panel Dimensions
Edit `/src/store/useConfigStore.js`:
```javascript
width: 3000,  // Change default width
height: 2200, // Change default height
```

### Adjust Colors
Edit `/src/components/materials/AluminiumMaterial.jsx`:
```javascript
const COLORS = {
  RAL9005: '#0A0A0A', // Change black
  RAL7016: '#383E42'  // Change anthracite
};
```

### Change Pricing
Edit `/src/store/useConfigStore.js`:
```javascript
const PRICES = {
  basePanel: 299.99,  // Adjust base price
  glassGetint: 50,    // Adjust tint price
  // ... etc
};
```

### Modify Rail System
Edit `/src/components/RailSystem.jsx`:
```javascript
const trackWidth = mmToMeters(50);  // Track width
const trackDepth = mmToMeters(40);  // Track depth
const trackSpacing = mmToMeters(2); // Space between tracks
```

## Testing Configurations

Try these scenarios in the UI:

### Test 1: Minimum Configuration
- Width: 1500mm
- Height: 1800mm
- Tracks: 3
- Color: Black
- Glass: Clear
- Design: Standard

Expected price: ~899.97 EUR

### Test 2: Maximum Configuration
- Width: 6000mm
- Height: 3000mm
- Tracks: 6
- Color: Anthracite
- Glass: Tinted
- Design: Steellook (Dublin)
- All options enabled

Expected price: ~4000+ EUR

### Test 3: Drag Interaction
1. Click and hold on a glass panel
2. Drag left/right
3. Panel should follow cursor
4. Release to stop

## Building for Production

```bash
npm run build
```

Output will be in `./assets/js/configurator.js` (995KB)

## Preview Production Build

```bash
npm run preview
```

Opens on: **http://localhost:4173**

## Common Issues

### Issue: Port already in use
```bash
# Kill process on port 5173
lsof -ti:5173 | xargs kill -9

# Or use different port
npm run dev -- --port 3000
```

### Issue: Three.js warnings in console
These are normal development warnings and won't appear in production build.

### Issue: Panels not visible
Check camera position in `Scene.jsx`:
```javascript
position={[mmToMeters(width * 1.5), mmToMeters(height * 0.8), mmToMeters(width * 1.2)]}
```

### Issue: Performance slow
Try reducing panel count or disabling shadows in `Scene.jsx`:
```javascript
<Canvas shadows={false}>
```

## Keyboard Shortcuts (Development)

- **F12**: Open browser DevTools
- **Ctrl + Shift + C**: Inspect 3D elements
- **Ctrl + R**: Reload page

## Project Structure (Quick Reference)

```
src/
├── App.jsx                    ← Main layout
├── components/
│   ├── Scene.jsx              ← 3D scene setup
│   ├── RailSystem.jsx         ← Rail geometry
│   ├── GlassPanel.jsx         ← Panel + frame
│   ├── ConfiguratorUI.jsx     ← Sidebar UI
│   └── materials/             ← PBR materials
├── store/
│   └── useConfigStore.js      ← State + pricing
└── utils/
    └── calculations.js        ← Math functions
```

## Key Technologies

- **React 18**: UI framework
- **React Three Fiber**: React + Three.js integration
- **Three.js**: 3D rendering engine
- **@react-three/drei**: Helper components
- **Zustand**: State management

## Next Steps

1. Read `IMPLEMENTATION.md` for detailed architecture
2. Review `CALCULATIONS.md` for math formulas
3. Check `FILE_MANIFEST.md` for complete file list
4. Customize pricing in `useConfigStore.js`
5. Adjust materials in `materials/` folder
6. Test WordPress integration with built bundle

## WordPress Integration

After building, integrate into WordPress:

```php
// In your WordPress plugin/theme
function enqueue_configurator() {
    wp_enqueue_script(
        'glass-configurator',
        plugins_url('assets/js/configurator.js', __FILE__),
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_configurator');

// Add shortcode
function glass_configurator_shortcode() {
    return '<div id="root" style="width:100%; height:80vh;"></div>';
}
add_shortcode('glass_configurator', 'glass_configurator_shortcode');
```

Use in WordPress:
```
[glass_configurator]
```

## Performance Tips

1. **Reduce geometry complexity**: Lower track counts render faster
2. **Optimize materials**: Reduce roughness/metalness for faster rendering
3. **Limit panel count**: More panels = more draw calls
4. **Use production build**: Development build is slower
5. **Enable GPU acceleration**: Check browser settings

## Debugging

### Enable React DevTools
Install browser extension: "React Developer Tools"

### Enable Three.js Inspector
Add to Scene.jsx:
```javascript
import { useHelper } from '@react-three/drei';
import { CameraHelper } from 'three';

// In component:
useHelper(ref, CameraHelper);
```

### Log State Changes
Add to useConfigStore.js:
```javascript
const useConfigStore = create((set, get) => ({
  // ... state
  setWidth: (width) => {
    console.log('Width changed:', width);
    set({ width });
  }
}));
```

## Getting Help

1. Check documentation files in project root
2. Review console for errors
3. Inspect 3D scene with browser DevTools
4. Verify state in React DevTools
5. Test with different configurations

## Checklist: Ready for Production?

- [ ] All prices are correct
- [ ] All configuration options work
- [ ] Drag interaction functions
- [ ] Build completes without errors
- [ ] No console errors in production
- [ ] Compatible with target browsers
- [ ] Performance is acceptable
- [ ] WordPress integration tested

## Quick Command Reference

```bash
# Development
npm run dev              # Start dev server
npm run build            # Build for production
npm run preview          # Preview production build

# Package management
npm install              # Install dependencies
npm update              # Update dependencies
npm audit fix           # Fix security issues

# Maintenance
npm run build -- --mode development  # Debug build
npm list                # List installed packages
npm outdated            # Check for updates
```

## Support

For questions or issues:
1. Review documentation files
2. Check browser console for errors
3. Verify Node.js and npm versions
4. Clear node_modules and reinstall: `rm -rf node_modules && npm install`

---

**You're ready to go! Run `npm run dev` and start building.**
