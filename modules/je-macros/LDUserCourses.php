<?php

namespace HelloWP\HWEleWooDynamic\Modules\JEMacros;

class LDUserCourses extends \Jet_Engine_Base_Macros {

    /**
     * Macro tag identifier
     *
     * @return string
     */
    public function macros_tag() {
        return 'ld_user_courses';
    }

    /**
     * Macro display name
     *
     * @return string
     */
    public function macros_name() {
        return 'LD User Courses';
    }

    /**
     * Macro arguments
     *
     * @return array
     */
    public function macros_args() {
        return array(
            'course_status' => array(
                'label'   => 'Course Status',
                'type'    => 'select',
                'options' => array(
                    'all'        => 'All',
                    'in_progress' => 'In Progress',
                    'completed'   => 'Completed'
                ),
                'default' => 'all',
            ),
        );
    }

    /**
     * Callback to retrieve the list of user-accessible courses based on selected status
     *
     * @param array $args
     * @return string Comma-separated course IDs or an error message
     */
    public function macros_callback( $args = array() ) {
        $user_id = get_current_user_id();

        // Check if user is logged in
        if ( !$user_id ) {
            return 'User is not logged in';
        }

        // Get the course status selected in the arguments
        $course_status = !empty( $args['course_status'] ) ? $args['course_status'] : 'all';

        // Get all courses owned by the user
        $courses = learndash_user_get_enrolled_courses( $user_id, array(), true );

        // Filter courses based on the selected status
        $filtered_courses = array_filter($courses, function($course_id) use ($user_id, $course_status) {
            if ($course_status === 'completed') {
                return learndash_course_completed($user_id, $course_id);
            } elseif ($course_status === 'in_progress') {
                return !learndash_course_completed($user_id, $course_id);
            }
            return true; // If 'all' is selected, include all courses
        });

        // Check if user has any courses after filtering
        if ( empty( $filtered_courses ) ) {
            return 'No courses found for selected status';
        }

        // Return a comma-separated list of course IDs
        return implode( ',', $filtered_courses );
    }
}
