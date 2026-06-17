<?php
/**
 * Admin Settings Banner - Responsive Grid Layout
 *
 * Developer Notes:
 * ----------------
 * This template uses a CSS Grid approach with repeat(auto-fit, minmax(300px, 1fr))
 * to create a flexible, responsive layout. The number of columns changes depending
 * on available width (e.g., on large or 4K screens, more columns will appear).
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <!-- Plugin Title & Short Description -->
    <h1><?php esc_html_e( 'Dynamic Elementor Extension', 'hw-elementor-woo-dynamic' ); ?></h1>
    <p>
        <?php esc_html_e( 'Extra dynamic tags and other useful functions (conditionally for WooCommerce, Memberships, Subscriptions, and LearnDash).', 'hw-elementor-woo-dynamic' ); ?>
    </p>

    <!-- Responsive Grid Container -->
    <div class="hw-grid-container" 
         style="
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem; 
            margin-top: 1.5rem;
         ">
        <!-- 1) Contact & Profiles -->
        <div class="card" style="padding: 1rem;">
            <h2 style="margin-top: 0;">
                <?php esc_html_e( 'Contact & Profiles', 'hw-elementor-woo-dynamic' ); ?>
            </h2>
            <p class="description">
                <?php esc_html_e( 'Reach out or find my code on GitHub/Gist.', 'hw-elementor-woo-dynamic' ); ?>
            </p>
            <hr />
            <table class="widefat striped" style="margin-bottom: 0;">
                <tbody>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>
                            <a href="mailto:lonsdale201@hotmail.com" target="_blank">lonsdale201@hotmail.com</a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Discord:</strong></td>
                        <td>lonsdale201</td>
                    </tr>
                    <tr>
                        <td><strong>GitHub:</strong></td>
                        <td>
                            <a href="https://github.com/Lonsdale201" target="_blank">
                                github.com/Lonsdale201
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Gist:</strong></td>
                        <td>
                            <a href="https://gist.github.com/Lonsdale201?page=1" target="_blank">
                                gist.github.com/Lonsdale201
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div><!-- .card -->

        <!-- 2) Plugin Info -->
        <div class="card" style="padding: 1rem;">
            <h2 style="margin-top: 0;">
                <?php esc_html_e( 'Plugin Info', 'hw-elementor-woo-dynamic' ); ?>
            </h2>
            <p class="description">
                <?php esc_html_e( 'Main repository & documentation links.', 'hw-elementor-woo-dynamic' ); ?>
            </p>
            <hr />
            <table class="widefat striped" style="margin-bottom: 0;">
                <tbody>
                    <tr>
                        <td><strong><?php esc_html_e('GitHub Plugin Page:', 'hw-elementor-woo-dynamic'); ?></strong></td>
                        <td>
                            <a href="https://github.com/Lonsdale201/dynamic-elementor-extension" target="_blank">
                                github.com/Lonsdale201/dynamic-elementor-extension
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e('Issues / Requests:', 'hw-elementor-woo-dynamic'); ?></strong></td>
                        <td>
                            <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/issues" target="_blank">
                                <?php esc_html_e('GitHub Issues', 'hw-elementor-woo-dynamic'); ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e('Documentation page:', 'hw-elementor-woo-dynamic'); ?></strong></td>
                        <td>
                            <a href="https://lonsdale201.github.io/lonsdale-plugins.github.io/dynamic-ele-ext/" 
                               target="_blank">
                                <?php esc_html_e('Documentation github page', 'hw-elementor-woo-dynamic'); ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e('Old Github Wiki:', 'hw-elementor-woo-dynamic'); ?></strong></td>
                        <td>
                            <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/Start-here" 
                               target="_blank">
                                <?php esc_html_e('Wiki / Start-here', 'hw-elementor-woo-dynamic'); ?>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div><!-- .card -->

        <!-- 3) Supported Plugins -->
        <div class="card" style="padding: 1rem;">
            <h2 style="margin-top: 0;">
                <?php esc_html_e('Supported Plugins', 'hw-elementor-woo-dynamic'); ?>
            </h2>
            <p class="description">
                <?php esc_html_e('The following plugins integrate seamlessly:', 'hw-elementor-woo-dynamic'); ?>
            </p>
            <hr />
            <ul style="list-style: disc; margin-left: 1.5rem;">
                <li><a href="https://elementor.com/" target="_blank">Elementor</a></li>
                <li><a href="https://crocoblock.com/plugins/jetengine/" target="_blank">JetEngine</a></li>
                <li><a href="https://woocommerce.com/" target="_blank">WooCommerce</a></li>
                <li><a href="https://woocommerce.com/products/woocommerce-memberships/" target="_blank">WooCommerce Membership</a></li>
                <li><a href="https://woocommerce.com/products/woocommerce-subscriptions/" target="_blank">WooCommerce Subscriptions</a></li>
                <li><a href="https://woocommerce.com/products/name-your-price/" target="_blank">WooCommerce Name Your Price</a></li>
                <li><a href="https://woocommerce.com/products/woocommerce-tab-manager/" target="_blank">
                    Tab Manager for WooCommerce (by Skyverge)</a></li>
                <li><a href="https://woocommerce.com/products/product-bundles/" target="_blank">
                    Product Bundles for WooCommerce</a></li>
                <li><a href="https://www.learndash.com/" target="_blank">LearnDash</a></li>
                <li><a href="https://memberpress.com/" target="_blank">Memberpress</a></li>
            </ul>
        </div><!-- .card -->
        
        <!-- 5) Roadmap -->
        <div class="card" style="padding: 1rem;">
            <h2 style="margin-top: 0;">
                <?php esc_html_e('Roadmap', 'hw-elementor-woo-dynamic'); ?>
            </h2>
            <p class="description">
                <?php esc_html_e('Here you can see which features are planned for future releases.', 'hw-elementor-woo-dynamic'); ?>
            </p>
            <hr />
            <!-- Display upcoming features as checkboxes -->
            <ul style="list-style: none; margin: 0; padding: 0;">
                <li style="margin-bottom: 0.5rem;">
                    <label>
                        <input type="checkbox" checked disabled />
                        <?php esc_html_e('Removed the WooCommerce dependency', 'hw-elementor-woo-dynamic'); ?>
                    </label>
                </li>
                <li style="margin-bottom: 0.5rem;">
                    <label>
                        <input type="checkbox" checked disabled />
                        <?php esc_html_e('Migrated the previously created plugin functions: Elementor Extra Theme Conditions', 'hw-elementor-woo-dynamic'); ?>
                    </label>
                </li>
                <li style="margin-bottom: 0.5rem;">
                    <label>
                        <input type="checkbox" checked disabled />
                        <?php esc_html_e('Woo Membership and subscriptions dynamic tags / functions', 'hw-elementor-woo-dynamic'); ?>
                    </label>
                </li>
                <li style="margin-bottom: 0.5rem;">
                    <label>
                        <input type="checkbox" checked disabled />
                        <?php esc_html_e('Learndash compatibity', 'hw-elementor-woo-dynamic'); ?>
                    </label>
                </li>
                <li style="margin-bottom: 0.5rem;">
                    <label>
                        <input type="checkbox" checked disabled />
                        <?php esc_html_e('New Widgets', 'hw-elementor-woo-dynamic'); ?>
                    </label>
                </li>
                <li style="margin-bottom: 0.5rem;">
                    <label>
                        <input type="checkbox" disabled />
                        <?php esc_html_e('TutorLMS', 'hw-elementor-woo-dynamic'); ?>
                    </label>
                </li>
                <li style="margin-bottom: 0.5rem;">
                    <label>
                        <input type="checkbox" disabled />
                        <?php esc_html_e('Woo Myaccount config', 'hw-elementor-woo-dynamic'); ?>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" disabled />
                        <?php esc_html_e('New JetEngine custom callbacks', 'hw-elementor-woo-dynamic'); ?>
                    </label>
                </li>
            </ul>
        </div><!-- .card -->

        <!-- 5) My Other Useful Works -->
        <div class="card" style="padding: 1rem;">
            <h2 style="margin-top: 0;">
                <?php esc_html_e('My Other Useful Works', 'hw-elementor-woo-dynamic'); ?>
            </h2>
            <p class="description">
                <?php esc_html_e('Other projects of mine you might find useful:', 'hw-elementor-woo-dynamic'); ?>
            </p>
            <hr />
            <ul style="list-style: none; padding-left: 0; margin: 0;">
                <!-- WP Agent Skills (not a plugin) -->
                <li style="margin-bottom: 1em;">
                    <strong>
                        <a href="https://github.com/Lonsdale201/wp-agent-skills" target="_blank">WP Agent Skills</a>
                    </strong>
                    <em>(<?php esc_html_e('Not a plugin', 'hw-elementor-woo-dynamic'); ?>)</em><br/>
                    <em>
                        <?php esc_html_e('More than 300 AI agent skills for more than 12 plugins.', 'hw-elementor-woo-dynamic'); ?>
                    </em><br/>
                    <a href="https://github.com/Lonsdale201/wp-agent-skills" target="_blank">
                        <?php esc_html_e('View on GitHub', 'hw-elementor-woo-dynamic'); ?>
                    </a>
                </li>

                <!-- Better Route (PHP library) -->
                <li style="margin-bottom: 1em;">
                    <strong>
                        <a href="https://github.com/Lonsdale201/better-route" target="_blank">Better Route</a>
                    </strong>
                    <em>(<?php esc_html_e('PHP library, not a plugin', 'hw-elementor-woo-dynamic'); ?>)</em><br/>
                    <em>
                        <?php esc_html_e('A PHP library for WordPress to build typed, validated REST APIs — fluent router, resource CRUD, auth / rate-limit / idempotency middleware and OpenAPI export. A dedicated AI agent skill set is available for it.', 'hw-elementor-woo-dynamic'); ?>
                    </em><br/>
                    <a href="https://github.com/Lonsdale201/better-route" target="_blank">
                        <?php esc_html_e('GitHub', 'hw-elementor-woo-dynamic'); ?>
                    </a>
                    &nbsp;|&nbsp;
                    <a href="https://lonsdale201.github.io/better-docs/docs/better-route/getting-started/installation/" target="_blank">
                        <?php esc_html_e('Documentation', 'hw-elementor-woo-dynamic'); ?>
                    </a>
                </li>

                <!-- FluentCRM Subscriptions for JetFormBuilder -->
                <li style="margin-bottom: 1em;">
                    <strong>
                        <a href="https://github.com/Lonsdale201/fluentcrm-subscriptions-for-jetformbuilder" target="_blank">FluentCRM Subscriptions for JetFormBuilder</a>
                    </strong><br/>
                    <em>
                        <?php esc_html_e('Adds subscription functionality to FluentCRM through JetFormBuilder forms.', 'hw-elementor-woo-dynamic'); ?>
                    </em><br/>
                    <a href="https://github.com/Lonsdale201/fluentcrm-subscriptions-for-jetformbuilder" target="_blank">
                        <?php esc_html_e('Download Link', 'hw-elementor-woo-dynamic'); ?>
                    </a>
                </li>

                <!-- AI for JetFormBuilder -->
                <li style="margin-bottom: 1em;">
                    <strong>
                        <a href="https://github.com/Lonsdale201/ai-for-jetformbuilder" target="_blank">AI for JetFormBuilder</a>
                    </strong><br/>
                    <em>
                        <?php esc_html_e('Add AI-powered actions to JetFormBuilder — process form submissions with an AI service.', 'hw-elementor-woo-dynamic'); ?>
                    </em><br/>
                    <a href="https://github.com/Lonsdale201/ai-for-jetformbuilder" target="_blank">
                        <?php esc_html_e('Download Link', 'hw-elementor-woo-dynamic'); ?>
                    </a>
                </li>

                <!-- Google Sheet for JetFormBuilder -->
                <li style="margin-bottom: 1em;">
                    <strong>
                        <a href="https://github.com/Lonsdale201/google-sheet-for-jetformbuilder" target="_blank">Google Sheet for JetFormBuilder</a>
                    </strong><br/>
                    <em>
                        <?php esc_html_e('Send JetFormBuilder form submissions straight to Google Sheets.', 'hw-elementor-woo-dynamic'); ?>
                    </em><br/>
                    <a href="https://github.com/Lonsdale201/google-sheet-for-jetformbuilder" target="_blank">
                        <?php esc_html_e('Download Link', 'hw-elementor-woo-dynamic'); ?>
                    </a>
                </li>
            </ul>
        </div><!-- .card -->

        <!-- 6) Changelog -->
        <div class="card" style="padding: 1rem;">
            <h2 style="margin-top: 0;">
                <?php esc_html_e('Changelog', 'hw-elementor-woo-dynamic'); ?>
            </h2>
            <p class="description">
                <?php esc_html_e('Latest updates and improvements:', 'hw-elementor-woo-dynamic'); ?>
            </p>
            <hr />
            <div style="max-height: 350px; overflow-y: auto;">
                <strong><?php esc_html_e('Version 2.4.5.3 - 2026.06.17', 'hw-elementor-woo-dynamic'); ?></strong>
                    <ul style="list-style: square; margin-left: 1.2rem;">
                        <li><?php esc_html_e('New: WP-CLI support — manage the plugin from the terminal via `wp dynamic-ext` (status diagnostics, enable / disable dynamic tags, export / import settings).', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('WC Order HPOS Query Builder: new Include / Exclude Brands filters, and a Shipping Methods filter in the General tab.', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Improved dynamic tag registration (migrated to Elementor’s modern dynamic_tags/register API).', 'hw-elementor-woo-dynamic'); ?></li>
                    </ul>
                <strong><?php esc_html_e('Version 2.4.5.2 - 2026.04.10', 'hw-elementor-woo-dynamic'); ?></strong>
                    <ul style="list-style: square; margin-left: 1.2rem;">
                        <li><?php esc_html_e('Cart Values and Cart Tax Values dynamic tags now refresh via AJAX on cart events (no page reload needed).', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('New JetEngine Dynamic Visibility condition: Purchased Products (WooCommerce) — checks if the current user has purchased any of the selected products.', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('New JetEngine Dynamic Visibility condition: Has Selected Subscription Active (Woo Subscriptions) — checks if the user has an active subscription for the selected products.', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Free Shipping Amount tag: new Advanced Mode with {freeamount} and {remainingamount} macros, and AJAX refresh support.', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('WC Order HPOS Query Builder: added Include/Exclude Categories filters and macro support for all include/exclude fields.', 'hw-elementor-woo-dynamic'); ?></li>
                    </ul>
                <strong><?php esc_html_e('Version 2.4.5 - 2025.12.08', 'hw-elementor-woo-dynamic'); ?></strong>
                    <ul style="list-style: square; margin-left: 1.2rem;">
                        <li><?php esc_html_e('New Widget: Dynamic text editor', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Fixed a bug that occurred when Elementor Pro was not installed, causing an infinite editor loading error.', 'hw-elementor-woo-dynamic'); ?></li>
                    </ul>
                 <strong><?php esc_html_e('Version 2.4.4.2 - 2025.11.06', 'hw-elementor-woo-dynamic'); ?></strong>
                    <ul style="list-style: square; margin-left: 1.2rem;">
                        <li><?php esc_html_e('New Auto Apply skeleton loader for Lisitng grid', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Now handle class additions more efficiently and have expanded the list of supported widgets with specific skeleton displays. ', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('FIxed how listing grid IDs are handled when using skeletons, and having multiple listing grids on the same page no longer causes issues. ', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Advanced stock, and Stock Quantity extra dynamic tag received additions. ', 'hw-elementor-woo-dynamic'); ?></li>
                    </ul>
                <strong><?php esc_html_e('Version 2.4.4.1 - 2025.11.05', 'hw-elementor-woo-dynamic'); ?></strong>
                    <ul style="list-style: square; margin-left: 1.2rem;">
                        <li><?php esc_html_e('Added new options for the Skeleton loader', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Disabled the standard Lisitng grid opacity effect if skeleton systems are used with loadmore and infinitescroll ', 'hw-elementor-woo-dynamic'); ?></li>
                    </ul>
                <strong><?php esc_html_e('Version 2.4.4 - 2025.11.04', 'hw-elementor-woo-dynamic'); ?></strong>
                    <ul style="list-style: square; margin-left: 1.2rem;">
                        <li><?php esc_html_e('JetEngine Listing Grid: full skeleton loading mechanism implemented with support for JetSmartFilters, pagination, load more, and infinite scroll.', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Added a new Dynamic Tag: Post Content.', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Added a new Query Filter for JetEngine Query Builder (Woo HPOS): include or exclude products based on their presence in an order.', 'hw-elementor-woo-dynamic'); ?></li>
                    </ul>
                <strong><?php esc_html_e('Version 2.4.3 - 2025.10.28', 'hw-elementor-woo-dynamic'); ?></strong>
                    <ul style="list-style: square; margin-left: 1.2rem;">
                        <li><?php esc_html_e('JetEngine Query Builder: new WC Order HPOS Query Type with payment-method filtering and improved date handling.', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Added WooCommerce order dynamic tags for JetEngine listings: Order ID (linkable), Order Date, Order Status, Order Link, Total Price, Order Items Count, Ordered Items (enhanced variation output), Order Customer, Order Payment Method.', 'hw-elementor-woo-dynamic'); ?></li>
                    </ul>
                <strong><?php esc_html_e('Version 2.4.2.1 - 2025.10.10', 'hw-elementor-woo-dynamic'); ?></strong>
                    <ul style="list-style: square; margin-left: 1.2rem;">
                        <li><?php esc_html_e('Fixed the Author archive Elementor template condition issue.', 'hw-elementor-woo-dynamic'); ?></li>
                    </ul>
                <strong><?php esc_html_e('Version 2.4.2 - 2025.10.09', 'hw-elementor-woo-dynamic'); ?></strong>
                    <ul style="list-style: square; margin-left: 1.2rem;">
                        <li><?php esc_html_e('Added two new LearnDash JetEngine Dynamic Visibility conditions: Lessons Completed and Quizzes Completed.', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Fixed text-domain loading to support WordPress 6.7+ locale handling changes.', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Added a new global Elementor dynamic tag Author URL.', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Membership card widget now offers a “Linkable Card” option and uses localized WooCommerce Membership status labels.', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Introduced a new Dynamic Add to Calendar widget.', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Current User Role JetEngine macro now returns all roles assigned to the user.', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Added the Dynamic Opening Hours widget.', 'hw-elementor-woo-dynamic'); ?></li>
                    </ul>
                <strong><?php esc_html_e('Version 2.4.0 - 2025.06.08', 'hw-elementor-woo-dynamic'); ?></strong>
                    <ul style="list-style: square; margin-left: 1.2rem;">
                        <li><?php esc_html_e('Elementor widgets is here! Dynamic Checkbox, Dynamic Bulk Add to cart, Membership cards, Dynamic Calculations', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('New Dynamic tags', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Improving existing dynamic tags', 'hw-elementor-woo-dynamic'); ?></li>
                        <hr>
                        <li><?php esc_html_e('full changelog can be found on github', 'hw-elementor-woo-dynamic'); ?></li>
                    </ul>
                <strong><?php esc_html_e('Version 2.3.3.1 - 2025.05.11', 'hw-elementor-woo-dynamic'); ?></strong>
                    <ul style="list-style: square; margin-left: 1.2rem;">
                        <li><?php esc_html_e('New update server implmentation, and fallback to the old server.', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Fixed the critical issue cause a missing file', 'hw-elementor-woo-dynamic'); ?></li>
                    </ul>
                <strong><?php esc_html_e('Version 2.3.2 - 2025.03.21', 'hw-elementor-woo-dynamic'); ?></strong>
                    <ul style="list-style: square; margin-left: 1.2rem;">
                        <li><?php esc_html_e('Fixed the Course Resume and Course Resume Text problems, now they work correctly even if the course has not started yet', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('We have improved the Lessons Number dynamic tag (learndash), now it can be set to take into account topics and aggregate them with lessons', 'hw-elementor-woo-dynamic'); ?></li>
                        <hr>
                        <li><?php esc_html_e('New JetEngine Macro: Current User Role', 'hw-elementor-woo-dynamic'); ?></li>
                        <hr>
                        <li><?php esc_html_e('New JetEngine Dynamic Visibility for Learndash: Course Not started', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('New JetEngine Dynamic Visibility for global usage: User Registration Time Elapsed', 'hw-elementor-woo-dynamic'); ?></li>
                    </ul>
                <strong><?php esc_html_e('Version 2.3.1 - 2025.02.14', 'hw-elementor-woo-dynamic'); ?></strong>
                    <ul style="list-style: square; margin-left: 1.2rem;">
                        <li><?php esc_html_e('Fixed the Advanced price dynamic tag WC_Tax::calc_tax issue', 'hw-elementor-woo-dynamic'); ?></li>
                        <hr>
                    </ul>
                <strong><?php esc_html_e('Version 2.3 - 2025.02.11', 'hw-elementor-woo-dynamic'); ?></strong>
                    <ul style="list-style: square; margin-left: 1.2rem;">
                        <li><b><?php esc_html_e('Removed the WooCommerce dependency', 'hw-elementor-woo-dynamic'); ?></b></li>
                        <hr>
                        <li><?php esc_html_e('New Dynamic tag for WooCommerce: Advanced price', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('New Elementor Theme conditions for WooCommerce: Is Product Individually Sold', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('New Elementor Theme Conditions for WooCommerce: Is product Bundle (Product Bundles for WooCommerce plugin)', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('Spec badge improvements: Woo Subs, Woo Bundle, and Sale support for badges', 'hw-elementor-woo-dynamic'); ?></li>
                        <hr>
                        <li><?php esc_html_e('New Dynamic Visibility - Memberpress - Access memberships', 'hw-elementor-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('New JetEngine Callback - Convert Units for Dynamic field widget', 'hw-elementor-woo-dynamic'); ?></li>
                        <br />
                        <li><?php esc_html_e('Backend updates:', 'hw-elementor-woo-dynamic'); ?></li>
                        <ul style="list-style: circle; margin-left: 1.2rem;">
                            <li><?php esc_html_e('New badge if new functions available', 'hw-elementor-woo-dynamic'); ?></li>
                            <li><?php esc_html_e('New Settings design', 'hw-elementor-woo-dynamic'); ?></li>
                        </ul>
                    </ul>
            </div>
        </div>
    </div><!-- .hw-grid-container -->
</div><!-- .wrap -->
