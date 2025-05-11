<?php
namespace HelloWP\HWEleWooDynamic\Modules\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class ProductIsSubscriptions extends Condition_Base {

public static function get_type() {
    return 'product';
}

public function get_name() {
    return 'is_product_subscription';
}

public function get_label() {
    return esc_html__( 'Is Product Subscription', 'hw-elementor-woo-dynamic' );
}

public function check( $args ) {
    $product = wc_get_product();
    if ( !$product ) {
        return false;
    }
    
    return ( $product->get_type() === 'subscription' || $product->get_type() === 'variable-subscription' );
 }
}
