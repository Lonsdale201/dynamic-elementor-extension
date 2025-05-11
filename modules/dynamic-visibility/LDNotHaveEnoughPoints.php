<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class LDNotHaveEnoughPoints extends Base {

    /**
     * Retrieve the unique identifier for this condition.
     *
     * @return string
     */
    public function get_id() {
        return 'ld-not-have-enough-points';
    }

    /**
     * Retrieve the display name for this condition.
     *
     * @return string
     */
    public function get_name() {
        return __('Not Have Enough Points', 'hw-elementor-woo-dynamic');
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
     * Check if the user has insufficient points for course access.
     *
     * @param array $args Array containing parameters specifying display conditions.
     * @return bool True if the user lacks the required points for access, false otherwise.
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
        if ('sfwd-courses' !== get_post_type($course_id)) {
            return false;
        }

        // Ensure user does not already have access to the course
        if (sfwd_lms_has_access($course_id, $user_id)) {
            return false;
        }

        // Retrieve the user’s current points and the required points for the course
        $user_points = learndash_get_user_course_points($user_id);
        $course_access_points = learndash_get_setting($course_id, 'course_points_access');

        // Check if the user has fewer points than required for course access
        return (!empty($course_access_points) && ($user_points < $course_access_points));
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
