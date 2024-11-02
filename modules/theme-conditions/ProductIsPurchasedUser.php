<?php
namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class ProductIsPurchasedUser extends Condition_Base {

    public static function get_type() {
        return 'product';
    }

    public function get_name() {
        return 'is_purchased_by_user';
    }

    public function get_label() {
        return esc_html__('Is Purchased By User', 'hw-ele-woo-dynamic');
    }

    public function check($args) {
        if (!is_user_logged_in()) {
            return false;
        }

        $user_id = get_current_user_id();
        $product_id = get_the_ID();

        if (wc_customer_bought_product('', $user_id, $product_id)) {
            return true;
        }

        return false;
    }
}
