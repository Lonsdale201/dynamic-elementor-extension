<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class ProductIsVariable extends Condition_Base {

    /**
     * Returns the type of condition.
     *
     * @return string The condition type.
     */
    public static function get_type() {
        return 'product';
    }

    /**
     * Returns the unique name of the condition.
     *
     * @return string The condition name.
     */
    public function get_name() {
        return 'is_variable_product';
    }

    /**
     * Returns the label for the condition to be displayed in the UI.
     *
     * @return string The condition label.
     */
    public function get_label() {
        return esc_html__( 'Is Product Variable', 'hw-elementor-woo-dynamic' );
    }

    /**
     * Checks if the product is a variable product.
     *
     * @param array $args Condition arguments.
     * @return bool True if the product is a variable product, false otherwise.
     */
    public function check( $args ) {
        // Ensure that WooCommerce is active before performing any product checks.
        if ( ! Dependencies::is_woocommerce_active() ) {
            return false;
        }

        global $product;

        // Get the product object if it's not already set or it's not a valid WC_Product instance.
        if ( ! $product instanceof \WC_Product ) {
            $product = wc_get_product( get_the_ID() );
        }

        // Return true if the product exists and is of type 'variable'.
        return $product ? $product->is_type( 'variable' ) : false;
    }
}
