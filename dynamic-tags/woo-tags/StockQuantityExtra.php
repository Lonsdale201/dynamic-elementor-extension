<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class StockQuantityExtra extends Tag {

    public function get_name() {
        return 'stock-quantity-extra';
    }

    public function get_title() {
        return esc_html__('Stock Quantity Extra', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'show_label',
            [
                'label' => __('Show Label', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => __('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'stock_visibility',
            [
                'label' => __('Stock Visibility', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => esc_html__('Default', 'hw-ele-woo-dynamic'),
                    'hide_if_not_specific' => esc_html__('Hide if not specific stock quantity', 'hw-ele-woo-dynamic'),
                    'hide_if_outofstock' => esc_html__('Hide if out of stock', 'hw-ele-woo-dynamic'),
                ],
            ]
        );

        $this->add_control(
            'instock_text',
            [
                'label' => __('In Stock Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'show_label' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'outofstock_text',
            [
                'label' => __('Out Of Stock Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'show_label' => 'yes',
                ],
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings();
        $product = wc_get_product(get_the_ID());

        if (!$product || $product->is_type('external')) {
            return;
        }

        $stock_quantity = $product->get_stock_quantity();
        $stock_status = $product->get_stock_status();
        $show_label = $settings['show_label'] === 'yes';
        $instock_text = $settings['instock_text'];
        $outofstock_text = $settings['outofstock_text'];
        $visibility = $settings['stock_visibility'];

        if ('hide_if_not_specific' === $visibility && $stock_quantity === null) {
            return;
        }

        if ('hide_if_outofstock' === $visibility && 'outofstock' === $stock_status) {
            return; 
        }

        if ('outofstock' === $stock_status) {
            echo wp_kses_post(!empty($outofstock_text) ? $outofstock_text : esc_html__('Out of stock', 'woocommerce'));
        } elseif ('instock' === $stock_status) {
            if ($stock_quantity !== null) {
                echo wp_kses_post($show_label ? $stock_quantity . ' ' . $instock_text : $stock_quantity);
            } else {
                echo wp_kses_post(!empty($instock_text) ? $instock_text : esc_html__('In stock', 'woocommerce'));
            }
        }
    }
}
