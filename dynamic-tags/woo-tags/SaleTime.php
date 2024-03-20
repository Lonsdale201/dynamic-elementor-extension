<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class SaleTime extends Tag {

    public function get_name() {
        return 'sale-time';
    }

    public function get_title() {
        return __('Sale Time', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::DATETIME_CATEGORY];
    }

    protected function _register_controls() {
    }

    public function render() {
        $product = wc_get_product(get_the_ID());

        if (!$product) {
            return;
        }

        $sale_end_date = $product->get_date_on_sale_to();

        if (!$sale_end_date) {
            echo '';
            return;
        }

        echo esc_html($sale_end_date->date('Y-m-d H:i:s'));
    }
}
