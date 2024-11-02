<?php
namespace HelloWP\HWEleWooDynamic\Modules\JEMacros;

class WCMembershipAccessPosts extends \Jet_Engine_Base_Macros {

    public function macros_tag() {
        return 'wc_membership_access_posts';
    }

    public function macros_name() {
        return 'WC Membership Access Posts';
    }

    public function macros_args() {
        $memberships = [];
        if ( function_exists( 'wc_memberships_get_membership_plans' ) ) {
            $plans = wc_memberships_get_membership_plans();
            foreach ( $plans as $plan ) {
                $memberships[$plan->get_id()] = $plan->get_name();
            }
        }

        return array(
            'membership' => array(
                'label'   => 'Membership',
                'type'    => 'select',
                'options' => $memberships,
                'default' => ''
            ),
        );
    }

    public function macros_callback( $args = array() ) {
        $user_id = get_current_user_id();
        $membership_id = !empty( $args['membership'] ) ? $args['membership'] : '';

        if ( !$user_id || !$membership_id ) {
            return 'User is not logged in or no membership selected';
        }

        if ( !wc_memberships_is_user_active_member( $user_id, $membership_id ) ) {
            return 'User does not have access to this membership';
        }

        $accessible_posts_ids = $this->get_accessible_posts_for_user_by_membership($user_id, $membership_id);

        if ( empty( $accessible_posts_ids ) ) {
            return 'No posts found for this membership';
        }

        return implode( ', ', $accessible_posts_ids );
    }

    protected function get_accessible_posts_for_user_by_membership($user_id, $membership_id) {
        $accessible_posts_ids = [];

        $post_types = get_post_types(array('public' => true), 'names');
        
        foreach ($post_types as $post_type) {
            $args = array(
                'post_type'      => $post_type,
                'posts_per_page' => -1,
                'fields'         => 'ids',
            );

            $query = new \WP_Query($args);

            foreach ($query->posts as $post_id) {
                if (wc_memberships_user_can($user_id, 'view', array('post' => $post_id))) {
                    $accessible_posts_ids[] = $post_id;
                }
            }
        }

        return array_unique($accessible_posts_ids);
    }
}
