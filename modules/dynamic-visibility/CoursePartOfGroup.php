<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class CoursePartOfGroup extends Base {

    /**
     * Retrieve the unique identifier for this condition.
     *
     * @return string
     */
    public function get_id() {
        return 'course-part-of-group';
    }

    /**
     * Retrieve the display name for this condition.
     *
     * @return string
     */
    public function get_name() {
        return __('Course Part of Any Group', 'hw-elementor-woo-dynamic');
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
     * Check if the current course is part of any LearnDash group.
     *
     * @param array $args Array containing parameters specifying display conditions.
     * @return bool True if the course is part of any group, false otherwise.
     */
    public function check($args = []) {
        global $post;
        $course_id = $post->ID;

        // Verify if the current post type is a LearnDash course
        if ('sfwd-courses' !== get_post_type($course_id)) {
            return false;
        }

        // Retrieve groups associated with the course
        $course_groups = learndash_get_course_groups($course_id);

        // Return true if the course is part of any group, false otherwise
        return !empty($course_groups);
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
