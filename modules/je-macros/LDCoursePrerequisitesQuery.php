<?php

namespace HelloWP\HWEleWooDynamic\Modules\JEMacros;

class LDCoursePrerequisitesQuery extends \Jet_Engine_Base_Macros {

    /**
     * Macro tag identifier
     *
     * @return string
     */
    public function macros_tag() {
        return 'ld_course_prerequisites_query';
    }

    /**
     * Macro display name
     *
     * @return string
     */
    public function macros_name() {
        return 'LD Course Prerequisites Query';
    }

    /**
     * Macro arguments (no arguments required here)
     *
     * @return array
     */
    public function macros_args() {
        return array(); 
    }

    /**
     * Callback to retrieve prerequisite courses for the current course
     *
     * @param array $args
     * @return string Comma-separated prerequisite course IDs or an error message
     */
    public function macros_callback( $args = array() ) {
        global $post;

        $course_id = isset( $post ) ? $post->ID : '';

        // Check if the current post is a course
        if ( 'sfwd-courses' !== get_post_type( $course_id ) ) {
            return 'This is not a course';
        }

        // Retrieve course prerequisites
        $prerequisites = learndash_get_course_prerequisite( $course_id );

        // Check if prerequisites exist and return them as a comma-separated list
        if ( is_array( $prerequisites ) && !empty( $prerequisites ) ) {
            $valid_prerequisites = array_filter( $prerequisites, function( $prerequisite ) {
                return isset( $prerequisite['post_id'] );
            });

            // If valid prerequisites exist, return them as a comma-separated list
            if (!empty($valid_prerequisites)) {
                return implode( ',', array_column($valid_prerequisites, 'post_id') );
            }
        }
        
        return 'No prerequisites found for this course';
    }
}
