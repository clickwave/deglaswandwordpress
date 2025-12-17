<?php
/**
 * Customer Portal - Handle user registration and customer dashboard
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

class CGC_Customer_Portal {

    /**
     * Initialize the customer portal
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'add_rewrite_rules'));
        add_action('template_redirect', array(__CLASS__, 'handle_portal_page'));
        add_filter('query_vars', array(__CLASS__, 'add_query_vars'));

        // AJAX handlers
        add_action('wp_ajax_cgc_approve_quote', array(__CLASS__, 'approve_quote'));
        add_action('wp_ajax_cgc_reject_quote', array(__CLASS__, 'reject_quote'));
    }

    /**
     * Add rewrite rules for customer portal
     */
    public static function add_rewrite_rules() {
        add_rewrite_rule(
            '^mijn-account/?$',
            'index.php?cgc_portal=dashboard',
            'top'
        );

        add_rewrite_rule(
            '^mijn-account/offerte/([0-9]+)/?$',
            'index.php?cgc_portal=quote&quote_id=$matches[1]',
            'top'
        );
    }

    /**
     * Add custom query vars
     */
    public static function add_query_vars($vars) {
        $vars[] = 'cgc_portal';
        $vars[] = 'quote_id';
        return $vars;
    }

    /**
     * Handle portal page display
     */
    public static function handle_portal_page() {
        $portal = get_query_var('cgc_portal');

        if (!$portal) {
            return;
        }

        // Check if user is logged in
        if (!is_user_logged_in()) {
            auth_redirect();
            exit;
        }

        // Load appropriate template
        switch ($portal) {
            case 'dashboard':
                self::render_dashboard();
                break;
            case 'quote':
                self::render_quote_detail();
                break;
        }

        exit;
    }

    /**
     * Create customer account when quote is submitted
     */
    public static function create_customer_account($customer_email, $customer_name, $offerte_id) {
        // Check if user already exists
        $user = get_user_by('email', $customer_email);

        if ($user) {
            // Link offerte to existing user
            update_post_meta($offerte_id, '_cgc_customer_user_id', $user->ID);
            return $user->ID;
        }

        // Create new user
        $username = sanitize_user(strtolower(str_replace(' ', '', $customer_name))) . '_' . time();
        $password = wp_generate_password(12, true, true);

        $user_id = wp_create_user($username, $password, $customer_email);

        if (is_wp_error($user_id)) {
            error_log('CGC: Failed to create user for offerte #' . $offerte_id . ': ' . $user_id->get_error_message());
            return false;
        }

        // Update user meta
        wp_update_user(array(
            'ID'           => $user_id,
            'display_name' => $customer_name,
            'first_name'   => explode(' ', $customer_name)[0],
            'last_name'    => implode(' ', array_slice(explode(' ', $customer_name), 1)),
            'role'         => 'customer',
        ));

        // Link offerte to user
        update_post_meta($offerte_id, '_cgc_customer_user_id', $user_id);

        // Send welcome email with login credentials
        self::send_welcome_email($user_id, $customer_email, $password, $offerte_id);

        return $user_id;
    }

    /**
     * Send welcome email to new customer
     */
    private static function send_welcome_email($user_id, $email, $password, $offerte_id) {
        $subject = 'Welkom bij De Glaswand - Uw account is aangemaakt';

        $login_url = home_url('/mijn-account/');
        $quote_url = home_url('/mijn-account/offerte/' . $offerte_id . '/');

        $message = sprintf(
            "Beste %s,\n\nBedankt voor uw offerte-aanvraag bij De Glaswand!\n\n" .
            "We hebben een persoonlijk account voor u aangemaakt waar u de status van uw offerte kunt volgen.\n\n" .
            "=== Inloggegevens ===\n" .
            "E-mailadres: %s\n" .
            "Wachtwoord: %s\n\n" .
            "Inloggen: %s\n\n" .
            "Uw offerte bekijken: %s\n\n" .
            "Tip: Wijzig uw wachtwoord na het eerste inloggen.\n\n" .
            "Met vriendelijke groet,\n" .
            "Team De Glaswand\n\n" .
            "---\n" .
            "Tel: 06 15 24 63 83\n" .
            "Email: info@deglaswand.nl",
            get_user_meta($user_id, 'first_name', true),
            $email,
            $password,
            $login_url,
            $quote_url
        );

        $headers = array(
            'From: De Glaswand <info@deglaswand.nl>',
            'Content-Type: text/plain; charset=UTF-8',
        );

        wp_mail($email, $subject, $message, $headers);
    }

    /**
     * Render customer dashboard
     */
    private static function render_dashboard() {
        $current_user = wp_get_current_user();

        // Get user's quotes
        $quotes = self::get_user_quotes($current_user->ID);

        get_header();
        ?>

        <div class="cgc-customer-portal">
            <div class="cgc-portal-container" style="max-width: 1200px; margin: 60px auto; padding: 0 20px;">

                <h1 style="font-size: 36px; margin-bottom: 12px;">Mijn Account</h1>
                <p style="color: #666; margin-bottom: 40px;">Welkom terug, <?php echo esc_html($current_user->display_name); ?>!</p>

                <div class="cgc-portal-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 24px; margin-bottom: 60px;">

                    <?php if (empty($quotes)): ?>
                        <div class="cgc-empty-state" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #f8f9fa; border-radius: 12px;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5" style="margin: 0 auto 20px;">
                                <path d="M9 12h6M9 16h6M13 4H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V9l-6-5z"/>
                            </svg>
                            <h3 style="color: #333; margin-bottom: 8px;">Geen offertes gevonden</h3>
                            <p style="color: #666; margin-bottom: 24px;">U heeft nog geen offertes aangevraagd.</p>
                            <a href="<?php echo home_url('/configurator/'); ?>" class="button button-primary" style="display: inline-block; padding: 12px 24px; background: #eb512f; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                                Nieuwe offerte aanvragen
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($quotes as $quote):
                            $status = get_post_meta($quote->ID, '_cgc_quote_status', true) ?: 'pending';
                            $status_labels = array(
                                'pending' => array('label' => 'In behandeling', 'color' => '#f59e0b'),
                                'approved' => array('label' => 'Goedgekeurd', 'color' => '#10b981'),
                                'rejected' => array('label' => 'Afgewezen', 'color' => '#ef4444'),
                            );
                            $status_info = $status_labels[$status];
                        ?>
                        <div class="cgc-quote-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: transform 0.2s, box-shadow 0.2s;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                                <div>
                                    <span style="display: inline-block; padding: 4px 12px; background: <?php echo $status_info['color']; ?>20; color: <?php echo $status_info['color']; ?>; font-size: 12px; font-weight: 600; border-radius: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                        <?php echo esc_html($status_info['label']); ?>
                                    </span>
                                </div>
                                <span style="color: #999; font-size: 13px;">
                                    <?php echo get_the_date('d-m-Y', $quote->ID); ?>
                                </span>
                            </div>

                            <h3 style="font-size: 18px; margin-bottom: 12px; color: #1f3d58;">
                                Offerte #<?php echo $quote->ID; ?>
                            </h3>

                            <div style="display: grid; gap: 8px; margin-bottom: 20px; font-size: 14px; color: #666;">
                                <div>
                                    <strong>Afmetingen:</strong>
                                    <?php echo esc_html(get_post_meta($quote->ID, '_cgc_width', true)); ?>mm ×
                                    <?php echo esc_html(get_post_meta($quote->ID, '_cgc_height', true)); ?>mm
                                </div>
                                <div>
                                    <strong>Rails:</strong>
                                    <?php echo esc_html(get_post_meta($quote->ID, '_cgc_track_count', true)); ?>
                                </div>
                                <div>
                                    <strong>Design:</strong>
                                    <?php echo esc_html(ucfirst(get_post_meta($quote->ID, '_cgc_design', true))); ?>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid #f0f0f0;">
                                <div>
                                    <span style="font-size: 13px; color: #999;">Geschatte prijs</span>
                                    <div style="font-size: 24px; font-weight: 700; color: #1f3d58;">
                                        € <?php echo number_format(get_post_meta($quote->ID, '_cgc_price_estimate', true), 2, ',', '.'); ?>
                                    </div>
                                </div>
                                <a href="<?php echo home_url('/mijn-account/offerte/' . $quote->ID . '/'); ?>"
                                   class="button"
                                   style="display: inline-block; padding: 10px 20px; background: #1f3d58; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; transition: background 0.2s;">
                                    Details
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>

                <div style="text-align: center; padding: 40px 0;">
                    <a href="<?php echo home_url('/configurator/'); ?>" class="button button-primary" style="display: inline-block; padding: 14px 32px; background: #eb512f; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px;">
                        Nieuwe offerte aanvragen
                    </a>
                </div>

            </div>
        </div>

        <?php
        get_footer();
    }

    /**
     * Render quote detail page
     */
    private static function render_quote_detail() {
        $quote_id = get_query_var('quote_id');
        $current_user = wp_get_current_user();

        // Verify quote belongs to user
        $quote_user_id = get_post_meta($quote_id, '_cgc_customer_user_id', true);

        if ($quote_user_id != $current_user->ID && !current_user_can('manage_options')) {
            wp_die('Geen toegang tot deze offerte.');
        }

        $quote = get_post($quote_id);
        if (!$quote || $quote->post_type !== 'offerte') {
            wp_die('Offerte niet gevonden.');
        }

        $status = get_post_meta($quote_id, '_cgc_quote_status', true) ?: 'pending';

        get_header();
        ?>

        <div class="cgc-quote-detail">
            <div class="cgc-detail-container" style="max-width: 900px; margin: 60px auto; padding: 0 20px;">

                <a href="<?php echo home_url('/mijn-account/'); ?>" style="display: inline-flex; align-items: center; gap: 8px; color: #666; text-decoration: none; margin-bottom: 32px; font-size: 14px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Terug naar overzicht
                </a>

                <div style="background: white; border-radius: 12px; padding: 40px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">

                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 32px;">
                        <div>
                            <h1 style="font-size: 32px; margin-bottom: 8px;">Offerte #<?php echo $quote_id; ?></h1>
                            <p style="color: #666;">Aangevraagd op <?php echo get_the_date('d F Y', $quote_id); ?></p>
                        </div>
                        <?php
                        $status_labels = array(
                            'pending' => array('label' => 'In behandeling', 'color' => '#f59e0b'),
                            'approved' => array('label' => 'Goedgekeurd door klant', 'color' => '#10b981'),
                            'rejected' => array('label' => 'Afgewezen door klant', 'color' => '#ef4444'),
                        );
                        $status_info = $status_labels[$status];
                        ?>
                        <span style="display: inline-block; padding: 8px 16px; background: <?php echo $status_info['color']; ?>20; color: <?php echo $status_info['color']; ?>; font-size: 13px; font-weight: 600; border-radius: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <?php echo esc_html($status_info['label']); ?>
                        </span>
                    </div>

                    <?php self::render_quote_configuration($quote_id); ?>

                    <div style="margin-top: 40px; padding-top: 32px; border-top: 2px solid #f0f0f0;">
                        <div style="text-align: right; margin-bottom: 32px;">
                            <span style="font-size: 14px; color: #999; display: block; margin-bottom: 8px;">Geschatte prijs (excl. BTW)</span>
                            <div style="font-size: 42px; font-weight: 700; color: #1f3d58;">
                                € <?php echo number_format(get_post_meta($quote_id, '_cgc_price_estimate', true), 2, ',', '.'); ?>
                            </div>
                            <span style="font-size: 13px; color: #666;">Inclusief BTW: € <?php echo number_format(get_post_meta($quote_id, '_cgc_price_estimate', true) * 1.21, 2, ',', '.'); ?></span>
                        </div>

                        <?php if ($status === 'pending'): ?>
                        <div style="background: #f8f9fa; padding: 24px; border-radius: 8px; margin-bottom: 24px;">
                            <h3 style="font-size: 16px; margin-bottom: 12px;">Offerte goedkeuren</h3>
                            <p style="font-size: 14px; color: #666; margin-bottom: 20px;">
                                Wij nemen zo spoedig mogelijk contact met u op om de definitieve offerte te bespreken.
                                U kunt deze offerte alvast goedkeuren of afwijzen.
                            </p>
                            <div style="display: flex; gap: 12px;">
                                <button onclick="approveQuote(<?php echo $quote_id; ?>)"
                                        class="cgc-approve-btn"
                                        style="flex: 1; padding: 14px; background: #10b981; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 15px;">
                                    Offerte goedkeuren
                                </button>
                                <button onclick="rejectQuote(<?php echo $quote_id; ?>)"
                                        class="cgc-reject-btn"
                                        style="flex: 1; padding: 14px; background: white; color: #ef4444; border: 2px solid #ef4444; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 15px;">
                                    Offerte afwijzen
                                </button>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>

                </div>

            </div>
        </div>

        <script>
        function approveQuote(quoteId) {
            if (!confirm('Weet u zeker dat u deze offerte wilt goedkeuren?')) {
                return;
            }

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'cgc_approve_quote',
                    quote_id: quoteId,
                    _wpnonce: '<?php echo wp_create_nonce('cgc_approve_quote'); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Offerte goedgekeurd! We nemen zo spoedig mogelijk contact met u op.');
                    location.reload();
                } else {
                    alert('Er is iets misgegaan. Probeer het later opnieuw.');
                }
            });
        }

        function rejectQuote(quoteId) {
            if (!confirm('Weet u zeker dat u deze offerte wilt afwijzen?')) {
                return;
            }

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'cgc_reject_quote',
                    quote_id: quoteId,
                    _wpnonce: '<?php echo wp_create_nonce('cgc_reject_quote'); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Offerte afgewezen.');
                    location.reload();
                } else {
                    alert('Er is iets misgegaan. Probeer het later opnieuw.');
                }
            });
        }
        </script>

        <?php
        get_footer();
    }

    /**
     * Render quote configuration details
     */
    private static function render_quote_configuration($quote_id) {
        ?>
        <div class="cgc-config-details" style="display: grid; gap: 24px;">

            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                <h3 style="font-size: 14px; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px;">Configuratie Details</h3>
                <div style="display: grid; gap: 12px;">
                    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 12px;">
                        <span style="color: #666;">Afmetingen:</span>
                        <strong><?php echo esc_html(get_post_meta($quote_id, '_cgc_width', true)); ?>mm × <?php echo esc_html(get_post_meta($quote_id, '_cgc_height', true)); ?>mm</strong>
                    </div>
                    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 12px;">
                        <span style="color: #666;">Aantal rails:</span>
                        <strong><?php echo esc_html(get_post_meta($quote_id, '_cgc_track_count', true)); ?></strong>
                    </div>
                    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 12px;">
                        <span style="color: #666;">Kleur kozijn:</span>
                        <strong><?php echo esc_html(get_post_meta($quote_id, '_cgc_frame_color', true)); ?></strong>
                    </div>
                    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 12px;">
                        <span style="color: #666;">Glastype:</span>
                        <strong><?php echo esc_html(ucfirst(get_post_meta($quote_id, '_cgc_glass_type', true))); ?></strong>
                    </div>
                    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 12px;">
                        <span style="color: #666;">Design:</span>
                        <strong><?php echo esc_html(ucfirst(get_post_meta($quote_id, '_cgc_design', true))); ?></strong>
                    </div>
                    <?php if (get_post_meta($quote_id, '_cgc_design', true) === 'steellook'): ?>
                    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 12px;">
                        <span style="color: #666;">Steellook type:</span>
                        <strong><?php echo esc_html(ucfirst(get_post_meta($quote_id, '_cgc_steellook_type', true))); ?></strong>
                    </div>
                    <?php endif; ?>
                    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 12px;">
                        <span style="color: #666;">Greep type:</span>
                        <strong><?php echo esc_html(ucfirst(get_post_meta($quote_id, '_cgc_handle_type', true))); ?></strong>
                    </div>
                </div>
            </div>

            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                <h3 style="font-size: 14px; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px;">Opties & Accessoires</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px;">
                    <?php
                    $options = array(
                        '_cgc_has_u_profiles' => 'U-profielen',
                        '_cgc_has_funderingskoker' => 'Funderingskoker',
                        '_cgc_has_hardhout_palen' => 'Hardhout palen',
                        '_cgc_has_tochtstrippen' => 'Tochtstrippen',
                        '_cgc_has_montage' => 'Montage',
                    );

                    foreach ($options as $meta_key => $label):
                        $value = get_post_meta($quote_id, $meta_key, true);
                        $icon = $value ? '✓' : '—';
                        $color = $value ? '#10b981' : '#ccc';
                    ?>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="color: <?php echo $color; ?>; font-weight: bold;"><?php echo $icon; ?></span>
                            <span style="color: #666;"><?php echo esc_html($label); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php
            $message = get_post_meta($quote_id, '_cgc_customer_message', true);
            if ($message):
            ?>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                <h3 style="font-size: 14px; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Uw bericht</h3>
                <p style="color: #666; line-height: 1.6;"><?php echo nl2br(esc_html($message)); ?></p>
            </div>
            <?php endif; ?>

        </div>
        <?php
    }

    /**
     * Get quotes for a user
     */
    private static function get_user_quotes($user_id) {
        $args = array(
            'post_type'      => 'offerte',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'   => '_cgc_customer_user_id',
                    'value' => $user_id,
                ),
            ),
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        return get_posts($args);
    }

    /**
     * AJAX: Approve quote
     */
    public static function approve_quote() {
        check_ajax_referer('cgc_approve_quote');

        if (!is_user_logged_in()) {
            wp_send_json_error('Not logged in');
        }

        $quote_id = intval($_POST['quote_id']);
        $user_id = get_current_user_id();

        // Verify quote belongs to user
        if (get_post_meta($quote_id, '_cgc_customer_user_id', true) != $user_id) {
            wp_send_json_error('Unauthorized');
        }

        // Update status
        update_post_meta($quote_id, '_cgc_quote_status', 'approved');
        update_post_meta($quote_id, '_cgc_quote_approved_date', current_time('mysql'));

        // Send notification to admin
        $admin_email = get_option('admin_email');
        $subject = 'Offerte #' . $quote_id . ' goedgekeurd door klant';
        $message = sprintf(
            "De klant heeft offerte #%d goedgekeurd.\n\nBekijk de offerte: %s",
            $quote_id,
            admin_url('post.php?post=' . $quote_id . '&action=edit')
        );

        wp_mail($admin_email, $subject, $message);

        wp_send_json_success();
    }

    /**
     * AJAX: Reject quote
     */
    public static function reject_quote() {
        check_ajax_referer('cgc_reject_quote');

        if (!is_user_logged_in()) {
            wp_send_json_error('Not logged in');
        }

        $quote_id = intval($_POST['quote_id']);
        $user_id = get_current_user_id();

        // Verify quote belongs to user
        if (get_post_meta($quote_id, '_cgc_customer_user_id', true) != $user_id) {
            wp_send_json_error('Unauthorized');
        }

        // Update status
        update_post_meta($quote_id, '_cgc_quote_status', 'rejected');
        update_post_meta($quote_id, '_cgc_quote_rejected_date', current_time('mysql'));

        // Send notification to admin
        $admin_email = get_option('admin_email');
        $subject = 'Offerte #' . $quote_id . ' afgewezen door klant';
        $message = sprintf(
            "De klant heeft offerte #%d afgewezen.\n\nBekijk de offerte: %s",
            $quote_id,
            admin_url('post.php?post=' . $quote_id . '&action=edit')
        );

        wp_mail($admin_email, $subject, $message);

        wp_send_json_success();
    }
}
