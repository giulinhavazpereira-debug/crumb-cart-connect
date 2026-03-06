# Bakery Orders - WordPress Plugin

## Installation

1. Download or copy the `bakery-orders` folder
2. Upload it to `/wp-content/plugins/` on your WordPress site
3. Go to **WordPress Admin → Plugins** and activate **Bakery Orders**
4. The plugin will automatically create the orders database table

## Usage

### Adding Products
1. Go to **WordPress Admin → Bakery Products → Add New**
2. Enter the product name, description, set a featured image
3. Set the **Price** and **Category** (cakes, brownies, cookies, cupcakes) in the sidebar
4. Publish the product

### Shortcodes (Elementor Compatible)
Use these shortcodes inside any Elementor **Shortcode** widget:

| Shortcode | Description |
|-----------|-------------|
| `[bakery_menu]` | Displays the full product menu with category filters |
| `[bakery_menu category="cakes"]` | Displays only cakes |
| `[bakery_cart]` | Displays the shopping cart |
| `[bakery_checkout]` | Displays the checkout form |

### Suggested Page Structure
- **Menu page**: Add `[bakery_menu]` shortcode
- **Cart/Checkout page**: Add `[bakery_cart]` then `[bakery_checkout]` shortcodes

### Managing Orders
1. Go to **WordPress Admin → Bakery Orders**
2. View all orders with customer info, items, and totals
3. Change order status: Pending → Preparing → Delivered

## File Structure

```
bakery-orders/
├── bakery-orders.php          # Main plugin file
├── includes/
│   ├── post-types.php         # Custom post type for products
│   ├── cart.php               # Cart session logic
│   ├── checkout.php           # Checkout processing
│   ├── shortcodes.php         # All shortcodes
│   ├── admin.php              # Admin orders dashboard
│   └── ajax.php               # AJAX handlers
└── assets/
    ├── css/
    │   ├── bakery-orders.css  # Frontend styles
    │   └── bakery-admin.css   # Admin styles
    └── js/
        ├── bakery-orders.js   # Frontend JavaScript
        └── bakery-admin.js    # Admin JavaScript
```

## WooCommerce Compatibility
This plugin works independently but doesn't conflict with WooCommerce. Both can run side by side.

## Requirements
- WordPress 5.0+
- PHP 7.4+
- jQuery (included with WordPress)
