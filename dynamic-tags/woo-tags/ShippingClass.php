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
 * Dynamic tag: Shipping Class
 *
 * Returns the product’s shipping class information: name, slug or description.
 */
class ShippingClass extends Tag {

    public function get_name() {
        return 'shipping-class';
    }

    public function get_title() {
        return __( 'Shipping Class', 'hw-ele-woo-dynamic' );
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    protected function _register_controls() {
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

    public function render() {
        $product = wc_get_product( get_the_ID() );
        if ( ! $product instanceof WC_Product ) {
            return;
        }

        $class_id = $product->get_shipping_class_id();
        if ( ! $class_id ) {
            return;
        }

        $term = get_term( $class_id, 'product_shipping_class' );
        if ( ! $term || is_wp_error( $term ) ) {
            return;
        }

        $value = '';
        switch ( $this->get_settings( 'return_value' ) ) {
            case 'slug':
                $value = $term->slug;
                break;
            case 'description':
                $value = $term->description;
                break;
            case 'name':
            default:
                $value = $term->name;
        }

        echo esc_html( $value );
    }
}
