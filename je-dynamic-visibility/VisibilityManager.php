<?php

namespace HelloWP\HWEleWooDynamic\JEDynamicVisibility;

class VisibilityManager {
    private static $instance = null;

    private function __construct() {
        add_action('jet-engine/modules/dynamic-visibility/conditions/register', [$this, 'register_conditions']);
    }

    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function register_conditions($conditions_manager) {
        if (class_exists('WC_Memberships')) {
            $conditions_manager->register_condition(new WCAccessMemberships());
            $conditions_manager->register_condition(new UserMembershipAccessCanView());
        }

        if (function_exists('wcs_get_users_subscriptions')) {
            $conditions_manager->register_condition(new WooSubscriptionsActive());
        }
    }
}
