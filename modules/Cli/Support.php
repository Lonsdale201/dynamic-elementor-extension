<?php

namespace HelloWP\HWEleWooDynamic\Modules\Cli;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shared, side-effect-light helpers for the WP-CLI commands.
 *
 * All plugin state lives in the single `dynamic_extension_settings` option;
 * these helpers read/write it without going through the admin Settings API
 * sanitiser (which is form-shaped and would drop unrelated keys). Writes are
 * always read-modify-write of the whole option so unrelated keys are preserved.
 */
final class Support {

    public const OPTION = 'dynamic_extension_settings';

    /**
     * @return array<string,mixed>
     */
    public static function get_settings(): array {
        $opt = get_option( self::OPTION );

        return is_array( $opt ) ? $opt : [];
    }

    /**
     * @param array<string,mixed> $settings
     */
    public static function save_settings( array $settings ): void {
        update_option( self::OPTION, $settings );
    }

    /**
     * Current enabled-tags map ( "{section}_{tag_id}" => "1" ).
     *
     * @return array<string,mixed>
     */
    public static function get_enabled_tags(): array {
        $opt = self::get_settings();

        return ( isset( $opt['enabled_tags'] ) && is_array( $opt['enabled_tags'] ) )
            ? $opt['enabled_tags']
            : [];
    }
}
