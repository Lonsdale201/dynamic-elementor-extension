<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

/**
 * Dynamic Tag: Product Weight with unit conversion
 */
class ProductWeight extends Tag {

    public function get_name() {
        return 'product-weight';
    }

    public function get_title() {
        return __( 'Product Weight', 'hw-ele-woo-dynamic' );
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
                    ''      => esc_html__( 'Store unit (no conversion)', 'hw-ele-woo-dynamic' ),
                    'kg'    => 'kg',
                    'g'     => 'g',
                    'dkg'   => 'dkg',
                    'lbs'   => 'lbs',
                    'oz'    => 'oz',
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
                    ''      => esc_html__( '—', 'hw-ele-woo-dynamic' ),
                    'kg'    => 'kg',
                    'g'     => 'g',
                    'dkg'   => 'dkg',
                    'lbs'   => 'lbs',
                    'oz'    => 'oz',
                ],
                'default' => '',
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings();
        $product  = wc_get_product( get_the_ID() );
        if ( ! $product instanceof WC_Product || ! $product->has_weight() ) {
            return;
        }

        $raw = $product->get_weight();
        if ( '' === $raw || ! is_numeric( $raw ) ) {
            return;
        }
        $value = (float) $raw;

        $store_unit = get_option( 'woocommerce_weight_unit' );
        $from = $settings['convert_from'] ?: $store_unit;
        $to   = $settings['convert_to'] ?: $store_unit;

        // conversion factors to kilograms
        $f = [
            'kg'  => 1.0,
            'g'   => 0.001,
            'dkg' => 0.01,
            'lbs' => 0.45359237,
            'oz'  => 0.0283495231,
        ];

        if ( isset( $f[ $from ], $f[ $to ] ) && ( $from !== '' && $to !== '' ) ) {
            $kg        = $value * $f[ $from ];
            $converted = $kg / $f[ $to ];
            $value     = rtrim( rtrim( number_format( $converted, 4, '.', '' ), '0' ), '.' );
        }

        // append unit
        echo esc_html( $value ) . ' ' . esc_html( $to );
    }
}
