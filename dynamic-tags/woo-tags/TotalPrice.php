<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Modules\DynamicTags\Module;

class TotalPrice extends AbstractOrderTag
{
    public function get_name()
    {
        return 'wc-order-hpos-total-price';
    }

    public function get_title()
    {
        return esc_html__('Total Price', 'hw-ele-woo-dynamic');
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

        echo wp_kses_post($order->get_formatted_order_total());
    }
}
