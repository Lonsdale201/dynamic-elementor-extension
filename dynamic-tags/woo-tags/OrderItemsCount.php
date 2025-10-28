<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Modules\DynamicTags\Module;

class OrderItemsCount extends AbstractOrderTag
{
    public function get_name()
    {
        return 'wc-order-hpos-items-count';
    }

    public function get_title()
    {
        return esc_html__('Order Items Count', 'hw-ele-woo-dynamic');
    }

    public function get_categories()
    {
        return [Module::TEXT_CATEGORY, Module::NUMBER_CATEGORY];
    }

    public function render()
    {
        $order = $this->get_current_order();

        if (! $order) {
            echo '';
            return;
        }

        $items = $order->get_items('line_item');
        $count = 0;

        if (! empty($items)) {
            foreach ($items as $item) {
                $count += (int) $item->get_quantity();
            }
        }

        echo esc_html((string) $count);
    }
}
