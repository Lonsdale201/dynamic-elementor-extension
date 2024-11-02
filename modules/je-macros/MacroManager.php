<?php

namespace HelloWP\HWEleWooDynamic\Modules\JEMacros;

use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies; 

class MacroManager {
    private static $_instance = null;

    /**
     * Singleton method to ensure a single instance.
     *
     * @return MacroManager
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor to register actions.
     */
    private function __construct() {
        add_action( 'jet-engine/register-macros', [ $this, 'register_macros' ] );
    }

    /**
     * Register macros for JetEngine.
     */
    public function register_macros() {
        // Always register the UserPurchasedProductsMac macro
        new UserPurchasedProductsMac();
        new WCLoopProducts();

        // Check if WooCommerce Memberships plugin is active using Dependencies class
        if ( Dependencies::is_memberships_active() ) {
            new WCMembershipAccessPosts();
            new UserActiveMembershipsMac();
        }

        // Register LearnDash related macros if active
        if ( Dependencies::is_learndash_active() ) {
            new LDUserCourses();
            new LDCourseAccessTypeQuery();
            new LDCoursePrerequisitesQuery();
            new LDUserGroups();
        }
    }
}
