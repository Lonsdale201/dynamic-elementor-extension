<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class ProductIsBundle extends Condition_Base {

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
        return 'is_product_bundle';
    }

    /**
     * Returns the label for the condition to be displayed in the UI.
     *
     * @return string The condition label.
     */
    public function get_label() {
        return esc_html__( 'Is Product Bundle', 'hw-elementor-woo-dynamic' );
    }

    /**
     * Checks if the product is a bundle.
     *
     * @param array $args Condition arguments.
     * @return bool True if the product is a bundle, false otherwise.
     */
    public function check( $args ) {
        // Ensure that WooCommerce and WooCommerce Product Bundles are active before checking.
        if ( ! Dependencies::is_woocommerce_active() || ! Dependencies::is_product_bundles_active() ) {
            return false;
        }

        global $product;

        // Get the product object if it's not already set or it's not a valid WC_Product instance.
        if ( ! $product instanceof \WC_Product ) {
            $product = wc_get_product( get_the_ID() );
        }

        // Check if the product exists and is an instance of WC_Product_Bundle.
        return $product instanceof \WC_Product_Bundle;
    }
}
