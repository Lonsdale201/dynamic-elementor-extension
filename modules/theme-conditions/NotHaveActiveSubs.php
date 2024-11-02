<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;
use WC_Subscriptions_Manager;

class NotHaveActiveSubs extends Condition_Base {

    public function get_name() {
        return 'not_have_active_subs';
    }

    public function get_label() {
        return esc_html__('Current User Not Have Active Subscriptions', 'hw-ele-woo-dynamic');
    }

    public function check($args) {
        if (!is_user_logged_in()) {
            return true;
        }
        
        $user_id = get_current_user_id();
        $subscriptions = wcs_get_users_subscriptions($user_id);

        foreach ($subscriptions as $subscription) {
            if ($subscription->has_status('active')) {
                return false; 
            }
        }

        return true; 
    }
}
