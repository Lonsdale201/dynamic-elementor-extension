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
 * QuizNumbers Dynamic Tag
 *
 * Displays the total number of quizzes associated with a LearnDash course on Elementor.
 */
class QuizNumbers extends Tag {

    /**
     * Returns the unique identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'quiz-numbers';
    }

    /**
     * Returns the title of the tag displayed in Elementor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Quiz Numbers', 'hw-ele-woo-dynamic' );
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
     * Adds an option for visibility control to determine who can view the quiz count.
     *
     * @return void
     */
    protected function _register_controls() {
        // Visibility control setting, using visibility options defined in LDQuery
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
     * Renders the total quiz count for the course if applicable.
     *
     * Checks course and user access, then fetches the quiz count.
     * Outputs the count if available and access conditions are met.
     *
     * @return void
     */
    public function render() {
        // Get the course ID from the current page/post context
        $course_id = LDQuery::get_course_id();

        // Exit if not on a course page or course ID is invalid
        if (!$course_id) {
            return;
        }

        // Retrieve visibility setting from the tag controls
        $settings = $this->get_settings();
        $visibility = $settings['visibility'];

        // Check access based on the visibility setting:
        // - "enrolled" means only users enrolled in the course can view the quiz count
        if ('enrolled' === $visibility && !LDQuery::user_has_access($course_id)) {
            return;
        }

        // Get the total quiz count associated with the course
        $quiz_count = LDQuery::count_quizzes_in_course($course_id);

        // Output the quiz count if available
        if ($quiz_count > 0) {
            echo esc_html($quiz_count);
        }
    }
}
