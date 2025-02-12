<?php

namespace HelloWP\HWEleWooDynamic\Modules;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

/**
 * Class DynamicSettings
 *
 * Developer Notes:
 * ----------------
 * Registers an admin settings page with multiple sections and fields for various dynamic tags.
 * WooCommerce-specific sections/fields are shown only if WooCommerce is active.
 * LearnDash-, Membership-, and Subscriptions-specific sections are also conditionally shown.
 */
class DynamicSettings {

    /**
     * Singleton instance.
     *
     * @var DynamicSettings|null
     */
    private static $instance = null;

    /**
     * Stores dynamic tags configuration grouped by section.
     *
     * @var array
     */
    private $tags_config = [];

    /**
     * Stores data for each settings section (title, description).
     *
     * @var array
     */
    private $sections;

    /**
     * Provides a global access point to get the single instance of this class.
     *
     * @return DynamicSettings
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        // Enqueue scripts and styles for our settings page
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        $this->initialize_tags_config();
    }

    /**
     * Adds the "Dynamic Elements" menu page under the WordPress admin menu.
     */
    public function add_settings_page() {
        if ( ! current_user_can( 'administrator' ) ) {
            return;
        }

        add_menu_page(
            __( 'Dynamic Elements', 'hw-ele-woo-dynamic' ),
            __( 'Dynamic Elements', 'hw-ele-woo-dynamic' ),
            'manage_options',
            'dynamic-extension-settings',
            [ $this, 'settings_page_html' ],
            '',
            65
        );
    }

     /**
     *
     * @param string $hook_suffix
     */
    public function enqueue_admin_assets( $hook_suffix ) {
        if ( $hook_suffix === 'toplevel_page_dynamic-extension-settings' ) {
            wp_enqueue_style(
                'dynamic-settings-css',
                HW_ELE_DYNAMIC_URL . 'assets/dynamicsettings.css',
                [],
                '1.0.0'
            );
        }
    }
    

    /**
     * Renders the HTML for our settings page.
     */
    public function settings_page_html() {
        if ( ! current_user_can( 'administrator' ) ) {
            return;
        }
        $template_path = HW_ELE_DYNAMIC_PATH . 'templates/admin-settings-banner.php';

        if ( file_exists( $template_path ) ) {
            include $template_path;
        }

        ?>
        <div class="wrap">
            <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
            <form action="options.php" method="post">
                <?php
                    settings_fields( 'dynamic_extension' );
                    do_settings_sections( 'dynamic-extension-settings' );
                    echo '<input type="hidden" name="dynamic_extension_settings[settings_submitted]" value="1">';
                    submit_button( __( 'Save Changes', 'hw-ele-woo-dynamic' ) );
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Initializes sections data with titles and descriptions for each settings section.
     */
    private function initialize_sections_with_descriptions() {
        $this->sections = [
            'woo_extras' => [
                'title'       => __( '[WooCommerce] Single Product page / loop dynamic tags', 'hw-ele-woo-dynamic' ),
                'description' => __(
                    'This section contains settings for single product pages and product loops.<br> Check the <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/Woo-%E2%80%90-Product-Dynamic-Tags">wikipedia section</a> for tutorial',
                    'hw-ele-woo-dynamic'
                ),
            ],
            'global' => [
                'title'       => __( '[WooCommerce] Global dynamic tags', 'hw-ele-woo-dynamic' ),
                'description' => __( 'Settings that apply globally across the site.', 'hw-ele-woo-dynamic' ),
            ],
            'cart_value' => [
                'title'       => __( '[WooCommerce] Cart dynamic tags', 'hw-ele-woo-dynamic' ),
                'description' => __( 'Settings that apply globally across the site.', 'hw-ele-woo-dynamic' ),
            ],
            'customer_specific' => [
                'title'       => __( '[WooCommerce] Customer Specific dynamic tags', 'hw-ele-woo-dynamic' ),
                'description' => __(
                    'Specific tags that return current logged-in user data. Check the <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/Woo-%E2%80%90-Customer-Dynamic-Tags">wikipedia section</a> for tutorial',
                    'hw-ele-woo-dynamic'
                ),
            ],
            'woo_membership' => [
                'title'       => __( '[WooCommerce Membership] dynamic tags', 'hw-ele-woo-dynamic' ),
                'description' => __(
                    'Specific tags for WooCommerce Membership. Check the <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/Woo-Membership-%E2%80%90-Dynamic-Tags">wikipedia section</a> for tutorial',
                    'hw-ele-woo-dynamic'
                ),
            ],
            'woo_subscriptions' => [
                'title'       => __( '[WooCommerce Subscriptions] dynamic tags', 'hw-ele-woo-dynamic' ),
                'description' => __(
                    'Specific tags for WooCommerce Subscriptions. Check the <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/Woo-Subscriptions-%E2%80%90-Dynamic-Tags">wikipedia section</a> for tutorial',
                    'hw-ele-woo-dynamic'
                ),
            ],
            'ld_extras_courses' => [
                'title'       => __( '[Learndash] Course dynamic tags', 'hw-ele-woo-dynamic' ),
                'description' => __(
                    'Specific tags for the Learndash courses single page. Check the <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/LearnDash-%E2%80%90-Dynamic-Tags"> wikipedia section</a> for tutorial',
                    'hw-ele-woo-dynamic'
                ),
            ],
            'ld_extras_global' => [
                'title'       => __( '[Learndash] Global dynamic tags', 'hw-ele-woo-dynamic' ),
                'description' => __(
                    'Specific Global tags for Learndash. Check the <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/LearnDash-%E2%80%90-Dynamic-Tags"> wikipedia section</a> for tutorial',
                    'hw-ele-woo-dynamic'
                ),
            ],
            'wp_bar_products_informations' => [
                'title'       => __( 'Enable extra products informations in the admin toolbar', 'hw-ele-woo-dynamic' ),
                'description' => __(
                    'In the WP admin top bar you can configure which product information is displayed when viewing the product page on the frontend',
                    'hw-ele-woo-dynamic'
                ),
            ],
            'wp_bar_learndash_informations' => [
                'title'       => __( 'Enable extra learndash informations in the admin toolbar', 'hw-ele-woo-dynamic' ),
                'description' => __(
                    'In the WP admin top bar you can configure which LearnDash information is displayed when viewing the course page on the frontend',
                    'hw-ele-woo-dynamic'
                ),
            ],
            'my_account_dashboard' => [
                'title'       => __( 'My Account Dashboard', 'hw-ele-woo-dynamic' ),
                'description' => __(
                    'Here you can insert shortcode content in your WooCommerce dashboard endpoint. You can use shortcode, plain text, or HTML.',
                    'hw-ele-woo-dynamic'
                ),
            ],
            'my_account_orders' => [
                'title'       => __( 'My Account Orders', 'hw-ele-woo-dynamic' ),
                'description' => __(
                    'Here you can insert shortcode content in your WooCommerce orders endpoint. You can use shortcode, plain text, or HTML.',
                    'hw-ele-woo-dynamic'
                ),
            ],
        ];
    }

    /**
     * Registers settings sections and fields. Certain sections are conditionally skipped
     * if WooCommerce, Subscriptions, Memberships, or LearnDash are not active.
     */
    public function register_settings() {
        $this->initialize_sections_with_descriptions();

        register_setting(
            'dynamic_extension',
            'dynamic_extension_settings',
            [ $this, 'validate_settings' ]
        );

        // Loop through each defined section and add it, unless its dependencies are missing.
        foreach ( $this->sections as $id => $data ) {

            // WooCommerce-based sections
            if (
                ( $id === 'woo_extras' && ! Dependencies::is_woocommerce_active() ) ||
                ( $id === 'global' && ! Dependencies::is_woocommerce_active() ) ||
                ( $id === 'cart_value' && ! Dependencies::is_woocommerce_active() ) ||
                ( $id === 'customer_specific' && ! Dependencies::is_woocommerce_active() ) ||
                ( $id === 'my_account_dashboard' && ! Dependencies::is_woocommerce_active() ) ||
                ( $id === 'my_account_orders' && ! Dependencies::is_woocommerce_active() ) ||
                ( $id === 'wp_bar_products_informations' && ! Dependencies::is_woocommerce_active() ) ||

                // WooCommerce Membership
                ( $id === 'woo_membership' && ( ! Dependencies::is_woocommerce_active() || ! Dependencies::is_memberships_active() ) ) ||
                // WooCommerce Subscriptions
                ( $id === 'woo_subscriptions' && ( ! Dependencies::is_woocommerce_active() || ! Dependencies::is_subscriptions_active() ) ) ||

                // LearnDash
                ( $id === 'ld_extras_courses' && ! Dependencies::is_learndash_active() ) ||
                ( $id === 'ld_extras_global' && ! Dependencies::is_learndash_active() ) ||
                ( $id === 'wp_bar_learndash_informations' && ! Dependencies::is_learndash_active() )
            ) {
                continue;
            }

            // Add the settings section
            add_settings_section(
                'dynamic_extension_' . $id,
                $data['title'],
                function() use ( $data ) {
                    echo '<p>' . $data['description'] . '</p>';
                },
                'dynamic-extension-settings'
            );

            // Add fields to the section.
            $this->add_settings_to_sections( $id );
        }
    }

    /**
     * Initializes the array of tags for each section (used by add_settings_to_sections()).
     */
    private function initialize_tags_config() {
        $this->tags_config = [
            'global' => [
                'advanced_product_category' => __( 'Advanced Product Category', 'hw-ele-woo-dynamic' ),
                'free_shipping_amount'      => __( 'Free Shipping Amount', 'hw-ele-woo-dynamic' ),
            ],
            'woo_extras' => [
                'advanced_stock'               => __( 'Advanced Stock', 'hw-ele-woo-dynamic' ),
                'advanced_sale_badge'          => __( 'Advanced Sale Badge', 'hw-ele-woo-dynamic' ),
                'featured_badge'               => __( 'Featured Badge', 'hw-ele-woo-dynamic' ),
                'product_attributes'           => __( 'Product Attributes', 'hw-ele-woo-dynamic' ),
                'product_description'          => __( 'Product Description', 'hw-ele-woo-dynamic' ),
                'product_height'               => __( 'Product Height', 'hw-ele-woo-dynamic' ),
                'product_lenght'               => __( 'Product Length', 'hw-ele-woo-dynamic' ), 
                'product_shipping_class'       => __( 'Product Shipping Class', 'hw-ele-woo-dynamic' ),
                'product-tabs'                 => __( 'Product Tabs', 'hw-ele-woo-dynamic' ),
                'product_width'                => __( 'Product Width', 'hw-ele-woo-dynamic' ),
                'product_weight'               => __( 'Product Weight', 'hw-ele-woo-dynamic' ),
                'purchased_badge'              => __( 'Purchased Badge', 'hw-ele-woo-dynamic' ),
                'sale_time'                    => __( 'Sale Time', 'hw-ele-woo-dynamic' ),
                'stock_quantity_extra'         => __( 'Stock Quantity Extra', 'hw-ele-woo-dynamic' ),
                'spec_badge'                   => __( 'Spec Badge', 'hw-ele-woo-dynamic' ),
                'stock_quantity'               => __( 'Stock Quantity', 'hw-ele-woo-dynamic' ),
                'product_gallery'              => __( 'Product Gallery', 'hw-ele-woo-dynamic' ),
                'taxonomy-acf-meta'            => __( 'ACF Taxonomy Meta', 'hw-ele-woo-dynamic' ),
                'next_product'                 => __( 'Next Product', 'hw-ele-woo-dynamic' ),
                'next_product_image'           => __( 'Next Product Image', 'hw-ele-woo-dynamic' ),
                'prev_product'                 => __( 'Previous Product', 'hw-ele-woo-dynamic' ),
                'prev_product_image'           => __( 'Previous Product Image', 'hw-ele-woo-dynamic' ),
                'variable-price-range'         => __( 'Variable Price', 'hw-ele-woo-dynamic' ),
                'advanced-price'               => __( 'Advanced Price', 'hw-ele-woo-dynamic' ),
            ],
            'cart_value' => [
                'cart_values'     => __( 'Cart Values', 'hw-ele-woo-dynamic' ),
                'cart_tax_values' => __( 'Cart Tax Values', 'hw-ele-woo-dynamic' ),
            ],
            'customer_specific' => [
                'customer_details'     => __( 'Customer Details', 'hw-ele-woo-dynamic' ),
                'completed_order'      => __( 'Completed Order', 'hw-ele-woo-dynamic' ),
                'purchased_products'   => __( 'Purchased Products', 'hw-ele-woo-dynamic' ),
                'total_spent'          => __( 'Total Spent', 'hw-ele-woo-dynamic' ),
                'my_account_endpoints' => __( 'MyAccount Endpoints', 'hw-ele-woo-dynamic' ),
                'last_order'           => __( 'Last Order', 'hw-ele-woo-dynamic' ),
                'user_role'            => __( 'User Role', 'hw-ele-woo-dynamic' ),
                'customer_logout'      => __( 'Customer logout', 'hw-ele-woo-dynamic' ),
            ],
            'woo_membership' => [
                'active_membership'         => __( 'Active Membership', 'hw-ele-woo-dynamic' ),
                'my_account_membershipLink' => __( 'MyAccount Membership Link', 'hw-ele-woo-dynamic' ),
                'active_membership_data'    => __( 'Active Membership Data', 'hw-ele-woo-dynamic' ),
                'current_membership_data'   => __( 'Current Membership Data', 'hw-ele-woo-dynamic' ),
                'restricted_products_view'  => __( 'Restricted Products View', 'hw-ele-woo-dynamic' ),
            ],
            'woo_subscriptions' => [
                'active_subscription'        => __( 'Active Subscription', 'hw-ele-woo-dynamic' ),
                'my_account_subscriptionlink'=> __( 'MyAccount Subscription Link', 'hw-ele-woo-dynamic' ),
                'active_subscription_data'   => __( 'Active Subscription Data', 'hw-ele-woo-dynamic' ),
            ],
            'ld_extras_courses' => [
                'awarded_points'              => __( 'Awarded on Completion', 'hw-ele-woo-dynamic' ),
                'ld_lessons'                  => __( 'Lessons Number', 'hw-ele-woo-dynamic' ),
                'ld_quiz'                     => __( 'Quiz Numbers', 'hw-ele-woo-dynamic' ),
                'enrolled_users_count'        => __( 'Students number', 'hw-ele-woo-dynamic' ),
                'last-activity'               => __( 'Last Activity', 'hw-ele-woo-dynamic' ),
                'progress-percentage'         => __( 'Course Progress', 'hw-ele-woo-dynamic' ),
                'user-course-status'          => __( 'Course Status', 'hw-ele-woo-dynamic' ),
                'access-expires'              => __( 'Access Expires', 'hw-ele-woo-dynamic' ),
                'course-materials'            => __( 'Course Materials', 'hw-ele-woo-dynamic' ),
                'course-access-type'          => __( 'Course Access Type', 'hw-ele-woo-dynamic' ),
                'student-limit'               => __( 'Student Limit', 'hw-ele-woo-dynamic' ),
                'course-start-date'           => __( 'Course Start Date', 'hw-ele-woo-dynamic' ),
                'required-points'            => __( 'Course Required Points For Access', 'hw-ele-woo-dynamic' ),
                'course-price'                => __( 'Course Price', 'hw-ele-woo-dynamic' ),
                'topics-counter'              => __( 'Topics Numbers', 'hw-ele-woo-dynamic' ),
                'certificates-link'           => __( 'Certificates Link', 'hw-ele-woo-dynamic' ),
                'course-prerequisites-list'   => __( 'Course Prerequisites List', 'hw-ele-woo-dynamic' ),
                'course-resume-text'          => __( 'Course Resume Text', 'hw-ele-woo-dynamic' ),
                'course-resume'               => __( 'Course Resume URL', 'hw-ele-woo-dynamic' ),
                'course-content'              => __( 'Course Content', 'hw-ele-woo-dynamic' ),
                'course-groups'               => __( 'Course Part of Groups', 'hw-ele-woo-dynamic' ),
            ],
            'ld_extras_global' => [
                'user-completed-courses-count'     => __( 'User Completed Courses Count', 'hw-ele-woo-dynamic' ),
                'user-course-points'               => __( 'User Course Points', 'hw-ele-woo-dynamic' ),
                'user-available-courses-count'      => __( 'User Enrolled Courses Count', 'hw-ele-woo-dynamic' ),
                'user-achieved-certificates-count'  => __( 'User Achieved Certificates Count', 'hw-ele-woo-dynamic' ),
                'user-groups-count'                 => __( 'User Groups Count', 'hw-ele-woo-dynamic' ),
            ],
            'wp_bar_products_informations' => [
                'product_type'     => __( 'Product Type', 'hw-ele-woo-dynamic' ),
                'product_sku'      => __( 'Product Sku', 'hw-ele-woo-dynamic' ),
                'product_inventory'=> __( 'Product Inventory', 'hw-ele-woo-dynamic' ),
                'shipping_class'   => __( 'Shipping Class', 'hw-ele-woo-dynamic' ),
                'product_status'   => __( 'Product Status', 'hw-ele-woo-dynamic' ),
            ],
            'wp_bar_learndash_informations' => [
                'ld_bar_lessons' => __( 'Lessons', 'hw-ele-woo-dynamic' ),
                'ld_bar_topics'  => __( 'Topics', 'hw-ele-woo-dynamic' ),
                'ld_bar_quizes'  => __( 'Quiz', 'hw-ele-woo-dynamic' ),
            ],
        ];
    }

    /**
     * Adds settings fields to each section by iterating through the $tags_config array.
     * Also adds the "My Account Dashboard" and "My Account Orders" textareas (if Woo is active).
     *
     * @param string $section_id The ID of the section to add fields to.
     */
    private function add_settings_to_sections( $section_id ) {
        // If there's a tags group for this section, add a field with checkboxes.
        if ( isset( $this->tags_config[ $section_id ] ) ) {
            $tags  = $this->tags_config[ $section_id ];
            $label = __( 'Enable Dynamic Tags', 'hw-ele-woo-dynamic' );

            if ( $section_id === 'wp_bar_products_informations' ) {
                $label = __( 'Enable Product Information Display', 'hw-ele-woo-dynamic' );
            } elseif ( $section_id === 'wp_bar_learndash_informations' ) {
                $label = __( 'Enable Learndash Info Display', 'hw-ele-woo-dynamic' );
            }

            add_settings_field(
                'dynamic_extension_' . $section_id . '_tags',
                $label,
                [ $this, 'woo_tags_field_html' ],
                'dynamic-extension-settings',
                'dynamic_extension_' . $section_id,
                [ 'tags' => $tags, 'section' => $section_id ]
            );
        }

        if ( in_array( $section_id, [ 'my_account_dashboard', 'my_account_orders' ], true ) ) {
            if ( Dependencies::is_woocommerce_active() ) {
                // Endpoint settings
                if ( $section_id === 'my_account_dashboard' ) {
                    add_settings_field(
                        'dashboard_before_content',
                        __( 'Insert Before Dashboard Content', 'hw-ele-woo-dynamic' ),
                        [ $this, 'render_textarea_field' ],
                        'dynamic-extension-settings',
                        'dynamic_extension_my_account_dashboard',
                        [ 'label_for' => 'dashboard_before_content' ]
                    );

                    add_settings_field(
                        'dashboard_after_content',
                        __( 'Insert After Dashboard Content', 'hw-ele-woo-dynamic' ),
                        [ $this, 'render_textarea_field' ],
                        'dynamic-extension-settings',
                        'dynamic_extension_my_account_dashboard',
                        [ 'label_for' => 'dashboard_after_content' ]
                    );
                }

                if ( $section_id === 'my_account_orders' ) {
                    add_settings_field(
                        'orders_before_table',
                        __( 'Insert Before Orders Table', 'hw-ele-woo-dynamic' ),
                        [ $this, 'render_textarea_field' ],
                        'dynamic-extension-settings',
                        'dynamic_extension_my_account_orders',
                        [ 'label_for' => 'orders_before_table' ]
                    );

                    add_settings_field(
                        'orders_after_table',
                        __( 'Insert After Dashboard Table', 'hw-ele-woo-dynamic' ),
                        [ $this, 'render_textarea_field' ],
                        'dynamic-extension-settings',
                        'dynamic_extension_my_account_orders',
                        [ 'label_for' => 'orders_after_table' ]
                    );
                }
            }
        }
    }

    /**
     * Renders the checkbox fields for each tag within a section.
     *
     * @param array $args
     */
    public function woo_tags_field_html( $args ) {
        $options      = get_option( 'dynamic_extension_settings' );
        $enabled_tags = $options['enabled_tags'] ?? [];

        echo '<div class="dynamic-settings-checkbox-group">';

        foreach ( $args['tags'] as $tag_id => $label ) {
            $full_tag_id = $args['section'] . '_' . $tag_id;
            $is_checked  = isset( $enabled_tags[ $full_tag_id ] ) ? 'checked' : '';

            // Default: No badge
            $badge_html = '';

            // If this tag is "advanced-price", add "New" badge
            if ( $tag_id === 'advanced-price' ) {
                $badge_html = ' <sup class="badge-new-feature">New</sup>';
            }

            // If this tag is "spec_badge", add "Improvements" badge
            if ( $tag_id === 'spec_badge' ) {
                $badge_html = ' <sup class="badge-improvements">Improvements</sup>';
            }

            echo "<div class='checkbox-container'>";
            echo "<input type='checkbox' 
                    id='dynamic_extension_settings_enabled_tags_{$full_tag_id}' 
                    name='dynamic_extension_settings[enabled_tags][{$full_tag_id}]' 
                    value='1' {$is_checked} />";
            echo "<label for='dynamic_extension_settings_enabled_tags_{$full_tag_id}'>";
            echo $label . $badge_html;
            echo "</label>";
            echo "</div>";
        }

        echo '</div>';
    }


    /**
     * Renders a textarea field for user input, used in My Account Dashboard and Orders.
     *
     * @param array $args
     */
    public function render_textarea_field( $args ) {
        $options = get_option( 'dynamic_extension_settings' );
        $value   = $options[ $args['label_for'] ] ?? '';
        echo "<textarea 
                id='{$args['label_for']}' 
                name='dynamic_extension_settings[{$args['label_for']}]' 
                rows='10' 
                cols='50'>{$value}</textarea>";
    }

    /**
     * Validates and sanitizes the settings input.
     *
     * @param array $inputs
     * @return array
     */
    public function validate_settings( $inputs ) {
        $validated = [];

        // Validate enabled tags (checkboxes).
        if ( isset( $inputs['enabled_tags'] ) && is_array( $inputs['enabled_tags'] ) ) {
            foreach ( $inputs['enabled_tags'] as $key => $value ) {
                $validated['enabled_tags'][ $key ] = ( $value === '1' ) ? '1' : '0';
            }
        }

        // Validate textareas for dashboard/orders inserts (wp_kses_post).
        $text_fields = [ 'dashboard_before_content', 'dashboard_after_content', 'orders_before_table', 'orders_after_table' ];
        foreach ( $text_fields as $field ) {
            if ( isset( $inputs[ $field ] ) ) {
                $validated[ $field ] = wp_kses_post( $inputs[ $field ] );
            }
        }

        return $validated;
    }
}
