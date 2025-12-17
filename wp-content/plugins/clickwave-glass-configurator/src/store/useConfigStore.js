import { create } from 'zustand';

/**
 * Configuration Store with Dynamic Backend Settings
 * Fetches pricing and options from WordPress REST API
 */
const useConfigStore = create((set, get) => ({
  // Loading state
  isLoading: true,
  error: null,

  // Backend settings (loaded from WordPress)
  settings: null,

  // Current wizard step
  currentStep: 0,

  // Wall configurations (support for multiple walls)
  walls: [
    {
      id: 1,
      width: 3000,
      height: 2200,
      trackCount: 3,
      frameColor: 'RAL9005',
      glassType: 'helder',
      design: 'standard',
      steellookType: null,
      hasUProfiles: false,
      hasFunderingskoker: false,
      hasHardhoutenPalen: false,
      meeneemersType: null,
      hasTochtstrippen: false,
      handleType: 'rechthoek',
      hasMontage: false
    }
  ],

  // Active wall index
  activeWallIndex: 0,

  // Global options (apply to entire order, not per wall)
  globalOptions: {
    hasMontage: false
  },

  // Customer info for quote
  customer: {
    name: '',
    email: '',
    phone: '',
    message: ''
  },

  // Initialize store with backend settings
  initializeSettings: async () => {
    try {
      set({ isLoading: true, error: null });

      // Fetch settings from WordPress REST API
      const response = await fetch(
        `${window.cgcConfig?.restUrl || '/wp-json/clickwave-glass/v1'}/settings`
      );

      if (!response.ok) {
        throw new Error('Failed to load configurator settings');
      }

      const settings = await response.json();

      // Update initial wall with default dimensions from settings
      set((state) => ({
        settings,
        isLoading: false,
        walls: state.walls.map((wall, index) =>
          index === 0
            ? {
                ...wall,
                width: settings.dimensions?.width?.default || 3000,
                height: settings.dimensions?.height?.default || 2200
              }
            : wall
        )
      }));
    } catch (error) {
      console.error('Error loading settings:', error);
      set({ error: error.message, isLoading: false });
    }
  },

  // Get active wall
  getActiveWall: () => {
    const state = get();
    return state.walls[state.activeWallIndex] || state.walls[0];
  },

  // Update active wall property
  updateActiveWall: (updates) => {
    set((state) => ({
      walls: state.walls.map((wall, index) =>
        index === state.activeWallIndex ? { ...wall, ...updates } : wall
      )
    }));
  },

  // Wizard navigation
  nextStep: () => {
    const state = get();
    const maxSteps = state.settings?.steps?.length || 7;
    if (state.currentStep < maxSteps - 1) {
      set({ currentStep: state.currentStep + 1 });
    }
  },

  previousStep: () => {
    const state = get();
    if (state.currentStep > 0) {
      set({ currentStep: state.currentStep - 1 });
    }
  },

  goToStep: (step) => {
    set({ currentStep: step });
  },

  // Add new wall
  addWall: () => {
    const state = get();
    const newWall = {
      id: Date.now(),
      width: state.settings?.dimensions?.width?.default || 3000,
      height: state.settings?.dimensions?.height?.default || 2200,
      trackCount: 3,
      frameColor: 'RAL9005',
      glassType: 'helder',
      design: 'standard',
      steellookType: null,
      hasUProfiles: false,
      hasFunderingskoker: false,
      hasHardhoutenPalen: false,
      meeneemersType: null,
      hasTochtstrippen: false,
      handleType: 'rechthoek',
      hasMontage: false
    };

    set((state) => ({
      walls: [...state.walls, newWall],
      activeWallIndex: state.walls.length
    }));
  },

  // Remove wall
  removeWall: (index) => {
    set((state) => {
      if (state.walls.length <= 1) return state;

      const newWalls = state.walls.filter((_, i) => i !== index);
      const newActiveIndex = Math.min(state.activeWallIndex, newWalls.length - 1);

      return {
        walls: newWalls,
        activeWallIndex: newActiveIndex
      };
    });
  },

  // Set active wall
  setActiveWall: (index) => {
    set({ activeWallIndex: index });
  },

  // Update customer info
  updateCustomer: (updates) => {
    set((state) => ({
      customer: { ...state.customer, ...updates }
    }));
  },

  // Update global options (montage, etc.)
  updateGlobalOptions: (updates) => {
    set((state) => ({
      globalOptions: { ...state.globalOptions, ...updates }
    }));
  },

  // Calculate price for a single wall
  // Note: Height does NOT affect pricing - only width/panel count matters
  calculateWallPrice: (wall) => {
    const state = get();
    const settings = state.settings;

    if (!settings || !settings.pricing) {
      return 0;
    }

    const pricing = settings.pricing;
    let price = 0;

    // Base price calculation - always per panel (height doesn't matter)
    // Using panel count which is determined by width
    price += wall.trackCount * (pricing.base_price_per_panel || 299.99);

    // Glass type surcharge
    if (wall.glassType && pricing.glass_types?.[wall.glassType]) {
      price += pricing.glass_types[wall.glassType].price_per_panel * wall.trackCount;
    }

    // Steellook design surcharge
    if (wall.design === 'steellook' && wall.steellookType && pricing.steellook?.[wall.steellookType]) {
      price += pricing.steellook[wall.steellookType].price_per_panel * wall.trackCount;
    }

    // Rail-specific pricing
    const railPricing = pricing.rails?.[wall.trackCount];
    if (railPricing) {
      // U-profiles
      if (wall.hasUProfiles) {
        price += railPricing.u_profiles || 0;
      }

      // Funderingskoker
      if (wall.hasFunderingskoker) {
        price += railPricing.funderingskoker || 0;

        // Hardhout palen (only if funderingskoker is selected)
        if (wall.hasHardhoutenPalen) {
          price += pricing.hardhout_palen || 0;
        }

        // Meeneemers
        if (wall.meeneemersType) {
          price += railPricing.meeneemers || 0;
        }
      }

      // Tochtstrippen
      if (wall.hasTochtstrippen) {
        price += railPricing.tochtstrippen || 0;
      }
    }

    // Handle surcharge
    if (wall.handleType === 'rond' && pricing.handles?.rond) {
      price += pricing.handles.rond.price || 0;
    }

    // Note: Montage is NOT per wall - it's a global option added once to total

    return price;
  },

  // Calculate total price for all walls + global options
  getTotalPrice: () => {
    const state = get();
    const settings = state.settings;

    // Sum all wall prices
    let total = state.walls.reduce((sum, wall) => {
      return sum + state.calculateWallPrice(wall);
    }, 0);

    // Add montage once (global option, not per wall)
    if (state.globalOptions.hasMontage && settings?.pricing?.montage) {
      total += settings.pricing.montage;
    }

    return total;
  },

  // Get montage price for display
  getMontagePrice: () => {
    const state = get();
    return state.settings?.pricing?.montage || 899;
  },

  // Format price for display
  formatPrice: (price) => {
    return new Intl.NumberFormat('nl-NL', {
      style: 'currency',
      currency: 'EUR'
    }).format(price);
  },

  // Get price breakdown for current wall
  getPriceBreakdown: () => {
    const state = get();
    const wall = state.getActiveWall();
    const settings = state.settings;

    if (!settings || !settings.pricing) {
      return [];
    }

    const pricing = settings.pricing;
    const breakdown = [];

    // Base price - always per panel (height doesn't affect price)
    breakdown.push({
      label: `Basisprijs (${wall.trackCount} panelen)`,
      price: wall.trackCount * (pricing.base_price_per_panel || 299.99)
    });

    // Glass type
    if (wall.glassType === 'getint' && pricing.glass_types?.getint) {
      breakdown.push({
        label: `Getint glas (${wall.trackCount}x)`,
        price: pricing.glass_types.getint.price_per_panel * wall.trackCount
      });
    }

    // Steellook
    if (wall.design === 'steellook' && wall.steellookType && pricing.steellook?.[wall.steellookType]) {
      const style = pricing.steellook[wall.steellookType];
      breakdown.push({
        label: `${style.name} (${wall.trackCount}x)`,
        price: style.price_per_panel * wall.trackCount
      });
    }

    // Rail-specific items
    const railPricing = pricing.rails?.[wall.trackCount];
    if (railPricing) {
      if (wall.hasUProfiles) {
        breakdown.push({
          label: 'U-profielen',
          price: railPricing.u_profiles
        });
      }

      if (wall.hasFunderingskoker) {
        breakdown.push({
          label: 'Funderingskoker',
          price: railPricing.funderingskoker
        });

        if (wall.hasHardhoutenPalen) {
          breakdown.push({
            label: 'Hardhout palen',
            price: pricing.hardhout_palen
          });
        }

        if (wall.meeneemersType) {
          breakdown.push({
            label: `Meeneemers (${wall.meeneemersType})`,
            price: railPricing.meeneemers
          });
        }
      }

      if (wall.hasTochtstrippen) {
        breakdown.push({
          label: 'Tochtstrippen',
          price: railPricing.tochtstrippen
        });
      }
    }

    // Handle
    if (wall.handleType === 'rond' && pricing.handles?.rond) {
      breakdown.push({
        label: 'Ronde handgreep',
        price: pricing.handles.rond.price
      });
    }

    // Montage
    if (wall.hasMontage) {
      breakdown.push({
        label: 'Professionele montage',
        price: pricing.montage
      });
    }

    return breakdown;
  },

  // Reset configuration
  reset: () => {
    const state = get();
    set({
      currentStep: 0,
      activeWallIndex: 0,
      walls: [
        {
          id: 1,
          width: state.settings?.dimensions?.width?.default || 3000,
          height: state.settings?.dimensions?.height?.default || 2200,
          trackCount: 3,
          frameColor: 'RAL9005',
          glassType: 'helder',
          design: 'standard',
          steellookType: null,
          hasUProfiles: false,
          hasFunderingskoker: false,
          hasHardhoutenPalen: false,
          meeneemersType: null,
          hasTochtstrippen: false,
          handleType: 'rechthoek'
        }
      ],
      globalOptions: {
        hasMontage: false
      },
      customer: {
        name: '',
        email: '',
        phone: '',
        message: ''
      }
    });
  }
}));

export default useConfigStore;
