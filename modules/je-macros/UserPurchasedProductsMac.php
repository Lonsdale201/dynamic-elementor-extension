<?php

namespace HelloWP\HWEleWooDynamic\Modules\JEMacros;

class UserPurchasedProductsMac extends \Jet_Engine_Base_Macros {

    public function macros_tag() {
        return 'wc_current_user_purchased_products';
    }

    public function macros_name() {
        return 'WC Current User Purchased Products';
    }

    public function macros_args() {
        return array();
    }

    public function macros_callback( $args = array() ) {
        $user_id = get_current_user_id();

        if ( !$user_id ) {
            return 'User is not logged in';
        }

        $product_ids = $this->get_user_purchased_products($user_id);

        if ( empty($product_ids) ) {
            return 'User has no purchased products';
        }

        return implode(', ', $product_ids);
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
