<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use HelloWP\HWEleWooDynamic\Modules\Helpers\LDQuery;

/**
 * CourseStatus Elementor Dynamic Tag
 *
 * Displays the user's course status in Elementor.
 */
class CourseStatus extends Tag {

    /**
     * Returns the unique name identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'user-course-status';
    }

    /**
     * Returns the title of the tag displayed in Elementor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Course Status', 'hw-elementor-woo-dynamic' );
    }

    /**
     * Returns the group identifier for this tag in Elementor.
     *
     * @return string
     */
    public function get_group() {
        return 'ld_extras_courses';
    }

    /**
     * Returns the categories for this tag in Elementor.
     *
     * @return array
     */
    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    /**
     * Registers controls for the tag in the Elementor editor.
     *
     * Controls include options for using custom text for course statuses
     * such as "Not Enrolled," "In Progress," and "Completed."
     *
     * @return void
     */
    protected function _register_controls() {
        // Custom text switcher control
        $this->add_control(
            'use_custom_text',
            [
                'label' => __( 'Custom Text', 'hw-elementor-woo-dynamic' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'hw-elementor-woo-dynamic' ),
                'label_off' => __( 'No', 'hw-elementor-woo-dynamic' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        // Custom text for "Not Enrolled" status
        $this->add_control(
            'custom_text_not_enrolled',
            [
                'label' => __( 'Not Enrolled', 'hw-elementor-woo-dynamic' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Not Enrolled', 'learndash' ),
                'condition' => [
                    'use_custom_text' => 'yes',
                ],
            ]
        );

        // Custom text for "In Progress" status
        $this->add_control(
            'custom_text_in_progress',
            [
                'label' => __( 'In Progress', 'hw-elementor-woo-dynamic' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'In Progress', 'learndash' ),
                'condition' => [
                    'use_custom_text' => 'yes',
                ],
            ]
        );

        // Custom text for "Completed" status
        $this->add_control(
            'custom_text_completed',
            [
                'label' => __( 'Completed', 'hw-elementor-woo-dynamic' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Completed', 'learndash' ),
                'condition' => [
                    'use_custom_text' => 'yes',
                ],
            ]
        );
    }

    /**
     * Renders the user's course status.
     *
     * This method checks if the user is enrolled in the course, retrieves
     * the course status, and displays it according to either default text or custom text.
     *
     * @return void
     */
    public function render() {
        $current_user = wp_get_current_user();

        // Return early if the user is not logged in
        if ( ! $current_user->exists() ) {
            echo '';
            return;
        }

        global $post;
        // Return early if the post type is not a LearnDash course
        if ( 'sfwd-courses' !== get_post_type( $post ) ) {
            echo '';
            return;
        }

        $course_id = $post->ID;
        $user_id = $current_user->ID;

        // Retrieve settings for custom text
        $settings = $this->get_settings();
        $use_custom_text = $settings['use_custom_text'] === 'yes';

        // Define default status texts
        $default_texts = [
            'not-enrolled' => __( 'Not Enrolled', 'learndash' ),
            'in-progress'  => __( 'In Progress', 'learndash' ),
            'completed'    => __( 'Completed', 'learndash' ),
        ];

        // Define custom status texts if custom text option is enabled
        $custom_texts = [
            'not-enrolled' => $settings['custom_text_not_enrolled'] ?? $default_texts['not-enrolled'],
            'in-progress'  => $settings['custom_text_in_progress'] ?? $default_texts['in-progress'],
            'completed'    => $settings['custom_text_completed'] ?? $default_texts['completed'],
        ];

        // Determine the course status key based on enrollment
        $status_key = 'not-enrolled';
        if ( LDQuery::user_has_access( $course_id ) ) {
            $raw_status = learndash_course_status( $course_id, $user_id );

            // Set status key based on raw status text
            if ( stripos( $raw_status, 'in progress' ) !== false ) {
                $status_key = 'in-progress';
            } elseif ( stripos( $raw_status, 'completed' ) !== false ) {
                $status_key = 'completed';
            }
        }

        // Select appropriate text based on custom or default setting
        $status = $use_custom_text ? $custom_texts[$status_key] : learndash_course_status( $course_id, $user_id );

        // Output the course status
        echo esc_html( $status );
    }
}
