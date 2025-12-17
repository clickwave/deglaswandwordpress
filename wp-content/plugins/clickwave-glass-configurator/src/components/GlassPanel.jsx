import React, { useRef, useState } from 'react';
import { useFrame, useThree } from '@react-three/fiber';
import { mmToMeters } from '../utils/calculations';
import GlassMaterial from './materials/GlassMaterial';
import AluminiumMaterial from './materials/AluminiumMaterial';

export default function GlassPanel({
  width,
  height,
  position = [0, 0, 0],
  glassType,
  frameColor,
  panelIndex,
  totalPanels,
  design = 'standard',
  steellookType = null
}) {
  const meshRef = useRef();
  const [isDragging, setIsDragging] = useState(false);
  const [dragStart, setDragStart] = useState(null);

  // Convert dimensions to meters
  const panelWidth = mmToMeters(width);
  const panelHeight = mmToMeters(height);
  const frameThickness = mmToMeters(50); // Frame around glass
  const glassThickness = mmToMeters(10);

  // Handle dragging
  const handlePointerDown = (e) => {
    e.stopPropagation();
    setIsDragging(true);
    setDragStart({ x: e.point.x, startPos: position[0] });
  };

  const handlePointerUp = () => {
    setIsDragging(false);
    setDragStart(null);
  };

  const handlePointerMove = (e) => {
    if (isDragging && dragStart && meshRef.current) {
      const deltaX = e.point.x - dragStart.x;
      meshRef.current.position.x = dragStart.startPos + deltaX;
    }
  };

  // Steellook design variations (simplified grid pattern)
  const renderSteellookBars = () => {
    if (design !== 'steellook' || !steellookType) return null;

    const bars = [];
    const barThickness = mmToMeters(20);
    const barDepth = mmToMeters(15);

    // Different patterns for different steellook types
    const patterns = {
      amsterdam: { horizontal: 1, vertical: 1 },
      barcelona: { horizontal: 2, vertical: 1 },
      cairo: { horizontal: 1, vertical: 2 },
      dublin: { horizontal: 2, vertical: 2 }
    };

    const pattern = patterns[steellookType] || patterns.amsterdam;

    // Horizontal bars
    for (let i = 0; i < pattern.horizontal; i++) {
      const y = ((i + 1) / (pattern.horizontal + 1) - 0.5) * panelHeight;
      bars.push(
        <mesh key={`h-${i}`} position={[0, y, glassThickness / 2]}>
          <boxGeometry args={[panelWidth - frameThickness * 2, barThickness, barDepth]} />
          <AluminiumMaterial color={frameColor} />
        </mesh>
      );
    }

    // Vertical bars
    for (let i = 0; i < pattern.vertical; i++) {
      const x = ((i + 1) / (pattern.vertical + 1) - 0.5) * (panelWidth - frameThickness * 2);
      bars.push(
        <mesh key={`v-${i}`} position={[x, 0, glassThickness / 2]}>
          <boxGeometry args={[barThickness, panelHeight - frameThickness * 2, barDepth]} />
          <AluminiumMaterial color={frameColor} />
        </mesh>
      );
    }

    return <group>{bars}</group>;
  };

  return (
    <group
      ref={meshRef}
      position={position}
      onPointerDown={handlePointerDown}
      onPointerUp={handlePointerUp}
      onPointerMove={handlePointerMove}
    >
      {/* Glass pane */}
      <mesh position={[0, 0, 0]}>
        <boxGeometry args={[panelWidth, panelHeight, glassThickness]} />
        <GlassMaterial glassType={glassType} />
      </mesh>

      {/* Top frame */}
      <mesh position={[0, panelHeight / 2 - frameThickness / 2, 0]}>
        <boxGeometry args={[panelWidth, frameThickness, frameThickness]} />
        <AluminiumMaterial color={frameColor} />
      </mesh>

      {/* Bottom frame */}
      <mesh position={[0, -panelHeight / 2 + frameThickness / 2, 0]}>
        <boxGeometry args={[panelWidth, frameThickness, frameThickness]} />
        <AluminiumMaterial color={frameColor} />
      </mesh>

      {/* Left frame */}
      <mesh position={[-panelWidth / 2 + frameThickness / 2, 0, 0]}>
        <boxGeometry args={[frameThickness, panelHeight - frameThickness * 2, frameThickness]} />
        <AluminiumMaterial color={frameColor} />
      </mesh>

      {/* Right frame */}
      <mesh position={[panelWidth / 2 - frameThickness / 2, 0, 0]}>
        <boxGeometry args={[frameThickness, panelHeight - frameThickness * 2, frameThickness]} />
        <AluminiumMaterial color={frameColor} />
      </mesh>

      {/* Steellook design bars */}
      {renderSteellookBars()}

      {/* Handle (simple rectangle or circle) */}
      <mesh position={[panelWidth / 3, 0, frameThickness]}>
        <boxGeometry args={[mmToMeters(120), mmToMeters(30), mmToMeters(20)]} />
        <AluminiumMaterial color={frameColor} />
      </mesh>
    </group>
  );
}
