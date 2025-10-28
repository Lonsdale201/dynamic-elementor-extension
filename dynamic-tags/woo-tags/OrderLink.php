<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Modules\DynamicTags\Module;

class OrderLink extends AbstractOrderTag
{
    public function get_name()
    {
        return 'wc-order-hpos-link';
    }

    public function get_title()
    {
        return esc_html__('Order Link', 'hw-ele-woo-dynamic');
    }

    public function get_categories()
    {
        return [Module::URL_CATEGORY];
    }

    public function render()
    {
        $order = $this->get_current_order();

        if (! $order) {
            echo '';
            return;
        }

        $url = $order->get_view_order_url();

        if (! $url) {
            $url = wc_get_endpoint_url('view-order', $order->get_id(), wc_get_page_permalink('myaccount'));
        }

        echo esc_url($url ?: '');
    }
}
