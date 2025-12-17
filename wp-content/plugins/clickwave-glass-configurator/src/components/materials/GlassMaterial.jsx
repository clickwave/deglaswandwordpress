import React from 'react';

export default function GlassMaterial({ glassType = 'helder' }) {
  const isTinted = glassType === 'getint';

  return (
    <meshPhysicalMaterial
      color={isTinted ? '#B8B8B8' : '#FFFFFF'}
      transmission={0.95}
      roughness={0.05}
      thickness={0.01}
      ior={1.5}
      transparent={true}
      opacity={isTinted ? 0.85 : 0.98}
      envMapIntensity={1.2}
      clearcoat={0.1}
      clearcoatRoughness={0.1}
    />
  );
}
