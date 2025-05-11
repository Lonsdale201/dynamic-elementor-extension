<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use HelloWP\HWEleWooDynamic\Modules\Helpers\LDQuery;

/**
 * AccessExpires Elementor Dynamic Tag
 *
 * Displays the expiration date of a user's access to a course in Elementor.
 */
class AccessExpires extends Tag {

    /**
     * Get tag name.
     *
     * @return string
     */
    public function get_name() {
        return 'access-expires';
    }

    /**
     * Get tag title.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Access Expires', 'hw-elementor-woo-dynamic' );
    }

    /**
     * Get tag group.
     *
     * @return string
     */
    public function get_group() {
        return 'ld_extras_courses';
    }

    /**
     * Get tag categories.
     *
     * @return array
     */
    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    /**
     * Register controls for the tag.
     *
     * Adds options to specify display format and custom text templates.
     *
     * @return void
     */
    protected function _register_controls() {
        $this->add_control(
            'no_expiry_text',
            [
                'label'   => __( 'No Expiry Text', 'hw-elementor-woo-dynamic' ),
                'type'    => Controls_Manager::TEXT,
                'default' => __( 'Lifetime', 'hw-elementor-woo-dynamic' ),
            ]
        );

        $this->add_control(
            'output_format',
            [
                'label'   => __( 'Output Format', 'hw-elementor-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'plain_date'  => __( 'Plain Date', 'hw-elementor-woo-dynamic' ),
                    'custom_text' => __( 'Custom Text', 'hw-elementor-woo-dynamic' ),
                ],
                'default' => 'plain_date',
            ]
        );

        $this->add_control(
            'custom_text_template',
            [
                'label'       => __( 'Custom Text Template', 'hw-elementor-woo-dynamic' ),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => __( 'Expires on %expiry_date%, %expiry_day% days left', 'hw-elementor-woo-dynamic' ),
                'condition'   => [
                    'output_format' => 'custom_text',
                ],
            ]
        );
    }

    /**
     * Render the expiration date of the user's access to the course.
     *
     * Checks if the post type is a course and if the user has access, then displays
     * the expiration date in the selected format, or a custom message if there is no expiration.
     *
     * @return void
     */
    public function render() {
        $user_id = get_current_user_id();
        $course_id = get_the_ID();

        // Check if the post is a course and the user has access; if not, render nothing.
        if ( 'sfwd-courses' !== get_post_type( $course_id ) || ! $user_id || ! LDQuery::user_has_access( $course_id ) ) {
            echo '';
            return;
        }

        $expiry = ld_course_access_expires_on( $course_id, $user_id );
        $settings = $this->get_settings();

        // If expiration is set to 0 or not defined, display the no expiry text.
        if ( $expiry === 0 ) {
            echo wp_kses_post( $settings['no_expiry_text'] );
        } else {
            // Check for custom text format; if enabled, apply custom template.
            if ( 'custom_text' === $settings['output_format'] ) {
                $expiry_date = date_i18n( get_option( 'date_format' ), $expiry );
                $days_left = ceil( ( $expiry - current_time( 'timestamp' ) ) / DAY_IN_SECONDS );

                $custom_text = str_replace( '%expiry_date%', $expiry_date, $settings['custom_text_template'] );
                $custom_text = str_replace( '%expiry_day%', $days_left, $custom_text );

                echo wp_kses_post( $custom_text );
            } else {
                // Display the default plain date format.
                echo wp_kses_post( date_i18n( get_option( 'date_format' ), $expiry ) );
            }
        }
    }
}
