<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VisibilityManager {
    private static $instance = null;

    /**
     * Private constructor to initialize the class.
     * Registers the custom conditions for dynamic visibility.
     */
    private function __construct() {
        add_action('jet-engine/modules/dynamic-visibility/conditions/register', [$this, 'register_conditions']);
    }

    /**
     * Singleton method to get the single instance of the class.
     *
     * @return VisibilityManager
     */
    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Registers custom conditions for JetEngine's Dynamic Visibility module.
     *
     * @param object $conditions_manager The conditions manager object.
     */
    public function register_conditions($conditions_manager) {
        // Register the WooCommerce-related condition if WooCommerce is active
        if ( Dependencies::is_woocommerce_active() ) {
            $conditions_manager->register_condition(new WCProductReviewed());
        }

        // Register other custom conditions
        $conditions_manager->register_condition(new CheckUrlPath());
        $conditions_manager->register_condition(new IsFrontPage());

        // Register MemberPress-related conditions
        if ( Dependencies::is_memberpress_active() ) {
            $conditions_manager->register_condition(new MPAccessMemberships());
        }

        // Register WooCommerce Memberships conditions
        if ( Dependencies::is_memberships_active() ) {
            $conditions_manager->register_condition(new WCAccessMemberships());
            $conditions_manager->register_condition(new UserMembershipAccessCanView());
            $conditions_manager->register_condition(new CurrentMembershipExpired());
        }

        // Register WooCommerce Subscriptions conditions
        if ( Dependencies::is_subscriptions_active() ) {
            $conditions_manager->register_condition(new WooSubscriptionsActive());
        }

        // Register LearnDash-related conditions
        if ( Dependencies::is_learndash_active() ) {
            $conditions_manager->register_condition(new LDUserCompletedCurrentCourse());
            $conditions_manager->register_condition(new CourseAccessType());
            $conditions_manager->register_condition(new CourseCertificatesAvailable());
            $conditions_manager->register_condition(new CoursePartOfGroup());
            $conditions_manager->register_condition(new CourseNotPurchased());
            $conditions_manager->register_condition(new LDCurrentUserPurchasedCurrentCourse());
            $conditions_manager->register_condition(new LDNotHaveEnoughPoints());
            $conditions_manager->register_condition(new LDStudentLimitReached());
            $conditions_manager->register_condition(new CoursePartOfSpecificGroups());
        }
    }
}
