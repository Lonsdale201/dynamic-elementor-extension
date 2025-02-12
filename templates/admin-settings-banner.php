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
    <h1><?php esc_html_e( 'Dynamic Elementor Extension', 'hw-ele-woo-dynamic' ); ?></h1>
    <p>
        <?php esc_html_e( 'Extra dynamic tags and other useful functions (conditionally for WooCommerce, Memberships, Subscriptions, and LearnDash).', 'hw-ele-woo-dynamic' ); ?>
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
                <?php esc_html_e( 'Contact & Profiles', 'hw-ele-woo-dynamic' ); ?>
            </h2>
            <p class="description">
                <?php esc_html_e( 'Reach out or find my code on GitHub/Gist.', 'hw-ele-woo-dynamic' ); ?>
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
                <?php esc_html_e( 'Plugin Info', 'hw-ele-woo-dynamic' ); ?>
            </h2>
            <p class="description">
                <?php esc_html_e( 'Main repository & documentation links.', 'hw-ele-woo-dynamic' ); ?>
            </p>
            <hr />
            <table class="widefat striped" style="margin-bottom: 0;">
                <tbody>
                    <tr>
                        <td><strong><?php esc_html_e('GitHub Plugin Page:', 'hw-ele-woo-dynamic'); ?></strong></td>
                        <td>
                            <a href="https://github.com/Lonsdale201/dynamic-elementor-extension" target="_blank">
                                github.com/Lonsdale201/dynamic-elementor-extension
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e('Issues / Requests:', 'hw-ele-woo-dynamic'); ?></strong></td>
                        <td>
                            <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/issues" target="_blank">
                                <?php esc_html_e('GitHub Issues', 'hw-ele-woo-dynamic'); ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e('Documentation:', 'hw-ele-woo-dynamic'); ?></strong></td>
                        <td>
                            <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/Start-here" 
                               target="_blank">
                                <?php esc_html_e('Wiki / Start-here', 'hw-ele-woo-dynamic'); ?>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div><!-- .card -->

        <!-- 3) Supported Plugins -->
        <div class="card" style="padding: 1rem;">
            <h2 style="margin-top: 0;">
                <?php esc_html_e('Supported Plugins', 'hw-ele-woo-dynamic'); ?>
            </h2>
            <p class="description">
                <?php esc_html_e('The following plugins integrate seamlessly:', 'hw-ele-woo-dynamic'); ?>
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

        <!-- 4) Roadmap -->
        <div class="card" style="padding: 1rem;">
            <h2 style="margin-top: 0;">
                <?php esc_html_e('Roadmap', 'hw-ele-woo-dynamic'); ?>
            </h2>
            <p class="description">
                <?php esc_html_e('Here you can see which features are planned for future releases.', 'hw-ele-woo-dynamic'); ?>
            </p>
            <hr />
            <!-- Display upcoming features as checkboxes -->
            <ul style="list-style: none; margin: 0; padding: 0;">
                <li style="margin-bottom: 0.5rem;">
                    <label>
                        <input type="checkbox" checked disabled />
                        <?php esc_html_e('Removed the WooCommerce dependency', 'hw-ele-woo-dynamic'); ?>
                    </label>
                </li>
                <li style="margin-bottom: 0.5rem;">
                    <label>
                        <input type="checkbox" checked disabled />
                        <?php esc_html_e('Migrated the previously created plugin functions: Elementor Extra Theme Conditions', 'hw-ele-woo-dynamic'); ?>
                    </label>
                </li>
                <li style="margin-bottom: 0.5rem;">
                    <label>
                        <input type="checkbox" checked disabled />
                        <?php esc_html_e('Woo Membership and subscriptions dynamic tags / functions', 'hw-ele-woo-dynamic'); ?>
                    </label>
                </li>
                <li style="margin-bottom: 0.5rem;">
                    <label>
                        <input type="checkbox" checked disabled />
                        <?php esc_html_e('Learndash compatibity', 'hw-ele-woo-dynamic'); ?>
                    </label>
                </li>
                <li style="margin-bottom: 0.5rem;">
                    <label>
                        <input type="checkbox" disabled />
                        <?php esc_html_e('TutorLMS', 'hw-ele-woo-dynamic'); ?>
                    </label>
                </li>
                <li style="margin-bottom: 0.5rem;">
                    <label>
                        <input type="checkbox" disabled />
                        <?php esc_html_e('Woo Myaccount config', 'hw-ele-woo-dynamic'); ?>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" disabled />
                        <?php esc_html_e('New JetEngine custom callbacks', 'hw-ele-woo-dynamic'); ?>
                    </label>
                </li>
            </ul>
        </div><!-- .card -->

        <!-- 5) My Other Plugins -->
        <div class="card" style="padding: 1rem;">
            <h2 style="margin-top: 0;">
                <?php esc_html_e('My Other Plugins', 'hw-ele-woo-dynamic'); ?>
            </h2>
            <p class="description">
                <?php esc_html_e('Check out some of my other WordPress plugin projects:', 'hw-ele-woo-dynamic'); ?>
            </p>
            <hr />
            <ul style="list-style: none; padding-left: 0; margin: 0;">
                <!-- Fluent Extend Triggers and Actions -->
                <li style="margin-bottom: 1em;">
                    <strong>
                        <a href="#" target="_blank">Fluent Extend Triggers and Actions</a>
                    </strong><br/>
                    <em>
                        <?php esc_html_e('Extra triggers, actions and others for WooCommerce, WordPress, JetFormBuilder, and Jetreviews', 'hw-ele-woo-dynamic'); ?>
                    </em><br/>
                    <a href="https://github.com/Lonsdale201/fluent-extend-triggers-and-actions" target="_blank">
                        <?php esc_html_e('Download Link', 'hw-ele-woo-dynamic'); ?>
                    </a>
                </li>

                <!-- Simple Loyalty Program for WooCommerce -->
                <li style="margin-bottom: 1em;">
                    <strong>
                        <a href="#" target="_blank">Simple Loyalty Program for WooCommerce</a>
                    </strong><br/>
                    <em>
                        <?php esc_html_e('This plugin adds customizable loyalty features to WooCommerce, offering various settings for a tailored experience.', 'hw-ele-woo-dynamic'); ?>
                    </em><br/>
                    <a href="https://github.com/Lonsdale201/fluent-extend-triggers-and-actions" target="_blank">
                        <?php esc_html_e('Download Link', 'hw-ele-woo-dynamic'); ?>
                    </a>
                </li>

                <!-- Simple My menu -->
                <li style="margin-bottom: 1em;">
                    <strong>
                        <a href="#" target="_blank">Simple My menu</a>
                    </strong><br/>
                    <em>
                        <?php esc_html_e('A simple plugin that allows you to create monograms for the users, create and display a custom account menu, and extend other menus with visibility settings, badges, icons, etc.', 'hw-ele-woo-dynamic'); ?>
                    </em><br/>
                    <a href="https://github.com/Lonsdale201/wp-mymenu" target="_blank">
                        <?php esc_html_e('Download Link', 'hw-ele-woo-dynamic'); ?>
                    </a>
                </li>

                <!-- Unity WebGL Integration -->
                <li style="margin-bottom: 1em;">
                    <strong>
                        <a href="#" target="_blank">Unity WebGL Integration</a>
                    </strong><br/>
                    <em>
                        <?php esc_html_e('Simple loader and initializer plugin for WordPress + Unity + WebGL projects.', 'hw-ele-woo-dynamic'); ?>
                    </em><br/>
                    <a href="https://github.com/Lonsdale201/unity-for-wp" target="_blank">
                        <?php esc_html_e('Download Link', 'hw-ele-woo-dynamic'); ?>
                    </a>
                </li>
            </ul>
        </div><!-- .card -->

        <!-- 6) Changelog -->
        <div class="card" style="padding: 1rem;">
            <h2 style="margin-top: 0;">
                <?php esc_html_e('Changelog', 'hw-ele-woo-dynamic'); ?>
            </h2>
            <p class="description">
                <?php esc_html_e('Latest updates and improvements:', 'hw-ele-woo-dynamic'); ?>
            </p>
            <hr />
            <div style="max-height: 350px; overflow-y: auto;">
                <strong><?php esc_html_e('Version 2.3 - 2025.02.11', 'hw-ele-woo-dynamic'); ?></strong>
                <ul style="list-style: square; margin-left: 1.2rem;">
                    <li><b><?php esc_html_e('Removed the WooCommerce dependency', 'hw-ele-woo-dynamic'); ?></b></li>
                    <hr>
                    <li><?php esc_html_e('New Dynamic tag for WooCommerce: Advanced price', 'hw-ele-woo-dynamic'); ?></li>
                    <li><?php esc_html_e('New Elementor Theme conditions for WooCommerce: Is Product Individually Sold', 'hw-ele-woo-dynamic'); ?></li>
                    <li><?php esc_html_e('New Elementor Theme Conditions for WooCommerce: Is product Bundle (Product Bundles for WooCommerce plugin)', 'hw-ele-woo-dynamic'); ?></li>
                    <li><?php esc_html_e('Spec badge improvements: Woo Subs, Woo Bundle, and Sale support for badges', 'hw-ele-woo-dynamic'); ?></li>
                    <hr>
                    <li><?php esc_html_e('New Dynamic Visibility - Memberpress - Access memberships', 'hw-ele-woo-dynamic'); ?></li>
                    <li><?php esc_html_e('New JetEngine Callback - Convert Units for Dynamic field widget', 'hw-ele-woo-dynamic'); ?></li>
                    <br />
                    <li><?php esc_html_e('Backend updates:', 'hw-ele-woo-dynamic'); ?></li>
                    <ul style="list-style: circle; margin-left: 1.2rem;">
                        <li><?php esc_html_e('New badge if new functions available', 'hw-ele-woo-dynamic'); ?></li>
                        <li><?php esc_html_e('New Settings design', 'hw-ele-woo-dynamic'); ?></li>
                    </ul>
                </ul>
            </div>
        </div><!-- .card -->

    </div><!-- .hw-grid-container -->
</div><!-- .wrap -->
