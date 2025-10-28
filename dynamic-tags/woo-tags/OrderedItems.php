<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;

class OrderedItems extends AbstractOrderTag
{
    public function get_name()
    {
        return 'wc-order-hpos-items-list';
    }

    public function get_title()
    {
        return esc_html__('Ordered Items', 'hw-ele-woo-dynamic');
    }

    public function get_categories()
    {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls()
    {
        $this->add_control(
            'display_format',
            [
                'label' => esc_html__('Display Format', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'default' => 'plain',
                'options' => [
                    'plain' => esc_html__('Plain', 'hw-ele-woo-dynamic'),
                    'list'  => esc_html__('List', 'hw-ele-woo-dynamic'),
                ],
            ]
        );

        $this->add_control(
            'separate_variable',
            [
                'label' => esc_html__('Separate variable', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => esc_html__('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'variation_label',
            [
                'label' => esc_html__('Variation label', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Variation:', 'hw-ele-woo-dynamic'),
                'placeholder' => esc_html__('Variation:', 'hw-ele-woo-dynamic'),
                'condition' => [
                    'separate_variable' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'show_attribute_name',
            [
                'label' => esc_html__('Show attribute names', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => esc_html__('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'separate_variable' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'plain_separator',
            [
                'label' => esc_html__('Separator', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => ', ',
                'placeholder' => ', ',
                'condition' => [
                    'display_format' => 'plain',
                ],
            ]
        );

        $this->add_control(
            'plain_split_row',
            [
                'label' => esc_html__('Split variation row', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => esc_html__('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'display_format' => 'plain',
                    'separate_variable' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'variation_class',
            [
                'label' => esc_html__('Variation CSS class', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => 'wc-order-hpos-variation',
                'placeholder' => 'wc-order-hpos-variation',
                'condition' => [
                    'separate_variable' => 'yes',
                ],
            ]
        );

    }

    public function render()
    {
        $order = $this->get_current_order();

        if (! $order) {
            echo '';
            return;
        }

        $items = $order->get_items('line_item');

        if (empty($items)) {
            echo '';
            return;
        }

        $settings = $this->get_settings_for_display();
        $format = $settings['display_format'] ?? 'plain';
        $separate_variable = isset($settings['separate_variable']) && 'yes' === $settings['separate_variable'];
        $raw_variation_label = isset($settings['variation_label']) ? trim($settings['variation_label']) : esc_html__('Variation:', 'hw-ele-woo-dynamic');
        $variation_label = $raw_variation_label !== '' ? $raw_variation_label : '';
        $show_attribute_name = ! (isset($settings['show_attribute_name']) && 'yes' !== $settings['show_attribute_name']);

        $separator = ', ';
        if ('plain' === $format) {
            $separator = isset($settings['plain_separator']) ? sanitize_text_field($settings['plain_separator']) : ', ';
        }

        
        $variation_class = $separate_variable ? (isset($settings['variation_class']) ? sanitize_html_class($settings['variation_class']) : 'wc-order-hpos-variation') : 'wc-order-hpos-variation';
        if (! $variation_class) {
            $variation_class = 'wc-order-hpos-variation';
        }

        $output = [];

        foreach ($items as $item) {
            $name = $item->get_name();
            $qty  = $item->get_quantity();
            $line = $qty > 1 ? sprintf('%s × %s', $name, absint($qty)) : $name;

            $parent_base_name = $name;
            $variation_html = '';
            $variation_plain = '';

            if ($separate_variable && $item->get_variation_id()) {
                $parent_product = $item->get_product_id() ? wc_get_product($item->get_product_id()) : null;
                if ($parent_product) {
                    $parent_base_name = $parent_product->get_name();
                }

                $variation_product = wc_get_product($item->get_variation_id());
                $raw_variation = $variation_product ? $variation_product->get_attribute_summary() : '';

                if (! $raw_variation) {
                    $raw_variation = $item->get_meta('variation_text');
                }

                $variation_value = $this->format_variation_value($raw_variation, $show_attribute_name);

                if ($variation_value !== '') {
                    $variation_plain_raw = ($variation_label !== '' ? $variation_label . ' ' : '') . $variation_value;
                    $variation_plain = esc_html($variation_plain_raw);
                    $variation_html = '<span class="' . esc_attr($variation_class) . '">' . $variation_plain . '</span>';
                }
            }

            $parent_display = $parent_base_name . ( $qty > 1 ? ' × ' . absint($qty) : '' );

            $output[] = [
                'parent_line' => esc_html($parent_display),
                'variation_html' => $variation_html,
                'variation_plain' => $variation_plain,
            ];
        }

        if ('list' === $format) {
            echo '<ul>';
            foreach ($output as $item) {
                if ($separate_variable && $item['variation_plain'] !== '') {
                    echo '<li>' . $item['parent_line'];
                    echo '<ul class="' . esc_attr($variation_class) . '"><li>' . $item['variation_plain'] . '</li></ul></li>';
                } else {
                    echo '<li>' . $item['parent_line'] . '</li>';
                }
            }
            echo '</ul>';
            return;
        }

        $flat = [];
        foreach ($output as $item) {
            if ($separate_variable && $item['variation_html'] !== '') {
                if ('plain' === $format && isset($settings['plain_split_row']) && 'yes' === $settings['plain_split_row']) {
                    $flat[] = $item['parent_line'] . '<br>' . $item['variation_html'];
                } else {
                    $flat[] = $item['parent_line'] . ' ' . $item['variation_html'];
                }
            } else {
                $flat[] = $item['parent_line'];
            }
        }

        echo wp_kses_post(implode($separator, $flat));
    }
    private function format_variation_value($value, bool $show_attribute_name): string
    {
        if (empty($value) && '0' !== $value) {
            return '';
        }

        if (is_array($value)) {
            $value = reset($value);
        }

        if (! is_string($value) && ! is_numeric($value)) {
            return '';
        }

        $value = wp_strip_all_tags((string) $value);

        if ($value === '') {
            return '';
        }

        if ($show_attribute_name) {
            return $value;
        }

        $segments = preg_split('/\s*[\|,]\s*/', $value);
        $parts = [];

        foreach ($segments as $segment) {
            $segment = trim($segment);

            if ($segment === '') {
                continue;
            }

            $pieces = explode(':', $segment, 2);
            $parts[] = isset($pieces[1]) ? trim($pieces[1]) : trim($pieces[0]);
        }

        $parts = array_filter($parts, static function ($item) {
            return $item !== '';
        });

        return implode(', ', $parts);
    }

}
