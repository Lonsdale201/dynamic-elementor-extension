<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Cart;

class CartValues extends Tag {

    public function get_name() {
        return 'cart-values';
    }

    public function get_title() {
        return __('Cart Values', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {

        $this->add_control(
            'cart_value_type',
            [
                'label' => __('Cart Value', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'return_items' => __('Return items', 'hw-ele-woo-dynamic'),
                    'cart_total' => __('Cart total', 'hw-ele-woo-dynamic'),
                    'items_count' => __('Items count', 'hw-ele-woo-dynamic'),
                ],
                'default' => 'cart_total',
            ]
        );

        $this->add_control(
            'delimiter',
            [
                'label' => __('Delimiter', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => ', ',
                'condition' => [
                    'cart_value_type' => 'return_items',
                ],
            ]
        );

        $this->add_control(
            'show_price_symbol',
            [
                'label' => __('Show Price Symbol', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => __('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'cart_value_type' => 'cart_total',
                ],
            ]
        );

        $this->add_control(
            'include_tax',
            [
                'label' => __('Include Tax', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => __('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'cart_value_type' => 'cart_total',
                ],
            ]
        );

        $this->add_control(
            'hide_if_cart_empty',
            [
                'label' => __('Hide if Cart Empty', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => __('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
    }

    public function render() {
        if ( ! function_exists( 'WC' ) ) {
            return;
        }

        if ( null === WC()->cart ) {
            wc_load_cart();
        }

        $settings = $this->get_settings();
        $cart = WC()->cart;

        if ('yes' === $settings['hide_if_cart_empty'] && $cart->is_empty()) {
            return; 
        }

        $cart_value_type = $settings['cart_value_type'];

        switch ($cart_value_type) {
            case 'return_items':
                $this->render_cart_items($settings['delimiter']);
                break;
            case 'cart_total':
                $this->render_cart_total();
                break;
            case 'items_count':
                $this->render_items_count();
                break;
        }
    }

    private function render_cart_items($delimiter) {
        $items = WC()->cart->get_cart();
        $item_names = [];

        foreach ($items as $item => $values) {
            $item_names[] = esc_html($values['data']->get_name());
        }

        echo esc_html(implode($delimiter, $item_names));
    }

    private function render_cart_total() {
        $cart = WC()->cart;
        
        $include_tax = 'yes' === $this->get_settings('include_tax');
        $cart_contents_total = $include_tax ? $cart->get_cart_contents_total() + $cart->get_cart_contents_tax() : $cart->get_cart_contents_total();
        
        $show_price_symbol = $this->get_settings('show_price_symbol');
    
        if ('yes' === $show_price_symbol) {
            echo wp_kses_post(wc_price($cart_contents_total));
        } else {
            echo esc_html(number_format($cart_contents_total, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator()));
        }
    }

    private function render_items_count() {
        echo intval(WC()->cart->get_cart_contents_count());
    }
    
}
