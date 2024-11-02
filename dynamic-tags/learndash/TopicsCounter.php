<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

/**
 * TopicsCounter Elementor Dynamic Tag
 *
 * Displays the total number of topics in all lessons within a specific course in Elementor.
 */
class TopicsCounter extends Tag {

    /**
     * Returns the unique identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'topics-counter';
    }

    /**
     * Returns the tag's title displayed in the Elementor editor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Topics Numbers', 'hw-ele-woo-dynamic' );
    }

    /**
     * Returns the group ID for this tag.
     *
     * @return string
     */
    public function get_group() {
        return 'ld_extras_courses';
    }

    /**
     * Returns the categories this tag belongs to.
     *
     * @return array
     */
    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    /**
     * Register controls for this tag in the Elementor editor.
     *
     * Note: No additional controls are required for this tag.
     *
     * @return void
     */
    protected function _register_controls() {
        // No controls needed for this tag
    }

    /**
     * Renders the total count of topics within the course's lessons.
     *
     * Iterates over each lesson in the course, counts the associated topics,
     * and outputs the total count. If there are no topics, outputs an empty string,
     * allowing Elementor to handle any fallback.
     *
     * @return void
     */
    public function render() {
        global $post;

        // Check if the post type is a LearnDash course; otherwise, do not display any output
        if ( 'sfwd-courses' !== get_post_type( $post ) ) {
            echo '';
            return;
        }

        $course_id = $post->ID;
        $lessons = learndash_get_lesson_list( $course_id );
        $topic_count = 0;

        // Iterate over each lesson and count topics associated with it
        foreach ( $lessons as $lesson ) {
            $topics = learndash_topic_dots( $lesson->ID, false, 'array' );
            if ( is_array( $topics ) ) {
                $topic_count += count( $topics );
            }
        }

        // Display the total topic count if greater than zero
        if ( $topic_count > 0 ) {
            echo esc_html( $topic_count );
        }
        // If there are no topics, no output is rendered and Elementor manages the fallback
    }
}
