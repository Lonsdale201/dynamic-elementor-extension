<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use HelloWP\HWEleWooDynamic\Modules\Helpers\LDQuery;

/**
 * CourseProgress Elementor Dynamic Tag
 *
 * Displays the course progress percentage in Elementor.
 */
class CourseProgress extends Tag {

    /**
     * Get the unique name identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'progress-percentage';
    }

    /**
     * Get the title for this tag as displayed in Elementor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Progress Percentage', 'hw-elementor-woo-dynamic' );
    }

    /**
     * Get the group identifier for this tag.
     *
     * @return string
     */
    public function get_group() {
        return 'ld_extras_courses';
    }

    /**
     * Get the category or categories for this tag in Elementor.
     *
     * @return array
     */
    public function get_categories() {
        return [ Module::NUMBER_CATEGORY ];
    }

    /**
     * Render the course progress percentage for the current user.
     *
     * This function retrieves the progress for the course that the user
     * is currently viewing and outputs it as a percentage.
     *
     * @return void
     */
    public function render() {
        global $post;

        // Check if the current post type is a LearnDash course.
        if ( 'sfwd-courses' !== get_post_type( $post ) ) {
            echo '0';
            return;
        }

        $user_id = get_current_user_id();
        $course_id = $post->ID;

        // Ensure both user ID and course ID are available.
        if ( empty( $user_id ) || empty( $course_id ) ) {
            echo '0';
            return;
        }

        // Retrieve course progress for the user.
        $course_progress = learndash_course_progress( [
            'user_id'   => $user_id,
            'course_id' => $course_id,
            'array'     => true
        ] );

        // Output the progress percentage if available.
        if ( ! empty( $course_progress ) && isset( $course_progress['percentage'] ) ) {
            echo intval( $course_progress['percentage'] );
        } else {
            echo '0';
        }
    }
}
