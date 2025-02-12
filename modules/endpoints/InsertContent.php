<?php
namespace HelloWP\HWEleWooDynamic\Modules\EndPoints;

use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class InsertContent
 *
 * Developer Notes:
 * ----------------
 * Inserts various pieces of content into the WooCommerce account area (e.g., dashboard, orders)
 * 
 */
class InsertContent {

    /**
     * Holds the plugin settings from the WordPress options API.
     *
     * @var array
     */
    private $settings;

    /**
     * Constructor.
     *
     * Initializes the settings and conditionally registers WooCommerce hooks
     * if it is detected to be active.
     */
    public function __construct() {
        $this->settings = get_option('dynamic_extension_settings');

        if ( Dependencies::is_woocommerce_active() ) {
            add_action( 'woocommerce_account_dashboard', [ $this, 'insert_dashboard_content' ], 10 );
            add_action( 'woocommerce_before_account_orders', [ $this, 'insert_orders_content' ], 10 );
            add_action( 'woocommerce_after_account_orders', [ $this, 'insert_orders_content_after' ], 20 );
        }
    }

    /**
     * Echoes dashboard content before and after the WooCommerce account dashboard.
     *
     * Hooks used:
     * - woocommerce_account_dashboard (priority 10 and 20)
     */
    public function insert_dashboard_content() {
        if ( ! empty( $this->settings['dashboard_before_content'] ) ) {
            echo do_shortcode( wp_kses_post( $this->settings['dashboard_before_content'] ) );
        }

        add_action( 'woocommerce_account_dashboard', function() {
            if ( ! empty( $this->settings['dashboard_after_content'] ) ) {
                echo do_shortcode( wp_kses_post( $this->settings['dashboard_after_content'] ) );
            }
        }, 20 );
    }

    /**
     * Echoes content before the Orders table on the WooCommerce account page.
     *
     * Hook used:
     * - woocommerce_before_account_orders (priority 10)
     */
    public function insert_orders_content() {
        if ( ! empty( $this->settings['orders_before_table'] ) ) {
            echo do_shortcode( wp_kses_post( $this->settings['orders_before_table'] ) );
        }
    }

    /**
     * Echoes content after the Orders table on the WooCommerce account page.
     *
     * Hook used:
     * - woocommerce_after_account_orders (priority 20)
     */
    public function insert_orders_content_after() {
        if ( ! empty( $this->settings['orders_after_table'] ) ) {
            echo do_shortcode( wp_kses_post( $this->settings['orders_after_table'] ) );
        }
    }
}
