<?php
add_action('woocommerce_checkout_process', 'wpb_check_product_restrictions');

function wpb_check_product_restrictions() {
    $blocked_products = get_option('blocked_products_regions_zip_codes', array());

    $shipping_state = WC()->customer->get_shipping_state();
    $shipping_city = WC()->customer->get_shipping_city();
    $shipping_zip = WC()->customer->get_shipping_postcode();

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $product_id = $cart_item['product_id'];
        $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));

        foreach ($blocked_products as $block) {
            $blocked_product_ids = array_map('trim', explode(',', $block['product']));
            $blocked_category_ids = array_map('trim', explode(',', $block['category']));
            $blocked_regions = array_map('trim', explode(',', $block['region']));
            $blocked_zips = array_map('trim', explode(',', $block['zip']));

            if (in_array($product_id, $blocked_product_ids) || array_intersect($product_categories, $blocked_category_ids)) {
                if (in_array($shipping_zip, $blocked_zips)) {
                    wc_add_notice(sprintf('Sorry, the product "%s" is banned in your ZIP code area %s. Please remove the banned item to proceed.', get_the_title($product_id), $shipping_zip), 'error');
                    return;
                }
                foreach ($blocked_regions as $region) {
                    if (strpos($region, ':') !== false) {
                        list($state, $city) = explode(':', $region);
                        if ($state === $shipping_state && $city === $shipping_city) {
                            wc_add_notice(sprintf('Sorry, the product "%s" is banned in your %s, %s. Please remove the banned item to proceed.', get_the_title($product_id), $city, $state), 'error');
                            return;
                        }
                    } else if ($region === $shipping_state) {
                        wc_add_notice(sprintf('Sorry, the product "%s" is banned in your state %s. Please remove the banned item to proceed.', get_the_title($product_id), $shipping_state), 'error');
                        return;
                    }
                }
            }
        }
    }
}

add_action('woocommerce_checkout_process', 'wpb_check_product_restrictions');
