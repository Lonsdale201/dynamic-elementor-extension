<?php
namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class LoggedIn extends Condition_Base {

    public static function get_type() {
        return 'general';
    }

    public function get_name() {
        return 'logged_in';
    }

    public function get_label() {
        return esc_html__('Logged In', 'hw-ele-woo-dynamic');
    }

    public function check($args) {
        return is_user_logged_in();
    }
}
