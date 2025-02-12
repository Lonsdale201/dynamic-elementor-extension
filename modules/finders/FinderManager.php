<?php
namespace HelloWP\HWEleWooDynamic\Modules\Finder;

use Elementor\Core\Common\Modules\Finder\Base_Category;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class FinderManager {
    private static $instance = null;

    /**
     * Singleton instance method.
     *
     * @return FinderManager
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor to register the Finder categories.
     */
    private function __construct() {
        add_action('elementor/finder/register', [$this, 'register_finder_categories']);
    }

    /**
     * Registers Finder categories in Elementor Finder.
     *
     * @param \Elementor\Core\Common\Modules\Finder\Manager $finder_manager
     */
    public function register_finder_categories($finder_manager) {
        // Register WooCommerce-related categories only if WooCommerce is active
        if ( Dependencies::is_woocommerce_active() ) {
            $finder_manager->register(new WooCommerceManagementCategory());
            $finder_manager->register(new PaymentMethodsCategory());
            $finder_manager->register(new ShippingMethods());
        }

        // Register JetEngine methods if JetEngine and its visibility module are active
        if ( Dependencies::is_jetengine_active_and_visibility_enabled() ) {
            $finder_manager->register(new JetEngineMethods());
        }

        // Register LearnDash methods if LearnDash is active
        if ( Dependencies::is_learndash_active() ) {
            $finder_manager->register(new LearnDashMethods());
        }
    }
}
