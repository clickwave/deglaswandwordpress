import React from 'react';
import useConfigStore from '../../../store/useConfigStore';

/**
 * Step 1: Dimensions selection
 */
const DimensionsStep = () => {
  const { settings, getActiveWall, updateActiveWall, formatPrice, calculateWallPrice } = useConfigStore();
  const wall = getActiveWall();
  const dimensions = settings?.dimensions || {};

  const handleWidthChange = (e) => {
    updateActiveWall({ width: parseInt(e.target.value) });
  };

  const handleHeightChange = (e) => {
    updateActiveWall({ height: parseInt(e.target.value) });
  };

  const m2 = ((wall.width / 1000) * (wall.height / 1000)).toFixed(2);

  return (
    <div className="cgc-step cgc-step-dimensions">
      <div className="cgc-step-header">
        <h2>Bepaal de afmetingen</h2>
        <p>Geef de breedte en hoogte van uw glaswand op in millimeters.</p>
      </div>

      <div className="cgc-dimension-controls">
        <div className="cgc-dimension-group">
          <label htmlFor="width">Breedte</label>
          <div className="cgc-slider-container">
            <input
              type="range"
              id="width"
              min={dimensions.width?.min || 1500}
              max={dimensions.width?.max || 6000}
              step={dimensions.width?.step || 100}
              value={wall.width}
              onChange={handleWidthChange}
              className="cgc-slider"
            />
            <div className="cgc-dimension-value">
              <input
                type="number"
                value={wall.width}
                onChange={handleWidthChange}
                min={dimensions.width?.min || 1500}
                max={dimensions.width?.max || 6000}
                step={dimensions.width?.step || 100}
                className="cgc-dimension-input"
              />
              <span className="cgc-dimension-unit">mm</span>
            </div>
          </div>
          <div className="cgc-dimension-range">
            <span>{dimensions.width?.min || 1500} mm</span>
            <span>{dimensions.width?.max || 6000} mm</span>
          </div>
        </div>

        <div className="cgc-dimension-group">
          <label htmlFor="height">Hoogte</label>
          <div className="cgc-slider-container">
            <input
              type="range"
              id="height"
              min={dimensions.height?.min || 1800}
              max={dimensions.height?.max || 3000}
              step={dimensions.height?.step || 100}
              value={wall.height}
              onChange={handleHeightChange}
              className="cgc-slider"
            />
            <div className="cgc-dimension-value">
              <input
                type="number"
                value={wall.height}
                onChange={handleHeightChange}
                min={dimensions.height?.min || 1800}
                max={dimensions.height?.max || 3000}
                step={dimensions.height?.step || 100}
                className="cgc-dimension-input"
              />
              <span className="cgc-dimension-unit">mm</span>
            </div>
          </div>
          <div className="cgc-dimension-range">
            <span>{dimensions.height?.min || 1800} mm</span>
            <span>{dimensions.height?.max || 3000} mm</span>
          </div>
        </div>
      </div>

      <div className="cgc-dimension-summary">
        <div className="cgc-summary-item">
          <span className="cgc-summary-label">Oppervlakte</span>
          <span className="cgc-summary-value">{m2} m²</span>
        </div>
        <div className="cgc-summary-item">
          <span className="cgc-summary-label">Breedte × Hoogte</span>
          <span className="cgc-summary-value">{wall.width} × {wall.height} mm</span>
        </div>
      </div>

      <style>{`
        .cgc-step-dimensions {
          max-width: 600px;
          margin: 0 auto;
        }

        .cgc-step-header {
          text-align: center;
          margin-bottom: 40px;
        }

        .cgc-step-header h2 {
          font-size: 28px;
          font-weight: 700;
          color: #1f3d58;
          margin: 0 0 12px;
        }

        .cgc-step-header p {
          font-size: 16px;
          color: #666;
          margin: 0;
        }

        .cgc-dimension-controls {
          display: flex;
          flex-direction: column;
          gap: 40px;
        }

        .cgc-dimension-group {
          display: flex;
          flex-direction: column;
          gap: 12px;
        }

        .cgc-dimension-group label {
          font-size: 16px;
          font-weight: 600;
          color: #333;
        }

        .cgc-slider-container {
          display: flex;
          align-items: center;
          gap: 20px;
        }

        .cgc-slider {
          flex: 1;
          height: 8px;
          -webkit-appearance: none;
          appearance: none;
          background: #e0e0e0;
          border-radius: 4px;
          outline: none;
        }

        .cgc-slider::-webkit-slider-thumb {
          -webkit-appearance: none;
          appearance: none;
          width: 24px;
          height: 24px;
          border-radius: 50%;
          background: #1f3d58;
          cursor: pointer;
          transition: transform 0.2s ease;
        }

        .cgc-slider::-webkit-slider-thumb:hover {
          transform: scale(1.1);
        }

        .cgc-slider::-moz-range-thumb {
          width: 24px;
          height: 24px;
          border-radius: 50%;
          background: #1f3d58;
          cursor: pointer;
          border: none;
        }

        .cgc-dimension-value {
          display: flex;
          align-items: center;
          gap: 4px;
          background: #f5f5f5;
          padding: 8px 12px;
          border-radius: 6px;
          min-width: 130px;
        }

        .cgc-dimension-input {
          width: 70px;
          border: none;
          background: transparent;
          font-size: 18px;
          font-weight: 600;
          color: #1f3d58;
          text-align: right;
        }

        .cgc-dimension-input:focus {
          outline: none;
        }

        .cgc-dimension-unit {
          font-size: 14px;
          color: #666;
        }

        .cgc-dimension-range {
          display: flex;
          justify-content: space-between;
          font-size: 12px;
          color: #999;
        }

        .cgc-dimension-summary {
          display: flex;
          gap: 20px;
          margin-top: 40px;
          padding: 20px;
          background: #f9f9f9;
          border-radius: 12px;
        }

        .cgc-summary-item {
          flex: 1;
          display: flex;
          flex-direction: column;
          align-items: center;
          gap: 4px;
        }

        .cgc-summary-label {
          font-size: 12px;
          color: #666;
          text-transform: uppercase;
          letter-spacing: 0.5px;
        }

        .cgc-summary-value {
          font-size: 20px;
          font-weight: 700;
          color: #1f3d58;
        }

        @media (max-width: 480px) {
          .cgc-slider-container {
            flex-direction: column;
            align-items: stretch;
          }

          .cgc-dimension-value {
            justify-content: center;
          }

          .cgc-dimension-summary {
            flex-direction: column;
          }
        }
      `}</style>
    </div>
  );
};

export default DimensionsStep;
