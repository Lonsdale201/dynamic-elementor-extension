<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class IsProductOutOfStock extends Condition_Base {

    /**
     * Returns the type of condition.
     *
     * @return string The type of condition.
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
        return 'is_product_out_of_stock';
    }

    /**
     * Returns the label for the condition to be displayed in the UI.
     *
     * @return string The label for the condition.
     */
    public function get_label() {
        return esc_html__( 'Is Product Out Of Stock', 'hw-ele-woo-dynamic' );
    }

    /**
     * Checks if the product is out of stock.
     *
     * @param array $args Condition arguments.
     * @return bool True if the product is out of stock, false otherwise.
     */
    public function check( $args ) {
        // Ensure that WooCommerce is active before executing any WooCommerce-related checks.
        if ( ! Dependencies::is_woocommerce_active() ) {
            return false;
        }

        global $product;

        // If $product is not an instance of WC_Product, try to get the product using the current post ID.
        if ( ! $product instanceof \WC_Product ) {
            $product = wc_get_product( get_the_ID() );
        }

        // Return true if the product exists and is not in stock, otherwise return false.
        return $product ? !$product->is_in_stock() : true;
    }
}
