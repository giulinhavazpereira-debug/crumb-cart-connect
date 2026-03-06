<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function bakery_process_checkout( $data ) {
    global $wpdb;
    $table = $wpdb->prefix . 'bakery_orders';
    $cart  = bakery_get_cart();

    if ( empty( $cart ) ) {
        return new WP_Error( 'empty_cart', 'Your cart is empty.' );
    }

    $name    = sanitize_text_field( $data['customer_name'] ?? '' );
    $phone   = sanitize_text_field( $data['customer_phone'] ?? '' );
    $address = sanitize_textarea_field( $data['customer_address'] ?? '' );
    $notes   = sanitize_textarea_field( $data['order_notes'] ?? '' );

    if ( empty( $name ) || empty( $phone ) || empty( $address ) ) {
        return new WP_Error( 'missing_fields', 'Please fill in all required fields.' );
    }

    if ( strlen( $name ) > 255 || strlen( $phone ) > 50 ) {
        return new WP_Error( 'invalid_input', 'Input too long.' );
    }

    $total = bakery_get_cart_total();

    $result = $wpdb->insert( $table, [
        'customer_name'    => $name,
        'customer_phone'   => $phone,
        'customer_address' => $address,
        'order_notes'      => $notes,
        'order_items'      => wp_json_encode( $cart ),
        'order_total'      => $total,
        'order_status'     => 'pending',
    ], [ '%s', '%s', '%s', '%s', '%s', '%f', '%s' ] );

    if ( $result === false ) {
        return new WP_Error( 'db_error', 'Could not save order.' );
    }

    bakery_clear_cart();
    return $wpdb->insert_id;
}
