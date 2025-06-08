<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

class PurchasedProducts extends Tag {

    public function get_name() {
        return 'purchased-products';
    }

    public function get_title() {
        return __('Purchased Products', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras-user';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'output_type',
            [
                'label' => __('Output Type', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'ids' => __('Return IDs', 'hw-ele-woo-dynamic'),
                    'titles' => __('Return Titles', 'hw-ele-woo-dynamic'),
                ],
                'default' => 'titles',
            ]
        );

        $this->add_control(
            'linkable',
            [
                'label' => __('Linkable', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => __('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'output_type' => 'titles',
                ],
            ]
        );

        $this->add_control(
            'link_target',
            [
                'label' => __('Link Target', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'product_page' => __('Product Page', 'hw-ele-woo-dynamic'),
                    'order_page' => __('Order Page', 'hw-ele-woo-dynamic'),
                ],
                'default' => 'product_page',
                'condition' => [
                    'linkable' => 'yes',
                ],
            ]
        );
    }

    public function render() {
        $output_type = $this->get_settings('output_type');
        $linkable = $this->get_settings('linkable') === 'yes';
        $link_target = $this->get_settings('link_target');
        $user_id = get_current_user_id();
    
        if (!$user_id) {
            return;
        }

        $product_ids = $this->get_user_purchased_products($user_id);
    
        if ('ids' === $output_type) {
            echo esc_html(implode(', ', $product_ids));
            return;
        }

        $this->render_product_titles($product_ids, $linkable, $link_target);
    }

    protected function get_order_url_for_product($user_id, $product_id) {
        $customer_orders = wc_get_orders([
            'customer' => $user_id,
            'status' => array('wc-completed'),
            'limit' => -1,
            'return' => 'ids',
        ]);
    
        foreach ($customer_orders as $order_id) {
            $order = wc_get_order($order_id);
            foreach ($order->get_items() as $item) {
                if ($product_id == $item->get_product_id()) {
                    return $order->get_view_order_url();
                }
            }
        }
    
        return null;
    }

    protected function get_product_url($product_id, $linkable, $link_target) {
        if (!$linkable) return '';

        if ('product_page' === $link_target) {
            return get_permalink($product_id);
        }

        if ('order_page' === $link_target) {
            return $this->get_order_url_for_product(get_current_user_id(), $product_id);
        }

        return '';
    }

    protected function render_product_titles($product_ids, $linkable, $link_target) {
        foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id);
            if (!$product) continue;
    
            $product_name = esc_html($product->get_name());
            $url = esc_url($this->get_product_url($product_id, $linkable, $link_target));
    
            echo $url ? wp_kses_post("<a href='$url'>$product_name</a>, ") : esc_html("$product_name, ");
        }
    }
    

    protected function get_user_purchased_products($user_id) {
        $customer_orders = wc_get_orders([
            'customer' => $user_id,
            'status' => array('wc-completed'),
            'limit' => -1,
            'return' => 'ids',
        ]);

        $product_ids = [];
        foreach ($customer_orders as $order_id) {
            $order = wc_get_order($order_id);
            foreach ($order->get_items() as $item) {
                $product_ids[] = $item->get_product_id();
            }
        }

        return array_unique($product_ids);
    }
}