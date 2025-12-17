import React, { useMemo, useRef } from 'react';
import { useFrame } from '@react-three/fiber';
import * as THREE from 'three';
import useConfigStore from '../store/useConfigStore';

/**
 * 3D Glass Wall Model Component
 * Clean frameless design with top and bottom rail only
 */
const GlassWallModel = () => {
  const { getActiveWall } = useConfigStore();
  const wall = getActiveWall();
  const groupRef = useRef();

  // Convert mm to Three.js units (1 unit = 1 meter)
  const width = wall.width / 1000;
  const height = wall.height / 1000;
  const panelCount = wall.trackCount;

  // Dimensions
  const overlapMm = 30;
  const overlap = overlapMm / 1000;
  const panelWidth = (width + (overlap * (panelCount - 1))) / panelCount;
  const glassThickness = 0.008; // 8mm glass

  // Rail dimensions - slim profile
  const railHeight = 0.045; // 45mm rail height
  const railDepth = 0.06; // 60mm rail depth
  const trackSpacing = 0.015; // spacing between panels in Z

  // Colors based on selection - More realistic RAL colors
  const frameColor = wall.frameColor === 'RAL9005' ? '#0a0a0a' : '#3d434b'; // RAL9005 Jet Black / RAL7016 Anthracite Grey
  const glassColor = wall.glassType === 'getint' ? '#d4e8e8' : '#f0f8ff';
  const glassOpacity = wall.glassType === 'getint' ? 0.15 : 0.05; // More transparent

  // Create materials
  const railMaterial = useMemo(() => new THREE.MeshStandardMaterial({
    color: frameColor,
    metalness: 0.8,
    roughness: 0.4
  }), [frameColor]);

  const glassMaterial = useMemo(() => new THREE.MeshPhysicalMaterial({
    color: glassColor,
    metalness: 0,
    roughness: 0.02, // Smoother glass
    transmission: 0.96, // More transparent - realistic glass
    thickness: 0.8,
    ior: 1.52, // Standard glass IOR
    transparent: true,
    opacity: glassOpacity,
    envMapIntensity: 1.2,
    clearcoat: 0.3, // More reflective
    clearcoatRoughness: 0.02
  }), [glassColor, glassOpacity]);

  const handleMaterial = useMemo(() => new THREE.MeshStandardMaterial({
    color: '#0A0A0A',
    metalness: 0.9,
    roughness: 0.3
  }), []);

  // Calculate panel positions - panels overlap and stack in Z
  const panelPositions = useMemo(() => {
    const positions = [];
    const startX = -width / 2 + panelWidth / 2;

    for (let i = 0; i < panelCount; i++) {
      // Each panel slightly behind the previous one
      const zOffset = i * trackSpacing;
      positions.push({
        x: startX + (i * (panelWidth - overlap)),
        z: zOffset,
        index: i
      });
    }
    return positions;
  }, [width, panelWidth, panelCount, overlap, trackSpacing]);

  // Glass panel height (between rails)
  const panelHeight = height - railHeight * 2;

  // Subtle animation
  useFrame((state) => {
    if (groupRef.current) {
      groupRef.current.position.y = Math.sin(state.clock.elapsedTime * 0.5) * 0.002;
    }
  });

  return (
    <group ref={groupRef} position={[0, 0, 0]}>
      {/* Bottom Rail - with visible track grooves */}
      <group position={[0, railHeight / 2, 0]}>
        {/* Main rail body */}
        <mesh material={railMaterial} castShadow receiveShadow>
          <boxGeometry args={[width + 0.02, railHeight, railDepth]} />
        </mesh>

        {/* Track grooves - visual detail */}
        {panelPositions.map((pos, i) => (
          <mesh
            key={`track-${i}`}
            position={[0, railHeight * 0.15, pos.z]}
            material={railMaterial}
          >
            <boxGeometry args={[width + 0.02, railHeight * 0.3, trackSpacing * 0.8]} />
          </mesh>
        ))}
      </group>

      {/* Top Rail - cleaner design */}
      <mesh position={[0, height - railHeight / 2, 0]} material={railMaterial} castShadow>
        <boxGeometry args={[width + 0.02, railHeight, railDepth]} />
      </mesh>

      {/* Glass Panels - with rollers */}
      {panelPositions.map((pos, index) => (
        <group key={index} position={[pos.x, railHeight + panelHeight / 2, pos.z]}>
          {/* Glass panel */}
          <mesh material={glassMaterial} castShadow receiveShadow>
            <boxGeometry args={[panelWidth, panelHeight, glassThickness]} />
          </mesh>

          {/* Rollers/wheels at bottom - 4 per panel */}
          {[0.25, 0.75].map((factor, i) => (
            <React.Fragment key={`roller-set-${i}`}>
              <Roller
                position={[-panelWidth * (0.5 - factor), -panelHeight / 2 - 0.01, -0.015]}
                material={railMaterial}
              />
              <Roller
                position={[-panelWidth * (0.5 - factor), -panelHeight / 2 - 0.01, 0.015]}
                material={railMaterial}
              />
            </React.Fragment>
          ))}

          {/* Steellook bars if enabled */}
          {wall.design === 'steellook' && (
            <SteellookBars
              width={panelWidth}
              height={panelHeight}
              type={wall.steellookType}
              material={railMaterial}
              glassThickness={glassThickness}
            />
          )}

          {/* Handle only on first panel */}
          {index === 0 && (
            <Handle
              position={[-panelWidth / 2 + 0.08, 0, glassThickness / 2 + 0.012]}
              type={wall.handleType}
              material={handleMaterial}
            />
          )}
        </group>
      ))}
    </group>
  );
};

/**
 * Steellook bars component
 */
const SteellookBars = ({ width, height, type, material, glassThickness }) => {
  const barThickness = 0.015;

  const config = useMemo(() => {
    switch (type) {
      case 'amsterdam':
        return { horizontal: 1, vertical: 0 };
      case 'barcelona':
        return { horizontal: 2, vertical: 0 };
      case 'cairo':
        return { horizontal: 3, vertical: 0 };
      case 'dublin':
        return { horizontal: 2, vertical: 1 };
      default:
        return { horizontal: 0, vertical: 0 };
    }
  }, [type]);

  return (
    <group position={[0, 0, glassThickness / 2 + barThickness / 2]}>
      {/* Horizontal bars */}
      {Array.from({ length: config.horizontal }).map((_, i) => {
        const yPos = ((i + 1) / (config.horizontal + 1)) * height - height / 2;
        return (
          <mesh key={`h-${i}`} position={[0, yPos, 0]} material={material} castShadow>
            <boxGeometry args={[width - 0.02, barThickness, barThickness]} />
          </mesh>
        );
      })}

      {/* Vertical bars */}
      {Array.from({ length: config.vertical }).map((_, i) => {
        const xPos = ((i + 1) / (config.vertical + 1)) * width - width / 2;
        return (
          <mesh key={`v-${i}`} position={[xPos, 0, 0]} material={material} castShadow>
            <boxGeometry args={[barThickness, height - 0.02, barThickness]} />
          </mesh>
        );
      })}
    </group>
  );
};

/**
 * Roller/Wheel Component - visible wheels under each panel
 */
const Roller = ({ position, material }) => {
  return (
    <group position={position}>
      {/* Wheel */}
      <mesh rotation={[Math.PI / 2, 0, 0]} material={material} castShadow>
        <cylinderGeometry args={[0.008, 0.008, 0.006, 16]} />
      </mesh>
    </group>
  );
};

/**
 * Handle Component - round or rectangular
 */
const Handle = ({ position, type, material }) => {
  if (type === 'rond') {
    // Round handle - circular disc style
    return (
      <group position={position}>
        {/* Main disc */}
        <mesh rotation={[0, 0, Math.PI / 2]} material={material} castShadow>
          <cylinderGeometry args={[0.025, 0.025, 0.012, 32]} />
        </mesh>
        {/* Center grip */}
        <mesh rotation={[0, 0, Math.PI / 2]} material={material} castShadow>
          <cylinderGeometry args={[0.008, 0.008, 0.018, 16]} />
        </mesh>
      </group>
    );
  }

  // Rectangular handle - vertical bar
  return (
    <group position={position}>
      <mesh material={material} castShadow>
        <boxGeometry args={[0.012, 0.15, 0.012]} />
      </mesh>
      {/* Top and bottom caps */}
      <mesh position={[0, 0.08, 0]} material={material} castShadow>
        <sphereGeometry args={[0.008, 16, 16]} />
      </mesh>
      <mesh position={[0, -0.08, 0]} material={material} castShadow>
        <sphereGeometry args={[0.008, 16, 16]} />
      </mesh>
    </group>
  );
};

export default GlassWallModel;
