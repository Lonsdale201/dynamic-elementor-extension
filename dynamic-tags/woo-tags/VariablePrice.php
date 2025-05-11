<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class VariablePrice extends Tag {

    public function get_name() {
        return 'variable-price-range';
    }

    public function get_title() {
        return __('Variable Price Range', 'hw-elementor-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'price_output',
            [
                'label' => __('Price Output', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'default' => 'both',
                'options' => [
                    'min' => __('Minimum Price', 'hw-elementor-woo-dynamic'),
                    'max' => __('Maximum Price', 'hw-elementor-woo-dynamic'),
                    'both' => __('Price Range', 'hw-elementor-woo-dynamic'),
                ],
            ]
        );

        $this->add_control(
            'separator',
            [
                'label' => __('Price Range Separator', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => ' - ',
                'condition' => [
                    'price_output' => 'both',
                ],
            ]
        );

        $this->add_control(
            'hide_if_not_variable',
            [
                'label' => __('Hide if Product not Variable', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', 'hw-elementor-woo-dynamic'),
                'label_off' => __('No', 'hw-elementor-woo-dynamic'),
                'return_value' => 'yes',
            ]
        );
    }

    public function render() {
        $product = wc_get_product(get_the_ID());

        if (!$product) {
            return;
        }

        if ($product->is_type('variable')) {
            $min_price = $product->get_variation_price('min', true);
            $max_price = $product->get_variation_price('max', true);
            $price_output = $this->get_settings('price_output');
            $separator = $this->get_settings('separator');

            if ('min' === $price_output) {
                echo wp_kses_post(wc_price($min_price));
            } elseif ('max' === $price_output) {
                echo wp_kses_post(wc_price($max_price));
            } else {
                echo wp_kses_post(wc_price($min_price) . $separator . wc_price($max_price));
            }

        } else {
            if ('yes' === $this->get_settings('hide_if_not_variable')) {
                return;
            }

            echo wp_kses_post($product->get_price_html());
        }
    }
}
