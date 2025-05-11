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
 * LastActivity Elementor Dynamic Tag
 *
 * Displays the user's last course-specific activity date within Elementor.
 */
class LastActivity extends Tag {

    /**
     * Returns the unique name identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'last-activity';
    }

    /**
     * Returns the title of the tag displayed in Elementor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Last Activity', 'hw-elementor-woo-dynamic' );
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
     * Includes options for selecting a date format (default or custom) 
     * and specifying a custom format if selected.
     *
     * @return void
     */
    protected function _register_controls() {
        $this->add_control(
            'date_format',
            [
                'label' => __( 'Format', 'hw-elementor-woo-dynamic' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'default' => __( 'Default', 'hw-elementor-woo-dynamic' ),
                    'custom'  => __( 'Custom', 'hw-elementor-woo-dynamic' ),
                ],
                'default' => 'default',
            ]
        );

        $this->add_control(
            'custom_date_format',
            [
                'label'       => __( 'Custom Date Format', 'hw-elementor-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'condition'   => [
                    'date_format' => 'custom',
                ],
                'placeholder' => 'Y-m-d H:i:s',
            ]
        );
    }

    /**
     * Renders the last activity date for the current user in the specified format.
     *
     * If no activity date is available, outputs an empty string.
     *
     * @return void
     */
    public function render() {
        global $post;

        // Check if post type is LearnDash course; exit if not
        if ( 'sfwd-courses' !== get_post_type( $post ) ) {
            echo '';
            return;
        }

        $course_id = $post->ID;
        $user_id   = get_current_user_id();

        // Exit if there is no valid user ID
        if ( empty( $user_id ) ) {
            echo '';
            return;
        }

        // Retrieve settings for date format options
        $settings           = $this->get_settings();
        $date_format_option = $settings['date_format'];
        $custom_date_format = $settings['custom_date_format'];

        // Fetch the last activity date using LDQuery helper
        $last_activity_date = LDQuery::get_last_activity_date( $course_id, $user_id );

        if ( $last_activity_date ) {
            // Convert timestamp to WordPress timezone
            $wp_timezone = new \DateTimeZone( wp_timezone_string() );
            $activity_date = new \DateTime( "@$last_activity_date" ); // Unix timestamp format
            $activity_date->setTimezone( $wp_timezone );

            // Set the date format based on user selection (default or custom)
            $date_format = ( 'custom' === $date_format_option && ! empty( $custom_date_format ) )
                ? $custom_date_format
                : get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

            $formatted_date = $activity_date->format( $date_format );
            echo esc_html( $formatted_date );
        } else {
            echo '';
        }
    }
}
