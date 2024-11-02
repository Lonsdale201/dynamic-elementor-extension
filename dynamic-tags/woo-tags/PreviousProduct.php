<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class PreviousProduct extends Tag {

    public function get_name() {
        return 'previous-product';
    }

    public function get_title() {
        return __('Previous Product', 'hw-ele-woo-dynamic');
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
                'label' => __('Display Type', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'title' => __('Product Title', 'hw-ele-woo-dynamic'),
                    'link' => __('Product Link', 'hw-ele-woo-dynamic'),
                    'price' => __('Product Price', 'hw-ele-woo-dynamic'),
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
            'order' => 'DESC',  
            'date_query' => [
                ['before' => $post->post_date],  
            ],
            'fields' => 'ids',
        ];

        $previous_product_query = new \WP_Query($args);
        if (empty($previous_product_query->posts)) {
            echo '';  
            return;
        }
        $previous_product_id = $previous_product_query->posts[0];
        $previous_product = wc_get_product($previous_product_id);
        if (!$previous_product) {
            echo ''; 
            return;
        }

        $display_type = $this->get_settings('display_type');
        switch ($display_type) {
            case 'link':
                echo esc_url(get_permalink($previous_product_id));
                break;
            case 'price':
                echo $previous_product->get_price_html();
                break;
            default: // 'title'
                echo esc_html($previous_product->get_name());
                break;
        }
    }
}
