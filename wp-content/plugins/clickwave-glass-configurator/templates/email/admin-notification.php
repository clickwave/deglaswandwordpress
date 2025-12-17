<?php
/**
 * Admin Notification Email Template
 *
 * Available variables: $offerte_data
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            background: #1e3a8a;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 30px;
        }
        .email-body h2 {
            color: #1e3a8a;
            font-size: 20px;
            margin-top: 0;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
        }
        .config-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .config-table th,
        .config-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        .config-table th {
            background: #f8f9fa;
            font-weight: bold;
            width: 40%;
            color: #555;
        }
        .config-table td {
            color: #333;
        }
        .price-row {
            background: #fef3c7;
            font-weight: bold;
            font-size: 18px;
        }
        .customer-info {
            background: #f0f9ff;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .customer-info h3 {
            margin-top: 0;
            color: #1e3a8a;
        }
        .customer-info p {
            margin: 8px 0;
        }
        .message-box {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #1e3a8a;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #1e3a8a;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
            font-weight: bold;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🔔 Nieuwe Offerte Aanvraag</h1>
        </div>

        <div class="email-body">
            <p><strong>Datum:</strong> <?php echo esc_html($offerte_data['date']); ?></p>
            <p><strong>Offerte ID:</strong> #<?php echo esc_html($offerte_data['offerte_id']); ?></p>

            <div class="customer-info">
                <h3>Klantgegevens</h3>
                <p><strong>Naam:</strong> <?php echo esc_html($offerte_data['customer_name']); ?></p>
                <p><strong>E-mail:</strong> <a href="mailto:<?php echo esc_attr($offerte_data['customer_email']); ?>"><?php echo esc_html($offerte_data['customer_email']); ?></a></p>
                <?php if (!empty($offerte_data['customer_phone'])): ?>
                <p><strong>Telefoon:</strong> <a href="tel:<?php echo esc_attr($offerte_data['customer_phone']); ?>"><?php echo esc_html($offerte_data['customer_phone']); ?></a></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($offerte_data['customer_message'])): ?>
            <div class="message-box">
                <strong>Bericht van klant:</strong><br>
                <?php echo nl2br(esc_html($offerte_data['customer_message'])); ?>
            </div>
            <?php endif; ?>

            <h2>Configuratie Details</h2>

            <table class="config-table">
                <tr>
                    <th>Afmetingen</th>
                    <td><?php echo esc_html($offerte_data['width']); ?> mm x <?php echo esc_html($offerte_data['height']); ?> mm (B x H)</td>
                </tr>
                <tr>
                    <th>Aantal rails</th>
                    <td><?php echo esc_html($offerte_data['track_count']); ?> rails</td>
                </tr>
                <tr>
                    <th>Kleur kozijn</th>
                    <td><?php echo esc_html($offerte_data['frame_color']); ?></td>
                </tr>
                <tr>
                    <th>Glastype</th>
                    <td><?php echo esc_html($offerte_data['glass_type']); ?></td>
                </tr>
                <tr>
                    <th>Design</th>
                    <td><?php echo esc_html($offerte_data['design']); ?></td>
                </tr>
                <?php if ($offerte_data['design'] === 'steellook' && !empty($offerte_data['steellook_type'])): ?>
                <tr>
                    <th>Steellook type</th>
                    <td><?php echo esc_html($offerte_data['steellook_type']); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th>U-profielen</th>
                    <td><?php echo $offerte_data['has_u_profiles'] ? 'Ja' : 'Nee'; ?></td>
                </tr>
                <tr>
                    <th>Funderingskoker</th>
                    <td><?php echo $offerte_data['has_funderingskoker'] ? 'Ja' : 'Nee'; ?></td>
                </tr>
                <tr>
                    <th>Hardhout palen</th>
                    <td><?php echo $offerte_data['has_hardhout_palen'] ? 'Ja' : 'Nee'; ?></td>
                </tr>
                <tr>
                    <th>Meeneemers</th>
                    <td><?php echo esc_html($offerte_data['meeneemers_type']); ?></td>
                </tr>
                <tr>
                    <th>Tochtstrippen</th>
                    <td><?php echo $offerte_data['has_tochtstrippen'] ? 'Ja' : 'Nee'; ?></td>
                </tr>
                <tr>
                    <th>Greep type</th>
                    <td><?php echo esc_html($offerte_data['handle_type']); ?></td>
                </tr>
                <tr>
                    <th>Montage</th>
                    <td><?php echo $offerte_data['has_montage'] ? 'Ja' : 'Nee'; ?></td>
                </tr>
                <tr class="price-row">
                    <th>Geschatte prijs</th>
                    <td>&euro; <?php echo number_format((float)$offerte_data['price_estimate'], 2, ',', '.'); ?></td>
                </tr>
            </table>

            <center>
                <a href="<?php echo esc_url($offerte_data['edit_link']); ?>" class="button">
                    Bekijk in WordPress Admin
                </a>
            </center>
        </div>

        <div class="email-footer">
            <p>Dit is een geautomatiseerd bericht van de Glaswand Configurator</p>
            <p>&copy; <?php echo date('Y'); ?> De Glaswand</p>
        </div>
    </div>
</body>
</html>
