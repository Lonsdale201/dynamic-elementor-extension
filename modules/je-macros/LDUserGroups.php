<?php

namespace HelloWP\HWEleWooDynamic\Modules\JEMacros;

class LDUserGroups extends \Jet_Engine_Base_Macros {

    /**
     * Macro tag identifier
     *
     * @return string
     */
    public function macros_tag() {
        return 'ld_user_groups';
    }

    /**
     * Macro display name
     *
     * @return string
     */
    public function macros_name() {
        return 'LD User Groups';
    }

    /**
     * Macro arguments
     *
     * @return array
     */
    public function macros_args() {
        return array(); // No arguments needed for this macro
    }

    /**
     * Callback to retrieve the list of user groups the current user is a member of
     *
     * @param array $args
     * @return string Comma-separated group IDs or an error message
     */
    public function macros_callback( $args = array() ) {
        $user_id = get_current_user_id();

        // Check if the user is logged in
        if ( !$user_id ) {
            return 'User is not logged in';
        }

        // Get groups the user belongs to using LearnDash functions
        $user_groups = learndash_get_users_group_ids( $user_id );

        // Check if the user is not part of any group
        if ( empty( $user_groups ) ) {
            return 'User is not part of any groups';
        }

        // Return a comma-separated list of group IDs
        return implode( ',', $user_groups );
    }
}
