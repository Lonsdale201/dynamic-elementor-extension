<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class AdvancedStock extends Tag {

    public function get_name() {
        return 'advanced-stock';
    }

    public function get_title() {
        return esc_html__('Advanced Stock', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'stock_text',
            [
                'label' => esc_html__('Stock Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'Only {remainingstock} left in stock!',
                'description' => esc_html__('Use {remainingstock} to display the remaining stock quantity.', 'hw-ele-woo-dynamic'),
            ]
        );

        $this->add_control(
            'stock_threshold',
            [
                'label' => esc_html__('Stock Threshold', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::NUMBER,
                'default' => 10,
                'description' => esc_html__('Display text when stock quantity is below this threshold.', 'hw-ele-woo-dynamic'),
            ]
        );

        $this->add_control(
            'outofstock_text',
            [
                'label' => esc_html__('Text if Out of Stock', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => esc_html__('Shown instead of the stock text when quantity is zero or product is out of stock.', 'hw-ele-woo-dynamic'),
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings();
        $product = wc_get_product(get_the_ID());

        if (!$product || !$product->managing_stock() || $product->backorders_allowed()) {
            return;
        }

        $remaining_stock = $product->get_stock_quantity();

        if ( (null === $remaining_stock || $remaining_stock > $settings['stock_threshold']) && $product->is_in_stock() ) {
            return;
        }

        if (!$product->is_in_stock()) {
            if (!empty($settings['outofstock_text'])) {
                echo wp_kses_post($settings['outofstock_text']);
            }
            return;
        }

        $remaining_stock = (int) $remaining_stock;
        $replaced_text = str_replace('{remainingstock}', $remaining_stock, $settings['stock_text']);
        echo wp_kses_post($replaced_text);
    }
}
