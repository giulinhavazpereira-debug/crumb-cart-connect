<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', 'bakery_orders_admin_menu' );
function bakery_orders_admin_menu() {
    add_menu_page(
        'Bakery Orders',
        'Bakery Orders',
        'manage_options',
        'bakery-orders',
        'bakery_orders_admin_page',
        'dashicons-clipboard',
        26
    );
}

add_action( 'admin_enqueue_scripts', 'bakery_orders_admin_assets' );
function bakery_orders_admin_assets( $hook ) {
    if ( $hook !== 'toplevel_page_bakery-orders' ) return;
    wp_enqueue_style( 'bakery-admin', BAKERY_ORDERS_URL . 'assets/css/bakery-admin.css', [], BAKERY_ORDERS_VERSION );
    wp_enqueue_script( 'bakery-admin', BAKERY_ORDERS_URL . 'assets/js/bakery-admin.js', [ 'jquery' ], BAKERY_ORDERS_VERSION, true );
    wp_localize_script( 'bakery-admin', 'bakeryAjax', [
        'url'   => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'bakery_nonce' ),
    ] );
}

function bakery_orders_admin_page() {
    global $wpdb;
    $table  = $wpdb->prefix . 'bakery_orders';
    $orders = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC", ARRAY_A );
    ?>
    <div class="wrap bakery-admin-wrap">
        <h1>🧁 Bakery Orders</h1>
        <div class="bakery-admin-stats">
            <?php
            $pending   = count( array_filter( $orders, fn($o) => $o['order_status'] === 'pending' ) );
            $preparing = count( array_filter( $orders, fn($o) => $o['order_status'] === 'preparing' ) );
            $delivered = count( array_filter( $orders, fn($o) => $o['order_status'] === 'delivered' ) );
            ?>
            <div class="bakery-stat pending"><strong><?php echo $pending; ?></strong><span>Pending</span></div>
            <div class="bakery-stat preparing"><strong><?php echo $preparing; ?></strong><span>Preparing</span></div>
            <div class="bakery-stat delivered"><strong><?php echo $delivered; ?></strong><span>Delivered</span></div>
            <div class="bakery-stat total"><strong><?php echo count( $orders ); ?></strong><span>Total</span></div>
        </div>

        <?php if ( empty( $orders ) ) : ?>
            <p>No orders yet.</p>
        <?php else : ?>
            <table class="wp-list-table widefat fixed striped bakery-orders-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $orders as $order ) :
                        $items = json_decode( $order['order_items'], true );
                    ?>
                        <tr>
                            <td>#<?php echo intval( $order['id'] ); ?></td>
                            <td>
                                <strong><?php echo esc_html( $order['customer_name'] ); ?></strong>
                                <?php if ( $order['order_notes'] ) : ?>
                                    <br><em class="bakery-note"><?php echo esc_html( $order['order_notes'] ); ?></em>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html( $order['customer_phone'] ); ?></td>
                            <td><?php echo esc_html( $order['customer_address'] ); ?></td>
                            <td>
                                <?php if ( is_array( $items ) ) : ?>
                                    <ul class="bakery-order-items-list">
                                    <?php foreach ( $items as $item ) : ?>
                                        <li><?php echo esc_html( $item['name'] ); ?> × <?php echo intval( $item['qty'] ); ?></li>
                                    <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </td>
                            <td><strong>$<?php echo number_format( floatval( $order['order_total'] ), 2 ); ?></strong></td>
                            <td>
                                <select class="bakery-status-select" data-order-id="<?php echo intval( $order['id'] ); ?>">
                                    <option value="pending" <?php selected( $order['order_status'], 'pending' ); ?>>⏳ Pending</option>
                                    <option value="preparing" <?php selected( $order['order_status'], 'preparing' ); ?>>👩‍🍳 Preparing</option>
                                    <option value="delivered" <?php selected( $order['order_status'], 'delivered' ); ?>>✅ Delivered</option>
                                </select>
                            </td>
                            <td><?php echo esc_html( date( 'M j, Y g:i A', strtotime( $order['created_at'] ) ) ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}
