<?php

namespace HelloWP\HWEleWooDynamic;

use HelloWP\HWEleWooDynamic\Modules\EndPoints\InsertContent;
use HelloWP\HWEleWooDynamic\Modules\Helpers\CartHelper;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;
use YahnisElsts\PluginUpdateChecker\v5p0\PucFactory;

final class Loader
{
    private const MINIMUM_WORDPRESS_VERSION = '6.0';
    private const MINIMUM_PHP_VERSION       = '8.0';
    private const MINIMUM_ELEMENTOR_VERSION = '3.22.0';

    private static ?self $instance = null;

    private ?InsertContent $insertContentInstance = null;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    private function __construct()
    {
        add_action('init', [$this, 'load_textdomain'], 0);
        add_action('init', [$this, 'on_init']);
        add_action('plugins_loaded', [$this, 'on_plugins_loaded']);
        add_filter('plugin_action_links_' . plugin_basename(HW_ELE_DYNAMIC_FILE), [$this, 'add_plugin_action_links']);
        add_filter('plugin_row_meta', [$this, 'add_plugin_row_meta'], 10, 2);
    }

    public function load_textdomain(): void
    {
        $wp_version = $GLOBALS['wp_version'] ?? '0';

        if (version_compare($wp_version, '6.7', '<')) {
            load_plugin_textdomain('hw-ele-woo-dynamic', false, dirname(plugin_basename(HW_ELE_DYNAMIC_FILE)) . '/languages');
            return;
        }

        load_textdomain(
            'hw-ele-woo-dynamic',
            plugin_dir_path(HW_ELE_DYNAMIC_FILE) . 'languages/hw-ele-woo-dynamic-' . determine_locale() . '.mo'
        );
    }

    public function on_plugins_loaded(): void
    {
        if (! $this->is_compatible()) {
            return;
        }

        add_action('elementor/init', [$this, 'init_elementor_integration']);

        PucFactory::buildUpdateChecker(
            'https://pluginupdater.hellodevs.dev/plugins/hw-elementor-woo-dynamic.json',
            HW_ELE_DYNAMIC_FILE,
            'hw-elementor-woo-dynamic'
        );
    }

    public function add_plugin_action_links(array $links): array
    {
        $settings_link = '<a href="' . admin_url('admin.php?page=dynamic-extension-settings') . '">' .
            __('Settings', 'hw-ele-woo-dynamic') . '</a>';
        array_unshift($links, $settings_link);

        return $links;
    }

    public function add_plugin_row_meta(array $links, string $file): array
    {
        if ($file === plugin_basename(HW_ELE_DYNAMIC_FILE)) {
            $links[] = '<a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/Start-here">' .
                __('Documentation', 'hw-ele-woo-dynamic') . '</a>';
        }

        return $links;
    }

    public function on_init(): void
    {
        if (! $this->is_compatible()) {
            return;
        }

        $this->init_elementor_integration();

        if (Dependencies::is_jetengine_active_and_visibility_enabled()) {
            Modules\DynamicVisibility\VisibilityManager::instance();
        }
    }

    public function init_elementor_integration(): void
    {
        TagManager::get_instance();
        Modules\DynamicSettings::get_instance();
        Modules\ThemeConditions\ThemeConditionManager::instance();
        Modules\Finder\FinderManager::get_instance();
        Modules\WPTopBar\TopBarSettings::get_instance();
        new Modules\Widgets\WidgetManager();

        if (Dependencies::is_woocommerce_active()) {
            if (! isset($this->insertContentInstance)) {
                $this->insertContentInstance = new InsertContent();
            }
            CartHelper::init();
        }

        if (class_exists('Jet_Engine')) {
            Modules\JEMacros\MacroManager::instance();
            Modules\Callbacks\CallbackManager::instance();
        }
    }

    private function is_compatible(): bool
    {
        if (! did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_elementor_plugin']);
            return false;
        }

        if (! version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return false;
        }

        if (version_compare(get_bloginfo('version'), self::MINIMUM_WORDPRESS_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_wordpress_version']);
            return false;
        }

        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return false;
        }

        return true;
    }

    public function admin_notice_elementor_plugin(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        echo '<div class="notice notice-warning is-dismissible"><p>' .
            esc_html__('Dynamic Elementor extension requires Elementor plugin to be activated. Please activate Elementor to use this plugin.', 'hw-ele-woo-dynamic') .
            '</p></div>';
    }

    public function admin_notice_minimum_wordpress_version(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        echo '<div class="notice notice-warning is-dismissible"><p>' .
            sprintf(
                esc_html__('Dynamic Elementor extension requires WordPress version %s or greater. Please update WordPress to use this plugin.', 'hw-ele-woo-dynamic'),
                esc_html(self::MINIMUM_WORDPRESS_VERSION)
            ) .
            '</p></div>';
    }

    public function admin_notice_minimum_php_version(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        echo '<div class="notice notice-warning is-dismissible"><p>' .
            sprintf(
                esc_html__('Dynamic Elementor extension requires PHP version %s or greater. Please update PHP to use this plugin.', 'hw-ele-woo-dynamic'),
                esc_html(self::MINIMUM_PHP_VERSION)
            ) .
            '</p></div>';
    }

    public function admin_notice_minimum_elementor_version(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        echo '<div class="notice notice-warning is-dismissible"><p>' .
            sprintf(
                esc_html__('Dynamic Elementor extension requires Elementor version %s or greater. Please update Elementor to use this plugin.', 'hw-ele-woo-dynamic'),
                esc_html(self::MINIMUM_ELEMENTOR_VERSION)
            ) .
            '</p></div>';
    }
}
