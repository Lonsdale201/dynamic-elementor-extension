<?php
/**
 * Plugin Name: Dynamic Elementor extension
 * Description: Extra Dynamic tags for woocommerce and more.
 * Version: 1.02
 * Author: Soczó Kristóf
 * Author URI: https://github.com/Lonsdale201?tab=repositories
 * Plugin URI: https://github.com/Lonsdale201/dynamic-elementor-extension
 * Text Domain: hw-ele-woo-dynamic
 * Elementor tested up to: 3.20.2
 * Elementor Pro tested up to: 3.20.0
 */

 namespace HelloWP\HWEleWooDynamic; 
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

require_once __DIR__ . '/vendor/autoload.php';
require dirname(__FILE__) . '/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5p0\PucFactory;

final class HW_Ele_Dynamic_Tags {

	const MINIMUM_WOOCOMMERCE_VERSION = '7.0.0';
	const MINIMUM_WORDPRESS_VERSION = '6.0';
	const MINIMUM_PHP_VERSION = '7.4';
    const MINIMUM_ELEMENTOR_VERSION = '3.17.0';

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
        add_action('plugins_loaded', [$this, 'init_on_plugins_loaded']);
        add_action('init', [$this, 'on_init']);
    }
    
    public function load_plugin_textdomain() {
        load_plugin_textdomain('hw-ele-woo-dynamic', false, basename(dirname(__FILE__)) . '/languages');
    }

    public function init_on_plugins_loaded() {

        if ( ! $this->is_compatible() ) {
            return;
        }
        add_action( 'elementor/init', [ $this, 'init_elementor_integration' ] );

        $myUpdateChecker = PucFactory::buildUpdateChecker(
            'https://plugin-uodater.alex.hellodevs.dev/plugins/hw-elementor-woo-dynamic.json',
            __FILE__,
            'hw-elementor-woo-dynamic'
        );

    }

    public function on_init() {
        $this->load_plugin_textdomain();

        if (!$this->is_compatible()) {
            return;
        }

        $this->init_elementor_integration();

        if ($this->is_jetengine_active_and_visibility_module_enabled()) {
            JEDynamicVisibility\VisibilityManager::instance();
        }
    }

    private function is_jetengine_active_and_visibility_module_enabled() {
        return class_exists('Jet_Engine') && function_exists('jet_engine') && jet_engine()->modules->is_module_active('dynamic-visibility');
    }

    public function init_elementor_integration() {
        \HelloWP\HWEleWooDynamic\TagManager::get_instance();
    }
    
	public function is_compatible() {

		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_elementor_plugin' ] );
			return false;
		}

		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return false;
		}

		if ( ! class_exists( 'WooCommerce' ) || version_compare( WC_VERSION, self::MINIMUM_WOOCOMMERCE_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_woocommerce_version' ] );
			return false;
		}

		if ( version_compare( get_bloginfo( 'version' ), self::MINIMUM_WORDPRESS_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_wordpress_version' ] );
			return false;
		}

		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return false;
		}

		return true;
	}

	public function admin_notice_minimum_woocommerce_version() {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo sprintf(__('Dynamic Elementor extension requires WooCommerce version %s or greater. Please update WooCommerce to use this plugin.', 'hw-ele-woo-dynamic'), self::MINIMUM_WOOCOMMERCE_VERSION);
        echo '</p></div>';
    }
    
    public function admin_notice_minimum_wordpress_version() {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo sprintf(__('Dynamic Elementor extension requiresWordPress version %s or greater. Please update WordPress to use this plugin.', 'hw-ele-woo-dynamic'), self::MINIMUM_WORDPRESS_VERSION);
        echo '</p></div>';
    }
    
    public function admin_notice_minimum_php_version() {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo sprintf(__('Dynamic Elementor extension requires PHP version %s or greater. Please update PHP to use this plugin.', 'hw-ele-woo-dynamic'), self::MINIMUM_PHP_VERSION);
        echo '</p></div>';
    }

    public function admin_notice_minimum_elementor_version() {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo sprintf(__('Dynamic Elementor extension requires Elementor version %s or greater. Please update Elementor to use this plugin.', 'hw-ele-woo-dynamic'), self::MINIMUM_ELEMENTOR_VERSION);
        echo '</p></div>';
    }
    
    
}

HW_Ele_Dynamic_Tags::instance();
