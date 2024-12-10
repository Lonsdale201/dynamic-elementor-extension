<?php
namespace HelloWP\HWEleWooDynamic\Modules\JEMacros;

class WCMembershipAccessAllPosts extends \Jet_Engine_Base_Macros {

    public function macros_tag() {
        return 'wc_membership_access_all_posts';
    }

    public function macros_name() {
        return 'WC Membership Access All Posts';
    }

    public function macros_args() {
        // No arguments required for this macro
        return [];
    }

    public function macros_callback( $args = array() ) {
        $user_id = get_current_user_id();

        if ( !$user_id ) {
            return 'User is not logged in';
        }

        $active_memberships = wc_memberships_get_user_active_memberships( $user_id );

        if ( empty( $active_memberships ) ) {
            return "User does not have any active memberships";
        }

        $accessible_posts_ids = [];

        foreach ( $active_memberships as $membership ) {
            $membership_id = $membership->get_plan_id();
            $posts = $this->get_posts_accessible_by_membership( $user_id, $membership_id );
            $accessible_posts_ids = array_merge( $accessible_posts_ids, $posts );
        }

        $accessible_posts_ids = array_unique( $accessible_posts_ids );

        if ( empty( $accessible_posts_ids ) ) {
            return "No posts found for the user's memberships";
        }

        return implode( ', ', $accessible_posts_ids );
    }


    protected function get_posts_accessible_by_membership( $user_id, $membership_id ) {
        $accessible_posts_ids = [];
        
        $plan = wc_memberships_get_membership_plan( $membership_id );
        if ( ! $plan ) {
            return $accessible_posts_ids;
        }

        $rules = $plan->get_content_restriction_rules();
        if ( empty( $rules ) ) {
            return $accessible_posts_ids;
        }

        foreach ( $rules as $rule ) {
            $object_ids = $rule->get_object_ids();
            foreach ( $object_ids as $post_id ) {
                if ( get_post( $post_id ) ) {
                    if ( wc_memberships_user_can( $user_id, 'view', array( 'post' => $post_id ) ) ) {
                        $accessible_posts_ids[] = $post_id;
                    }
                }
            }
        }

        return array_unique( $accessible_posts_ids );
    }

    
}