<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class NextProduct extends Tag {

    public function get_name() {
        return 'next-product';
    }

    public function get_title() {
        return __('Next Product', 'hw-elementor-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::URL_CATEGORY, Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'display_type',
            [
                'label' => __('Display Type', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'title' => __('Product Title', 'hw-elementor-woo-dynamic'),
                    'link' => __('Product Link', 'hw-elementor-woo-dynamic'),
                    'price' => __('Product Price', 'hw-elementor-woo-dynamic'),
                ],
                'default' => 'title',
            ]
        );
    }

    public function render() {
        global $post;

        $args = [
            'post_type' => 'product',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'ASC',
            'date_query' => [
                ['after' => $post->post_date],
            ],
            'fields' => 'ids',
        ];

        $next_product_query = new \WP_Query($args);
        if (empty($next_product_query->posts)) {
            echo '';  
            return;
        }
        $next_product_id = $next_product_query->posts[0];
        $next_product = wc_get_product($next_product_id);
        if (!$next_product) {
            echo '';  
            return;
        }

        $display_type = $this->get_settings('display_type');
        switch ($display_type) {
            case 'link':
                echo esc_url(get_permalink($next_product_id));
                break;
            case 'price':
                echo $next_product->get_price_html();
                break;
            default: // 'title'
                echo esc_html($next_product->get_name());
                break;
        }
    }
}
