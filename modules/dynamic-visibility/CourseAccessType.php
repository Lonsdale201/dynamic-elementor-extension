<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class CourseAccessType extends Base {

    public function get_id() {
        return 'course-access-type';
    }

    public function get_name() {
        return __('Course Access Type', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'LearnDash';
    }

    /**
     * Define custom controls for the visibility condition
     *
     * @return array
     */
    public function get_custom_controls() {
        return array(
            'course_access_type' => array(
                'label'   => __('Select Access Type', 'hw-ele-woo-dynamic'),
                'type'    => 'select',
                'options' => array(
                    'open'      => __('Open', 'hw-ele-woo-dynamic'),
                    'free'      => __('Free', 'hw-ele-woo-dynamic'),
                    'paynow'    => __('Buy now', 'hw-ele-woo-dynamic'),
                    'subscribe' => __('Recurring', 'hw-ele-woo-dynamic'),
                    'closed'    => __('Closed', 'hw-ele-woo-dynamic'),
                ),
                'default' => 'open',
            ),
        );
    }

    /**
     * Check condition based on the selected course access type
     *
     * @param array $args
     * @return bool
     */
    public function check($args = array()) {
        $course_id = get_the_ID();

        // Ensure we're on a course post type
        if ('sfwd-courses' !== get_post_type($course_id)) {
            return false;
        }

        // Retrieve the course access type setting
        $course_access_type = learndash_get_setting($course_id, 'course_price_type');
        $selected_access_type = isset($args['condition_settings']['course_access_type']) ? $args['condition_settings']['course_access_type'] : 'open';

        // Determine visibility based on 'show' or 'hide' type
        $type = isset($args['type']) ? $args['type'] : 'show';
        return ('hide' === $type) ? ($course_access_type !== $selected_access_type) : ($course_access_type === $selected_access_type);
    }

    public function is_for_fields() {
        return false;
    }

    public function need_value_detect() {
        return false;
    }
}
