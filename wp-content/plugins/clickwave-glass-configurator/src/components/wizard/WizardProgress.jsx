import React from 'react';
import useConfigStore from '../../store/useConfigStore';

/**
 * Progress indicator for the wizard steps
 */
const WizardProgress = () => {
  const { currentStep, settings, goToStep } = useConfigStore();
  const steps = settings?.steps || [];

  return (
    <div className="cgc-wizard-progress">
      <div className="cgc-progress-bar">
        <div
          className="cgc-progress-fill"
          style={{ width: `${((currentStep + 1) / steps.length) * 100}%` }}
        />
      </div>

      <div className="cgc-steps-indicator">
        {steps.map((step, index) => (
          <button
            key={step.id}
            className={`cgc-step-dot ${index === currentStep ? 'active' : ''} ${
              index < currentStep ? 'completed' : ''
            }`}
            onClick={() => index <= currentStep && goToStep(index)}
            disabled={index > currentStep}
            title={step.title}
          >
            <span className="cgc-step-number">{index + 1}</span>
            <span className="cgc-step-label">{step.title}</span>
          </button>
        ))}
      </div>

      <style>{`
        .cgc-wizard-progress {
          padding: 20px 0 30px;
          border-bottom: 1px solid #e0e0e0;
          margin-bottom: 30px;
        }

        .cgc-progress-bar {
          height: 4px;
          background: #e0e0e0;
          border-radius: 2px;
          margin-bottom: 20px;
          overflow: hidden;
        }

        .cgc-progress-fill {
          height: 100%;
          background: linear-gradient(90deg, #1f3d58 0%, #ee6b4e 100%);
          border-radius: 2px;
          transition: width 0.3s ease;
        }

        .cgc-steps-indicator {
          display: flex;
          justify-content: space-between;
          gap: 8px;
        }

        .cgc-step-dot {
          display: flex;
          flex-direction: column;
          align-items: center;
          gap: 8px;
          background: none;
          border: none;
          cursor: pointer;
          padding: 0;
          flex: 1;
          opacity: 0.5;
          transition: opacity 0.2s ease;
        }

        .cgc-step-dot:disabled {
          cursor: not-allowed;
        }

        .cgc-step-dot.active,
        .cgc-step-dot.completed {
          opacity: 1;
        }

        .cgc-step-number {
          width: 32px;
          height: 32px;
          border-radius: 50%;
          background: #e0e0e0;
          color: #666;
          display: flex;
          align-items: center;
          justify-content: center;
          font-weight: 600;
          font-size: 14px;
          transition: all 0.2s ease;
        }

        .cgc-step-dot.active .cgc-step-number {
          background: #1f3d58;
          color: white;
          transform: scale(1.1);
        }

        .cgc-step-dot.completed .cgc-step-number {
          background: #ee6b4e;
          color: white;
        }

        .cgc-step-label {
          font-size: 11px;
          color: #666;
          text-align: center;
          max-width: 80px;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }

        .cgc-step-dot.active .cgc-step-label {
          color: #1f3d58;
          font-weight: 600;
        }

        @media (max-width: 768px) {
          .cgc-step-label {
            display: none;
          }

          .cgc-step-number {
            width: 28px;
            height: 28px;
            font-size: 12px;
          }
        }
      `}</style>
    </div>
  );
};

export default WizardProgress;
