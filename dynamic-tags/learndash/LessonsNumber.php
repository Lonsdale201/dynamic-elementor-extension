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
 * Displays the number of lessons (and optionally topics) 
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
        return [ Module::TEXT_CATEGORY ];
    }

    /**
     * Registers controls for the tag in the Elementor editor.
     *
     * Adds options for visibility control, output formatting,
     * and a switcher to include Topics in the count.
     *
     * @return void
     */
    protected function _register_controls() {
        $this->add_control(
            'visibility',
            [
                'label'   => __( 'Visibility', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => LDQuery::get_visibility_options(),
                'default' => 'everyone',
            ]
        );

        $this->add_control(
            'include_topics',
            [
                'label'        => __( 'Include Topics', 'hw-ele-woo-dynamic' ),
                'type'         => Controls_Manager::SWITCHER,
                'description'  => __( 'If enabled, topics count will be added to the lesson count.', 'hw-ele-woo-dynamic' ),
                'label_on'     => __( 'Yes', 'hw-ele-woo-dynamic' ),
                'label_off'    => __( 'No', 'hw-ele-woo-dynamic' ),
                'return_value' => 'yes',
                'default'      => '',
            ]
        );

        $this->add_control(
            'formatting',
            [
                'label'   => __( 'Formatting', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'plain'            => __( 'Plain Number', 'hw-ele-woo-dynamic' ),
                    'formatted'        => __( 'Formatted Number (Completed/Total)', 'hw-ele-woo-dynamic' ),
                    'completed_lessons'=> __( 'Completed Lessons (Topics) Only', 'hw-ele-woo-dynamic' ),
                ],
                'default' => 'plain',
            ]
        );
    }

    /**
     * Renders the lesson count or completion status based on user settings.
     *
     * Checks visibility and formatting settings to output:
     * - total lessons (and topics if enabled),
     * - completed lessons (and topics),
     * - or a formatted "completed/total" style.
     *
     * @return void
     */
    public function render() {
        $course_id = LDQuery::get_course_id();

        if ( ! $course_id ) {
            echo '';
            return;
        }

        $settings         = $this->get_settings();
        $visibility       = $settings['visibility'];
        $formatting       = $settings['formatting'];
        $include_topics   = $settings['include_topics'];

        if ( 'enrolled' === $visibility && ! LDQuery::user_has_access( $course_id ) ) {
            echo '';
            return;
        }

        $total_lessons    = LDQuery::get_lessons_count( $course_id );
        $completed_lessons= LDQuery::get_completed_lessons_count( $course_id );

        if ( 'yes' === $include_topics ) {
            $topics_count          = LDQuery::get_topics_count( $course_id );
            $completed_topics_count= LDQuery::get_completed_topics_count( $course_id );

            $total_lessons        += $topics_count;
            $completed_lessons    += $completed_topics_count;
        }

        switch ( $formatting ) {
            case 'formatted':
                $output = $completed_lessons . '/' . $total_lessons;
                break;

            case 'completed_lessons':
                $output = $completed_lessons;
                break;

            case 'plain':
            default:
                $output = $total_lessons;
                break;
        }

        echo esc_html( $output );
    }
}
