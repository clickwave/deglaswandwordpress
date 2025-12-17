import React from 'react';
import { Canvas } from '@react-three/fiber';
import { OrbitControls, Environment, Grid, PerspectiveCamera } from '@react-three/drei';
import useConfigStore from '../store/useConfigStore';
import RailSystem from './RailSystem';
import GlassPanel from './GlassPanel';
import { calculatePanelWidth, calculatePanelPositions, mmToMeters } from '../utils/calculations';

function SceneContent() {
  const {
    width,
    height,
    trackCount,
    frameColor,
    glassType,
    design,
    steellookType,
    panelCount
  } = useConfigStore();

  // Calculate panel dimensions and positions
  const panelWidth = calculatePanelWidth(width, panelCount, 25);
  const positions = calculatePanelPositions(width, panelCount, 25);

  return (
    <>
      {/* Camera */}
      <PerspectiveCamera
        makeDefault
        position={[mmToMeters(width * 1.5), mmToMeters(height * 0.8), mmToMeters(width * 1.2)]}
        fov={50}
      />

      {/* Controls */}
      <OrbitControls
        target={[0, mmToMeters(height / 2), 0]}
        minPolarAngle={Math.PI / 4}
        maxPolarAngle={Math.PI / 2}
        enablePan={true}
        enableZoom={true}
        enableRotate={true}
      />

      {/* Lighting */}
      <ambientLight intensity={0.5} />
      <directionalLight position={[5, 10, 5]} intensity={1} castShadow />
      <directionalLight position={[-5, 5, -5]} intensity={0.5} />
      <Environment preset="studio" />

      {/* Grid helper */}
      <Grid
        args={[mmToMeters(10000), mmToMeters(10000)]}
        cellSize={mmToMeters(500)}
        cellThickness={0.5}
        cellColor="#6b7280"
        sectionSize={mmToMeters(1000)}
        sectionThickness={1}
        sectionColor="#374151"
        fadeDistance={mmToMeters(15000)}
        fadeStrength={1}
        followCamera={false}
      />

      {/* Bottom Rail */}
      <RailSystem
        trackCount={trackCount}
        width={width}
        height={height}
        position={[0, 0, 0]}
        frameColor={frameColor}
        isTop={false}
      />

      {/* Top Rail */}
      <RailSystem
        trackCount={trackCount}
        width={width}
        height={height}
        position={[0, mmToMeters(height), 0]}
        frameColor={frameColor}
        isTop={true}
      />

      {/* Glass Panels */}
      {positions.map((xPos, index) => (
        <GlassPanel
          key={index}
          width={panelWidth}
          height={height}
          position={[mmToMeters(xPos), mmToMeters(height / 2), 0]}
          glassType={glassType}
          frameColor={frameColor}
          panelIndex={index}
          totalPanels={panelCount}
          design={design}
          steellookType={steellookType}
        />
      ))}
    </>
  );
}

export default function Scene() {
  return (
    <Canvas
      shadows
      dpr={[1, 2]}
      gl={{ antialias: true, alpha: false }}
      style={{ background: '#f3f4f6' }}
    >
      <SceneContent />
    </Canvas>
  );
}
