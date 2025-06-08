<?php
namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class WCAccessMemberships extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

    /**
     * Returns the unique ID for the condition
     * 
     * @return string
     */
    public function get_id() {
        return 'wc-access-memberships';
    }

    /**
     * Returns the display name for the condition in the admin panel
     * 
     * @return string
     */
    public function get_name() {
        return __( 'Access Memberships', 'hw-ele-woo-dynamic' );
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
     * Main check function to verify if the user has access to specified memberships
     * 
     * @param array $args Arguments for the visibility check
     * @return bool True if the condition is met, false otherwise
     */
    public function check( $args = array() ) {
        // Retrieve current user ID; return false if not logged in
        $user_id = get_current_user_id();
        if ( !$user_id ) {
            return false;
        }

        // Retrieve selected memberships from condition settings
        $selected_memberships = isset( $args['condition_settings']['selected_memberships'] ) 
                                ? $args['condition_settings']['selected_memberships'] 
                                : array();

        // Retrieve the user's active memberships
        $user_memberships = wc_memberships_get_user_memberships( $user_id, array( 'status' => array('active') ) );

        // Loop through selected memberships and determine if user has all required memberships
        $has_membership = true;
        foreach ( $selected_memberships as $selected_membership ) {
            $has_this_membership = false;
            
            // Check if the user has any active membership
            if ( $selected_membership == 'any_membership' ) {
                $has_this_membership = !empty( $user_memberships );
            } else {
                // Verify specific membership plan
                foreach ( $user_memberships as $membership ) {
                    if ( $membership->get_plan()->get_slug() == $selected_membership ) {
                        $has_this_membership = true;
                        break;
                    }
                }
            }
            
            // Update main condition status to ensure user meets all specified memberships
            $has_membership = $has_membership && $has_this_membership;
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
        // Fetch all published membership plans
        $memberships = wc_memberships_get_membership_plans( array( 'post_status' => 'publish' ) );

        // Set up the options list, starting with 'Any Membership'
        $options = array(
            'any_membership' => __( 'Any Membership', 'hw-ele-woo-dynamic' ),
        );

        // Add each available membership to the options list by slug
        foreach ( $memberships as $membership ) {
            $options[ $membership->get_slug() ] = $membership->get_name();
        }

        // Return the control for selecting memberships
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
