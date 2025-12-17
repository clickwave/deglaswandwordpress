<?php
/**
 * Mini Sales CRM - Status tracking, pipeline, notities
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

class CGC_CRM {

    /**
     * Initialize
     */
    public static function init() {
        // Add CRM dashboard menu
        add_action('admin_menu', array(__CLASS__, 'add_crm_menu'), 5);

        // Add status metabox
        add_action('add_meta_boxes', array(__CLASS__, 'add_status_metabox'));

        // Add notes metabox
        add_action('add_meta_boxes', array(__CLASS__, 'add_notes_metabox'));

        // Save status
        add_action('save_post_offerte', array(__CLASS__, 'save_status'), 10, 2);

        // Add status column to admin list
        add_filter('manage_offerte_posts_columns', array(__CLASS__, 'add_status_column'));
        add_action('manage_offerte_posts_custom_column', array(__CLASS__, 'render_status_column'), 10, 2);

        // Add status filter
        add_action('restrict_manage_posts', array(__CLASS__, 'add_status_filter'));
        add_filter('parse_query', array(__CLASS__, 'filter_by_status'));

        // Make status column sortable
        add_filter('manage_edit-offerte_sortable_columns', array(__CLASS__, 'make_status_sortable'));

        // Add AJAX handler for notes
        add_action('wp_ajax_cgc_add_note', array(__CLASS__, 'ajax_add_note'));

        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_scripts'));
    }

    /**
     * Get available statuses
     */
    public static function get_statuses() {
        return array(
            'new' => array(
                'label' => 'Nieuw',
                'color' => '#0073aa',
                'icon' => '📋'
            ),
            'quote_sent' => array(
                'label' => 'Offerte Verstuurd',
                'color' => '#826eb4',
                'icon' => '📤'
            ),
            'follow_up' => array(
                'label' => 'Follow-up',
                'color' => '#f0b849',
                'icon' => '📞'
            ),
            'won' => array(
                'label' => 'Gewonnen',
                'color' => '#46b450',
                'icon' => '✅'
            ),
            'lost' => array(
                'label' => 'Verloren',
                'color' => '#dc3232',
                'icon' => '❌'
            ),
            'cancelled' => array(
                'label' => 'Geannuleerd',
                'color' => '#999',
                'icon' => '🚫'
            ),
        );
    }

    /**
     * Add CRM menu
     */
    public static function add_crm_menu() {
        add_submenu_page(
            'edit.php?post_type=offerte',
            'Sales Dashboard',
            '📊 Sales Dashboard',
            'edit_posts',
            'cgc-crm-dashboard',
            array(__CLASS__, 'render_dashboard')
        );
    }

    /**
     * Render CRM dashboard
     */
    public static function render_dashboard() {
        $statuses = self::get_statuses();

        // Get statistics
        $stats = array();
        $total_value = 0;
        $total_count = 0;

        foreach ($statuses as $status_key => $status_info) {
            $args = array(
                'post_type' => 'offerte',
                'post_status' => 'any',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => '_cgc_status',
                        'value' => $status_key,
                    ),
                ),
            );

            $query = new WP_Query($args);
            $count = $query->found_posts;
            $value = 0;

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $price = get_post_meta(get_the_ID(), '_cgc_price_estimate', true);
                    $value += floatval($price);
                }
                wp_reset_postdata();
            }

            $stats[$status_key] = array(
                'count' => $count,
                'value' => $value,
            );

            $total_count += $count;
            if ($status_key !== 'lost' && $status_key !== 'cancelled') {
                $total_value += $value;
            }
        }

        // Get recent offertes
        $recent_args = array(
            'post_type' => 'offerte',
            'posts_per_page' => 10,
            'orderby' => 'date',
            'order' => 'DESC',
        );
        $recent_query = new WP_Query($recent_args);

        ?>
        <div class="wrap">
            <h1 style="display: flex; align-items: center; gap: 10px;">
                <span class="dashicons dashicons-chart-line" style="font-size: 32px;"></span>
                Sales Dashboard
            </h1>

            <div style="background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;">Pipeline Overzicht</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
                    <?php foreach ($statuses as $status_key => $status_info): ?>
                        <a href="<?php echo admin_url('edit.php?post_type=offerte&cgc_status=' . $status_key); ?>"
                           style="text-decoration: none; color: inherit; display: block; padding: 20px; background: #f9f9f9; border-left: 4px solid <?php echo $status_info['color']; ?>; border-radius: 4px; transition: transform 0.2s;">
                            <div style="font-size: 24px; margin-bottom: 10px;"><?php echo $status_info['icon']; ?></div>
                            <div style="font-size: 13px; color: #666; margin-bottom: 8px;"><?php echo $status_info['label']; ?></div>
                            <div style="font-size: 28px; font-weight: bold; color: <?php echo $status_info['color']; ?>; margin-bottom: 5px;">
                                <?php echo $stats[$status_key]['count']; ?>
                            </div>
                            <div style="font-size: 14px; color: #666;">
                                € <?php echo number_format($stats[$status_key]['value'], 0, ',', '.'); ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                        <div>
                            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">Totaal Offertes</div>
                            <div style="font-size: 32px; font-weight: bold;"><?php echo $total_count; ?></div>
                        </div>
                        <div>
                            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">Pipeline Waarde</div>
                            <div style="font-size: 32px; font-weight: bold; color: #46b450;">€ <?php echo number_format($total_value, 0, ',', '.'); ?></div>
                        </div>
                        <div>
                            <?php
                            $won_count = $stats['won']['count'];
                            $total_decided = $won_count + $stats['lost']['count'];
                            $win_rate = $total_decided > 0 ? round(($won_count / $total_decided) * 100) : 0;
                            ?>
                            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">Win Rate</div>
                            <div style="font-size: 32px; font-weight: bold; color: #0073aa;"><?php echo $win_rate; ?>%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;">Recente Offertes</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Offerte #</th>
                            <th>Klant</th>
                            <th>Status</th>
                            <th>Waarde</th>
                            <th>Datum</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_query->have_posts()): ?>
                            <?php while ($recent_query->have_posts()): $recent_query->the_post(); ?>
                                <?php
                                $post_id = get_the_ID();
                                $customer_name = get_post_meta($post_id, '_cgc_customer_name', true);
                                $price = get_post_meta($post_id, '_cgc_price_estimate', true);
                                $status = get_post_meta($post_id, '_cgc_status', true) ?: 'new';
                                $status_info = $statuses[$status];
                                ?>
                                <tr>
                                    <td><strong>#<?php echo str_pad($post_id, 4, '0', STR_PAD_LEFT); ?></strong></td>
                                    <td><?php echo esc_html($customer_name); ?></td>
                                    <td>
                                        <span style="display: inline-block; padding: 4px 10px; background: <?php echo $status_info['color']; ?>; color: white; border-radius: 12px; font-size: 11px; font-weight: 600;">
                                            <?php echo $status_info['icon']; ?> <?php echo $status_info['label']; ?>
                                        </span>
                                    </td>
                                    <td><strong>€ <?php echo number_format($price, 2, ',', '.'); ?></strong></td>
                                    <td><?php echo get_the_date('d-m-Y'); ?></td>
                                    <td>
                                        <a href="<?php echo get_edit_post_link($post_id); ?>" class="button button-small">Bekijken</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php wp_reset_postdata(); ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                                    Nog geen offertes
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * Add status metabox
     */
    public static function add_status_metabox() {
        add_meta_box(
            'cgc_status',
            '📊 Sales Status',
            array(__CLASS__, 'render_status_metabox'),
            'offerte',
            'side',
            'high'
        );
    }

    /**
     * Render status metabox
     */
    public static function render_status_metabox($post) {
        wp_nonce_field('cgc_status_nonce', 'cgc_status_nonce_field');

        $current_status = get_post_meta($post->ID, '_cgc_status', true) ?: 'new';
        $statuses = self::get_statuses();

        ?>
        <div style="margin: 15px 0;">
            <?php foreach ($statuses as $status_key => $status_info): ?>
                <label style="display: block; padding: 12px; margin-bottom: 8px; background: <?php echo $current_status === $status_key ? $status_info['color'] : '#f9f9f9'; ?>; color: <?php echo $current_status === $status_key ? 'white' : '#333'; ?>; border-radius: 6px; cursor: pointer; transition: all 0.2s; border: 2px solid <?php echo $current_status === $status_key ? $status_info['color'] : 'transparent'; ?>;">
                    <input type="radio" name="cgc_status" value="<?php echo $status_key; ?>" <?php checked($current_status, $status_key); ?> style="margin-right: 8px;">
                    <span style="font-size: 16px; margin-right: 8px;"><?php echo $status_info['icon']; ?></span>
                    <strong><?php echo $status_info['label']; ?></strong>
                </label>
            <?php endforeach; ?>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('input[name="cgc_status"]').on('change', function() {
                var status = $(this).val();
                var statuses = <?php echo json_encode($statuses); ?>;
                var statusInfo = statuses[status];

                // Update all labels
                $('input[name="cgc_status"]').each(function() {
                    var label = $(this).parent();
                    var thisStatus = $(this).val();
                    var thisInfo = statuses[thisStatus];

                    if ($(this).is(':checked')) {
                        label.css({
                            'background': thisInfo.color,
                            'color': 'white',
                            'border-color': thisInfo.color
                        });
                    } else {
                        label.css({
                            'background': '#f9f9f9',
                            'color': '#333',
                            'border-color': 'transparent'
                        });
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Add notes metabox
     */
    public static function add_notes_metabox() {
        add_meta_box(
            'cgc_notes',
            '📝 Notities & Activiteiten',
            array(__CLASS__, 'render_notes_metabox'),
            'offerte',
            'normal',
            'high'
        );
    }

    /**
     * Render notes metabox
     */
    public static function render_notes_metabox($post) {
        $notes = get_post_meta($post->ID, '_cgc_notes', true);
        if (!is_array($notes)) {
            $notes = array();
        }

        // Sort by date descending
        usort($notes, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        ?>
        <div style="margin: 15px 0;">
            <!-- Add note form -->
            <div style="background: #f9f9f9; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                <textarea id="cgc-new-note" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Voeg een notitie of activiteit toe..."></textarea>
                <div style="margin-top: 10px; display: flex; gap: 10px;">
                    <button type="button" class="button button-primary" id="cgc-add-note" data-post-id="<?php echo $post->ID; ?>">
                        Notitie Toevoegen
                    </button>
                    <select id="cgc-note-type" style="padding: 5px;">
                        <option value="note">📝 Notitie</option>
                        <option value="call">📞 Telefoongesprek</option>
                        <option value="email">📧 Email</option>
                        <option value="meeting">🤝 Afspraak</option>
                    </select>
                </div>
            </div>

            <!-- Notes list -->
            <div id="cgc-notes-list">
                <?php if (empty($notes)): ?>
                    <p style="text-align: center; color: #999; padding: 40px 0;">
                        Nog geen notities toegevoegd
                    </p>
                <?php else: ?>
                    <?php foreach ($notes as $note): ?>
                        <?php
                        $type_icons = array(
                            'note' => '📝',
                            'call' => '📞',
                            'email' => '📧',
                            'meeting' => '🤝'
                        );
                        $icon = isset($type_icons[$note['type']]) ? $type_icons[$note['type']] : '📝';
                        ?>
                        <div style="background: white; padding: 15px; border-left: 3px solid #0073aa; margin-bottom: 10px; border-radius: 4px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                <div style="font-size: 13px; color: #666;">
                                    <span style="font-size: 16px; margin-right: 5px;"><?php echo $icon; ?></span>
                                    <strong><?php echo esc_html($note['user']); ?></strong>
                                </div>
                                <div style="font-size: 12px; color: #999;">
                                    <?php echo date('d-m-Y H:i', strtotime($note['date'])); ?>
                                </div>
                            </div>
                            <div style="line-height: 1.6;">
                                <?php echo nl2br(esc_html($note['content'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Save status
     */
    public static function save_status($post_id, $post) {
        // Check nonce
        if (!isset($_POST['cgc_status_nonce_field']) || !wp_verify_nonce($_POST['cgc_status_nonce_field'], 'cgc_status_nonce')) {
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

        // Save status
        if (isset($_POST['cgc_status'])) {
            update_post_meta($post_id, '_cgc_status', sanitize_text_field($_POST['cgc_status']));
        }
    }

    /**
     * Add status column
     */
    public static function add_status_column($columns) {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            if ($key === 'date') {
                $new_columns['cgc_status'] = '📊 Status';
            }
            $new_columns[$key] = $value;
        }
        return $new_columns;
    }

    /**
     * Render status column
     */
    public static function render_status_column($column, $post_id) {
        if ($column === 'cgc_status') {
            $status = get_post_meta($post_id, '_cgc_status', true) ?: 'new';
            $statuses = self::get_statuses();
            $status_info = isset($statuses[$status]) ? $statuses[$status] : $statuses['new'];

            echo '<span style="display: inline-block; padding: 4px 10px; background: ' . $status_info['color'] . '; color: white; border-radius: 12px; font-size: 11px; font-weight: 600; white-space: nowrap;">';
            echo $status_info['icon'] . ' ' . $status_info['label'];
            echo '</span>';
        }
    }

    /**
     * Add status filter
     */
    public static function add_status_filter() {
        global $typenow;

        if ($typenow === 'offerte') {
            $statuses = self::get_statuses();
            $current = isset($_GET['cgc_status']) ? $_GET['cgc_status'] : '';

            echo '<select name="cgc_status" style="margin-left: 10px;">';
            echo '<option value="">Alle Statussen</option>';
            foreach ($statuses as $status_key => $status_info) {
                echo '<option value="' . $status_key . '" ' . selected($current, $status_key, false) . '>';
                echo $status_info['icon'] . ' ' . $status_info['label'];
                echo '</option>';
            }
            echo '</select>';
        }
    }

    /**
     * Filter by status
     */
    public static function filter_by_status($query) {
        global $pagenow, $typenow;

        if ($pagenow === 'edit.php' && $typenow === 'offerte' && isset($_GET['cgc_status']) && $_GET['cgc_status'] !== '') {
            $query->set('meta_key', '_cgc_status');
            $query->set('meta_value', sanitize_text_field($_GET['cgc_status']));
        }
    }

    /**
     * Make status sortable
     */
    public static function make_status_sortable($columns) {
        $columns['cgc_status'] = 'cgc_status';
        return $columns;
    }

    /**
     * AJAX: Add note
     */
    public static function ajax_add_note() {
        check_ajax_referer('cgc_add_note', 'nonce');

        $post_id = intval($_POST['post_id']);
        $content = sanitize_textarea_field($_POST['content']);
        $type = sanitize_text_field($_POST['type']);

        if (!current_user_can('edit_post', $post_id)) {
            wp_send_json_error('Unauthorized');
        }

        $notes = get_post_meta($post_id, '_cgc_notes', true);
        if (!is_array($notes)) {
            $notes = array();
        }

        $user = wp_get_current_user();

        $new_note = array(
            'date' => current_time('mysql'),
            'user' => $user->display_name,
            'type' => $type,
            'content' => $content,
        );

        array_unshift($notes, $new_note);
        update_post_meta($post_id, '_cgc_notes', $notes);

        wp_send_json_success($new_note);
    }

    /**
     * Enqueue admin scripts
     */
    public static function enqueue_admin_scripts($hook) {
        if ($hook === 'post.php' || $hook === 'post-new.php') {
            global $post;
            if ($post && $post->post_type === 'offerte') {
                wp_enqueue_script('cgc-crm-js', CGC_PLUGIN_URL . 'assets/admin/admin-crm.js', array('jquery'), CGC_VERSION, true);
                wp_localize_script('cgc-crm-js', 'cgcCRM', array(
                    'ajaxUrl' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('cgc_add_note'),
                ));
            }
        }
    }
}
