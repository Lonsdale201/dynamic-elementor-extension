<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

class NextProductImage extends Data_Tag {

    public function get_name() {
        return 'next-product-image';
    }

    public function get_title() {
        return __('Next Product Image', 'hw-elementor-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [ Module::IMAGE_CATEGORY ];
    }

    public function get_value(array $options = []) {
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
            return [];
        }

        $next_product_id = $next_product_query->posts[0];
        $next_product = wc_get_product($next_product_id);
        if (!$next_product) {
            return [];
        }

        $image_id = $next_product->get_image_id();
        if (!$image_id) {
            return [];
        }

        return [
            'id' => $image_id,
            'url' => wp_get_attachment_image_url($image_id, 'full')
        ];
    }
}
