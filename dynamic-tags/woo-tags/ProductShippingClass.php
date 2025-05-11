<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use WC_Product;

class ProductShippingClass extends Tag {

    public function get_name() {
        return 'product-shipping-class';
    }

    public function get_title() {
        return __('Product Shipping Class', 'hw-elementor-woo-dynamic');
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
            echo '';
            return;
        }
    
        $shipping_class_id = $product->get_shipping_class_id();
        if ($shipping_class_id) {
            $shipping_class = get_term_by('id', $shipping_class_id, 'product_shipping_class');
            if ($shipping_class) {
                echo esc_html($shipping_class->name);
            } else {
                echo '';
            }
        } else {
            echo ''; 
        }
    }
    
}
