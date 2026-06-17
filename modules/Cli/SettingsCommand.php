<?php

namespace HelloWP\HWEleWooDynamic\Modules\Cli;

use HelloWP\HWEleWooDynamic\Modules\DynamicSettings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * `wp dynamic-ext settings ...` — export / import the plugin configuration
 * as JSON, for staging↔production parity and provisioning many sites.
 */
final class SettingsCommand {

    /**
     * Exports the plugin settings (dynamic_extension_settings) as JSON.
     *
     * ## OPTIONS
     *
     * [--file=<path>]
     * : Write JSON to this file instead of stdout.
     *
     * ## EXAMPLES
     *
     *     wp dynamic-ext settings export > settings.json
     *     wp dynamic-ext settings export --file=settings.json
     *
     * @when after_wp_load
     */
    public function export( array $args, array $assoc_args ): void {
        $settings = Support::get_settings();

        $json = wp_json_encode( $settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        if ( false === $json ) {
            \WP_CLI::error( 'Failed to encode settings to JSON.' );
        }

        $file = (string) \WP_CLI\Utils\get_flag_value( $assoc_args, 'file', '' );

        if ( '' !== $file ) {
            if ( false === file_put_contents( $file, $json ) ) {
                \WP_CLI::error( "Cannot write to file: {$file}" );
            }
            \WP_CLI::success( sprintf( 'Exported %d setting key(s) to %s.', count( $settings ), $file ) );
            return;
        }

        // Raw JSON to stdout (WP_CLI::line adds no prefix, so it pipes cleanly).
        \WP_CLI::line( $json );
    }

    /**
     * Imports plugin settings from a JSON file, merging over current settings.
     *
     * enabled_tags from the file is filtered against the catalog (unknown keys
     * are dropped with a warning) and the HTML text fields are run through
     * wp_kses_post. Other existing option keys are preserved.
     *
     * ## OPTIONS
     *
     * <file>
     * : Path to a JSON file produced by `settings export`.
     *
     * [--yes]
     * : Skip the confirmation prompt.
     *
     * ## EXAMPLES
     *
     *     wp dynamic-ext settings import settings.json
     *     wp dynamic-ext settings import settings.json --yes
     *
     * @when after_wp_load
     */
    public function import( array $args, array $assoc_args ): void {
        [ $file ] = $args;

        if ( ! is_readable( $file ) ) {
            \WP_CLI::error( "Cannot read file: {$file}" );
        }

        $data = json_decode( (string) file_get_contents( $file ), true );
        if ( ! is_array( $data ) ) {
            \WP_CLI::error( 'Invalid JSON: expected an object of settings.' );
        }

        $catalog   = DynamicSettings::get_flat_tag_catalog();
        $sanitized = [];
        $dropped   = 0;

        if ( isset( $data['enabled_tags'] ) && is_array( $data['enabled_tags'] ) ) {
            $sanitized['enabled_tags'] = [];
            foreach ( $data['enabled_tags'] as $full_id => $value ) {
                if ( ! isset( $catalog[ $full_id ] ) ) {
                    $dropped++;
                    continue;
                }
                if ( ! empty( $value ) ) {
                    $sanitized['enabled_tags'][ $full_id ] = '1'; // disabled tags are simply omitted
                }
            }
        }

        $text_fields = 0;
        foreach ( DynamicSettings::get_text_field_keys() as $field ) {
            if ( isset( $data[ $field ] ) ) {
                $sanitized[ $field ] = wp_kses_post( (string) $data[ $field ] );
                $text_fields++;
            }
        }

        $enabled_in_import = isset( $sanitized['enabled_tags'] ) ? count( $sanitized['enabled_tags'] ) : 0;

        \WP_CLI::log( sprintf(
            'Import preview: %d tag(s) enabled, %d text field(s)%s.',
            $enabled_in_import,
            $text_fields,
            $dropped ? sprintf( ', %d unknown tag key(s) dropped', $dropped ) : ''
        ) );

        \WP_CLI::confirm( 'Overwrite the current plugin settings with the imported values?', $assoc_args );

        // Merge so unrelated existing keys survive; enabled_tags & text fields come from the file.
        $merged = array_merge( Support::get_settings(), $sanitized );
        Support::save_settings( $merged );

        \WP_CLI::success( 'Settings imported.' );
    }
}
