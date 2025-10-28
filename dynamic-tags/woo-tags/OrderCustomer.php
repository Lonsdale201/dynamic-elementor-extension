<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;

class OrderCustomer extends AbstractOrderTag
{
    public function get_name()
    {
        return 'wc-order-hpos-customer';
    }

    public function get_title()
    {
        return esc_html__('Order Customer', 'hw-ele-woo-dynamic');
    }

    public function get_categories()
    {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls()
    {
        $this->add_control(
            'customer_field',
            [
                'label' => esc_html__('Customer Detail', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'default' => 'name',
                'options' => [
                    'name'  => esc_html__('Name', 'hw-ele-woo-dynamic'),
                    'email' => esc_html__('Email', 'hw-ele-woo-dynamic'),
                ],
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

        $settings = $this->get_settings_for_display();
        $field = $settings['customer_field'] ?? 'name';

        if ('email' === $field) {
            $email = $order->get_billing_email();

            if (! $email && $order->get_user_id()) {
                $user = get_user_by('id', $order->get_user_id());
                if ($user) {
                    $email = $user->user_email;
                }
            }

            echo esc_html($email ?: '');
            return;
        }

        $name = $order->get_formatted_billing_full_name();

        if (! $name) {
            $name = trim($order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name());
        }

        if (! $name && $order->get_user_id()) {
            $user = get_user_by('id', $order->get_user_id());
            if ($user) {
                $name = $user->display_name ?: $user->user_login;
            }
        }

        echo esc_html($name ?: '');
    }
}
