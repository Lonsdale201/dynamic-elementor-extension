<?php

namespace HelloWP\HWEleWooDynamic\JEDynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class UserMembershipAccessCanView extends Base {

    public function get_id() {
        return 'user-membership-access-can-view';
    }

    public function get_name() {
        return __( 'User Membership Access Can View', 'jet-engine' );
    }

    public function get_group() {
        return 'Woo Membership';
    }

    public function check( $args = array() ) {
        $user_id = get_current_user_id();
        $post_id = get_the_ID();
        $post_type = get_post_type($post_id);

        if (!$user_id || !$post_id) {
            return false;
        }

        $can_view = wc_memberships_user_can( $user_id, 'view', array( $post_type => $post_id ) );

        $type = isset( $args['type'] ) ? $args['type'] : 'show';
        return ( 'hide' === $type ) ? !$can_view : $can_view;
    }

    public function is_for_fields() {
        return false;
    }

    public function need_value_detect() {
        return false;
    }
}
