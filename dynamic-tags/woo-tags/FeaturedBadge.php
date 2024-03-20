<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class FeaturedBadge extends Tag {

    public function get_name() {
        return 'featured-badge';
    }

    public function get_title() {
        return __('Featured Badge', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'enable_custom_text',
            [
                'label' => __('Enable Custom Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => __('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'custom_text',
            [
                'label' => __('Custom Text for Featured Product', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Featured', 'hw-ele-woo-dynamic'),
                'condition' => [
                    'enable_custom_text' => 'yes',
                ],
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings();
        $product = wc_get_product(get_the_ID());

        if (!$product || !$product->is_featured()) {
            return;
        }

        $text = $settings['enable_custom_text'] === 'yes' && !empty($settings['custom_text']) ? $settings['custom_text'] : __('Featured', 'woocommerce');

        echo wp_kses_post($text);
    }
}
