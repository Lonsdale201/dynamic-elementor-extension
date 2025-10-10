<?php

namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class UserRoleTypes extends Condition_Base {

    private $role;
    private $condition_name;

    public function __construct( $role ) {
        $this->role = $role;
        $this->condition_name = 'user_role_' . \sanitize_key( $role );

        parent::__construct();
    }

    public static function get_type() {
        return 'general';
    }

    public function get_name() {
        return $this->condition_name;
    }

    public function get_label() {
        return $this->get_role_label( $this->role );
    }

    public function check( $args ) {
        $current_user = wp_get_current_user();
        return in_array( $this->role, (array) $current_user->roles );
    }

    protected function get_role_label( $role ) {
        $wp_roles = wp_roles();
        $role_names = $wp_roles->get_names();
        return isset( $role_names[ $role ] ) ? translate_user_role( $role_names[ $role ] ) : ucfirst( $role );
    }
}
