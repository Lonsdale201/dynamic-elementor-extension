<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

class ProductGallery extends Data_Tag {

    public function get_name() {
        return 'product-gallery-first-image';
    }

    public function get_title() {
        return __('Product Gallery Image', 'hw-elementor-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [ Module::IMAGE_CATEGORY ];
    }

    protected function _register_controls() {
        $this->add_control(
            'image_index',
            [
                'label' => __('Image Index', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'description' => __('Set the index of the image in the gallery. 0 for the first image.', 'hw-elementor-woo-dynamic'),
            ]
        );

        $this->add_control(
            'fallback_featured_image',
            [
                'label' => __('Fallback to Featured Image', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'hw-elementor-woo-dynamic'),
                'label_off' => __('No', 'hw-elementor-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => __('If enabled, and there is no gallery or the index is out of bounds, the featured image will be displayed.', 'hw-elementor-woo-dynamic'),
            ]
        );
    }

    public function get_value( array $options = [] ) {
        $product = wc_get_product();

        if (!$product) {
            return [];
        }

        $attachment_ids = $product->get_gallery_image_ids();
        $image_index = $this->get_settings('image_index');

        if (empty($attachment_ids) || !isset($attachment_ids[$image_index])) {
            if ($this->get_settings('fallback_featured_image') === 'yes') {
                $featured_image_id = $product->get_image_id();
                if ($featured_image_id) {
                    return [
                        'id' => $featured_image_id,
                        'url' => wp_get_attachment_url($featured_image_id)
                    ];
                }
            }

            $image_index = 0;
        }

        if (!empty($attachment_ids) && isset($attachment_ids[$image_index])) {
            $image_id = $attachment_ids[$image_index];
        } else {
            return [];
        }

        return [
            'id' => $image_id,
            'url' => esc_url(wp_get_attachment_url($image_id))
        ];
    }
}
