<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class ProductIsDownloadable extends Condition_Base {

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
        return 'is_downloadable_product';
    }

    /**
     * Returns the label for the condition to be displayed in the UI.
     *
     * @return string The condition label.
     */
    public function get_label() {
        return esc_html__( 'Is Product Downloadable', 'hw-elementor-woo-dynamic' );
    }

    /**
     * Checks if the product is downloadable.
     *
     * @param array $args Condition arguments.
     * @return bool True if the product or one of its variations is downloadable, false otherwise.
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

        // Check if the product exists.
        if ( $product ) {
            // Return true if the product is downloadable.
            if ( $product->is_downloadable() ) {
                return true;
            }

            // If the product is a variable product, check its variations.
            if ( $product->is_type( 'variable' ) ) {
                foreach ( $product->get_children() as $child_id ) {
                    $variation = wc_get_product( $child_id );
                    
                    // If any variation is downloadable, return true.
                    if ( $variation && $variation->is_downloadable() ) {
                        return true;
                    }
                }
            }
        }

        // Return false if neither the product nor any variation is downloadable.
        return false;
    }
}
