<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;

/**
 * CoursePrerequisitesList Elementor Dynamic Tag
 *
 * Displays a list of prerequisites for a course in Elementor.
 */
class CoursePrerequisitesList extends Tag {

    /**
     * Returns the unique identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'course-prerequisites-list';
    }

    /**
     * Returns the tag's title displayed in the Elementor editor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Course Prerequisites List', 'hw-ele-woo-dynamic' );
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
     * Registers the controls for Elementor.
     *
     * Adds controls for list format (inline or list) and linkability.
     *
     * @return void
     */
    protected function _register_controls() {
        $this->add_control(
            'list_format',
            [
                'label' => __( 'List Format', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'list' => __( 'List', 'hw-ele-woo-dynamic' ),
                    'inline' => __( 'Inline', 'hw-ele-woo-dynamic' ),
                ],
                'default' => 'list',
            ]
        );

        $this->add_control(
            'linkable',
            [
                'label' => __( 'Linkable', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __( 'Yes', 'hw-ele-woo-dynamic' ),
                'label_off' => __( 'No', 'hw-ele-woo-dynamic' ),
                'return_value' => 'yes',
            ]
        );
    }

    /**
     * Renders the list of course prerequisites.
     *
     * Checks if prerequisites exist for the course. Displays them either as a list or inline,
     * with optional links if the "linkable" setting is enabled.
     *
     * @return void
     */
    public function render() {
        global $post;

        // Verify the post type is a LearnDash course
        if ( 'sfwd-courses' !== get_post_type( $post ) ) {
            return;
        }

        $course_id = $post->ID;
        $prerequisites = learndash_get_course_prerequisite( $course_id );
        $settings = $this->get_settings();

        // Render the prerequisites if they exist
        if ( is_array( $prerequisites ) && ! empty( $prerequisites ) ) {
            if ( 'inline' === $settings['list_format'] ) {
                // Render inline format
                $titles = [];
                foreach ( $prerequisites as $prerequisite_id ) {
                    if ( get_post_type( $prerequisite_id ) === 'sfwd-courses' ) {
                        $prerequisite_title = get_the_title( $prerequisite_id );
                        $prerequisite_link = get_permalink( $prerequisite_id );

                        // Format with or without link based on settings
                        $titles[] = ( 'yes' === $settings['linkable'] ) ?
                            '<a href="' . esc_url( $prerequisite_link ) . '" target="_blank">' . esc_html( $prerequisite_title ) . '</a>' :
                            esc_html( $prerequisite_title );
                    }
                }
                echo implode( ', ', $titles );
            } else {
                // Render list format
                echo '<ul>';
                foreach ( $prerequisites as $prerequisite_id ) {
                    if ( get_post_type( $prerequisite_id ) === 'sfwd-courses' ) {
                        $prerequisite_title = get_the_title( $prerequisite_id );
                        $prerequisite_link = get_permalink( $prerequisite_id );

                        echo '<li>' . ( 'yes' === $settings['linkable'] ?
                            '<a href="' . esc_url( $prerequisite_link ) . '" target="_blank">' . esc_html( $prerequisite_title ) . '</a>' :
                            esc_html( $prerequisite_title ) ) . '</li>';
                    }
                }
                echo '</ul>';
            }
        }
    }
}
