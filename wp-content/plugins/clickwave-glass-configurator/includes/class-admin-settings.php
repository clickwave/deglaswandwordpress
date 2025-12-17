<?php
/**
 * Admin Settings Page for Glass Configurator
 * Allows website owner to manage all product options and pricing
 */

if (!defined('ABSPATH')) {
    exit;
}

class CGC_Admin_Settings {

    /**
     * Option name for storing all settings
     */
    const OPTION_NAME = 'cgc_configurator_settings';

    /**
     * Initialize admin settings
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        add_action('admin_init', array(__CLASS__, 'register_settings'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_scripts'));
        add_action('rest_api_init', array(__CLASS__, 'register_settings_endpoint'));
    }

    /**
     * Add admin menu page
     */
    public static function add_admin_menu() {
        add_menu_page(
            __('Glaswand Configurator', 'clickwave-glass'),
            __('Glaswand Config', 'clickwave-glass'),
            'manage_options',
            'cgc-settings',
            array(__CLASS__, 'render_settings_page'),
            'dashicons-grid-view',
            30
        );

        add_submenu_page(
            'cgc-settings',
            __('Instellingen', 'clickwave-glass'),
            __('Instellingen', 'clickwave-glass'),
            'manage_options',
            'cgc-settings',
            array(__CLASS__, 'render_settings_page')
        );

        add_submenu_page(
            'cgc-settings',
            __('Prijzen', 'clickwave-glass'),
            __('Prijzen', 'clickwave-glass'),
            'manage_options',
            'cgc-pricing',
            array(__CLASS__, 'render_pricing_page')
        );

        add_submenu_page(
            'cgc-settings',
            __('Opties', 'clickwave-glass'),
            __('Opties', 'clickwave-glass'),
            'manage_options',
            'cgc-options',
            array(__CLASS__, 'render_options_page')
        );
    }

    /**
     * Register settings
     */
    public static function register_settings() {
        register_setting('cgc_settings_group', self::OPTION_NAME, array(
            'sanitize_callback' => array(__CLASS__, 'sanitize_settings'),
            'default' => self::get_default_settings()
        ));
    }

    /**
     * Register REST API endpoint for frontend
     */
    public static function register_settings_endpoint() {
        register_rest_route('clickwave-glass/v1', '/settings', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_frontend_settings'),
            'permission_callback' => '__return_true'
        ));
    }

    /**
     * Get settings for frontend (public, no sensitive data)
     */
    public static function get_frontend_settings() {
        $settings = self::get_settings();

        return rest_ensure_response(array(
            'dimensions' => $settings['dimensions'],
            'pricing' => $settings['pricing'],
            'options' => $settings['options'],
            'steps' => $settings['wizard_steps'],
            'labels' => $settings['labels']
        ));
    }

    /**
     * Enqueue admin scripts and styles
     */
    public static function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'cgc-') === false) {
            return;
        }

        wp_enqueue_style(
            'cgc-admin-styles',
            CGC_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            CGC_VERSION
        );

        wp_enqueue_script(
            'cgc-admin-scripts',
            CGC_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            CGC_VERSION,
            true
        );

        wp_localize_script('cgc-admin-scripts', 'cgcAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cgc_admin_nonce'),
            'strings' => array(
                'saved' => __('Instellingen opgeslagen!', 'clickwave-glass'),
                'error' => __('Er is een fout opgetreden.', 'clickwave-glass'),
                'confirm_delete' => __('Weet je zeker dat je dit wilt verwijderen?', 'clickwave-glass')
            )
        ));
    }

    /**
     * Get default settings
     */
    public static function get_default_settings() {
        return array(
            // Dimension limits
            'dimensions' => array(
                'width' => array(
                    'min' => 1500,
                    'max' => 6000,
                    'default' => 3000,
                    'step' => 100
                ),
                'height' => array(
                    'min' => 1800,
                    'max' => 3000,
                    'default' => 2200,
                    'step' => 100
                )
            ),

            // Pricing structure based on flowchart
            'pricing' => array(
                // Base price calculation method
                'base_price_method' => 'per_m2', // 'per_m2' or 'per_panel' or 'fixed'
                'base_price_per_m2' => 150,
                'base_price_per_panel' => 299.99,

                // Frame colors - geen prijsverschil volgens flowchart
                'frame_colors' => array(
                    'RAL9005' => array(
                        'name' => 'Zwart RAL9005',
                        'price' => 0,
                        'hex' => '#0A0A0A'
                    ),
                    'RAL7016' => array(
                        'name' => 'Antraciet RAL7016',
                        'price' => 0,
                        'hex' => '#383E42'
                    )
                ),

                // Glass types
                'glass_types' => array(
                    'helder' => array(
                        'name' => 'Helder glas',
                        'price_per_panel' => 0
                    ),
                    'getint' => array(
                        'name' => 'Getint glas',
                        'price_per_panel' => 50
                    )
                ),

                // Rail/track pricing - based on flowchart tables
                'rails' => array(
                    3 => array(
                        'min_width' => 1500,
                        'max_width' => 3300,
                        'u_profiles' => 59.99,
                        'funderingskoker' => 124.99,
                        'meeneemers' => 49.99,
                        'tochtstrippen' => 29.99
                    ),
                    4 => array(
                        'min_width' => 2000,
                        'max_width' => 4400,
                        'u_profiles' => 74.99,
                        'funderingskoker' => 149.99,
                        'meeneemers' => 59.99,
                        'tochtstrippen' => 39.99
                    ),
                    5 => array(
                        'min_width' => 2500,
                        'max_width' => 5500,
                        'u_profiles' => 89.99,
                        'funderingskoker' => 174.99,
                        'meeneemers' => 69.99,
                        'tochtstrippen' => 49.99
                    ),
                    6 => array(
                        'min_width' => 3000,
                        'max_width' => 6600,
                        'u_profiles' => 104.99,
                        'funderingskoker' => 199.99,
                        'meeneemers' => 79.99,
                        'tochtstrippen' => 59.99
                    )
                ),

                // Steellook designs
                'steellook' => array(
                    'amsterdam' => array(
                        'name' => 'Steellook Amsterdam',
                        'price_per_panel' => 99.99,
                        'description' => '1 horizontale balk'
                    ),
                    'barcelona' => array(
                        'name' => 'Steellook Barcelona',
                        'price_per_panel' => 169.99,
                        'description' => '2 horizontale balken'
                    ),
                    'cairo' => array(
                        'name' => 'Steellook Cairo',
                        'price_per_panel' => 169.99,
                        'description' => '3 horizontale balken'
                    ),
                    'dublin' => array(
                        'name' => 'Steellook Dublin',
                        'price_per_panel' => 199.99,
                        'description' => 'Grid patroon'
                    )
                ),

                // Hardhout palen addon for funderingskoker
                'hardhout_palen' => 99.99,

                // Handle types
                'handles' => array(
                    'rechthoek' => array(
                        'name' => 'Rechthoek',
                        'price' => 0
                    ),
                    'rond' => array(
                        'name' => 'Rond',
                        'price' => 49.99
                    )
                ),

                // Montage
                'montage' => 899
            ),

            // Product options toggles
            'options' => array(
                'show_frame_color' => true,
                'show_glass_type' => true,
                'show_design' => true,
                'show_u_profiles' => true,
                'show_funderingskoker' => true,
                'show_hardhout_palen' => true,
                'show_meeneemers' => true,
                'show_tochtstrippen' => true,
                'show_handles' => true,
                'show_montage' => true,
                'show_extra_wall_option' => true // "Hier optie om nog een wand samen te stellen"
            ),

            // Wizard steps configuration
            'wizard_steps' => array(
                array(
                    'id' => 'dimensions',
                    'title' => 'Afmetingen',
                    'description' => 'Bepaal de breedte en hoogte van uw glaswand',
                    'icon' => 'ruler'
                ),
                array(
                    'id' => 'rails',
                    'title' => 'Aantal rails',
                    'description' => 'Kies het aantal schuifpanelen',
                    'icon' => 'layers'
                ),
                array(
                    'id' => 'style',
                    'title' => 'Kleur & Glas',
                    'description' => 'Kies uw profielkleur en glastype',
                    'icon' => 'palette'
                ),
                array(
                    'id' => 'design',
                    'title' => 'Design',
                    'description' => 'Standaard of Steellook uitvoering',
                    'icon' => 'grid'
                ),
                array(
                    'id' => 'accessories',
                    'title' => 'Accessoires',
                    'description' => 'U-profielen, funderingskoker en meer',
                    'icon' => 'plus-circle'
                ),
                array(
                    'id' => 'finish',
                    'title' => 'Afwerking',
                    'description' => 'Handgrepen en montage',
                    'icon' => 'check-circle'
                ),
                array(
                    'id' => 'summary',
                    'title' => 'Overzicht',
                    'description' => 'Controleer uw configuratie',
                    'icon' => 'clipboard'
                )
            ),

            // UI Labels (translatable)
            'labels' => array(
                'next' => 'Volgende',
                'previous' => 'Vorige',
                'finish' => 'Offerte aanvragen',
                'total_price' => 'Totaalprijs',
                'vat_notice' => 'Prijzen zijn inclusief BTW',
                'add_another' => 'Nog een wand toevoegen',
                'remove_wall' => 'Wand verwijderen'
            ),

            // Email settings
            'email' => array(
                'admin_email' => get_option('admin_email'),
                'from_name' => get_bloginfo('name'),
                'from_email' => get_option('admin_email'),
                'cc_emails' => '',
                'bcc_emails' => ''
            )
        );
    }

    /**
     * Get current settings with defaults
     */
    public static function get_settings() {
        $defaults = self::get_default_settings();
        $settings = get_option(self::OPTION_NAME, array());
        return wp_parse_args($settings, $defaults);
    }

    /**
     * Sanitize settings
     */
    public static function sanitize_settings($input) {
        $sanitized = array();

        // Sanitize dimensions
        if (isset($input['dimensions'])) {
            $sanitized['dimensions'] = array(
                'width' => array(
                    'min' => absint($input['dimensions']['width']['min'] ?? 1500),
                    'max' => absint($input['dimensions']['width']['max'] ?? 6000),
                    'default' => absint($input['dimensions']['width']['default'] ?? 3000),
                    'step' => absint($input['dimensions']['width']['step'] ?? 100)
                ),
                'height' => array(
                    'min' => absint($input['dimensions']['height']['min'] ?? 1800),
                    'max' => absint($input['dimensions']['height']['max'] ?? 3000),
                    'default' => absint($input['dimensions']['height']['default'] ?? 2200),
                    'step' => absint($input['dimensions']['height']['step'] ?? 100)
                )
            );
        }

        // Sanitize pricing
        if (isset($input['pricing'])) {
            $sanitized['pricing'] = self::sanitize_pricing($input['pricing']);
        }

        // Sanitize options
        if (isset($input['options'])) {
            $sanitized['options'] = array_map('rest_sanitize_boolean', $input['options']);
        }

        // Sanitize wizard steps
        if (isset($input['wizard_steps'])) {
            $sanitized['wizard_steps'] = array_map(function($step) {
                return array(
                    'id' => sanitize_key($step['id'] ?? ''),
                    'title' => sanitize_text_field($step['title'] ?? ''),
                    'description' => sanitize_text_field($step['description'] ?? ''),
                    'icon' => sanitize_text_field($step['icon'] ?? '')
                );
            }, $input['wizard_steps']);
        }

        // Sanitize labels
        if (isset($input['labels'])) {
            $sanitized['labels'] = array_map('sanitize_text_field', $input['labels']);
        }

        // Sanitize email settings
        if (isset($input['email'])) {
            $sanitized['email'] = array(
                'admin_email' => sanitize_email($input['email']['admin_email'] ?? ''),
                'from_name' => sanitize_text_field($input['email']['from_name'] ?? ''),
                'from_email' => sanitize_email($input['email']['from_email'] ?? ''),
                'cc_emails' => sanitize_text_field($input['email']['cc_emails'] ?? ''),
                'bcc_emails' => sanitize_text_field($input['email']['bcc_emails'] ?? '')
            );
        }

        return $sanitized;
    }

    /**
     * Sanitize pricing array
     */
    private static function sanitize_pricing($pricing) {
        $sanitized = array();

        $sanitized['base_price_method'] = sanitize_key($pricing['base_price_method'] ?? 'per_m2');
        $sanitized['base_price_per_m2'] = floatval($pricing['base_price_per_m2'] ?? 150);
        $sanitized['base_price_per_panel'] = floatval($pricing['base_price_per_panel'] ?? 299.99);

        // Frame colors
        if (isset($pricing['frame_colors'])) {
            foreach ($pricing['frame_colors'] as $key => $color) {
                $sanitized['frame_colors'][sanitize_key($key)] = array(
                    'name' => sanitize_text_field($color['name'] ?? ''),
                    'price' => floatval($color['price'] ?? 0),
                    'hex' => sanitize_hex_color($color['hex'] ?? '#000000')
                );
            }
        }

        // Glass types
        if (isset($pricing['glass_types'])) {
            foreach ($pricing['glass_types'] as $key => $type) {
                $sanitized['glass_types'][sanitize_key($key)] = array(
                    'name' => sanitize_text_field($type['name'] ?? ''),
                    'price_per_panel' => floatval($type['price_per_panel'] ?? 0)
                );
            }
        }

        // Rails
        if (isset($pricing['rails'])) {
            foreach ($pricing['rails'] as $count => $rail) {
                $sanitized['rails'][absint($count)] = array(
                    'min_width' => absint($rail['min_width'] ?? 1500),
                    'max_width' => absint($rail['max_width'] ?? 6000),
                    'u_profiles' => floatval($rail['u_profiles'] ?? 0),
                    'funderingskoker' => floatval($rail['funderingskoker'] ?? 0),
                    'meeneemers' => floatval($rail['meeneemers'] ?? 0),
                    'tochtstrippen' => floatval($rail['tochtstrippen'] ?? 0)
                );
            }
        }

        // Steellook
        if (isset($pricing['steellook'])) {
            foreach ($pricing['steellook'] as $key => $style) {
                $sanitized['steellook'][sanitize_key($key)] = array(
                    'name' => sanitize_text_field($style['name'] ?? ''),
                    'price_per_panel' => floatval($style['price_per_panel'] ?? 0),
                    'description' => sanitize_text_field($style['description'] ?? '')
                );
            }
        }

        $sanitized['hardhout_palen'] = floatval($pricing['hardhout_palen'] ?? 99.99);

        // Handles
        if (isset($pricing['handles'])) {
            foreach ($pricing['handles'] as $key => $handle) {
                $sanitized['handles'][sanitize_key($key)] = array(
                    'name' => sanitize_text_field($handle['name'] ?? ''),
                    'price' => floatval($handle['price'] ?? 0)
                );
            }
        }

        $sanitized['montage'] = floatval($pricing['montage'] ?? 899);

        return $sanitized;
    }

    /**
     * Render main settings page
     */
    public static function render_settings_page() {
        $settings = self::get_settings();
        ?>
        <div class="wrap cgc-admin-wrap">
            <h1><?php _e('Glaswand Configurator Instellingen', 'clickwave-glass'); ?></h1>

            <div class="cgc-admin-header">
                <p><?php _e('Beheer alle instellingen voor de 3D glaswand configurator.', 'clickwave-glass'); ?></p>
            </div>

            <div class="cgc-admin-cards">
                <div class="cgc-admin-card">
                    <h2><?php _e('Prijzen Beheren', 'clickwave-glass'); ?></h2>
                    <p><?php _e('Stel basisprijzen, optieprijzen en kortingen in.', 'clickwave-glass'); ?></p>
                    <a href="<?php echo admin_url('admin.php?page=cgc-pricing'); ?>" class="button button-primary">
                        <?php _e('Naar Prijzen', 'clickwave-glass'); ?>
                    </a>
                </div>

                <div class="cgc-admin-card">
                    <h2><?php _e('Product Opties', 'clickwave-glass'); ?></h2>
                    <p><?php _e('Schakel opties in/uit en pas beschrijvingen aan.', 'clickwave-glass'); ?></p>
                    <a href="<?php echo admin_url('admin.php?page=cgc-options'); ?>" class="button button-primary">
                        <?php _e('Naar Opties', 'clickwave-glass'); ?>
                    </a>
                </div>

                <div class="cgc-admin-card">
                    <h2><?php _e('Offertes', 'clickwave-glass'); ?></h2>
                    <p><?php _e('Bekijk alle binnengekomen offerte aanvragen.', 'clickwave-glass'); ?></p>
                    <a href="<?php echo admin_url('edit.php?post_type=offerte'); ?>" class="button button-primary">
                        <?php _e('Naar Offertes', 'clickwave-glass'); ?>
                    </a>
                </div>
            </div>

            <form method="post" action="options.php" class="cgc-settings-form">
                <?php settings_fields('cgc_settings_group'); ?>

                <h2><?php _e('Algemene Instellingen', 'clickwave-glass'); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Afmetingen', 'clickwave-glass'); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php _e('Breedte instellingen', 'clickwave-glass'); ?></legend>
                                <p><strong><?php _e('Breedte (mm)', 'clickwave-glass'); ?></strong></p>
                                <label>
                                    <?php _e('Min:', 'clickwave-glass'); ?>
                                    <input type="number" name="<?php echo self::OPTION_NAME; ?>[dimensions][width][min]"
                                           value="<?php echo esc_attr($settings['dimensions']['width']['min']); ?>"
                                           class="small-text">
                                </label>
                                <label>
                                    <?php _e('Max:', 'clickwave-glass'); ?>
                                    <input type="number" name="<?php echo self::OPTION_NAME; ?>[dimensions][width][max]"
                                           value="<?php echo esc_attr($settings['dimensions']['width']['max']); ?>"
                                           class="small-text">
                                </label>
                                <label>
                                    <?php _e('Standaard:', 'clickwave-glass'); ?>
                                    <input type="number" name="<?php echo self::OPTION_NAME; ?>[dimensions][width][default]"
                                           value="<?php echo esc_attr($settings['dimensions']['width']['default']); ?>"
                                           class="small-text">
                                </label>

                                <p><strong><?php _e('Hoogte (mm)', 'clickwave-glass'); ?></strong></p>
                                <label>
                                    <?php _e('Min:', 'clickwave-glass'); ?>
                                    <input type="number" name="<?php echo self::OPTION_NAME; ?>[dimensions][height][min]"
                                           value="<?php echo esc_attr($settings['dimensions']['height']['min']); ?>"
                                           class="small-text">
                                </label>
                                <label>
                                    <?php _e('Max:', 'clickwave-glass'); ?>
                                    <input type="number" name="<?php echo self::OPTION_NAME; ?>[dimensions][height][max]"
                                           value="<?php echo esc_attr($settings['dimensions']['height']['max']); ?>"
                                           class="small-text">
                                </label>
                                <label>
                                    <?php _e('Standaard:', 'clickwave-glass'); ?>
                                    <input type="number" name="<?php echo self::OPTION_NAME; ?>[dimensions][height][default]"
                                           value="<?php echo esc_attr($settings['dimensions']['height']['default']); ?>"
                                           class="small-text">
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                </table>

                <h2><?php _e('Email Instellingen', 'clickwave-glass'); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Admin Email', 'clickwave-glass'); ?></th>
                        <td>
                            <input type="email" name="<?php echo self::OPTION_NAME; ?>[email][admin_email]"
                                   value="<?php echo esc_attr($settings['email']['admin_email']); ?>"
                                   class="regular-text">
                            <p class="description"><?php _e('Offerte notificaties worden naar dit adres gestuurd.', 'clickwave-glass'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Afzender Naam', 'clickwave-glass'); ?></th>
                        <td>
                            <input type="text" name="<?php echo self::OPTION_NAME; ?>[email][from_name]"
                                   value="<?php echo esc_attr($settings['email']['from_name']); ?>"
                                   class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('CC Emails', 'clickwave-glass'); ?></th>
                        <td>
                            <input type="text" name="<?php echo self::OPTION_NAME; ?>[email][cc_emails]"
                                   value="<?php echo esc_attr($settings['email']['cc_emails']); ?>"
                                   class="regular-text">
                            <p class="description"><?php _e('Komma-gescheiden lijst van extra ontvangers.', 'clickwave-glass'); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Instellingen Opslaan', 'clickwave-glass')); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render pricing page
     */
    public static function render_pricing_page() {
        $settings = self::get_settings();
        ?>
        <div class="wrap cgc-admin-wrap">
            <h1><?php _e('Prijzen Beheren', 'clickwave-glass'); ?></h1>

            <form method="post" action="options.php" class="cgc-settings-form">
                <?php settings_fields('cgc_settings_group'); ?>

                <!-- Base Pricing -->
                <h2><?php _e('Basis Prijs', 'clickwave-glass'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Prijsmethode', 'clickwave-glass'); ?></th>
                        <td>
                            <select name="<?php echo self::OPTION_NAME; ?>[pricing][base_price_method]">
                                <option value="per_m2" <?php selected($settings['pricing']['base_price_method'], 'per_m2'); ?>>
                                    <?php _e('Per m²', 'clickwave-glass'); ?>
                                </option>
                                <option value="per_panel" <?php selected($settings['pricing']['base_price_method'], 'per_panel'); ?>>
                                    <?php _e('Per paneel', 'clickwave-glass'); ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Prijs per m²', 'clickwave-glass'); ?></th>
                        <td>
                            <input type="number" step="0.01" name="<?php echo self::OPTION_NAME; ?>[pricing][base_price_per_m2]"
                                   value="<?php echo esc_attr($settings['pricing']['base_price_per_m2']); ?>"
                                   class="small-text"> €
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Prijs per paneel', 'clickwave-glass'); ?></th>
                        <td>
                            <input type="number" step="0.01" name="<?php echo self::OPTION_NAME; ?>[pricing][base_price_per_panel]"
                                   value="<?php echo esc_attr($settings['pricing']['base_price_per_panel']); ?>"
                                   class="small-text"> €
                        </td>
                    </tr>
                </table>

                <!-- Glass Types -->
                <h2><?php _e('Glastypen', 'clickwave-glass'); ?></h2>
                <table class="widefat cgc-pricing-table">
                    <thead>
                        <tr>
                            <th><?php _e('Type', 'clickwave-glass'); ?></th>
                            <th><?php _e('Naam', 'clickwave-glass'); ?></th>
                            <th><?php _e('Meerprijs per paneel', 'clickwave-glass'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($settings['pricing']['glass_types'] as $key => $type): ?>
                        <tr>
                            <td><code><?php echo esc_html($key); ?></code></td>
                            <td>
                                <input type="text" name="<?php echo self::OPTION_NAME; ?>[pricing][glass_types][<?php echo esc_attr($key); ?>][name]"
                                       value="<?php echo esc_attr($type['name']); ?>" class="regular-text">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="<?php echo self::OPTION_NAME; ?>[pricing][glass_types][<?php echo esc_attr($key); ?>][price_per_panel]"
                                       value="<?php echo esc_attr($type['price_per_panel']); ?>" class="small-text"> €
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Rails Pricing -->
                <h2><?php _e('Prijzen per Aantal Rails', 'clickwave-glass'); ?></h2>
                <p class="description"><?php _e('Prijzen voor accessoires variëren per aantal rails/panelen.', 'clickwave-glass'); ?></p>
                <table class="widefat cgc-pricing-table">
                    <thead>
                        <tr>
                            <th><?php _e('Rails', 'clickwave-glass'); ?></th>
                            <th><?php _e('Min Breedte', 'clickwave-glass'); ?></th>
                            <th><?php _e('Max Breedte', 'clickwave-glass'); ?></th>
                            <th><?php _e('U-profielen', 'clickwave-glass'); ?></th>
                            <th><?php _e('Funderingskoker', 'clickwave-glass'); ?></th>
                            <th><?php _e('Meeneemers', 'clickwave-glass'); ?></th>
                            <th><?php _e('Tochtstrippen', 'clickwave-glass'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($settings['pricing']['rails'] as $count => $rail): ?>
                        <tr>
                            <td><strong><?php echo esc_html($count); ?> rails</strong></td>
                            <td>
                                <input type="number" name="<?php echo self::OPTION_NAME; ?>[pricing][rails][<?php echo esc_attr($count); ?>][min_width]"
                                       value="<?php echo esc_attr($rail['min_width']); ?>" class="small-text"> mm
                            </td>
                            <td>
                                <input type="number" name="<?php echo self::OPTION_NAME; ?>[pricing][rails][<?php echo esc_attr($count); ?>][max_width]"
                                       value="<?php echo esc_attr($rail['max_width']); ?>" class="small-text"> mm
                            </td>
                            <td>
                                <input type="number" step="0.01" name="<?php echo self::OPTION_NAME; ?>[pricing][rails][<?php echo esc_attr($count); ?>][u_profiles]"
                                       value="<?php echo esc_attr($rail['u_profiles']); ?>" class="small-text"> €
                            </td>
                            <td>
                                <input type="number" step="0.01" name="<?php echo self::OPTION_NAME; ?>[pricing][rails][<?php echo esc_attr($count); ?>][funderingskoker]"
                                       value="<?php echo esc_attr($rail['funderingskoker']); ?>" class="small-text"> €
                            </td>
                            <td>
                                <input type="number" step="0.01" name="<?php echo self::OPTION_NAME; ?>[pricing][rails][<?php echo esc_attr($count); ?>][meeneemers]"
                                       value="<?php echo esc_attr($rail['meeneemers']); ?>" class="small-text"> €
                            </td>
                            <td>
                                <input type="number" step="0.01" name="<?php echo self::OPTION_NAME; ?>[pricing][rails][<?php echo esc_attr($count); ?>][tochtstrippen]"
                                       value="<?php echo esc_attr($rail['tochtstrippen']); ?>" class="small-text"> €
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Steellook Pricing -->
                <h2><?php _e('Steellook Designs', 'clickwave-glass'); ?></h2>
                <table class="widefat cgc-pricing-table">
                    <thead>
                        <tr>
                            <th><?php _e('Design', 'clickwave-glass'); ?></th>
                            <th><?php _e('Naam', 'clickwave-glass'); ?></th>
                            <th><?php _e('Beschrijving', 'clickwave-glass'); ?></th>
                            <th><?php _e('Prijs per paneel', 'clickwave-glass'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($settings['pricing']['steellook'] as $key => $style): ?>
                        <tr>
                            <td><code><?php echo esc_html($key); ?></code></td>
                            <td>
                                <input type="text" name="<?php echo self::OPTION_NAME; ?>[pricing][steellook][<?php echo esc_attr($key); ?>][name]"
                                       value="<?php echo esc_attr($style['name']); ?>" class="regular-text">
                            </td>
                            <td>
                                <input type="text" name="<?php echo self::OPTION_NAME; ?>[pricing][steellook][<?php echo esc_attr($key); ?>][description]"
                                       value="<?php echo esc_attr($style['description']); ?>" class="regular-text">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="<?php echo self::OPTION_NAME; ?>[pricing][steellook][<?php echo esc_attr($key); ?>][price_per_panel]"
                                       value="<?php echo esc_attr($style['price_per_panel']); ?>" class="small-text"> €
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Other Pricing -->
                <h2><?php _e('Overige Prijzen', 'clickwave-glass'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Hardhout Palen', 'clickwave-glass'); ?></th>
                        <td>
                            <input type="number" step="0.01" name="<?php echo self::OPTION_NAME; ?>[pricing][hardhout_palen]"
                                   value="<?php echo esc_attr($settings['pricing']['hardhout_palen']); ?>"
                                   class="small-text"> €
                            <p class="description"><?php _e('Meerprijs voor hardhout palen bij funderingskoker', 'clickwave-glass'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Handgreep Rond', 'clickwave-glass'); ?></th>
                        <td>
                            <input type="number" step="0.01" name="<?php echo self::OPTION_NAME; ?>[pricing][handles][rond][price]"
                                   value="<?php echo esc_attr($settings['pricing']['handles']['rond']['price']); ?>"
                                   class="small-text"> €
                            <p class="description"><?php _e('Meerprijs voor ronde handgreep (rechthoek is standaard)', 'clickwave-glass'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Montage', 'clickwave-glass'); ?></th>
                        <td>
                            <input type="number" step="0.01" name="<?php echo self::OPTION_NAME; ?>[pricing][montage]"
                                   value="<?php echo esc_attr($settings['pricing']['montage']); ?>"
                                   class="small-text"> €
                            <p class="description"><?php _e('Prijs voor professionele montage', 'clickwave-glass'); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Prijzen Opslaan', 'clickwave-glass')); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render options page
     */
    public static function render_options_page() {
        $settings = self::get_settings();
        ?>
        <div class="wrap cgc-admin-wrap">
            <h1><?php _e('Product Opties', 'clickwave-glass'); ?></h1>

            <form method="post" action="options.php" class="cgc-settings-form">
                <?php settings_fields('cgc_settings_group'); ?>

                <h2><?php _e('Zichtbaarheid Opties', 'clickwave-glass'); ?></h2>
                <p class="description"><?php _e('Schakel opties in of uit in de configurator.', 'clickwave-glass'); ?></p>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Profielkleur', 'clickwave-glass'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo self::OPTION_NAME; ?>[options][show_frame_color]"
                                       value="1" <?php checked($settings['options']['show_frame_color']); ?>>
                                <?php _e('Toon profielkleur keuze', 'clickwave-glass'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Glastype', 'clickwave-glass'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo self::OPTION_NAME; ?>[options][show_glass_type]"
                                       value="1" <?php checked($settings['options']['show_glass_type']); ?>>
                                <?php _e('Toon glastype keuze (helder/getint)', 'clickwave-glass'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Design', 'clickwave-glass'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo self::OPTION_NAME; ?>[options][show_design]"
                                       value="1" <?php checked($settings['options']['show_design']); ?>>
                                <?php _e('Toon design keuze (standaard/steellook)', 'clickwave-glass'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('U-profielen', 'clickwave-glass'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo self::OPTION_NAME; ?>[options][show_u_profiles]"
                                       value="1" <?php checked($settings['options']['show_u_profiles']); ?>>
                                <?php _e('Toon U-profielen optie', 'clickwave-glass'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Funderingskoker', 'clickwave-glass'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo self::OPTION_NAME; ?>[options][show_funderingskoker]"
                                       value="1" <?php checked($settings['options']['show_funderingskoker']); ?>>
                                <?php _e('Toon funderingskoker optie', 'clickwave-glass'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Hardhout Palen', 'clickwave-glass'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo self::OPTION_NAME; ?>[options][show_hardhout_palen]"
                                       value="1" <?php checked($settings['options']['show_hardhout_palen']); ?>>
                                <?php _e('Toon hardhout palen optie (bij funderingskoker)', 'clickwave-glass'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Meeneemers', 'clickwave-glass'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo self::OPTION_NAME; ?>[options][show_meeneemers]"
                                       value="1" <?php checked($settings['options']['show_meeneemers']); ?>>
                                <?php _e('Toon meeneemers optie', 'clickwave-glass'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Tochtstrippen', 'clickwave-glass'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo self::OPTION_NAME; ?>[options][show_tochtstrippen]"
                                       value="1" <?php checked($settings['options']['show_tochtstrippen']); ?>>
                                <?php _e('Toon tochtstrippen optie', 'clickwave-glass'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Handgrepen', 'clickwave-glass'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo self::OPTION_NAME; ?>[options][show_handles]"
                                       value="1" <?php checked($settings['options']['show_handles']); ?>>
                                <?php _e('Toon handgreep keuze', 'clickwave-glass'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Montage', 'clickwave-glass'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo self::OPTION_NAME; ?>[options][show_montage]"
                                       value="1" <?php checked($settings['options']['show_montage']); ?>>
                                <?php _e('Toon montage optie', 'clickwave-glass'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Extra Wand', 'clickwave-glass'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo self::OPTION_NAME; ?>[options][show_extra_wall_option]"
                                       value="1" <?php checked($settings['options']['show_extra_wall_option']); ?>>
                                <?php _e('Toon "Nog een wand toevoegen" optie', 'clickwave-glass'); ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <h2><?php _e('Wizard Stappen', 'clickwave-glass'); ?></h2>
                <p class="description"><?php _e('Configureer de stappen in de configurator wizard.', 'clickwave-glass'); ?></p>

                <table class="widefat cgc-steps-table">
                    <thead>
                        <tr>
                            <th><?php _e('Volgorde', 'clickwave-glass'); ?></th>
                            <th><?php _e('ID', 'clickwave-glass'); ?></th>
                            <th><?php _e('Titel', 'clickwave-glass'); ?></th>
                            <th><?php _e('Beschrijving', 'clickwave-glass'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($settings['wizard_steps'] as $index => $step): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><code><?php echo esc_html($step['id']); ?></code></td>
                            <td>
                                <input type="text" name="<?php echo self::OPTION_NAME; ?>[wizard_steps][<?php echo $index; ?>][title]"
                                       value="<?php echo esc_attr($step['title']); ?>" class="regular-text">
                                <input type="hidden" name="<?php echo self::OPTION_NAME; ?>[wizard_steps][<?php echo $index; ?>][id]"
                                       value="<?php echo esc_attr($step['id']); ?>">
                                <input type="hidden" name="<?php echo self::OPTION_NAME; ?>[wizard_steps][<?php echo $index; ?>][icon]"
                                       value="<?php echo esc_attr($step['icon']); ?>">
                            </td>
                            <td>
                                <input type="text" name="<?php echo self::OPTION_NAME; ?>[wizard_steps][<?php echo $index; ?>][description]"
                                       value="<?php echo esc_attr($step['description']); ?>" class="large-text">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h2><?php _e('Labels', 'clickwave-glass'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Volgende knop', 'clickwave-glass'); ?></th>
                        <td>
                            <input type="text" name="<?php echo self::OPTION_NAME; ?>[labels][next]"
                                   value="<?php echo esc_attr($settings['labels']['next']); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Vorige knop', 'clickwave-glass'); ?></th>
                        <td>
                            <input type="text" name="<?php echo self::OPTION_NAME; ?>[labels][previous]"
                                   value="<?php echo esc_attr($settings['labels']['previous']); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Afronden knop', 'clickwave-glass'); ?></th>
                        <td>
                            <input type="text" name="<?php echo self::OPTION_NAME; ?>[labels][finish]"
                                   value="<?php echo esc_attr($settings['labels']['finish']); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Totaalprijs label', 'clickwave-glass'); ?></th>
                        <td>
                            <input type="text" name="<?php echo self::OPTION_NAME; ?>[labels][total_price]"
                                   value="<?php echo esc_attr($settings['labels']['total_price']); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('BTW melding', 'clickwave-glass'); ?></th>
                        <td>
                            <input type="text" name="<?php echo self::OPTION_NAME; ?>[labels][vat_notice]"
                                   value="<?php echo esc_attr($settings['labels']['vat_notice']); ?>" class="regular-text">
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Opties Opslaan', 'clickwave-glass')); ?>
            </form>
        </div>
        <?php
    }
}
