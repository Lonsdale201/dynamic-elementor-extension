<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class ProductWidth extends Tag {

    public function get_name() {
        return 'product-width';
    }

    public function get_title() {
        return __('Product Width', 'hw-elementor-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    public function render() {
        $product = wc_get_product(get_the_ID());

        if (!$product) {
            return;
        }

        $width = $product->get_width();

        if (!empty($width)) {
            echo esc_html($width);
        }
    }
}
