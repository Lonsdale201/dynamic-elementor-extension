<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;
use ElementorPro\Modules\QueryControl\Module as QueryModule;

class WCSelectedSubscriptionsActive extends Base {

    /**
     * Returns the unique condition ID.
     *
     * @return string
     */
    public function get_id() {
        return 'wc-selected-subscriptions-active';
    }

    /**
     * Returns the display name for this condition.
     *
     * @return string
     */
    public function get_name() {
        return __('Has Selected Subscription Active', 'hw-ele-woo-dynamic');
    }

    /**
     * Returns the condition group.
     *
     * @return string
     */
    public function get_group() {
        return 'Woo Subscriptions';
    }

    /**
     * Check if the current user has an active or pending-cancellation subscription
     * for any of the selected subscription products.
     *
     * @param array $args Condition arguments including 'type' (show/hide) and 'condition_settings'.
     * @return bool
     */
    public function check($args = []) {
        $user_id = get_current_user_id();

        if (!$user_id || !function_exists('wcs_get_users_subscriptions')) {
            return false;
        }

        $selected = isset($args['condition_settings']['selected_subscription_products']) ? (array) $args['condition_settings']['selected_subscription_products'] : [];
        $selected = array_filter(array_map('absint', $selected));

        if (empty($selected)) {
            return false;
        }

        $subscriptions = wcs_get_users_subscriptions($user_id);
        $has_active = false;

        foreach ($subscriptions as $subscription) {
            $status = $subscription->get_status();

            if (!in_array($status, ['active', 'pending-cancel'], true)) {
                continue;
            }

            foreach ($subscription->get_items() as $item) {
                $product_id = $item->get_product_id();

                if (in_array($product_id, $selected, true)) {
                    $has_active = true;
                    break 2;
                }
            }
        }

        $type = isset($args['type']) ? $args['type'] : 'show';

        return ('hide' === $type) ? !$has_active : $has_active;
    }

    /**
     * Define custom controls — Elementor Pro AJAX-powered query control for subscription product search.
     *
     * @return array
     */
    public function get_custom_controls() {
        return [
            'selected_subscription_products' => [
                'label'       => __('Select Subscription Products', 'hw-ele-woo-dynamic'),
                'description' => __('If multiple products are selected, the condition uses OR logic — at least one must have an active subscription.', 'hw-ele-woo-dynamic'),
                'type'        => QueryModule::QUERY_CONTROL_ID,
                'label_block' => true,
                'multiple'    => true,
                'options'     => [],
                'autocomplete' => [
                    'object' => QueryModule::QUERY_OBJECT_POST,
                    'query'  => [
                        'post_type' => 'product',
                    ],
                ],
            ],
        ];
    }

    /**
     * This condition is not for field-based use.
     *
     * @return bool
     */
    public function is_for_fields() {
        return false;
    }

    /**
     * Value detection is not required.
     *
     * @return bool
     */
    public function need_value_detect() {
        return false;
    }
}
