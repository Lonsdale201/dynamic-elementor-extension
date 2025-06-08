<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

class FreeShippingAmount extends Tag {

    public function get_name() {
        return 'free-shipping-amount';
    }

    public function get_title() {
        return esc_html__('Free Shipping Amount', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::NUMBER_CATEGORY, Module::TEXT_CATEGORY];
    }

    private function get_free_shipping_min_amount() {
        $free_shipping_methods = [];
    
        $shipping_zones = \WC_Shipping_Zones::get_zones();
        foreach ($shipping_zones as $zone) {
            foreach ($zone['shipping_methods'] as $method) {
                if ('free_shipping' === $method->id && 'yes' === $method->enabled) {
                    if (!empty($method->min_amount)) {
                        $cleaned_amount = preg_replace('/[^0-9.]+/', '', $method->min_amount);
                        $free_shipping_methods[] = $cleaned_amount;
                    }
                }
            }
        }
    
        if (!empty($free_shipping_methods)) {
            return min($free_shipping_methods);
        }
    
        return false;
    }
    
    public function render() {
        $min_amount = $this->get_free_shipping_min_amount();
    
        if ($min_amount !== false) {
            echo wp_kses_post(wc_price($min_amount));
        }
    }
    
}
