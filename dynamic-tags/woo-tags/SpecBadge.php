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

        $exclude_options = [
            'featured' => __('Exclude the Featured', 'hw-ele-woo-dynamic'),
            'sold_individually' => __('Exclude Sold Individually', 'hw-ele-woo-dynamic'),
            'digital' => __('Exclude Digital', 'hw-ele-woo-dynamic'),
            'virtual' => __('Exclude Virtual', 'hw-ele-woo-dynamic'),
            'external' => __('Exclude External', 'hw-ele-woo-dynamic'),
            'sale' => __('Exclude Sale', 'hw-ele-woo-dynamic'),
        ];
    
        if (self::is_subscriptions_active()) {
            $exclude_options['subscription'] = __('Exclude Subscriptions', 'hw-ele-woo-dynamic');
        }
        if (self::is_product_bundles_active()) {
            $exclude_options['bundle'] = __('Exclude Bundles', 'hw-ele-woo-dynamic');
        }
    
        $this->add_control(
            'exclude_features',
            [
                'label' => __('Exclude Features', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $exclude_options,
                'label_block' => true,
                'description' => __('Specify what features should not be included in the badge.', 'hw-ele-woo-dynamic'),
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
    
        $this->add_control(
            'external_custom_text',
            [
                'label' => __('External Product Custom Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => __('External', 'hw-ele-woo-dynamic'),
                'condition' => [
                    'enable_custom_text' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'sale_custom_text',
            [
                'label' => __('Sale Product Custom Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Sale', 'hw-ele-woo-dynamic'),
                'condition' => [
                    'enable_custom_text' => 'yes',
                ],
            ]
        );        
    
        if (self::is_subscriptions_active()) {
            $this->add_control(
                'subscription_custom_text',
                [
                    'label' => __('Subscription Product Custom Text', 'hw-ele-woo-dynamic'),
                    'type' => Controls_Manager::TEXT,
                    'default' => __('Subscription', 'hw-ele-woo-dynamic'),
                    'condition' => [
                        'enable_custom_text' => 'yes',
                    ],
                ]
            );
        }
    
        if (self::is_product_bundles_active()) {
            $this->add_control(
                'bundle_custom_text',
                [
                    'label' => __('Bundle Product Custom Text', 'hw-ele-woo-dynamic'),
                    'type' => Controls_Manager::TEXT,
                    'default' => __('Bundle', 'hw-ele-woo-dynamic'),
                    'condition' => [
                        'enable_custom_text' => 'yes',
                    ],
                ]
            );
        }
    }

    public function render() {
        $settings = $this->get_settings();
        $product = wc_get_product(get_the_ID());
    
        if (!$product) {
            return;
        }
    
        $badges = [];
        $excludes = (array) $settings['exclude_features'];
    
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
    
        if ($product->get_type() === 'external' && !in_array('external', $excludes)) {
            $badges[] = $settings['enable_custom_text'] === 'yes' ? $settings['external_custom_text'] : __('External', 'woocommerce');
        }
    
        if ($product->is_on_sale() && !in_array('sale', $excludes)) {
            $badges[] = $settings['enable_custom_text'] === 'yes' ? $settings['sale_custom_text'] : __('Sale', 'hw-ele-woo-dynamic');
        }
    
        // Check for subscription products
        if ($this->is_subscription_product($product) && !in_array('subscription', $excludes)) {
            $badges[] = $settings['enable_custom_text'] === 'yes' ? $settings['subscription_custom_text'] : __('Subscription', 'hw-ele-woo-dynamic');
        }
    
        // Check for bundle products
        if ($this->is_bundle_product($product) && !in_array('bundle', $excludes)) {
            $badges[] = $settings['enable_custom_text'] === 'yes' ? $settings['bundle_custom_text'] : __('Bundle', 'hw-ele-woo-dynamic');
        }
    
        $badges_safe = array_map('wp_kses_post', $badges);
        echo implode(', ', $badges_safe);
    }
    

    /**
     * Check if a product is a subscription.
     *
     * @param WC_Product $product
     * @return bool
     */
    private function is_subscription_product($product) {
        if (!self::is_subscriptions_active()) {
            return false;
        }

        return $product->is_type('subscription') || $product->is_type('variable-subscription');
    }

    /**
     * Check if a product is a bundle.
     *
     * @param WC_Product $product
     * @return bool
     */
    private function is_bundle_product($product) {
        if (!self::is_product_bundles_active()) {
            return false;
        }

        return $product->is_type('bundle');
    }

    /**
     * Check if WooCommerce Subscriptions is active.
     *
     * @return bool
     */
    public static function is_subscriptions_active() {
        return function_exists('wcs_get_users_subscriptions');
    }

    /**
     * Check if WooCommerce Product Bundles is active.
     *
     * @return bool
     */
    public static function is_product_bundles_active() {
        return class_exists('WC_Product_Bundle');
    }
}
