<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class LoggedOut extends Condition_Base {

    public static function get_type() {
        return 'general';
    }

    public function get_name() {
        return 'logged_out';
    }

    public function get_label() {
        return esc_html__('Logged Out', 'hw-ele-woo-dynamic');
    }

    public function check($args) {
        return !is_user_logged_in();
    }
}
