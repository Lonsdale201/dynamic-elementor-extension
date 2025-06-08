<?php
namespace HelloWP\HWEleWooDynamic\Modules\Helpers;

use ReflectionClass;
use WC_Form_Handler;

/**
 * Segít egyszerre több terméket beletenni a kosárba URL alapján,
 * és a végén redirectel a kosárra vagy a checkoutra.
 */
class CartHelper {

    /**
     * Regisztrálja a hookokat.
     */
    public static function init(): void {
        add_action( 'wp_loaded', [ __CLASS__, 'handle_add_to_cart' ], 15 );
    }

    public static function handle_add_to_cart(): void {
        // Csak WooCommerce handler esetén folytatjuk
        if ( ! class_exists( 'WC_Form_Handler' ) ) {
            return;
        }

        // 1) CSRF-ellenőrzés
        $nonce_field = isset( $_REQUEST['_hw_nonce'] )
            ? sanitize_text_field( wp_unslash( $_REQUEST['_hw_nonce'] ) )
            : '';
        if ( ! wp_verify_nonce( $nonce_field, 'hw_add_multiple_to_cart' ) ) {
            return;
        }

        // 2) Termékpárok string betöltése és validálása
        $raw_add = isset( $_REQUEST['add-to-cart'] )
            ? sanitize_text_field( wp_unslash( $_REQUEST['add-to-cart'] ) )
            : '';
        if ( '' === $raw_add || false === strpos( $raw_add, ',' ) ) {
            return;
        }

        // 3) Tiltjuk a core add_to_cart_action-t, mi kezeljük a párokat
        remove_action( 'wp_loaded', [ 'WC_Form_Handler', 'add_to_cart_action' ], 20 );

        // 4) Redirect cél meghatározása, csak "cart" vagy "checkout"
        $redirect_key = isset( $_REQUEST['redirect'] )
            ? sanitize_text_field( wp_unslash( $_REQUEST['redirect'] ) )
            : '';
        $allowed = [ 'cart', 'checkout' ];
        $redirect_to = in_array( $redirect_key, $allowed, true )
            ? ( 'checkout' === $redirect_key ? wc_get_checkout_url() : wc_get_cart_url() )
            : wc_get_cart_url();

        // 5) Párbontás és kosárba tétel
        $pairs = array_filter( array_map( 'trim', explode( ',', $raw_add ) ) );
        $total = count( $pairs );
        $i     = 0;

        foreach ( $pairs as $pair ) {
            $i++;
            list( $pid, $qty ) = array_pad( explode( ':', $pair ), 2, 1 );
            $pid = absint( $pid );
            $qty = max( 1, absint( $qty ) );

            // Az utolsó termékhez hagyjuk a WC_Form_Handler-t, hogy maga redirecteljen
            if ( $i === $total ) {
                $_REQUEST['quantity']    = $qty;
                $_REQUEST['add-to-cart'] = $pid;
                WC_Form_Handler::add_to_cart_action( $redirect_to );
            }

            // Korábbiakat manuálisan adjuk a kosárhoz
            $product = wc_get_product( $pid );
            if ( $product && $product->is_purchasable() ) {
                WC()->cart->add_to_cart( $pid, $qty );
            }
        }
    }

    /**
     * Reflection segítségével privát metódusok behívása (ha szükség lenne rá).
     */
    private static function invoke_private( string $class, string $method, ...$args ) {
        $ref = new ReflectionClass( $class );
        $m   = $ref->getMethod( $method );
        $m->setAccessible( true );
        return $m->invokeArgs( null, $args );
    }
}
