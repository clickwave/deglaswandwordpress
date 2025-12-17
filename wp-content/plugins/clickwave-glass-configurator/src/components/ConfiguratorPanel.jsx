import React, { useState } from 'react';
import useConfigStore from '../store/useConfigStore';

/**
 * Main Configuration Panel Component
 * Step-based wizard interface
 */
const ConfiguratorPanel = () => {
  const {
    currentStep,
    settings,
    getActiveWall,
    updateActiveWall,
    nextStep,
    previousStep,
    getTotalPrice,
    formatPrice,
    getPriceBreakdown,
    customer,
    updateCustomer,
    walls,
    activeWallIndex,
    setActiveWall,
    addWall,
    removeWall,
    calculateWallPrice,
    globalOptions,
    updateGlobalOptions,
    getMontagePrice
  } = useConfigStore();

  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitSuccess, setSubmitSuccess] = useState(false);
  const [privacyAccepted, setPrivacyAccepted] = useState(false);

  const wall = getActiveWall();
  const pricing = settings?.pricing || {};
  const options = settings?.options || {};
  const labels = settings?.labels || {};

  const steps = [
    { id: 'dimensions', title: 'Afmetingen' },
    { id: 'options', title: 'Opties' },
    { id: 'quote', title: 'Offerte' }
  ];

  const totalPrice = getTotalPrice();

  // Handle quote submission
  const handleSubmit = async () => {
    if (!customer.name || !customer.email) {
      alert('Vul alstublieft uw naam en email in.');
      return;
    }

    setIsSubmitting(true);

    try {
      // Prepare data in the format the REST API expects
      const quoteData = {
        // Dimensions
        width: wall.width,
        height: wall.height,
        trackCount: wall.trackCount,

        // Colors & Materials
        frameColor: wall.frameColor,
        glassType: wall.glassType,

        // Design
        design: wall.design,
        steellookType: wall.steellookType || '',
        handleType: wall.handleType,

        // Options
        hasUProfiles: wall.hasUProfiles || false,
        hasFunderingskoker: wall.hasFunderingskoker || false,
        hasHardhoutPalen: wall.hasHardhoutPalen || false,
        meeneemersType: wall.meeneemersType || 'none',
        hasTochtstrippen: wall.hasTochtstrippen || false,
        hasMontage: wall.hasMontage || false,

        // Pricing
        priceEstimate: totalPrice,

        // Customer info
        customerName: customer.name,
        customerEmail: customer.email,
        customerPhone: customer.phone || '',
        customerMessage: customer.message || ''
      };

      const response = await fetch(`${window.cgcConfig?.restUrl}/quote`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(quoteData)
      });

      const data = await response.json();

      if (response.ok && data.success) {
        setSubmitSuccess(true);
      } else {
        console.error('Submit failed:', data);
        alert('Er is iets misgegaan. Probeer het later opnieuw.');
      }
    } catch (error) {
      console.error('Submit error:', error);
      alert('Er is een fout opgetreden. Controleer uw internetverbinding en probeer het opnieuw.');
    } finally {
      setIsSubmitting(false);
    }
  };

  // Render step content
  const renderStepContent = () => {
    switch (currentStep) {
      case 0:
        return <DimensionsStep wall={wall} updateActiveWall={updateActiveWall} settings={settings} pricing={pricing} />;
      case 1:
        return <OptionsStep wall={wall} updateActiveWall={updateActiveWall} settings={settings} pricing={pricing} options={options} />;
      case 2:
        return submitSuccess ? (
          <SuccessMessage />
        ) : (
          <QuoteStep
            wall={wall}
            customer={customer}
            updateCustomer={updateCustomer}
            privacyAccepted={privacyAccepted}
            setPrivacyAccepted={setPrivacyAccepted}
            formatPrice={formatPrice}
            totalPrice={totalPrice}
          />
        );
      default:
        return null;
    }
  };

  const isLastStep = currentStep === steps.length - 1;

  return (
    <>
      {/* Header with back button and step indicator */}
      <div className="cgc-panel-header">
        <div className="cgc-panel-header-left">
          <button
            className="cgc-back-btn"
            onClick={previousStep}
            disabled={currentStep === 0}
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M15 18l-6-6 6-6" />
            </svg>
            Terug
          </button>
        </div>

        <div className="cgc-step-indicator">
          <span className="cgc-step-number">{currentStep + 1}.</span>
          <span className="cgc-step-title">{steps[currentStep]?.title}</span>
        </div>

        <div className="cgc-step-dots">
          {steps.map((step, index) => (
            <div
              key={step.id}
              className={`cgc-step-dot ${index <= currentStep ? 'active' : ''}`}
            />
          ))}
        </div>
      </div>

      {/* Multi-wall tabs */}
      {currentStep < 2 && (
        <div className="cgc-wall-tabs">
          <div className="cgc-wall-tabs-header">
            <span className="cgc-wall-tabs-title">
              {walls.length === 1 ? 'Uw configuratie' : `${walls.length} wanden`}
            </span>
            {options.show_extra_wall_option && walls.length < 5 && (
              <button className="cgc-wall-tab-add-btn" onClick={addWall} title="Extra wand toevoegen">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                  <path d="M12 5v14M5 12h14" />
                </svg>
                <span>Extra wand toevoegen</span>
              </button>
            )}
          </div>
          <div className="cgc-wall-tabs-list">
            {walls.map((w, index) => (
              <div
                key={w.id}
                className={`cgc-wall-tab ${index === activeWallIndex ? 'active' : ''}`}
                onClick={() => setActiveWall(index)}
              >
                <div className="cgc-wall-tab-content">
                  <span className="cgc-wall-tab-label">Wand {index + 1}</span>
                  <span className="cgc-wall-tab-price">{formatPrice(calculateWallPrice(w))}</span>
                </div>
                {walls.length > 1 && (
                  <button
                    className="cgc-wall-tab-remove"
                    onClick={(e) => {
                      e.stopPropagation();
                      if (window.confirm('Weet u zeker dat u deze wand wilt verwijderen?')) {
                        removeWall(index);
                      }
                    }}
                    title="Wand verwijderen"
                  >
                    ×
                  </button>
                )}
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Content */}
      <div className="cgc-panel-content">
        {renderStepContent()}
      </div>

      {/* Footer with montage option, price and navigation */}
      {!submitSuccess && (
        <div className="cgc-panel-footer">
          {/* Montage option - global, one-time cost */}
          {options.show_montage && (
            <div
              className={`cgc-montage-option ${globalOptions.hasMontage ? 'selected' : ''}`}
              onClick={() => updateGlobalOptions({ hasMontage: !globalOptions.hasMontage })}
            >
              <div className="cgc-montage-left">
                <div className="cgc-checkbox-box">
                  <span className="cgc-checkbox-check">✓</span>
                </div>
                <div className="cgc-montage-text">
                  <span className="cgc-montage-label">Professionele montage</span>
                  <span className="cgc-montage-note">Eenmalig, voor alle wanden</span>
                </div>
              </div>
              <span className="cgc-montage-price">+ {formatPrice(getMontagePrice())}</span>
            </div>
          )}

          <div className="cgc-price-summary">
            {walls.length > 1 && (
              <div className="cgc-price-row">
                <span className="cgc-price-label">{walls.length} wanden</span>
                <span className="cgc-price-value">{formatPrice(totalPrice - (globalOptions.hasMontage ? getMontagePrice() : 0))}</span>
              </div>
            )}
            {globalOptions.hasMontage && (
              <div className="cgc-price-row">
                <span className="cgc-price-label">Montage</span>
                <span className="cgc-price-value">{formatPrice(getMontagePrice())}</span>
              </div>
            )}
            <div className="cgc-price-row total">
              <span className="cgc-price-label">Totaal</span>
              <span className="cgc-price-value">{formatPrice(totalPrice)}</span>
            </div>
            <p className="cgc-price-notice">{labels.vat_notice || 'Incl. BTW'}</p>
          </div>

          {isLastStep ? (
            <button
              className="cgc-next-btn submit"
              onClick={handleSubmit}
              disabled={isSubmitting || !customer.name || !customer.email}
            >
              {isSubmitting ? 'Verzenden...' : (labels.finish || 'Offerte aanvragen')}
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" />
              </svg>
            </button>
          ) : (
            <button className="cgc-next-btn" onClick={nextStep}>
              {labels.next || 'Volgende'}
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M9 18l6-6-6-6" />
              </svg>
            </button>
          )}
        </div>
      )}
    </>
  );
};

/**
 * Step 1: Dimensions
 */
const DimensionsStep = ({ wall, updateActiveWall, settings, pricing }) => {
  const dimensions = settings?.dimensions || {};

  // Calculate slider progress percentage for filled track
  const getSliderProgress = (value, min, max) => {
    return ((value - min) / (max - min)) * 100;
  };

  // Calculate recommended track count based on width
  const getRecommendedTrackCount = (width) => {
    // Based on typical glass panel widths (max ~1200mm per panel for stability)
    // These thresholds can be adjusted based on product specifications
    if (width <= 2400) return 3;
    if (width <= 3600) return 4;
    if (width <= 4800) return 5;
    return 6;
  };

  const handleDimensionChange = (key, value, min, max, step) => {
    const numValue = parseInt(value);
    if (numValue >= min && numValue <= max) {
      const updates = { [key]: numValue };

      // Auto-update track count when width changes
      if (key === 'width') {
        updates.trackCount = getRecommendedTrackCount(numValue);
      }

      updateActiveWall(updates);
    }
  };

  const incrementValue = (key, current, step, max) => {
    const newVal = Math.min(current + step, max);
    const updates = { [key]: newVal };

    // Auto-update track count when width changes
    if (key === 'width') {
      updates.trackCount = getRecommendedTrackCount(newVal);
    }

    updateActiveWall(updates);
  };

  const decrementValue = (key, current, step, min) => {
    const newVal = Math.max(current - step, min);
    const updates = { [key]: newVal };

    // Auto-update track count when width changes
    if (key === 'width') {
      updates.trackCount = getRecommendedTrackCount(newVal);
    }

    updateActiveWall(updates);
  };

  return (
    <div className="cgc-step-content">
      {/* Rails Info - Display only, auto-calculated based on width */}
      <div className="cgc-option-group">
        <div className="cgc-option-label">Aantal rails / panelen</div>
        <div className="cgc-info-display">
          <span className="cgc-info-value">{wall.trackCount} rails</span>
          <span className="cgc-info-note">(automatisch bepaald op basis van breedte)</span>
        </div>
      </div>

      {/* Width Control */}
      <div className="cgc-dimension-control">
        <div className="cgc-dimension-header">
          <span className="cgc-dimension-label">Breedte</span>
          <div className="cgc-dimension-value-wrapper">
            <button
              className="cgc-dimension-btn"
              onClick={() => decrementValue('width', wall.width, dimensions.width?.step || 100, dimensions.width?.min || 1500)}
            >
              −
            </button>
            <span className="cgc-dimension-value">
              {wall.width}<span className="cgc-dimension-unit">mm</span>
            </span>
            <button
              className="cgc-dimension-btn"
              onClick={() => incrementValue('width', wall.width, dimensions.width?.step || 100, dimensions.width?.max || 6000)}
            >
              +
            </button>
          </div>
        </div>
        <input
          type="range"
          className="cgc-dimension-slider"
          min={dimensions.width?.min || 1500}
          max={dimensions.width?.max || 6000}
          step={dimensions.width?.step || 100}
          value={wall.width}
          onChange={(e) => handleDimensionChange('width', e.target.value, dimensions.width?.min, dimensions.width?.max, dimensions.width?.step)}
          style={{ '--slider-progress': `${getSliderProgress(wall.width, dimensions.width?.min || 1500, dimensions.width?.max || 6000)}%` }}
        />
        <div className="cgc-dimension-range">
          <span>{dimensions.width?.min || 1500}</span>
          <span>{dimensions.width?.max || 6000}</span>
        </div>
      </div>

      {/* Height Control */}
      <div className="cgc-dimension-control">
        <div className="cgc-dimension-header">
          <span className="cgc-dimension-label">Hoogte</span>
          <div className="cgc-dimension-value-wrapper">
            <button
              className="cgc-dimension-btn"
              onClick={() => decrementValue('height', wall.height, dimensions.height?.step || 100, dimensions.height?.min || 1800)}
            >
              −
            </button>
            <span className="cgc-dimension-value">
              {wall.height}<span className="cgc-dimension-unit">mm</span>
            </span>
            <button
              className="cgc-dimension-btn"
              onClick={() => incrementValue('height', wall.height, dimensions.height?.step || 100, dimensions.height?.max || 3000)}
            >
              +
            </button>
          </div>
        </div>
        <input
          type="range"
          className="cgc-dimension-slider"
          min={dimensions.height?.min || 1800}
          max={dimensions.height?.max || 3000}
          step={dimensions.height?.step || 100}
          value={wall.height}
          onChange={(e) => handleDimensionChange('height', e.target.value, dimensions.height?.min, dimensions.height?.max, dimensions.height?.step)}
          style={{ '--slider-progress': `${getSliderProgress(wall.height, dimensions.height?.min || 1800, dimensions.height?.max || 3000)}%` }}
        />
        <div className="cgc-dimension-range">
          <span>{dimensions.height?.min || 1800}</span>
          <span>{dimensions.height?.max || 3000}</span>
        </div>
      </div>

      {/* Frame Color */}
      <div className="cgc-option-group">
        <div className="cgc-option-label">Profielkleur</div>
        <div className="cgc-radio-options">
          {Object.entries(pricing.frame_colors || {}).map(([key, color]) => {
            // Map color keys to swatch classes
            const swatchClass = key === 'black' || key.includes('9005') ? 'black' :
                               key === 'anthracite' || key.includes('7016') ? 'anthracite' :
                               key === 'white' ? 'white' : 'black';
            return (
              <div
                key={key}
                className={`cgc-radio-option ${wall.frameColor === key ? 'selected' : ''}`}
                onClick={() => updateActiveWall({ frameColor: key })}
              >
                <div className="cgc-radio-option-left">
                  <div className={`cgc-color-swatch ${swatchClass}`} />
                  <span className="cgc-radio-label">{color.name}</span>
                </div>
                <span className={`cgc-radio-price ${color.price === 0 ? 'free' : ''}`}>
                  {color.price === 0 ? 'Standaard' : `+ €${color.price}`}
                </span>
              </div>
            );
          })}
        </div>
      </div>

      {/* Glass Type */}
      <div className="cgc-option-group">
        <div className="cgc-option-label">Glastype</div>
        <div className="cgc-radio-options">
          {Object.entries(pricing.glass_types || {}).map(([key, type]) => (
            <div
              key={key}
              className={`cgc-radio-option ${wall.glassType === key ? 'selected' : ''}`}
              onClick={() => updateActiveWall({ glassType: key })}
            >
              <div className="cgc-radio-option-left">
                <div className="cgc-radio-circle">
                  <div className="cgc-radio-circle-inner" />
                </div>
                <span className="cgc-radio-label">{type.name}</span>
              </div>
              <span className={`cgc-radio-price ${type.price_per_panel === 0 ? 'free' : ''}`}>
                {type.price_per_panel === 0 ? 'Standaard' : `+ €${type.price_per_panel}/paneel`}
              </span>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

/**
 * Step 2: Options (Design, Accessories)
 */
const OptionsStep = ({ wall, updateActiveWall, settings, pricing, options }) => {
  const railPricing = pricing.rails?.[wall.trackCount] || {};

  return (
    <div className="cgc-step-content">
      {/* Design Selection */}
      {options.show_design && (
        <div className="cgc-option-group">
          <div className="cgc-option-label">Design uitvoering</div>
          <div className="cgc-button-options">
            <button
              className={`cgc-option-btn ${wall.design === 'standard' ? 'active' : ''}`}
              onClick={() => updateActiveWall({ design: 'standard', steellookType: null })}
            >
              Standaard
            </button>
            <button
              className={`cgc-option-btn ${wall.design === 'steellook' ? 'active' : ''}`}
              onClick={() => updateActiveWall({ design: 'steellook', steellookType: 'amsterdam' })}
            >
              Steellook
            </button>
          </div>
        </div>
      )}

      {/* Steellook Types */}
      {wall.design === 'steellook' && (
        <div className="cgc-option-group">
          <div className="cgc-option-label">Steellook type</div>
          <div className="cgc-radio-options">
            {Object.entries(pricing.steellook || {}).map(([key, style]) => (
              <div
                key={key}
                className={`cgc-radio-option ${wall.steellookType === key ? 'selected' : ''}`}
                onClick={() => updateActiveWall({ steellookType: key })}
              >
                <div className="cgc-radio-option-left">
                  <div className="cgc-radio-circle">
                    <div className="cgc-radio-circle-inner" />
                  </div>
                  <div>
                    <span className="cgc-radio-label">{style.name}</span>
                    {style.description && (
                      <div style={{ fontSize: '12px', color: '#999', marginTop: '2px' }}>
                        {style.description}
                      </div>
                    )}
                  </div>
                </div>
                <span className="cgc-radio-price">+ €{style.price_per_panel}/paneel</span>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Extra Options */}
      <div className="cgc-option-group">
        <div className="cgc-option-label">Extra opties</div>
        <div className="cgc-checkbox-options">
          {options.show_u_profiles && (
            <div
              className={`cgc-checkbox-option ${wall.hasUProfiles ? 'selected' : ''}`}
              onClick={() => updateActiveWall({ hasUProfiles: !wall.hasUProfiles })}
            >
              <div className="cgc-checkbox-option-left">
                <div className="cgc-checkbox-box">
                  <span className="cgc-checkbox-check">✓</span>
                </div>
                <span className="cgc-checkbox-label">U-profielen</span>
              </div>
              <span className="cgc-checkbox-price">+ €{railPricing.u_profiles || 0}</span>
            </div>
          )}

          {options.show_funderingskoker && (
            <div
              className={`cgc-checkbox-option ${wall.hasFunderingskoker ? 'selected' : ''}`}
              onClick={() => updateActiveWall({
                hasFunderingskoker: !wall.hasFunderingskoker,
                hasHardhoutenPalen: false,
                meeneemersType: !wall.hasFunderingskoker ? 'opgezet' : null
              })}
            >
              <div className="cgc-checkbox-option-left">
                <div className="cgc-checkbox-box">
                  <span className="cgc-checkbox-check">✓</span>
                </div>
                <span className="cgc-checkbox-label">Funderingskoker</span>
              </div>
              <span className="cgc-checkbox-price">+ €{railPricing.funderingskoker || 0}</span>
            </div>
          )}

          {wall.hasFunderingskoker && options.show_hardhout_palen && (
            <div
              className={`cgc-checkbox-option ${wall.hasHardhoutenPalen ? 'selected' : ''}`}
              onClick={() => updateActiveWall({ hasHardhoutenPalen: !wall.hasHardhoutenPalen })}
              style={{ marginLeft: '24px' }}
            >
              <div className="cgc-checkbox-option-left">
                <div className="cgc-checkbox-box">
                  <span className="cgc-checkbox-check">✓</span>
                </div>
                <span className="cgc-checkbox-label">Incl. hardhout palen</span>
              </div>
              <span className="cgc-checkbox-price">+ €{pricing.hardhout_palen || 0}</span>
            </div>
          )}

          {options.show_tochtstrippen && (
            <div
              className={`cgc-checkbox-option ${wall.hasTochtstrippen ? 'selected' : ''}`}
              onClick={() => updateActiveWall({ hasTochtstrippen: !wall.hasTochtstrippen })}
            >
              <div className="cgc-checkbox-option-left">
                <div className="cgc-checkbox-box">
                  <span className="cgc-checkbox-check">✓</span>
                </div>
                <span className="cgc-checkbox-label">Tochtstrippen</span>
              </div>
              <span className="cgc-checkbox-price">+ €{railPricing.tochtstrippen || 0}</span>
            </div>
          )}
          {/* Note: Montage is moved to global options in footer - it's a one-time cost, not per wall */}
        </div>
      </div>

      {/* Handle Type */}
      {options.show_handles && (
        <div className="cgc-option-group">
          <div className="cgc-option-label">Handgreep</div>
          <div className="cgc-radio-options">
            {Object.entries(pricing.handles || {}).map(([key, handle]) => (
              <div
                key={key}
                className={`cgc-radio-option ${wall.handleType === key ? 'selected' : ''}`}
                onClick={() => updateActiveWall({ handleType: key })}
              >
                <div className="cgc-radio-option-left">
                  <div className="cgc-radio-circle">
                    <div className="cgc-radio-circle-inner" />
                  </div>
                  <span className="cgc-radio-label">{handle.name}</span>
                </div>
                <span className={`cgc-radio-price ${handle.price === 0 ? 'free' : ''}`}>
                  {handle.price === 0 ? 'Standaard' : `+ €${handle.price}`}
                </span>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
};

/**
 * Step 3: Quote Form
 */
const QuoteStep = ({ wall, customer, updateCustomer, privacyAccepted, setPrivacyAccepted, formatPrice, totalPrice }) => {
  return (
    <div className="cgc-quote-form">
      {/* Configuration Summary */}
      <div className="cgc-config-summary">
        <h3>Uw configuratie</h3>
        <div className="cgc-config-summary-row">
          <span className="cgc-config-summary-label">Afmetingen:</span>
          <span className="cgc-config-summary-value">{wall.width} × {wall.height} mm</span>
        </div>
        <div className="cgc-config-summary-row">
          <span className="cgc-config-summary-label">Aantal panelen:</span>
          <span className="cgc-config-summary-value">{wall.trackCount}</span>
        </div>
        <div className="cgc-config-summary-row">
          <span className="cgc-config-summary-label">Design:</span>
          <span className="cgc-config-summary-value">
            {wall.design === 'steellook' ? `Steellook ${wall.steellookType}` : 'Standaard'}
          </span>
        </div>
        <div className="cgc-config-summary-row" style={{ fontWeight: 600, paddingTop: '8px', borderTop: '1px solid #e0e0e0', marginTop: '8px' }}>
          <span className="cgc-config-summary-label">Totaal:</span>
          <span className="cgc-config-summary-value">{formatPrice(totalPrice)}</span>
        </div>
      </div>

      {/* Contact Form */}
      <div className="cgc-form-group">
        <label className="cgc-form-label">
          Naam <span className="required">*</span>
        </label>
        <input
          type="text"
          className="cgc-form-input"
          placeholder="Uw volledige naam"
          value={customer.name}
          onChange={(e) => updateCustomer({ name: e.target.value })}
        />
      </div>

      <div className="cgc-form-group">
        <label className="cgc-form-label">
          E-mailadres <span className="required">*</span>
        </label>
        <input
          type="email"
          className="cgc-form-input"
          placeholder="email@voorbeeld.nl"
          value={customer.email}
          onChange={(e) => updateCustomer({ email: e.target.value })}
        />
      </div>

      <div className="cgc-form-group">
        <label className="cgc-form-label">
          Telefoonnummer <span className="required">*</span>
        </label>
        <input
          type="tel"
          className="cgc-form-input"
          placeholder="06 12345678"
          value={customer.phone}
          onChange={(e) => updateCustomer({ phone: e.target.value })}
        />
      </div>

      <div className="cgc-form-group">
        <label className="cgc-form-label">Opmerkingen / vragen</label>
        <textarea
          className="cgc-form-textarea"
          placeholder="Heeft u nog vragen of opmerkingen?"
          value={customer.message}
          onChange={(e) => updateCustomer({ message: e.target.value })}
        />
      </div>

      <div className="cgc-privacy-check">
        <input
          type="checkbox"
          id="privacy"
          checked={privacyAccepted}
          onChange={(e) => setPrivacyAccepted(e.target.checked)}
        />
        <label htmlFor="privacy">
          Ik ga akkoord met de <a href="/privacybeleid/" target="_blank">privacyverklaring</a> en geef toestemming voor het verwerken van mijn gegevens voor deze offerte-aanvraag. *
        </label>
      </div>

      <p className="cgc-form-notice">
        Wij nemen binnen 1 werkdag contact met u op.
      </p>
    </div>
  );
};

/**
 * Success Message
 */
const SuccessMessage = () => (
  <div className="cgc-success-message">
    <div className="cgc-success-icon">✓</div>
    <h3>Bedankt voor uw aanvraag!</h3>
    <p>
      Uw offerte-aanvraag is succesvol verzonden.<br />
      Wij nemen binnen 1 werkdag contact met u op.
    </p>
  </div>
);

export default ConfiguratorPanel;
