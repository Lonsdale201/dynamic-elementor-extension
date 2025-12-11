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
        $raw_value = isset( $args['value'] ) ? (string) $args['value'] : '';
        $current_url = isset( $_SERVER['REQUEST_URI'] )
            ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) )
            : '';

        $type = isset( $args['type'] ) ? $args['type'] : 'show';

        $needle_list = preg_split( '/[\r\n,]+/', $raw_value );
        if ( ! is_array( $needle_list ) || empty( array_filter( $needle_list, 'strlen' ) ) ) {
            $needle_list = [ $raw_value ];
        }

        $needle_list = array_filter(
            array_map(
                static function ( $needle ) {
                    $needle = trim( (string) $needle );
                    return '' !== $needle ? sanitize_text_field( $needle ) : '';
                },
                $needle_list
            )
        );

        $is_param_present = false;
        foreach ( $needle_list as $needle ) {
            if ( false !== strpos( $current_url, $needle ) ) {
                $is_param_present = true;
                break;
            }
        }

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
