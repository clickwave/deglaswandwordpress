# GLB Models Directory

Deze map bevat de 3D .glb renders voor de glazen schuifwanden.

## Model Naamgeving

De models moeten de volgende naamgevingsconventie volgen:

```
glass-wall-{aantal-rails}-{kleur}-{glas-type}.glb
```

### Voorbeelden:

- `glass-wall-3-black-clear.glb` - 3 rails, zwart frame, helder glas
- `glass-wall-3-anthracite-clear.glb` - 3 rails, antraciet frame, helder glas
- `glass-wall-4-black-clear.glb` - 4 rails, zwart frame, helder glas
- `glass-wall-4-black-tinted.glb` - 4 rails, zwart frame, getint glas
- `glass-wall-5-anthracite-clear.glb` - 5 rails, antraciet frame, helder glas

### Parameters:

**Aantal Rails (trackCount):**
- `2` - 2 rails
- `3` - 3 rails
- `4` - 4 rails
- `5` - 5 rails
- `6` - 6 rails

**Kleur (frameColor):**
- `black` - RAL 9005 Zwart
- `anthracite` - RAL 7016 Antraciet

**Glas Type (glassType):**
- `clear` - Helder glas
- `tinted` - Getint glas

## Fallback Model

Als een specifiek model niet gevonden wordt, zal het systeem terugvallen op:
```
glass-wall-default.glb
```

Dit model moet **altijd** aanwezig zijn als backup.

## Model Requirements

- **Formaat**: .glb (GLTF Binary)
- **Eenheden**: Meters (1 unit = 1 meter)
- **Origin**: Model moet gecentreerd zijn op (0, 0, 0)
- **Schaal**: Model wordt automatisch geschaald naar de juiste afmetingen
- **Optimalisatie**: Gebruik Draco compressie voor kleinere bestandsgrootte

## Model Preparatie

1. **Export vanuit Blender/3D software**:
   - Gebruik GLTF 2.0 (.glb) export
   - Schakel Draco compressie in
   - Export met materialen en textures

2. **Plaats de .glb files** in deze directory (`/public/models/`)

3. **Test** de models door de configurator te gebruiken en verschillende opties te selecteren

## Huidige Status

- ✅ Directory aangemaakt
- ⏳ Wachtend op .glb files van Rick
- ⏳ Default fallback model nodig

## Instructies voor Rick

1. Plaats je .glb renders in deze map
2. Gebruik de juiste naamgeving zoals hierboven beschreven
3. Zorg dat er minimaal een `glass-wall-default.glb` aanwezig is
4. Test de configurator na het uploaden

## Technische Details

De models worden geladen via `@react-three/drei` useGLTF hook en automatisch geschaald naar de door de gebruiker gekozen afmetingen (breedte x hoogte).

Het systeem preload veelgebruikte models voor betere performance.
