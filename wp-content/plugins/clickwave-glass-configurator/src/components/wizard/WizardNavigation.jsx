import React from 'react';
import useConfigStore from '../../store/useConfigStore';

/**
 * Navigation buttons for the wizard
 */
const WizardNavigation = ({ onSubmit }) => {
  const { currentStep, settings, nextStep, previousStep } = useConfigStore();
  const steps = settings?.steps || [];
  const labels = settings?.labels || {};

  const isFirstStep = currentStep === 0;
  const isLastStep = currentStep === steps.length - 1;

  return (
    <div className="cgc-wizard-navigation">
      <button
        type="button"
        className="cgc-btn cgc-btn-secondary"
        onClick={previousStep}
        disabled={isFirstStep}
      >
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
          <path d="M15 18l-6-6 6-6" />
        </svg>
        {labels.previous || 'Vorige'}
      </button>

      {isLastStep ? (
        <button
          type="button"
          className="cgc-btn cgc-btn-primary cgc-btn-submit"
          onClick={onSubmit}
        >
          {labels.finish || 'Offerte aanvragen'}
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" />
          </svg>
        </button>
      ) : (
        <button
          type="button"
          className="cgc-btn cgc-btn-primary"
          onClick={nextStep}
        >
          {labels.next || 'Volgende'}
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <path d="M9 18l6-6-6-6" />
          </svg>
        </button>
      )}

      <style>{`
        .cgc-wizard-navigation {
          display: flex;
          justify-content: space-between;
          gap: 16px;
          padding-top: 30px;
          border-top: 1px solid #e0e0e0;
          margin-top: 30px;
        }

        .cgc-btn {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          padding: 14px 24px;
          font-size: 16px;
          font-weight: 500;
          border-radius: 6px;
          border: none;
          cursor: pointer;
          transition: all 0.2s ease;
        }

        .cgc-btn:disabled {
          opacity: 0.5;
          cursor: not-allowed;
        }

        .cgc-btn-secondary {
          background: #f5f5f5;
          color: #333;
        }

        .cgc-btn-secondary:hover:not(:disabled) {
          background: #e8e8e8;
        }

        .cgc-btn-primary {
          background: #1f3d58;
          color: white;
        }

        .cgc-btn-primary:hover:not(:disabled) {
          background: #2a5070;
        }

        .cgc-btn-submit {
          background: #ee6b4e;
        }

        .cgc-btn-submit:hover:not(:disabled) {
          background: #d95a3d;
        }

        @media (max-width: 480px) {
          .cgc-wizard-navigation {
            flex-direction: column;
          }

          .cgc-btn {
            justify-content: center;
          }
        }
      `}</style>
    </div>
  );
};

export default WizardNavigation;
