// Panel width calculation based on total width and overlap
export const calculatePanelWidth = (totalWidth, panelCount, overlapPerPanel = 25) => {
  return (totalWidth + (overlapPerPanel * (panelCount - 1))) / panelCount;
};

// Calculate panel positions with overlap
export const calculatePanelPositions = (totalWidth, panelCount, overlapPerPanel = 25) => {
  const panelWidth = calculatePanelWidth(totalWidth, panelCount, overlapPerPanel);
  const positions = [];

  for (let i = 0; i < panelCount; i++) {
    // Distribute panels evenly across total width
    const x = (i - (panelCount - 1) / 2) * (totalWidth / panelCount);
    positions.push(x);
  }

  return positions;
};

// Convert mm to meters (Three.js units)
export const mmToMeters = (mm) => mm / 1000;

// Format price with Euro symbol
export const formatPrice = (price) => {
  return new Intl.NumberFormat('nl-NL', {
    style: 'currency',
    currency: 'EUR'
  }).format(price);
};
