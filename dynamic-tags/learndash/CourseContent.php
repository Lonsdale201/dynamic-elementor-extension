<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use HelloWP\HWEleWooDynamic\Modules\Helpers\LDQuery;

class CourseContent extends Tag {

    public function get_name() {
        return 'course-content';
    }

    public function get_title() {
        return __( 'Course Content', 'hw-ele-woo-dynamic' );
    }

    public function get_group() {
        return 'ld_extras_courses';
    }

    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

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
            'output_format',
            [
                'label' => __( 'Output Format', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'default' => __( 'Default', 'hw-ele-woo-dynamic' ),
                    'filtered' => __( 'Filtered', 'hw-ele-woo-dynamic' ),
                ],
                'default' => 'default',
            ]
        );

        $this->add_control(
            'text_trim',
            [
                'label' => __( 'Text Words Trim', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'trim_count',
            [
                'label' => __( 'Number of Words', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 50,
                'condition' => [
                    'text_trim' => 'yes',
                ],
            ]
        );
    }

    public function render() {
        global $post;

        if ('sfwd-courses' !== get_post_type($post)) {
            echo '';
            return;
        }

        $course_id = $post->ID;
        $user_id = get_current_user_id();

        $settings = $this->get_settings();
        $visibility = $settings['visibility'];
        $output_format = $settings['output_format'];
        $text_trim = $settings['text_trim'];
        $trim_count = !empty($settings['trim_count']) ? intval($settings['trim_count']) : 50;

        if ('enrolled' === $visibility && !LDQuery::user_has_access($course_id)) {
            echo '';
            return;
        }

        // Retrieve the raw content of the course
        $content = get_post_field('post_content', $course_id);

        if ('filtered' === $output_format) {
            $content = strip_tags($content, '<p><span><br>'); 
            $content = '<p>' . wp_strip_all_tags($content, true) . '</p>'; 
        }

        if ($text_trim === 'yes' && $trim_count > 0) {
            $content = wp_trim_words($content, $trim_count, '...');
        }

        // Output the content
        if (!empty($content)) {
            echo wp_kses_post($content);
        }
    }
}
