<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

class CustomerLogout extends Tag {

    public function get_name() {
        return 'customer-logout';
    }

    public function get_title() {
        return esc_html__('Customer Logout URL', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras-user';
    }

    public function get_categories() {
        return [ Module::URL_CATEGORY ];
    }

    public function render() {
        $logout_url = wc_logout_url();
        echo esc_url($logout_url);
    }
    
}
