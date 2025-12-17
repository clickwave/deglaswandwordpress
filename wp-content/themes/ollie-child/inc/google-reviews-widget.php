<?php
/**
 * Google Reviews Widget
 * Displays live Google reviews using the Google Places API
 */

// Add shortcode for Google reviews
add_shortcode('google_reviews', 'cgc_google_reviews_shortcode');

function cgc_google_reviews_shortcode($atts) {
    $atts = shortcode_atts(array(
        'place_id' => 'ChIJTf_c8q762_',  // Dé Glaswand Place ID
        'max_reviews' => 6,
        'min_rating' => 4,
    ), $atts);

    ob_start();
    ?>
    <div class="google-reviews-widget" data-place-id="<?php echo esc_attr($atts['place_id']); ?>" data-max-reviews="<?php echo esc_attr($atts['max_reviews']); ?>" data-min-rating="<?php echo esc_attr($atts['min_rating']); ?>">
        <div class="reviews-loading" style="text-align: center; padding: 60px 20px;">
            <div class="spinner" style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #1f3d58; border-radius: 50%; animation: spin 1s linear infinite;"></div>
            <p style="margin-top: 20px; color: #666;">Reviews laden...</p>
        </div>
        <div class="reviews-container" style="display: none;"></div>
        <div class="reviews-error" style="display: none; text-align: center; padding: 40px 20px;">
            <p style="color: #999;">Google reviews kunnen momenteel niet worden geladen.</p>
        </div>
    </div>

    <style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .google-reviews-widget {
        width: 100%;
        margin: 0 auto;
    }

    .reviews-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 24px;
        margin-top: 40px;
    }

    .review-card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .review-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .review-header {
        display: flex;
        align-items: center;
        margin-bottom: 16px;
        gap: 12px;
    }

    .review-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #1f3d58;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 18px;
        flex-shrink: 0;
    }

    .review-author-info {
        flex: 1;
    }

    .review-author {
        font-weight: 600;
        font-size: 16px;
        color: #1f3d58;
        margin: 0 0 4px 0;
    }

    .review-rating {
        color: #f59e0b;
        font-size: 16px;
        letter-spacing: 2px;
    }

    .review-text {
        color: #4b5563;
        line-height: 1.6;
        font-size: 15px;
        margin: 0 0 12px 0;
    }

    .review-date {
        color: #9ca3af;
        font-size: 13px;
    }

    .reviews-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .google-rating-badge {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: white;
        padding: 16px 32px;
        border-radius: 50px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    .google-logo {
        width: 32px;
        height: 32px;
    }

    .rating-info {
        text-align: left;
    }

    .rating-stars {
        color: #f59e0b;
        font-size: 20px;
        letter-spacing: 2px;
    }

    .rating-text {
        color: #6b7280;
        font-size: 14px;
        margin: 0;
    }

    .view-all-button {
        text-align: center;
        margin-top: 40px;
    }

    .view-all-button a {
        display: inline-block;
        background: #1f3d58;
        color: white;
        padding: 14px 32px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: background 0.2s ease;
    }

    .view-all-button a:hover {
        background: #152b3f;
    }

    @media (max-width: 768px) {
        .reviews-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>
    <?php
    return ob_get_clean();
}

// Enqueue Google Places API and custom script
add_action('wp_enqueue_scripts', 'cgc_enqueue_google_reviews_scripts');

function cgc_enqueue_google_reviews_scripts() {
    // Note: You'll need to add your Google API key in the theme settings
    $google_api_key = get_option('cgc_google_api_key', '');

    if ($google_api_key) {
        wp_enqueue_script(
            'google-places-api',
            'https://maps.googleapis.com/maps/api/js?key=' . $google_api_key . '&libraries=places',
            array(),
            null,
            true
        );
    }

    wp_enqueue_script(
        'cgc-google-reviews',
        get_stylesheet_directory_uri() . '/assets/js/google-reviews.js',
        array('jquery'),
        '1.0.0',
        true
    );
}

// Add settings page for Google API key
add_action('admin_menu', 'cgc_reviews_settings_menu');

function cgc_reviews_settings_menu() {
    add_options_page(
        'Google Reviews Instellingen',
        'Google Reviews',
        'manage_options',
        'cgc-google-reviews',
        'cgc_reviews_settings_page'
    );
}

function cgc_reviews_settings_page() {
    if (isset($_POST['cgc_save_api_key'])) {
        update_option('cgc_google_api_key', sanitize_text_field($_POST['google_api_key']));
        echo '<div class="notice notice-success"><p>API sleutel opgeslagen!</p></div>';
    }

    $api_key = get_option('cgc_google_api_key', '');
    ?>
    <div class="wrap">
        <h1>Google Reviews Instellingen</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="google_api_key">Google Places API Sleutel</label></th>
                    <td>
                        <input type="text" id="google_api_key" name="google_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text">
                        <p class="description">
                            Voer je Google Places API sleutel in. <a href="https://console.cloud.google.com/apis/credentials" target="_blank">Maak een API sleutel aan</a> en activeer de "Places API".
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Opslaan', 'primary', 'cgc_save_api_key'); ?>
        </form>

        <hr>

        <h2>Shortcode Gebruik</h2>
        <p>Gebruik de volgende shortcode om Google reviews te tonen:</p>
        <code>[google_reviews]</code>

        <h3>Parameters:</h3>
        <ul>
            <li><code>place_id</code> - Google Place ID (standaard: Dé Glaswand)</li>
            <li><code>max_reviews</code> - Maximum aantal reviews (standaard: 6)</li>
            <li><code>min_rating</code> - Minimum rating om te tonen (standaard: 4)</li>
        </ul>

        <p>Voorbeeld: <code>[google_reviews max_reviews="9" min_rating="5"]</code></p>
    </div>
    <?php
}
