<?php
/**
 * Email Handler for Quote Notifications
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

class CGC_Email_Handler {

    /**
     * Send admin notification email
     */
    public function send_admin_notification($offerte_id) {
        // Get admin email
        $admin_email = get_option('admin_email');

        // Get offerte data
        $offerte_data = $this->get_offerte_data($offerte_id);

        if (!$offerte_data) {
            return false;
        }

        // Email subject
        $subject = sprintf(
            __('[De Glaswand] Nieuwe offerte aanvraag - %s', 'clickwave-glass'),
            $offerte_data['customer_name']
        );

        // Get email template
        ob_start();
        // Make $offerte_data available in template scope
        extract(array('offerte_data' => $offerte_data));
        include CGC_PLUGIN_DIR . 'templates/email/admin-notification.php';
        $message = ob_get_clean();

        // Email headers
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: De Glaswand <' . $admin_email . '>',
            'Reply-To: ' . $offerte_data['customer_email'],
        );

        // Send email
        return wp_mail($admin_email, $subject, $message, $headers);
    }

    /**
     * Send customer confirmation email
     */
    public function send_customer_confirmation($offerte_id) {
        // Get offerte data
        $offerte_data = $this->get_offerte_data($offerte_id);

        if (!$offerte_data) {
            return false;
        }

        // Email subject
        $subject = __('Uw offerte aanvraag bij De Glaswand', 'clickwave-glass');

        // Get email template
        ob_start();
        // Make $offerte_data available in template scope
        extract(array('offerte_data' => $offerte_data));
        include CGC_PLUGIN_DIR . 'templates/email/customer-confirmation.php';
        $message = ob_get_clean();

        // Email headers
        $admin_email = get_option('admin_email');
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: De Glaswand <' . $admin_email . '>',
            'Reply-To: ' . $admin_email,
        );

        // Send email
        return wp_mail($offerte_data['customer_email'], $subject, $message, $headers);
    }

    /**
     * Get offerte data
     */
    private function get_offerte_data($offerte_id) {
        $post = get_post($offerte_id);

        if (!$post || $post->post_type !== 'offerte') {
            return false;
        }

        // Get all meta data
        $data = array(
            'offerte_id'         => $offerte_id,
            'offerte_title'      => $post->post_title,
            'date'               => get_the_date('d-m-Y H:i', $offerte_id),
            'edit_link'          => admin_url('post.php?post=' . $offerte_id . '&action=edit'),

            // Configuration
            'width'              => get_post_meta($offerte_id, '_cgc_width', true),
            'height'             => get_post_meta($offerte_id, '_cgc_height', true),
            'track_count'        => get_post_meta($offerte_id, '_cgc_track_count', true),
            'frame_color'        => get_post_meta($offerte_id, '_cgc_frame_color', true),
            'glass_type'         => get_post_meta($offerte_id, '_cgc_glass_type', true),
            'design'             => get_post_meta($offerte_id, '_cgc_design', true),
            'steellook_type'     => get_post_meta($offerte_id, '_cgc_steellook_type', true),
            'has_u_profiles'     => get_post_meta($offerte_id, '_cgc_has_u_profiles', true),
            'has_funderingskoker' => get_post_meta($offerte_id, '_cgc_has_funderingskoker', true),
            'has_hardhout_palen' => get_post_meta($offerte_id, '_cgc_has_hardhout_palen', true),
            'meeneemers_type'    => get_post_meta($offerte_id, '_cgc_meeneemers_type', true),
            'has_tochtstrippen'  => get_post_meta($offerte_id, '_cgc_has_tochtstrippen', true),
            'handle_type'        => get_post_meta($offerte_id, '_cgc_handle_type', true),
            'has_montage'        => get_post_meta($offerte_id, '_cgc_has_montage', true),
            'price_estimate'     => get_post_meta($offerte_id, '_cgc_price_estimate', true),

            // Customer data
            'customer_name'      => get_post_meta($offerte_id, '_cgc_customer_name', true),
            'customer_email'     => get_post_meta($offerte_id, '_cgc_customer_email', true),
            'customer_phone'     => get_post_meta($offerte_id, '_cgc_customer_phone', true),
            'customer_message'   => get_post_meta($offerte_id, '_cgc_customer_message', true),
        );

        return $data;
    }

    /**
     * Format boolean for display
     */
    private function format_boolean($value) {
        return $value ? __('Ja', 'clickwave-glass') : __('Nee', 'clickwave-glass');
    }
}
