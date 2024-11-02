<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class UserMembershipAccessCanView extends Base {

    /**
     * Returns the unique ID for the condition
     * 
     * @return string
     */
    public function get_id() {
        return 'user-membership-access-can-view';
    }

    /**
     * Returns the display name for the condition in the admin panel
     * 
     * @return string
     */
    public function get_name() {
        return __( 'User Membership Access Can View', 'jet-engine' );
    }

    /**
     * Defines the group this condition belongs to in JetEngine's visibility settings
     * 
     * @return string
     */
    public function get_group() {
        return 'Woo Membership';
    }

    /**
     * Main check method to determine if the user can view the current post based on membership
     * 
     * @param array $args Arguments for the visibility check
     * @return bool True if the condition is met, false otherwise
     */
    public function check( $args = array() ) {
        // Retrieve the current user ID and the current post ID
        $user_id = get_current_user_id();
        $post_id = get_the_ID();
        $post_type = get_post_type($post_id);

        // If user is not logged in or post is invalid, return false
        if (!$user_id || !$post_id) {
            return false;
        }

        // Check if the user has view access for the current post based on their membership level
        $can_view = wc_memberships_user_can( $user_id, 'view', array( $post_type => $post_id ) );

        // Determine whether to show or hide based on the 'type' argument ('show' by default)
        $type = isset( $args['type'] ) ? $args['type'] : 'show';
        return ( 'hide' === $type ) ? !$can_view : $can_view;
    }

    /**
     * Specifies that this condition is not intended for form fields
     * 
     * @return bool
     */
    public function is_for_fields() {
        return false;
    }

    /**
     * Specifies that no value detection is necessary for this condition
     * 
     * @return bool
     */
    public function need_value_detect() {
        return false;
    }
}
