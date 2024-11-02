<?php

namespace HelloWP\HWEleWooDynamic\Modules\WPTopBar;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class TopBarSettings {
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_before_admin_bar_render', [$this, 'add_product_info_to_admin_bar'], 100);
    }

    public function add_product_info_to_admin_bar() {
        if (!is_product() || !is_user_logged_in() || !current_user_can('manage_woocommerce')) {
            return;
        }

        global $wp_admin_bar, $product;
        $product_id = get_the_ID();
        $product = wc_get_product($product_id);
        if (!$product) {
            return;
        }

        $options = get_option('dynamic_extension_settings');
        $enabled_tags = $options['enabled_tags'] ?? [];

        $shouldAddProductInfo = !empty($enabled_tags['wp_bar_products_informations_product_type']) || !empty($enabled_tags['wp_bar_products_informations_product_sku']);

        if ($shouldAddProductInfo) {
            $wp_admin_bar->add_node([
                'id'    => 'product_info',
                'title' => __('Product Info', 'hw-ele-woo-dynamic')
            ]);
        }

        // Product Type
        if (!empty($enabled_tags['wp_bar_products_informations_product_type'])) {
            $product_types = wc_get_product_types();
            $product_type_label = $product_types[$product->get_type()] ?? __('Unknown', 'woocommerce');
            $wp_admin_bar->add_node([
                'parent' => 'product_info',
                'id'     => 'product_type',
                'title'  => sprintf(__('Type: %s', 'hw-ele-woo-dynamic'), $product_type_label),
                'href'   => false
            ]);
        }

        // Product SKU
        if (!empty($enabled_tags['wp_bar_products_informations_product_sku'])) {
            $sku_label = __('SKU', 'woocommerce');
            $sku = $product->get_sku() ?: __('N/A', 'woocommerce');
            $wp_admin_bar->add_node([
                'parent' => 'product_info',
                'id'     => 'product_sku',
                'title'  => sprintf(__('%s: %s', 'hw-ele-woo-dynamic'), $sku_label, $sku),
                'href'   => false
            ]);
        }

        $this->add_inventory_node($wp_admin_bar, $product, $enabled_tags);
        $this->add_shipping_class_node($wp_admin_bar, $product, $enabled_tags);
        $this->add_product_status_node($wp_admin_bar, $product, $enabled_tags);
    }

        private function add_shipping_class_node($wp_admin_bar, $product, $enabled_tags) {
            if (!empty($enabled_tags['wp_bar_products_informations_shipping_class']) && !$wp_admin_bar->get_node('shipping_class')) {
                $shipping_class = $product->get_shipping_class() ?: __('No shipping class', 'woocommerce');
                $shipping_class_label = __('Shipping Class', 'hw-ele-woo-dynamic');
                $wp_admin_bar->add_node([
                    'id'     => 'shipping_class',
                    'title'  => sprintf(__('%s: %s', 'hw-ele-woo-dynamic'), $shipping_class_label, $shipping_class),
                    'href'   => false
                ]);
            }
        }

        private function add_product_status_node($wp_admin_bar, $product, $enabled_tags) {
            if (!empty($enabled_tags['wp_bar_products_informations_product_status'])) {
                $product_status = get_post_status($product->get_id());
                $statuses = get_post_statuses();
                $status_label = $statuses[$product_status] ?? __('Unknown', 'woocommerce');
                $product_status_label = __('Product Status', 'hw-ele-woo-dynamic');
        
                $wp_admin_bar->add_node([
                    'id'     => 'product_status',
                    'title'  => sprintf(__('%s: %s', 'hw-ele-woo-dynamic'), $product_status_label, $status_label),
                    'href'   => false
                ]);
            }
        }
    
        private function add_inventory_node($wp_admin_bar, $product, $enabled_tags) {
            if (!empty($enabled_tags['wp_bar_products_informations_product_inventory'])) {
                $low_stock_amount = $product->get_low_stock_amount();
                if ($low_stock_amount === '') {
                    $low_stock_amount = get_option('woocommerce_notify_low_stock_amount');
                }
                $stock_quantity = $product->get_stock_quantity();
                $stock_status = $product->get_stock_status();
                $color = ($stock_status === 'outofstock') ? '#E53935' : ($stock_status === 'onbackorder' ? '#FFB300' : '#43A047');
                $status_text = ($stock_status === 'outofstock') ? __('Out of stock', 'woocommerce') : __('In stock', 'woocommerce');
                if ($stock_status === 'onbackorder') {
                    $status_text = __('On backorder', 'woocommerce');
                } elseif ($stock_quantity !== null && $stock_quantity <= $low_stock_amount) {
                    $status_text = __('Low stock', 'woocommerce') . " ($stock_quantity)";
                } elseif ($stock_quantity > $low_stock_amount || $stock_quantity > 0) {
                    $status_text = __('Stock', 'woocommerce') . " ($stock_quantity)";
                }

                $wp_admin_bar->add_node([
                    'id'     => 'product_stock_status',
                    'title'  => "<div style='background-color: $color; color: #FFFFFF; padding-left: 5px; padding-right: 5px;'>$status_text</div>",
                    'href'   => false
                ]);
            }
        }
    
}
