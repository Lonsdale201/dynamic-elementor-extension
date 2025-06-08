<?php
namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class UserRegisteredDateVisibility extends Base {

    public function get_id() {
        return 'user-registered-date';
    }

    public function get_name() {
        return __( 'User Registration Time Elapsed', 'hw-ele-woo-dynamic' );
    }

    public function get_group() {
        return 'Other';
    }

    public function check( $args = array() ) {
        $current_user = wp_get_current_user();
        if ( ! $current_user || 0 === $current_user->ID ) {
            return false;
        }

        $registered_date = strtotime( $current_user->user_registered );
        $unit = isset( $args['condition_settings']['time_unit'] ) ? $args['condition_settings']['time_unit'] : 'hours';
        $value = isset( $args['condition_settings']['value'] ) ? intval( $args['condition_settings']['value'] ) : 0;

        switch ( $unit ) {
            case 'days':
                $time_offset = $value * DAY_IN_SECONDS;
                break;
            case 'months':
                $time_offset = $value * 30 * DAY_IN_SECONDS;
                break;
            case 'years':
                $time_offset = $value * 365 * DAY_IN_SECONDS;
                break;
            default: // hours
                $time_offset = $value * HOUR_IN_SECONDS;
                break;
        }

        $target_time = $registered_date + $time_offset;
        $current_time = current_time( 'timestamp' );
        $type = isset( $args['type'] ) ? $args['type'] : 'show';

        return ( 'hide' === $type )
            ? $current_time < $target_time
            : $current_time >= $target_time;
    }

    public function get_custom_controls() {
        return [
            'time_unit' => [
                'label'   => __( 'Time Unit', 'hw-ele-woo-dynamic' ),
                'type'    => 'select',
                'options' => [
                    'hours'  => __( 'Hours', 'hw-ele-woo-dynamic' ),
                    'days'   => __( 'Days', 'hw-ele-woo-dynamic' ),
                    'months' => __( 'Months', 'hw-ele-woo-dynamic' ),
                    'years'  => __( 'Years', 'hw-ele-woo-dynamic' ),
                ],
                'default' => 'hours',
            ],
            'value' => [
                'label'   => __( 'Value', 'hw-ele-woo-dynamic' ),
                'type'    => 'number',
                'default' => 0,
            ],
        ];
    }

    public function is_for_fields() {
        return false;
    }

    public function need_value_detect() {
        return false;
    }
}
