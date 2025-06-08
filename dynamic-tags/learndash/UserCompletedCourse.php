<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

/**
 * UserCompletedCourse Elementor Dynamic Tag
 *
 * Displays the count of completed courses for the logged-in user.
 */
class UserCompletedCourse extends Tag {

    public function get_name() {
        return 'user-completed-courses-count';
    }

    public function get_title() {
        return __( 'User Completed Courses Count', 'hw-ele-woo-dynamic' );
    }

    public function get_group() {
        return 'ld_extras_global';
    }

    public function get_categories() {
        return [ Module::TEXT_CATEGORY, Module::NUMBER_CATEGORY ];
    }

    /**
     * Register controls for Elementor editor.
     */
    protected function _register_controls() {
        $this->add_control(
            'no_courses_text',
            [
                'label' => __( 'Text if No Courses', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => __( 'Text to display if user has no completed courses', 'hw-ele-woo-dynamic' ),
            ]
        );
    }

    /**
     * Renders the count of completed courses or a predefined text if none are completed.
     */
    public function render() {
        // Check if the user is logged in; output an empty value if not.
        if ( ! is_user_logged_in() ) {
            echo '';
            return;
        }
    
        $user_id = get_current_user_id();
        $course_info = get_user_meta( $user_id, '_sfwd-course_progress', true );
        $completed_courses_count = 0;
    
        // Loop through course information to count only completed courses that still exist.
        if ( ! empty( $course_info ) ) {
            foreach ( $course_info as $course_id => $progress ) {
                // Check if the course post exists and is not permanently deleted, and is marked as completed.
                if ( get_post_status( $course_id ) && ! empty( $progress['completed'] ) && intval( $progress['completed'] ) > 0 ) {
                    $completed_courses_count++;
                }
            }
        }
    
        // Output the completed courses count if any; otherwise, display the custom text for no completed courses.
        if ( $completed_courses_count > 0 ) {
            echo esc_html( $completed_courses_count );
        } else {
            echo esc_html( $this->get_settings( 'no_courses_text' ) );
        }
    }
    
}
