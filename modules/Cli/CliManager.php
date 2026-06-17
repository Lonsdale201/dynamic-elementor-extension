<?php

namespace HelloWP\HWEleWooDynamic\Modules\Cli;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registers the plugin's WP-CLI commands.
 *
 * Call only when WP-CLI is running (guarded in the Loader). Command classes are
 * passed as class strings so WP-CLI lazy-instantiates them on dispatch.
 */
final class CliManager {

    public static function register(): void {
        \WP_CLI::add_command( 'dynamic-ext', RootCommand::class );
        \WP_CLI::add_command( 'dynamic-ext tags', TagsCommand::class );
        \WP_CLI::add_command( 'dynamic-ext settings', SettingsCommand::class );
    }
}
