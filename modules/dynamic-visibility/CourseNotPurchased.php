<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class CourseNotPurchased extends Base {

    /**
     * Retrieve the condition's unique ID.
     *
     * @return string
     */
    public function get_id() {
        return 'course-not-purchased';
    }

    /**
     * Retrieve the condition's display name.
     *
     * @return string
     */
    public function get_name() {
        return __('Course Not Enrolled', 'hw-ele-woo-dynamic');
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
     * Check if the user is not enrolled in the course.
     *
     * @param array $args Array of arguments specifying display conditions.
     * @return bool True if the condition meets the requirement to show/hide.
     */
    public function check($args = array()) {
        $user_id = get_current_user_id();
        global $post;
        $course_id = $post->ID;

        // Verify if the current post is a LearnDash course
        if ('sfwd-courses' != get_post_type($course_id)) {
            return false;
        }

        // Check if the user has access to the course
        $has_access = sfwd_lms_has_access($course_id, $user_id);

        // Determine condition behavior based on the specified 'show' or 'hide' type
        $type = isset($args['type']) ? $args['type'] : 'show';
        return ('hide' === $type) ? $has_access : !$has_access;
    }

    /**
     * Set condition usage to general content display rather than fields.
     *
     * @return bool
     */
    public function is_for_fields() {
        return false;
    }

    /**
     * Disable value detection for this condition, as it's not required.
     *
     * @return bool
     */
    public function need_value_detect() {
        return false;
    }
}
