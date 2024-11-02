<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class ProductIsExternal extends Condition_Base {

    public static function get_type() {
        return 'product';
    }

    public function get_name() {
        return 'is_external_product';
    }

    public function get_label() {
        return esc_html__('Is Product External', 'hw-ele-woo-dynamic');
    }

    public function check($args) {
        global $product;

        if (!$product instanceof WC_Product) {
            $product = wc_get_product(get_the_ID());
        }

        if ($product && $product->is_type('external')) {
            return true;
        }

        return false;
    }
}