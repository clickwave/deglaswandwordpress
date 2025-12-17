<?php
/**
 * PDF Generator for Offertes/Facturen
 * Simple, clean PDF generation with 1-click button
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

class CGC_PDF_Generator {

    /**
     * Initialize
     */
    public static function init() {
        // Add PDF download button to offerte edit screen
        add_action('post_submitbox_misc_actions', array(__CLASS__, 'add_pdf_button'));

        // Handle PDF generation
        add_action('admin_post_cgc_generate_pdf', array(__CLASS__, 'generate_pdf'));
    }

    /**
     * Add PDF button to publish metabox
     */
    public static function add_pdf_button() {
        global $post;

        if ($post->post_type !== 'offerte') {
            return;
        }

        ?>
        <div class="misc-pub-section" style="border-top: 1px solid #ddd; padding: 10px 0;">
            <div style="display: flex; gap: 10px;">
                <a href="<?php echo admin_url('admin-post.php?action=cgc_generate_pdf&post_id=' . $post->ID . '&type=offerte&nonce=' . wp_create_nonce('cgc_pdf_' . $post->ID)); ?>"
                   class="button button-primary"
                   style="flex: 1; text-align: center; justify-content: center; display: flex; align-items: center; gap: 6px;"
                   target="_blank">
                    <span class="dashicons dashicons-pdf" style="font-size: 18px;"></span>
                    <span>Offerte PDF</span>
                </a>

                <a href="<?php echo admin_url('admin-post.php?action=cgc_generate_pdf&post_id=' . $post->ID . '&type=factuur&nonce=' . wp_create_nonce('cgc_pdf_' . $post->ID)); ?>"
                   class="button"
                   style="flex: 1; text-align: center; justify-content: center; display: flex; align-items: center; gap: 6px;"
                   target="_blank">
                    <span class="dashicons dashicons-media-spreadsheet" style="font-size: 18px;"></span>
                    <span>Factuur PDF</span>
                </a>
            </div>
        </div>
        <?php
    }

    /**
     * Generate PDF
     */
    public static function generate_pdf() {
        $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
        $type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'offerte';
        $nonce = isset($_GET['nonce']) ? $_GET['nonce'] : '';

        // Verify nonce
        if (!wp_verify_nonce($nonce, 'cgc_pdf_' . $post_id)) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            wp_die('Unauthorized');
        }

        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'offerte') {
            wp_die('Invalid post');
        }

        // Get all data
        $data = self::get_offerte_data($post_id);

        // Generate HTML
        $html = self::generate_pdf_html($data, $type);

        // Generate PDF using simple HTML to PDF conversion
        self::output_pdf($html, $type, $post_id);
    }

    /**
     * Get offerte data
     */
    private static function get_offerte_data($post_id) {
        $post = get_post($post_id);

        $data = array(
            'offerte_id' => $post_id,
            'offerte_number' => 'OFF-' . date('Y') . '-' . str_pad($post_id, 4, '0', STR_PAD_LEFT),
            'date' => get_the_date('d-m-Y', $post_id),

            // Customer
            'customer_name' => get_post_meta($post_id, '_cgc_customer_name', true),
            'customer_email' => get_post_meta($post_id, '_cgc_customer_email', true),
            'customer_phone' => get_post_meta($post_id, '_cgc_customer_phone', true),
            'customer_message' => get_post_meta($post_id, '_cgc_customer_message', true),

            // Configuration
            'width' => get_post_meta($post_id, '_cgc_width', true),
            'height' => get_post_meta($post_id, '_cgc_height', true),
            'track_count' => get_post_meta($post_id, '_cgc_track_count', true),
            'frame_color' => get_post_meta($post_id, '_cgc_frame_color', true),
            'glass_type' => get_post_meta($post_id, '_cgc_glass_type', true),
            'design' => get_post_meta($post_id, '_cgc_design', true),
            'handle_type' => get_post_meta($post_id, '_cgc_handle_type', true),

            // Options
            'has_u_profiles' => get_post_meta($post_id, '_cgc_has_u_profiles', true),
            'has_funderingskoker' => get_post_meta($post_id, '_cgc_has_funderingskoker', true),
            'has_hardhout_palen' => get_post_meta($post_id, '_cgc_has_hardhout_palen', true),
            'has_tochtstrippen' => get_post_meta($post_id, '_cgc_has_tochtstrippen', true),
            'has_montage' => get_post_meta($post_id, '_cgc_has_montage', true),

            // Pricing
            'price_estimate' => get_post_meta($post_id, '_cgc_price_estimate', true),
            'line_items' => get_post_meta($post_id, '_cgc_line_items', true),
        );

        return $data;
    }

    /**
     * Generate PDF HTML
     */
    private static function generate_pdf_html($data, $type) {
        // Get company settings
        $company = CGC_Company_Settings::get_settings();

        // Get logo URL
        $logo_url = '';
        if (!empty($company['logo_id'])) {
            $logo_url = wp_get_attachment_url($company['logo_id']);
        }

        $doc_title = $type === 'factuur' ? 'Factuurnummer' : 'Offertenummer';
        $doc_number = $type === 'factuur' ? '#' . str_pad($data['offerte_id'], 2, '0', STR_PAD_LEFT) : '#' . str_pad($data['offerte_id'], 2, '0', STR_PAD_LEFT);

        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                @page { size: A4 portrait; margin: 0; }
                @media print {
                    body { margin: 0; width: 210mm; height: 297mm; }
                    .container { page-break-after: avoid; }
                }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
                    font-size: 8pt;
                    color: #1a1a1a;
                    line-height: 1.2;
                    width: 210mm;
                    min-height: 297mm;
                }
                .container {
                    width: 210mm;
                    min-height: 297mm;
                    margin: 0 auto;
                    padding: 12mm 10mm;
                    box-sizing: border-box;
                }
                .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; padding-bottom: 12px; border-bottom: 1px solid #e0e0e0; }
                .logo-container { flex: 1; }
                .logo { max-width: 180px; }
                .logo img { max-width: 100%; height: auto; display: block; }
                .doc-info { text-align: right; flex: 1; }
                .doc-title { font-size: 14pt; font-weight: bold; color: #1f3d58; margin-bottom: 3px; }
                .doc-number { font-size: 10pt; color: #666; }
                .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
                .info-box h3 { font-size: 8pt; color: #666; text-transform: uppercase; margin-bottom: 6px; }
                .info-box p { margin-bottom: 3px; }
                .config-section { margin-bottom: 12px; }
                .config-section h3 { font-size: 9pt; background: #f0f0f0; padding: 6px 8px; margin-bottom: 8px; }
                .config-item { display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #eee; }
                .config-label { font-weight: 600; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
                th { background: #1f3d58; color: white; padding: 6px 8px; text-align: left; font-size: 8pt; }
                td { padding: 5px 8px; border-bottom: 1px solid #eee; font-size: 8pt; }
                .text-right { text-align: right; }
                .total-row { background: #f9f9f9; font-weight: bold; font-size: 9pt; }
                .footer { margin-top: 15px; padding-top: 12px; border-top: 2px solid #ddd; font-size: 7pt; color: #666; text-align: center; }
            </style>
        </head>
        <body>
            <div class="container">
                <!-- Header -->
                <div class="header">
                    <div class="logo-container">
                        <?php if ($logo_url): ?>
                            <div class="logo">
                                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($company['company_name']); ?>">
                            </div>
                        <?php else: ?>
                            <div style="font-size: 20pt; font-weight: 700; color: #1a3d5c;">
                                <?php echo esc_html($company['company_name']); ?>
                            </div>
                        <?php endif; ?>
                        <p style="font-size: 7pt; color: #666; margin-top: 4px;"><?php echo esc_html($company['website']); ?></p>
                    </div>
                    <div class="doc-info">
                        <div class="doc-title"><?php echo $doc_title; ?> <?php echo $doc_number; ?></div>
                        <p style="margin-top: 4px; font-size: 7pt; color: #666;">Datum <?php echo $data['date']; ?></p>
                    </div>
                </div>

                <!-- Company Info & Customer Info -->
                <div class="info-grid">
                    <div class="info-box">
                        <p style="font-size: 8pt; margin-bottom: 6px;"><strong><?php echo esc_html($company['company_name']); ?></strong></p>
                        <p style="font-size: 7pt; line-height: 1.3;">
                            <?php echo esc_html($company['address_street']); ?><br>
                            <?php echo esc_html($company['address_postcode']); ?> <?php echo esc_html($company['address_city']); ?>, <?php echo esc_html($company['address_province']); ?><br>
                            <?php echo esc_html($company['address_country']); ?>
                        </p>
                        <p style="font-size: 7pt; margin-top: 6px; line-height: 1.3;">
                            iban <?php echo esc_html($company['iban']); ?><br>
                            kvk <?php echo esc_html($company['kvk']); ?><br>
                            btw <?php echo esc_html($company['btw']); ?>
                        </p>
                    </div>
                    <div class="info-box">
                        <h3 style="font-size: 7pt; color: #666; margin-bottom: 4px;">Klantenservice</h3>
                        <p style="font-size: 7pt; line-height: 1.3;">
                            <?php echo esc_html($company['phone']); ?><br>
                            <?php echo esc_html($company['email']); ?>
                        </p>
                        <h3 style="font-size: 7pt; color: #666; margin: 8px 0 4px;">Klant</h3>
                        <p style="font-size: 7pt; line-height: 1.3;">
                            <strong><?php echo esc_html($data['customer_name']); ?></strong><br>
                            <?php echo esc_html($data['customer_email']); ?><br>
                            <?php if ($data['customer_phone']): ?>
                                <?php echo esc_html($data['customer_phone']); ?><br>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <!-- Configuration -->
                <div class="config-section">
                    <h3>Configuratie Glazen Schuifwand</h3>
                    <div class="config-item">
                        <span class="config-label">Afmetingen:</span>
                        <span><?php echo $data['width']; ?> × <?php echo $data['height']; ?> mm</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Aantal panelen:</span>
                        <span><?php echo $data['track_count']; ?> rails</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Frame kleur:</span>
                        <span><?php echo esc_html($data['frame_color']); ?></span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Glas type:</span>
                        <span><?php echo esc_html($data['glass_type']); ?></span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Design:</span>
                        <span><?php echo esc_html($data['design']); ?></span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Handgreep:</span>
                        <span><?php echo esc_html($data['handle_type']); ?></span>
                    </div>
                </div>

                <!-- Options -->
                <?php
                $selected_options = array();
                if ($data['has_u_profiles']) $selected_options[] = 'U-profielen';
                if ($data['has_funderingskoker']) $selected_options[] = 'Funderingskoker';
                if ($data['has_hardhout_palen']) $selected_options[] = 'Hardhout palen';
                if ($data['has_tochtstrippen']) $selected_options[] = 'Tochtstrippen';
                if ($data['has_montage']) $selected_options[] = 'Montage';

                if (!empty($selected_options)):
                ?>
                <div class="config-section">
                    <h3>Geselecteerde Opties</h3>
                    <?php foreach ($selected_options as $option): ?>
                        <div class="config-item">
                            <span>✓ <?php echo esc_html($option); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Line Items -->
                <?php
                $line_items = $data['line_items'];
                if (!empty($line_items) && is_array($line_items)):
                ?>
                <div class="config-section">
                    <h3>Extra Items</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Omschrijving</th>
                                <th class="text-right">Aantal</th>
                                <th class="text-right">Prijs per stuk</th>
                                <th class="text-right">Totaal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($line_items as $item): ?>
                                <tr>
                                    <td><?php echo esc_html($item['description']); ?></td>
                                    <td class="text-right"><?php echo $item['quantity']; ?></td>
                                    <td class="text-right">€ <?php echo number_format($item['unit_price'], 2, ',', '.'); ?></td>
                                    <td class="text-right">€ <?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <!-- Total -->
                <table>
                    <tr class="total-row">
                        <td colspan="3">TOTAAL (excl. BTW)</td>
                        <td class="text-right">€ <?php echo number_format($data['price_estimate'], 2, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">BTW (21%)</td>
                        <td class="text-right">€ <?php echo number_format($data['price_estimate'] * 0.21, 2, ',', '.'); ?></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3" style="font-size: 10pt;">TOTAAL (incl. BTW)</td>
                        <td class="text-right" style="font-size: 10pt;">€ <?php echo number_format($data['price_estimate'] * 1.21, 2, ',', '.'); ?></td>
                    </tr>
                </table>

                <!-- Footer -->
                <div class="footer">
                    <p style="font-weight: 600; margin-bottom: 6px;">Bedankt voor uw bestelling!</p>
                    <p style="font-size: 7pt; line-height: 1.3;">
                        Wij verzoeken u vriendelijk dit bedrag uiterlijk op de dag van montage/levering over te maken naar<br>
                        rekeningnummer <?php echo esc_html($company['iban']); ?> onder vermelding van het <?php echo strtolower($doc_title); ?>
                    </p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    /**
     * Output PDF - Simple print-friendly HTML
     */
    private static function output_pdf($html, $type, $post_id) {
        // Output print-friendly HTML that can be saved as PDF via browser
        echo $html;
        ?>
        <script>
        // Auto-trigger print dialog
        window.onload = function() {
            window.print();
        };
        </script>
        <?php
        exit;
    }
}
