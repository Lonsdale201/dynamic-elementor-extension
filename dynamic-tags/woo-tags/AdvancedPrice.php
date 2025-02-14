<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;
use WC_Tax;

class AdvancedPrice extends Tag {

    public function get_name() {
        return 'advanced-price';
    }

    public function get_title() {
        return __('Advanced Price', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'show_tax_info',
            [
                'label' => __('Show Tax Information', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => __('No', 'hw-ele-woo-dynamic'),
                'default' => '',
                'return_value' => 'yes',
            ]
        );
    }

    public function render() {
        $product = wc_get_product(get_the_ID());

        if (!$product) {
            return;
        }

        $price_html = $product->get_price_html();
        $show_tax_info = $this->get_settings('show_tax_info');

        // Show the regular price HTML
        echo wp_kses_post($price_html);

        // Skip tax information for variable products
        if ($product->is_type('variable')) {
            return;
        }

        // Show tax information if enabled
        if ('yes' === $show_tax_info) {
            $this->show_tax_info($product);
        }
    }

    /**
     * Displays tax information below the price.
     *
     * @param WC_Product $product
     */
    private function show_tax_info($product) {
        $tax_amount = $this->get_product_tax($product);
        $tax_display = get_option('woocommerce_tax_display_cart');

        if ($tax_amount > 0) {
            if ($tax_display === 'incl') {
                $formatted_tax = wc_price($tax_amount);
                $tax_info = sprintf(__('(includes %s)', 'woocommerce'), $formatted_tax);

                echo '<small class="includes_tax"> ' . wp_kses_post($tax_info) . '</small>';
            } else {
                $tax_info = __('(ex. VAT)', 'woocommerce');
                echo '<small class="excludes_tax"> ' . esc_html($tax_info) . '</small>';
            }
        }
    }

  
    /**
     * Calculate the tax amount for the product.
     *
     * @param WC_Product $product The WooCommerce product object.
     * @return float The calculated tax amount.
     */
    private function get_product_tax($product) {
        // Get the applicable tax rates for the product's tax class.
        $tax_rates = WC_Tax::get_rates($product->get_tax_class());

        // Retrieve the base price and cast it to float to ensure proper arithmetic operations.
        $base_price = (float) $product->get_price('edit');

        // Calculate the taxes using WooCommerce's built-in function.
        $taxes = WC_Tax::calc_tax($base_price, $tax_rates, wc_prices_include_tax());

        // Return the sum of calculated taxes.
        return array_sum($taxes);
    }

}
