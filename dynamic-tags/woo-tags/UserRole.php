<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use WP_User;

class UserRole extends Tag {

    public function get_name() {
        return 'user-role';
    }

    public function get_title() {
        return __('User Role', 'hw-elementor-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras-user';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    public function render() {
        if (!is_user_logged_in()) {
            echo '';
            return;
        }

        $user = wp_get_current_user();
        if (empty($user->roles)) {
            echo '';
            return;
        }

        $roles = $user->roles; 
        $role_names = array_map(function ($role) {
            $wp_roles = wp_roles();
            return isset($wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role]) : '';
        }, $roles);

        echo esc_html(implode(', ', $role_names));
    }
}
