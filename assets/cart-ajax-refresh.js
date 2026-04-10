(function ($) {
    'use strict';

    var refreshTimer = null;

    function refreshCartDynamicTags() {
        if (refreshTimer) {
            clearTimeout(refreshTimer);
        }

        refreshTimer = setTimeout(function () {
            refreshTimer = null;

            var $elements = $('.hw-cart-dynamic');
            if (!$elements.length) {
                return;
            }

            var items = [];
            $elements.each(function () {
                items.push({
                    tag: $(this).data('tag'),
                    settings: $(this).data('settings')
                });
            });

            $.post(hw_cart_refresh.ajax_url, {
                action: 'hw_refresh_cart_values',
                nonce: hw_cart_refresh.nonce,
                items: items
            }, function (response) {
                if (!response.success || !response.data) {
                    return;
                }

                $elements.each(function (index) {
                    if (typeof response.data[index] !== 'undefined') {
                        $(this).html(response.data[index]);
                    }
                });
            });
        }, 300);
    }

    $(document.body).on(
        'added_to_cart removed_from_cart updated_cart_totals wc_fragments_refreshed',
        refreshCartDynamicTags
    );

})(jQuery);
