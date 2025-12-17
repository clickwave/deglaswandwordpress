<?php
/**
 * Customer Confirmation Email Template
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
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .email-header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .email-body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #1e3a8a;
            margin-bottom: 20px;
        }
        .intro-text {
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .config-summary {
            background: #f0f9ff;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
            border-left: 4px solid #1e3a8a;
        }
        .config-summary h2 {
            color: #1e3a8a;
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dbeafe;
        }
        .summary-item:last-child {
            border-bottom: none;
        }
        .summary-label {
            font-weight: bold;
            color: #555;
        }
        .summary-value {
            color: #333;
        }
        .price-highlight {
            background: #fef3c7;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 30px 0;
        }
        .price-highlight p {
            margin: 0 0 5px 0;
            color: #666;
            font-size: 14px;
        }
        .price-highlight .price {
            font-size: 32px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
        }
        .info-box h3 {
            margin-top: 0;
            color: #1e3a8a;
            font-size: 18px;
        }
        .info-box ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .info-box li {
            margin: 8px 0;
        }
        .contact-info {
            background: #1e3a8a;
            color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
            text-align: center;
        }
        .contact-info h3 {
            margin-top: 0;
            font-size: 20px;
        }
        .contact-info p {
            margin: 8px 0;
        }
        .contact-info a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .email-footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Bedankt voor uw aanvraag!</h1>
            <p>Uw offerte is succesvol ontvangen</p>
        </div>

        <div class="email-body">
            <p class="greeting">Beste <?php echo esc_html($offerte_data['customer_name']); ?>,</p>

            <div class="intro-text">
                <p>
                    Hartelijk dank voor uw interesse in onze glazen schuifwanden!
                    Wij hebben uw offerte aanvraag in goede orde ontvangen en zullen deze
                    met aandacht bekijken.
                </p>
                <p>
                    <strong>Referentienummer:</strong> #<?php echo esc_html($offerte_data['offerte_id']); ?><br>
                    <strong>Datum aanvraag:</strong> <?php echo esc_html($offerte_data['date']); ?>
                </p>
            </div>

            <div class="config-summary">
                <h2>Uw configuratie</h2>

                <div class="summary-item">
                    <span class="summary-label">Afmetingen:</span>
                    <span class="summary-value"><?php echo esc_html($offerte_data['width']); ?> x <?php echo esc_html($offerte_data['height']); ?> mm</span>
                </div>

                <div class="summary-item">
                    <span class="summary-label">Aantal rails:</span>
                    <span class="summary-value"><?php echo esc_html($offerte_data['track_count']); ?></span>
                </div>

                <div class="summary-item">
                    <span class="summary-label">Kleur kozijn:</span>
                    <span class="summary-value"><?php echo esc_html($offerte_data['frame_color']); ?></span>
                </div>

                <div class="summary-item">
                    <span class="summary-label">Glastype:</span>
                    <span class="summary-value"><?php echo esc_html($offerte_data['glass_type']); ?></span>
                </div>

                <div class="summary-item">
                    <span class="summary-label">Design:</span>
                    <span class="summary-value"><?php echo esc_html($offerte_data['design']); ?></span>
                </div>

                <?php if ($offerte_data['has_montage']): ?>
                <div class="summary-item">
                    <span class="summary-label">Montage:</span>
                    <span class="summary-value">Inclusief</span>
                </div>
                <?php endif; ?>
            </div>

            <div class="price-highlight">
                <p>Geschatte indicatieprijs</p>
                <div class="price">&euro; <?php echo number_format((float)$offerte_data['price_estimate'], 2, ',', '.'); ?></div>
                <p style="font-size: 12px; color: #666; margin-top: 10px;">
                    *Dit is een indicatieprijs. De exacte prijs wordt bepaald na opname en maatwerk.
                </p>
            </div>

            <div class="info-box">
                <h3>Wat gebeurt er nu?</h3>
                <ul>
                    <li><strong>Binnen 24 uur</strong> nemen wij telefonisch of per e-mail contact met u op</li>
                    <li>We bespreken uw wensen en beantwoorden eventuele vragen</li>
                    <li>Indien gewenst plannen we een <strong>gratis</strong> plaatsopname in</li>
                    <li>U ontvangt binnen 2-3 werkdagen een gedetailleerde offerte op maat</li>
                </ul>
            </div>

            <div class="contact-info">
                <h3>Contact</h3>
                <p>Heeft u vragen? Neem gerust contact met ons op:</p>
                <p>
                    <strong>Telefoon:</strong> <a href="tel:+31123456789">+31 (0)12 345 67 89</a><br>
                    <strong>E-mail:</strong> <a href="mailto:info@deglaswand.nl">info@deglaswand.nl</a>
                </p>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <p style="font-size: 16px; color: #1e3a8a;">
                    <strong>Wij kijken ernaar uit om uw project te realiseren!</strong>
                </p>
            </div>
        </div>

        <div class="email-footer">
            <p><strong>De Glaswand</strong></p>
            <p>Specialist in glazen schuifwanden</p>
            <p>&copy; <?php echo date('Y'); ?> De Glaswand - Alle rechten voorbehouden</p>
        </div>
    </div>
</body>
</html>
