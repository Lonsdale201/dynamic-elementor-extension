<?php

namespace HelloWP\HWEleWooDynamic\Modules\Cli;

use HelloWP\HWEleWooDynamic\Modules\DynamicSettings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * `wp dynamic-ext tags ...` — list and toggle dynamic tags.
 *
 * These are the same on/off switches as the "Enable Dynamic Tags" checkboxes on
 * the Dynamic Elements settings page. A tag identifier is the `tag` column from
 * `wp dynamic-ext tags list` (e.g. `woo_extras_product_attributes`).
 */
final class TagsCommand {

    /**
     * Lists the dynamic tags in the catalog with their enabled / available state.
     *
     * ## OPTIONS
     *
     * [--enabled]
     * : Only tags that are currently enabled.
     *
     * [--available]
     * : Only tags whose plugin dependencies are currently met.
     *
     * [--group=<section>]
     * : Filter by catalog section id (the `group` column), e.g. woo_extras.
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
     *   - count
     * ---
     *
     * ## EXAMPLES
     *
     *     wp dynamic-ext tags list
     *     wp dynamic-ext tags list --enabled
     *     wp dynamic-ext tags list --group=woo_extras --format=json
     *
     * @subcommand list
     * @when after_wp_load
     */
    public function list_( array $args, array $assoc_args ): void {
        $catalog = DynamicSettings::get_flat_tag_catalog();
        $enabled = Support::get_enabled_tags();

        $only_enabled   = (bool) \WP_CLI\Utils\get_flag_value( $assoc_args, 'enabled', false );
        $only_available = (bool) \WP_CLI\Utils\get_flag_value( $assoc_args, 'available', false );
        $group          = (string) \WP_CLI\Utils\get_flag_value( $assoc_args, 'group', '' );

        $items = [];
        foreach ( $catalog as $full_id => $meta ) {
            if ( '' !== $group && $meta['section'] !== $group ) {
                continue;
            }

            $is_enabled   = ! empty( $enabled[ $full_id ] );
            $is_available = DynamicSettings::is_section_available( $meta['section'] );

            if ( $only_enabled && ! $is_enabled ) {
                continue;
            }
            if ( $only_available && ! $is_available ) {
                continue;
            }

            $items[] = [
                'tag'       => $full_id,
                'label'     => $meta['label'],
                'group'     => $meta['section'],
                'enabled'   => $is_enabled ? 'yes' : 'no',
                'available' => $is_available ? 'yes' : 'no',
            ];
        }

        $format = \WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'table' );

        \WP_CLI\Utils\format_items( $format, $items, [ 'tag', 'label', 'group', 'enabled', 'available' ] );
    }

    /**
     * Enables one or more dynamic tags.
     *
     * ## OPTIONS
     *
     * [<tag>...]
     * : One or more tag identifiers (the `tag` column of `tags list`).
     *
     * [--all]
     * : Enable every tag in the catalog.
     *
     * ## EXAMPLES
     *
     *     wp dynamic-ext tags enable woo_extras_product_attributes global_tags_global_post_content
     *     wp dynamic-ext tags enable --all
     *
     * @when after_wp_load
     */
    public function enable( array $args, array $assoc_args ): void {
        $this->toggle( $args, $assoc_args, true );
    }

    /**
     * Disables one or more dynamic tags.
     *
     * ## OPTIONS
     *
     * [<tag>...]
     * : One or more tag identifiers (the `tag` column of `tags list`).
     *
     * [--all]
     * : Disable every tag in the catalog.
     *
     * ## EXAMPLES
     *
     *     wp dynamic-ext tags disable woo_extras_spec_badge
     *     wp dynamic-ext tags disable --all
     *
     * @when after_wp_load
     */
    public function disable( array $args, array $assoc_args ): void {
        $this->toggle( $args, $assoc_args, false );
    }

    /**
     * Shared enable/disable implementation.
     *
     * Enabling stores "1" (mirrors a ticked checkbox). Disabling UNSETS the key
     * rather than writing "0", because the settings UI decides "checked" with
     * isset() — a stored "0" would show as ticked while staying inactive at
     * runtime. All other option keys are preserved (read-modify-write).
     */
    private function toggle( array $args, array $assoc_args, bool $enable ): void {
        $catalog = DynamicSettings::get_flat_tag_catalog();
        $all     = (bool) \WP_CLI\Utils\get_flag_value( $assoc_args, 'all', false );

        if ( $all ) {
            $targets = array_keys( $catalog );
        } elseif ( ! empty( $args ) ) {
            $targets = $args;
        } else {
            \WP_CLI::error( 'Provide one or more tag identifiers, or use --all. See: wp dynamic-ext tags list' );
        }

        $settings = Support::get_settings();
        if ( ! isset( $settings['enabled_tags'] ) || ! is_array( $settings['enabled_tags'] ) ) {
            $settings['enabled_tags'] = [];
        }

        $changed = 0;
        $noop    = 0;
        $unknown = 0;

        foreach ( $targets as $full_id ) {
            if ( ! isset( $catalog[ $full_id ] ) ) {
                \WP_CLI::warning( "Unknown tag: {$full_id} (skipped)" );
                $unknown++;
                continue;
            }

            $currently = ! empty( $settings['enabled_tags'][ $full_id ] );

            if ( $enable ) {
                if ( $currently ) {
                    $noop++;
                    continue;
                }
                $settings['enabled_tags'][ $full_id ] = '1';
                $changed++;

                if ( ! DynamicSettings::is_section_available( $catalog[ $full_id ]['section'] ) ) {
                    \WP_CLI::warning( "Enabled '{$full_id}', but its dependency is inactive — it won't appear until that plugin is active." );
                }
            } else {
                if ( ! $currently ) {
                    $noop++;
                    continue;
                }
                unset( $settings['enabled_tags'][ $full_id ] );
                $changed++;
            }
        }

        if ( $changed > 0 ) {
            Support::save_settings( $settings );
        }

        \WP_CLI::success( sprintf(
            '%s: %d changed, %d already %s, %d unknown.',
            $enable ? 'enable' : 'disable',
            $changed,
            $noop,
            $enable ? 'enabled' : 'disabled',
            $unknown
        ) );
    }
}
