<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class IsProductOutOfStock extends Condition_Base {

    public static function get_type() {
        return 'product';
    }

    public function get_name() {
        return 'is_product_out_of_stock';
    }

    public function get_label() {
        return esc_html__( 'Is Product Out Of Stock', 'hw-ele-woo-dynamic' );
    }

    public function check( $args ) {
        global $product;

        if ( ! $product instanceof WC_Product ) {
            $product = wc_get_product( get_the_ID() );
        }

        return $product ? !$product->is_in_stock() : true;
    }
}

