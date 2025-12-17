import React, { useEffect, useState, Suspense } from 'react';
import { Canvas } from '@react-three/fiber';
import { OrbitControls, Environment, ContactShadows } from '@react-three/drei';
import useConfigStore from './store/useConfigStore';
import ConfiguratorPanel from './components/ConfiguratorPanel';
import GlassWallModel from './components/GlassWallModel';
import GLBModel from './components/GLBModel';
import './styles/configurator.css';

// Toggle between GLB renders and procedural models
const USE_GLB_MODELS = true;

/**
 * Main Configurator App
 * Layout: 3D Canvas (left) + Configuration Panel (right)
 * Inspired by Daglichtdesign configurator UI/UX
 */
const App = () => {
  const { isLoading, error, initializeSettings } = useConfigStore();

  useEffect(() => {
    initializeSettings();
  }, []);

  if (isLoading) {
    return (
      <div className="cgc-app cgc-loading-state">
        <div className="cgc-loader">
          <div className="cgc-spinner"></div>
          <p>Configurator laden...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="cgc-app cgc-error-state">
        <div className="cgc-error">
          <h3>Er is iets misgegaan</h3>
          <p>{error}</p>
          <button onClick={() => window.location.reload()}>Opnieuw proberen</button>
        </div>
      </div>
    );
  }

  return (
    <div className="cgc-app">
      {/* 3D Canvas Section */}
      <div className="cgc-canvas-section">
        <Canvas
          shadows
          camera={{ position: [4, 2.5, 4], fov: 50 }}
          gl={{ antialias: true, alpha: false }}
        >
          {/* Sky-like background gradient */}
          <color attach="background" args={['#e8f4f8']} />

          {/* Lighting */}
          <ambientLight intensity={0.5} />
          <directionalLight
            position={[10, 10, 5]}
            intensity={1.2}
            castShadow
            shadow-mapSize-width={2048}
            shadow-mapSize-height={2048}
          />
          <directionalLight position={[-5, 5, -5]} intensity={0.3} />

          {/* Environment for glass reflections */}
          <Environment preset="city" />

          {/* Glass Wall Model */}
          <Suspense fallback={null}>
            {USE_GLB_MODELS ? <GLBModel /> : <GlassWallModel />}
          </Suspense>

          {/* Floor/ground plane */}
          <mesh rotation={[-Math.PI / 2, 0, 0]} position={[0, -0.01, 0]} receiveShadow>
            <planeGeometry args={[20, 20]} />
            <meshStandardMaterial color="#a8d5a2" roughness={0.8} />
          </mesh>

          {/* Contact shadows for realism */}
          <ContactShadows
            position={[0, 0, 0]}
            opacity={0.5}
            scale={12}
            blur={2.5}
            far={4}
          />

          {/* Orbit Controls */}
          <OrbitControls
            enablePan={false}
            minPolarAngle={Math.PI / 6}
            maxPolarAngle={Math.PI / 2.1}
            minDistance={2.5}
            maxDistance={8}
            target={[0, 1, 0]}
          />
        </Canvas>

        {/* Canvas Controls Hint */}
        <div className="cgc-canvas-controls">
          <div className="cgc-control-btn">
            <span>🖱️</span>
            <span>Roteren & Zoom</span>
          </div>
        </div>
      </div>

      {/* Configuration Panel - Right Side */}
      <div className="cgc-panel-section">
        <ConfiguratorPanel />
      </div>
    </div>
  );
};

export default App;
