<?php
namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class ProductIsIndividuallySold extends Condition_Base {

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
        return 'is_individually_sold_product';
    }

    /**
     * Returns the label for the condition to be displayed in the UI.
     *
     * @return string The condition label.
     */
    public function get_label() {
        return esc_html__( 'Is Product Individually Sold', 'hw-ele-woo-dynamic' );
    }

    /**
     * Checks if the product is set as "individually sold".
     *
     * @param array $args Condition arguments.
     * @return bool True if the product is individually sold, false otherwise.
     */
    public function check( $args ) {
        if ( ! Dependencies::is_woocommerce_active() ) {
            return false;
        }

        global $product;
        if ( ! $product instanceof \WC_Product ) {
            $product = wc_get_product( get_the_ID() );
        }
        return $product ? $product->is_sold_individually() : false;
    }
}
