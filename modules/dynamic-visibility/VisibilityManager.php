<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

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
        // Register the custom conditions
        $conditions_manager->register_condition(new WCProductReviewed());
        $conditions_manager->register_condition(new CheckUrlPath());
        $conditions_manager->register_condition(new IsFrontPage());

        if (Dependencies::is_memberships_active()) {
            $conditions_manager->register_condition(new WCAccessMemberships());
            $conditions_manager->register_condition(new UserMembershipAccessCanView());
            $conditions_manager->register_condition(new CurrentMembershipExpired());
        }

        if (Dependencies::is_subscriptions_active()) {
            $conditions_manager->register_condition(new WooSubscriptionsActive());
        }

        if (Dependencies::is_learndash_active()) {
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
