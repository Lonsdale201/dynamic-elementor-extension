<?php
namespace HelloWP\HWEleWooDynamic\Modules\Finder;

use Elementor\Core\Common\Modules\Finder\Base_Category;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class FinderManager {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('elementor/finder/register', [$this, 'register_finder_categories']);
    }

    public function register_finder_categories($finder_manager) {
        $finder_manager->register(new WooCommerceManagementCategory());
        $finder_manager->register(new PaymentMethodsCategory());
        $finder_manager->register(new ShippingMethods());

        if (Dependencies::is_jetengine_active_and_visibility_enabled()) {
            $finder_manager->register(new JetEngineMethods());
        }

        if (Dependencies::is_learndash_active()) {
            $finder_manager->register(new LearnDashMethods());
        }
    }
}