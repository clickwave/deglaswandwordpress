import React from 'react';
import useConfigStore from '../store/useConfigStore';
import { formatPrice } from '../utils/calculations';

export default function ConfiguratorUI() {
  const {
    width,
    height,
    trackCount,
    frameColor,
    glassType,
    design,
    steellookType,
    hasUProfiles,
    hasFunderingskoker,
    hasHardhoutenPalen,
    meeneemersType,
    hasTochtstrippen,
    handleType,
    hasMontage,
    totalPrice,
    setWidth,
    setHeight,
    setTrackCount,
    setFrameColor,
    setGlassType,
    setDesign,
    setSteellookType,
    setHasUProfiles,
    setHasFunderingskoker,
    setHasHardhoutenPalen,
    setMeeneemersType,
    setHasTochtstrippen,
    setHandleType,
    setHasMontage
  } = useConfigStore();

  return (
    <div style={styles.sidebar}>
      <div style={styles.header}>
        <h1 style={styles.title}>Glas Schuifwand Configurator</h1>
      </div>

      <div style={styles.content}>
        {/* Dimensions */}
        <section style={styles.section}>
          <h2 style={styles.sectionTitle}>Afmetingen</h2>

          <label style={styles.label}>
            Breedte: {width}mm
            <input
              type="range"
              min="1500"
              max="6000"
              step="100"
              value={width}
              onChange={(e) => setWidth(Number(e.target.value))}
              style={styles.slider}
            />
          </label>

          <label style={styles.label}>
            Hoogte: {height}mm
            <input
              type="range"
              min="1800"
              max="3000"
              step="100"
              value={height}
              onChange={(e) => setHeight(Number(e.target.value))}
              style={styles.slider}
            />
          </label>
        </section>

        {/* Track Count */}
        <section style={styles.section}>
          <h2 style={styles.sectionTitle}>Aantal Rails/Panelen</h2>
          <div style={styles.buttonGroup}>
            {[3, 4, 5, 6].map((count) => (
              <button
                key={count}
                onClick={() => setTrackCount(count)}
                style={{
                  ...styles.button,
                  ...(trackCount === count ? styles.buttonActive : {})
                }}
              >
                {count}
              </button>
            ))}
          </div>
        </section>

        {/* Frame Color */}
        <section style={styles.section}>
          <h2 style={styles.sectionTitle}>Kleur</h2>
          <div style={styles.buttonGroup}>
            <button
              onClick={() => setFrameColor('RAL9005')}
              style={{
                ...styles.button,
                ...(frameColor === 'RAL9005' ? styles.buttonActive : {})
              }}
            >
              Zwart RAL9005
            </button>
            <button
              onClick={() => setFrameColor('RAL7016')}
              style={{
                ...styles.button,
                ...(frameColor === 'RAL7016' ? styles.buttonActive : {})
              }}
            >
              Antraciet RAL7016
            </button>
          </div>
        </section>

        {/* Glass Type */}
        <section style={styles.section}>
          <h2 style={styles.sectionTitle}>Glastype</h2>
          <div style={styles.buttonGroup}>
            <button
              onClick={() => setGlassType('helder')}
              style={{
                ...styles.button,
                ...(glassType === 'helder' ? styles.buttonActive : {})
              }}
            >
              Helder
            </button>
            <button
              onClick={() => setGlassType('getint')}
              style={{
                ...styles.button,
                ...(glassType === 'getint' ? styles.buttonActive : {})
              }}
            >
              Getint (+50/paneel)
            </button>
          </div>
        </section>

        {/* Design */}
        <section style={styles.section}>
          <h2 style={styles.sectionTitle}>Design</h2>
          <div style={styles.buttonGroup}>
            <button
              onClick={() => setDesign('standard')}
              style={{
                ...styles.button,
                ...(design === 'standard' ? styles.buttonActive : {})
              }}
            >
              Standard
            </button>
            <button
              onClick={() => setDesign('steellook')}
              style={{
                ...styles.button,
                ...(design === 'steellook' ? styles.buttonActive : {})
              }}
            >
              Steellook
            </button>
          </div>

          {design === 'steellook' && (
            <div style={{ ...styles.buttonGroup, marginTop: '10px' }}>
              {[
                { key: 'amsterdam', label: 'Amsterdam (+99.99)', price: 99.99 },
                { key: 'barcelona', label: 'Barcelona (+169.99)', price: 169.99 },
                { key: 'cairo', label: 'Cairo (+169.99)', price: 169.99 },
                { key: 'dublin', label: 'Dublin (+199.99)', price: 199.99 }
              ].map((type) => (
                <button
                  key={type.key}
                  onClick={() => setSteellookType(type.key)}
                  style={{
                    ...styles.buttonSmall,
                    ...(steellookType === type.key ? styles.buttonActive : {})
                  }}
                >
                  {type.label}
                </button>
              ))}
            </div>
          )}
        </section>

        {/* Additional Options */}
        <section style={styles.section}>
          <h2 style={styles.sectionTitle}>Extra Opties</h2>

          <label style={styles.checkbox}>
            <input
              type="checkbox"
              checked={hasUProfiles}
              onChange={(e) => setHasUProfiles(e.target.checked)}
            />
            U-profielen
          </label>

          <label style={styles.checkbox}>
            <input
              type="checkbox"
              checked={hasFunderingskoker}
              onChange={(e) => setHasFunderingskoker(e.target.checked)}
            />
            Funderingskoker
          </label>

          {hasFunderingskoker && (
            <>
              <label style={{ ...styles.checkbox, marginLeft: '20px' }}>
                <input
                  type="checkbox"
                  checked={hasHardhoutenPalen}
                  onChange={(e) => setHasHardhoutenPalen(e.target.checked)}
                />
                Met hardhout palen
              </label>

              <div style={{ marginLeft: '20px', marginTop: '10px' }}>
                <label style={styles.radioLabel}>
                  <input
                    type="radio"
                    checked={meeneemersType === 'opgezet'}
                    onChange={() => setMeeneemersType('opgezet')}
                  />
                  Meeneemers opgezet
                </label>
                <label style={styles.radioLabel}>
                  <input
                    type="radio"
                    checked={meeneemersType === 'afzijged'}
                    onChange={() => setMeeneemersType('afzijged')}
                  />
                  Meeneemers afzijged
                </label>
              </div>
            </>
          )}

          <label style={styles.checkbox}>
            <input
              type="checkbox"
              checked={hasTochtstrippen}
              onChange={(e) => setHasTochtstrippen(e.target.checked)}
            />
            Tochtstrippen
          </label>
        </section>

        {/* Handle Type */}
        <section style={styles.section}>
          <h2 style={styles.sectionTitle}>Handgreep</h2>
          <div style={styles.buttonGroup}>
            <button
              onClick={() => setHandleType('rechthoek')}
              style={{
                ...styles.button,
                ...(handleType === 'rechthoek' ? styles.buttonActive : {})
              }}
            >
              Rechthoek
            </button>
            <button
              onClick={() => setHandleType('rond')}
              style={{
                ...styles.button,
                ...(handleType === 'rond' ? styles.buttonActive : {})
              }}
            >
              Rond (+49.99)
            </button>
          </div>
        </section>

        {/* Installation */}
        <section style={styles.section}>
          <label style={styles.checkbox}>
            <input
              type="checkbox"
              checked={hasMontage}
              onChange={(e) => setHasMontage(e.target.checked)}
            />
            Montage (+899)
          </label>
        </section>
      </div>

      {/* Price Display */}
      <div style={styles.footer}>
        <div style={styles.priceLabel}>Totaalprijs:</div>
        <div style={styles.priceValue}>{formatPrice(totalPrice)}</div>
        <button style={styles.orderButton}>Bestellen</button>
      </div>
    </div>
  );
}

const styles = {
  sidebar: {
    width: '350px',
    height: '100vh',
    backgroundColor: '#ffffff',
    boxShadow: '2px 0 10px rgba(0,0,0,0.1)',
    display: 'flex',
    flexDirection: 'column',
    overflow: 'hidden'
  },
  header: {
    padding: '20px',
    borderBottom: '2px solid #e5e7eb',
    backgroundColor: '#1f2937'
  },
  title: {
    margin: 0,
    fontSize: '20px',
    fontWeight: 'bold',
    color: '#ffffff'
  },
  content: {
    flex: 1,
    overflowY: 'auto',
    padding: '20px'
  },
  section: {
    marginBottom: '25px'
  },
  sectionTitle: {
    fontSize: '14px',
    fontWeight: 'bold',
    marginBottom: '12px',
    color: '#1f2937',
    textTransform: 'uppercase',
    letterSpacing: '0.5px'
  },
  label: {
    display: 'block',
    marginBottom: '15px',
    fontSize: '13px',
    color: '#374151',
    fontWeight: '500'
  },
  slider: {
    width: '100%',
    marginTop: '8px',
    cursor: 'pointer'
  },
  buttonGroup: {
    display: 'grid',
    gridTemplateColumns: '1fr 1fr',
    gap: '8px'
  },
  button: {
    padding: '10px 15px',
    border: '2px solid #e5e7eb',
    backgroundColor: '#ffffff',
    borderRadius: '6px',
    cursor: 'pointer',
    fontSize: '13px',
    fontWeight: '500',
    transition: 'all 0.2s',
    color: '#374151'
  },
  buttonSmall: {
    padding: '8px 12px',
    border: '2px solid #e5e7eb',
    backgroundColor: '#ffffff',
    borderRadius: '6px',
    cursor: 'pointer',
    fontSize: '12px',
    fontWeight: '500',
    transition: 'all 0.2s',
    color: '#374151'
  },
  buttonActive: {
    backgroundColor: '#1f2937',
    borderColor: '#1f2937',
    color: '#ffffff'
  },
  checkbox: {
    display: 'block',
    marginBottom: '12px',
    fontSize: '13px',
    cursor: 'pointer',
    color: '#374151'
  },
  radioLabel: {
    display: 'block',
    marginBottom: '8px',
    fontSize: '13px',
    cursor: 'pointer',
    color: '#374151'
  },
  footer: {
    padding: '20px',
    borderTop: '2px solid #e5e7eb',
    backgroundColor: '#f9fafb'
  },
  priceLabel: {
    fontSize: '14px',
    color: '#6b7280',
    marginBottom: '5px',
    textTransform: 'uppercase',
    letterSpacing: '0.5px'
  },
  priceValue: {
    fontSize: '32px',
    fontWeight: 'bold',
    color: '#1f2937',
    marginBottom: '15px'
  },
  orderButton: {
    width: '100%',
    padding: '15px',
    backgroundColor: '#10b981',
    color: '#ffffff',
    border: 'none',
    borderRadius: '8px',
    fontSize: '16px',
    fontWeight: 'bold',
    cursor: 'pointer',
    transition: 'background-color 0.2s'
  }
};
