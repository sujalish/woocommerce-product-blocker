<?php
// Admin settings and registration of options
function wpb_register_settings() {
    register_setting('wpb-settings-group', 'blocked_products_regions_zip_codes', array(
        'type' => 'array',
        'sanitize_callback' => 'wpb_sanitize_blocked_products'
    ));
}

function wpb_sanitize_blocked_products($input) {
    $sanitized_input = array();
    foreach ($input as $key => $value) {
        $sanitized_input[$key]['product'] = sanitize_text_field($value['product']);
        $sanitized_input[$key]['category'] = sanitize_text_field($value['category']);
        $sanitized_input[$key]['region'] = sanitize_text_field($value['region']);
        $sanitized_input[$key]['zip'] = sanitize_text_field($value['zip']);
    }
    return $sanitized_input;
}

add_action('admin_init', 'wpb_register_settings');
