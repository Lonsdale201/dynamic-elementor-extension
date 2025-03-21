<?php
/**
 * Plugin Name: Dynamic Elementor Extension
 * Description: Extra dynamic tags and other useful functions (conditionally for WooCommerce, Memberships, Subscriptions, and LearnDash).
 * Version: 2.3.2
 * Author: Soczó Kristóf
 * Author URI: https://github.com/Lonsdale201?tab=repositories
 * Plugin URI: https://github.com/Lonsdale201/dynamic-elementor-extension
 * Text Domain: hw-ele-woo-dynamic
 * Elementor tested up to: 3.28.0
 * Elementor Pro tested up to: 3.28.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace HelloWP\HWEleWooDynamic;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define the plugin constant for easier reference.
define( 'HW_ELE_DYNAMIC_PLUGIN', plugin_dir_url( __FILE__ ) );
define( 'HW_ELE_DYNAMIC_URL', plugin_dir_url( __FILE__ ) );
define( 'HW_ELE_DYNAMIC_PATH', plugin_dir_path( __FILE__ ) );


// Autoload dependencies (Composer, etc.).
require_once __DIR__ . '/vendor/autoload.php';
require dirname(__FILE__) . '/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5p0\PucFactory;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

/**
 * Main class for the Dynamic Elementor extension.
 *
 * @package HelloWP\HWEleWooDynamic
 */
final class HW_Ele_Dynamic_Tags {

    const MINIMUM_WORDPRESS_VERSION  = '6.0';
    const MINIMUM_PHP_VERSION        = '8.0';
    const MINIMUM_ELEMENTOR_VERSION  = '3.22.0';

    /**
     * Singleton instance
     *
     * @var HW_Ele_Dynamic_Tags
     */
    private static $_instance = null;

    /**
     *
     * @var \HelloWP\HWEleWooDynamic\Modules\EndPoints\InsertContent
     */
    private $insertContentInstance;

    /**
     * Ensure only one instance of the class is loaded.
     *
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
        add_action( 'plugins_loaded', [ $this, 'init_on_plugins_loaded' ] );
        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), [ $this, 'add_plugin_action_links' ] );
        add_filter( 'plugin_row_meta', [ $this, 'add_plugin_row_meta' ], 10, 2 );
        add_action( 'init', [ $this, 'on_init' ] );
    }

    /**
     * Load plugin textdomain for translations.
     */
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

    /**
     * Initialize actions after plugins are loaded.
     *
     */
    public function init_on_plugins_loaded() {

        // PHP, Elementor 
        if ( ! $this->is_compatible() ) {
            return;
        }

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
     *
     * @param  array $links
     * @return array
     */
    public function add_plugin_action_links($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=dynamic-extension-settings') . '">' . __('Settings', 'hw-ele-woo-dynamic') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Add custom links in the plugin row meta.
     *
     * @param  array  $links
     * @param  string $file
     * @return array
     */
    public function add_plugin_row_meta($links, $file) {
        if ( $file == plugin_basename(__FILE__) ) {
            $documentation_link = '<a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/Start-here">' . __('Documentation', 'hw-ele-woo-dynamic') . '</a>';
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
        if ( Dependencies::is_jetengine_active_and_visibility_enabled() ) {
            Modules\DynamicVisibility\VisibilityManager::instance();
        }
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

        if ( Dependencies::is_woocommerce_active() ) {
            if ( ! isset( $this->insertContentInstance ) ) {
                $this->insertContentInstance = new \HelloWP\HWEleWooDynamic\Modules\EndPoints\InsertContent();
            }
        }

        /**
         * Jet_Engine macros, callbacks
         */
        if ( class_exists( 'Jet_Engine' ) ) {
            \HelloWP\HWEleWooDynamic\Modules\JEMacros\MacroManager::instance();
            \HelloWP\HWEleWooDynamic\Modules\Callbacks\CallbackManager::instance();
        }
        
    }

    /**
     * Check if the environment is compatible with the plugin (Elementor, WP, PHP).
     *
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
    public function admin_notice_elementor_plugin() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo __('Dynamic Elementor extension requires Elementor plugin to be activated. Please activate Elementor to use this plugin.', 'hw-ele-woo-dynamic');
        echo '</p></div>';
    }

    public function admin_notice_minimum_wordpress_version() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo sprintf(
            __('Dynamic Elementor extension requires WordPress version %s or greater. Please update WordPress to use this plugin.', 'hw-ele-woo-dynamic'),
            self::MINIMUM_WORDPRESS_VERSION
        );
        echo '</p></div>';
    }

    public function admin_notice_minimum_php_version() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo sprintf(
            __('Dynamic Elementor extension requires PHP version %s or greater. Please update PHP to use this plugin.', 'hw-ele-woo-dynamic'),
            self::MINIMUM_PHP_VERSION
        );
        echo '</p></div>';
    }

    public function admin_notice_minimum_elementor_version() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo sprintf(
            __('Dynamic Elementor extension requires Elementor version %s or greater. Please update Elementor to use this plugin.', 'hw-ele-woo-dynamic'),
            self::MINIMUM_ELEMENTOR_VERSION
        );
        echo '</p></div>';
    }

}

HW_Ele_Dynamic_Tags::instance();