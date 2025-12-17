<?php
/**
 * Offerte Line Items - Custom items toevoegen door admin
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

class CGC_Offerte_Line_Items {

    /**
     * Initialize
     */
    public static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'add_line_items_metabox'));
        add_action('save_post_offerte', array(__CLASS__, 'save_line_items'), 10, 2);

        // AJAX handlers
        add_action('wp_ajax_cgc_add_line_item', array(__CLASS__, 'ajax_add_line_item'));
        add_action('wp_ajax_cgc_remove_line_item', array(__CLASS__, 'ajax_remove_line_item'));
        add_action('wp_ajax_cgc_update_configuration', array(__CLASS__, 'ajax_update_configuration'));

        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_scripts'));
    }

    /**
     * Enqueue admin scripts
     */
    public static function enqueue_admin_scripts($hook) {
        global $post_type;

        if ($hook === 'post.php' && $post_type === 'offerte') {
            wp_enqueue_style('cgc-admin-offerte', CGC_PLUGIN_URL . 'assets/admin/admin-offerte.css', array(), CGC_VERSION);
            wp_enqueue_script('cgc-admin-offerte', CGC_PLUGIN_URL . 'assets/admin/admin-offerte.js', array('jquery'), CGC_VERSION, true);

            wp_localize_script('cgc-admin-offerte', 'cgcAdmin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('cgc_admin_offerte'),
            ));
        }
    }

    /**
     * Add meta box
     */
    public static function add_line_items_metabox() {
        add_meta_box(
            'cgc_line_items',
            __('Extra Items & Aanpassingen', 'clickwave-glass'),
            array(__CLASS__, 'render_line_items_metabox'),
            'offerte',
            'normal',
            'high'
        );

        add_meta_box(
            'cgc_edit_configuration',
            __('Configuratie Aanpassen', 'clickwave-glass'),
            array(__CLASS__, 'render_edit_configuration_metabox'),
            'offerte',
            'normal',
            'high'
        );

        add_meta_box(
            'cgc_edit_dimensions',
            __('Afmetingen & Specificaties', 'clickwave-glass'),
            array(__CLASS__, 'render_dimensions_metabox'),
            'offerte',
            'normal',
            'high'
        );
    }

    /**
     * Render line items metabox
     */
    public static function render_line_items_metabox($post) {
        wp_nonce_field('cgc_save_line_items', 'cgc_line_items_nonce');

        $line_items = get_post_meta($post->ID, '_cgc_line_items', true);
        if (!is_array($line_items)) {
            $line_items = array();
        }

        $base_price = get_post_meta($post->ID, '_cgc_price_estimate', true);
        $line_items_total = 0;

        foreach ($line_items as $item) {
            $line_items_total += floatval($item['price']);
        }

        $new_total = $base_price + $line_items_total;
        ?>

        <div class="cgc-line-items-container">

            <div class="cgc-price-summary" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                    <div>
                        <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">Basis Configuratie</label>
                        <div style="font-size: 20px; font-weight: 600; color: #1f3d58;">€ <?php echo number_format($base_price, 2, ',', '.'); ?></div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">Extra Items</label>
                        <div style="font-size: 20px; font-weight: 600; color: #f59e0b;">€ <?php echo number_format($line_items_total, 2, ',', '.'); ?></div>
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">Nieuwe Totaal</label>
                        <div style="font-size: 24px; font-weight: 700; color: #10b981;">€ <?php echo number_format($new_total, 2, ',', '.'); ?></div>
                    </div>
                </div>
                <div style="font-size: 12px; color: #999; padding-top: 10px; border-top: 1px solid #e0e0e0;">
                    Inclusief BTW: € <?php echo number_format($new_total * 1.21, 2, ',', '.'); ?>
                </div>
            </div>

            <div class="cgc-line-items-list" style="margin-bottom: 20px;">
                <?php if (empty($line_items)): ?>
                    <p style="text-align: center; color: #999; padding: 40px 0; background: #f8f9fa; border-radius: 8px;">
                        Geen extra items toegevoegd. Voeg items toe via het formulier hieronder.
                    </p>
                <?php else: ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Omschrijving</th>
                                <th style="width: 15%;">Aantal</th>
                                <th style="width: 20%;">Prijs per stuk</th>
                                <th style="width: 10%;">Totaal</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($line_items as $index => $item): ?>
                                <tr>
                                    <td><strong><?php echo esc_html($item['description']); ?></strong></td>
                                    <td><?php echo esc_html($item['quantity']); ?></td>
                                    <td>€ <?php echo number_format($item['unit_price'], 2, ',', '.'); ?></td>
                                    <td><strong>€ <?php echo number_format($item['price'], 2, ',', '.'); ?></strong></td>
                                    <td>
                                        <button type="button" class="button cgc-remove-line-item" data-index="<?php echo $index; ?>" data-post-id="<?php echo $post->ID; ?>">
                                            ×
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="cgc-add-line-item-form" style="background: white; border: 2px dashed #ccc; padding: 20px; border-radius: 8px;">
                <h3 style="margin-top: 0; margin-bottom: 16px;">Nieuw Item Toevoegen</h3>
                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 600;">Omschrijving</label>
                        <input type="text" id="cgc-new-item-description" class="regular-text" placeholder="bijv. Sandwichpaneel, Houten balk, Reiskosten" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 600;">Aantal</label>
                        <input type="number" id="cgc-new-item-quantity" min="1" value="1" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 600;">Prijs per stuk (€)</label>
                        <input type="number" id="cgc-new-item-price" min="0" step="0.01" placeholder="0.00" style="width: 100%;">
                    </div>
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 600;">Notities (optioneel)</label>
                    <textarea id="cgc-new-item-notes" rows="2" class="large-text" placeholder="Extra informatie voor dit item" style="width: 100%;"></textarea>
                </div>

                <button type="button" id="cgc-add-line-item-btn" class="button button-primary" data-post-id="<?php echo $post->ID; ?>">
                    <span class="dashicons dashicons-plus-alt" style="vertical-align: middle;"></span> Item Toevoegen
                </button>

                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e0e0e0;">
                    <strong>Product Bibliotheek:</strong>
                    <p style="font-size: 13px; color: #666; margin: 8px 0;">Klik op een product om toe te voegen. <a href="<?php echo admin_url('edit.php?post_type=offerte&page=cgc-product-library'); ?>">Beheer producten →</a></p>
                    <div style="display: flex; gap: 8px; margin-top: 8px; flex-wrap: wrap;">
                        <?php
                        $library_products = CGC_Product_Library::get_products();
                        if (!empty($library_products)):
                            foreach ($library_products as $product):
                                ?>
                                <button type="button" class="button cgc-quick-add" data-description="<?php echo esc_attr($product['name']); ?>" data-price="<?php echo esc_attr($product['price']); ?>">
                                    + <?php echo esc_html($product['name']); ?> (€<?php echo number_format($product['price'], 0, ',', '.'); ?>)
                                </button>
                                <?php
                            endforeach;
                        else:
                            echo '<p style="color: #999; font-size: 13px;">Nog geen producten in bibliotheek. <a href="' . admin_url('edit.php?post_type=offerte&page=cgc-product-library') . '">Voeg je eerste product toe →</a></p>';
                        endif;
                        ?>
                    </div>
                </div>
            </div>

        </div>

        <style>
        .cgc-remove-line-item {
            color: #dc3232;
            font-size: 20px;
            line-height: 1;
            padding: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
        }
        .cgc-remove-line-item:hover {
            background: #dc3232;
            color: white;
        }
        .cgc-quick-add {
            font-size: 12px;
        }
        </style>
        <?php
    }

    /**
     * Render edit configuration metabox
     */
    public static function render_edit_configuration_metabox($post) {
        // Get pricing from settings
        $settings = get_option('cgc_settings', array());
        $pricing = isset($settings['pricing']) ? $settings['pricing'] : array();

        $base_price = floatval(get_post_meta($post->ID, '_cgc_price_estimate', true));

        ?>
        <div class="cgc-edit-config" style="background: #fffbf0; border: 2px solid #f59e0b; padding: 20px; border-radius: 8px;">
            <div style="display: flex; align-items: start; gap: 16px; margin-bottom: 16px;">
                <span class="dashicons dashicons-warning" style="color: #f59e0b; font-size: 24px;"></span>
                <div>
                    <h4 style="margin: 0 0 8px 0; color: #f59e0b;">Configuratie Aanpassen</h4>
                    <p style="margin: 0; color: #666;">
                        Pas hier de configuratie aan. De prijs wordt direct herberekend.
                    </p>
                </div>
            </div>

            <!-- Live Price Display -->
            <div id="cgc-live-price-display" style="background: white; padding: 16px; border-radius: 8px; margin-bottom: 16px; border: 2px solid #10b981;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 12px; color: #666; margin-bottom: 4px;">Basis Prijs</div>
                        <div style="font-size: 18px; font-weight: 600;" id="cgc-base-price-display">€ <?php echo number_format($base_price, 2, ',', '.'); ?></div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #666; margin-bottom: 4px;">Extra Opties</div>
                        <div style="font-size: 18px; font-weight: 600; color: #f59e0b;" id="cgc-options-price-display">€ 0,00</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #666; margin-bottom: 4px;">Nieuwe Totaal</div>
                        <div style="font-size: 24px; font-weight: 700; color: #10b981;" id="cgc-new-total-display">€ <?php echo number_format($base_price, 2, ',', '.'); ?></div>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                <?php
                $options = array(
                    '_cgc_has_u_profiles' => array(
                        'label' => 'U-profielen',
                        'price' => isset($pricing['u_profiles']) ? floatval($pricing['u_profiles']) : 250
                    ),
                    '_cgc_has_funderingskoker' => array(
                        'label' => 'Funderingskoker',
                        'price' => isset($pricing['funderingskoker']) ? floatval($pricing['funderingskoker']) : 150
                    ),
                    '_cgc_has_hardhout_palen' => array(
                        'label' => 'Hardhout palen',
                        'price' => isset($pricing['hardhout_palen']) ? floatval($pricing['hardhout_palen']) : 300
                    ),
                    '_cgc_has_tochtstrippen' => array(
                        'label' => 'Tochtstrippen',
                        'price' => isset($pricing['tochtstrippen']) ? floatval($pricing['tochtstrippen']) : 75
                    ),
                    '_cgc_has_montage' => array(
                        'label' => 'Montage',
                        'price' => isset($pricing['montage']) ? floatval($pricing['montage']) : 500
                    ),
                );

                foreach ($options as $meta_key => $option):
                    $checked = get_post_meta($post->ID, $meta_key, true);
                ?>
                    <label class="cgc-config-option" style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px; background: white; border-radius: 6px; cursor: pointer; border: 2px solid <?php echo $checked ? '#10b981' : '#e0e0e0'; ?>; transition: all 0.2s;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" class="cgc-config-checkbox" name="<?php echo esc_attr($meta_key); ?>" value="1" <?php checked($checked, 1); ?> data-price="<?php echo $option['price']; ?>" style="margin: 0; width: 18px; height: 18px;">
                            <span style="font-weight: 600; font-size: 15px;"><?php echo esc_html($option['label']); ?></span>
                        </div>
                        <span style="font-weight: 700; color: #10b981; font-size: 15px;">+ €<?php echo number_format($option['price'], 0, ',', '.'); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <p style="margin-top: 16px; margin-bottom: 0; font-size: 12px; color: #999;">
                <strong>Let op:</strong> Klik op "Bijwerken" om de wijzigingen definitief op te slaan.
            </p>
        </div>

        <script>
        jQuery(document).ready(function($) {
            const basePrice = <?php echo $base_price; ?>;

            function updatePrices() {
                let optionsTotal = 0;

                $('.cgc-config-checkbox:checked').each(function() {
                    optionsTotal += parseFloat($(this).data('price') || 0);
                });

                const newTotal = basePrice + optionsTotal;

                $('#cgc-options-price-display').text('€ ' + optionsTotal.toFixed(2).replace('.', ','));
                $('#cgc-new-total-display').text('€ ' + newTotal.toFixed(2).replace('.', ','));

                // Update meta box
                $('#cgc_price_estimate').val(newTotal.toFixed(2));
            }

            $('.cgc-config-checkbox').on('change', function() {
                const $label = $(this).closest('.cgc-config-option');
                if ($(this).is(':checked')) {
                    $label.css('border-color', '#10b981');
                } else {
                    $label.css('border-color', '#e0e0e0');
                }
                updatePrices();
            });

            // Initial calculation
            updatePrices();
        });
        </script>
        <?php
    }

    /**
     * Render dimensions metabox
     */
    public static function render_dimensions_metabox($post) {
        wp_nonce_field('cgc_save_dimensions', 'cgc_dimensions_nonce');

        $width = get_post_meta($post->ID, '_cgc_width', true);
        $height = get_post_meta($post->ID, '_cgc_height', true);
        $track_count = get_post_meta($post->ID, '_cgc_track_count', true);
        $frame_color = get_post_meta($post->ID, '_cgc_frame_color', true);
        $glass_type = get_post_meta($post->ID, '_cgc_glass_type', true);
        $design = get_post_meta($post->ID, '_cgc_design', true);
        $handle_type = get_post_meta($post->ID, '_cgc_handle_type', true);
        ?>
        <div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
            <p style="margin-bottom: 20px; color: #666;">Pas hier alle configuratie details aan. Wijzigingen worden opgeslagen bij "Bijwerken".</p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Breedte (mm) *</label>
                    <input type="number" name="cgc_width" value="<?php echo esc_attr($width); ?>" min="1000" max="10000" step="1" class="regular-text" required>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Hoogte (mm) *</label>
                    <input type="number" name="cgc_height" value="<?php echo esc_attr($height); ?>" min="1000" max="3000" step="1" class="regular-text" required>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Aantal Rails/Panelen *</label>
                    <input type="number" name="cgc_track_count" value="<?php echo esc_attr($track_count); ?>" min="2" max="6" step="1" class="regular-text" required>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Frame Kleur *</label>
                    <input type="text" name="cgc_frame_color" value="<?php echo esc_attr($frame_color); ?>" class="regular-text" placeholder="RAL 9005 - Zwart">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Glas Type *</label>
                    <input type="text" name="cgc_glass_type" value="<?php echo esc_attr($glass_type); ?>" class="regular-text" placeholder="Helder glas">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Design *</label>
                    <input type="text" name="cgc_design" value="<?php echo esc_attr($design); ?>" class="regular-text" placeholder="Standaard">
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Handgreep Type *</label>
                    <input type="text" name="cgc_handle_type" value="<?php echo esc_attr($handle_type); ?>" class="large-text" placeholder="Standaard handgreep">
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Save line items and configuration changes
     */
    public static function save_line_items($post_id, $post) {
        // Check nonce
        if (!isset($_POST['cgc_line_items_nonce']) || !wp_verify_nonce($_POST['cgc_line_items_nonce'], 'cgc_save_line_items')) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save dimensions if present
        if (isset($_POST['cgc_dimensions_nonce']) && wp_verify_nonce($_POST['cgc_dimensions_nonce'], 'cgc_save_dimensions')) {
            if (isset($_POST['cgc_width'])) {
                update_post_meta($post_id, '_cgc_width', intval($_POST['cgc_width']));
            }
            if (isset($_POST['cgc_height'])) {
                update_post_meta($post_id, '_cgc_height', intval($_POST['cgc_height']));
            }
            if (isset($_POST['cgc_track_count'])) {
                update_post_meta($post_id, '_cgc_track_count', intval($_POST['cgc_track_count']));
            }
            if (isset($_POST['cgc_frame_color'])) {
                update_post_meta($post_id, '_cgc_frame_color', sanitize_text_field($_POST['cgc_frame_color']));
            }
            if (isset($_POST['cgc_glass_type'])) {
                update_post_meta($post_id, '_cgc_glass_type', sanitize_text_field($_POST['cgc_glass_type']));
            }
            if (isset($_POST['cgc_design'])) {
                update_post_meta($post_id, '_cgc_design', sanitize_text_field($_POST['cgc_design']));
            }
            if (isset($_POST['cgc_handle_type'])) {
                update_post_meta($post_id, '_cgc_handle_type', sanitize_text_field($_POST['cgc_handle_type']));
            }
        }

        // Save configuration changes
        $options = array(
            '_cgc_has_u_profiles',
            '_cgc_has_funderingskoker',
            '_cgc_has_hardhout_palen',
            '_cgc_has_tochtstrippen',
            '_cgc_has_montage',
        );

        foreach ($options as $option) {
            $value = isset($_POST[$option]) ? 1 : 0;
            update_post_meta($post_id, $option, $value);
        }
    }

    /**
     * AJAX: Add line item
     */
    public static function ajax_add_line_item() {
        check_ajax_referer('cgc_admin_offerte', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Unauthorized');
        }

        $post_id = intval($_POST['post_id']);
        $description = sanitize_text_field($_POST['description']);
        $quantity = intval($_POST['quantity']);
        $unit_price = floatval($_POST['unit_price']);
        $notes = sanitize_textarea_field($_POST['notes']);

        $line_items = get_post_meta($post_id, '_cgc_line_items', true);
        if (!is_array($line_items)) {
            $line_items = array();
        }

        $line_items[] = array(
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'price' => $quantity * $unit_price,
            'notes' => $notes,
            'date_added' => current_time('mysql'),
        );

        update_post_meta($post_id, '_cgc_line_items', $line_items);

        wp_send_json_success(array(
            'message' => 'Item toegevoegd',
            'reload' => true,
        ));
    }

    /**
     * AJAX: Remove line item
     */
    public static function ajax_remove_line_item() {
        check_ajax_referer('cgc_admin_offerte', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Unauthorized');
        }

        $post_id = intval($_POST['post_id']);
        $index = intval($_POST['index']);

        $line_items = get_post_meta($post_id, '_cgc_line_items', true);
        if (is_array($line_items) && isset($line_items[$index])) {
            unset($line_items[$index]);
            $line_items = array_values($line_items); // Re-index array
            update_post_meta($post_id, '_cgc_line_items', $line_items);
        }

        wp_send_json_success(array(
            'message' => 'Item verwijderd',
            'reload' => true,
        ));
    }
}
