# Panel Width and Positioning Calculations

## Overview

The glass sliding wall system requires careful calculation of panel widths and positions to ensure:
1. Panels overlap correctly when closed
2. Total width matches specified dimensions
3. Panels distribute evenly across the rail system

## Panel Width Formula

```
panelWidth = (totalWidth + (overlap × (panelCount - 1))) / panelCount
```

### Variables:
- `totalWidth`: Total width of the system in mm
- `panelCount`: Number of panels (equals track count)
- `overlap`: Amount each panel overlaps with the next (25mm standard)
- `panelWidth`: Width of each individual panel

### Example Calculations:

#### 3-Panel System (3000mm total width)
```
panelWidth = (3000 + (25 × (3 - 1))) / 3
panelWidth = (3000 + 50) / 3
panelWidth = 3050 / 3
panelWidth = 1016.67mm
```

**Verification:**
- 3 panels closed: 1016.67 × 3 = 3050mm
- Minus overlap: 3050 - (25 × 2) = 3000mm ✓

#### 4-Panel System (4000mm total width)
```
panelWidth = (4000 + (25 × (4 - 1))) / 4
panelWidth = (4000 + 75) / 4
panelWidth = 4075 / 4
panelWidth = 1018.75mm
```

**Verification:**
- 4 panels closed: 1018.75 × 4 = 4075mm
- Minus overlap: 4075 - (25 × 3) = 4000mm ✓

#### 5-Panel System (5000mm total width)
```
panelWidth = (5000 + (25 × (5 - 1))) / 5
panelWidth = (5000 + 100) / 5
panelWidth = 5100 / 5
panelWidth = 1020mm
```

**Verification:**
- 5 panels closed: 1020 × 5 = 5100mm
- Minus overlap: 5100 - (25 × 4) = 5000mm ✓

## Panel Positioning Formula

Panels are positioned evenly across the total width:

```
position[i] = (i - (panelCount - 1) / 2) × (totalWidth / panelCount)
```

### Variables:
- `i`: Panel index (0 to panelCount - 1)
- `position[i]`: X-coordinate of panel center in mm
- Center of system is at X = 0

### Example: 3-Panel System (3000mm width)

```
spacing = 3000 / 3 = 1000mm

position[0] = (0 - (3-1)/2) × 1000 = -1 × 1000 = -1000mm (left)
position[1] = (1 - (3-1)/2) × 1000 =  0 × 1000 =     0mm (center)
position[2] = (2 - (3-1)/2) × 1000 =  1 × 1000 =  1000mm (right)
```

**Visual:**
```
        -1500         -500          500          1500
          |             |            |             |
    ------+------+------+------+-----+------+------+------
          |      |      |      |     |      |      |
        Panel 0  |    Panel 1  |   Panel 2  |
      (1016.67mm)|  (1016.67mm)|  (1016.67mm)
                 |             |
          <----- 25mm overlap ---->
```

### Example: 4-Panel System (4000mm width)

```
spacing = 4000 / 4 = 1000mm

position[0] = (0 - 1.5) × 1000 = -1500mm
position[1] = (1 - 1.5) × 1000 =  -500mm
position[2] = (2 - 1.5) × 1000 =   500mm
position[3] = (3 - 1.5) × 1000 =  1500mm
```

## Rail Track Calculations

Each panel requires one track in the rail system.

### Track Dimensions:
- Track width: 50mm
- Track spacing: 2mm
- Track depth: 40mm (bottom) / 30mm (top)

### Total Rail Width:

```
railWidth = (trackWidth × trackCount) + (spacing × (trackCount - 1))
railWidth = (50 × trackCount) + (2 × (trackCount - 1))
```

#### Examples:
```
3 tracks: (50 × 3) + (2 × 2) = 150 + 4  = 154mm
4 tracks: (50 × 4) + (2 × 3) = 200 + 6  = 206mm
5 tracks: (50 × 5) + (2 × 4) = 250 + 8  = 258mm
6 tracks: (50 × 6) + (2 × 5) = 300 + 10 = 310mm
```

## Frame Dimensions

Each panel has an aluminum frame around the glass:

- Frame thickness: 50mm (all sides)
- Glass area: `(panelWidth - 100) × (panelHeight - 100)`

### Example: 1020mm × 2200mm Panel
```
Glass dimensions:
Width:  1020 - 100 = 920mm
Height: 2200 - 100 = 2100mm
```

## Overlap Behavior

When panels are closed (stacked):

```
Panel 1: Position X - panelWidth/2 to X + panelWidth/2
Panel 2: Position X - panelWidth/2 to X + panelWidth/2
Overlap: 25mm at the edges
```

**Closed Configuration:**
```
|<---- Panel 1 ---->|
                |<---- Panel 2 ---->|
                |<-25mm->|
                (overlap)
```

**Open Configuration:**
Panels can slide along their respective tracks, maintaining parallelism.

## Unit Conversions

Three.js uses meters, so all calculations need conversion:

```javascript
meters = millimeters / 1000

// Examples:
3000mm → 3.0m
1016.67mm → 1.01667m
50mm → 0.05m
```

## Pricing Based on Configuration

Base calculation:
```
price = (basePrice × panelCount) + options
```

### Panel Count Impact:
```
3 panels: 3 × 299.99 = 899.97 EUR (base)
4 panels: 4 × 299.99 = 1199.96 EUR (base)
5 panels: 5 × 299.99 = 1499.95 EUR (base)
6 panels: 6 × 299.99 = 1799.94 EUR (base)
```

### Per-Panel Options:
- Tinted glass: +50 EUR per panel
- Amsterdam steellook: +99.99 EUR per panel
- Barcelona steellook: +169.99 EUR per panel
- Cairo steellook: +169.99 EUR per panel
- Dublin steellook: +199.99 EUR per panel

### System-Wide Options:
- U-profiles: 149.99-299.99 EUR (by track count)
- Funderingskoker: 199.99 EUR
- Hardwood poles: +149.99 EUR
- Draft strips: 89.99-149.99 EUR (by track count)
- Round handle: +49.99 EUR
- Installation: +899 EUR

## Mathematical Proofs

### Proof: Panel Width Formula

Given:
- `n` panels
- Total width `W`
- Overlap `o` between adjacent panels

When closed, panels overlap:
```
Total visible width = (n × panelWidth) - ((n-1) × o)

Set equal to W:
W = (n × panelWidth) - ((n-1) × o)

Solve for panelWidth:
W + ((n-1) × o) = n × panelWidth
panelWidth = (W + ((n-1) × o)) / n
```

### Proof: Centering Formula

To center `n` panels around origin (X=0):

Average position should be 0:
```
sum(positions) / n = 0

Using: position[i] = (i - offset) × spacing

sum((i - offset) × spacing) = 0
spacing × sum(i - offset) = 0
sum(i) - (n × offset) = 0

sum(i) from 0 to n-1 = n(n-1)/2

n(n-1)/2 - n × offset = 0
offset = (n-1)/2
```

Therefore: `position[i] = (i - (n-1)/2) × spacing` centers the system.

## Implementation Notes

1. **Precision**: Use floating-point for panel widths (allow fractions of mm)
2. **Rounding**: Display rounded values in UI, use precise values in calculations
3. **Boundaries**: Ensure panels don't extend beyond rail system
4. **Validation**: Check minimum/maximum widths are practical for manufacturing

## Visual Reference

```
TOP VIEW - 3 Panel System (3000mm total width)

Rail System (154mm wide)
╔═══════════════════════════════════════════════════════════╗
║                                                           ║
║  Track 1    Track 2    Track 3                          ║
║  [====]    [====]    [====]                             ║
║                                                           ║
╚═══════════════════════════════════════════════════════════╝
    │           │           │
    └───────────┴───────────┴─── Glass Panels

Panel Positions:
[-1000mm]     [0mm]      [+1000mm]
    │           │            │
    ▼           ▼            ▼
┌─────────┐ ┌─────────┐ ┌─────────┐
│ Panel 1 │ │ Panel 2 │ │ Panel 3 │
│1016.67mm│ │1016.67mm│ │1016.67mm│
└─────────┘ └─────────┘ └─────────┘
     └─25mm overlap─┘
```

## Code Reference

See implementation in:
- `/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/src/utils/calculations.js`
