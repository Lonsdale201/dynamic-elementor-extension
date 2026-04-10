<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use HelloWP\HWEleWooDynamic\Modules\Helpers\CartAjaxRefresh;

class FreeShippingAmount extends Tag {

    public function get_name() {
        return 'free-shipping-amount';
    }

    public function get_title() {
        return esc_html__('Free Shipping Amount', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::NUMBER_CATEGORY, Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'advanced_mode',
            [
                'label'        => esc_html__('Advanced Mode', 'hw-ele-woo-dynamic'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Yes', 'hw-ele-woo-dynamic'),
                'label_off'    => esc_html__('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default'      => '',
            ]
        );

        $this->add_control(
            'template',
            [
                'label'       => esc_html__('Template', 'hw-ele-woo-dynamic'),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => '{freeamount}',
                'description' => esc_html__('Macros: {freeamount} = free shipping threshold, {remainingamount} = amount still needed (AJAX refreshed).', 'hw-ele-woo-dynamic'),
                'condition'   => [
                    'advanced_mode' => 'yes',
                ],
            ]
        );
    }

    public function render() {
        $min_amount = CartAjaxRefresh::get_free_shipping_min_amount();

        if ($min_amount === false) {
            return;
        }

        $settings = $this->get_settings();

        if ('yes' !== ($settings['advanced_mode'] ?? '')) {
            echo wp_kses_post(wc_price($min_amount));
            return;
        }

        if (null === WC()->cart) {
            wc_load_cart();
        }

        $template   = $settings['template'] ?? '{freeamount}';
        $cart_total = CartAjaxRefresh::get_cart_subtotal_for_shipping();
        $remaining  = max(0, $min_amount - $cart_total);

        $output = str_replace(
            ['{freeamount}', '{remainingamount}'],
            [wp_kses_post(wc_price($min_amount)), wp_kses_post(wc_price($remaining))],
            $template
        );

        $refresh_data = wp_json_encode([
            'advanced_mode' => 'yes',
            'template'      => $template,
        ]);

        echo '<span class="hw-cart-dynamic" data-tag="free-shipping-amount" data-settings="' . esc_attr($refresh_data) . '">';
        echo wp_kses_post($output);
        echo '</span>';
    }
}
