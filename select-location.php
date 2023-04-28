<?php
/*
Plugin Name: Location and Time Selector for WooCommerce
Plugin URI: https://example.com/location-and-time-selector
Description: This plugin adds location and time selection options to the WooCommerce checkout process.
Version: 1.0
Author: Aaron Besson
Author URI: https://bespokett.com
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add the location and time selection fields to the checkout form
add_action('woocommerce_after_shipping_rate', 'location_and_time_selector_add_checkout_fields');

// Validate the location and time selection fields
add_action('woocommerce_checkout_process', 'location_and_time_selector_validate_checkout_fields');

// Save the location and time selection fields to the order meta
add_action('woocommerce_checkout_update_order_meta', 'location_and_time_selector_save_checkout_fields');

// Display the location and time selection fields in the order details (both admin and customer)
add_action('woocommerce_order_details_after_order_table', 'location_and_time_selector_display_order_meta');
add_action('woocommerce_admin_order_data_after_billing_address', 'location_and_time_selector_display_order_meta_admin');

function location_and_time_selector_add_checkout_fields() {
    global $woocommerce;

    // Add location field
    woocommerce_form_field('location_selector', array(
        'type' => 'select',
        'class' => array('form-row-wide'),
        'label' => __('Select a location'),
        'options' => array(
            '' => 'Choose a location...',
            'location_one' => 'San Juan',
            'location_two' => 'City Gate',
        ),
        'required' => true,
    ), $woocommerce->checkout->get_value('location_selector'));

    // Add time field
    woocommerce_form_field('time_selector', array(
        'type' => 'time',
        'class' => array('form-row-wide'),
        'label' => __('Select a time'),
        'required' => true,
    ), $woocommerce->checkout->get_value('time_selector'));
}


function location_and_time_selector_validate_checkout_fields() {
    if (empty($_POST['location_selector'])) {
        wc_add_notice(__('Please select a location.'), 'error');
    }

    if (empty($_POST['time_selector'])) {
        wc_add_notice(__('Please select a time.'), 'error');
    }
}

function location_and_time_selector_save_checkout_fields($order_id) {
    if (!empty($_POST['location_selector'])) {
        update_post_meta($order_id, '_location_selector', sanitize_text_field($_POST['location_selector']));
    }

    if (!empty($_POST['time_selector'])) {
        update_post_meta($order_id, '_time_selector', sanitize_text_field($_POST['time_selector']));
    }
}

function location_and_time_selector_display_order_meta($order) {
    echo '<h2>Location and Time Selection</h2>';
    echo '<p><strong>Location:</strong> ' . get_post_meta($order->get_id(), '_location_selector', true) . '</p>';
    echo '<p><strong>Time:</strong> ' . get_post_meta($order->get_id(), '_time_selector', true) . '</p>';
}

function location_and_time_selector_display_order_meta_admin($order) {
    echo '<h3>Location and Time Selection</h3>';
    echo '<p><strong>Location:</strong> ' . get_post_meta($order->get_id(), '_location_selector', true) . '</p>';
    echo '<p><strong>Time:</strong> ' . get_post_meta($order->get_id(), '_time_selector', true) . '</p>';
}
