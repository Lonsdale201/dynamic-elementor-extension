<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class LDUserCompletedCurrentCourse extends Base {

    /**
     * Condition ID for visibility rule
     *
     * @return string
     */
    public function get_id() {
        return 'ld-user-completed-current-course';
    }

    /**
     * Condition display name for admin panel
     *
     * @return string
     */
    public function get_name() {
        return __('Course Completed', 'hw-ele-woo-dynamic');
    }

    /**
     * Group under which this condition falls in the visibility settings
     *
     * @return string
     */
    public function get_group() {
        return 'LearnDash';
    }

    /**
     * Main check function to determine if the condition is met
     *
     * @param array $args Visibility condition arguments
     * @return bool
     */
    public function check( $args = array() ) {
        $user_id = get_current_user_id();

        // If user is not logged in, return false
        if ( !$user_id ) {
            return false;
        }

        global $post;
        $course_id = isset($post->ID) ? $post->ID : 0;

        // Verify if the current post is a LearnDash course
        if ( 'sfwd-courses' !== get_post_type( $course_id ) ) {
            return false;
        }

        // Check if the user has completed the course
        $completed = learndash_course_completed( $user_id, $course_id );

        // Determine if we need to show or hide based on completion
        $type = isset( $args['type'] ) ? $args['type'] : 'show';
        return ('hide' === $type) ? !$completed : $completed;
    }

    /**
     * Set this condition not to be for form fields
     *
     * @return bool
     */
    public function is_for_fields() {
        return false;
    }

    /**
     * Indicate that value detection is not needed
     *
     * @return bool
     */
    public function need_value_detect() {
        return false;
    }
}
