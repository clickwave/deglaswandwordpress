<?php
/**
 * Plugin Name: Clickwave Glass Configurator
 * Plugin URI: https://clickwave.nl
 * Description: 3D Glass Sliding Wall Configurator with quote system
 * Version: 1.0.0
 * Author: Clickwave
 * Author URI: https://clickwave.nl
 * Text Domain: clickwave-glass
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('CGC_VERSION', '1.0.0');
define('CGC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CGC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include files
require_once CGC_PLUGIN_DIR . 'includes/class-cpt-offerte.php';
require_once CGC_PLUGIN_DIR . 'includes/class-rest-api.php';
require_once CGC_PLUGIN_DIR . 'includes/class-shortcode.php';
require_once CGC_PLUGIN_DIR . 'includes/class-email-handler.php';
require_once CGC_PLUGIN_DIR . 'includes/class-admin-settings.php';
require_once CGC_PLUGIN_DIR . 'includes/class-customer-portal.php';
require_once CGC_PLUGIN_DIR . 'includes/class-product-library.php';
require_once CGC_PLUGIN_DIR . 'includes/class-company-settings.php';
require_once CGC_PLUGIN_DIR . 'includes/class-offerte-line-items.php';
require_once CGC_PLUGIN_DIR . 'includes/class-pdf-generator.php';
require_once CGC_PLUGIN_DIR . 'includes/class-crm.php';

// Initialize plugin
add_action('plugins_loaded', 'cgc_init_plugin');

/**
 * Initialize plugin components
 */
function cgc_init_plugin() {
    CGC_CPT_Offerte::init();
    CGC_REST_API::init();
    CGC_Shortcode::init();
    CGC_Admin_Settings::init();
    CGC_Customer_Portal::init();
    CGC_Product_Library::init();
    CGC_Company_Settings::init();
    CGC_Offerte_Line_Items::init();
    CGC_PDF_Generator::init();
    CGC_CRM::init();
}

/**
 * Add custom user role for customers
 */
function cgc_add_customer_role() {
    add_role(
        'customer',
        'Klant',
        array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
        )
    );
}
register_activation_hook(__FILE__, 'cgc_add_customer_role');

// Activation hook
register_activation_hook(__FILE__, 'cgc_activate_plugin');

/**
 * Plugin activation
 */
function cgc_activate_plugin() {
    // Register CPT
    CGC_CPT_Offerte::register_post_type();

    // Flush rewrite rules
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'cgc_deactivate_plugin');

/**
 * Plugin deactivation
 */
function cgc_deactivate_plugin() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
