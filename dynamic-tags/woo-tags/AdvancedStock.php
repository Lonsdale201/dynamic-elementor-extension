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
        return __('Advanced Stock', 'hw-ele-woo-dynamic');
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
                'label' => __('Stock Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'Only {remainingstock} left in stock!',
                'description' => __('Use {remainingstock} to display the remaining stock quantity.', 'hw-ele-woo-dynamic'),
            ]
        );

        $this->add_control(
            'stock_threshold',
            [
                'label' => __('Stock Threshold', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::NUMBER,
                'default' => 10,
                'description' => __('Display text when stock quantity is below this threshold.', 'hw-ele-woo-dynamic'),
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

        if (null === $remaining_stock || $remaining_stock > $settings['stock_threshold']) {
            return;
        }

        $replaced_text = str_replace('{remainingstock}', $remaining_stock, $settings['stock_text']);
        echo wp_kses_post($replaced_text);
    }
}
