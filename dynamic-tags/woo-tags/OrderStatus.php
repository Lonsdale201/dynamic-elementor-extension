<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Modules\DynamicTags\Module;

class OrderStatus extends AbstractOrderTag
{
    public function get_name()
    {
        return 'wc-order-hpos-status';
    }

    public function get_title()
    {
        return esc_html__('Order Status', 'hw-ele-woo-dynamic');
    }

    public function get_categories()
    {
        return [Module::TEXT_CATEGORY];
    }

    public function render()
    {
        $order = $this->get_current_order();

        if (! $order) {
            echo '';
            return;
        }

        $status_key = $order->get_status();
        $label = wc_get_order_status_name($status_key);

        echo esc_html($label);
    }
}
