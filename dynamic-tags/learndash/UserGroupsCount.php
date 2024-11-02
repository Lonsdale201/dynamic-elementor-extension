<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

/**
 * UserGroupsCount Elementor Dynamic Tag
 *
 * Displays the total number of groups the logged-in user is a member of.
 */
class UserGroupsCount extends Tag {

    public function get_name() {
        return 'user-groups-count';
    }

    public function get_title() {
        return __( 'User Groups Count', 'hw-ele-woo-dynamic' );
    }

    public function get_group() {
        return 'ld_extras_global';
    }

    public function get_categories() {
        return [ Module::TEXT_CATEGORY, Module::NUMBER_CATEGORY ];
    }

    /**
     * Register controls for Elementor editor.
     */
    protected function _register_controls() {
        // No additional controls are required for this tag.
    }

    /**
     * Render the number of groups the user is a member of.
     */
    public function render() {
        // Return empty if the user is not logged in.
        if ( ! is_user_logged_in() ) {
            echo ''; // Return empty if not logged in
            return;
        }

        $user_id = get_current_user_id();
        
        // Fetch the groups the user is a member of using LearnDash.
        $user_groups = learndash_get_users_group_ids( $user_id );

        // Calculate the total number of groups.
        $group_count = is_array( $user_groups ) ? count( $user_groups ) : 0;

        // Display the group count if greater than zero; otherwise, output empty.
        if ( $group_count > 0 ) {
            echo esc_html( $group_count );
        } else {
            echo ''; // Empty value if no groups
        }
    }
}
