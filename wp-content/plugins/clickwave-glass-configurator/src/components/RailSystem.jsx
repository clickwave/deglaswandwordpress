import React, { useMemo } from 'react';
import { Shape, ExtrudeGeometry } from 'three';
import { mmToMeters } from '../utils/calculations';
import AluminiumMaterial from './materials/AluminiumMaterial';

export default function RailSystem({ trackCount, width, height, position = [0, 0, 0], frameColor, isTop = false }) {
  const geometry = useMemo(() => {
    // Convert dimensions to meters
    const railWidth = mmToMeters(width);
    const trackWidth = mmToMeters(50); // Each track is 50mm wide
    const trackDepth = mmToMeters(isTop ? 30 : 40); // Top rail shallower than bottom
    const trackSpacing = mmToMeters(2); // 2mm spacing between tracks

    // Create rail profile shape
    const shape = new Shape();

    // Base of the rail
    const totalRailDepth = mmToMeters(isTop ? 40 : 50);
    const baseHeight = mmToMeters(10);

    shape.moveTo(0, 0);
    shape.lineTo(0, baseHeight);

    // Create multiple tracks
    for (let i = 0; i < trackCount; i++) {
      const trackStart = i * (trackWidth + trackSpacing);

      // Track slot
      shape.lineTo(trackStart, baseHeight);
      shape.lineTo(trackStart, baseHeight + trackDepth);
      shape.lineTo(trackStart + trackWidth, baseHeight + trackDepth);
      shape.lineTo(trackStart + trackWidth, baseHeight);
    }

    // Complete the profile
    const totalWidth = trackCount * (trackWidth + trackSpacing) - trackSpacing;
    shape.lineTo(totalWidth, baseHeight);
    shape.lineTo(totalWidth, 0);
    shape.lineTo(0, 0);

    // Extrude the profile along the width
    const extrudeSettings = {
      steps: 1,
      depth: railWidth,
      bevelEnabled: false
    };

    return new ExtrudeGeometry(shape, extrudeSettings);
  }, [trackCount, width, isTop]);

  // Calculate centering offset
  const trackWidth = mmToMeters(50);
  const trackSpacing = mmToMeters(2);
  const totalTrackWidth = trackCount * (trackWidth + trackSpacing) - trackSpacing;
  const offsetX = -totalTrackWidth / 2;

  return (
    <mesh
      geometry={geometry}
      position={[position[0] + offsetX, position[1], position[2] - mmToMeters(width) / 2]}
      rotation={[0, 0, 0]}
    >
      <AluminiumMaterial color={frameColor} />
    </mesh>
  );
}
