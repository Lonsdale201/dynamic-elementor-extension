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
 * AwardedPoints Elementor Dynamic Tag
 *
 * Displays the points awarded for a specific course in Elementor.
 */
class AwardedPoints extends Tag {

    /**
     * Get tag name.
     *
     * @return string
     */
    public function get_name() {
        return 'awarded-points';
    }

    /**
     * Get tag title.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Awarded on Completions', 'hw-elementor-woo-dynamic' );
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
     * Adds the tag to both text and number categories.
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
     * Register controls for visibility.
     *
     * Adds options for visibility settings based on user access.
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
     * Render the awarded points for the course.
     *
     * Displays points if the user has access to the course based on visibility setting.
     *
     * @return void
     */
    public function render() {
        $course_id = LDQuery::get_course_id();

        // Exit if no course ID is available.
        if (!$course_id) {
            echo '';
            return;
        }

        $settings = $this->get_settings();
        $visibility = $settings['visibility'];

        // Check user access based on visibility setting.
        if ('enrolled' === $visibility && !LDQuery::user_has_access($course_id)) {
            echo '';
            return;
        }

        // Retrieve and display awarded points if available.
        $awarded_points = learndash_get_setting($course_id, 'course_points');
        if (!empty($awarded_points) && $awarded_points > 0) {
            echo esc_html($awarded_points);
        } else {
            echo '';
        }
    }
}
