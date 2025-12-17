<?php
/**
 * Custom Post Type: Offerte (Quote)
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

class CGC_CPT_Offerte {

    /**
     * Initialize the CPT
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_type'));
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post_offerte', array(__CLASS__, 'save_meta'), 10, 2);
    }

    /**
     * Register Custom Post Type
     */
    public static function register_post_type() {
        $labels = array(
            'name'                  => __('Offertes', 'clickwave-glass'),
            'singular_name'         => __('Offerte', 'clickwave-glass'),
            'menu_name'             => __('Glaswand Offertes', 'clickwave-glass'),
            'name_admin_bar'        => __('Offerte', 'clickwave-glass'),
            'add_new'               => __('Nieuwe toevoegen', 'clickwave-glass'),
            'add_new_item'          => __('Nieuwe offerte toevoegen', 'clickwave-glass'),
            'new_item'              => __('Nieuwe offerte', 'clickwave-glass'),
            'edit_item'             => __('Offerte bewerken', 'clickwave-glass'),
            'view_item'             => __('Offerte bekijken', 'clickwave-glass'),
            'all_items'             => __('Alle offertes', 'clickwave-glass'),
            'search_items'          => __('Offertes zoeken', 'clickwave-glass'),
            'not_found'             => __('Geen offertes gevonden', 'clickwave-glass'),
            'not_found_in_trash'    => __('Geen offertes gevonden in prullenbak', 'clickwave-glass'),
        );

        $args = array(
            'labels'                => $labels,
            'public'                => false,
            'publicly_queryable'    => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => false,
            'rewrite'               => false,
            'capability_type'       => 'post',
            'has_archive'           => false,
            'hierarchical'          => false,
            'menu_position'         => 25,
            'menu_icon'             => 'dashicons-clipboard',
            'supports'              => array('title'),
            'show_in_rest'          => false,
        );

        register_post_type('offerte', $args);
    }

    /**
     * Add meta boxes
     */
    public static function add_meta_boxes() {
        add_meta_box(
            'cgc_quote_status',
            __('Status & Acties', 'clickwave-glass'),
            array(__CLASS__, 'render_status_meta_box'),
            'offerte',
            'side',
            'high'
        );

        add_meta_box(
            'cgc_configuration_details',
            __('Configuratie Details', 'clickwave-glass'),
            array(__CLASS__, 'render_configuration_meta_box'),
            'offerte',
            'normal',
            'high'
        );

        add_meta_box(
            'cgc_customer_details',
            __('Klantgegevens', 'clickwave-glass'),
            array(__CLASS__, 'render_customer_meta_box'),
            'offerte',
            'side',
            'default'
        );
    }

    /**
     * Render configuration meta box
     */
    public static function render_configuration_meta_box($post) {
        // Get meta values
        $width = get_post_meta($post->ID, '_cgc_width', true);
        $height = get_post_meta($post->ID, '_cgc_height', true);
        $track_count = get_post_meta($post->ID, '_cgc_track_count', true);
        $frame_color = get_post_meta($post->ID, '_cgc_frame_color', true);
        $glass_type = get_post_meta($post->ID, '_cgc_glass_type', true);
        $design = get_post_meta($post->ID, '_cgc_design', true);
        $steellook_type = get_post_meta($post->ID, '_cgc_steellook_type', true);
        $has_u_profiles = get_post_meta($post->ID, '_cgc_has_u_profiles', true);
        $has_funderingskoker = get_post_meta($post->ID, '_cgc_has_funderingskoker', true);
        $has_hardhout_palen = get_post_meta($post->ID, '_cgc_has_hardhout_palen', true);
        $meeneemers_type = get_post_meta($post->ID, '_cgc_meeneemers_type', true);
        $has_tochtstrippen = get_post_meta($post->ID, '_cgc_has_tochtstrippen', true);
        $handle_type = get_post_meta($post->ID, '_cgc_handle_type', true);
        $has_montage = get_post_meta($post->ID, '_cgc_has_montage', true);
        $price_estimate = get_post_meta($post->ID, '_cgc_price_estimate', true);
        $customer_message = get_post_meta($post->ID, '_cgc_customer_message', true);

        ?>
        <table class="form-table">
            <tr>
                <th><strong><?php _e('Afmetingen', 'clickwave-glass'); ?></strong></th>
                <td>
                    <?php echo esc_html($width); ?> mm (breedte) x <?php echo esc_html($height); ?> mm (hoogte)
                </td>
            </tr>
            <tr>
                <th><?php _e('Aantal rails', 'clickwave-glass'); ?></th>
                <td><?php echo esc_html($track_count); ?></td>
            </tr>
            <tr>
                <th><?php _e('Kleur kozijn', 'clickwave-glass'); ?></th>
                <td><?php echo esc_html($frame_color); ?></td>
            </tr>
            <tr>
                <th><?php _e('Glastype', 'clickwave-glass'); ?></th>
                <td><?php echo esc_html($glass_type); ?></td>
            </tr>
            <tr>
                <th><?php _e('Design', 'clickwave-glass'); ?></th>
                <td><?php echo esc_html($design); ?></td>
            </tr>
            <?php if ($design === 'steellook' && $steellook_type): ?>
            <tr>
                <th><?php _e('Steellook type', 'clickwave-glass'); ?></th>
                <td><?php echo esc_html($steellook_type); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><?php _e('U-profielen', 'clickwave-glass'); ?></th>
                <td><?php echo $has_u_profiles ? __('Ja', 'clickwave-glass') : __('Nee', 'clickwave-glass'); ?></td>
            </tr>
            <tr>
                <th><?php _e('Funderingskoker', 'clickwave-glass'); ?></th>
                <td><?php echo $has_funderingskoker ? __('Ja', 'clickwave-glass') : __('Nee', 'clickwave-glass'); ?></td>
            </tr>
            <tr>
                <th><?php _e('Hardhout palen', 'clickwave-glass'); ?></th>
                <td><?php echo $has_hardhout_palen ? __('Ja', 'clickwave-glass') : __('Nee', 'clickwave-glass'); ?></td>
            </tr>
            <tr>
                <th><?php _e('Meeneemers', 'clickwave-glass'); ?></th>
                <td><?php echo esc_html($meeneemers_type); ?></td>
            </tr>
            <tr>
                <th><?php _e('Tochtstrippen', 'clickwave-glass'); ?></th>
                <td><?php echo $has_tochtstrippen ? __('Ja', 'clickwave-glass') : __('Nee', 'clickwave-glass'); ?></td>
            </tr>
            <tr>
                <th><?php _e('Greep type', 'clickwave-glass'); ?></th>
                <td><?php echo esc_html($handle_type); ?></td>
            </tr>
            <tr>
                <th><?php _e('Montage', 'clickwave-glass'); ?></th>
                <td><?php echo $has_montage ? __('Ja', 'clickwave-glass') : __('Nee', 'clickwave-glass'); ?></td>
            </tr>
            <tr>
                <th><strong><?php _e('Geschatte prijs', 'clickwave-glass'); ?></strong></th>
                <td><strong>&euro; <?php echo number_format((float)$price_estimate, 2, ',', '.'); ?></strong></td>
            </tr>
            <?php if ($customer_message): ?>
            <tr>
                <th><?php _e('Bericht klant', 'clickwave-glass'); ?></th>
                <td><?php echo nl2br(esc_html($customer_message)); ?></td>
            </tr>
            <?php endif; ?>
        </table>
        <?php
    }

    /**
     * Render status meta box
     */
    public static function render_status_meta_box($post) {
        $status = get_post_meta($post->ID, '_cgc_quote_status', true) ?: 'pending';
        $user_id = get_post_meta($post->ID, '_cgc_customer_user_id', true);
        $approved_date = get_post_meta($post->ID, '_cgc_quote_approved_date', true);
        $rejected_date = get_post_meta($post->ID, '_cgc_quote_rejected_date', true);

        $status_labels = array(
            'pending' => array('label' => 'In behandeling', 'color' => '#f59e0b'),
            'approved' => array('label' => 'Goedgekeurd', 'color' => '#10b981'),
            'rejected' => array('label' => 'Afgewezen', 'color' => '#ef4444'),
        );

        $current_status = $status_labels[$status];
        ?>
        <div class="cgc-status-box" style="padding: 15px; background: <?php echo $current_status['color']; ?>20; border-left: 4px solid <?php echo $current_status['color']; ?>; margin: -12px -12px 15px -12px;">
            <strong style="color: <?php echo $current_status['color']; ?>; font-size: 14px;">
                <?php echo esc_html($current_status['label']); ?>
            </strong>
        </div>

        <?php if ($approved_date): ?>
        <p style="color: #10b981; margin-bottom: 15px;">
            <span class="dashicons dashicons-yes-alt" style="vertical-align: middle;"></span>
            Goedgekeurd op <?php echo date_i18n('d-m-Y H:i', strtotime($approved_date)); ?>
        </p>
        <?php endif; ?>

        <?php if ($rejected_date): ?>
        <p style="color: #ef4444; margin-bottom: 15px;">
            <span class="dashicons dashicons-dismiss" style="vertical-align: middle;"></span>
            Afgewezen op <?php echo date_i18n('d-m-Y H:i', strtotime($rejected_date)); ?>
        </p>
        <?php endif; ?>

        <?php if ($user_id): ?>
        <hr style="margin: 15px 0;">
        <p>
            <strong>Klant account:</strong><br>
            <a href="<?php echo admin_url('user-edit.php?user_id=' . $user_id); ?>" target="_blank">
                <?php echo get_userdata($user_id)->display_name; ?>
                <span class="dashicons dashicons-external" style="font-size: 14px; vertical-align: middle;"></span>
            </a>
        </p>
        <p>
            <a href="<?php echo home_url('/mijn-account/offerte/' . $post->ID . '/'); ?>" class="button button-secondary" target="_blank" style="width: 100%; text-align: center;">
                Klant weergave
                <span class="dashicons dashicons-external" style="font-size: 14px; vertical-align: middle;"></span>
            </a>
        </p>
        <?php endif; ?>
        <?php
    }

    /**
     * Render customer meta box
     */
    public static function render_customer_meta_box($post) {
        $customer_name = get_post_meta($post->ID, '_cgc_customer_name', true);
        $customer_email = get_post_meta($post->ID, '_cgc_customer_email', true);
        $customer_phone = get_post_meta($post->ID, '_cgc_customer_phone', true);

        ?>
        <p>
            <strong><?php _e('Naam:', 'clickwave-glass'); ?></strong><br>
            <?php echo esc_html($customer_name); ?>
        </p>
        <p>
            <strong><?php _e('E-mail:', 'clickwave-glass'); ?></strong><br>
            <a href="mailto:<?php echo esc_attr($customer_email); ?>">
                <?php echo esc_html($customer_email); ?>
            </a>
        </p>
        <?php if ($customer_phone): ?>
        <p>
            <strong><?php _e('Telefoon:', 'clickwave-glass'); ?></strong><br>
            <a href="tel:<?php echo esc_attr($customer_phone); ?>">
                <?php echo esc_html($customer_phone); ?>
            </a>
        </p>
        <?php endif; ?>
        <?php
    }

    /**
     * Save meta data
     */
    public static function save_meta($post_id, $post) {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Meta fields are saved via REST API, not through admin
        // This function is here for future manual editing capabilities
    }

    /**
     * Save offerte meta data
     * Called from REST API
     */
    public static function save_offerte_data($post_id, $data) {
        // Configuration data
        update_post_meta($post_id, '_cgc_width', absint($data['width']));
        update_post_meta($post_id, '_cgc_height', absint($data['height']));
        update_post_meta($post_id, '_cgc_track_count', absint($data['trackCount']));
        update_post_meta($post_id, '_cgc_frame_color', sanitize_text_field($data['frameColor']));
        update_post_meta($post_id, '_cgc_glass_type', sanitize_text_field($data['glassType']));
        update_post_meta($post_id, '_cgc_design', sanitize_text_field($data['design']));
        update_post_meta($post_id, '_cgc_steellook_type', sanitize_text_field($data['steellookType'] ?? ''));
        update_post_meta($post_id, '_cgc_has_u_profiles', (bool)($data['hasUProfiles'] ?? false));
        update_post_meta($post_id, '_cgc_has_funderingskoker', (bool)($data['hasFunderingskoker'] ?? false));
        update_post_meta($post_id, '_cgc_has_hardhout_palen', (bool)($data['hasHardhoutPalen'] ?? false));
        update_post_meta($post_id, '_cgc_meeneemers_type', sanitize_text_field($data['meeneemersType'] ?? 'none'));
        update_post_meta($post_id, '_cgc_has_tochtstrippen', (bool)($data['hasTochtstrippen'] ?? false));
        update_post_meta($post_id, '_cgc_handle_type', sanitize_text_field($data['handleType']));
        update_post_meta($post_id, '_cgc_has_montage', (bool)($data['hasMontage'] ?? false));
        update_post_meta($post_id, '_cgc_price_estimate', floatval($data['priceEstimate']));

        // Customer data
        update_post_meta($post_id, '_cgc_customer_name', sanitize_text_field($data['customerName']));
        update_post_meta($post_id, '_cgc_customer_email', sanitize_email($data['customerEmail']));
        update_post_meta($post_id, '_cgc_customer_phone', sanitize_text_field($data['customerPhone'] ?? ''));
        update_post_meta($post_id, '_cgc_customer_message', sanitize_textarea_field($data['customerMessage'] ?? ''));
    }
}
