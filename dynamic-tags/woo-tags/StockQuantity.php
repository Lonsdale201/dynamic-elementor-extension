<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class StockQuantity extends Tag {

    public function get_name() {
        return 'stock-quantity';
    }

    public function get_title() {
        return __('Stock Quantity', 'hw-elementor-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY, Module::NUMBER_CATEGORY];
    }

    protected function _register_controls() {

    }

    public function render() {
        $product = wc_get_product(get_the_ID());

        if (!$product) {
            return;
        }

        $stock_quantity = $product->get_stock_quantity();

        if ($stock_quantity === null) {
            echo '';
        } else {
            echo esc_html($stock_quantity);
        }
    }
}
