<?php
namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class WooExtrasConditions extends Condition_Base {

    public static function get_type() {
        return 'woocommerce';
    }

    public function get_name() {
        return 'product_extras';
    }

    public function get_label() {
        return esc_html__('Product Extras', 'hw-ele-woo-dynamic');
    }

    public function check($args) {
        return true;
    }
    
    public function register_sub_conditions() {
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsDigital());
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsDownloadable());
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsFeatured());
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\IsProductOutOfStock());
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsOnSale());
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsVariable());
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsExternal());
        $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsPurchasedUser());

        if ( class_exists( 'WC_Subscriptions' ) ) {
            $this->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsSubscriptions());
        }
    }
}
