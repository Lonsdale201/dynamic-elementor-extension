<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use HelloWP\HWEleWooDynamic\Modules\Helpers\LDQuery;

/**
 * CoursePrice Elementor Dynamic Tag
 *
 * Displays the price of a LearnDash course in Elementor.
 */
class CoursePrice extends Tag {

    /**
     * Returns the unique identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'course-price';
    }

    /**
     * Returns the tag's title displayed in the Elementor editor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Course Price', 'hw-ele-woo-dynamic' );
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
     * Registers controls for this tag in the Elementor editor.
     *
     * Provides options for defining text to display if the course is free,
     * enrolled, or if the price should only show under certain conditions.
     *
     * @return void
     */
    protected function _register_controls() {
        $this->add_control(
            'free_text',
            [
                'label' => __( 'Free Text', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Free', 'hw-ele-woo-dynamic' ),
            ]
        );

        $this->add_control(
            'enrolled_text',
            [
                'label' => __( 'Enrolled Text', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'You have this course', 'hw-ele-woo-dynamic' ),
            ]
        );

        $this->add_control(
            'price_type_visibility',
            [
                'label' => __( 'Price Type Visibility', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'show_all',
                'options' => [
                    'show_all' => __( 'Show All Types', 'hw-ele-woo-dynamic' ),
                    'show_if_has_price' => __( 'Show Only if Has Price', 'hw-ele-woo-dynamic' ),
                ],
            ]
        );

        $this->add_control(
            'visibility',
            [
                'label' => __( 'Visibility', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::SELECT,
                'options' => LDQuery::get_visibility_options(),
                'default' => 'everyone',
            ]
        );
    }

    /**
     * Renders the course price or a custom text based on the user's enrollment status and price settings.
     *
     * Checks the course's price details and displays either the price, custom text for enrolled users,
     * or free text as applicable.
     *
     * @return void
     */
    public function render() {
        global $post;

        $course_id = $post->ID;
        $user_id = get_current_user_id();
        $settings = $this->get_settings();
        $visibility = $settings['visibility'];

        // Check if user access meets the visibility setting
        if ('enrolled' === $visibility && !LDQuery::user_has_access($course_id)) {
            echo '';
            return;
        }

        // Display enrolled text if the user has access
        if ( sfwd_lms_has_access( $course_id, $user_id ) ) {
            echo esc_html( $settings['enrolled_text'] );
            return;
        }

        // Retrieve course price details
        $course_price_details = learndash_get_course_price( $course_id, $user_id );
        $price_visibility = $settings['price_type_visibility'];
        $free_text = $settings['free_text'];

        if ( is_array( $course_price_details ) ) {
            $price = $course_price_details['price'];
            $currency_code = isset( $course_price_details['currency'] ) ? $course_price_details['currency'] : '';
            $formatted_price = learndash_get_price_formatted( $price, $currency_code );

            // Check price visibility conditions
            if ( 'show_if_has_price' === $price_visibility ) {
                if ( 'open' === $course_price_details['type'] || 'free' === $course_price_details['type'] ) {
                    echo esc_html( $free_text );
                    return;
                } elseif ( empty( $formatted_price ) ) {
                    echo '';
                    return;
                }
            }

            // Display free text for open or free courses; otherwise, show the formatted price
            if ( 'open' === $course_price_details['type'] || 'free' === $course_price_details['type'] ) {
                echo esc_html( $free_text );
            } elseif ( ! empty( $formatted_price ) ) {
                echo esc_html( $formatted_price );
            }
        } else {
            echo '';
        }
    }
}
