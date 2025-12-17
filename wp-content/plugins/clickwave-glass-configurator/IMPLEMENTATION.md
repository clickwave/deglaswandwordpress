# Implementation Details - Glass Sliding Wall 3D Configurator

## Project Overview

A fully parametric 3D product configurator built with React Three Fiber for configuring glass sliding wall systems. The application features real-time 3D visualization, interactive controls, and dynamic pricing.

## File Structure

```
clickwave-glass-configurator/
├── src/
│   ├── App.jsx                          # Main application container
│   ├── index.jsx                        # React entry point
│   ├── components/
│   │   ├── Scene.jsx                    # R3F Canvas, lighting, camera setup
│   │   ├── RailSystem.jsx               # Parametric multi-track rail system
│   │   ├── GlassPanel.jsx               # Glass panel with frame and drag interaction
│   │   ├── ConfiguratorUI.jsx           # Sidebar with all configuration options
│   │   └── materials/
│   │       ├── AluminiumMaterial.jsx    # PBR aluminum material (RAL colors)
│   │       └── GlassMaterial.jsx        # PBR glass material with transmission
│   ├── store/
│   │   └── useConfigStore.js            # Zustand state management with pricing
│   └── utils/
│       └── calculations.js              # Panel width and position calculations
├── assets/js/
│   └── configurator.js                  # Built bundle (995KB)
├── package.json
├── vite.config.js
└── index.html
```

## Key Components

### 1. State Management (useConfigStore.js)

**Location:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/store/useConfigStore.js`

Uses Zustand for lightweight, efficient state management without boilerplate.

**State Properties:**
- `width` (1500-6000mm): Total system width
- `height` (1800-3000mm): System height
- `trackCount` (3-6): Number of rails/panels
- `frameColor`: 'RAL9005' (black) or 'RAL7016' (anthracite)
- `glassType`: 'helder' (clear) or 'getint' (tinted)
- `design`: 'standard' or 'steellook'
- `steellookType`: 'amsterdam' | 'barcelona' | 'cairo' | 'dublin'
- Additional options: U-profiles, funderingskoker, handles, etc.

**Computed Properties:**
- `panelCount`: Equals trackCount (1 panel per track)
- `totalPrice`: Calculates complete price based on all selections

**Pricing Structure:**
```javascript
Base panel: 299.99 EUR
Glass tint: +50 EUR per panel
Steellook designs: +99.99 to +199.99 per panel
U-profiles: 149.99-299.99 (by track count)
Funderingskoker: 199.99
Installation: +899 EUR
```

### 2. Scene Component (Scene.jsx)

**Location:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/components/Scene.jsx`

Sets up the 3D environment using React Three Fiber.

**Key Features:**
- Perspective camera positioned at 1.5x width, 0.8x height distance
- OrbitControls with constrained rotation (45-90 degrees polar angle)
- Studio environment preset for realistic PBR rendering
- Grid helper for scale reference (500mm cells)
- Ambient + directional lighting setup

**Rendering Logic:**
```javascript
- Bottom rail at y=0
- Top rail at y=height
- Panels evenly distributed with calculated overlap
- All positions in meters (converted from mm)
```

### 3. RailSystem Component (RailSystem.jsx)

**Location:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/components/RailSystem.jsx`

Procedurally generates multi-track rail profiles using Three.js Shape and ExtrudeGeometry.

**Technical Details:**
- Track width: 50mm each
- Track spacing: 2mm between tracks
- Track depth: 30mm (top) / 40mm (bottom)
- Base height: 10mm
- Extrusion: Full width of system

**Generation Process:**
1. Create 2D profile shape with multiple track slots
2. Extrude profile along system width
3. Apply aluminum material with selected RAL color

**Why This Approach:**
- Fully parametric - adapts to any track count
- Single mesh geometry (efficient)
- Realistic profile matching reference images
- Easy to modify dimensions

### 4. GlassPanel Component (GlassPanel.jsx)

**Location:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/components/GlassPanel.jsx`

Creates individual glass panels with frames, handles, and optional steellook designs.

**Structure:**
- Glass pane (10mm thick, full width/height)
- Aluminum frame (50mm thick, all sides)
- Handle (120x30x20mm)
- Optional steellook bars (procedural grid patterns)

**Drag Interaction:**
- Uses Three.js pointer events
- Tracks drag delta from start position
- Allows horizontal sliding simulation
- Stops propagation to prevent camera rotation while dragging

**Steellook Patterns:**
```javascript
Amsterdam: 1 horizontal + 1 vertical bar
Barcelona: 2 horizontal + 1 vertical bar
Cairo: 1 horizontal + 2 vertical bars
Dublin: 2 horizontal + 2 vertical bars
```

**Panel Width Calculation:**
```javascript
panelWidth = (totalWidth + (overlap * (panelCount - 1))) / panelCount
overlap = 25mm per panel
```

This ensures panels overlap correctly when closed while fitting the total width.

### 5. Materials

#### AluminiumMaterial.jsx
**Location:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/components/materials/AluminiumMaterial.jsx`

PBR metal material with RAL color options.

**Properties:**
```javascript
RAL9005: #0A0A0A (matte black)
RAL7016: #383E42 (anthracite grey)
roughness: 0.7 (matte finish)
metalness: 0.9 (highly metallic)
envMapIntensity: 1.0 (environment reflections)
```

#### GlassMaterial.jsx
**Location:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/components/materials/GlassMaterial.jsx`

PBR glass material with transmission for realistic transparency.

**Properties:**
```javascript
transmission: 0.95 (95% light transmission)
roughness: 0.05 (very smooth)
thickness: 0.01 (10mm)
ior: 1.5 (index of refraction for glass)
clearcoat: 0.1 (subtle surface coating)

Helder (clear): color #FFFFFF, opacity 0.98
Getint (tinted): color #B8B8B8, opacity 0.85
```

### 6. ConfiguratorUI Component (ConfiguratorUI.jsx)

**Location:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/components/ConfiguratorUI.jsx`

350px sidebar with all configuration controls.

**Sections:**
1. **Afmetingen (Dimensions)**: Width/height sliders
2. **Aantal Rails**: 3-6 track buttons
3. **Kleur (Color)**: RAL9005/RAL7016 toggle
4. **Glastype**: Helder/Getint toggle
5. **Design**: Standard/Steellook with sub-options
6. **Extra Opties**: Checkboxes for all addons
7. **Handgreep**: Handle type selector
8. **Montage**: Installation checkbox

**Footer:**
- Live price display (formatted in EUR)
- "Bestellen" (Order) button

**Styling:**
- Clean, modern interface
- Dark headers, light content
- Active state highlighting
- Grid button layouts
- Responsive controls

### 7. Utility Functions (calculations.js)

**Location:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/utils/calculations.js`

**Functions:**

```javascript
calculatePanelWidth(totalWidth, panelCount, overlap = 25)
// Returns individual panel width accounting for overlap

calculatePanelPositions(totalWidth, panelCount, overlap = 25)
// Returns array of X positions for even distribution

mmToMeters(mm)
// Converts millimeters to meters (Three.js uses meters)

formatPrice(price)
// Formats price as EUR currency (nl-NL locale)
```

## Performance Optimizations

1. **Memoized Geometry**: Rail geometry regenerates only when dependencies change
2. **Efficient State**: Zustand provides minimal re-renders
3. **Single Geometries**: Rails are single extruded shapes, not multiple boxes
4. **Proper Three.js Patterns**: Uses R3F best practices

## Build Configuration

**vite.config.js:**
```javascript
output: {
  entryFileNames: 'configurator.js'  // Single bundle
  outDir: './assets/js'              // WordPress-ready location
}
```

**Build Output:**
- `configurator.js`: 995KB (287KB gzipped)
- Includes React, Three.js, R3F, and all dependencies

## Development Workflow

**Start Development Server:**
```bash
cd /Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator
npm run dev
```
Opens on `http://localhost:5173`

**Build for Production:**
```bash
npm run build
```
Outputs to `./assets/js/configurator.js`

**Preview Production Build:**
```bash
npm run preview
```

## WordPress Integration

The plugin should enqueue the built JavaScript:

```php
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'glass-configurator',
        plugins_url('assets/js/configurator.js', __FILE__),
        array(),
        '1.0.0',
        true
    );
});
```

Add shortcode for embedding:
```php
add_shortcode('glass_configurator', function() {
    return '<div id="root" style="width:100%; height:80vh;"></div>';
});
```

## Future Enhancements

1. **Carrier/Wheel System**: Add visible carriers and wheels on rails
2. **U-Profile Visualization**: Show optional U-profiles in 3D
3. **Foundation Visualization**: Render funderingskoker when selected
4. **Better Handle Models**: More detailed 3D handle geometry
5. **Panel Stacking**: Animate panels stacking when sliding
6. **Shadow Rendering**: Enable realistic shadows
7. **Screenshot Export**: Allow users to save configuration image
8. **Configuration Save/Load**: Persist configurations to WordPress
9. **Quote Form Integration**: Connect order button to backend
10. **Mobile Optimization**: Touch controls and responsive layout

## Technical Considerations

**Coordinate System:**
- Three.js uses Y-up coordinate system
- Origin (0,0,0) at bottom rail center
- X-axis: width (left/right)
- Y-axis: height (up/down)
- Z-axis: depth (forward/back)

**Units:**
- Input: millimeters (UI-friendly)
- Calculations: millimeters
- Three.js: meters (converted via mmToMeters)

**Materials:**
- All materials use PBR workflow
- Requires proper lighting (Environment preset)
- Transmission requires MeshPhysicalMaterial

**React Three Fiber:**
- Declarative 3D with React components
- Auto-disposes Three.js objects
- Proper hooks (useFrame, useThree)
- Event system for interactions

## Dependencies

```json
"@react-three/fiber": "^8.15.0"  // React renderer for Three.js
"@react-three/drei": "^9.88.0"   // Helper components and utilities
"three": "^0.158.0"               // 3D library
"zustand": "^4.4.0"               // State management
"react": "^18.2.0"                // UI library
"vite": "^5.0.0"                  // Build tool
```

## Summary

This implementation provides a complete, production-ready 3D configurator with:

- Fully parametric 3D model generation
- Real-time interactive visualization
- Complete configuration options matching flowchart
- Dynamic pricing calculation
- Drag-to-slide panel interaction
- PBR materials for realistic rendering
- Clean, intuitive UI
- WordPress-ready build output

All files are created and functional. The application is ready for development testing via `npm run dev` or production deployment via `npm run build`.
