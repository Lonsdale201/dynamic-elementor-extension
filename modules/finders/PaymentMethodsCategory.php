<?php
namespace HelloWP\HWEleWooDynamic\Modules\Finder;

use Elementor\Core\Common\Modules\Finder\Base_Category;
use WC_Payment_Gateways;

class PaymentMethodsCategory extends Base_Category {
    public function get_id() {
        return 'payment-methods';
    }

    public function get_title() {
        return esc_html__('Payment Methods', 'hw-ele-woo-dynamic');
    }

    public function get_category_items(array $options = []) {
        $payment_gateways = WC_Payment_Gateways::instance();
        $gateways = $payment_gateways->payment_gateways();
        $items = [];

        foreach ($gateways as $gateway_id => $gateway) {
            $status = ($gateway->enabled === 'yes') ? 'Active' : 'Inactive';
            $formatted_id = sprintf('%s:%s', $gateway_id, $gateway->id); 
            $items[$formatted_id] = [
                'title' => sprintf('%s (%s) [%s]', $gateway->get_title(), $status, $formatted_id),
                'icon' => 'info-circle-o',
                'url' => admin_url('admin.php?page=wc-settings&tab=checkout&section=' . strtolower($gateway_id)),
                'keywords' => ['payment', 'method', $gateway->get_title(), strtolower($status), $gateway_id]
            ];
        }

        return $items;
    }
}
