# File Manifest - Glass Sliding Wall 3D Configurator

Complete list of all files created for the React Three Fiber configurator.

## Root Directory
```
/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/
```

## Configuration Files

### package.json
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/package.json`
- NPM package configuration
- Dependencies: React, R3F, Three.js, Drei, Zustand
- Scripts: dev, build, preview

### vite.config.js
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/vite.config.js`
- Vite build configuration
- Output: `./assets/js/configurator.js`
- React plugin enabled

### index.html
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/index.html`
- Development entry point
- Root div for React mounting
- Module script loader

### .gitignore
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/.gitignore`
- Git ignore rules
- Excludes: node_modules, build output, IDE files

## Source Files

### Entry Point

#### index.jsx
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/index.jsx`
- React application entry point
- Mounts App component to DOM
- Strict mode enabled

#### App.jsx
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/App.jsx`
- Main application container
- Layout: Sidebar + 3D Canvas
- Flexbox layout

## Components

### Scene.jsx
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/components/Scene.jsx`

**Purpose:** R3F Canvas and 3D scene setup

**Features:**
- Perspective camera positioning
- Orbit controls (constrained rotation)
- Studio environment lighting
- Grid helper for scale reference
- Renders rails and panels

**Dependencies:**
- @react-three/fiber (Canvas)
- @react-three/drei (OrbitControls, Environment, Grid, PerspectiveCamera)
- RailSystem component
- GlassPanel component
- useConfigStore (state)

### RailSystem.jsx
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/components/RailSystem.jsx`

**Purpose:** Parametric multi-track rail generation

**Features:**
- Procedural geometry using Three.js Shape & ExtrudeGeometry
- Adapts to 3-6 tracks
- Separate top/bottom rail configurations
- 50mm track width, 2mm spacing

**Props:**
- `trackCount`: Number of tracks (3-6)
- `width`: System width in mm
- `height`: System height in mm
- `position`: [x, y, z] array
- `frameColor`: RAL9005 or RAL7016
- `isTop`: Boolean for top/bottom variation

**Dependencies:**
- Three.js (Shape, ExtrudeGeometry)
- AluminiumMaterial
- Calculations utilities

### GlassPanel.jsx
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/components/GlassPanel.jsx`

**Purpose:** Individual glass panel with frame and interaction

**Features:**
- Glass pane with frame on all sides
- Drag-to-slide interaction
- Steellook design patterns (optional)
- Handle geometry
- Pointer event handling

**Props:**
- `width`: Panel width in mm
- `height`: Panel height in mm
- `position`: [x, y, z] array
- `glassType`: 'helder' or 'getint'
- `frameColor`: RAL9005 or RAL7016
- `panelIndex`: Index in array
- `totalPanels`: Total panel count
- `design`: 'standard' or 'steellook'
- `steellookType`: 'amsterdam' | 'barcelona' | 'cairo' | 'dublin'

**Steellook Patterns:**
- Amsterdam: 1H + 1V bar
- Barcelona: 2H + 1V bars
- Cairo: 1H + 2V bars
- Dublin: 2H + 2V bars

**Dependencies:**
- @react-three/fiber (useFrame, useThree)
- GlassMaterial
- AluminiumMaterial
- Calculations utilities

### ConfiguratorUI.jsx
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/components/ConfiguratorUI.jsx`

**Purpose:** Configuration sidebar with all options

**Sections:**
1. Afmetingen (Dimensions) - width/height sliders
2. Aantal Rails - track count selector (3-6)
3. Kleur - frame color (RAL9005/RAL7016)
4. Glastype - glass type (helder/getint)
5. Design - standard/steellook with subtypes
6. Extra Opties - checkboxes for addons
7. Handgreep - handle type selector
8. Montage - installation option

**Footer:**
- Live price display
- Order button

**Styling:**
- 350px fixed width
- Scrollable content area
- Dark header, light content
- Button groups with active states

**Dependencies:**
- useConfigStore (all state and actions)
- Calculations utilities (formatPrice)

## Materials

### AluminiumMaterial.jsx
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/components/materials/AluminiumMaterial.jsx`

**Purpose:** PBR aluminum material component

**Features:**
- RAL9005 (Matte Black): #0A0A0A
- RAL7016 (Anthracite): #383E42
- Roughness: 0.7
- Metalness: 0.9
- Environment map intensity: 1.0

**Props:**
- `color`: 'RAL9005' or 'RAL7016'

**Returns:** MeshStandardMaterial

### GlassMaterial.jsx
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/components/materials/GlassMaterial.jsx`

**Purpose:** PBR glass material with transmission

**Features:**
- Helder (clear): White, 98% opacity
- Getint (tinted): Grey, 85% opacity
- Transmission: 0.95
- Roughness: 0.05
- Thickness: 0.01 (10mm)
- IOR: 1.5
- Clearcoat: 0.1

**Props:**
- `glassType`: 'helder' or 'getint'

**Returns:** MeshPhysicalMaterial

## State Management

### useConfigStore.js
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/store/useConfigStore.js`

**Purpose:** Zustand store for application state

**State Properties:**
- Dimensions: width, height
- Configuration: trackCount, frameColor, glassType, design
- Steellook: steellookType
- Options: hasUProfiles, hasFunderingskoker, hasHardhoutenPalen
- Carriers: meeneemersType
- Accessories: hasTochtstrippen, handleType
- Services: hasMontage

**Computed Properties:**
- `panelCount`: Equals trackCount
- `totalPrice`: Calculates full price

**Actions:**
- Setter for each state property
- Cascading updates (e.g., design → steellookType)

**Pricing Logic:**
- Base prices per option
- Per-panel multipliers
- Track-count dependent prices
- System-wide additions

## Utilities

### calculations.js
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/utils/calculations.js`

**Purpose:** Panel calculations and utilities

**Functions:**

#### calculatePanelWidth(totalWidth, panelCount, overlapPerPanel = 25)
Returns individual panel width accounting for overlap.

Formula: `(totalWidth + (overlap × (panelCount - 1))) / panelCount`

#### calculatePanelPositions(totalWidth, panelCount, overlapPerPanel = 25)
Returns array of X positions for panel centers.

Formula: `(i - (panelCount - 1) / 2) × (totalWidth / panelCount)`

#### mmToMeters(mm)
Converts millimeters to meters for Three.js.

Formula: `mm / 1000`

#### formatPrice(price)
Formats number as EUR currency (nl-NL locale).

Returns: "€1.234,56" format

## Build Output

### configurator.js
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/assets/js/configurator.js`

**Size:** 995KB (287KB gzipped)

**Contents:**
- React 18
- React DOM
- Three.js
- React Three Fiber
- @react-three/drei
- Zustand
- All application code

**Usage:**
```javascript
// WordPress enqueue
wp_enqueue_script(
  'glass-configurator',
  plugins_url('assets/js/configurator.js', __FILE__),
  array(),
  '1.0.0',
  true
);
```

## Documentation

### README.md
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/README.md`
- Project overview (modified by user for WordPress integration)
- WordPress integration instructions
- REST API endpoints
- Security features

### IMPLEMENTATION.md
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/IMPLEMENTATION.md`
- Detailed implementation guide
- Component architecture
- Technical decisions
- Performance optimizations
- Future enhancements

### CALCULATIONS.md
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/CALCULATIONS.md`
- Mathematical formulas
- Panel width calculations
- Position calculations
- Rail track dimensions
- Worked examples
- Visual diagrams

### FILE_MANIFEST.md (this file)
**Path:** `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/FILE_MANIFEST.md`
- Complete file listing
- File purposes
- Dependencies
- Props/parameters

## Dependencies Installed

Via npm (node_modules/):
- react@18.2.0
- react-dom@18.2.0
- @react-three/fiber@8.15.0
- @react-three/drei@9.88.0
- three@0.158.0
- zustand@4.4.0
- vite@5.0.0
- @vitejs/plugin-react@4.2.0

Total: 141 packages

## Summary Statistics

**Total Files Created:** 15
- Source files (.jsx/.js): 10
- Configuration files: 4
- Documentation files: 4

**Lines of Code (estimated):**
- Components: ~600 lines
- State/Utils: ~300 lines
- Configuration: ~100 lines
- Total: ~1000 lines

**Bundle Size:**
- Uncompressed: 995KB
- Gzipped: 287KB

## Quick Start Commands

```bash
# Navigate to project
cd "/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator"

# Install dependencies
npm install

# Start dev server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

## Development URLs

- **Dev Server:** http://localhost:5173
- **Preview Server:** http://localhost:4173

## All Files (Tree View)

```
clickwave-glass-configurator/
├── package.json
├── vite.config.js
├── index.html
├── .gitignore
├── README.md
├── IMPLEMENTATION.md
├── CALCULATIONS.md
├── FILE_MANIFEST.md
├── node_modules/ (141 packages)
├── assets/
│   └── js/
│       ├── configurator.js (995KB)
│       └── index.html
└── src/
    ├── index.jsx
    ├── App.jsx
    ├── components/
    │   ├── Scene.jsx
    │   ├── RailSystem.jsx
    │   ├── GlassPanel.jsx
    │   ├── ConfiguratorUI.jsx
    │   └── materials/
    │       ├── AluminiumMaterial.jsx
    │       └── GlassMaterial.jsx
    ├── store/
    │   └── useConfigStore.js
    └── utils/
        └── calculations.js
```

## Status: COMPLETE

All files have been created and are ready for development or production use.

Build tested successfully: ✓
Dependencies installed: ✓
Documentation complete: ✓
