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
 * StudentLimit Elementor Dynamic Tag
 *
 * Displays the maximum number of students allowed in a course along with the remaining seats available.
 */
class StudentLimit extends Tag {

    /**
     * Returns the unique identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'student-limit';
    }

    /**
     * Returns the tag's title displayed in the Elementor editor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Student Limit', 'hw-ele-woo-dynamic' );
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
     * Registers controls in the Elementor editor for the student limit tag.
     *
     * Provides options for visibility and output format (plain or custom).
     *
     * @return void
     */
    protected function _register_controls() {
        $this->add_control(
            'visibility',
            [
                'label' => __( 'Visibility', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::SELECT,
                'options' => LDQuery::get_visibility_options(),
                'default' => 'everyone',
            ]
        );

        $this->add_control(
            'format',
            [
                'label' => __( 'Returned Count', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'plain' => __( 'Plain Count', 'hw-ele-woo-dynamic' ),
                    'custom' => __( 'Custom Format', 'hw-ele-woo-dynamic' ),
                ],
                'default' => 'plain',
            ]
        );

        $this->add_control(
            'custom_format',
            [
                'label' => __( 'Custom Format', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'Limit: %current_seat%, Remaining: %remaining_seat%',
                'condition' => [
                    'format' => 'custom',
                ],
                'description' => __( 'Use %current_seat% for total seats and %remaining_seat% for remaining seats.', 'hw-ele-woo-dynamic' ),
            ]
        );
    }

    /**
     * Renders the course's maximum seat count or remaining seats based on settings.
     *
     * Displays either a plain count or a formatted string.
     *
     * @return void
     */
    public function render() {
        global $post;

        // Check if post type is 'sfwd-courses' (LearnDash course)
        if ('sfwd-courses' !== get_post_type($post)) {
            echo '';
            return;
        }

        $course_id = $post->ID;
        $settings = $this->get_settings();
        $visibility = $settings['visibility'];

        // Check visibility setting and user access
        if ('enrolled' === $visibility && !LDQuery::user_has_access($course_id)) {
            echo '';
            return;
        }

        $seat_limit = $this->get_seat_limit($course_id);

        if (is_numeric($seat_limit)) {
            $enrolled_users_count = $this->get_enrolled_users_count($course_id);
            $remaining_seats = max(0, $seat_limit - $enrolled_users_count);

            // Output based on the chosen format
            if ('custom' === $settings['format']) {
                $custom_format = $settings['custom_format'];
                $formatted_output = str_replace('%current_seat%', $seat_limit, $custom_format);
                $formatted_output = str_replace('%remaining_seat%', $remaining_seats, $formatted_output);
                echo wp_kses_post($formatted_output);
            } else {
                echo esc_html($seat_limit);
            }
        }
    }

    /**
     * Retrieves the maximum seat limit for a course.
     *
     * Checks post meta for seat limit, returning the integer value if set and valid.
     *
     * @param int $course_id Course ID to get seat limit for.
     * @return int|string The seat limit or an empty string if not set.
     */
    private function get_seat_limit($course_id) {
        $course_meta = get_post_meta($course_id, '_sfwd-courses', true);
        if (isset($course_meta['sfwd-courses_course_seats_limit']) && is_numeric($course_meta['sfwd-courses_course_seats_limit']) && $course_meta['sfwd-courses_course_seats_limit'] > 0) {
            return (int)$course_meta['sfwd-courses_course_seats_limit'];
        }
        return '';
    }

    /**
     * Retrieves the count of enrolled users for a given course.
     *
     * Uses WP_User_Query to count users with course enrollment metadata.
     *
     * @param int $course_id Course ID to count enrolled users for.
     * @return int The number of enrolled users.
     */
    private function get_enrolled_users_count($course_id) {
        $user_query_args = [
            'meta_query' => [
                [
                    'key'     => 'course_' . $course_id . '_access_from',
                    'compare' => 'EXISTS',
                ],
            ],
        ];
        $user_query = new \WP_User_Query($user_query_args);
        return $user_query->get_total();
    }
}
