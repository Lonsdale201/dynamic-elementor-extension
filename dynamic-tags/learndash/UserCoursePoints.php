<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

/**
 * UserCoursePoints Elementor Dynamic Tag
 *
 * Displays the logged-in user's accumulated course points.
 */
class UserCoursePoints extends Tag {

    public function get_name() {
        return 'user-course-points';
    }

    public function get_title() {
        return __( 'User Course Points', 'hw-ele-woo-dynamic' );
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
        // No controls are required for this tag.
    }

    /**
     * Render the user's course points if available, or an empty value if not.
     */
    public function render() {
        // Check if the user is logged in; output an empty value if not.
        if ( ! is_user_logged_in() ) {
            echo ''; // Return empty if not logged in
            return;
        }

        $user_id = get_current_user_id();
        
        // Retrieve user's total course points.
        $user_points = learndash_get_user_course_points( $user_id );

        // Display course points if greater than zero; otherwise, output empty value.
        if ( $user_points > 0 ) {
            echo esc_html( $user_points );
        } else {
            echo ''; // Empty value if user has no points
        }
    }
}
