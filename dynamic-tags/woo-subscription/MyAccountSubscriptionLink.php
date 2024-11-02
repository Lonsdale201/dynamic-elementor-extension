<?php

namespace HelloWP\HWEleWooDynamic\WooTags\Subscription;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

class MyAccountSubscriptionLink extends Tag {

    public function get_name() {
        return 'myaccount-subscription-link';
    }

    public function get_title() {
        return __('MyAccount Subscription Link', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras-user';
    }

    public function get_categories() {
        return [Module::URL_CATEGORY];
    }

    public function render() {
        if (!function_exists('wcs_get_users_subscriptions')) {
            echo '';
            return;
        }

        $user_id = get_current_user_id();
        if (!$user_id) {
            echo '';
            return;
        }

        $subscriptions = wcs_get_users_subscriptions($user_id);
        $subscriptions_count = count($subscriptions);

        if ($subscriptions_count <= 0) {
            echo '';
            return;
        }

        if ($subscriptions_count == 1) {
            $subscription = reset($subscriptions);
            $subscription_url = $subscription->get_view_order_url();
            echo esc_url($subscription_url);
        } else {
            $myaccount_page_id = get_option('woocommerce_myaccount_page_id');
            if ($myaccount_page_id) {
                $myaccount_url = get_permalink($myaccount_page_id);
                $subscriptions_endpoint = 'subscriptions';
                $subscriptions_area_url = wc_get_endpoint_url($subscriptions_endpoint, '', $myaccount_url);
                echo esc_url($subscriptions_area_url);
            } else {
                echo '';
            }
        }
    }
}
