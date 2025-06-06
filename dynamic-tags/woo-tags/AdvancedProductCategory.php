<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;
use WP_Query;

class AdvancedProductCategory extends Tag {

    public function get_name() {
        return 'advanced-product-category';
    }

    public function get_title() {
        return esc_html__('Advanced Product Category', 'hw-elementor-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        // Registering all controls, including the new Hide Uncategorized switcher
        $this->add_control(
            'query_type',
            [
                'label' => esc_html__('Query', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'all' => esc_html__('All', 'hw-elementor-woo-dynamic'),
                    'current_query' => esc_html__('Current Query', 'hw-elementor-woo-dynamic'),
                ],
                'default' => 'all',
            ]
        );

        $this->add_control(
            'output_format',
            [
                'label' => esc_html__('Output Format', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'inline' => esc_html__('Inline', 'hw-elementor-woo-dynamic'),
                    'list' => esc_html__('List', 'hw-elementor-woo-dynamic'),
                ],
                'default' => 'inline',
            ]
        );

        $this->add_control(
            'enable_multi_leveling',
            [
                'label' => esc_html__('Enable Multi Leveling', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'hw-elementor-woo-dynamic'),
                'label_off' => esc_html__('No', 'hw-elementor-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'output_format' => 'list',
                ],
            ]
        );

        $this->add_control(
            'back_to_prev_text',
            [
                'label' => esc_html__('Back to prev', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Back to previous {prev_cat}', 'hw-elementor-woo-dynamic'),
                'description' => esc_html__('Use the {prev_cat} to display the previous level term name', 'hw-elementor-woo-dynamic'),
                'condition' => [
                    'query_type' => 'current_query',
                ],
            ]
        );

        $this->add_control(
            'always_show_back_to_prev',
            [
                'label' => esc_html__('Always Show Back to Prev', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'hw-elementor-woo-dynamic'),
                'label_off' => esc_html__('No', 'hw-elementor-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'query_type' => 'current_query',
                ],
            ]
        );

        $this->add_control(
            'shop_page_back_text',
            [
                'label' => esc_html__('Shop Page Back Text', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Return to Shop', 'hw-elementor-woo-dynamic'),
                'condition' => [
                    'always_show_back_to_prev' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'linkable',
            [
                'label' => esc_html__('Linkable', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'hw-elementor-woo-dynamic'),
                'label_off' => esc_html__('No', 'hw-elementor-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'show_counter',
            [
                'label' => esc_html__('Show Counter', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'hw-elementor-woo-dynamic'),
                'label_off' => esc_html__('No', 'hw-elementor-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'hide_empty',
            [
                'label' => esc_html__('Hide Empty', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'hw-elementor-woo-dynamic'),
                'label_off' => esc_html__('No', 'hw-elementor-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'hide_uncategorized',
            [
                'label' => esc_html__('Hide Uncategorized', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'hw-elementor-woo-dynamic'),
                'label_off' => esc_html__('No', 'hw-elementor-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'delimiter',
            [
                'label' => esc_html__('Delimiter', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => ', ',
                'condition' => [
                    'output_format' => 'inline',
                ],
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings_for_display();
        $query_type = sanitize_text_field($settings['query_type']);
        $categories = $this->get_categories_based_on_query($query_type, $settings);
        $output_format = sanitize_text_field($settings['output_format']);
        $delimiter = sanitize_text_field($settings['delimiter']);
        $linkable = sanitize_text_field($settings['linkable']);
        $show_counter = sanitize_text_field($settings['show_counter']);
        $back_to_prev_text = sanitize_text_field($settings['back_to_prev_text']);
        $always_show_back_to_prev = sanitize_text_field($settings['always_show_back_to_prev']);
        $shop_page_back_text = sanitize_text_field($settings['shop_page_back_text']);
        $enable_multi_leveling = sanitize_text_field($settings['enable_multi_leveling']);
    
        $prev_cat_link = '';
        $prev_cat_name = '';
    
        if ('current_query' === $query_type && is_product_category()) {
            $queried_object = get_queried_object();
            $parent_id = $queried_object->parent;
            if ($parent_id && ('yes' === $always_show_back_to_prev || !empty($categories))) {
                $prev_cat_link = esc_url(get_term_link($parent_id, 'product_cat'));
                $prev_cat_name = get_term($parent_id)->name;
                $back_to_prev_text_replaced = str_replace('{prev_cat}', esc_html($prev_cat_name), $back_to_prev_text);
                $prev_link_html = "<a href='{$prev_cat_link}'>" . esc_html($back_to_prev_text_replaced) . "</a>";
            } elseif ('yes' === $always_show_back_to_prev && empty($categories)) {
                // Use the Shop page URL if no categories exist
                $shop_page_url = get_permalink(wc_get_page_id('shop'));
                $prev_link_html = "<a href='{$shop_page_url}'>" . esc_html($shop_page_back_text) . "</a>";
            }
        }
    
        if ($output_format === 'list') {
            echo '<ul>';
            if (isset($prev_link_html)) {
                echo "<li class='hw-top-level'>$prev_link_html</li>";
            }
            $this->list_categories($categories, $settings);
            echo '</ul>';
        } else {
            $category_links = [];
            if (isset($prev_link_html)) {
                array_unshift($category_links, $prev_link_html);
            }
            foreach ($categories as $category) {
                $link = esc_url(get_term_link($category->term_id, 'product_cat'));
                $name = esc_html($category->name);
                $count_html = $show_counter === 'yes' ? "<span class='hw-counter'>" . esc_html($category->count) . "</span>" : "";
                $category_html = $linkable === 'yes' ? "<a href='{$link}'>{$name}</a> {$count_html}" : "{$name} {$count_html}";
                $category_links[] = $category_html;
            }
            echo implode($delimiter, $category_links);
        }
    }
    

    protected function get_categories_based_on_query($query_type, $settings) {
        $args = [
            'taxonomy' => 'product_cat',
            'hide_empty' => $settings['hide_empty'] === 'yes',
            'orderby' => 'name', 
            'order' => 'ASC', 
        ];
    
        if ($query_type === 'current_query' && is_product_category()) {
            $queried_object = get_queried_object();
            $args['parent'] = $queried_object->term_id;
        }

        $categories = get_terms($args);

        // Check if hide_uncategorized is enabled, filter out default category
        if ('yes' === $settings['hide_uncategorized']) {
            $default_cat_id = get_option('default_product_cat', '0'); // Fetch default product category ID
            $categories = array_filter($categories, function ($category) use ($default_cat_id) {
                return $category->term_id !== intval($default_cat_id);
            });
        }
    
        return is_wp_error($categories) ? [] : $categories;
    }
    
    protected function list_categories($categories, $settings, $level = 0) {
        foreach ($categories as $category) {
            $class = $level === 0 ? 'hw-top-level' : 'hw-sub-level';
            $link = esc_url(get_term_link($category->term_id, 'product_cat'));
            $name = esc_html($category->name);
            
            $count_html = $settings['show_counter'] === 'yes' ? "<span class='hw-counter'>" . esc_html($category->count) . "</span>" : "";
            
            echo "<li class='$class'>";
            if ($settings['linkable'] === 'yes' && !is_wp_error($link)) {
                echo "<a href='{$link}'>{$name}</a> {$count_html}";
            } else {
                echo "{$name} {$count_html}";
            }
    
            if ($settings['enable_multi_leveling'] === 'yes') {
                $child_categories = get_terms(['taxonomy' => 'product_cat', 'parent' => $category->term_id, 'hide_empty' => $settings['hide_empty'] === 'yes']);
                if (!empty($child_categories)) {
                    echo '<ul>';
                    $this->list_categories($child_categories, $settings, $level + 1);
                    echo '</ul>';
                }
            }
            echo "</li>";
        }
    }
}
