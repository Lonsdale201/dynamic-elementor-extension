<?php
namespace HelloWP\HWEleWooDynamic\Modules\Finder;

use Elementor\Core\Common\Modules\Finder\Base_Category;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class LearnDashMethods extends Base_Category {
    
    public function get_id() {
        return 'learndash-methods';
    }

    public function get_title() {
        return esc_html__('LearnDash Methods', 'hw-elementor-woo-dynamic');
    }

    public function get_category_items(array $options = []) {
        return [
            'new-course' => [
                'title' => esc_html__('New Course', 'hw-elementor-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('post-new.php?post_type=sfwd-courses'),
                'keywords' => [
                    __('course', 'hw-elementor-woo-dynamic'), 
                    __('learndash', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic')
                ]
            ],
            'new-lesson' => [
                'title' => esc_html__('New Lesson', 'hw-elementor-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('post-new.php?post_type=sfwd-lessons'),
                'keywords' => [
                    __('lesson', 'hw-elementor-woo-dynamic'), 
                    __('learndash', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic')
                ]
            ],
            'new-topic' => [
                'title' => esc_html__('New Topic', 'hw-elementor-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('post-new.php?post_type=sfwd-topic'),
                'keywords' => [
                    __('topic', 'hw-elementor-woo-dynamic'), 
                    __('learndash', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic')
                ]
            ],
            'new-quiz' => [
                'title' => esc_html__('New Quiz', 'hw-elementor-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('post-new.php?post_type=sfwd-quiz'),
                'keywords' => [
                    __('quiz', 'hw-elementor-woo-dynamic'), 
                    __('learndash', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic')
                ]
            ],
            'new-group' => [
                'title' => esc_html__('New Group', 'hw-elementor-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('post-new.php?post_type=groups'),
                'keywords' => [
                    __('group', 'hw-elementor-woo-dynamic'), 
                    __('learndash', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic')
                ]
            ],
            'submitted-essays' => [
                'title' => esc_html__('Submitted Essays', 'hw-elementor-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('edit.php?post_type=sfwd-essays'),
                'keywords' => [
                    __('essays', 'hw-elementor-woo-dynamic'), 
                    __('learndash', 'hw-elementor-woo-dynamic'), 
                    __('submitted', 'hw-elementor-woo-dynamic')
                ]
            ],
        ];
    }
}
