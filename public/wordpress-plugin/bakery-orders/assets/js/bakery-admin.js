(function($) {
    'use strict';

    $(document).on('change', '.bakery-status-select', function() {
        var select = $(this);
        var orderId = select.data('order-id');
        var status = select.val();

        $.post(bakeryAjax.url, {
            action: 'bakery_update_order_status',
            nonce: bakeryAjax.nonce,
            order_id: orderId,
            status: status
        }, function(res) {
            if (res.success) {
                select.css('outline', '2px solid #4caf50');
                setTimeout(function() { select.css('outline', 'none'); }, 1500);
            } else {
                alert('Error updating status.');
            }
        });
    });

})(jQuery);
