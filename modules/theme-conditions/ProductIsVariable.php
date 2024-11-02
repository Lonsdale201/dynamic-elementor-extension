<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class ProductIsVariable extends Condition_Base {

    public static function get_type() {
        return 'product';
    }

    public function get_name() {
        return 'is_variable_product';
    }

    public function get_label() {
        return esc_html__( 'Is Product Variable', 'hw-ele-woo-dynamic' );
    }

    public function check( $args ) {
        global $product;

        if ( ! $product instanceof WC_Product ) {
            $product = wc_get_product( get_the_ID() );
        }

        if ( $product && $product->is_type( 'variable' ) ) {
            return true;
        }

        return false;
    }
}