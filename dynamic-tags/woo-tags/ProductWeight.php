<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use WC_Product;

class ProductWeight extends Tag {

    public function get_name() {
        return 'product-weight';
    }

    public function get_title() {
        return __('Product Weight', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    public function render() {
        $product = wc_get_product(get_the_ID());

        if (!$product || !$product->has_weight()) {
            return;
        }

        echo esc_html($product->get_weight()) . ' ' . get_option('woocommerce_weight_unit');
    }
}
