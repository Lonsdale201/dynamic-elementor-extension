<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

/**
 * CourseGroups Elementor Dynamic Tag
 *
 * Displays the names of LearnDash groups the current course is part of.
 */
class CoursePartOfGroup extends Tag {

    /**
     * Returns the unique identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'course-groups';
    }

    /**
     * Returns the title of the tag displayed in Elementor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Course Part of Groups', 'hw-ele-woo-dynamic' );
    }

    /**
     * Returns the group ID for this tag in Elementor.
     *
     * @return string
     */
    public function get_group() {
        return 'ld_extras_courses';
    }

    /**
     * Returns the categories this tag belongs to in Elementor.
     *
     * @return array
     */
    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    /**
     * Registers controls for the tag in the Elementor editor.
     */
    protected function _register_controls() {
        $this->add_control(
            'output_type',
            [
                'label' => __( 'Output', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'all' => __( 'All Groups', 'hw-ele-woo-dynamic' ),
                    'latest' => __( 'Latest', 'hw-ele-woo-dynamic' ),
                ],
                'default' => 'all',
            ]
        );

        $this->add_control(
            'linkable',
            [
                'label' => __( 'Linkable', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'hw-ele-woo-dynamic' ),
                'label_off' => __( 'No', 'hw-ele-woo-dynamic' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'delimiter',
            [
                'label' => __( 'Delimiter', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::TEXT,
                'default' => ', ',
                'description' => __( 'Set a delimiter for multiple group names. Leave empty for no delimiter.', 'hw-ele-woo-dynamic' ),
                'condition' => [
                    'output_type' => 'all',
                ],
            ]
        );
    }

    /**
     * Renders the names of groups the current course is part of.
     */
    public function render() {
        global $post;

        // Ensure the post type is 'sfwd-courses' (LearnDash course)
        if ( 'sfwd-courses' !== get_post_type( $post ) ) {
            echo '';
            return;
        }

        $course_id = $post->ID;
        $settings = $this->get_settings();
        $output_type = $settings['output_type'];
        $linkable = $settings['linkable'] === 'yes';
        $delimiter = isset( $settings['delimiter'] ) ? $settings['delimiter'] : ', ';

        // Retrieve groups associated with the course
        $course_groups = learndash_get_course_groups( $course_id );

        // Check if there are any groups associated with the course
        if ( ! empty( $course_groups ) && is_array( $course_groups ) ) {
            $group_names = array_map( function( $group_id ) use ( $linkable ) {
                $group = get_post( $group_id );
                if ( $group ) {
                    $group_name = $group->post_title;
                    if ( $linkable ) {
                        return '<a href="' . esc_url( get_permalink( $group_id ) ) . '">' . esc_html( $group_name ) . '</a>';
                    } else {
                        return esc_html( $group_name );
                    }
                }
                return '';
            }, $course_groups );

            // Filter out empty group names
            $group_names = array_filter( $group_names );

            if ( 'latest' === $output_type ) {
                // Display the latest group name
                $latest_group = end( $group_names );
                echo $latest_group; // Already escaped in the map function
            } else {
                // Display all group names, separated by the chosen delimiter
                if ( ! empty( $group_names ) ) {
                    echo implode( $delimiter, $group_names ); // Already escaped in the map function
                } else {
                    echo '';
                }
            }
        } else {
            echo ''; // No groups found
        }
    }
}
