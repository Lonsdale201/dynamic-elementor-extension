<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

/**
 * UserAvailableCoursesCount Elementor Dynamic Tag
 *
 * Displays the total number of courses the logged-in user is enrolled in.
 */
class UserAvailableCoursesCount extends Tag {

    public function get_name() {
        return 'user-available-courses-count';
    }

    public function get_title() {
        return __( 'User Enrolled Courses Count', 'hw-ele-woo-dynamic' );
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
     * Render the number of enrolled courses or an empty value if not applicable.
     */
    public function render() {
        // Return empty if the user is not logged in.
        if ( ! is_user_logged_in() ) {
            echo ''; // Return empty if not logged in
            return;
        }

        $user_id = get_current_user_id();
        
        // Fetch the courses the user is currently enrolled in using LearnDash.
        $courses_owned = learndash_user_get_enrolled_courses( $user_id, array(), true );

        // Calculate the total number of enrolled courses.
        $course_count = is_array( $courses_owned ) ? count( $courses_owned ) : 0;

        // Display the course count if greater than zero; otherwise, output empty.
        if ( $course_count > 0 ) {
            echo esc_html( $course_count );
        } else {
            echo ''; // Empty value if no courses
        }
    }
}
