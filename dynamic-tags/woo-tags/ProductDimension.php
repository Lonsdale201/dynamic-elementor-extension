<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

/**
 * Dynamic Tag: Product Dimension (L x W x H)
 */
class ProductDimension extends Tag {

    public function get_name() {
        return 'product-dimension';
    }

    public function get_title() {
        return __( 'Product Dimension', 'hw-ele-woo-dynamic' );
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    protected function _register_controls() {
        // Convert From unit
        $this->add_control(
            'convert_from',
            [
                'label'   => esc_html__( 'Convert From', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    ''   => esc_html__( 'Store unit (no conversion)', 'hw-ele-woo-dynamic' ),
                    'mm' => 'mm',
                    'cm' => 'cm',
                    'm'  => 'm',
                    'in' => 'in',
                    'yd' => 'yd',
                ],
                'default' => '',
            ]
        );

        // Convert To unit
        $this->add_control(
            'convert_to',
            [
                'label'   => esc_html__( 'Convert To', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    ''   => esc_html__( '—', 'hw-ele-woo-dynamic' ),
                    'mm' => 'mm',
                    'cm' => 'cm',
                    'm'  => 'm',
                    'in' => 'in',
                    'yd' => 'yd',
                ],
                'default' => '',
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings();
        $product  = wc_get_product( get_the_ID() );
        if ( ! $product instanceof WC_Product ) {
            return;
        }

        // Raw dimensions
        $raw_l = $product->get_length();
        $raw_w = $product->get_width();
        $raw_h = $product->get_height();

        // If nothing
        if ( ( $raw_l === '' || ! is_numeric( $raw_l ) ) &&
             ( $raw_w === '' || ! is_numeric( $raw_w ) ) &&
             ( $raw_h === '' || ! is_numeric( $raw_h ) ) ) {
            return;
        }

        $from = $settings['convert_from'];
        $to   = $settings['convert_to'];

        // Conversion factors to meters
        $f = [
            'mm' => 0.001,
            'cm' => 0.01,
            'm'  => 1.0,
            'in' => 0.0254,
            'yd' => 0.9144,
        ];

        // Helper for conversion: float|string -> string
        $convert_value = function( $raw ) use ( $from, $to, $f ) {
            if ( '' === $raw || ! is_numeric( $raw ) ) {
                return '';
            }
            $val = (float) $raw;
            if ( $from && $to && isset( $f[ $from ], $f[ $to ] ) ) {
                $meters    = $val * $f[ $from ];
                $converted = $meters / $f[ $to ];
                // up to 4 decimals, trim zeros
                $val = rtrim( rtrim( number_format( $converted, 4, '.', '' ), '0' ), '.' );
            }
            return $val;
        };

        $l = $convert_value( $raw_l );
        $w = $convert_value( $raw_w );
        $h = $convert_value( $raw_h );

        // Build output LxWxH (empty parts will be skipped)
        $parts = array_filter( [ $l, $w, $h ], function( $v ) { return '' !== $v; } );
        if ( empty( $parts ) ) {
            return;
        }

        // Preserve LxWxH order, even if some missing
        // If a dimension is missing, we still include empty placeholder
        $output = sprintf(
            '%s x %s x %s',
            $l !== '' ? $l : '',
            $w !== '' ? $w : '',
            $h !== '' ? $h : ''
        );

        echo esc_html( $output );
    }
}
