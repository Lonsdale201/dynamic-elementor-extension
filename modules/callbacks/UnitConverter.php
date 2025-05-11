<?php

namespace HelloWP\HWEleWooDynamic\Modules\Callbacks;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class UnitConverter {

    /**
     * Registers the UnitConverter callback with JetEngine.
     */
    public static function register() {
        add_filter( 'jet-engine/listings/allowed-callbacks', [ __CLASS__, 'add_measurement_callback' ] );
        add_filter( 'jet-engine/listing/dynamic-field/callback-args', [ __CLASS__, 'add_measurement_callback_args' ], 10, 3 );
        add_filter( 'jet-engine/listings/allowed-callbacks-args', [ __CLASS__, 'add_measurement_callback_controls' ] );
    }

    public static function add_measurement_callback( $callbacks ) {
        $callbacks['unit_converter'] = 'Convert units';
        return $callbacks;
    }

    public static function add_measurement_callback_controls( $args ) {
        $unit_options = [
            'mm'   => esc_html__( 'Millimeters (mm)', 'hw-elementor-woo-dynamic' ),
            'cm'   => esc_html__( 'Centimeters (cm)', 'hw-elementor-woo-dynamic' ),
            'm'    => esc_html__( 'Meters (m)', 'hw-elementor-woo-dynamic' ),
            'km'   => esc_html__( 'Kilometers (km)', 'hw-elementor-woo-dynamic' ),
            'in'   => esc_html__( 'Inches (in)', 'hw-elementor-woo-dynamic' ),
            'ft'   => esc_html__( 'Feet (ft)', 'hw-elementor-woo-dynamic' ),
            'yd'   => esc_html__( 'Yards (yd)', 'hw-elementor-woo-dynamic' ),
            'mile' => esc_html__( 'Miles', 'hw-elementor-woo-dynamic' ),
            'ml'   => esc_html__( 'Milliliters (ml)', 'hw-elementor-woo-dynamic' ),
            'dl'   => esc_html__( 'Deciliters (dl)', 'hw-elementor-woo-dynamic' ),
            'l'    => esc_html__( 'Liters (l)', 'hw-elementor-woo-dynamic' ),
            'g'    => esc_html__( 'Grams (g)', 'hw-elementor-woo-dynamic' ),
            'dkg'  => esc_html__( 'Dekagrams (dkg)', 'hw-elementor-woo-dynamic' ),
            'kg'   => esc_html__( 'Kilograms (kg)', 'hw-elementor-woo-dynamic' ),
            'lb'   => esc_html__( 'Pounds (lb)', 'hw-elementor-woo-dynamic' ),
            'byte' => esc_html__( 'Bytes', 'hw-elementor-woo-dynamic' ),
            'mb'   => esc_html__( 'Megabytes (MB)', 'hw-elementor-woo-dynamic' ),
            'gb'   => esc_html__( 'Gigabytes (GB)', 'hw-elementor-woo-dynamic' ),
            'w'    => esc_html__( 'Watts (W)', 'hw-elementor-woo-dynamic' ),
            'kw'   => esc_html__( 'Kilowatts (kW)', 'hw-elementor-woo-dynamic' ),
            'mw'   => esc_html__( 'Megawatts (MW)', 'hw-elementor-woo-dynamic' ),
            'gw'   => esc_html__( 'Gigawatts (GW)', 'hw-elementor-woo-dynamic' ),
            'v'    => esc_html__( 'Volts (V)', 'hw-elementor-woo-dynamic' ),
            'kmph' => esc_html__( 'Kilometers per hour (km/h)', 'hw-elementor-woo-dynamic' ),
            'mph'  => esc_html__( 'Miles per hour (mph)', 'hw-elementor-woo-dynamic' ),
            's'    => esc_html__( 'Seconds (s)', 'hw-elementor-woo-dynamic' ),
            'min'  => esc_html__( 'Minutes (min)', 'hw-elementor-woo-dynamic' ),
            'h'    => esc_html__( 'Hours (h)', 'hw-elementor-woo-dynamic' ),
            'd'    => esc_html__( 'Days (d)', 'hw-elementor-woo-dynamic' ),
            'c'    => esc_html__( 'Celsius (°C)', 'hw-elementor-woo-dynamic' ),
            'k'    => esc_html__( 'Kelvin (K)', 'hw-elementor-woo-dynamic' ),
            'f'    => esc_html__( 'Fahrenheit (°F)', 'hw-elementor-woo-dynamic' ),
        ];

        $args['source_measurement_unit'] = [
            'label'   => esc_html__( 'Source Measurement Unit', 'hw-elementor-woo-dynamic' ),
            'type'    => 'select',
            'default' => 'cm',
            'options' => $unit_options,
            'condition' => [
                'dynamic_field_filter' => 'yes',
                'filter_callback'      => [ 'unit_converter' ],
            ],
        ];

        $args['target_measurement_unit'] = [
            'label'   => esc_html__( 'Target Measurement Unit', 'hw-elementor-woo-dynamic' ),
            'type'    => 'select',
            'default' => 'cm',
            'options' => $unit_options,
            'condition' => [
                'dynamic_field_filter' => 'yes',
                'filter_callback'      => [ 'unit_converter' ],
            ],
        ];

        $args['custom_format'] = [
            'label'   => esc_html__( 'Custom Format', 'hw-elementor-woo-dynamic' ),
            'type'    => 'switcher',
            'default' => '',
            'return_value' => 'yes',
            'condition' => [
                'dynamic_field_filter' => 'yes',
                'filter_callback'      => [ 'unit_converter' ],
            ],
        ];

        $args['hide_unit'] = [
            'label'   => esc_html__( 'Hide Unit', 'hw-elementor-woo-dynamic' ),
            'type'    => 'switcher',
            'default' => '',
            'return_value' => 'yes',
            'condition' => [
                'dynamic_field_filter' => 'yes',
                'filter_callback'      => [ 'unit_converter' ],
                'custom_format'        => 'yes',
            ],
        ];

        $args['decimal_point'] = [
            'label'       => esc_html__( 'Decimal Point', 'hw-elementor-woo-dynamic' ),
            'type'        => 'text',
            'default'     => '.',
            'condition'   => [
                'dynamic_field_filter' => 'yes',
                'filter_callback'      => [ 'unit_converter' ],
                'custom_format'        => 'yes',
            ],
        ];

        $args['thousand_separator'] = [
            'label'       => esc_html__( 'Thousand Separator', 'hw-elementor-woo-dynamic' ),
            'type'        => 'text',
            'default'     => ',',
            'condition'   => [
                'dynamic_field_filter' => 'yes',
                'filter_callback'      => [ 'unit_converter' ],
                'custom_format'        => 'yes',
            ],
        ];

        $args['decimal_points'] = [
            'label'       => esc_html__( 'Decimal Points', 'hw-elementor-woo-dynamic' ),
            'type'        => 'number',
            'default'     => 0,
            'min'         => 0,
            'condition'   => [
                'dynamic_field_filter' => 'yes',
                'filter_callback'      => [ 'unit_converter' ],
                'custom_format'        => 'yes',
            ],
        ];

        return $args;
    }

    public static function add_measurement_callback_args( $args, $callback, $settings = [] ) {
        if ( 'unit_converter' === $callback ) {
            $args[] = $settings['source_measurement_unit'] ?? 'cm';
            $args[] = $settings['target_measurement_unit'] ?? 'cm';
            $args[] = $settings['custom_format'] ?? false;
            $args[] = $settings['hide_unit'] ?? false;
            $args[] = $settings['decimal_point'] ?? '.';
            $args[] = $settings['thousand_separator'] ?? ',';
            $args[] = $settings['decimal_points'] ?? 0;
        }
        return $args;
    }

    public static function unit_converter( $field_value = null, $source_unit = 'cm', $target_unit = 'cm', $custom_format = false, $hide_unit = false, $decimal_point = '.', $thousand_separator = ',', $decimal_points = 0 ) {

        // Store the original value for use in the macro
        global $original_field_value;
        $original_field_value = $field_value;
    
        // Remove any non-numeric characters except for dots and commas
        $field_value = preg_replace('/[^0-9.,]/', '', $field_value);
    
        if ( ! is_numeric( str_replace( array( ',', '.' ), '', $field_value ) ) ) {
            return $field_value;
        }
    
        // Replace commas with dots and convert to float
        $field_value = floatval( str_replace( ',', '.', $field_value ) );
    
        // Define categories for units
        $unit_categories = array(
            'length' => array('mm', 'cm', 'm', 'km', 'in', 'ft', 'yd', 'mile'),
            'volume' => array('ml', 'dl', 'l'),
            'weight' => array('g', 'dkg', 'kg', 'lb'),
            'data' => array('byte', 'mb', 'gb'),
            'power' => array('w', 'kw', 'mw', 'gw'),
            'speed' => array('kmph', 'mph'),
            'time' => array('s', 'min', 'h', 'd'),
            'temperature' => array('c', 'k', 'f')
        );
    
        // Find the category of the source and target units
        $source_category = null;
        $target_category = null;
        foreach ($unit_categories as $category => $units) {
            if (in_array($source_unit, $units)) {
                $source_category = $category;
            }
            if (in_array($target_unit, $units)) {
                $target_category = $category;
            }
        }
    
        // If the source and target units are not in the same category, return an error
        if ($source_category !== $target_category) {
            return 'Invalid conversion';
        }
    
        // Conversion factors to base unit
        $conversion_factors_to_base = array(
            'mm'    => 0.001,
            'cm'    => 0.01,
            'm'     => 1,
            'km'    => 1000,
            'in'    => 0.0254,
            'ft'    => 0.3048,
            'yd'    => 0.9144,
            'mile'  => 1609.34,
            'ml'    => 0.000001,
            'dl'    => 0.0001,
            'l'     => 0.001,
            'g'     => 0.001,
            'dkg'   => 0.01,
            'kg'    => 1,
            'lb'    => 0.453592,
            'byte'  => 0.000000001,
            'mb'    => 0.000001,
            'gb'    => 0.001,
            'w'     => 1,
            'kw'    => 1000,
            'mw'    => 1000000,
            'gw'    => 1000000000,
            'v'     => 1,
            'kmph'  => 0.277778,
            'mph'   => 0.44704,
            's'     => 1,
            'min'   => 60,
            'h'     => 3600,
            'd'     => 86400,
            'c'     => 1, // Handle temperature conversion separately
            'k'     => 1,
            'f'     => 1,
        );
    
        // Conversion factors from base unit to target unit
        $conversion_factors_from_base = array(
            'mm'    => 1000,
            'cm'    => 100,
            'm'     => 1,
            'km'    => 0.001,
            'in'    => 39.3701,
            'ft'    => 3.28084,
            'yd'    => 1.09361,
            'mile'  => 0.000621371,
            'ml'    => 1000000,
            'dl'    => 10000,
            'l'     => 1000,
            'g'     => 1000,
            'dkg'   => 100,
            'kg'    => 1,
            'lb'    => 2.20462,
            'byte'  => 1000000000,
            'mb'    => 1000000,
            'gb'    => 1000,
            'w'     => 1,
            'kw'    => 0.001,
            'mw'    => 0.000001,
            'gw'    => 0.000000001,
            'v'     => 1,
            'kmph'  => 3.6,
            'mph'   => 2.23694,
            's'     => 1,
            'min'   => 0.0166667,
            'h'     => 0.000277778,
            'd'     => 0.0000115741,
            'c'     => 1,
            'k'     => 1,
            'f'     => 1,
        );
    
        // Handle temperature conversion separately
        if ( in_array($source_unit, ['c', 'k', 'f']) || in_array($target_unit, ['c', 'k', 'f']) ) {
            // Convert source to Kelvin
            switch ($source_unit) {
                case 'c':
                    $value_in_base_unit = $field_value + 273.15;
                    break;
                case 'f':
                    $value_in_base_unit = ($field_value - 32) / 1.8 + 273.15;
                    break;
                case 'k':
                    $value_in_base_unit = $field_value;
                    break;
            }
            // Convert from Kelvin to target
            switch ($target_unit) {
                case 'c':
                    $converted_value = $value_in_base_unit - 273.15;
                    break;
                case 'f':
                    $converted_value = ($value_in_base_unit - 273.15) * 1.8 + 32;
                    break;
                case 'k':
                    $converted_value = $value_in_base_unit;
                    break;
            }
        } else {
            // Convert source value to base unit
            if ( isset( $conversion_factors_to_base[ $source_unit ] ) ) {
                $value_in_base_unit = $field_value * $conversion_factors_to_base[ $source_unit ];
            } else {
                // If the source unit is not recognized, return the original value
                return $field_value . ' ' . $source_unit;
            }
    
            // Convert value in base unit to target unit
            if ( isset( $conversion_factors_from_base[ $target_unit ] ) ) {
                $converted_value = $value_in_base_unit * $conversion_factors_from_base[ $target_unit ];
            } else {
                // If the target unit is not recognized, return the value in base unit
                return $value_in_base_unit . ' ' . $source_unit;
            }
        }
    
        // Apply custom formatting
        if ( $custom_format ) {
            $converted_value = number_format( $converted_value, $decimal_points, $decimal_point, $thousand_separator );
        }
    
        if ( $custom_format && $hide_unit ) {
            return $converted_value;
        } else {
            return $converted_value . ' ' . $target_unit;
        }
    }
    
}
