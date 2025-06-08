<?php
namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class ProductLength extends Tag {

    public function get_name()       { return 'product-length'; }
    public function get_title()      { return __('Product Length', 'hw-ele-woo-dynamic'); }
    public function get_group()      { return 'woo-extras'; }
    public function get_categories() { return [ Module::TEXT_CATEGORY ]; }

    protected function _register_controls() {
        $units = [
            ''   => esc_html__('Store unit (no conversion)', 'hw-ele-woo-dynamic'),
            'mm' => 'mm',
            'cm' => 'cm',
            'm'  => 'm',
            'in' => 'in',
            'yd' => 'yd',
        ];
        $this->add_control('convert_from', [
            'label'   => esc_html__('Convert From', 'hw-ele-woo-dynamic'),
            'type'    => Controls_Manager::SELECT,
            'options' => $units,
            'default' => '',
        ]);
        $this->add_control('convert_to', [
            'label'   => esc_html__('Convert To', 'hw-ele-woo-dynamic'),
            'type'    => Controls_Manager::SELECT,
            'options' => $units,
            'default' => '',
        ]);
    }

    public function render() {
        $settings = $this->get_settings();
        $product  = wc_get_product(get_the_ID());
        if (!$product) {
            return;
        }
        $raw = $product->get_length();
        if ($raw === '' || ! is_numeric($raw)) {
            return;
        }
        $value = (float) $raw;
        $from  = $settings['convert_from'];
        $to    = $settings['convert_to'];
        if ($from && $to) {
            $value = $this->convert_units($value, $from, $to);
        }
        echo esc_html($value);
    }

    private function convert_units(float $value, string $from, string $to): float {
        $f = [
            'mm' => 0.001,
            'cm' => 0.01,
            'm'  => 1.0,
            'in' => 0.0254,
            'yd' => 0.9144,
        ];
        if (isset($f[$from], $f[$to])) {
            $meters = $value * $f[$from];
            $value  = $meters / $f[$to];
        }
        return (float) rtrim(rtrim(number_format($value, 4, '.', ''), '0'), '.');
    }
}
