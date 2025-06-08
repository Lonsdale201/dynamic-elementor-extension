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
 * CourseMaterials Elementor Dynamic Tag
 *
 * Displays the course materials in Elementor with WYSIWYG formatting.
 */
class CourseMaterials extends Tag {

    /**
     * Returns the unique name of the tag.
     *
     * @return string
     */
    public function get_name() {
        return 'course-materials';
    }

    /**
     * Returns the title of the tag for display in the Elementor panel.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Course Materials', 'hw-ele-woo-dynamic' );
    }

    /**
     * Returns the group this tag belongs to.
     *
     * @return string
     */
    public function get_group() {
        return 'ld_extras_courses';
    }

    /**
     * Returns the categories for this tag.
     *
     * Registers the tag in the text category.
     *
     * @return array
     */
    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    /**
     * Registers controls for the visibility setting.
     *
     * Adds a control to manage content visibility based on user enrollment.
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
     * Renders the course materials content, retaining WYSIWYG formatting.
     *
     * Checks user access based on visibility setting before outputting materials.
     *
     * @return void
     */
    public function render() {
        global $post;

        // Exit if the post type is not a course.
        if ('sfwd-courses' !== get_post_type($post)) {
            echo '';
            return;
        }

        $course_id = $post->ID;
        $user_id = get_current_user_id();

        // Retrieve user-defined settings.
        $settings = $this->get_settings();
        $visibility = $settings['visibility'];

        // Check user access based on visibility setting.
        if ('enrolled' === $visibility && !LDQuery::user_has_access($course_id)) {
            echo '';
            return;
        }

        // Retrieve course materials content.
        $materials = learndash_get_setting($course_id, 'course_materials');

        // Output course materials if available.
        if (!empty($materials)) {
            echo wp_kses_post($materials);
        }
    }
}
