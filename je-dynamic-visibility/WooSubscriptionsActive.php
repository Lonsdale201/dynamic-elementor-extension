<?php
namespace HelloWP\HWEleWooDynamic\JEDynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;


class WooSubscriptionsActive extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

    public function get_id() {
        return 'woo-subscriptions-active-subscription';
    }

    public function get_name() {
        return __( 'Current user any active subscriptions', 'jet-engine' );
    }

    public function get_group() {
        return 'Woo Subscriptions';
    }

    public function check($args = array()) {
        $user_id = get_current_user_id();
    
        if (!$user_id) {
            return false;
        }
    
        if (!function_exists('wcs_get_users_subscriptions')) {
            return false;
        }
    
        $subscriptions = wcs_get_users_subscriptions($user_id);
    
        $has_active_subscription = false;
        foreach ($subscriptions as $subscription) {
            if ($subscription->has_status('active')) {
                $has_active_subscription = true;
                break; 
            }
        }
    
        $type = isset($args['type']) ? $args['type'] : 'show';
        return ('hide' === $type) ? !$has_active_subscription : $has_active_subscription;
    }
    

    public function is_for_fields() {
        return false; 
    }

    public function need_value_detect() {
        return false; 
    }

}

