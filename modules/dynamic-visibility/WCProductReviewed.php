<?php
namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class WCProductReviewed extends Base {

    /**
     * Returns the unique condition ID
     *
     * @return string
     */
    public function get_id() {
        return 'wc-product-reviewed';
    }

    /**
     * Returns the name displayed in the admin panel for this condition
     *
     * @return string
     */
    public function get_name() {
        return __('User Reviewed Product', 'hw-ele-woo-dynamic');
    }

    /**
     * Returns the condition group name for organizing in the visibility settings
     *
     * @return string
     */
    public function get_group() {
        return 'WooCommerce Extra';
    }

    /**
     * Checks if the current user has reviewed the specified product
     *
     * @param array $args Arguments for visibility condition (includes 'type' for show/hide)
     * @return bool True if condition is met, false otherwise
     */
    public function check($args = array()) {
        $user_id = get_current_user_id();
        $product_id = get_the_ID(); 

        // Return false if user is not logged in or product ID is not available
        if (!$user_id || !$product_id) {
            return false;
        }

        // Retrieve approved reviews from the current user for the specific product
        $comments = get_comments(array(
            'user_id' => $user_id,
            'post_id' => $product_id,
            'type'    => 'review', // Only fetch review-type comments
            'status'  => 'approve', // Only consider approved reviews
            'count'   => true // Count the reviews instead of fetching all details
        ));

        // Determine if the user has reviewed the product (more than 0 reviews)
        $has_reviewed = $comments > 0; 

        // Toggle visibility based on the 'show' or 'hide' setting in condition arguments
        $type = isset($args['type']) ? $args['type'] : 'show';
        return ('hide' === $type) ? !$has_reviewed : $has_reviewed;
    }

    /**
     * Specify that this condition is not for field-based use
     *
     * @return bool
     */
    public function is_for_fields() {
        return false;
    }

    /**
     * Indicate that value detection is not required for this condition
     *
     * @return bool
     */
    public function need_value_detect() {
        return false;
    }
}
