<?php
/**
 * Shortcode Handler for Glass Configurator
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

class CGC_Shortcode {

    /**
     * Initialize shortcode
     */
    public static function init() {
        add_shortcode('glass_3d_app', array(__CLASS__, 'render_shortcode'));

        // Register as a block for FSE compatibility
        add_action('init', array(__CLASS__, 'register_block'));

        // Always enqueue on configurator pages
        add_action('wp_enqueue_scripts', array(__CLASS__, 'maybe_enqueue_assets'));
    }

    /**
     * Register as a dynamic block for FSE templates
     */
    public static function register_block() {
        register_block_type('clickwave/glass-configurator', array(
            'render_callback' => array(__CLASS__, 'render_shortcode'),
            'attributes' => array(
                'height' => array(
                    'type' => 'string',
                    'default' => '80vh'
                )
            )
        ));
    }

    /**
     * Enqueue assets on pages that need them
     */
    public static function maybe_enqueue_assets() {
        global $post;

        // Check if we're on a page with the shortcode or configurator template
        if (is_page() && $post) {
            $has_shortcode = has_shortcode($post->post_content, 'glass_3d_app');
            $has_block = has_block('clickwave/glass-configurator', $post);
            $is_configurator_template = get_page_template_slug($post->ID) === 'page-configurator';

            // Also check if URL contains 'configurator'
            $is_configurator_url = strpos($_SERVER['REQUEST_URI'], 'configurator') !== false;

            if ($has_shortcode || $has_block || $is_configurator_template || $is_configurator_url) {
                self::enqueue_assets();
                self::localize_script();
            }
        }
    }

    /**
     * Localize script with config data
     */
    private static function localize_script() {
        wp_localize_script('cgc-configurator', 'cgcConfig', array(
            'restUrl'     => rest_url('clickwave-glass/v1'),
            'nonce'       => wp_create_nonce('cgc_quote_nonce'),
            'homeUrl'     => home_url('/'),
            'assetsUrl'   => CGC_PLUGIN_URL . 'assets/',
            'translations' => array(
                'submitSuccess' => __('Uw offerte aanvraag is succesvol verzonden!', 'clickwave-glass'),
                'submitError'   => __('Er is een fout opgetreden. Probeer het opnieuw.', 'clickwave-glass'),
            ),
        ));
    }

    /**
     * Render shortcode
     */
    public static function render_shortcode($atts) {
        // Parse attributes
        $atts = shortcode_atts(array(
            'height' => '80vh',
        ), $atts, 'glass_3d_app');

        // Enqueue scripts and styles (also handles localization)
        self::enqueue_assets();
        self::localize_script();

        // Return container HTML
        $height = esc_attr($atts['height']);
        ob_start();
        ?>
        <div id="glass-configurator-root" style="height: <?php echo $height; ?>; min-height: 600px;">
            <div class="cgc-loading">
                <p><?php _e('Configurator laden...', 'clickwave-glass'); ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Enqueue scripts and styles
     */
    private static function enqueue_assets() {
        // Prevent double enqueue
        static $enqueued = false;
        if ($enqueued) {
            return;
        }
        $enqueued = true;

        // Enqueue CSS (Vite outputs to assets/js/ directory)
        wp_enqueue_style(
            'cgc-configurator',
            CGC_PLUGIN_URL . 'assets/js/configurator.css',
            array(),
            CGC_VERSION
        );

        // Check if Vite build exists, otherwise use development mode
        $js_file = CGC_PLUGIN_DIR . 'assets/js/configurator.js';

        if (file_exists($js_file)) {
            // Production build - needs type="module" for ES modules
            wp_enqueue_script(
                'cgc-configurator',
                CGC_PLUGIN_URL . 'assets/js/configurator.js',
                array(),
                CGC_VERSION,
                true
            );

            // Add type="module" for ES module support
            add_filter('script_loader_tag', array(__CLASS__, 'add_module_type'), 10, 2);
        } else {
            // Development mode - load from Vite dev server
            wp_enqueue_script(
                'cgc-configurator',
                'http://localhost:5173/src/index.jsx',
                array(),
                null,
                true
            );

            // Add type="module" attribute for Vite
            add_filter('script_loader_tag', array(__CLASS__, 'add_module_type'), 10, 2);
        }
    }

    /**
     * Add type="module" to script tag
     */
    public static function add_module_type($tag, $handle) {
        if ('cgc-configurator' === $handle) {
            $tag = str_replace(' src', ' type="module" src', $tag);
        }
        return $tag;
    }
}
