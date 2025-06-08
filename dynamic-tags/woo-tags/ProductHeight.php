<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class ProductHeight extends Tag {

    public function get_name() {
        return 'product-height';
    }

    public function get_title() {
        return __('Product Height', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    protected function _register_controls() {
        // Convert From unit
        $this->add_control('convert_from', [
            'label'   => esc_html__('Convert From', 'hw-ele-woo-dynamic'),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                ''   => esc_html__('Store unit (no conversion)', 'hw-ele-woo-dynamic'),
                'mm' => 'mm',
                'cm' => 'cm',
                'm'  => 'm',
                'in' => 'in',
                'yd' => 'yd',
            ],
            'default' => '',
        ]);

        // Convert To unit
        $this->add_control('convert_to', [
            'label'   => esc_html__('Convert To', 'hw-ele-woo-dynamic'),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                ''   => esc_html__('—', 'hw-ele-woo-dynamic'),
                'mm' => 'mm',
                'cm' => 'cm',
                'm'  => 'm',
                'in' => 'in',
                'yd' => 'yd',
            ],
            'default' => '',
        ]);
    }

    public function render() {
        $settings = $this->get_settings();
        $product  = wc_get_product(get_the_ID());
        if (!$product) {
            return;
        }

        $raw = $product->get_height();
        if ($raw === '' || ! is_numeric($raw)) {
            return;
        }
        $value = (float) $raw;

        $from = $settings['convert_from'];
        $to   = $settings['convert_to'];

        // conversion factors to meters
        $f = [
            'mm' => 0.001,
            'cm' => 0.01,
            'm'  => 1.0,
            'in' => 0.0254,
            'yd' => 0.9144,
        ];

        // if both set and valid, convert
        if ($from && $to && isset($f[$from], $f[$to])) {
            // from→m, then m→to
            $meters     = $value * $f[$from];
            $converted  = $meters / $f[$to];
            // format with up to 4 decimals, trimming trailing zeros
            $value = rtrim(rtrim(number_format($converted, 4, '.', ''), '0'), '.');
        }

        echo esc_html($value);
    }
}
