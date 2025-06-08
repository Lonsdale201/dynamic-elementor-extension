<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class ProductIsNameYourPrice extends Condition_Base {

    public static function get_type() {
        return 'product';
    }

    public function get_name() {
        return 'product_is_name_your_price';
    }

    public function get_label() {
        return esc_html__('Is Name Your Price', 'hw-ele-woo-dynamic');
    }

    public function check($args) {
        $product = wc_get_product(get_the_ID());

        if (!$product instanceof \WC_Product) {
            return false;
        }

        return $product->get_meta('_nyp') === 'yes';
    }
}
