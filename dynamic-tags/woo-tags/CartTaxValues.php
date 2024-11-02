<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Cart;

class CartTaxValues extends Tag {

    public function get_name() {
        return 'cart-tax-values';
    }

    public function get_title() {
        return esc_html__('Cart Tax Values', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'show_price_symbol',
            [
                'label' => esc_html__('Show Price Symbol', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => esc_html__('No', 'hw-ele-woo-dynamic'),
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

        $cart = WC()->cart;
        $cart_tax_total = floatval($cart->get_taxes_total());
        
        $show_price_symbol = $this->get_settings('show_price_symbol');

        if ( 'yes' === $show_price_symbol ) {
            echo wp_kses_post( wc_price( $cart_tax_total ) );
        } else {
            echo wp_kses_post( number_format( $cart_tax_total, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() ) );
        }
    }
}
