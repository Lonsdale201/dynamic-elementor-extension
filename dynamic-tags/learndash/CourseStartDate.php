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
 * CourseStartDate Elementor Dynamic Tag
 *
 * Displays the start date of a course in Elementor.
 */
class CourseStartDate extends Tag {

    /**
     * Returns the unique name identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'course-start-date';
    }

    /**
     * Returns the title of the tag displayed in Elementor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Course Start Date', 'hw-ele-woo-dynamic' );
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
        return [ Module::TEXT_CATEGORY, Module::DATETIME_CATEGORY ];
    }

    /**
     * Registers controls for the tag in the Elementor editor.
     *
     * Controls include visibility settings, date format, and a custom
     * message for when no start date is available.
     *
     * @return void
     */
    protected function _register_controls() {
        // Visibility control for limiting display based on user status
        $this->add_control(
            'visibility',
            [
                'label' => __( 'Visibility', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::SELECT,
                'options' => LDQuery::get_visibility_options(),
                'default' => 'everyone',
            ]
        );

        // Date format control to allow custom formatting or default
        $this->add_control(
            'date_format',
            [
                'label' => __( 'Date Format', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::TEXT,
                'default' => get_option( 'date_format' ),
                'description' => __( 'Specify the date format. Defaults to WordPress settings if left empty.', 'hw-ele-woo-dynamic' ),
            ]
        );

        // Message displayed when no start date is available
        $this->add_control(
            'no_start_date_text',
            [
                'label' => __( 'No Start Date Text', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'No start date available', 'hw-ele-woo-dynamic' ),
            ]
        );
    }

    /**
     * Renders the course start date.
     *
     * This method checks the post type, retrieves the start date for
     * the course, and displays it in the specified format. If there is no
     * start date, it displays an empty value for compatibility with countdown widgets.
     *
     * @return void
     */
    public function render() {
        global $post;

        // Check if the post type is "sfwd-courses" (LearnDash course)
        if ( 'sfwd-courses' !== get_post_type( $post ) ) {
            echo '';
            return;
        }

        $course_id = $post->ID;
        $user_id = get_current_user_id();

        // Retrieve settings from Elementor controls
        $settings = $this->get_settings();
        $visibility = $settings['visibility'];

        // Visibility check based on user enrollment
        if ( 'enrolled' === $visibility && ! LDQuery::user_has_access( $course_id ) ) {
            echo '';
            return;
        }

        // Retrieve the course start date
        $start_date_timestamp = ld_course_access_from( $course_id, $user_id );

        // Format and display the start date, or an empty value if not available
        if ( $start_date_timestamp ) {
            $date_format = $settings['date_format'] ?: get_option( 'date_format' );
            $formatted_date = date_i18n( $date_format, $start_date_timestamp );
            echo esc_html( $formatted_date );
        } else {
            echo ''; // Returns an empty value for compatibility with countdown widgets
        }
    }
}
