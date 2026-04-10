<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;
use ElementorPro\Modules\QueryControl\Module as QueryModule;

class WCPurchasedProducts extends Base {

    /**
     * Returns the unique condition ID.
     *
     * @return string
     */
    public function get_id() {
        return 'wc-purchased-products';
    }

    /**
     * Returns the display name for this condition.
     *
     * @return string
     */
    public function get_name() {
        return __('Purchased Products', 'hw-ele-woo-dynamic');
    }

    /**
     * Returns the condition group.
     *
     * @return string
     */
    public function get_group() {
        return 'WooCommerce Extra';
    }

    /**
     * Check if the current user has purchased any of the selected products.
     *
     * @param array $args Condition arguments including 'type' (show/hide) and 'condition_settings'.
     * @return bool
     */
    public function check($args = []) {
        $user_id = get_current_user_id();

        if (!$user_id) {
            return false;
        }

        $selected = isset($args['condition_settings']['selected_products']) ? (array) $args['condition_settings']['selected_products'] : [];
        $selected = array_filter(array_map('absint', $selected));

        if (empty($selected)) {
            return false;
        }

        $has_purchased = false;

        foreach ($selected as $product_id) {
            if (wc_customer_bought_product('', $user_id, $product_id)) {
                $has_purchased = true;
                break;
            }
        }

        $type = isset($args['type']) ? $args['type'] : 'show';

        return ('hide' === $type) ? !$has_purchased : $has_purchased;
    }

    /**
     * Define custom controls — Elementor Pro AJAX-powered query control for product search.
     *
     * @return array
     */
    public function get_custom_controls() {
        return [
            'selected_products' => [
                'label'       => __('Select Products', 'hw-ele-woo-dynamic'),
                'description' => __('If multiple products are selected, the condition uses OR logic — at least one must have been purchased.', 'hw-ele-woo-dynamic'),
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
