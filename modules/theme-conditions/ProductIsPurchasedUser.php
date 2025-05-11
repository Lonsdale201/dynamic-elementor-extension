<?php
namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class ProductIsPurchasedUser extends Condition_Base {

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
        return 'is_purchased_by_user';
    }

    /**
     * Returns the label for the condition to be displayed in the UI.
     *
     * @return string The condition label.
     */
    public function get_label() {
        return esc_html__('Is Purchased By User', 'hw-elementor-woo-dynamic');
    }

    /**
     * Checks if the current user has purchased the product.
     *
     * @param array $args Condition arguments.
     * @return bool True if the user has purchased the product, false otherwise.
     */
    public function check($args) {
        // Ensure that WooCommerce is active before performing any product-related checks.
        if ( ! Dependencies::is_woocommerce_active() ) {
            return false;
        }

        // Check if the user is logged in.
        if ( ! is_user_logged_in() ) {
            return false;
        }

        $user_id = get_current_user_id();  // Get the current user ID.
        $product_id = get_the_ID();        // Get the current product ID.

        // Check if the user has purchased this product using WooCommerce's wc_customer_bought_product function.
        if ( wc_customer_bought_product( '', $user_id, $product_id ) ) {
            return true;
        }

        // Return false if the user has not purchased the product.
        return false;
    }
}
