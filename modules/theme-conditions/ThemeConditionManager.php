<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class ThemeConditionManager {
    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        add_action('elementor/theme/register_conditions', [$this, 'register_conditions'], 995);
    }

    public function register_conditions($conditions_manager) {
        $general_condition = $conditions_manager->get_condition('general');
        if ($general_condition) {
            $general_condition->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\UserRoles());
            $general_condition->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\UserStatus());

            if (function_exists('wcs_get_users_subscriptions')) {
                $general_condition->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\NotHaveActiveSubs());
            }
        }
    
        $product_condition = $conditions_manager->get_condition('product');
        if ($product_condition) {
            $product_condition->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\WooExtrasConditions());

            if (Dependencies::is_name_your_price_active()) {
                $product_condition->register_sub_condition(new \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ProductIsNameYourPrice());
            }
        }
    }
    
}
