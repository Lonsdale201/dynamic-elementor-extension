<?php

namespace HelloWP\HWEleWooDynamic\Modules\JEQuery;

use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class QueryManager
{
    private const QUERY_SLUG = 'wc-order-hpos';

    private static ?self $instance = null;

    public static function instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function get_slug(): string
    {
        return self::QUERY_SLUG;
    }

    private function __construct()
    {
        if (! Dependencies::is_woocommerce_active()) {
            return;
        }

        add_action('jet-engine/query-builder/query-editor/register', [$this, 'register_editor_component']);
        add_action('jet-engine/query-builder/queries/register', [$this, 'register_query']);
    }

    public function register_editor_component($manager): void
    {
        if (! class_exists(__NAMESPACE__ . '\\WCOrderHposQueryEditor')) {
            require_once __DIR__ . '/WCOrderHposQueryEditor.php';
        }

        $manager->register_type(new WCOrderHposQueryEditor());
    }

    public function register_query($manager): void
    {
        if (! class_exists(__NAMESPACE__ . '\\WCOrderHposQuery')) {
            require_once __DIR__ . '/WCOrderHposQuery.php';
        }

        $manager::register_query(self::QUERY_SLUG, WCOrderHposQuery::class);
    }
}
