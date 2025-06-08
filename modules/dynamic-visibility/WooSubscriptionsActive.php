<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class WooSubscriptionsActive extends Base {

    /**
     * Unique ID for this condition, used for registration
     *
     * @return string
     */
    public function get_id() {
        return 'woo-subscriptions-active-subscription';
    }

    /**
     * The display name of the condition shown in the admin interface
     *
     * @return string
     */
    public function get_name() {
        return __('Has Active Subscriptions With Selected Statuses', 'hw-ele-woo-dynamic');
    }

    /**
     * Group under which this condition is categorized in the visibility settings
     *
     * @return string
     */
    public function get_group() {
        return 'Woo Subscriptions';
    }

    /**
     * Define custom controls for selecting specific subscription statuses
     *
     * @return array
     */
    public function get_custom_controls() {
        // Retrieve available subscription statuses
        $statuses = wcs_get_subscription_statuses();
        $options = [];
        
        // Format status labels for the select control
        foreach ($statuses as $status => $label) {
            $options[$status] = ucwords(str_replace('wc-', '', $label));
        }

        return [
            'subscription_statuses' => [
                'label'       => __('Select Subscription Statuses', 'hw-ele-woo-dynamic'),
                'type'        => 'select2',
                'multiple'    => true,
                'default'     => ['wc-active'],
                'options'     => $options,
                'label_block' => true,
            ],
        ];
    }

    /**
     * Main function to check if the user has active subscriptions with selected statuses
     *
     * @param array $args Array of arguments for the condition, including 'type' and custom settings
     * @return bool True if condition is met based on selected statuses, false otherwise
     */
    public function check($args = []) {
        $user_id = get_current_user_id();
    
        // Return false if the user is not logged in or WooCommerce Subscriptions is not active
        if (!$user_id || !function_exists('wcs_get_users_subscriptions')) {
            return false;
        }
    
        // Fetch the user's subscriptions and selected statuses for the condition
        $subscriptions = wcs_get_users_subscriptions($user_id);
        $selected_statuses = isset($args['condition_settings']['subscription_statuses']) 
            ? $args['condition_settings']['subscription_statuses'] 
            : ['wc-active'];
    
        // Check if any subscription matches the selected statuses
        foreach ($subscriptions as $subscription) {
            if (in_array('wc-' . $subscription->get_status(), $selected_statuses)) {
                // Return based on 'show' or 'hide' type in settings
                return ('hide' === $args['type']) ? false : true;
            }
        }
    
        // If no matching subscriptions found, return based on 'hide' type
        return ('hide' === $args['type']) ? true : false;
    }

    /**
     * Specify that this condition does not apply to form fields
     *
     * @return bool
     */
    public function is_for_fields() {
        return false;
    }

    /**
     * Indicate that value detection is not needed for this condition
     *
     * @return bool
     */
    public function need_value_detect() {
        return false;
    }
}
