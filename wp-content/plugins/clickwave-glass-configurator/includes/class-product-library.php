<?php
/**
 * Product Library - Flexibele producten/opties bibliotheek
 * Admin kan hier zelf producten toevoegen die gebruikt kunnen worden
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

class CGC_Product_Library {

    /**
     * Initialize
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_menu_page'));
        add_action('admin_post_cgc_save_product', array(__CLASS__, 'save_product'));
        add_action('admin_post_cgc_delete_product', array(__CLASS__, 'delete_product'));
    }

    /**
     * Add menu page
     */
    public static function add_menu_page() {
        add_submenu_page(
            'edit.php?post_type=offerte',
            __('Product Bibliotheek', 'clickwave-glass'),
            __('Product Bibliotheek', 'clickwave-glass'),
            'manage_options',
            'cgc-product-library',
            array(__CLASS__, 'render_library_page')
        );
    }

    /**
     * Get all products
     */
    public static function get_products() {
        $products = get_option('cgc_product_library', array());
        if (!is_array($products)) {
            $products = array();
        }
        return $products;
    }

    /**
     * Render library page
     */
    public static function render_library_page() {
        $products = self::get_products();

        // Handle edit mode
        $edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : null;
        $edit_product = null;
        if ($edit_id !== null && isset($products[$edit_id])) {
            $edit_product = $products[$edit_id];
        }
        ?>
        <div class="wrap">
            <h1><?php _e('Product Bibliotheek', 'clickwave-glass'); ?></h1>
            <p><?php _e('Beheer hier alle producten/opties die je kunt toevoegen aan offertes. Deze zijn flexibel en kunnen alles zijn (rails, panelen, accessoires, diensten).', 'clickwave-glass'); ?></p>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-top: 30px;">

                <!-- Add/Edit Product Form -->
                <div style="background: white; padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
                    <h2><?php echo $edit_product ? __('Product Bewerken', 'clickwave-glass') : __('Nieuw Product', 'clickwave-glass'); ?></h2>

                    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                        <input type="hidden" name="action" value="cgc_save_product">
                        <?php wp_nonce_field('cgc_product_action', 'cgc_product_nonce'); ?>

                        <?php if ($edit_id !== null): ?>
                            <input type="hidden" name="product_id" value="<?php echo $edit_id; ?>">
                        <?php endif; ?>

                        <table class="form-table">
                            <tr>
                                <th><label for="product_name">Naam *</label></th>
                                <td>
                                    <input type="text" id="product_name" name="product_name" value="<?php echo $edit_product ? esc_attr($edit_product['name']) : ''; ?>" class="regular-text" required>
                                    <p class="description">Bijv: "Extra 2-rails schuifwand", "Sandwichpaneel", "Montage"</p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="product_category">Categorie</label></th>
                                <td>
                                    <select id="product_category" name="product_category">
                                        <option value="rail" <?php echo ($edit_product && $edit_product['category'] === 'rail') ? 'selected' : ''; ?>>Rails/Schuifwanden</option>
                                        <option value="panel" <?php echo ($edit_product && $edit_product['category'] === 'panel') ? 'selected' : ''; ?>>Panelen</option>
                                        <option value="accessory" <?php echo ($edit_product && $edit_product['category'] === 'accessory') ? 'selected' : ''; ?>>Accessoires</option>
                                        <option value="service" <?php echo ($edit_product && $edit_product['category'] === 'service') ? 'selected' : ''; ?>>Diensten</option>
                                        <option value="other" <?php echo ($edit_product && $edit_product['category'] === 'other') ? 'selected' : ''; ?>>Overig</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="product_price">Prijs (€) *</label></th>
                                <td>
                                    <input type="number" id="product_price" name="product_price" value="<?php echo $edit_product ? esc_attr($edit_product['price']) : ''; ?>" step="0.01" min="0" class="regular-text" required>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="product_description">Beschrijving</label></th>
                                <td>
                                    <textarea id="product_description" name="product_description" rows="3" class="large-text"><?php echo $edit_product ? esc_textarea($edit_product['description']) : ''; ?></textarea>
                                    <p class="description">Optionele notities of specificaties</p>
                                </td>
                            </tr>
                        </table>

                        <p class="submit">
                            <button type="submit" class="button button-primary">
                                <?php echo $edit_product ? __('Product Bijwerken', 'clickwave-glass') : __('Product Toevoegen', 'clickwave-glass'); ?>
                            </button>
                            <?php if ($edit_id !== null): ?>
                                <a href="<?php echo admin_url('edit.php?post_type=offerte&page=cgc-product-library'); ?>" class="button">Annuleren</a>
                            <?php endif; ?>
                        </p>
                    </form>
                </div>

                <!-- Products List -->
                <div style="background: white; padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
                    <h2><?php _e('Alle Producten', 'clickwave-glass'); ?></h2>

                    <?php if (empty($products)): ?>
                        <p style="color: #999; text-align: center; padding: 40px 0;">
                            Nog geen producten toegevoegd. Voeg je eerste product toe via het formulier links.
                        </p>
                    <?php else: ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">Naam</th>
                                    <th style="width: 20%;">Categorie</th>
                                    <th style="width: 15%;">Prijs</th>
                                    <th style="width: 25%;">Beschrijving</th>
                                    <th style="width: 10%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $id => $product): ?>
                                    <tr>
                                        <td><strong><?php echo esc_html($product['name']); ?></strong></td>
                                        <td>
                                            <?php
                                            $categories = array(
                                                'rail' => 'Rails/Schuifwanden',
                                                'panel' => 'Panelen',
                                                'accessory' => 'Accessoires',
                                                'service' => 'Diensten',
                                                'other' => 'Overig'
                                            );
                                            echo esc_html($categories[$product['category']] ?? 'Overig');
                                            ?>
                                        </td>
                                        <td><strong>€ <?php echo number_format($product['price'], 2, ',', '.'); ?></strong></td>
                                        <td><?php echo esc_html($product['description']); ?></td>
                                        <td>
                                            <a href="<?php echo admin_url('edit.php?post_type=offerte&page=cgc-product-library&edit=' . $id); ?>" class="button button-small">Bewerken</a>
                                            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display: inline;">
                                                <input type="hidden" name="action" value="cgc_delete_product">
                                                <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                                <?php wp_nonce_field('cgc_product_action', 'cgc_product_nonce'); ?>
                                                <button type="submit" class="button button-small" onclick="return confirm('Weet je zeker dat je dit product wilt verwijderen?');" style="color: #dc3232;">Verwijderen</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

            </div>
        </div>
        <?php
    }

    /**
     * Save product
     */
    public static function save_product() {
        // Check nonce
        if (!isset($_POST['cgc_product_nonce']) || !wp_verify_nonce($_POST['cgc_product_nonce'], 'cgc_product_action')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $products = self::get_products();

        $product = array(
            'name' => sanitize_text_field($_POST['product_name']),
            'category' => sanitize_text_field($_POST['product_category']),
            'price' => floatval($_POST['product_price']),
            'description' => sanitize_textarea_field($_POST['product_description']),
        );

        // Edit existing or add new
        if (isset($_POST['product_id']) && $_POST['product_id'] !== '') {
            $product_id = intval($_POST['product_id']);
            $products[$product_id] = $product;
        } else {
            $products[] = $product;
        }

        update_option('cgc_product_library', $products);

        wp_redirect(admin_url('edit.php?post_type=offerte&page=cgc-product-library'));
        exit;
    }

    /**
     * Delete product
     */
    public static function delete_product() {
        // Check nonce
        if (!isset($_POST['cgc_product_nonce']) || !wp_verify_nonce($_POST['cgc_product_nonce'], 'cgc_product_action')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $products = self::get_products();
        $product_id = intval($_POST['product_id']);

        if (isset($products[$product_id])) {
            unset($products[$product_id]);
            // Re-index array
            $products = array_values($products);
            update_option('cgc_product_library', $products);
        }

        wp_redirect(admin_url('edit.php?post_type=offerte&page=cgc-product-library'));
        exit;
    }
}
