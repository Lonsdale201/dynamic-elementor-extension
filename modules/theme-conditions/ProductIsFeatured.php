<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class ProductIsFeatured extends Condition_Base {

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
        return 'is_product_featured';
    }

    /**
     * Returns the label for the condition to be displayed in the UI.
     *
     * @return string The condition label.
     */
    public function get_label() {
        return esc_html__( 'Is Product Featured', 'hw-ele-woo-dynamic' );
    }

    /**
     * Checks if the product is marked as featured.
     *
     * @param array $args Condition arguments.
     * @return bool True if the product is featured, false otherwise.
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

        // Return true if the product exists and is marked as featured, otherwise return false.
        return $product ? $product->is_featured() : false;
    }
}
