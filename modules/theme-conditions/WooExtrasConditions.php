<?php
namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class WooExtrasConditions extends Condition_Base {

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
        return 'product_extras';
    }

    /**
     * Returns the label for the condition to be displayed in the UI.
     *
     * @return string The condition label.
     */
    public function get_label() {
        return esc_html__('Product Extras', 'hw-ele-woo-dynamic');
    }

    /**
     * Always returns true as a base condition.
     *
     * @param array $args Condition arguments.
     * @return bool True.
     */
    public function check($args) {
        // Ensure that WooCommerce is active before executing any product-related conditions.
        if ( ! Dependencies::is_woocommerce_active() ) {
            return false;
        }

        return true;
    }

    /**
     * Registers sub-conditions under this condition.
     */
    public function register_sub_conditions() {
        // Ensure that WooCommerce is active before registering sub-conditions.
        if ( ! Dependencies::is_woocommerce_active() ) {
            return;
        }

        // Register WooCommerce-related sub-conditions.
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsDigital());
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsDownloadable());
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsFeatured());
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\IsProductOutOfStock());
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsOnSale());
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsVariable());
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsExternal());
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsPurchasedUser());
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsIndividuallySold());

        // Register sub-condition for WooCommerce Subscriptions if the plugin is active.
        if ( class_exists( 'WC_Subscriptions' ) ) {
            $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsSubscriptions());
        }
        if ( Dependencies::is_product_bundles_active() ) {
            $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsBundle());
        }
        
    }
}
