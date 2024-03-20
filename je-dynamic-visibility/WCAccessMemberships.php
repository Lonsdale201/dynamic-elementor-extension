<?php
namespace HelloWP\HWEleWooDynamic\JEDynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class WCAccessMemberships extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

    public function get_id() {
        return 'wc-access-memberships';
    }

    public function get_name() {
        return __( 'Access Memberships', 'jet-engine' );
    }

    public function get_group() {
        return 'Woo Membership';
    }

    public function check( $args = array() ) {
        $user_id = get_current_user_id();

        if ( !$user_id ) {
            return false;
        }

        $selected_memberships = isset( $args['condition_settings']['selected_memberships'] ) ? $args['condition_settings']['selected_memberships'] : array();
        $user_memberships = wc_memberships_get_user_memberships( $user_id, array( 'status' => array('active') ) );

        $has_membership = true;
        foreach ( $selected_memberships as $selected_membership ) {
            $has_this_membership = false;
            if ( $selected_membership == 'any_membership' ) {
                $has_this_membership = !empty( $user_memberships );
            } else {
                foreach ( $user_memberships as $membership ) {
                    if ( $membership->get_plan()->get_slug() == $selected_membership ) {
                        $has_this_membership = true;
                        break;
                    }
                }
            }
            $has_membership = $has_membership && $has_this_membership;
        }

        $type = isset( $args['type'] ) ? $args['type'] : 'show';
        return ( 'hide' === $type ) ? !$has_membership : $has_membership;
    }


   public function get_custom_controls() {
        $memberships = wc_memberships_get_membership_plans( array( 'post_status' => 'publish' ) );
        $options = array(
            'any_membership' => __( 'Any Membership', 'jet-engine' ),
        );

        foreach ( $memberships as $membership ) {
            $options[ $membership->get_slug() ] = $membership->get_name();
        }

        return array(
            'selected_memberships' => array(
                'label'    => __( 'Select Memberships', 'jet-engine' ),
                'type'     => 'select2',
                'multiple' => true,
                'default'  => array(),
                'options'  => $options,
            ),
        );
    }

    public function is_for_fields() {
        return false; 
    }

    public function need_value_detect() {
        return false; 
    }
}
