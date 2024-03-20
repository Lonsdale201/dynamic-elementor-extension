<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class AdvancedSaleBadge extends Tag {

    public function get_name() {
        return 'advanced-sale-badge';
    }

    public function get_title() {
        return __('Advanced Sale Badge', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'sale_text',
            [
                'label' => __('Sale Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'Sale!',
                'placeholder' => 'Sale!',
                'description' => __('Use {sale_number} to display the amount saved. Use {sale_percentage} to display the sale percentage.', 'hw-ele-woo-dynamic'),
            ]
        );

        $this->add_control(
            'format_currency',
            [
                'label' => __('Currency and Format', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => __('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => __('Enable this to format the sale number as a price with currency.', 'hw-ele-woo-dynamic'),
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings();
        $product = wc_get_product(get_the_ID());

        if (!$product || !$product->is_on_sale()) {
            return;
        }

        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        $sale_number = $regular_price - $sale_price;
        $sale_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);

        if ('yes' === $settings['format_currency']) {
            $sale_number = wc_price($sale_number);
        }

        $sale_text = str_replace(['{sale_number}', '{sale_percentage}'], [$sale_number, $sale_percentage], $settings['sale_text']);
        
        echo wp_kses_post($sale_text);
    }
}
