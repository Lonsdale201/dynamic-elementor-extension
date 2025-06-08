<?php
namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class MPAccessMemberships extends Base {

    /**
     * Returns the unique ID for the condition
     * 
     * @return string
     */
    public function get_id() {
        return 'mp-access-memberships';
    }

    /**
     * Returns the display name for the condition in the admin panel
     * 
     * @return string
     */
    public function get_name() {
        return __( 'MemberPress Access Memberships', 'hw-ele-woo-dynamic' );
    }

    /**
     * Defines the group this condition belongs to in JetEngine's visibility settings
     * 
     * @return string
     */
    public function get_group() {
        return 'MemberPress Membership';
    }

    /**
     * Main check function to verify if the user has access to specified memberships
     * 
     * @param array $args Arguments for the visibility check
     * @return bool True if the condition is met, false otherwise
     */
    public function check( $args = array() ) {
        $user_id = get_current_user_id();
        if ( !$user_id ) {
            return false;
        }
    
        // Retrieve selected memberships from condition settings
        $selected_memberships = isset( $args['condition_settings']['selected_memberships'] ) 
                                ? $args['condition_settings']['selected_memberships'] 
                                : array();
    
        // Get user's active memberships correctly
        $user = new \MeprUser($user_id);
        $user_memberships = $user->active_product_subscriptions();
    
        if ( empty( $user_memberships ) ) {
            return false;
        }
    
        // Convert memberships to array of IDs
        $user_membership_ids = array_map('strval', $user_memberships); 

        $has_membership = false;
        foreach ( $selected_memberships as $selected_membership ) {
            if ( in_array( $selected_membership, $user_membership_ids ) ) {
                $has_membership = true;
                break;
            }
        }
    
        // Determine if we need to show or hide based on membership status
        $type = isset( $args['type'] ) ? $args['type'] : 'show';
        return ( 'hide' === $type ) ? !$has_membership : $has_membership;
    }
    

    /**
     * Retrieve custom controls for the visibility condition settings
     * 
     * @return array List of controls including selectable memberships
     */
    public function get_custom_controls() {
        // Fetch all MemberPress memberships
        $memberships = get_posts( array(
            'post_type'      => 'memberpressproduct',
            'post_status'    => 'publish',
            'numberposts'    => -1
        ));

        $options = array();
        foreach ( $memberships as $membership ) {
            $options[ $membership->ID ] = $membership->post_title;
        }

        return array(
            'selected_memberships' => array(
                'label'    => __( 'Select Memberships', 'hw-ele-woo-dynamic' ),
                'type'     => 'select2',
                'multiple' => true,
                'default'  => array(),
                'options'  => $options,
            ),
        );
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
     * Indicates that no value detection is necessary for this condition
     * 
     * @return bool
     */
    public function need_value_detect() {
        return false; 
    }
}
