<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class ProductIsDigital extends Condition_Base {

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
        return 'product_is_digital';
    }

    /**
     * Returns the label for the condition to be displayed in the UI.
     *
     * @return string The condition label.
     */
    public function get_label() {
        return esc_html__('Is Product Virtual', 'hw-ele-woo-dynamic');
    }

    /**
     * Checks if the product is a virtual (digital) product.
     *
     * @param array $args Condition arguments.
     * @return bool True if the product or one of its variations is virtual, false otherwise.
     */
    public function check($args) {
        // Ensure that WooCommerce is active before performing any product checks.
        if ( ! Dependencies::is_woocommerce_active() ) {
            return false;
        }

        // Get the product object from the current post ID.
        $product = wc_get_product(get_the_ID());
        
        // Return false if no product is found.
        if ( ! $product ) {
            return false;
        }

        // Check if the product is a virtual product.
        if ( $product->is_virtual() ) {
            return true;
        }

        // If the product is a variable product, check its variations.
        if ( $product->is_type('variable') ) {
            foreach ( $product->get_children() as $child_id ) {
                $variation = wc_get_product($child_id);
                
                // If any variation is virtual, return true.
                if ( $variation && $variation->is_virtual() ) {
                    return true;
                }
            }
        }

        // Return false if neither the product nor any variation is virtual.
        return false;
    }
}
