<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// [bakery_menu] — Product listing
add_shortcode( 'bakery_menu', 'bakery_menu_shortcode' );
function bakery_menu_shortcode( $atts ) {
    $atts = shortcode_atts( [ 'category' => '' ], $atts );

    $args = [
        'post_type'      => 'bakery_product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ];

    if ( ! empty( $atts['category'] ) ) {
        $args['meta_query'] = [ [
            'key'   => '_bakery_category',
            'value' => sanitize_text_field( $atts['category'] ),
        ] ];
    }

    $products = new WP_Query( $args );
    if ( ! $products->have_posts() ) return '<p class="bakery-empty">No products found.</p>';

    ob_start();
    echo '<div class="bakery-menu">';

    // Category filter
    $categories = [ 'all', 'cakes', 'brownies', 'cookies', 'cupcakes' ];
    echo '<div class="bakery-filters">';
    foreach ( $categories as $cat ) {
        $active = $cat === 'all' ? ' active' : '';
        echo '<button class="bakery-filter-btn' . $active . '" data-category="' . esc_attr( $cat ) . '">' . ucfirst( $cat ) . '</button>';
    }
    echo '</div>';

    echo '<div class="bakery-products">';
    while ( $products->have_posts() ) {
        $products->the_post();
        $price = get_post_meta( get_the_ID(), '_bakery_price', true );
        $cat   = get_post_meta( get_the_ID(), '_bakery_category', true );
        $image = get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ?: BAKERY_ORDERS_URL . 'assets/images/placeholder.png';
        ?>
        <div class="bakery-product-card" data-category="<?php echo esc_attr( $cat ); ?>">
            <div class="bakery-product-image">
                <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
            </div>
            <div class="bakery-product-info">
                <span class="bakery-product-category"><?php echo esc_html( ucfirst( $cat ) ); ?></span>
                <h3 class="bakery-product-name"><?php the_title(); ?></h3>
                <p class="bakery-product-desc"><?php echo wp_trim_words( get_the_excerpt(), 15 ); ?></p>
                <div class="bakery-product-footer">
                    <span class="bakery-product-price">$<?php echo esc_html( number_format( floatval( $price ), 2 ) ); ?></span>
                    <button class="bakery-add-to-cart" data-product-id="<?php echo get_the_ID(); ?>">Add to Cart</button>
                </div>
            </div>
        </div>
        <?php
    }
    echo '</div></div>';
    wp_reset_postdata();
    return ob_get_clean();
}

// [bakery_cart] — Cart display
add_shortcode( 'bakery_cart', 'bakery_cart_shortcode' );
function bakery_cart_shortcode() {
    $cart = bakery_get_cart();
    ob_start();
    ?>
    <div class="bakery-cart" id="bakery-cart">
        <h2 class="bakery-section-title">🛒 Your Cart</h2>
        <div class="bakery-cart-items" id="bakery-cart-items">
            <?php if ( empty( $cart ) ) : ?>
                <p class="bakery-empty">Your cart is empty.</p>
            <?php else : ?>
                <?php foreach ( $cart as $id => $item ) : ?>
                    <div class="bakery-cart-item" data-product-id="<?php echo esc_attr( $id ); ?>">
                        <img src="<?php echo esc_url( $item['image'] ); ?>" alt="" class="bakery-cart-thumb">
                        <div class="bakery-cart-item-info">
                            <h4><?php echo esc_html( $item['name'] ); ?></h4>
                            <span class="bakery-cart-item-price">$<?php echo number_format( $item['price'], 2 ); ?></span>
                        </div>
                        <div class="bakery-cart-item-actions">
                            <button class="bakery-qty-btn" data-action="decrease" data-product-id="<?php echo esc_attr( $id ); ?>">−</button>
                            <span class="bakery-qty"><?php echo intval( $item['qty'] ); ?></span>
                            <button class="bakery-qty-btn" data-action="increase" data-product-id="<?php echo esc_attr( $id ); ?>">+</button>
                            <button class="bakery-remove-btn" data-product-id="<?php echo esc_attr( $id ); ?>">✕</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="bakery-cart-total">
            <strong>Total: $<span id="bakery-cart-total-amount"><?php echo number_format( bakery_get_cart_total(), 2 ); ?></span></strong>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// [bakery_checkout] — Checkout form
add_shortcode( 'bakery_checkout', 'bakery_checkout_shortcode' );
function bakery_checkout_shortcode() {
    ob_start();
    ?>
    <div class="bakery-checkout" id="bakery-checkout">
        <h2 class="bakery-section-title">📦 Checkout</h2>
        <form id="bakery-checkout-form" class="bakery-form">
            <div class="bakery-form-group">
                <label for="bakery-name">Full Name *</label>
                <input type="text" id="bakery-name" name="customer_name" required maxlength="255" placeholder="Your full name">
            </div>
            <div class="bakery-form-group">
                <label for="bakery-phone">Phone Number *</label>
                <input type="tel" id="bakery-phone" name="customer_phone" required maxlength="50" placeholder="Your phone number">
            </div>
            <div class="bakery-form-group">
                <label for="bakery-address">Delivery Address *</label>
                <textarea id="bakery-address" name="customer_address" required rows="3" placeholder="Full delivery address"></textarea>
            </div>
            <div class="bakery-form-group">
                <label for="bakery-notes">Order Notes</label>
                <textarea id="bakery-notes" name="order_notes" rows="2" placeholder="Any special instructions..."></textarea>
            </div>
            <button type="submit" class="bakery-checkout-btn">Place Order</button>
        </form>
        <div id="bakery-checkout-message" class="bakery-message" style="display:none;"></div>
    </div>
    <?php
    return ob_get_clean();
}
