<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class SpecBadge extends Tag {

    public function get_name() {
        return 'spec-badge';
    }

    public function get_title() {
        return __('Special Badge', 'hw-ele-woo-dynamic');
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
                'default' => '',
            ]
        );

        $this->add_control(
            'exclude_features',
            [
                'label' => __('Exclude Features', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => [
                    'featured' => __('Exclude the Featured', 'hw-ele-woo-dynamic'),
                    'sold_individually' => __('Exclude Sold Individually', 'hw-ele-woo-dynamic'),
                    'digital' => __('Exclude Digital', 'hw-ele-woo-dynamic'),
                    'virtual' => __('Exclude Virtual', 'hw-ele-woo-dynamic'),
                ],
                'label_block' => true,
                'description' => __('Since it lists all values, you can specify what not to include in the badge.', 'hw-ele-woo-dynamic'),
            ]
        );

        $this->add_control(
            'featured_custom_text',
            [
                'label' => __('Featured Product Custom Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Featured', 'woocommerce'),
                'condition' => [
                    'enable_custom_text' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'digital_custom_text',
            [
                'label' => __('Digital Product Custom Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Digital', 'hw-ele-woo-dynamic'),
                'condition' => [
                    'enable_custom_text' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'virtual_custom_text',
            [
                'label' => __('Virtual Product Custom Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Virtual', 'hw-ele-woo-dynamic'),
                'condition' => [
                    'enable_custom_text' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'sold_individually_custom_text',
            [
                'label' => __('Sold Individually Custom Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Sold Individually', 'woocommerce'),
                'condition' => [
                    'enable_custom_text' => 'yes',
                ],
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings();
        $product = wc_get_product(get_the_ID());
    
        if (!$product) {
            return;
        }
    
        $badges = [];
        $excludes = (array) $settings['exclude_features']; // Cast to array for safety
    
        if ($product->is_featured() && !in_array('featured', $excludes)) {
            $badges[] = $settings['enable_custom_text'] === 'yes' ? $settings['featured_custom_text'] : __('Featured', 'woocommerce');
        }
    
        if ($product->is_virtual() && !in_array('virtual', $excludes)) {
            $badges[] = $settings['enable_custom_text'] === 'yes' ? $settings['virtual_custom_text'] : __('Virtual', 'woocommerce');
        }
    
        if ($product->is_downloadable() && !in_array('digital', $excludes)) {
            $badges[] = $settings['enable_custom_text'] === 'yes' ? $settings['digital_custom_text'] : __('Digital', 'woocommerce');
        }
    
        if ($product->is_sold_individually() && !in_array('sold_individually', $excludes)) {
            $badges[] = $settings['enable_custom_text'] === 'yes' ? $settings['sold_individually_custom_text'] : __('Sold Individually', 'woocommerce');
        }
    
        $badges_safe = array_map('wp_kses_post', $badges); 
        echo implode(', ', $badges_safe);
    }
    
}
