<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class CurrentMembershipExpired extends Base {

    /**
     * Retrieve the unique identifier for this condition.
     *
     * @return string
     */
    public function get_id() {
        return 'current-membership-expired';
    }

    /**
     * Retrieve the display name for this condition.
     *
     * @return string
     */
    public function get_name() {
        return __( 'Current Membership Expired', 'hw-ele-woo-dynamic' );
    }

    /**
     * Assign this condition to the Woo Membership group.
     *
     * @return string
     */
    public function get_group() {
        return 'Woo Membership';
    }

    /**
     * Check if the user's current membership has expired.
     *
     * @param array $args Array containing parameters specifying display conditions.
     * @return bool True if the membership is expired and condition is set to 'show', false otherwise.
     */
    public function check($args = []) {
        $user_id = get_current_user_id();
        if (!$user_id) {
            return false; // Exit if no user is logged in
        }

        $user_membership_id = get_the_ID();
        $membership = new \WC_Memberships_User_Membership($user_membership_id);

        // Validate that membership exists and belongs to the current user
        if (!$membership || $membership->get_user_id() !== $user_id) {
            return false;
        }

        // Define condition type ('show' or 'hide') and check membership expiration status
        $type = isset($args['type']) ? $args['type'] : 'show';
        $expired = 'expired' === $membership->get_status();

        return ('hide' === $type) ? !$expired : $expired;
    }

    /**
     * Set custom controls for this condition if needed.
     *
     * @return void
     */
    public function get_custom_controls() {
        // Custom controls are not required for this condition
    }

    /**
     * Indicate that this condition is not for field-specific visibility.
     *
     * @return bool
     */
    public function is_for_fields() {
        return false;
    }

    /**
     * Indicate that value detection is not required for this condition.
     *
     * @return bool
     */
    public function need_value_detect() {
        return false;
    }
}
