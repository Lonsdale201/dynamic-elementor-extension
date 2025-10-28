<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;
use WC_DateTime;

class OrderDate extends AbstractOrderTag
{
    public function get_name()
    {
        return 'wc-order-hpos-date';
    }

    public function get_title()
    {
        return esc_html__('Order Date', 'hw-ele-woo-dynamic');
    }

    public function get_categories()
    {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls()
    {
        $this->add_control(
            'date_format',
            [
                'label' => esc_html__('Date Format', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'description' => esc_html__('Use PHP date format. Leave empty to use the site default.', 'hw-ele-woo-dynamic'),
                'default' => '',
                'placeholder' => get_option('date_format'),
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

        $date = $order->get_date_created();

        if (! $date instanceof WC_DateTime) {
            echo '';
            return;
        }

        $settings = $this->get_settings_for_display();
        $format = ! empty($settings['date_format']) ? $settings['date_format'] : get_option('date_format');

        echo esc_html($date->date_i18n($format));
    }
}
