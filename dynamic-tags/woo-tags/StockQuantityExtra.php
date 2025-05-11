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
        return __('Stock Quantity Extra', 'hw-elementor-woo-dynamic');
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
                'label' => __('Show Label', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'hw-elementor-woo-dynamic'),
                'label_off' => __('No', 'hw-elementor-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'stock_visibility',
            [
                'label' => __('Stock Visibility', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Default', 'hw-elementor-woo-dynamic'),
                    'hide_if_not_specific' => __('Hide if not specific stock quantity', 'hw-elementor-woo-dynamic'),
                    'hide_if_outofstock' => __('Hide if out of stock', 'hw-elementor-woo-dynamic'),
                ],
            ]
        );

        $this->add_control(
            'instock_text',
            [
                'label' => __('In Stock Text', 'hw-elementor-woo-dynamic'),
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
                'label' => __('Out Of Stock Text', 'hw-elementor-woo-dynamic'),
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

        if (!$product) {
            return;
        }

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
            echo !empty($outofstock_text) ? wp_kses_post($outofstock_text) : __('Out of stock', 'woocommerce');
        } else if ('instock' === $stock_status) {
            if ($stock_quantity !== null) {
                echo wp_kses_post($show_label ? $stock_quantity . ' ' . $instock_text : $stock_quantity);
            } else {
                echo !empty($instock_text) ? wp_kses_post($instock_text) : __('In stock', 'woocommerce');
            }
        }
    }
}