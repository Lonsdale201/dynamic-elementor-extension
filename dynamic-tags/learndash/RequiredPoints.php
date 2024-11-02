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
 * RequiredPoints Elementor Dynamic Tag
 *
 * Displays the required points needed to access a course in Elementor.
 */
class RequiredPoints extends Tag {

    /**
     * Returns the unique identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'required-points';
    }

    /**
     * Returns the title of the tag displayed in Elementor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Required Points for Access', 'hw-ele-woo-dynamic' );
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
     *
     * Adds a visibility control to determine who can view the required points.
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
    }

    /**
     * Renders the required points for the current course.
     *
     * Checks course type and user access, then retrieves and displays the points if available.
     *
     * @return void
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
        $visibility = $settings['visibility'];

        // Check access based on visibility setting:
        // - "enrolled" means only users enrolled in the course can view required points
        if ( 'enrolled' === $visibility && ! LDQuery::user_has_access( $course_id ) ) {
            echo '';
            return;
        }

        // Retrieve and display required points for course access
        $required_points = learndash_get_setting( $post, 'course_points_access' );

        if ( ! empty( $required_points ) && $required_points > 0 ) {
            echo esc_html( $required_points );
        }
    }
}
