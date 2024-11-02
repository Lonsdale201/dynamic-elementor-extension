<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class ProductIsDigital extends Condition_Base {

    public static function get_type() {
        return 'product';
    }

    public function get_name() {
        return 'product_is_digital';
    }

    public function get_label() {
        return esc_html__('Is Product Virtual', 'hw-ele-woo-dynamic');
    }

    public function check($args) {
        $product = wc_get_product(get_the_ID());
        if (!$product) {
            return false;
        }

        if ($product->is_virtual()) {
            return true;
        }

        if ($product->is_type('variable')) {
            foreach ($product->get_children() as $child_id) {
                $variation = wc_get_product($child_id);
                if ($variation && $variation->is_virtual()) {
                    return true;
                }
            }
        }

        return false;
    }
}
