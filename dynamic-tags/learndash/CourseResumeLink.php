<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

/**
 * CourseResumeLink Elementor Dynamic Tag
 *
 * Displays the resume URL for a LearnDash course in Elementor, based on the user's course progress.
 */
class CourseResumeLink extends Tag {

    /**
     * Returns the unique name identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'course-resume';
    }

    /**
     * Returns the title of this tag for display in Elementor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Course Resume URL', 'hw-ele-woo-dynamic' );
    }

    /**
     * Returns the group ID to categorize this tag in Elementor.
     *
     * @return string
     */
    public function get_group() {
        return 'ld_extras_courses';
    }

    /**
     * Returns the categories for this tag.
     *
     * @return array
     */
    public function get_categories() {
        return [ Module::URL_CATEGORY ];
    }

    /**
     * Registers controls for the tag in Elementor.
     *
     * No additional controls are required for this tag.
     *
     * @return void
     */
    protected function _register_controls() {
        // No additional controls needed for this tag
    }

    /**
     * Renders the course resume URL based on the user's progress.
     *
     * Outputs the URL to the first incomplete step of the course for enrolled users.
     * If the course is completed, it outputs the main course URL. For non-enrolled users, outputs the main course URL.
     *
     * @return void
     */
    public function render() {
        global $post;

        // Ensure current post is a LearnDash course
        if ( 'sfwd-courses' !== get_post_type( $post ) ) {
            echo '';
            return;
        }

        $course_id = $post->ID;
        $user_id = get_current_user_id();

        // Check if the user has access to the course
        if ( $user_id && sfwd_lms_has_access( $course_id, $user_id ) ) {
            $course_status = learndash_course_status( $course_id, $user_id, true );

            // For non-completed courses, find the resume URL of the first incomplete step
            if ( 'completed' !== $course_status ) {
                $resume_step_id = learndash_user_progress_get_first_incomplete_step( $user_id, $course_id );
                
                // Get the parent step URL for any incomplete lessons or topics
                if ( $resume_step_id ) {
                    $resume_step_id = learndash_user_progress_get_parent_incomplete_step( $user_id, $course_id, $resume_step_id );
                    echo esc_url( get_permalink( $resume_step_id ) );
                } else {
                    // If no incomplete steps, fallback to the course main URL
                    echo esc_url( get_permalink( $course_id ) );
                }
            } else {
                // Course is completed, output the main course URL
                echo esc_url( get_permalink( $course_id ) );
            }
        } else {
            // Non-enrolled user fallback to the main course URL
            echo esc_url( get_permalink( $course_id ) );
        }
    }
}
