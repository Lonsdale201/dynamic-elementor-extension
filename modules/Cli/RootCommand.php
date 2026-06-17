<?php

namespace HelloWP\HWEleWooDynamic\Modules\Cli;

use HelloWP\HWEleWooDynamic\Modules\DynamicSettings;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Top-level `wp dynamic-ext` command — environment diagnostics.
 */
final class RootCommand {

    /**
     * Shows environment and plugin diagnostics: versions, dependency detection
     * and how many dynamic tags are enabled / effective / available.
     *
     * Read-only. Useful for support ("why doesn't my tag show up?") — it tells
     * you whether Elementor (Pro), the AJAX query control, and each integration
     * are active, and how many of the enabled tags can actually render.
     *
     * ## OPTIONS
     *
     * [--format=<format>]
     * : Output format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - yaml
     * ---
     *
     * ## EXAMPLES
     *
     *     wp dynamic-ext status
     *     wp dynamic-ext status --format=json
     *
     * @when after_wp_load
     */
    public function status( array $args, array $assoc_args ): void {
        $catalog = DynamicSettings::get_flat_tag_catalog();
        $enabled = Support::get_enabled_tags();

        $enabled_count   = 0;
        $effective_count = 0;
        foreach ( $enabled as $full_id => $value ) {
            if ( empty( $value ) ) {
                continue; // matches TagManager's `if ( empty( $value ) ) continue;`
            }
            $enabled_count++;

            $section = $catalog[ $full_id ]['section'] ?? null;
            if ( null !== $section && DynamicSettings::is_section_available( $section ) ) {
                $effective_count++;
            }
        }

        $rows = [
            [ 'setting' => 'Plugin version',                  'value' => defined( 'HW_ELE_DYNAMIC_VERSION' ) ? HW_ELE_DYNAMIC_VERSION : 'unknown' ],
            [ 'setting' => 'Elementor',                       'value' => defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : 'not active' ],
            [ 'setting' => 'Elementor Pro',                   'value' => defined( 'ELEMENTOR_PRO_VERSION' ) ? ELEMENTOR_PRO_VERSION : 'not active' ],
            [ 'setting' => 'AJAX query control (Pro picker)', 'value' => class_exists( '\ElementorPro\Modules\QueryControl\Module' ) ? 'available' : 'unavailable' ],
            [ 'setting' => 'WooCommerce',                     'value' => Dependencies::is_woocommerce_active() ? 'active' : 'inactive' ],
            [ 'setting' => 'WC Memberships',                  'value' => Dependencies::is_memberships_active() ? 'active' : 'inactive' ],
            [ 'setting' => 'WC Subscriptions',                'value' => Dependencies::is_subscriptions_active() ? 'active' : 'inactive' ],
            [ 'setting' => 'LearnDash',                       'value' => Dependencies::is_learndash_active() ? 'active' : 'inactive' ],
            [ 'setting' => 'JetEngine + Dynamic Visibility',  'value' => Dependencies::is_jetengine_active_and_visibility_enabled() ? 'active' : 'inactive' ],
            [ 'setting' => 'Tags enabled',                    'value' => (string) $enabled_count ],
            [ 'setting' => 'Tags effective (deps met)',       'value' => (string) $effective_count ],
            [ 'setting' => 'Tags available (catalog)',        'value' => (string) count( $catalog ) ],
        ];

        $format = \WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'table' );

        \WP_CLI\Utils\format_items( $format, $rows, [ 'setting', 'value' ] );
    }
}
