<?php

namespace HelloWP\HWEleWooDynamic\Modules\Helpers;

/**
 * Class to check plugin dependencies.
 */
class Dependencies {

     /**
     * Check if WooCommerce is active.
     *
     * @return bool
     */
    public static function is_woocommerce_active() {
        return class_exists( 'WooCommerce' );
    }

    /**
     * Check if WooCommerce Tab Manager by Skyverge
     * 
     * @return bool
     */
    public static function is_tab_manager_active() {
        return class_exists('WC_Tab_Manager_Loader'); 
    }

    /**
     * Check if WooCommerce Subscriptions is active.
     * 
     * @return bool
     */
    public static function is_subscriptions_active() {
        return function_exists('wcs_get_users_subscriptions');
    }

    /**
     * Check if WooCommerce Memberships is active.
     * 
     * @return bool
     */
    public static function is_memberships_active() {
        return function_exists('wc_memberships');
    }

    /**
     * Check if JetEngine and Dynamic Visibility module are active.
     * 
     * @return bool
     */
    public static function is_jetengine_active_and_visibility_enabled() {
        return class_exists('Jet_Engine') && function_exists('jet_engine') && jet_engine()->modules->is_module_active('dynamic-visibility');
    }

    /**
     * Check if LearnDash is active.
     * 
     * @return bool
     */
    public static function is_learndash_active() {
        return class_exists('SFWD_LMS');
    }

     /**
     * Check if WooCommerce Name Your Price is active.
     * 
     * @return bool
     */
    public static function is_name_your_price_active() {
        return defined('WC_NYP_PLUGIN_FILE') && class_exists('WC_Name_Your_Price');
    }

    /**
     * Check if MemberPress is active.
     * 
     * @return bool
     */
    public static function is_memberpress_active() {
        return class_exists('MeprAppCtrl');
    }

    /**
     * Check if WooCommerce Product Bundles is active.
     * 
     * @return bool
     */
    public static function is_product_bundles_active() {
        return class_exists( 'WC_Product_Bundle' );
    }
}
