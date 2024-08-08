<?php
/*
Plugin Name: WooCommerce Product Blocker
Plugin URI: https://serpkid.com
Description: Blocks certain products and categories from being shipped to specified regions.
Version: 2.1
Author: SERPKIDxIDESIGN
Author URI: https://serpkid.com
*/

// Hook for adding admin menus
add_action('admin_menu', 'wpb_adding_admin_menu');

// Include necessary files
require_once('includes/admin-settings.php');
require_once('includes/frontend-handler.php');

// Function to add admin menu and setup CSS loading
function wpb_adding_admin_menu() {
    $page_hook_suffix = add_menu_page('Product Blocker', 'Product Blocker', 'manage_options', 'product-blocker-settings', 'wpb_admin_page', 'dashicons-block-default');
    
    // Use the hook suffix to add the enqueue function specifically for this page
    add_action('admin_print_styles-' . $page_hook_suffix, 'wpb_enqueue_admin_styles');
}

// Function to enqueue admin styles
function wpb_enqueue_admin_styles() {
    wp_enqueue_style('wpb_admin_style', plugin_dir_url(__FILE__) . 'css/admin-style.css');
}

// Admin page functionality
function wpb_admin_page(){
    $blocked_products = get_option('blocked_products_regions_zip_codes', array());
    ?>
    <div class="wrap wpb-admin-page">
        <h2>Product Blocker Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('wpb-settings-group');
            do_settings_sections('wpb-settings-group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Blocked Products, Categories, Regions, and ZIP Codes</th>
                    <td>
                        <div id="wpb-blocked-products-container">
                            <?php if (!empty($blocked_products)): ?>
                                <?php foreach ($blocked_products as $index => $block): ?>
                                    <div class="wpb-blocked-product">
                                        <input type="text" name="blocked_products_regions_zip_codes[<?php echo $index; ?>][product]" value="<?php echo esc_attr($block['product']); ?>" placeholder="Product IDs (comma separated)" />
                                        <input type="text" name="blocked_products_regions_zip_codes[<?php echo $index; ?>][category]" value="<?php echo esc_attr($block['category']); ?>" placeholder="Category IDs (comma separated)" />
                                        <input type="text" name="blocked_products_regions_zip_codes[<?php echo $index; ?>][region]" value="<?php echo esc_attr($block['region']); ?>" placeholder="Regions (comma separated)" />
                                        <input type="text" name="blocked_products_regions_zip_codes[<?php echo $index; ?>][zip]" value="<?php echo esc_attr($block['zip']); ?>" placeholder="ZIP Codes (comma separated)" />
                                        <button type="button" class="wpb-remove-blocked-product button">Remove</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="wpb-add-blocked-product" class="button">Add Product/Category Restriction</button>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('wpb-blocked-products-container');
            const addButton = document.getElementById('wpb-add-blocked-product');

            addButton.addEventListener('click', function() {
                const index = container.children.length;
                const newBlock = document.createElement('div');
                newBlock.classList.add('wpb-blocked-product');
                newBlock.innerHTML = `
                    <input type="text" name="blocked_products_regions_zip_codes[${index}][product]" placeholder="Product IDs (comma separated)" />
                    <input type="text" name="blocked_products_regions_zip_codes[${index}][category]" placeholder="Category IDs (comma separated)" />
                    <input type="text" name="blocked_products_regions_zip_codes[${index}][region]" placeholder="Regions (comma separated)" />
                    <input type="text" name="blocked_products_regions_zip_codes[${index}][zip]" placeholder="ZIP Codes (comma separated)" />
                    <button type="button" class="wpb-remove-blocked-product button">Remove</button>
                `;
                container.appendChild(newBlock);
            });

            container.addEventListener('click', function(event) {
                if (event.target.classList.contains('wpb-remove-blocked-product')) {
                    event.target.parentElement.remove();
                }
            });
        });
    </script>
    <?php
}
