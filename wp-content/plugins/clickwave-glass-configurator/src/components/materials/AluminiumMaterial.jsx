import React from 'react';

const COLORS = {
  RAL9005: '#0A0A0A', // Matte Black
  RAL7016: '#383E42'  // Anthracite Grey
};

export default function AluminiumMaterial({ color = 'RAL9005' }) {
  return (
    <meshStandardMaterial
      color={COLORS[color]}
      roughness={0.7}
      metalness={0.9}
      envMapIntensity={1.0}
    />
  );
}
