<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

/**
 * Class ThemeConditionManager
 *
 * Developer Notes:
 * ----------------
 * Registers custom theme conditions for Elementor if certain dependencies are active.
 */
class ThemeConditionManager {

    /**
     * Holds the singleton instance.
     *
     * @var ThemeConditionManager|null
     */
    private static $_instance = null;

    /**
     * Returns a single instance of this class.
     *
     * @return ThemeConditionManager
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Private constructor to prevent multiple instances.
     */
    private function __construct() {
        add_action( 'elementor/theme/register_conditions', [ $this, 'register_conditions' ], 995 );
    }

    /**
     * Registers sub-conditions under relevant condition groups (general, product, etc.).
     *
     * @param \Elementor\Core\Conditions\Conditions_Manager $conditions_manager
     */
    public function register_conditions( $conditions_manager ) {

        // Register under "general" condition group.
        $general_condition = $conditions_manager->get_condition( 'general' );
        if ( $general_condition ) {
            $general_condition->register_sub_condition( new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\UserRoles() );
            $general_condition->register_sub_condition( new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\UserStatus() );

            // WooCommerce Subscriptions check (optional).
            if ( function_exists( 'wcs_get_users_subscriptions' ) ) {
                $general_condition->register_sub_condition( new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\NotHaveActiveSubs() );
            }
        }

        /**
         * Register product-related conditions only if WooCommerce is active.
         */
        if ( Dependencies::is_woocommerce_active() ) {
            $product_condition = $conditions_manager->get_condition( 'product' );
            if ( $product_condition ) {
                $product_condition->register_sub_condition( new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\WooExtrasConditions() );

                // Check if "Name Your Price" plugin is active.
                if ( Dependencies::is_name_your_price_active() ) {
                    $product_condition->register_sub_condition( new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsNameYourPrice() );
                }
            }
        }
    }

}
