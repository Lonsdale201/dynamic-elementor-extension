<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class ProductAttributes extends Tag {

    public function get_name() {
        return 'product-attributes';
    }

    public function get_title() {
        return __('Product Attributes', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'display_type',
            [
                'label' => __('Display Type', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'default' => 'label/value',
                'options' => [
                    'value' => __('Value', 'hw-ele-woo-dynamic'),
                    'label' => __('Label', 'hw-ele-woo-dynamic'),
                    'label/value' => __('Label/Value', 'hw-ele-woo-dynamic'),
                ],
            ]
        );

        $attributes = wc_get_attribute_taxonomies();
        $attribute_options = ['all' => __('All', 'hw-ele-woo-dynamic')];

        foreach ($attributes as $attribute) {
            $attribute_options[$attribute->attribute_name] = $attribute->attribute_label;
        }

        $this->add_control(
            'attribute_name',
            [
                'label' => __('Attribute', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'default' => 'all',
                'options' => $attribute_options,
            ]
        );

        $this->add_control(
            'output_style',
            [
                'label' => __('Output Style', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'default' => 'delimiter',
                'options' => [
                    'ul' => __('UL', 'hw-ele-woo-dynamic'),
                    'ol' => __('OL', 'hw-ele-woo-dynamic'),
                    'delimiter' => __('Delimiter', 'hw-ele-woo-dynamic'),
                ],
            ]
        );
    
        $this->add_control(
            'delimiter',
            [
                'label' => __('Delimiter', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => ', ',
                'condition' => [
                    'output_style' => 'delimiter',
                ],
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings();
        $display_type = $settings['display_type'];
        $selected_attribute = $settings['attribute_name'];
        $output_style = $settings['output_style'];
        $delimiter = $settings['delimiter'];
        $product = wc_get_product(get_the_ID());
    
        if (!$product) {
            return;
        }
    
        $attributes = $product->get_attributes();
        $output = [];
    
        foreach ($attributes as $attribute_name => $attribute) {
            $normalized_name = str_replace('pa_', '', $attribute_name);
    
            if ('all' !== $selected_attribute && $selected_attribute !== $normalized_name) {
                continue;
            }
    
            $attribute_terms = wc_get_product_terms($product->get_id(), $attribute_name, ['fields' => 'all']);
            $values = array_map(function($term) { return esc_html($term->name); }, $attribute_terms);
            $label = esc_html(wc_attribute_label($attribute_name));
    
            switch ($display_type) {
                case 'label':
                    $item = $label;
                    break;
                case 'value':
                    $item = implode(esc_html($delimiter), $values);
                    break;
                case 'label/value':
                    $item = "$label: " . implode(esc_html($delimiter), $values);
                    break;
            }
    
            $output[] = $item;
        }
    
        if ($output_style === 'ul' || $output_style === 'ol') {
            $tag = $output_style === 'ul' ? 'ul' : 'ol';
            echo "<$tag><li>" . implode("</li><li>", array_map('wp_kses_post', $output)) . "</li></$tag>";
        } else {
            echo implode(esc_html($delimiter), array_map('esc_html', $output));
        }
    }
    
}
