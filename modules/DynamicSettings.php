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
            __( 'Dynamic Elements', 'hw-elementor-woo-dynamic' ),
            __( 'Dynamic Elements', 'hw-elementor-woo-dynamic' ),
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
                    submit_button( __( 'Save Changes', 'hw-elementor-woo-dynamic' ) );
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
            'global_tags' => [
                'title'       => __( '[WordPress] Global dynamic tags', 'hw-elementor-woo-dynamic' ),
                'description' => __( 'Settings that apply globally across the site.', 'hw-elementor-woo-dynamic' ),
            ],
            'woo_extras' => [
                'title'       => __( '[WooCommerce] Single Product page / loop dynamic tags', 'hw-elementor-woo-dynamic' ),
                'description' => __(
                    'This section contains settings for single product pages and product loops.<br> Check the <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/Woo-%E2%80%90-Product-Dynamic-Tags">wikipedia section</a> for tutorial',
                    'hw-elementor-woo-dynamic'
                ),
            ],
            'global' => [
                'title'       => __( '[WooCommerce] Global dynamic tags', 'hw-elementor-woo-dynamic' ),
                'description' => __( 'Settings that apply globally across the site.', 'hw-elementor-woo-dynamic' ),
            ],
            'cart_value' => [
                'title'       => __( '[WooCommerce] Cart dynamic tags', 'hw-elementor-woo-dynamic' ),
                'description' => __( 'Settings that apply globally across the site.', 'hw-elementor-woo-dynamic' ),
            ],
            'customer_specific' => [
                'title'       => __( '[WooCommerce] Customer Specific dynamic tags', 'hw-elementor-woo-dynamic' ),
                'description' => __(
                    'Specific tags that return current logged-in user data. Check the <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/Woo-%E2%80%90-Customer-Dynamic-Tags">wikipedia section</a> for tutorial',
                    'hw-elementor-woo-dynamic'
                ),
            ],
            'woo_membership' => [
                'title'       => __( '[WooCommerce Membership] dynamic tags', 'hw-elementor-woo-dynamic' ),
                'description' => __(
                    'Specific tags for WooCommerce Membership. Check the <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/Woo-Membership-%E2%80%90-Dynamic-Tags">wikipedia section</a> for tutorial',
                    'hw-elementor-woo-dynamic'
                ),
            ],
            'woo_subscriptions' => [
                'title'       => __( '[WooCommerce Subscriptions] dynamic tags', 'hw-elementor-woo-dynamic' ),
                'description' => __(
                    'Specific tags for WooCommerce Subscriptions. Check the <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/Woo-Subscriptions-%E2%80%90-Dynamic-Tags">wikipedia section</a> for tutorial',
                    'hw-elementor-woo-dynamic'
                ),
            ],
            'ld_extras_courses' => [
                'title'       => __( '[Learndash] Course dynamic tags', 'hw-elementor-woo-dynamic' ),
                'description' => __(
                    'Specific tags for the Learndash courses single page. Check the <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/LearnDash-%E2%80%90-Dynamic-Tags"> wikipedia section</a> for tutorial',
                    'hw-elementor-woo-dynamic'
                ),
            ],
            'ld_extras_global' => [
                'title'       => __( '[Learndash] Global dynamic tags', 'hw-elementor-woo-dynamic' ),
                'description' => __(
                    'Specific Global tags for Learndash. Check the <a href="https://github.com/Lonsdale201/dynamic-elementor-extension/wiki/LearnDash-%E2%80%90-Dynamic-Tags"> wikipedia section</a> for tutorial',
                    'hw-elementor-woo-dynamic'
                ),
            ],
            'wp_bar_products_informations' => [
                'title'       => __( 'Enable extra products informations in the admin toolbar', 'hw-elementor-woo-dynamic' ),
                'description' => __(
                    'In the WP admin top bar you can configure which product information is displayed when viewing the product page on the frontend',
                    'hw-elementor-woo-dynamic'
                ),
            ],
            'wp_bar_learndash_informations' => [
                'title'       => __( 'Enable extra learndash informations in the admin toolbar', 'hw-elementor-woo-dynamic' ),
                'description' => __(
                    'In the WP admin top bar you can configure which LearnDash information is displayed when viewing the course page on the frontend',
                    'hw-elementor-woo-dynamic'
                ),
            ],
            'my_account_dashboard' => [
                'title'       => __( 'My Account Dashboard', 'hw-elementor-woo-dynamic' ),
                'description' => __(
                    'Here you can insert shortcode content in your WooCommerce dashboard endpoint. You can use shortcode, plain text, or HTML.',
                    'hw-elementor-woo-dynamic'
                ),
            ],
            'my_account_orders' => [
                'title'       => __( 'My Account Orders', 'hw-elementor-woo-dynamic' ),
                'description' => __(
                    'Here you can insert shortcode content in your WooCommerce orders endpoint. You can use shortcode, plain text, or HTML.',
                    'hw-elementor-woo-dynamic'
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
            [ $this, 'validate_settings' ] // phpcs:ignore PluginCheck.CodeAnalysis.SettingSanitization.register_settingDynamic
        );
        

        foreach ( $this->sections as $id => $data ) {

            // WooCommerce-based sections
            if ( 'global_tags' !== $id && (
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
                ) ) {
                    continue;
                }

            // Add the settings section
            add_settings_section(
                'dynamic_extension_' . $id,
                $data['title'],
                function() use ( $data ) {
                    echo '<p>' . wp_kses_post( $data['description'] ) . '</p>';
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
            'global_tags' => [
                'global_dynamic_calculation'    => __( 'Dynamic Calculation', 'hw-elementor-woo-dynamic' ),
            ],
            'global' => [
                'advanced_product_category'     => __( 'Advanced Product Category', 'hw-elementor-woo-dynamic' ),
                'free_shipping_amount'          => __( 'Free Shipping Amount', 'hw-elementor-woo-dynamic' ),
            ],
            'woo_extras' => [
                'advanced-price'               => __( 'Advanced Price', 'hw-elementor-woo-dynamic' ),
                'advanced_sale_badge'          => __( 'Advanced Sale Badge', 'hw-elementor-woo-dynamic' ),
                'advanced_stock'               => __( 'Advanced Stock', 'hw-elementor-woo-dynamic' ),
                'featured_badge'               => __( 'Featured Badge', 'hw-elementor-woo-dynamic' ),
                'next_product'                 => __( 'Next Product', 'hw-elementor-woo-dynamic' ),
                'next_product_image'           => __( 'Next Product Image', 'hw-elementor-woo-dynamic' ),
                'prev_product'                 => __( 'Previous Product', 'hw-elementor-woo-dynamic' ),
                'prev_product_image'           => __( 'Previous Product Image', 'hw-elementor-woo-dynamic' ),
                'product-tabs'                 => __( 'Product Tabs', 'hw-elementor-woo-dynamic' ),
                'product_attributes'           => __( 'Product Attributes', 'hw-elementor-woo-dynamic' ),
                'product_description'          => __( 'Product Description', 'hw-elementor-woo-dynamic' ),
                'product_dimension'            => __( 'Product Dimension', 'hw-elementor-woo-dynamic' ),
                'product_gallery'              => __( 'Product Gallery', 'hw-elementor-woo-dynamic' ),
                'product_height'               => __( 'Product Height', 'hw-elementor-woo-dynamic' ),
                'product_lenght'               => __( 'Product Length', 'hw-elementor-woo-dynamic' ),
                'product_shipping_class'       => __( 'Product Shipping Class', 'hw-elementor-woo-dynamic' ),
                'product_weight'               => __( 'Product Weight', 'hw-elementor-woo-dynamic' ),
                'product_width'                => __( 'Product Width', 'hw-elementor-woo-dynamic' ),
                'purchased_badge'              => __( 'Purchased Badge', 'hw-elementor-woo-dynamic' ),
                'sale_time'                    => __( 'Sale Time', 'hw-elementor-woo-dynamic' ),
                'spec_badge'                   => __( 'Spec Badge', 'hw-elementor-woo-dynamic' ),
                'stock_quantity'               => __( 'Stock Quantity', 'hw-elementor-woo-dynamic' ),
                'stock_quantity_extra'         => __( 'Stock Quantity Extra', 'hw-elementor-woo-dynamic' ),
                'taxonomy-acf-meta'            => __( 'ACF Taxonomy Meta', 'hw-elementor-woo-dynamic' ),
                'variable-price-range'         => __( 'Variable Price', 'hw-elementor-woo-dynamic' ),
            ],

            'cart_value' => [
                'cart_values'     => __( 'Cart Values', 'hw-elementor-woo-dynamic' ),
                'cart_tax_values' => __( 'Cart Tax Values', 'hw-elementor-woo-dynamic' ),
            ],
            'customer_specific' => [
                'customer_details'     => __( 'Customer Details', 'hw-elementor-woo-dynamic' ),
                'completed_order'      => __( 'Completed Order', 'hw-elementor-woo-dynamic' ),
                'purchased_products'   => __( 'Purchased Products', 'hw-elementor-woo-dynamic' ),
                'total_spent'          => __( 'Total Spent', 'hw-elementor-woo-dynamic' ),
                'my_account_endpoints' => __( 'MyAccount Endpoints', 'hw-elementor-woo-dynamic' ),
                'last_order'           => __( 'Last Order', 'hw-elementor-woo-dynamic' ),
                'user_role'            => __( 'User Role', 'hw-elementor-woo-dynamic' ),
                'customer_logout'      => __( 'Customer logout', 'hw-elementor-woo-dynamic' ),
            ],
            'woo_membership' => [
                'active_membership'         => __( 'Active Membership', 'hw-elementor-woo-dynamic' ),
                'user_membership_plan_count'         => __( 'Membership Plan Count', 'hw-elementor-woo-dynamic' ),
                'my_account_membershipLink' => __( 'MyAccount Membership Link', 'hw-elementor-woo-dynamic' ),
                'active_membership_data'    => __( 'Active Membership Data', 'hw-elementor-woo-dynamic' ),
                'current_membership_data'   => __( 'Current Membership Data', 'hw-elementor-woo-dynamic' ),
                'restricted_products_view'  => __( 'Restricted Products View', 'hw-elementor-woo-dynamic' ),
            ],
            'woo_subscriptions' => [
                'active_subscription'        => __( 'Active Subscription', 'hw-elementor-woo-dynamic' ),
                'my_account_subscriptionlink'=> __( 'MyAccount Subscription Link', 'hw-elementor-woo-dynamic' ),
                'active_subscription_data'   => __( 'Active Subscription Data', 'hw-elementor-woo-dynamic' ),
            ],
            'ld_extras_courses' => [
                'awarded_points'              => __( 'Awarded on Completion', 'hw-elementor-woo-dynamic' ),
                'ld_lessons'                  => __( 'Lessons Number', 'hw-elementor-woo-dynamic' ),
                'ld_quiz'                     => __( 'Quiz Numbers', 'hw-elementor-woo-dynamic' ),
                'enrolled_users_count'        => __( 'Students number', 'hw-elementor-woo-dynamic' ),
                'last-activity'               => __( 'Last Activity', 'hw-elementor-woo-dynamic' ),
                'progress-percentage'         => __( 'Course Progress', 'hw-elementor-woo-dynamic' ),
                'user-course-status'          => __( 'Course Status', 'hw-elementor-woo-dynamic' ),
                'access-expires'              => __( 'Access Expires', 'hw-elementor-woo-dynamic' ),
                'course-materials'            => __( 'Course Materials', 'hw-elementor-woo-dynamic' ),
                'course-access-type'          => __( 'Course Access Type', 'hw-elementor-woo-dynamic' ),
                'student-limit'               => __( 'Student Limit', 'hw-elementor-woo-dynamic' ),
                'course-start-date'           => __( 'Course Start Date', 'hw-elementor-woo-dynamic' ),
                'required-points'            => __( 'Course Required Points For Access', 'hw-elementor-woo-dynamic' ),
                'course-price'                => __( 'Course Price', 'hw-elementor-woo-dynamic' ),
                'topics-counter'              => __( 'Topics Numbers', 'hw-elementor-woo-dynamic' ),
                'certificates-link'           => __( 'Certificates Link', 'hw-elementor-woo-dynamic' ),
                'course-prerequisites-list'   => __( 'Course Prerequisites List', 'hw-elementor-woo-dynamic' ),
                'course-resume-text'          => __( 'Course Resume Text', 'hw-elementor-woo-dynamic' ),
                'course-resume'               => __( 'Course Resume URL', 'hw-elementor-woo-dynamic' ),
                'course-content'              => __( 'Course Content', 'hw-elementor-woo-dynamic' ),
                'course-groups'               => __( 'Course Part of Groups', 'hw-elementor-woo-dynamic' ),
            ],
            'ld_extras_global' => [
                'user-completed-courses-count'     => __( 'User Completed Courses Count', 'hw-elementor-woo-dynamic' ),
                'user-course-points'               => __( 'User Course Points', 'hw-elementor-woo-dynamic' ),
                'user-available-courses-count'      => __( 'User Enrolled Courses Count', 'hw-elementor-woo-dynamic' ),
                'user-achieved-certificates-count'  => __( 'User Achieved Certificates Count', 'hw-elementor-woo-dynamic' ),
                'user-groups-count'                 => __( 'User Groups Count', 'hw-elementor-woo-dynamic' ),
            ],
            'wp_bar_products_informations' => [
                'product_type'     => __( 'Product Type', 'hw-elementor-woo-dynamic' ),
                'product_sku'      => __( 'Product Sku', 'hw-elementor-woo-dynamic' ),
                'product_inventory'=> __( 'Product Inventory', 'hw-elementor-woo-dynamic' ),
                'shipping_class'   => __( 'Shipping Class', 'hw-elementor-woo-dynamic' ),
                'product_status'   => __( 'Product Status', 'hw-elementor-woo-dynamic' ),
            ],
            'wp_bar_learndash_informations' => [
                'ld_bar_lessons' => __( 'Lessons', 'hw-elementor-woo-dynamic' ),
                'ld_bar_topics'  => __( 'Topics', 'hw-elementor-woo-dynamic' ),
                'ld_bar_quizes'  => __( 'Quiz', 'hw-elementor-woo-dynamic' ),
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
            $label = __( 'Enable Dynamic Tags', 'hw-elementor-woo-dynamic' );

            if ( $section_id === 'wp_bar_products_informations' ) {
                $label = __( 'Enable Product Information Display', 'hw-elementor-woo-dynamic' );
            } elseif ( $section_id === 'wp_bar_learndash_informations' ) {
                $label = __( 'Enable Learndash Info Display', 'hw-elementor-woo-dynamic' );
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
                        __( 'Insert Before Dashboard Content', 'hw-elementor-woo-dynamic' ),
                        [ $this, 'render_textarea_field' ],
                        'dynamic-extension-settings',
                        'dynamic_extension_my_account_dashboard',
                        [ 'label_for' => 'dashboard_before_content' ]
                    );

                    add_settings_field(
                        'dashboard_after_content',
                        __( 'Insert After Dashboard Content', 'hw-elementor-woo-dynamic' ),
                        [ $this, 'render_textarea_field' ],
                        'dynamic-extension-settings',
                        'dynamic_extension_my_account_dashboard',
                        [ 'label_for' => 'dashboard_after_content' ]
                    );
                }

                if ( $section_id === 'my_account_orders' ) {
                    add_settings_field(
                        'orders_before_table',
                        __( 'Insert Before Orders Table', 'hw-elementor-woo-dynamic' ),
                        [ $this, 'render_textarea_field' ],
                        'dynamic-extension-settings',
                        'dynamic_extension_my_account_orders',
                        [ 'label_for' => 'orders_before_table' ]
                    );

                    add_settings_field(
                        'orders_after_table',
                        __( 'Insert After Dashboard Table', 'hw-elementor-woo-dynamic' ),
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
            $is_checked  = isset( $enabled_tags[ $full_tag_id ] ) ? '1' : '';

            $badge_html = '';
            if ( $tag_id === 'user_membership_plan_count' ) {
                $badge_html = ' <sup class="badge-new-feature">New</sup>';
            }
            if ( $tag_id === 'global_dynamic_calculation' ) {
                $badge_html = ' <sup class="badge-new-feature">New</sup>';
            }
            if ( $tag_id === 'product_dimension' ) {
                $badge_html = ' <sup class="badge-new-feature">New</sup>';
            }
            if ( $tag_id === 'active_membership' ) {
                $badge_html = ' <sup class="badge-improvements">Improvements</sup>';
            }
            if ( $tag_id === 'product_shipping_class' ) {
                $badge_html = ' <sup class="badge-improvements">Improvements</sup>';
            }
            if ( $tag_id === 'product_height' ) {
                $badge_html = ' <sup class="badge-improvements">Improvements</sup>';
            }
            if ( $tag_id === 'product_width' ) {
                $badge_html = ' <sup class="badge-improvements">Improvements</sup>';
            }
            if ( $tag_id === 'product_lenght' ) {
                $badge_html = ' <sup class="badge-improvements">Improvements</sup>';
            }
            if ( $tag_id === 'product_attributes' ) {
                $badge_html = ' <sup class="badge-improvements">Improvements</sup>';
            }

            if ( $tag_id === 'course-resume' ) {
                $badge_html = ' <sup class="badge-improvements">Improvements</sup>';
            }

            echo "<div class='checkbox-container'>";
            echo '<input type="checkbox" id="' . esc_attr( 'dynamic_extension_settings_enabled_tags_' . $full_tag_id ) . '" name="' . esc_attr( "dynamic_extension_settings[enabled_tags][{$full_tag_id}]" ) . '" value="1" ' . checked( $is_checked, '1', false ) . ' />';
            echo '<label for="' . esc_attr( 'dynamic_extension_settings_enabled_tags_' . $full_tag_id ) . '">' . esc_html( $label ) . wp_kses_post( $badge_html ) . '</label>';
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

        echo '<textarea id="' . esc_attr( $args['label_for'] ) . '" name="dynamic_extension_settings[' . esc_attr( $args['label_for'] ) . ']" rows="10" cols="50">' . esc_textarea( $value ) . '</textarea>';
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
