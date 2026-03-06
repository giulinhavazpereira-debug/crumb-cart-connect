<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Add to cart
add_action( 'wp_ajax_bakery_add_to_cart', 'bakery_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_bakery_add_to_cart', 'bakery_ajax_add_to_cart' );
function bakery_ajax_add_to_cart() {
    check_ajax_referer( 'bakery_nonce', 'nonce' );
    $product_id = intval( $_POST['product_id'] ?? 0 );
    if ( $product_id && bakery_add_to_cart( $product_id ) ) {
        wp_send_json_success( [ 'cart' => bakery_get_cart(), 'total' => bakery_get_cart_total(), 'count' => array_sum( array_column( bakery_get_cart(), 'qty' ) ) ] );
    }
    wp_send_json_error( 'Invalid product.' );
}

// Remove from cart
add_action( 'wp_ajax_bakery_remove_from_cart', 'bakery_ajax_remove_from_cart' );
add_action( 'wp_ajax_nopriv_bakery_remove_from_cart', 'bakery_ajax_remove_from_cart' );
function bakery_ajax_remove_from_cart() {
    check_ajax_referer( 'bakery_nonce', 'nonce' );
    $product_id = intval( $_POST['product_id'] ?? 0 );
    bakery_remove_from_cart( $product_id );
    wp_send_json_success( [ 'cart' => bakery_get_cart(), 'total' => bakery_get_cart_total(), 'count' => array_sum( array_column( bakery_get_cart(), 'qty' ) ) ] );
}

// Update qty
add_action( 'wp_ajax_bakery_update_qty', 'bakery_ajax_update_qty' );
add_action( 'wp_ajax_nopriv_bakery_update_qty', 'bakery_ajax_update_qty' );
function bakery_ajax_update_qty() {
    check_ajax_referer( 'bakery_nonce', 'nonce' );
    $product_id = intval( $_POST['product_id'] ?? 0 );
    $qty = intval( $_POST['qty'] ?? 1 );
    bakery_update_cart_qty( $product_id, $qty );
    wp_send_json_success( [ 'cart' => bakery_get_cart(), 'total' => bakery_get_cart_total(), 'count' => array_sum( array_column( bakery_get_cart(), 'qty' ) ) ] );
}

// Checkout
add_action( 'wp_ajax_bakery_checkout', 'bakery_ajax_checkout' );
add_action( 'wp_ajax_nopriv_bakery_checkout', 'bakery_ajax_checkout' );
function bakery_ajax_checkout() {
    check_ajax_referer( 'bakery_nonce', 'nonce' );
    $result = bakery_process_checkout( $_POST );
    if ( is_wp_error( $result ) ) {
        wp_send_json_error( $result->get_error_message() );
    }
    wp_send_json_success( [ 'order_id' => $result, 'message' => 'Order placed successfully!' ] );
}

// Admin: update order status
add_action( 'wp_ajax_bakery_update_order_status', 'bakery_ajax_update_order_status' );
function bakery_ajax_update_order_status() {
    check_ajax_referer( 'bakery_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized' );
    }
    global $wpdb;
    $table = $wpdb->prefix . 'bakery_orders';
    $order_id = intval( $_POST['order_id'] ?? 0 );
    $status = sanitize_text_field( $_POST['status'] ?? '' );
    $valid = [ 'pending', 'preparing', 'delivered' ];
    if ( ! in_array( $status, $valid ) ) {
        wp_send_json_error( 'Invalid status.' );
    }
    $wpdb->update( $table, [ 'order_status' => $status ], [ 'id' => $order_id ], [ '%s' ], [ '%d' ] );
    wp_send_json_success();
}
