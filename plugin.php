<?php
/**
 * Plugin Name: Dynamic Elementor Extension
 * Description: Extra dynamic tags and other useful functions (conditionally for WooCommerce, Memberships, Subscriptions, and LearnDash).
 * Version: 2.4.2.1
 * Author: Soczó Kristóf
 * Author URI: https://github.com/Lonsdale201?tab=repositories
 * Plugin URI: https://github.com/Lonsdale201/dynamic-elementor-extension
 * Text Domain: hw-ele-woo-dynamic
 * Elementor tested up to: 3.32.4
 * Elementor Pro tested up to: 3.32.2
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace HelloWP\HWEleWooDynamic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'HW_ELE_DYNAMIC_VERSION' ) ) {
	define( 'HW_ELE_DYNAMIC_VERSION', '2.4.2.1' );
}

define( 'HW_ELE_DYNAMIC_FILE', __FILE__ );
define( 'HW_ELE_DYNAMIC_PLUGIN', plugin_dir_url( __FILE__ ) );
define( 'HW_ELE_DYNAMIC_URL', plugin_dir_url( __FILE__ ) );
define( 'HW_ELE_DYNAMIC_PATH', plugin_dir_path( __FILE__ ) );

require_once __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/plugin-update-checker/plugin-update-checker.php';
require_once __DIR__ . '/app/Loader.php';

Loader::instance();
