<?php
namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class UserStatus extends Condition_Base {

    public static function get_type() {
        return 'general';
    }

    public function get_name() {
        return 'user_status';
    }

    public function get_label() {
        return esc_html__('User Status', 'hw-elementor-woo-dynamic');
    }

    public function check($args) {
        return true;
    }

    public function register_sub_conditions() {
        $this->register_sub_condition(new LoggedIn());
        $this->register_sub_condition(new LoggedOut());
    }
}
