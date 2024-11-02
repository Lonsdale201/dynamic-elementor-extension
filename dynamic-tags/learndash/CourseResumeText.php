<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

/**
 * CourseResumeText Elementor Dynamic Tag
 *
 * Displays a custom resume text based on the user's progress in a course in Elementor.
 */
class CourseResumeText extends Tag {

    /**
     * Returns the unique name identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'course-resume-text';
    }

    /**
     * Returns the title of this tag for display in Elementor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Course Resume Text', 'hw-ele-woo-dynamic' );
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
        return [ Module::TEXT_CATEGORY ];
    }

    /**
     * Registers controls for custom text options in Elementor.
     *
     * Adds controls to customize text for each course state: in progress, not started, completed, and not owned.
     *
     * @return void
     */
    protected function _register_controls() {
        $this->add_control(
            'in_progress_text',
            [
                'label' => __( 'In Progress Text', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Continue Learning',
            ]
        );

        $this->add_control(
            'not_started_text',
            [
                'label' => __( 'Not Started Text', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Start Course',
            ]
        );

        $this->add_control(
            'completed_text',
            [
                'label' => __( 'Completed Text', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Read More',
            ]
        );

        $this->add_control(
            'user_not_have_course_text',
            [
                'label' => __( 'Not Owned Text', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Get Started',
            ]
        );
    }

    /**
     * Renders the appropriate resume text based on course progress.
     *
     * Outputs the custom text defined by the user for the course's current state:
     * - "In Progress" if the user has started but not completed the course
     * - "Not Started" if the user has access but has not started
     * - "Completed" if the user has finished the course
     * - "Not Owned" if the user does not have access
     *
     * @return void
     */
    public function render() {
        global $post;

        // Confirm that the current post is a LearnDash course
        if ( 'sfwd-courses' !== get_post_type( $post ) ) {
            echo '';
            return;
        }

        $course_id = $post->ID;
        $user_id = get_current_user_id();

        // Check if user is enrolled in the course and retrieve course progress
        if ( $user_id && sfwd_lms_has_access( $course_id, $user_id ) ) {
            $resume_step_id = learndash_user_progress_get_first_incomplete_step( $user_id, $course_id );
            $completed = learndash_course_completed( $user_id, $course_id );

            // Output text based on course completion state
            if ( $completed ) {
                echo esc_html( $this->get_settings( 'completed_text' ) );
            } elseif ( $resume_step_id ) {
                echo esc_html( $this->get_settings( 'in_progress_text' ) );
            } else {
                echo esc_html( $this->get_settings( 'not_started_text' ) );
            }
        } else {
            // Output "Not Owned" text if user is not enrolled
            echo esc_html( $this->get_settings( 'user_not_have_course_text' ) );
        }
    }
}
