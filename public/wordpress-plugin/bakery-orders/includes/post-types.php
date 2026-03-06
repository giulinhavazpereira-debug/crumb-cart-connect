<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', 'bakery_orders_register_post_types' );
function bakery_orders_register_post_types() {
    register_post_type( 'bakery_product', [
        'labels' => [
            'name'               => 'Bakery Products',
            'singular_name'      => 'Product',
            'add_new_item'       => 'Add New Product',
            'edit_item'          => 'Edit Product',
            'all_items'          => 'All Products',
            'menu_name'          => 'Bakery Products',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-store',
        'supports'     => [ 'title', 'editor', 'thumbnail' ],
        'has_archive'  => false,
    ] );
}

// Add price meta box
add_action( 'add_meta_boxes', 'bakery_product_meta_boxes' );
function bakery_product_meta_boxes() {
    add_meta_box( 'bakery_price', 'Product Price', 'bakery_price_meta_box_cb', 'bakery_product', 'side' );
    add_meta_box( 'bakery_category', 'Product Category', 'bakery_category_meta_box_cb', 'bakery_product', 'side' );
}

function bakery_price_meta_box_cb( $post ) {
    $price = get_post_meta( $post->ID, '_bakery_price', true );
    wp_nonce_field( 'bakery_save_price', 'bakery_price_nonce' );
    echo '<label>Price ($):</label><br>';
    echo '<input type="number" step="0.01" name="bakery_price" value="' . esc_attr( $price ) . '" style="width:100%">';
}

function bakery_category_meta_box_cb( $post ) {
    $cat = get_post_meta( $post->ID, '_bakery_category', true );
    wp_nonce_field( 'bakery_save_cat', 'bakery_cat_nonce' );
    $categories = [ 'cakes', 'brownies', 'cookies', 'cupcakes' ];
    echo '<select name="bakery_category" style="width:100%">';
    foreach ( $categories as $c ) {
        echo '<option value="' . $c . '"' . selected( $cat, $c, false ) . '>' . ucfirst( $c ) . '</option>';
    }
    echo '</select>';
}

add_action( 'save_post_bakery_product', 'bakery_save_product_meta' );
function bakery_save_product_meta( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    if ( isset( $_POST['bakery_price_nonce'] ) && wp_verify_nonce( $_POST['bakery_price_nonce'], 'bakery_save_price' ) ) {
        if ( isset( $_POST['bakery_price'] ) ) {
            update_post_meta( $post_id, '_bakery_price', sanitize_text_field( $_POST['bakery_price'] ) );
        }
    }

    if ( isset( $_POST['bakery_cat_nonce'] ) && wp_verify_nonce( $_POST['bakery_cat_nonce'], 'bakery_save_cat' ) ) {
        if ( isset( $_POST['bakery_category'] ) ) {
            update_post_meta( $post_id, '_bakery_category', sanitize_text_field( $_POST['bakery_category'] ) );
        }
    }
}
