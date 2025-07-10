<?php
/**
 * Plugin Name: Dynamic Elementor Extension
 * Description: Extra dynamic tags and other useful functions (conditionally for WooCommerce, Memberships, Subscriptions, and LearnDash).
 * Version: 2.4.1
 * Author: Soczó Kristóf
 * Author URI: https://github.com/Lonsdale201?tab=repositories
 * Plugin URI: https://github.com/Lonsdale201/dynamic-elementor-extension
 * Text Domain: hw-ele-woo-dynamic
 * Elementor tested up to: 3.29.2
 * Elementor Pro tested up to: 3.29.2
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace HelloWP\HWEleWooDynamic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HW_ELE_DYNAMIC_PLUGIN', plugin_dir_url( __FILE__ ) );
define( 'HW_ELE_DYNAMIC_URL',    plugin_dir_url( __FILE__ ) );
define( 'HW_ELE_DYNAMIC_PATH',   plugin_dir_path( __FILE__ ) );

if ( ! defined( 'HW_ELE_DYNAMIC_VERSION' ) ) {
	define( 'HW_ELE_DYNAMIC_VERSION', '2.4.1' );
}

require_once __DIR__ . '/vendor/autoload.php';
require dirname( __FILE__ ) . '/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5p0\PucFactory;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;
use HelloWP\HWEleWooDynamic\Modules\Helpers\CartHelper;

/**
 * Main class for the Dynamic Elementor extension.
 */
final class HW_Ele_Dynamic_Tags {

	const MINIMUM_WORDPRESS_VERSION = '6.0';
	const MINIMUM_PHP_VERSION       = '8.0';
	const MINIMUM_ELEMENTOR_VERSION = '3.22.0';

	/**
	 * Singleton instance.
	 *
	 * @var HW_Ele_Dynamic_Tags|null
	 */
	private static $_instance = null;

	/**
	 * @var \HelloWP\HWEleWooDynamic\Modules\EndPoints\InsertContent
	 */
	private $insertContentInstance;

	/**
	 * Ensure only one instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init_on_plugins_loaded' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'add_plugin_action_links' ] );
		add_filter( 'plugin_row_meta', [ $this, 'add_plugin_row_meta' ], 10, 2 );
		add_action( 'init', [ $this, 'on_init' ] );
	}

	/** Load translations. */
	public function load_plugin_textdomain() {
		if ( version_compare( $GLOBALS['wp_version'], '6.7', '<' ) ) {
			load_plugin_textdomain( 'hw-ele-woo-dynamic', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		} else {
			load_textdomain(
				'hw-ele-woo-dynamic',
				plugin_dir_path( __FILE__ ) . 'languages/hw-ele-woo-dynamic-' . determine_locale() . '.mo'
			);
		}
	}

	/** After plugins loaded. */
	public function init_on_plugins_loaded() {
		if ( ! $this->is_compatible() ) {
			return;
		}

		add_action( 'elementor/init', [ $this, 'init_elementor_integration' ] );

		$myUpdateChecker = PucFactory::buildUpdateChecker(
            'https://pluginupdater.hellodevs.dev/plugins/hw-elementor-woo-dynamic.json',
            __FILE__,
            'hw-elementor-woo-dynamic'
        );
	}

	/** Add settings link. */
	public function add_plugin_action_links( $links ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=dynamic-extension-settings' ) . '">' .
			__( 'Settings', 'hw-ele-woo-dynamic' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/** Add meta links. */
	public function add_plugin_row_meta( $links, $file ) {
		if ( $file === plugin_basename( __FILE__ ) ) {
			$links[] = '<a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/Start-here">' .
				__( 'Documentation', 'hw-ele-woo-dynamic' ) . '</a>';
		}
		return $links;
	}

	/** Init run. */
	public function on_init() {
		$this->load_plugin_textdomain();

		if ( ! $this->is_compatible() ) {
			return;
		}

		$this->init_elementor_integration();

		if ( Dependencies::is_jetengine_active_and_visibility_enabled() ) {
			Modules\DynamicVisibility\VisibilityManager::instance();
		}
	}

	/** Elementor modules. */
	public function init_elementor_integration() {
		\HelloWP\HWEleWooDynamic\TagManager::get_instance();
		\HelloWP\HWEleWooDynamic\Modules\DynamicSettings::get_instance();
		\HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ThemeConditionManager::instance();
		\HelloWP\HWEleWooDynamic\Modules\Finder\FinderManager::get_instance();
		\HelloWP\HWEleWooDynamic\Modules\WPTopBar\TopBarSettings::get_instance();
		new \HelloWP\HWEleWooDynamic\Modules\Widgets\WidgetManager();

		if ( Dependencies::is_woocommerce_active() ) {
			if ( ! isset( $this->insertContentInstance ) ) {
				$this->insertContentInstance = new \HelloWP\HWEleWooDynamic\Modules\EndPoints\InsertContent();
			}
			CartHelper::init();
		}

		if ( class_exists( 'Jet_Engine' ) ) {
			\HelloWP\HWEleWooDynamic\Modules\JEMacros\MacroManager::instance();
			\HelloWP\HWEleWooDynamic\Modules\Callbacks\CallbackManager::instance();
		}
	}

	/** Compatibility checks. */
	public function is_compatible() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_elementor_plugin' ] );
			return false;
		}

		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
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


	public function admin_notice_elementor_plugin() {
		if ( ! current_user_can( 'manage_options' ) ) { return; }
		echo '<div class="notice notice-warning is-dismissible"><p>' .
			esc_html__( 'Dynamic Elementor extension requires Elementor plugin to be activated. Please activate Elementor to use this plugin.', 'hw-ele-woo-dynamic' ) .
			'</p></div>';
	}

	public function admin_notice_minimum_wordpress_version() {
		if ( ! current_user_can( 'manage_options' ) ) { return; }
		echo '<div class="notice notice-warning is-dismissible"><p>' .
			sprintf(
				esc_html__( 'Dynamic Elementor extension requires WordPress version %s or greater. Please update WordPress to use this plugin.', 'hw-ele-woo-dynamic' ),
				esc_html( self::MINIMUM_WORDPRESS_VERSION )
			) .
			'</p></div>';
	}

	public function admin_notice_minimum_php_version() {
		if ( ! current_user_can( 'manage_options' ) ) { return; }
		echo '<div class="notice notice-warning is-dismissible"><p>' .
			sprintf(
				esc_html__( 'Dynamic Elementor extension requires PHP version %s or greater. Please update PHP to use this plugin.', 'hw-ele-woo-dynamic' ),
				esc_html( self::MINIMUM_PHP_VERSION )
			) .
			'</p></div>';
	}

	public function admin_notice_minimum_elementor_version() {
		if ( ! current_user_can( 'manage_options' ) ) { return; }
		echo '<div class="notice notice-warning is-dismissible"><p>' .
			sprintf(
				esc_html__( 'Dynamic Elementor extension requires Elementor version %s or greater. Please update Elementor to use this plugin.', 'hw-ele-woo-dynamic' ),
				esc_html( self::MINIMUM_ELEMENTOR_VERSION )
			) .
			'</p></div>';
	}
}

HW_Ele_Dynamic_Tags::instance();
