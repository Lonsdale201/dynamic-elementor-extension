<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class CoursePartOfSpecificGroups extends Base {

    /**
     * Retrieve the unique identifier for this condition.
     *
     * @return string
     */
    public function get_id() {
        return 'course-part-of-specific-groups';
    }

    /**
     * Retrieve the display name for this condition.
     *
     * @return string
     */
    public function get_name() {
        return __('Course Part of Specific Groups', 'hw-ele-woo-dynamic');
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
     * Check if the current course is part of any selected LearnDash group.
     *
     * @param array $args Array containing parameters specifying display conditions.
     * @return bool True if the course is part of any selected group, false otherwise.
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

        // Get selected groups from the condition settings
        $selected_groups = isset($args['condition_settings']['selected_groups']) ? $args['condition_settings']['selected_groups'] : [];

        // Get the course name contain text
        $course_name_contain = isset($args['condition_settings']['course_name_contain']) ? $args['condition_settings']['course_name_contain'] : '';

        // Retrieve all LearnDash groups if course name contain is set
        $group_names_match = [];
        if (!empty($course_name_contain)) {
            $all_groups = $this->get_learndash_groups();
            foreach ($all_groups as $group_id => $group_name) {
                if (stripos($group_name, $course_name_contain) !== false) {
                    $group_names_match[] = $group_id;
                }
            }
        }

        // Determine if the course is in any selected group or name-matched group
        $is_in_group = !empty(array_intersect($course_groups, $selected_groups)) || !empty(array_intersect($course_groups, $group_names_match));

        // Handle the visibility type (show/hide)
        $type = isset($args['type']) ? $args['type'] : 'show';

        // If type is "hide", return the opposite of $is_in_group
        return $type === 'hide' ? !$is_in_group : $is_in_group;
    }

    /**
     * Retrieve available LearnDash groups for the select2 control.
     *
     * @return array Associative array of group IDs and names.
     */
    private function get_learndash_groups() {
        $groups = get_posts([
            'post_type'      => 'groups',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);

        $options = [];
        foreach ($groups as $group) {
            $options[$group->ID] = $group->post_title;
        }

        return $options;
    }

    /**
     * Define custom controls for this condition.
     *
     * @return array Controls configuration.
     */
    public function get_custom_controls() {
        $group_options = $this->get_learndash_groups();

        return [
            'selected_groups' => [
                'label'    => __('Select Groups', 'hw-ele-woo-dynamic'),
                'type'     => 'select2',
                'multiple' => true,
                'default'  => [],
                'options'  => $group_options,
            ],
            'course_name_contain' => [
                'label'       => __('Group Name Contain', 'hw-ele-woo-dynamic'),
                'type'        => 'text',
                'default'     => '',
                'description' => __('If both options are filled, the condition will evaluate using an OR relationship.', 'hw-ele-woo-dynamic'),
            ],
        ];
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
