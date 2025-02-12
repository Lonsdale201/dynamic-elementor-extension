<?php

namespace HelloWP\HWEleWooDynamic\Modules\Callbacks;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

class CallbackManager {

    /**
     * Singleton instance.
     *
     * @var CallbackManager|null
     */
    private static $instance = null;

    /**
     * Private constructor to prevent direct object creation.
     */
    private function __construct() {
        $this->register_callbacks();
    }

    /**
     * Returns the single instance of the class.
     *
     * @return CallbackManager
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Registers all custom callbacks for JetEngine.
     */
    public function register_callbacks() {
        UnitConverter::register();
    }
}
