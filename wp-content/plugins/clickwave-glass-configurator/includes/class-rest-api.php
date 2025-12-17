<?php
/**
 * REST API Handler for Quote Submissions
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

class CGC_REST_API {

    /**
     * API namespace
     */
    const NAMESPACE = 'clickwave-glass/v1';

    /**
     * Initialize REST API
     */
    public static function init() {
        add_action('rest_api_init', array(__CLASS__, 'register_routes'));
    }

    /**
     * Register REST API routes
     */
    public static function register_routes() {
        register_rest_route(self::NAMESPACE, '/quote', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array(__CLASS__, 'create_quote'),
            'permission_callback' => '__return_true', // Allow public access for now
            'args'                => self::get_quote_schema(),
        ));

        register_rest_route(self::NAMESPACE, '/nonce', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array(__CLASS__, 'get_nonce'),
            'permission_callback' => '__return_true',
        ));
    }

    /**
     * Get fresh nonce (for SPA refresh)
     */
    public static function get_nonce() {
        return rest_ensure_response(array(
            'nonce' => wp_create_nonce('cgc_quote_nonce'),
        ));
    }

    /**
     * Verify nonce for security
     */
    public static function verify_nonce($request) {
        $nonce = $request->get_header('X-WP-Nonce');

        if (!$nonce) {
            return new WP_Error(
                'missing_nonce',
                __('Nonce is missing', 'clickwave-glass'),
                array('status' => 403)
            );
        }

        $verified = wp_verify_nonce($nonce, 'cgc_quote_nonce');

        if (!$verified) {
            return new WP_Error(
                'invalid_nonce',
                __('Invalid nonce', 'clickwave-glass'),
                array('status' => 403)
            );
        }

        return true;
    }

    /**
     * Create quote endpoint handler
     */
    public static function create_quote($request) {
        // Get parameters
        $params = $request->get_json_params();

        // Validate required fields
        $required_fields = array(
            'width', 'height', 'trackCount', 'frameColor',
            'glassType', 'design', 'handleType', 'priceEstimate',
            'customerName', 'customerEmail'
        );

        foreach ($required_fields as $field) {
            if (empty($params[$field])) {
                return new WP_Error(
                    'missing_field',
                    sprintf(__('Required field missing: %s', 'clickwave-glass'), $field),
                    array('status' => 400)
                );
            }
        }

        // Validate email
        if (!is_email($params['customerEmail'])) {
            return new WP_Error(
                'invalid_email',
                __('Invalid email address', 'clickwave-glass'),
                array('status' => 400)
            );
        }

        // Validate dimensions
        if ($params['width'] < 1000 || $params['width'] > 10000) {
            return new WP_Error(
                'invalid_width',
                __('Width must be between 1000 and 10000 mm', 'clickwave-glass'),
                array('status' => 400)
            );
        }

        if ($params['height'] < 1000 || $params['height'] > 3000) {
            return new WP_Error(
                'invalid_height',
                __('Height must be between 1000 and 3000 mm', 'clickwave-glass'),
                array('status' => 400)
            );
        }

        // Create post title
        $post_title = sprintf(
            '%s - %s',
            sanitize_text_field($params['customerName']),
            date_i18n('d-m-Y H:i')
        );

        // Create offerte post
        $post_data = array(
            'post_title'   => $post_title,
            'post_status'  => 'publish',
            'post_type'    => 'offerte',
            'post_author'  => 1, // Admin user
        );

        $post_id = wp_insert_post($post_data, true);

        if (is_wp_error($post_id)) {
            return new WP_Error(
                'create_failed',
                __('Failed to create quote', 'clickwave-glass'),
                array('status' => 500)
            );
        }

        // Save meta data
        CGC_CPT_Offerte::save_offerte_data($post_id, $params);

        // Create customer account (temporarily disabled for testing)
        $user_id = false;
        /* Temporarily disabled
        if (class_exists('CGC_Customer_Portal')) {
            try {
                $user_id = CGC_Customer_Portal::create_customer_account(
                    $params['customerEmail'],
                    $params['customerName'],
                    $post_id
                );
            } catch (Exception $e) {
                error_log('CGC: Failed to create customer account: ' . $e->getMessage());
            }
        } else {
            error_log('CGC: CGC_Customer_Portal class not found');
        }
        */

        // Send emails
        $email_handler = new CGC_Email_Handler();
        $admin_sent = $email_handler->send_admin_notification($post_id);
        $customer_sent = $email_handler->send_customer_confirmation($post_id);

        // Log if emails failed (but don't fail the request)
        if (!$admin_sent) {
            error_log('CGC: Failed to send admin notification email for offerte #' . $post_id);
        }

        if (!$customer_sent) {
            error_log('CGC: Failed to send customer confirmation email for offerte #' . $post_id);
        }

        // Return success response
        return rest_ensure_response(array(
            'success'     => true,
            'offerte_id'  => $post_id,
            'message'     => __('Quote submitted successfully', 'clickwave-glass'),
            'emails_sent' => array(
                'admin'    => $admin_sent,
                'customer' => $customer_sent,
            ),
        ));
    }

    /**
     * Get quote submission schema
     */
    private static function get_quote_schema() {
        return array(
            'width' => array(
                'required'          => true,
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
                'validate_callback' => function($value) {
                    return $value >= 1000 && $value <= 10000;
                },
            ),
            'height' => array(
                'required'          => true,
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
                'validate_callback' => function($value) {
                    return $value >= 1000 && $value <= 3000;
                },
            ),
            'trackCount' => array(
                'required'          => true,
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
                'validate_callback' => function($value) {
                    return $value >= 2 && $value <= 6;
                },
            ),
            'frameColor' => array(
                'required'          => true,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'glassType' => array(
                'required'          => true,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'design' => array(
                'required'          => true,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'steellookType' => array(
                'required'          => false,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'hasUProfiles' => array(
                'required'          => false,
                'type'              => 'boolean',
            ),
            'hasFunderingskoker' => array(
                'required'          => false,
                'type'              => 'boolean',
            ),
            'hasHardhoutPalen' => array(
                'required'          => false,
                'type'              => 'boolean',
            ),
            'meeneemersType' => array(
                'required'          => false,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'hasTochtstrippen' => array(
                'required'          => false,
                'type'              => 'boolean',
            ),
            'handleType' => array(
                'required'          => true,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'hasMontage' => array(
                'required'          => false,
                'type'              => 'boolean',
            ),
            'priceEstimate' => array(
                'required'          => true,
                'type'              => 'number',
                'sanitize_callback' => function($value) {
                    return floatval($value);
                },
            ),
            'customerName' => array(
                'required'          => true,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'customerEmail' => array(
                'required'          => true,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_email',
                'validate_callback' => 'is_email',
            ),
            'customerPhone' => array(
                'required'          => false,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'customerMessage' => array(
                'required'          => false,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
            ),
        );
    }
}
