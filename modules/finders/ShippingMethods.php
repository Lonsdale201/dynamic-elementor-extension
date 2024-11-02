<?php
namespace HelloWP\HWEleWooDynamic\Modules\Finder;

use Elementor\Core\Common\Modules\Finder\Base_Category;
use WC_Shipping;

class ShippingMethods extends Base_Category {
    public function get_id() {
        return 'shipping-methods';
    }

    public function get_title() {
        return esc_html__('Shipping Methods', 'hw-ele-woo-dynamic');
    }

    public function get_category_items(array $options = []) {
        $shipping = WC_Shipping::instance();
        $methods = $shipping->load_shipping_methods();
        $items = [];

        foreach ($methods as $method_id => $method) {
            if (isset($method->enabled)) {
                $status = ($method->enabled === 'yes') ? __('Active', 'hw-ele-woo-dynamic') : __('Inactive', 'hw-ele-woo-dynamic');
                $title = method_exists($method, 'get_method_title') ? $method->get_method_title() : $method->title; 
                $items[$method_id] = [
                    'title' => sprintf('%s (%s) [%s]', $title, $status, $method_id),
                    'icon' => 'info-circle-o',
                    'url' => admin_url('admin.php?page=wc-settings&tab=shipping&section=' . strtolower($method_id)),
                    'keywords' => ['shipping', 'method', $title, strtolower($status), $method_id] 
                ];
            }
        }

        return $items;
    }
}
