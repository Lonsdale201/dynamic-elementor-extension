<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class CourseCertificatesAvailable extends Base {

    /**
     * Retrieve the condition's unique ID.
     *
     * @return string
     */
    public function get_id() {
        return 'course-certificates-available';
    }

    /**
     * Retrieve the condition's display name.
     *
     * @return string
     */
    public function get_name() {
        return __('Course Certificates Available', 'hw-ele-woo-dynamic');
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
     * Check if a certificate is assigned to the course.
     *
     * @param array $args Array of arguments specifying display conditions.
     * @return bool True if the condition meets the requirement to show/hide.
     */
    public function check($args = array()) {
        global $post;
        $course_id = $post->ID;

        // Verify if the current post is a LearnDash course
        if ('sfwd-courses' != get_post_type($course_id)) {
            return false;
        }

        // Check if a certificate ID is assigned to the course
        $certificate_id = learndash_get_setting($course_id, 'certificate');
        $certificate_available = !empty($certificate_id);

        // Determine condition behavior based on the specified 'show' or 'hide' type
        $type = isset($args['type']) ? $args['type'] : 'show';
        return ('hide' === $type) ? !$certificate_available : $certificate_available;
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
