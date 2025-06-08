<?php
namespace HelloWP\HWEleWooDynamic\Modules\Finder;

use Elementor\Core\Common\Modules\Finder\Base_Category;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class LearnDashMethods extends Base_Category {
    
    public function get_id() {
        return 'learndash-methods';
    }

    public function get_title() {
        return esc_html__('LearnDash Methods', 'hw-ele-woo-dynamic');
    }

    public function get_category_items(array $options = []) {
        return [
            'new-course' => [
                'title' => esc_html__('New Course', 'hw-ele-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('post-new.php?post_type=sfwd-courses'),
                'keywords' => [
                    __('course', 'hw-ele-woo-dynamic'), 
                    __('learndash', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic')
                ]
            ],
            'new-lesson' => [
                'title' => esc_html__('New Lesson', 'hw-ele-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('post-new.php?post_type=sfwd-lessons'),
                'keywords' => [
                    __('lesson', 'hw-ele-woo-dynamic'), 
                    __('learndash', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic')
                ]
            ],
            'new-topic' => [
                'title' => esc_html__('New Topic', 'hw-ele-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('post-new.php?post_type=sfwd-topic'),
                'keywords' => [
                    __('topic', 'hw-ele-woo-dynamic'), 
                    __('learndash', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic')
                ]
            ],
            'new-quiz' => [
                'title' => esc_html__('New Quiz', 'hw-ele-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('post-new.php?post_type=sfwd-quiz'),
                'keywords' => [
                    __('quiz', 'hw-ele-woo-dynamic'), 
                    __('learndash', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic')
                ]
            ],
            'new-group' => [
                'title' => esc_html__('New Group', 'hw-ele-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('post-new.php?post_type=groups'),
                'keywords' => [
                    __('group', 'hw-ele-woo-dynamic'), 
                    __('learndash', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic')
                ]
            ],
            'submitted-essays' => [
                'title' => esc_html__('Submitted Essays', 'hw-ele-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('edit.php?post_type=sfwd-essays'),
                'keywords' => [
                    __('essays', 'hw-ele-woo-dynamic'), 
                    __('learndash', 'hw-ele-woo-dynamic'), 
                    __('submitted', 'hw-ele-woo-dynamic')
                ]
            ],
        ];
    }
}
