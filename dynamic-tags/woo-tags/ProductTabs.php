<?php
namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class ProductTabs extends Tag {

    public function get_name() {
        return 'product-tabs';
    }

    public function get_title() {
        return __('Product Tabs', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $tab_source_options = [
            'default' => __('Default Tabs', 'hw-ele-woo-dynamic')
        ];

        if (Dependencies::is_tab_manager_active()) {
            $tab_source_options['tab_manager'] = __('Tab Manager', 'hw-ele-woo-dynamic');
        }

        $this->add_control(
            'tab_source',
            [
                'label' => __('Tab Source', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => $tab_source_options,
                'default' => 'default',
            ]
        );

        $this->add_control(
            'tab_selection',
            [
                'label' => __('Choose Tab', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_default_tabs(),
                'default' => 'description',
                'condition' => [
                    'tab_source' => 'default',
                ],
            ]
        );

        if (Dependencies::is_tab_manager_active()) {
            $this->add_control(
                'tab_manager_selection',
                [
                    'label' => __('Choose Tab', 'hw-ele-woo-dynamic'),
                    'type' => Controls_Manager::SELECT,
                    'options' => $this->get_tab_manager_tabs(),
                    'condition' => [
                        'tab_source' => 'tab_manager',
                    ],
                ]
            );
        }

        $this->add_control(
            'output',
            [
                'label' => __('Output', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'name' => __('Name', 'hw-ele-woo-dynamic'),
                    'content' => __('Content', 'hw-ele-woo-dynamic'),
                ],
                'default' => 'content',
            ]
        );
    }

    private function get_default_tabs() {
        return [
            'description' => __('Description', 'hw-ele-woo-dynamic'),
            'additional_information' => __('Additional Information', 'hw-ele-woo-dynamic'),
        ];
    }

    private function get_tab_manager_tabs() {
        if (!Dependencies::is_tab_manager_active()) {
            return [];
        }

        $args = [
            'post_type' => 'wc_product_tab',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ];
        $tabs = get_posts($args);
        $options = [];

        foreach ($tabs as $tab) {
            $options[$tab->ID] = $tab->post_title;
        }

        return $options;
    }

    public function render() {
        $product = wc_get_product(get_the_ID());
        if (!$product) {
            return;
        }

        $settings = $this->get_settings();
        $tab_source = $settings['tab_source'];
        $output_type = $settings['output'];

        if ('default' === $tab_source) {
            $this->render_default_tab($settings['tab_selection'], $output_type, $product);
        } elseif ('tab_manager' === $tab_source && Dependencies::is_tab_manager_active()) {
            $this->render_tab_manager_tab($settings['tab_manager_selection'], $output_type);
        }
    }

    private function render_default_tab($selected_tab, $output_type, $product) {
        global $product;
        $product = wc_get_product(get_the_ID());
        
        $tabs = apply_filters('woocommerce_product_tabs', []);
        
        if (!is_array($tabs) || !isset($tabs[$selected_tab])) {
            return;
        }
        
        $tab = $tabs[$selected_tab];
        
        if ('name' === $output_type && isset($tab['title'])) {
            echo esc_html($tab['title']);
        } elseif ('content' === $output_type) {
            add_filter('woocommerce_product_description_heading', '__return_false');
            ob_start();
            call_user_func($tab['callback'], $selected_tab, $tab);
            $content = ob_get_clean();
            echo wp_kses_post($content);
            remove_filter('woocommerce_product_description_heading', '__return_false');
        }
    }
    
    private function render_tab_manager_tab($tab_id, $output_type) {
        $product_tabs = get_post_meta(get_the_ID(), '_product_tabs', true);

        if (!$product_tabs || !array_search($tab_id, array_column($product_tabs, 'id'))) {
            return;
        }

        $tab_post = get_post($tab_id);
        if (!$tab_post || 'wc_product_tab' !== $tab_post->post_type) {
            return;
        }

        if ('name' === $output_type) {
            echo esc_html($tab_post->post_title);
        } elseif ('content' === $output_type && !empty($tab_post->post_content)) {
            echo wp_kses_post($tab_post->post_content);
        }
    }
}
