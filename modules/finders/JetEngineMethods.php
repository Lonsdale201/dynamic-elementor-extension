<?php
namespace HelloWP\HWEleWooDynamic\Modules\Finder;

use Elementor\Core\Common\Modules\Finder\Base_Category;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class JetEngineMethods extends Base_Category {
    
    public function get_id() {
        return 'jetengine-methods';
    }

    public function get_title() {
        return esc_html__('JetEngine Methods', 'hw-ele-woo-dynamic');
    }

    public function get_category_items(array $options = []) {
        return [
            'new-query' => [
                'title' => esc_html__('New Query', 'hw-ele-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('admin.php?page=jet-engine-query&query_action=add'),
                'keywords' => [
                    __('query', 'hw-ele-woo-dynamic'), 
                    __('jetengine', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic')
                ]
            ],
            'new-cpt' => [
                'title' => esc_html__('New CPT', 'hw-ele-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('admin.php?page=jet-engine-cpt&cpt_action=add'),
                'keywords' => [
                    __('cpt', 'hw-ele-woo-dynamic'), 
                    __('custom post type', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic'), 
                    __('add new custom post type', 'hw-ele-woo-dynamic'),
                    __('new custom post type', 'hw-ele-woo-dynamic'), 
                    __('jetengine', 'hw-ele-woo-dynamic')
                ]
            ],
            'add-new-meta-boxes' => [
                'title' => esc_html__('New Meta Boxes', 'hw-ele-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('admin.php?page=jet-engine-meta&cpt_meta_action=add'),
                'keywords' => [
                    __('meta box', 'hw-ele-woo-dynamic'), 
                    __('meta', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic'), 
                    __('jetengine', 'hw-ele-woo-dynamic')
                ]
            ],
            'new-taxonomy' => [
                'title' => esc_html__('New Taxonomy', 'hw-ele-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('admin.php?page=jet-engine-cpt-tax'),
                'keywords' => [
                    __('taxonomy', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic'), 
                    __('jetengine', 'hw-ele-woo-dynamic'), 
                    __('new taxonomy', 'hw-ele-woo-dynamic')
                ]
            ],
            'shortcode-generator' => [
                'title' => esc_html__('Shortcode Generator', 'hw-ele-woo-dynamic'),
                'icon' => 'code',
                'url' => admin_url('admin.php?page=jet-engine#shortcode_generator'),
                'keywords' => [
                    __('shortcode', 'hw-ele-woo-dynamic'), 
                    __('generator', 'hw-ele-woo-dynamic'), 
                    __('jetengine', 'hw-ele-woo-dynamic')
                ]
            ],
            'macro-generator' => [
                'title' => esc_html__('Macro Generator', 'hw-ele-woo-dynamic'),
                'icon' => 'code',
                'url' => admin_url('admin.php?page=jet-engine#macros_generator'),
                'keywords' => [
                    __('macro', 'hw-ele-woo-dynamic'), 
                    __('generator', 'hw-ele-woo-dynamic'), 
                    __('jetengine', 'hw-ele-woo-dynamic')
                ]
            ],
        ];
    }
}
