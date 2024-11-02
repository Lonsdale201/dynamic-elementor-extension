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
 * LessonsNumber Dynamic Tag
 *
 * Displays the number of lessons within a LearnDash course on Elementor.
 */
class LessonsNumber extends Tag {

    /**
     * Returns the unique identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'lessons-number';
    }

    /**
     * Returns the title of the tag displayed in Elementor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Lessons Number', 'hw-ele-woo-dynamic' );
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
        return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
    }

    /**
     * Registers controls for the tag in the Elementor editor.
     *
     * Adds options for visibility control and output formatting.
     *
     * @return void
     */
    protected function _register_controls() {
        $this->add_control(
            'visibility',
            [
                'label' => __( 'Visibility', 'hw-ele-woo-dynamic' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => LDQuery::get_visibility_options(), 
                'default' => 'everyone',
            ]
        );

        $this->add_control(
            'formatting',
            [
                'label' => __( 'Formatting', 'hw-ele-woo-dynamic' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'plain' => __( 'Plain Number', 'hw-ele-woo-dynamic' ),
                    'formatted' => __( 'Formatted Number', 'hw-ele-woo-dynamic' ),
                    'completed_lessons' => __( 'Completed Lessons Number', 'hw-ele-woo-dynamic' ),
                ],
                'default' => 'plain',
            ]
        );
    }

    /**
     * Renders the lesson count or completion status based on user settings.
     *
     * Checks visibility and formatting settings to output total lessons,
     * completed lessons, or both in a formatted string.
     *
     * @return void
     */
    public function render() {
        $course_id = LDQuery::get_course_id();

        // Exit if course ID is not valid
        if (!$course_id) {
            echo '';
            return;
        }

        // Retrieve control settings
        $settings = $this->get_settings();
        $visibility = $settings['visibility'];
        $formatting = $settings['formatting'];

        // Verify user access based on visibility setting
        if ('enrolled' === $visibility && !LDQuery::user_has_access($course_id)) {
            echo '';
            return;
        }

        // Retrieve lesson counts: total and completed
        $total_lessons = LDQuery::get_lessons_count($course_id);
        $completed_lessons = LDQuery::get_completed_lessons_count($course_id);

        // Output based on formatting option selected
        if ('formatted' === $formatting) {
            // Displays completed/total lessons (e.g., "3/10")
            $output = $completed_lessons . '/' . $total_lessons;
        } elseif ('completed_lessons' === $formatting) {
            // Displays only the completed lessons count
            $output = $completed_lessons;
        } else {
            // Displays only the total lessons count
            $output = $total_lessons;
        }

        // Output the final result
        echo esc_html($output);
    }
}
