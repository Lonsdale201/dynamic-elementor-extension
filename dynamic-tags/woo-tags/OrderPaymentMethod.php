<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Modules\DynamicTags\Module;

class OrderPaymentMethod extends AbstractOrderTag
{
    public function get_name()
    {
        return 'wc-order-hpos-payment-method';
    }

    public function get_title()
    {
        return esc_html__('Order Payment Method', 'hw-ele-woo-dynamic');
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

        $method = $order->get_payment_method_title();

        if (! $method) {
            $method = $order->get_payment_method();
        }

        echo esc_html($method ?: '');
    }
}
