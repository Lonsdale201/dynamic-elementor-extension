<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;

class OrderID extends AbstractOrderTag
{
    public function get_name()
    {
        return 'wc-order-hpos-id';
    }

    public function get_title()
    {
        return esc_html__('Order ID', 'hw-ele-woo-dynamic');
    }

    public function get_categories()
    {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls()
    {
        $this->add_control(
            'linkable',
            [
                'label' => esc_html__('Linkable', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => esc_html__('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
    }

    public function render()
    {
        $order = $this->get_current_order();

        if (! $order) {
            echo '';
            return;
        }

        $order_id = $order->get_id();
        $settings = $this->get_settings_for_display();
        $is_linkable = isset($settings['linkable']) && 'yes' === $settings['linkable'];

        if ($is_linkable) {
            $url = $order->get_view_order_url();

            if (! $url) {
                $url = wc_get_endpoint_url('view-order', $order_id, wc_get_page_permalink('myaccount'));
            }

            if ($url) {
                echo sprintf('<a href="%s">%s</a>', esc_url($url), esc_html((string) $order_id));
                return;
            }
        }

        echo esc_html((string) $order_id);
    }
}
