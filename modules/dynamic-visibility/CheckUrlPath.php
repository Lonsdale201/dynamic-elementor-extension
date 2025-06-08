<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class CheckUrlPath extends Base {

    /**
     * Retrieves a unique identifier for this condition.
     *
     * @return string
     */
    public function get_id() {
        return 'check-url-path';
    }

    /**
     * Retrieves the display name for this condition.
     *
     * @return string
     */
    public function get_name() {
        return __('Check URL Path', 'hw-ele-woo-dynamic');
    }

    /**
     * Assigns this condition to the appropriate group.
     *
     * @return string
     */
    public function get_group() {
        return 'Other';
    }

    /**
     * Checks if the current URL contains the specified value.
     *
     * @param array $args Array containing parameters for display conditions.
     * @return bool True if the value is found and the condition is set to "show", false otherwise.
     */
    public function check($args = []) {
        $value_to_check = isset( $args['value'] ) ? $args['value'] : '';
        $current_url = isset( $_SERVER['REQUEST_URI'] ) 
            ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) 
            : '';

        $type = isset( $args['type'] ) ? $args['type'] : 'show';
        $is_param_present = strpos( $current_url, $value_to_check ) !== false;

        return ( 'hide' === $type ) ? ! $is_param_present : $is_param_present;

    }

    /**
     * Indicates that this condition is not field-specific.
     *
     * @return bool
     */
    public function is_for_fields() {
        return false;
    }

    /**
     * Indicates that this condition requires value detection.
     *
     * @return bool
     */
    public function need_value_detect() {
        return true;
    }
}
