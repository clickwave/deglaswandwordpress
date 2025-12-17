<?php
/**
 * Company Settings - Bedrijfsgegevens beheren
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

class CGC_Company_Settings {

    /**
     * Initialize
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_menu_page'));
        add_action('admin_post_cgc_save_company_settings', array(__CLASS__, 'save_settings'));
    }

    /**
     * Add menu page
     */
    public static function add_menu_page() {
        add_submenu_page(
            'edit.php?post_type=offerte',
            __('Bedrijfsgegevens', 'clickwave-glass'),
            __('Bedrijfsgegevens', 'clickwave-glass'),
            'manage_options',
            'cgc-company-settings',
            array(__CLASS__, 'render_settings_page')
        );
    }

    /**
     * Get default settings
     */
    public static function get_settings() {
        $defaults = array(
            'company_name' => 'Dé Glaswand',
            'website' => 'https://deglaswand.nl/products',
            'email' => 'info@deglaswand.nl',
            'phone' => '06 15 24 63 83',
            'address_street' => 'Wethouder Rebellaan 61',
            'address_city' => 'Barneveld',
            'address_province' => 'Gelderland',
            'address_postcode' => '3771ka',
            'address_country' => 'Nederland',
            'iban' => 'NL08INGB0108497771',
            'kvk' => '81717202',
            'btw' => 'NL 003596374 B 55',
            'logo_id' => 113, // Default logo attachment ID
        );

        $settings = get_option('cgc_company_settings', $defaults);
        return wp_parse_args($settings, $defaults);
    }

    /**
     * Render settings page
     */
    public static function render_settings_page() {
        // Enqueue media uploader
        wp_enqueue_media();

        if (isset($_GET['updated']) && $_GET['updated'] === 'true') {
            echo '<div class="notice notice-success is-dismissible"><p>Bedrijfsgegevens opgeslagen!</p></div>';
        }

        $settings = self::get_settings();
        ?>
        <div class="wrap">
            <h1><?php _e('Bedrijfsgegevens', 'clickwave-glass'); ?></h1>
            <p><?php _e('Deze gegevens worden gebruikt op offertes, facturen en emails.', 'clickwave-glass'); ?></p>

            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="max-width: 800px;">
                <input type="hidden" name="action" value="cgc_save_company_settings">
                <?php wp_nonce_field('cgc_company_settings', 'cgc_settings_nonce'); ?>

                <h2>Logo & Algemene Gegevens</h2>
                <table class="form-table">
                    <tr>
                        <th><label for="logo_id">Bedrijfslogo</label></th>
                        <td>
                            <?php
                            $logo_id = isset($settings['logo_id']) ? $settings['logo_id'] : 113;
                            $logo_url = $logo_id ? wp_get_attachment_url($logo_id) : '';
                            ?>
                            <div style="margin-bottom: 10px;">
                                <?php if ($logo_url): ?>
                                    <img id="logo-preview" src="<?php echo esc_url($logo_url); ?>" style="max-width: 200px; height: auto; display: block; margin-bottom: 10px; border: 1px solid #ddd; padding: 10px; background: white;">
                                <?php else: ?>
                                    <img id="logo-preview" src="" style="max-width: 200px; height: auto; display: none; margin-bottom: 10px; border: 1px solid #ddd; padding: 10px; background: white;">
                                <?php endif; ?>
                            </div>
                            <input type="hidden" id="logo_id" name="logo_id" value="<?php echo esc_attr($logo_id); ?>">
                            <button type="button" class="button" id="upload_logo_button">Logo Kiezen</button>
                            <button type="button" class="button" id="remove_logo_button" <?php echo !$logo_url ? 'style="display:none;"' : ''; ?>>Logo Verwijderen</button>
                            <p class="description">Aanbevolen: Logo met transparante achtergrond (PNG)</p>

                            <script>
                            jQuery(document).ready(function($) {
                                var mediaUploader;

                                $('#upload_logo_button').on('click', function(e) {
                                    e.preventDefault();

                                    if (mediaUploader) {
                                        mediaUploader.open();
                                        return;
                                    }

                                    mediaUploader = wp.media({
                                        title: 'Kies een logo',
                                        button: { text: 'Gebruik dit logo' },
                                        multiple: false
                                    });

                                    mediaUploader.on('select', function() {
                                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                                        $('#logo_id').val(attachment.id);
                                        $('#logo-preview').attr('src', attachment.url).show();
                                        $('#remove_logo_button').show();
                                    });

                                    mediaUploader.open();
                                });

                                $('#remove_logo_button').on('click', function(e) {
                                    e.preventDefault();
                                    $('#logo_id').val('');
                                    $('#logo-preview').attr('src', '').hide();
                                    $(this).hide();
                                });
                            });
                            </script>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="company_name">Bedrijfsnaam *</label></th>
                        <td>
                            <input type="text" id="company_name" name="company_name" value="<?php echo esc_attr($settings['company_name']); ?>" class="regular-text" required>
                            <p class="description">De officiële naam van je bedrijf</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="website">Website URL</label></th>
                        <td>
                            <input type="url" id="website" name="website" value="<?php echo esc_attr($settings['website']); ?>" class="regular-text">
                            <p class="description">Bijv: https://deglaswand.nl/products</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="email">E-mailadres *</label></th>
                        <td>
                            <input type="email" id="email" name="email" value="<?php echo esc_attr($settings['email']); ?>" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="phone">Telefoonnummer *</label></th>
                        <td>
                            <input type="text" id="phone" name="phone" value="<?php echo esc_attr($settings['phone']); ?>" class="regular-text" required>
                        </td>
                    </tr>
                </table>

                <h2>Bedrijfsadres</h2>
                <table class="form-table">
                    <tr>
                        <th><label for="address_street">Straat + Huisnummer *</label></th>
                        <td>
                            <input type="text" id="address_street" name="address_street" value="<?php echo esc_attr($settings['address_street']); ?>" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="address_postcode">Postcode *</label></th>
                        <td>
                            <input type="text" id="address_postcode" name="address_postcode" value="<?php echo esc_attr($settings['address_postcode']); ?>" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="address_city">Stad *</label></th>
                        <td>
                            <input type="text" id="address_city" name="address_city" value="<?php echo esc_attr($settings['address_city']); ?>" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="address_province">Provincie</label></th>
                        <td>
                            <input type="text" id="address_province" name="address_province" value="<?php echo esc_attr($settings['address_province']); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th><label for="address_country">Land *</label></th>
                        <td>
                            <input type="text" id="address_country" name="address_country" value="<?php echo esc_attr($settings['address_country']); ?>" class="regular-text" required>
                        </td>
                    </tr>
                </table>

                <h2>Fiscale Gegevens</h2>
                <table class="form-table">
                    <tr>
                        <th><label for="iban">IBAN *</label></th>
                        <td>
                            <input type="text" id="iban" name="iban" value="<?php echo esc_attr($settings['iban']); ?>" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="kvk">KVK Nummer *</label></th>
                        <td>
                            <input type="text" id="kvk" name="kvk" value="<?php echo esc_attr($settings['kvk']); ?>" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="btw">BTW Nummer *</label></th>
                        <td>
                            <input type="text" id="btw" name="btw" value="<?php echo esc_attr($settings['btw']); ?>" class="regular-text" required>
                        </td>
                    </tr>
                </table>

                <?php submit_button('Opslaan'); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Save settings
     */
    public static function save_settings() {
        // Check nonce
        if (!isset($_POST['cgc_settings_nonce']) || !wp_verify_nonce($_POST['cgc_settings_nonce'], 'cgc_company_settings')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $settings = array(
            'logo_id' => isset($_POST['logo_id']) ? intval($_POST['logo_id']) : 113,
            'company_name' => sanitize_text_field($_POST['company_name']),
            'website' => esc_url_raw($_POST['website']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'address_street' => sanitize_text_field($_POST['address_street']),
            'address_city' => sanitize_text_field($_POST['address_city']),
            'address_province' => sanitize_text_field($_POST['address_province']),
            'address_postcode' => sanitize_text_field($_POST['address_postcode']),
            'address_country' => sanitize_text_field($_POST['address_country']),
            'iban' => sanitize_text_field($_POST['iban']),
            'kvk' => sanitize_text_field($_POST['kvk']),
            'btw' => sanitize_text_field($_POST['btw']),
        );

        update_option('cgc_company_settings', $settings);

        wp_redirect(admin_url('edit.php?post_type=offerte&page=cgc-company-settings&updated=true'));
        exit;
    }
}
