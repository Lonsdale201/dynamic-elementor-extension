<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class LDStudentLimitReached extends Base {

    /**
     * Retrieve the unique identifier for this condition.
     *
     * @return string
     */
    public function get_id() {
        return 'ld-student-limit-reached';
    }

    /**
     * Retrieve the display name for this condition.
     *
     * @return string
     */
    public function get_name() {
        return __('Course Reached Student Limit', 'hw-elementor-woo-dynamic');
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
     * Check if the student limit for the course has been reached.
     *
     * @param array $args Array containing parameters specifying display conditions.
     * @return bool True if the student limit is reached, false otherwise.
     */
    public function check($args = array()) {
        global $post;

        // Ensure the current post type is a LearnDash course
        if ('sfwd-courses' !== get_post_type($post)) {
            return false;
        }

        $course_id = $post->ID;

        // Retrieve the seat limit for the course
        $seat_limit = $this->get_seat_limit($course_id);
        if ('' === $seat_limit) {
            return false;
        }

        // Count the number of currently enrolled students
        $enrolled_users_count = $this->get_enrolled_users_count($course_id);
        $limit_reached = ($enrolled_users_count >= $seat_limit);

        // Return based on condition type ('show' or 'hide')
        $type = isset($args['type']) ? $args['type'] : 'show';
        return ('hide' === $type) ? !$limit_reached : $limit_reached;
    }

    /**
     * Retrieve the course seat limit from course meta data.
     *
     * @param int $course_id The ID of the course.
     * @return int|string The seat limit, or an empty string if not set.
     */
    private function get_seat_limit($course_id) {
        $course_meta = get_post_meta($course_id, '_sfwd-courses', true);
        return isset($course_meta['sfwd-courses_course_seats_limit']) && is_numeric($course_meta['sfwd-courses_course_seats_limit'])
            ? (int)$course_meta['sfwd-courses_course_seats_limit']
            : '';
    }

    /**
     * Count the number of enrolled users for the given course.
     *
     * @param int $course_id The ID of the course.
     * @return int The count of enrolled users.
     */
    private function get_enrolled_users_count($course_id) {
        $user_query = new \WP_User_Query([
            'meta_query' => [
                [
                    'key'     => 'course_' . $course_id . '_access_from',
                    'compare' => 'EXISTS',
                ],
            ],
        ]);
        return $user_query->get_total();
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
