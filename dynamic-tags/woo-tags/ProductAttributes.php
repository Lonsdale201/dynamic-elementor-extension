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
        return esc_html__('Product Attributes', 'hw-elementor-woo-dynamic');
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
                'label' => esc_html__('Display Type', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'default' => 'label/value',
                'options' => [
                    'value' => esc_html__('Value', 'hw-elementor-woo-dynamic'),
                    'label' => esc_html__('Label', 'hw-elementor-woo-dynamic'),
                    'label/value' => esc_html__('Label/Value', 'hw-elementor-woo-dynamic'),
                ],
            ]
        );

        $this->add_control(
            'label_separator',
            [
                'label' => esc_html__('Label Separator', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => ': ',
                'condition' => [
                    'display_type' => 'label/value',
                ],
            ]
        );

        $this->add_control(
            'linkable',
            [
                'label' => esc_html__('Linkable', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__('Yes', 'hw-elementor-woo-dynamic'),
                'label_off' => esc_html__('No', 'hw-elementor-woo-dynamic'),
                'return_value' => 'yes',
            ]
        );

        $attributes = wc_get_attribute_taxonomies();
        $attribute_options = ['all' => esc_html__('All', 'hw-elementor-woo-dynamic')];

        foreach ($attributes as $attribute) {
            $attribute_options[$attribute->attribute_name] = $attribute->attribute_label;
        }

        $this->add_control(
            'attribute_name',
            [
                'label' => esc_html__('Attribute', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'default' => 'all',
                'options' => $attribute_options,
            ]
        );

        $this->add_control(
            'output_style',
            [
                'label' => esc_html__('Output Style', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'default' => 'delimiter',
                'options' => [
                    'ul' => esc_html__('UL', 'hw-elementor-woo-dynamic'),
                    'ol' => esc_html__('OL', 'hw-elementor-woo-dynamic'),
                    'delimiter' => esc_html__('Delimiter', 'hw-elementor-woo-dynamic'),
                ],
            ]
        );
    
        $this->add_control(
            'delimiter',
            [
                'label' => esc_html__('Delimiter', 'hw-elementor-woo-dynamic'),
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
        $label_separator = $settings['label_separator'];
        $selected_attribute = $settings['attribute_name'];
        $output_style = $settings['output_style'];
        $delimiter = $settings['delimiter'];
        $linkable = $settings['linkable'] === 'yes';
        $product = wc_get_product(get_the_ID());
    
        if (!$product) {
            return;
        }

        $attributes = $product->get_attributes();
        $output = [];

        // Get all registered attributes to check which ones have archives enabled
        $registered_attributes = wc_get_attribute_taxonomies();
        $archivable_attributes = [];
        foreach ($registered_attributes as $registered_attribute) {
            if ($registered_attribute->attribute_public) {
                $archivable_attributes[] = 'pa_' . $registered_attribute->attribute_name;
            }
        }
    
        foreach ($attributes as $attribute_name => $attribute) {
            $normalized_name = str_replace('pa_', '', $attribute_name);
    
            if ('all' !== $selected_attribute && $selected_attribute !== $normalized_name) {
                continue;
            }
    
            $attribute_terms = wc_get_product_terms($product->get_id(), $attribute_name, ['fields' => 'all']);
            $values = array_map(function($term) use ($linkable, $archivable_attributes, $attribute_name) {
                $term_name = esc_html($term->name);
                // Only create link if 'linkable' is enabled and term has an archive page
                if ($linkable && in_array($attribute_name, $archivable_attributes)) {
                    $term_link = get_term_link($term);
                    if (!is_wp_error($term_link)) {
                        return '<a href="' . esc_url($term_link) . '">' . $term_name . '</a>';
                    }
                }
                return $term_name;
            }, $attribute_terms);
            
            $label = esc_html(wc_attribute_label($attribute_name));
    
            switch ($display_type) {
                case 'label':
                    $item = $label;
                    break;
                case 'value':
                    $item = implode(esc_html($delimiter), $values);
                    break;
                case 'label/value':
                    $item = $label . esc_html($label_separator) . implode(esc_html($delimiter), $values);
                    break;
            }
    
            $output[] = $item;
        }
    
        if ($output_style === 'ul' || $output_style === 'ol') {
            $tag = $output_style === 'ul' ? 'ul' : 'ol';
            echo "<$tag><li>" . implode("</li><li>", array_map('wp_kses_post', $output)) . "</li></$tag>";
        } else {
            echo implode(esc_html($delimiter), array_map('wp_kses_post', $output));
        }
    }
}
