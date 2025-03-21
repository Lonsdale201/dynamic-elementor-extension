<?php

namespace HelloWP\HWEleWooDynamic\Modules\JEMacros;

/**
 * CurrentUserRole Macro
 *
 * Returns the role of the currently logged-in user.
 */
class CurrentUserRole extends \Jet_Engine_Base_Macros {

    /**
     * Macro tag (programmatic identifier).
     *
     * Used as the unique slug for referencing the macro in JetEngine.
     *
     * @return string
     */
    public function macros_tag() {
        return 'current_user_role';
    }

    /**
     * Macro display name.
     *
     * The human-readable name shown in the JetEngine UI.
     *
     * @return string
     */
    public function macros_name() {
        return 'Current User Role';
    }

    /**
     * Define macro arguments (if needed).
     *
     * In this case, no arguments are required.
     *
     * @return array
     */
    public function macros_args() {
        return array();
    }

    /**
     * The main callback that returns the macro value.
     *
     * This method returns the first role assigned to the currently logged-in user.
     *
     * @param array $args Optional arguments passed by JetEngine.
     *
     * @return string The user's role or a message if not logged in or no role is assigned.
     */
    public function macros_callback( $args = array() ) {
        $current_user = wp_get_current_user();

        if ( empty( $current_user->ID ) ) {
            return 'No logged in user';
        }

        if ( empty( $current_user->roles ) ) {
            return 'No role assigned';
        }

        return sanitize_text_field( $current_user->roles[0] );
    }
}
