<?php
namespace HelloWP\HWEleWooDynamic\Modules\EndPoints;

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


class InsertContent {
    private $settings;

    public function __construct() {
        $this->settings = get_option('dynamic_extension_settings'); 
        add_action('woocommerce_account_dashboard', [$this, 'insert_dashboard_content'], 10);
        
        add_action('woocommerce_before_account_orders', [$this, 'insert_orders_content'], 10);
        add_action('woocommerce_after_account_orders', [$this, 'insert_orders_content_after'], 20);
    }

    public function insert_dashboard_content() {
        if (!empty($this->settings['dashboard_before_content'])) {
            echo do_shortcode(wp_kses_post($this->settings['dashboard_before_content']));
        }
        add_action('woocommerce_account_dashboard', function() {
            if (!empty($this->settings['dashboard_after_content'])) {
                echo do_shortcode(wp_kses_post($this->settings['dashboard_after_content']));
            }
        }, 20);
    }

    public function insert_orders_content() {
        if (!empty($this->settings['orders_before_table'])) {
            echo do_shortcode(wp_kses_post($this->settings['orders_before_table']));
        }
    }

    public function insert_orders_content_after() {
        if (!empty($this->settings['orders_after_table'])) {
            echo do_shortcode(wp_kses_post($this->settings['orders_after_table']));
        }
    }
}
