<?php
namespace HelloWP\HWEleWooDynamic\Modules\Finder;

use Elementor\Core\Common\Modules\Finder\Base_Category;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class JetEngineMethods extends Base_Category {
    
    public function get_id() {
        return 'jetengine-methods';
    }

    public function get_title() {
        return esc_html__('JetEngine Methods', 'hw-elementor-woo-dynamic');
    }

    public function get_category_items(array $options = []) {
        return [
            'new-query' => [
                'title' => esc_html__('New Query', 'hw-elementor-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('admin.php?page=jet-engine-query&query_action=add'),
                'keywords' => [
                    __('query', 'hw-elementor-woo-dynamic'), 
                    __('jetengine', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic')
                ]
            ],
            'new-cpt' => [
                'title' => esc_html__('New CPT', 'hw-elementor-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('admin.php?page=jet-engine-cpt&cpt_action=add'),
                'keywords' => [
                    __('cpt', 'hw-elementor-woo-dynamic'), 
                    __('custom post type', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic'), 
                    __('add new custom post type', 'hw-elementor-woo-dynamic'),
                    __('new custom post type', 'hw-elementor-woo-dynamic'), 
                    __('jetengine', 'hw-elementor-woo-dynamic')
                ]
            ],
            'add-new-meta-boxes' => [
                'title' => esc_html__('New Meta Boxes', 'hw-elementor-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('admin.php?page=jet-engine-meta&cpt_meta_action=add'),
                'keywords' => [
                    __('meta box', 'hw-elementor-woo-dynamic'), 
                    __('meta', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic'), 
                    __('jetengine', 'hw-elementor-woo-dynamic')
                ]
            ],
            'new-taxonomy' => [
                'title' => esc_html__('New Taxonomy', 'hw-elementor-woo-dynamic'),
                'icon' => 'plus',
                'url' => admin_url('admin.php?page=jet-engine-cpt-tax'),
                'keywords' => [
                    __('taxonomy', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic'), 
                    __('jetengine', 'hw-elementor-woo-dynamic'), 
                    __('new taxonomy', 'hw-elementor-woo-dynamic')
                ]
            ],
            'shortcode-generator' => [
                'title' => esc_html__('Shortcode Generator', 'hw-elementor-woo-dynamic'),
                'icon' => 'code',
                'url' => admin_url('admin.php?page=jet-engine#shortcode_generator'),
                'keywords' => [
                    __('shortcode', 'hw-elementor-woo-dynamic'), 
                    __('generator', 'hw-elementor-woo-dynamic'), 
                    __('jetengine', 'hw-elementor-woo-dynamic')
                ]
            ],
            'macro-generator' => [
                'title' => esc_html__('Macro Generator', 'hw-elementor-woo-dynamic'),
                'icon' => 'code',
                'url' => admin_url('admin.php?page=jet-engine#macros_generator'),
                'keywords' => [
                    __('macro', 'hw-elementor-woo-dynamic'), 
                    __('generator', 'hw-elementor-woo-dynamic'), 
                    __('jetengine', 'hw-elementor-woo-dynamic')
                ]
            ],
        ];
    }
}
