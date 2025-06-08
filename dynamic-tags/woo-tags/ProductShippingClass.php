<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Dynamic tag: Product Shipping Class
 *
 * Returns the product’s shipping class information: name, slug, or description.
 * If no class is set, outputs nothing, so Elementor fallback can apply.
 */
class ProductShippingClass extends Tag {

    public function get_name(): string {
        return 'product-shipping-class';
    }

    public function get_title(): string {
        return __( 'Product Shipping Class', 'hw-ele-woo-dynamic' );
    }

    public function get_group(): string {
        return 'woo-extras';
    }

    public function get_categories(): array {
        return [ Module::TEXT_CATEGORY ];
    }

    protected function _register_controls(): void {
        $this->add_control(
            'return_value',
            [
                'label'   => __( 'Return Value', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'name'        => __( 'Name', 'hw-ele-woo-dynamic' ),
                    'slug'        => __( 'Slug', 'hw-ele-woo-dynamic' ),
                    'description' => __( 'Description', 'hw-ele-woo-dynamic' ),
                ],
                'default' => 'name',
            ]
        );
    }

    public function render(): void {
        $product = wc_get_product( get_the_ID() );
        if ( ! $product instanceof WC_Product ) {
            return;
        }

        $shipping_class_id = $product->get_shipping_class_id();
        if ( ! $shipping_class_id ) {
            return;
        }

        $term = get_term( $shipping_class_id, 'product_shipping_class' );
        if ( ! $term || is_wp_error( $term ) ) {
            return;
        }

        $return_value = $this->get_settings_for_display( 'return_value' );
        $value = '';

        switch ( $return_value ) {
            case 'slug':
                $value = $term->slug;
                break;
            case 'description':
                $value = $term->description;
                break;
            case 'name':
            default:
                $value = $term->name;
                break;
        }

        if ( '' !== trim( $value ) ) {
            echo esc_html( $value );
        }
        
    }
}
