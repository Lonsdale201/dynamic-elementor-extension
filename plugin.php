<?php
/**
 * Plugin Name: Dynamic Elementor Extension
 * Description: Extra dynamic tags and other useful functions for WooCommerce, Memberships, Subscriptions, and LearnDash.
 * Version: 2.2.3
 * Author: Soczó Kristóf
 * Author URI: https://github.com/Lonsdale201?tab=repositories
 * Plugin URI: https://github.com/Lonsdale201/dynamic-elementor-extension
 * Text Domain: hw-ele-woo-dynamic
 * Elementor tested up to: 3.25.3
 * Elementor Pro tested up to: 3.25.0
 * Requires Plugins: woocommerce, elementor
 */

namespace HelloWP\HWEleWooDynamic;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define the plugin constant for easier reference
define( 'HW_ELE_DYNAMIC_PLUGIN', plugin_dir_url( __FILE__ ) );

// Autoload dependencies
require_once __DIR__ . '/vendor/autoload.php';
require dirname(__FILE__) . '/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5p0\PucFactory;

/**
 * Main class for the Dynamic Elementor extension.
 */
final class HW_Ele_Dynamic_Tags {

    // Minimum version requirements
    const MINIMUM_WOOCOMMERCE_VERSION = '9.0.0';
    const MINIMUM_WORDPRESS_VERSION = '6.0';
    const MINIMUM_PHP_VERSION = '8.0';
    const MINIMUM_ELEMENTOR_VERSION = '3.22.0';

    // Singleton instance
    private static $_instance = null;

    private $insertContentInstance;

    /**
     * Ensure only one instance of the class is loaded.
     * @return HW_Ele_Dynamic_Tags
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor to initialize the plugin actions.
     */
    public function __construct() {
        add_action( 'plugins_loaded', [$this, 'init_on_plugins_loaded'] );
        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_plugin_action_links'] );
        add_filter( 'plugin_row_meta', [$this, 'add_plugin_row_meta'], 10, 2 );
        add_action( 'init', [$this, 'on_init'] );
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_styles'] );
    }

    /**
     * Load plugin text domain for translations.
     * Optimized for WordPress 6.0+ to handle translations.
     */
    public function load_plugin_textdomain() {
        // Check if optimized loading is available (WP 6.0+)
        if ( function_exists( 'wp_set_script_translations' ) ) {
            load_plugin_textdomain('hw-ele-woo-dynamic', false, dirname(plugin_basename(__FILE__)) . '/languages');
            wp_set_script_translations('dynamic-settings-js', 'hw-ele-woo-dynamic', plugin_dir_path(__FILE__) . 'languages');
        } else {
            // Fallback for older WordPress versions
            load_textdomain('hw-ele-woo-dynamic', WP_LANG_DIR . '/plugins/hw-ele-woo-dynamic-' . get_locale() . '.mo');
        }
    }

    /**
     * Enqueue admin-specific styles.
     * @param string $hook_suffix
     */
    public function enqueue_admin_styles($hook_suffix) {
        if ( $hook_suffix == 'toplevel_page_dynamic-extension-settings' ) {
            wp_enqueue_style('dynamic-settings-css', plugin_dir_url(__FILE__) . 'assets/dynamicsettings.css', [], '1.0.0');
        }
    }

    /**
     * Initialize actions after plugins are loaded.
     */
    public function init_on_plugins_loaded() {
        if ( ! $this->is_compatible() ) {
            return;
        }

        // Initialize Elementor integration if compatible
        add_action( 'elementor/init', [ $this, 'init_elementor_integration' ] );

        // Plugin update checker setup
        $myUpdateChecker = PucFactory::buildUpdateChecker(
            'https://plugin-uodater.alex.hellodevs.dev/plugins/hw-elementor-woo-dynamic.json',
            __FILE__,
            'hw-elementor-woo-dynamic'
        );
    }

    /**
     * Add settings link to the plugin action links.
     * @param array $links
     * @return array
     */
    public function add_plugin_action_links($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=dynamic-extension-settings') . '">' . __('Settings', 'hw-ele-woo-dynamic') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Add custom links in the plugin row meta.
     * @param array $links
     * @param string $file
     * @return array
     */
    public function add_plugin_row_meta($links, $file) {
        if ( $file == plugin_basename(__FILE__) ) {
            $documentation_link = '<a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki">' . __('Documentation', 'hw-ele-woo-dynamic') . '</a>';
            $links[] = $documentation_link;
        }
        return $links;
    }

    /**
     * Run initial checks and Elementor integration.
     */
    public function on_init() {
        $this->load_plugin_textdomain();

        if ( ! $this->is_compatible() ) {
            return;
        }

        $this->init_elementor_integration();

        if ( $this->is_jetengine_active_and_visibility_module_enabled() ) {
            Modules\DynamicVisibility\VisibilityManager::instance();
        }
    }

    /**
     * Check if JetEngine and its dynamic visibility module are active.
     * @return bool
     */
    private function is_jetengine_active_and_visibility_module_enabled() {
        return class_exists('Jet_Engine') && function_exists('jet_engine') && jet_engine()->modules->is_module_active('dynamic-visibility');
    }

    /**
     * Initialize Elementor-related modules.
     */
    public function init_elementor_integration() {
        \HelloWP\HWEleWooDynamic\TagManager::get_instance();
        \HelloWP\HWEleWooDynamic\Modules\DynamicSettings::get_instance();
        \HelloWP\HWEleWooDynamic\Modules\ThemeConditions\ThemeConditionManager::instance();
        \HelloWP\HWEleWooDynamic\Modules\Finder\FinderManager::get_instance();
        \HelloWP\HWEleWooDynamic\Modules\WPTopBar\TopBarSettings::get_instance();

        if (!isset($this->insertContentInstance)) {
            $this->insertContentInstance = new \HelloWP\HWEleWooDynamic\Modules\EndPoints\InsertContent();
        }

        if ( class_exists('Jet_Engine') ) {
            \HelloWP\HWEleWooDynamic\Modules\JEMacros\MacroManager::instance();
        }
    }

    /**
     * Check if the environment is compatible with the plugin.
     * @return bool
     */
    public function is_compatible() {
        // Check if Elementor is loaded
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_elementor_plugin' ] );
            return false;
        }

        // Verify Elementor version
        if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
            return false;
        }

        // Verify WooCommerce version
        if ( ! class_exists( 'WooCommerce' ) || version_compare( WC_VERSION, self::MINIMUM_WOOCOMMERCE_VERSION, '<' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_woocommerce_version' ] );
            return false;
        }

        // Verify WordPress version
        if ( version_compare( get_bloginfo( 'version' ), self::MINIMUM_WORDPRESS_VERSION, '<' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_wordpress_version' ] );
            return false;
        }

        // Verify PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
            return false;
        }

        return true;
    }

    // Admin notices for compatibility checks

    public function admin_notice_minimum_woocommerce_version() {
        if ( ! current_user_can('manage_options') ) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo sprintf(__('Dynamic Elementor extension requires WooCommerce version %s or greater. Please update WooCommerce to use this plugin.', 'hw-ele-woo-dynamic'), self::MINIMUM_WOOCOMMERCE_VERSION);
        echo '</p></div>';
    }

    public function admin_notice_elementor_plugin() {
        if ( ! current_user_can('manage_options') ) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo __('Dynamic Elementor extension requires Elementor plugin to be activated. Please activate Elementor to use this plugin.', 'hw-ele-woo-dynamic');
        echo '</p></div>';
    }

    public function admin_notice_minimum_wordpress_version() {
        if ( ! current_user_can('manage_options') ) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo sprintf(__('Dynamic Elementor extension requires WordPress version %s or greater. Please update WordPress to use this plugin.', 'hw-ele-woo-dynamic'), self::MINIMUM_WORDPRESS_VERSION);
        echo '</p></div>';
    }

    public function admin_notice_minimum_php_version() {
        if ( ! current_user_can('manage_options') ) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo sprintf(__('Dynamic Elementor extension requires PHP version %s or greater. Please update PHP to use this plugin.', 'hw-ele-woo-dynamic'), self::MINIMUM_PHP_VERSION);
        echo '</p></div>';
    }

    public function admin_notice_minimum_elementor_version() {
        if ( ! current_user_can('manage_options') ) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo sprintf(__('Dynamic Elementor extension requires Elementor version %s or greater. Please update Elementor to use this plugin.', 'hw-ele-woo-dynamic'), self::MINIMUM_ELEMENTOR_VERSION);
        echo '</p></div>';
    }
}

HW_Ele_Dynamic_Tags::instance();
