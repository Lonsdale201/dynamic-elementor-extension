<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Order_Query;

class CompletedOrder extends Tag {

    public function get_name() {
        return 'completed-order';
    }

    public function get_title() {
        return esc_html__('Completed Order', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras-user';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY, Module::NUMBER_CATEGORY];
    }

    public function render() {
        $user_id = get_current_user_id();

        if (!$user_id) {
            echo '';
            return;
        }

        $query = new WC_Order_Query([
            'customer_id' => $user_id,
            'status' => 'completed',
            'return' => 'ids',
            'limit' => -1, 
        ]);

        $orders = $query->get_orders();

        $completed_orders_count = is_array($orders) ? count($orders) : 0;

        echo esc_html($completed_orders_count);
    }
}
