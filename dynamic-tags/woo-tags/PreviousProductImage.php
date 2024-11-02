<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

class PreviousProductImage extends Data_Tag {

    public function get_name() {
        return 'previous-product-image';
    }

    public function get_title() {
        return __('Previous Product Image', 'hw-ele-woo-dynamic');
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
            'order' => 'DESC',  
            'date_query' => [
                ['before' => $post->post_date],  
            ],
            'fields' => 'ids',
        ];

        $previous_product_query = new \WP_Query($args);
        if (empty($previous_product_query->posts)) {
            return [];
        }

        $previous_product_id = $previous_product_query->posts[0];
        $previous_product = wc_get_product($previous_product_id);
        if (!$previous_product) {
            return [];
        }

        $image_id = $previous_product->get_image_id();
        if (!$image_id) {
            return [];
        }

        return [
            'id' => $image_id,
            'url' => wp_get_attachment_image_url($image_id, 'full')
        ];
    }
}
