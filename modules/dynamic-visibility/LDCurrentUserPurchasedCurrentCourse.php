<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class LDCurrentUserPurchasedCurrentCourse extends Base {

    /**
     * Retrieve the unique identifier for this condition.
     *
     * @return string
     */
    public function get_id() {
        return 'ld-current-user-purchased-current-course';
    }

    /**
     * Retrieve the display name for this condition.
     *
     * @return string
     */
    public function get_name() {
        return __('Course Enrolled', 'hw-elementor-woo-dynamic');
    }

    /**
     * Assign this condition to the LearnDash group.
     *
     * @return string
     */
    public function get_group() {
        return 'LearnDash';
    }

    /**
     * Check if the current user has purchased the specified course.
     *
     * @param array $args Array containing parameters specifying display conditions.
     * @return bool True if the user has enrolled in the course and condition is set to 'show', false otherwise.
     */
    public function check($args = array()) {
        $user_id = get_current_user_id();

        // Validate that a user is logged in
        if (!$user_id) {
            return false;
        }

        global $post;
        $course_id = $post->ID;

        // Verify the current post type is a LearnDash course
        if ('sfwd-courses' != get_post_type($course_id)) {
            return false;
        }

        // Retrieve the list of courses the user is enrolled in
        $user_courses = ld_get_mycourses($user_id);
        $has_purchased = in_array($course_id, $user_courses);

        // Determine condition type ('show' or 'hide') and return result accordingly
        $type = isset($args['type']) ? $args['type'] : 'show';
        return ('hide' === $type) ? !$has_purchased : $has_purchased;
    }

    /**
     * Indicate that this condition is not for field-specific visibility.
     *
     * @return bool
     */
    public function is_for_fields() {
        return false;
    }

    /**
     * Indicate that value detection is not required for this condition.
     *
     * @return bool
     */
    public function need_value_detect() {
        return false;
    }
}
