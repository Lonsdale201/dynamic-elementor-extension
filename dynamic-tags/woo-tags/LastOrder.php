<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Customer;

class LastOrder extends Tag {

    public function get_name() {
        return 'last-order';
    }

    public function get_title() {
        return esc_html__('Last Order', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras-user';
    }

    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    protected function _register_controls() {
        $this->add_control(
            'order_data',
            [
                'label' => esc_html__('Order Data', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'id' => esc_html__('Order ID', 'hw-ele-woo-dynamic'),
                    'status' => esc_html__('Order Status', 'hw-ele-woo-dynamic'),
                    'date' => esc_html__('Order Date', 'hw-ele-woo-dynamic'),
                    'items' => esc_html__('Order Items', 'hw-ele-woo-dynamic'),
                    'item_count' => esc_html__('Item Count', 'hw-ele-woo-dynamic'), 
                    'total_amount' => esc_html__('Order Amount', 'hw-ele-woo-dynamic'),
                    'tax_amount' => esc_html__('Order Tax Amount', 'hw-ele-woo-dynamic'),
                    'shipping_method' => esc_html__('Shipping method', 'hw-ele-woo-dynamic'),
                    'payment_method' => esc_html__('Payment method', 'hw-ele-woo-dynamic'),
                ],
                'default' => 'id',
            ]
        );

        $this->add_control(
            'linkable',
            [
                'label' => esc_html__('Linkable', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => esc_html__('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'order_data' => 'id',
                ],
            ]
        );
    }

    public function render() {
        $settings = $settings = $this->get_settings_for_display();
        $user_id = get_current_user_id();
    
        if (!$user_id) {
            echo '';
            return;
        }
    
        $customer = new WC_Customer($user_id);
        $last_order = $customer->get_last_order();
    
        if (!$last_order) {
            echo '';
            return;
        }
    
        switch ($settings['order_data']) {
            case 'id':
                $order_id = $last_order->get_id();
                if ('yes' === $settings['linkable']) {
                    $order_url = $last_order->get_view_order_url();
                    echo '<a href="' . esc_url($order_url) . '">' . esc_html($order_id) . '</a>';
                } else {
                    echo esc_html($order_id);
                }
                break;
            case 'status':
                $order_status = $last_order->get_status();
                $human_readable_status = wc_get_order_status_name($order_status);
                echo esc_html($human_readable_status);
                break;
            case 'date':
                $order_date = $last_order->get_date_created()->date_i18n(get_option('date_format'));
                echo esc_html($order_date);
                break;
            case 'items':
                $items = $last_order->get_items();
                $item_names = array_map(function($item) { return $item->get_name(); }, $items);
                echo esc_html(implode(', ', $item_names));
                break;
            default:
                echo '';
                break;
            case 'item_count':
                $items = $last_order->get_items();
                echo count($items);
                break;
            case 'total_amount':
                $total_amount = $last_order->get_total();
                echo wp_kses_post(wc_price($total_amount));
                break;
            case 'tax_amount':
                $tax_amount = $last_order->get_total_tax();
                echo wp_kses_post(wc_price($tax_amount));
                break;
            case 'shipping_method':
                $shipping_methods = $last_order->get_items('shipping');
                $shipping_method_names = [];
                foreach ($shipping_methods as $shipping_method) {
                    $shipping_method_names[] = $shipping_method->get_name();
                }
                echo esc_html(implode(', ', $shipping_method_names));
                break;
            case 'payment_method':
                $payment_method = $last_order->get_payment_method_title();
                echo esc_html($payment_method);
                break;
                
        }
    }
    
    
}
