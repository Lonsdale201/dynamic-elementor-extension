<?php
namespace HelloWP\HWEleWooDynamic\Modules\Widgets;

use Elementor\Controls_Manager;
use Elementor\Element_Base;

class JetEngineListingGridSkeletonSettings {

    private const STYLE_HANDLE = 'hw-jet-listing-skeleton';
    private const SCRIPT_HANDLE = 'hw-jet-listing-skeleton-js';

    private static ?self $instance = null;

    private bool $assets_registered = false;

    public static function instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        if ( did_action( 'elementor/init' ) ) {
            $this->register_hooks();
            return;
        }

        add_action( 'elementor/init', [ $this, 'register_hooks' ] );
    }

    public function register_hooks(): void {
        if ( ! function_exists( 'jet_engine' ) ) {
            return;
        }

        // Add a dedicated "Skeleton" section under Content after General.
        add_action(
            'elementor/element/jet-listing-grid/section_general/after_section_end',
            [ $this, 'add_skeleton_controls_section' ],
            10,
            2
        );

        add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
        add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'register_assets' ] );

        add_filter(
            'jet-engine/listing/container-classes',
            [ $this, 'maybe_add_container_class' ],
            10,
            3
        );

        add_filter(
            'jet-engine/listing/container-atts',
            [ $this, 'maybe_add_container_attribute' ],
            10,
            3
        );

        add_filter(
            'jet-engine/listing/render/default-settings',
            [ $this, 'inject_default_setting' ]
        );

        add_filter(
            'jet-smart-filters/providers/jet-engine/stored-settings',
            [ $this, 'inject_provider_setting' ],
            10,
            2
        );
    }

    public function add_skeleton_controls_section( Element_Base $element, array $args ): void {
        if ( ! method_exists( $element, 'start_controls_section' ) ) {
            return;
        }

        // Avoid duplicate registration
        $controls = $element->get_controls();
        if ( isset( $controls['hw_skeleton_loading'] ) || isset( $controls['hw_skeleton_corners'] ) ) {
            return;
        }

        // Start our custom section under Content tab
        $element->start_controls_section(
            'hw_skeleton_section',
            [
                'label' => __( 'Skeleton', 'hw-ele-woo-dynamic' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Main switcher
        $element->add_control(
            'hw_skeleton_loading',
            [
                'label'              => __( 'Skeleton Loading', 'hw-ele-woo-dynamic' ),
                'type'               => Controls_Manager::SWITCHER,
                'label_on'           => __( 'On', 'hw-ele-woo-dynamic' ),
                'label_off'          => __( 'Off', 'hw-ele-woo-dynamic' ),
                'return_value'       => 'yes',
                'default'            => '',
                'frontend_available' => true,
            ]
        );

        // Corners select (visible only when switcher is on)
        $element->add_control(
            'hw_skeleton_corners',
            [
                'label'              => __( 'Corners', 'hw-ele-woo-dynamic' ),
                'type'               => Controls_Manager::SELECT,
                'options'            => [
                    'straight' => __( 'Straight', 'hw-ele-woo-dynamic' ),
                    'rounded'  => __( 'Rounded', 'hw-ele-woo-dynamic' ),
                ],
                'default'            => 'rounded',
                'condition'          => [ 'hw_skeleton_loading' => 'yes' ],
                'frontend_available' => true,
            ]
        );

        // Helpful notices for editors while skeleton loading is enabled
        $element->add_control(
            'hw_skeleton_alert_usage',
            [
                'type'      => Controls_Manager::ALERT,
                'alert_type'=> 'info',
                'heading'   => __( 'How to trigger the skeleton', 'hw-ele-woo-dynamic' ),
                'content'   => __( 'Use the <code>skeleton-loading</code> class on any widget that should display the skeleton while the listing is loading.', 'hw-ele-woo-dynamic' ),
                'condition' => [ 'hw_skeleton_loading' => 'yes' ],
            ]
        );

        $element->add_control(
            'hw_skeleton_alert_hide',
            [
                'type'      => Controls_Manager::ALERT,
                'alert_type'=> 'info',
                'heading'   => __( 'Hide elements during loading', 'hw-ele-woo-dynamic' ),
                'content'   => __( 'Apply the <code>skeleton-loading-justhide</code> class if an element should stay hidden until the data arrives (no skeleton, no placeholder).', 'hw-ele-woo-dynamic' ),
                'condition' => [ 'hw_skeleton_loading' => 'yes' ],
            ]
        );

        $element->add_control(
            'hw_skeleton_alert_multitext',
            [
                'type'      => Controls_Manager::ALERT,
                'alert_type'=> 'info',
                'heading'   => __( 'Multiline text support', 'hw-ele-woo-dynamic' ),
                'content'   => __( 'Use <code>skeleton-multitext-loading</code> for multiline text polishing. Supported widgets: Elementor Text Editor, Headline, and JetEngine Dynamic Field.', 'hw-ele-woo-dynamic' ),
                'condition' => [ 'hw_skeleton_loading' => 'yes' ],
            ]
        );

        $element->add_control(
            'hw_skeleton_alert_iconlist',
            [
                'type'      => Controls_Manager::ALERT,
                'alert_type'=> 'info',
                'heading'   => __( 'Icon List integration', 'hw-ele-woo-dynamic' ),
                'content'   => __( 'Add <code>skeleton-list-loading</code> to Elementor Icon List widgets to animate each <code>&lt;li&gt;</code> item separately.', 'hw-ele-woo-dynamic' ),
                'condition' => [ 'hw_skeleton_loading' => 'yes' ],
            ]
        );

        $element->add_control(
            'hw_skeleton_alert_position',
            [
                'type'      => Controls_Manager::ALERT,
                'alert_type'=> 'warning',
                'heading'   => __( 'Absolute positioning tip', 'hw-ele-woo-dynamic' ),
                'content'   => __( 'If an absolute-positioned element jumps during loading, force its original positioning with <code>--hw-skeleton-original-position: absolute;</code> on the element.', 'hw-ele-woo-dynamic' ),
                'condition' => [ 'hw_skeleton_loading' => 'yes' ],
            ]
        );

        $element->end_controls_section();
    }

    public function register_assets(): void {
        if ( $this->assets_registered ) {
            return;
        }

        wp_register_style(
            self::STYLE_HANDLE,
            HW_ELE_DYNAMIC_URL . 'modules/widgets/assets/jet-listing-skeleton.css',
            [],
            HW_ELE_DYNAMIC_VERSION
        );

        wp_register_script(
            self::SCRIPT_HANDLE,
            HW_ELE_DYNAMIC_URL . 'modules/widgets/assets/jet-skeleton.js',
            [ 'jquery', 'jet-engine-frontend' ],
            HW_ELE_DYNAMIC_VERSION,
            true
        );

        $this->assets_registered = true;
    }

    /**
     * @param array $classes
     * @param array $settings
     * @param object $render
     */
    public function maybe_add_container_class( array $classes, array $settings, $render ): array {
        if ( ! $this->should_activate_skeleton( $settings, $render ) ) {
            return $classes;
        }

        $this->enqueue_assets();

        $classes[] = 'hw-jet-listing-skeleton';

        // Apply corners style class if set
        if ( ! empty( $settings['hw_skeleton_corners'] ) ) {
            if ( 'straight' === $settings['hw_skeleton_corners'] ) {
                $classes[] = 'hw-skeleton-straight';
            } elseif ( 'rounded' === $settings['hw_skeleton_corners'] ) {
                $classes[] = 'hw-skeleton-rounded';
            }
        }

        return array_values( array_unique( $classes ) );
    }

    /**
     * @param array $attributes
     * @param array $settings
     * @param object $render
     */
    public function maybe_add_container_attribute( array $attributes, array $settings, $render ): array {
        if ( ! $this->should_activate_skeleton( $settings, $render ) ) {
            return $attributes;
        }

        $this->enqueue_assets();

        $attributes[] = 'data-hw-skeleton="true"';

        if ( ! empty( $settings['_element_id'] ) ) {
            $attributes[] = 'data-hw-query-id="' . esc_attr( $settings['_element_id'] ) . '"';
        }

        return $attributes;
    }

    private function enqueue_assets(): void {
        $this->register_assets();

        wp_enqueue_style( self::STYLE_HANDLE );
        wp_enqueue_script( self::SCRIPT_HANDLE );
    }

    /**
     * @param array $settings
     * @param object $render
     */
    private function should_activate_skeleton( array $settings, $render ): bool {
        if ( empty( $settings['hw_skeleton_loading'] ) || 'yes' !== $settings['hw_skeleton_loading'] ) {
            return false;
        }

        if ( ! is_object( $render ) ) {
            return false;
        }

        if ( class_exists( '\Jet_Engine_Render_Listing_Grid', false ) && ! $render instanceof \Jet_Engine_Render_Listing_Grid ) {
            return false;
        }

        if ( method_exists( $render, 'get_name' ) && 'jet-listing-grid' !== $render->get_name() ) {
            return false;
        }

        return true;
    }

    public function inject_default_setting( array $settings ): array {
        if ( ! array_key_exists( 'hw_skeleton_loading', $settings ) ) {
            $settings['hw_skeleton_loading'] = '';
        }

        if ( ! array_key_exists( 'hw_skeleton_corners', $settings ) ) {
            $settings['hw_skeleton_corners'] = 'rounded';
        }

        return $settings;
    }

    public function inject_provider_setting( array $settings, array $widget_settings ): array {
        if ( array_key_exists( 'hw_skeleton_loading', $settings ) ) {
            return $settings;
        }

        $settings['hw_skeleton_loading'] = isset( $widget_settings['hw_skeleton_loading'] )
            ? $widget_settings['hw_skeleton_loading']
            : '';

        if ( ! array_key_exists( 'hw_skeleton_corners', $settings ) ) {
            $settings['hw_skeleton_corners'] = isset( $widget_settings['hw_skeleton_corners'] )
                ? $widget_settings['hw_skeleton_corners']
                : 'rounded';
        }

        return $settings;
    }
}
