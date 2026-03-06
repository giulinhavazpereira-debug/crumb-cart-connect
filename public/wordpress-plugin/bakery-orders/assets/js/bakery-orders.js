(function($) {
    'use strict';

    // Category filter
    $(document).on('click', '.bakery-filter-btn', function() {
        var cat = $(this).data('category');
        $('.bakery-filter-btn').removeClass('active');
        $(this).addClass('active');
        if (cat === 'all') {
            $('.bakery-product-card').show();
        } else {
            $('.bakery-product-card').hide();
            $('.bakery-product-card[data-category="' + cat + '"]').show();
        }
    });

    // Add to cart
    $(document).on('click', '.bakery-add-to-cart', function() {
        var btn = $(this);
        var productId = btn.data('product-id');
        btn.prop('disabled', true).text('Adding...');
        $.post(bakeryAjax.url, {
            action: 'bakery_add_to_cart',
            nonce: bakeryAjax.nonce,
            product_id: productId
        }, function(res) {
            if (res.success) {
                btn.text('✓ Added!');
                updateCartDisplay(res.data);
                setTimeout(function() { btn.prop('disabled', false).text('Add to Cart'); }, 1500);
            } else {
                btn.prop('disabled', false).text('Add to Cart');
                alert('Error adding to cart.');
            }
        });
    });

    // Quantity buttons
    $(document).on('click', '.bakery-qty-btn', function() {
        var productId = $(this).data('product-id');
        var action = $(this).data('action');
        var qtyEl = $(this).siblings('.bakery-qty');
        var currentQty = parseInt(qtyEl.text());
        var newQty = action === 'increase' ? currentQty + 1 : currentQty - 1;
        if (newQty < 1) newQty = 0;

        $.post(bakeryAjax.url, {
            action: 'bakery_update_qty',
            nonce: bakeryAjax.nonce,
            product_id: productId,
            qty: newQty
        }, function(res) {
            if (res.success) {
                if (newQty === 0) {
                    $('.bakery-cart-item[data-product-id="' + productId + '"]').fadeOut(300, function() { $(this).remove(); });
                } else {
                    qtyEl.text(newQty);
                }
                $('#bakery-cart-total-amount').text(parseFloat(res.data.total).toFixed(2));
            }
        });
    });

    // Remove from cart
    $(document).on('click', '.bakery-remove-btn', function() {
        var productId = $(this).data('product-id');
        $.post(bakeryAjax.url, {
            action: 'bakery_remove_from_cart',
            nonce: bakeryAjax.nonce,
            product_id: productId
        }, function(res) {
            if (res.success) {
                $('.bakery-cart-item[data-product-id="' + productId + '"]').fadeOut(300, function() { $(this).remove(); });
                $('#bakery-cart-total-amount').text(parseFloat(res.data.total).toFixed(2));
                if (res.data.count === 0) {
                    $('#bakery-cart-items').html('<p class="bakery-empty">Your cart is empty.</p>');
                }
            }
        });
    });

    // Checkout
    $(document).on('submit', '#bakery-checkout-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('.bakery-checkout-btn');
        btn.prop('disabled', true).text('Processing...');

        var data = {
            action: 'bakery_checkout',
            nonce: bakeryAjax.nonce,
            customer_name: form.find('[name="customer_name"]').val(),
            customer_phone: form.find('[name="customer_phone"]').val(),
            customer_address: form.find('[name="customer_address"]').val(),
            order_notes: form.find('[name="order_notes"]').val()
        };

        $.post(bakeryAjax.url, data, function(res) {
            var msgEl = $('#bakery-checkout-message');
            if (res.success) {
                msgEl.removeClass('error').addClass('success').html(
                    '<h3>🎉 Order Placed!</h3><p>Order #' + res.data.order_id + ' has been received. We\'ll start preparing it soon!</p>'
                ).show();
                form.hide();
                $('#bakery-cart-items').html('<p class="bakery-empty">Your cart is empty.</p>');
                $('#bakery-cart-total-amount').text('0.00');
            } else {
                msgEl.removeClass('success').addClass('error').text(res.data).show();
                btn.prop('disabled', false).text('Place Order');
            }
        });
    });

    function updateCartDisplay(data) {
        // Lightweight update — just refresh total
        if ($('#bakery-cart-total-amount').length) {
            $('#bakery-cart-total-amount').text(parseFloat(data.total).toFixed(2));
        }
    }

})(jQuery);
