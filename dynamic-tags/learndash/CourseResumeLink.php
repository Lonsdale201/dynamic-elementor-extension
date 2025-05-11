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
        return __( 'Course Resume URL', 'hw-elementor-woo-dynamic' );
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
        $user_id   = get_current_user_id();
    
        // Check if the user has access to the course
        if ( $user_id && sfwd_lms_has_access( $course_id, $user_id ) ) {
    
            // Lekérjük a felhasználó haladását tömb formájában
            $progress = learndash_course_progress( array(
                'user_id'   => $user_id,
                'course_id' => $course_id,
                'array'     => true,
            ) );
    
            // Biztonsági ellenőrzés, hogy van-e completed/total kulcs a tömbben
            if ( isset( $progress['completed'], $progress['total'] ) ) {
    
                // Ha 0 a completed, akkor még nem kezdte el a kurzust.
                if ( 0 === (int) $progress['completed'] ) {
                    echo esc_url( get_permalink( $course_id ) );
                    return;
                }
    
                // Ha teljes mértékben befejezte (completed == total)
                if ( (int) $progress['completed'] === (int) $progress['total'] ) {
                    echo esc_url( get_permalink( $course_id ) );
                    return;
                }
    
                // Ha részben haladt, de még nem fejezte be => resume
                $resume_step_id = learndash_user_progress_get_first_incomplete_step( $user_id, $course_id );
    
                if ( $resume_step_id ) {
                    $resume_step_id = learndash_user_progress_get_parent_incomplete_step( $user_id, $course_id, $resume_step_id );
                    echo esc_url( get_permalink( $resume_step_id ) );
                } else {
                    // Ha valamiért nincs incomplete step, mehet a fő oldalra
                    echo esc_url( get_permalink( $course_id ) );
                }
    
            } else {
                // Ha valamiért a progress nem adott vissza usable adatokat, fallback a fő URL-re
                echo esc_url( get_permalink( $course_id ) );
            }
        } else {
            // Non-enrolled user => fő kurzus URL
            echo esc_url( get_permalink( $course_id ) );
        }
    }
    
}
