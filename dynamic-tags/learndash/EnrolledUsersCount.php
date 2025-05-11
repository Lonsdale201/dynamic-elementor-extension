<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use HelloWP\HWEleWooDynamic\Modules\Helpers\LDQuery;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * EnrolledUsersCount Elementor Dynamic Tag
 *
 * Displays the count of users enrolled in a course within Elementor.
 */
class EnrolledUsersCount extends Tag {

    /**
     * Returns the unique name identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'enrolled-users-count';
    }

    /**
     * Returns the title of the tag displayed in Elementor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Students Number', 'hw-elementor-woo-dynamic' );
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
        return [
            Module::TEXT_CATEGORY,
            Module::NUMBER_CATEGORY
        ];
    }

    /**
     * Registers controls for the tag in the Elementor editor.
     *
     * This includes a visibility control to specify whether only enrolled users or all users
     * can see the enrolled count.
     *
     * @return void
     */
    protected function _register_controls() {
        $this->add_control(
            'visibility',
            [
                'label' => __( 'Visibility', 'hw-elementor-woo-dynamic' ),
                'type' => Controls_Manager::SELECT,
                'options' => LDQuery::get_visibility_options(),
                'default' => 'everyone',
            ]
        );
    }

    /**
     * Renders the enrolled users count for a course.
     *
     * This method fetches the course ID, verifies visibility settings, queries the user data
     * to determine the number of enrolled users, and displays the result.
     *
     * @return void
     */
    public function render() {
        // Retrieve the current course ID from the post context
        $course_id = LDQuery::get_course_id();

        // If no course ID is found, exit early
        if (!$course_id) {
            echo '';
            return;
        }

        // Retrieve settings for visibility and determine access permissions
        $settings = $this->get_settings();
        $visibility = $settings['visibility'];

        // If visibility is set to enrolled-only, check if the user has access to the course
        if ('enrolled' === $visibility && !sfwd_lms_has_access($course_id, get_current_user_id())) {
            echo '';
            return;
        }

        // Query the users who are enrolled in the course based on course access meta key
        $user_query_args = [
            'meta_query' => [
                [
                    'key'     => 'course_' . $course_id . '_access_from',
                    'compare' => 'EXISTS'
                ]
            ]
        ];
        $user_query = new \WP_User_Query($user_query_args);
        $enrolled_users_count = $user_query->get_total();

        // Display the enrolled users count if greater than zero, otherwise output empty
        if ($enrolled_users_count > 0) {
            echo esc_html($enrolled_users_count);
        } else {
            echo ''; 
        }
    }
}
