import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';

// Wait for DOM to be ready
const initApp = () => {
  const container = document.getElementById('glass-configurator-root');
  if (container) {
    const root = ReactDOM.createRoot(container);
    root.render(
      <React.StrictMode>
        <App />
      </React.StrictMode>
    );
  }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initApp);
} else {
  initApp();
}
