<?php
/**
 * Plugin Name: Bakery Orders
 * Plugin URI: https://example.com/bakery-orders
 * Description: A lightweight bakery ordering system with product menu, cart, checkout, order management and Elementor shortcodes.
 * Version: 1.0.0
 * Author: Bakery Dev
 * License: GPL-2.0+
 * Text Domain: bakery-orders
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'BAKERY_ORDERS_VERSION', '1.0.0' );
define( 'BAKERY_ORDERS_PATH', plugin_dir_path( __FILE__ ) );
define( 'BAKERY_ORDERS_URL', plugin_dir_url( __FILE__ ) );

// Start session for cart
add_action( 'init', 'bakery_orders_start_session', 1 );
function bakery_orders_start_session() {
    if ( ! session_id() && ! headers_sent() ) {
        session_start();
    }
}

// Include files
require_once BAKERY_ORDERS_PATH . 'includes/post-types.php';
require_once BAKERY_ORDERS_PATH . 'includes/cart.php';
require_once BAKERY_ORDERS_PATH . 'includes/checkout.php';
require_once BAKERY_ORDERS_PATH . 'includes/shortcodes.php';
require_once BAKERY_ORDERS_PATH . 'includes/admin.php';
require_once BAKERY_ORDERS_PATH . 'includes/ajax.php';

// Enqueue assets
add_action( 'wp_enqueue_scripts', 'bakery_orders_enqueue' );
function bakery_orders_enqueue() {
    wp_enqueue_style( 'bakery-orders', BAKERY_ORDERS_URL . 'assets/css/bakery-orders.css', [], BAKERY_ORDERS_VERSION );
    wp_enqueue_script( 'bakery-orders', BAKERY_ORDERS_URL . 'assets/js/bakery-orders.js', [ 'jquery' ], BAKERY_ORDERS_VERSION, true );
    wp_localize_script( 'bakery-orders', 'bakeryAjax', [
        'url'   => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'bakery_nonce' ),
    ] );
}

// Activation: create orders table
register_activation_hook( __FILE__, 'bakery_orders_activate' );
function bakery_orders_activate() {
    bakery_orders_register_post_types();
    flush_rewrite_rules();

    global $wpdb;
    $table = $wpdb->prefix . 'bakery_orders';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        customer_name VARCHAR(255) NOT NULL,
        customer_phone VARCHAR(50) NOT NULL,
        customer_address TEXT NOT NULL,
        order_notes TEXT,
        order_items LONGTEXT NOT NULL,
        order_total DECIMAL(10,2) NOT NULL,
        order_status VARCHAR(30) DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// Deactivation
register_deactivation_hook( __FILE__, 'bakery_orders_deactivate' );
function bakery_orders_deactivate() {
    flush_rewrite_rules();
}
