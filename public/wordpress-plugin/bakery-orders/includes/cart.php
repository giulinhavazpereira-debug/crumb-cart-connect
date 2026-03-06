<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function bakery_get_cart() {
    return isset( $_SESSION['bakery_cart'] ) ? $_SESSION['bakery_cart'] : [];
}

function bakery_add_to_cart( $product_id, $qty = 1 ) {
    $cart = bakery_get_cart();
    if ( isset( $cart[ $product_id ] ) ) {
        $cart[ $product_id ]['qty'] += $qty;
    } else {
        $product = get_post( $product_id );
        if ( ! $product || $product->post_type !== 'bakery_product' ) return false;
        $price = floatval( get_post_meta( $product_id, '_bakery_price', true ) );
        $image = get_the_post_thumbnail_url( $product_id, 'thumbnail' ) ?: '';
        $cart[ $product_id ] = [
            'name'  => $product->post_title,
            'price' => $price,
            'image' => $image,
            'qty'   => $qty,
        ];
    }
    $_SESSION['bakery_cart'] = $cart;
    return true;
}

function bakery_remove_from_cart( $product_id ) {
    $cart = bakery_get_cart();
    unset( $cart[ $product_id ] );
    $_SESSION['bakery_cart'] = $cart;
}

function bakery_update_cart_qty( $product_id, $qty ) {
    $cart = bakery_get_cart();
    if ( isset( $cart[ $product_id ] ) ) {
        if ( $qty <= 0 ) {
            unset( $cart[ $product_id ] );
        } else {
            $cart[ $product_id ]['qty'] = intval( $qty );
        }
    }
    $_SESSION['bakery_cart'] = $cart;
}

function bakery_get_cart_total() {
    $cart = bakery_get_cart();
    $total = 0;
    foreach ( $cart as $item ) {
        $total += $item['price'] * $item['qty'];
    }
    return $total;
}

function bakery_clear_cart() {
    $_SESSION['bakery_cart'] = [];
}
