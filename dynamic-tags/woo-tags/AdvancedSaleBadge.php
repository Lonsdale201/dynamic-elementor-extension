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
        return esc_html__('Advanced Sale Badge', 'hw-elementor-woo-dynamic');
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
                'label' => esc_html__('Sale Text', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'Sale!',
                'placeholder' => 'Sale!',
                'description' => esc_html__('Use {sale_number} to display the amount saved. Use {sale_percentage} to display the sale percentage.', 'hw-elementor-woo-dynamic'),
            ]
        );

        if (class_exists('WC_Memberships')) {
            $this->add_control(
                'membership_sale_text',
                [
                    'label' => esc_html__('Membership Sale Text', 'hw-elementor-woo-dynamic'),
                    'type' => Controls_Manager::TEXTAREA,
                    'default' => 'Exclusive Sale for Members: {sale_percentage}% off!',
                    'placeholder' => 'Exclusive Sale for Members: {sale_percentage}% off!',
                    'description' => esc_html__('Use {sale_number} to display the amount saved. Use {sale_percentage} to display the sale percentage. This text is used when the product is discounted exclusively for members.', 'hw-elementor-woo-dynamic'),
                ]
            );
        }

        $this->add_control(
            'free_text',
            [
                'label' => esc_html__('Free Text', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Free!', 'hw-elementor-woo-dynamic'),
                'description' => esc_html__('Text to display when the product is free.', 'hw-elementor-woo-dynamic'),
            ]
        );

        $this->add_control(
            'format_currency',
            [
                'label' => esc_html__('Currency and Format', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'hw-elementor-woo-dynamic'),
                'label_off' => esc_html__('No', 'hw-elementor-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => esc_html__('Enable this to format the sale number as a price with currency.', 'hw-elementor-woo-dynamic'),
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings_for_display();
        $product = wc_get_product(get_the_ID());
        $user_id = get_current_user_id();
        
        if (!$product) {
            return; 
        }
        
        $is_on_sale = $product->is_on_sale();
        $is_discounted = false;
        $regular_price = 0;
        $sale_price = 0;
        $sale_number = 0;
        $sale_percentage = 0;
        $display_text = '';
    
        if ($product->is_type('variable')) {
            $has_sale_price = false;
            foreach ($product->get_available_variations() as $variation) {
                $variation_obj = wc_get_product($variation['variation_id']);
                if ($variation_obj->get_sale_price()) {
                    $has_sale_price = true;
                    $regular_price = $variation_obj->get_regular_price();
                    $sale_price = $variation_obj->get_sale_price();
                    break;
                }
            }
            if (!$has_sale_price) {
                $regular_price = $product->get_regular_price();
                $sale_price = $product->get_sale_price();
            }
        } else {
            $regular_price = $product->get_regular_price();
            $sale_price = $product->get_sale_price();
        }
    
        if ($regular_price > 0 && $sale_price >= 0 && $regular_price > $sale_price) {
            $sale_number = $regular_price - $sale_price;
            $sale_percentage = ($regular_price > 0) ? round(($sale_number / $regular_price) * 100) : 0;
        }
    
        if (class_exists('WC_Memberships') && $user_id) {
            $is_discounted = wc_memberships_user_has_member_discount($product->get_id());
        }
    
        if ($sale_price == 0) {
            echo wp_kses_post($settings['free_text']);
            return;
        } elseif ($is_on_sale || $is_discounted) {
            $display_text = $is_discounted ? $settings['membership_sale_text'] : $settings['sale_text'];
        }
    
        if ('yes' === $settings['format_currency'] && $sale_number > 0) {
            $sale_number = wc_price($sale_number);
        }
    
        $display_text = str_replace(['{sale_number}', '{sale_percentage}'], [$sale_number, $sale_percentage], $display_text);
    
        echo wp_kses_post($display_text);
    }

    
}