import React, { useRef, useEffect, useMemo } from 'react';
import { useGLTF } from '@react-three/drei';
import * as THREE from 'three';
import useConfigStore from '../store/useConfigStore';

/**
 * GLB Model Loader Component
 * Loads and displays .glb 3D renders based on configuration
 */
const GLBModel = () => {
  const { getActiveWall } = useConfigStore();
  const wall = getActiveWall();
  const groupRef = useRef();

  // Determine which model to load based on track count
  // We have 2-rail and 6-rail models, interpolate for others
  const getModelPath = () => {
    const trackCount = wall.trackCount;
    const basePath = '/wp-content/plugins/clickwave-glass-configurator/public/models/';

    // Use 2-rail for 2-3 tracks, 6-rail for 4-6 tracks
    if (trackCount <= 3) {
      return basePath + 'glaswand-2-rail.glb';
    } else {
      return basePath + 'glaswand-6-rail.glb';
    }
  };

  const modelPath = useMemo(() => getModelPath(), [wall.trackCount]);
  const { scene } = useGLTF(modelPath);

  // Scale model based on wall dimensions
  useEffect(() => {
    if (groupRef.current && scene) {
      const clonedScene = scene.clone();

      // Calculate bounding box of the model
      const box = new THREE.Box3().setFromObject(clonedScene);
      const size = new THREE.Vector3();
      box.getSize(size);

      // Target dimensions in meters
      const targetWidth = wall.width / 1000;
      const targetHeight = wall.height / 1000;

      // Calculate uniform scale to fit (use smallest scale to maintain proportions)
      const scaleX = targetWidth / size.x;
      const scaleY = targetHeight / size.y;
      const scale = Math.min(scaleX, scaleY) * 0.8; // 0.8 to leave some margin

      // Apply uniform scale
      groupRef.current.scale.set(scale, scale, scale);

      // Center the model at origin
      const center = new THREE.Vector3();
      box.getCenter(center);
      groupRef.current.position.set(
        -center.x * scale,
        0, // Keep at ground level
        -center.z * scale
      );
    }
  }, [wall.width, wall.height, scene]);

  if (!scene) {
    return null;
  }

  return (
    <group ref={groupRef}>
      <primitive object={scene.clone()} />
    </group>
  );
};

export default GLBModel;

// Preload models for better performance
const preloadModels = () => {
  const basePath = '/wp-content/plugins/clickwave-glass-configurator/public/models/';

  // Preload both available models
  useGLTF.preload(basePath + 'glaswand-2-rail.glb');
  useGLTF.preload(basePath + 'glaswand-6-rail.glb');
};

// Call preload when module loads
preloadModels();

export { preloadModels };
