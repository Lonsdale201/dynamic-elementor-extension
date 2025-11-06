<?php
namespace HelloWP\HWEleWooDynamic\Modules\Widgets;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use function sanitize_hex_color;

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

        $element->add_control(
            'hw_skeleton_auto_apply',
            [
                'label'              => __( 'Auto Apply Skeleton', 'hw-ele-woo-dynamic' ),
                'type'               => Controls_Manager::SWITCHER,
                'label_on'           => __( 'On', 'hw-ele-woo-dynamic' ),
                'label_off'          => __( 'Off', 'hw-ele-woo-dynamic' ),
                'return_value'       => 'yes',
                'default'            => '',
                'condition'          => [ 'hw_skeleton_loading' => 'yes' ],
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

        // Radius slider (only when skeleton is on and corners are rounded)
        $element->add_control(
            'hw_skeleton_radius',
            [
                'label'              => __( 'Border Radius', 'hw-ele-woo-dynamic' ),
                'type'               => Controls_Manager::SLIDER,
                'size_units'         => [ 'px' ],
                'range'              => [
                    'px' => [ 'min' => 0, 'max' => 50, 'step' => 1 ],
                ],
                'default'            => [ 'size' => 10, 'unit' => 'px' ],
                'condition'          => [
                    'hw_skeleton_loading' => 'yes',
                    'hw_skeleton_corners' => 'rounded',
                ],
                'frontend_available' => true,
            ]
        );

        // Style select (animation variants)
        $element->add_control(
            'hw_skeleton_style',
            [
                'label'              => __( 'Animation Style', 'hw-ele-woo-dynamic' ),
                'type'               => Controls_Manager::SELECT,
                'options'            => [
                    'shimmer' => __( 'Shimmer', 'hw-ele-woo-dynamic' ),
                    'fade'    => __( 'Fade', 'hw-ele-woo-dynamic' ),
                ],
                'default'            => 'shimmer',
                'condition'          => [ 'hw_skeleton_loading' => 'yes' ],
                'frontend_available' => true,
            ]
        );

        // Base color
        $element->add_control(
            'hw_skeleton_base_color',
            [
                'label'              => __( 'Base Color', 'hw-ele-woo-dynamic' ),
                'type'               => Controls_Manager::COLOR,
                'default'            => '#e0e0e0',
                'condition'          => [ 'hw_skeleton_loading' => 'yes' ],
                'frontend_available' => true,
            ]
        );

        // Highlight color
        $element->add_control(
            'hw_skeleton_highlight_color',
            [
                'label'              => __( 'Highlight Color', 'hw-ele-woo-dynamic' ),
                'type'               => Controls_Manager::COLOR,
                'default'            => '#f5f5f5',
                'condition'          => [ 'hw_skeleton_loading' => 'yes' ],
                'frontend_available' => true,
            ]
        );

        // Animation speed
        $element->add_control(
            'hw_skeleton_speed',
            [
                'label'              => __( 'Animation Speed', 'hw-ele-woo-dynamic' ),
                'type'               => Controls_Manager::SLIDER,
                'size_units'         => [ 's' ],
                'range'              => [
                    's' => [ 'min' => 0.5, 'max' => 5, 'step' => 0.1 ],
                ],
                'default'            => [ 'size' => 1.35, 'unit' => 's' ],
                'condition'          => [ 'hw_skeleton_loading' => 'yes' ],
                'frontend_available' => true,
            ]
        );

        // Shimmer direction (only for shimmer style)
        $element->add_control(
            'hw_skeleton_direction',
            [
                'label'              => __( 'Shimmer Direction', 'hw-ele-woo-dynamic' ),
                'type'               => Controls_Manager::SELECT,
                'options'            => [
                    'ltr' => __( 'Left to Right', 'hw-ele-woo-dynamic' ),
                    'rtl' => __( 'Right to Left', 'hw-ele-woo-dynamic' ),
                ],
                'default'            => 'ltr',
                'condition'          => [
                    'hw_skeleton_loading' => 'yes',
                    'hw_skeleton_style'   => 'shimmer',
                ],
                'frontend_available' => true,
            ]
        );

        // Helpful notices for editors while skeleton loading is enabled
        $element->add_control(
            'hw_skeleton_alert_usage',
            [
                'type'      => Controls_Manager::ALERT,
                'alert_type'=> 'success',
                'heading'   => __( 'Auto Apply function', 'hw-ele-woo-dynamic' ),
                'content'   => sprintf(
                    __( 'This automation currently supports a curated list of widgets. Review the full list and setup instructions here: <a href="%s" target="_blank" rel="noopener">Skeleton Loading Documentation</a>.', 'hw-ele-woo-dynamic' ),
                    esc_url( 'https://lonsdale201.github.io/lonsdale-plugins.github.io/dynamic-ele-ext/jetengine-listing-skeleton-loading' )
                ),
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
                'heading'   => __( 'Special skeleton split by widgets', 'hw-ele-woo-dynamic' ),
                'content'   => sprintf(
                    __( 'Certain widgets can display tailored skeleton shapes (image/title/lines). Learn how to apply them in the documentation: <a href="%s" target="_blank" rel="noopener">Skeleton Loading Documentation</a>.', 'hw-ele-woo-dynamic' ),
                    esc_url( 'https://lonsdale201.github.io/lonsdale-plugins.github.io/dynamic-ele-ext/jetengine-listing-skeleton-loading' )
                ),
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

        if ( ! empty( $settings['hw_skeleton_style'] ) ) {
            $style = strtolower( (string) $settings['hw_skeleton_style'] );
            if ( in_array( $style, [ 'shimmer', 'fade' ], true ) ) {
                $classes[] = 'hw-skeleton-style-' . $style;
            }
        }

        if ( ! empty( $settings['hw_skeleton_direction'] ) ) {
            $dir = strtolower( (string) $settings['hw_skeleton_direction'] );
            if ( in_array( $dir, [ 'ltr', 'rtl' ], true ) ) {
                $classes[] = 'hw-skeleton-dir-' . $dir;
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

        // Pass custom radius as data attribute; JS will apply CSS var to avoid style collisions
        $style_fragments = [];
        $radius_px      = null;
        if ( ! empty( $settings['hw_skeleton_corners'] ) && 'rounded' === $settings['hw_skeleton_corners'] ) {
            if ( isset( $settings['hw_skeleton_radius']['size'] ) && is_numeric( $settings['hw_skeleton_radius']['size'] ) ) {
                $radius_px = max( 0, (int) $settings['hw_skeleton_radius']['size'] );
            }
        } else {
            // Straight corners explicitly zero
            $radius_px = 0;
        }

        if ( null !== $radius_px ) {
            $attributes[] = 'data-hw-skeleton-radius="' . esc_attr( $radius_px ) . '"';
            $style_fragments[] = '--hw-skeleton-radius: ' . esc_attr( $radius_px ) . 'px;';
        }

        $speed_seconds = null;
        if ( isset( $settings['hw_skeleton_speed']['size'] ) && is_numeric( $settings['hw_skeleton_speed']['size'] ) ) {
            $speed_val = max( 0.1, (float) $settings['hw_skeleton_speed']['size'] );
            $speed_seconds = $speed_val;
        }

        if ( null !== $speed_seconds ) {
            $style_fragments[] = '--hw-skeleton-animation-duration: ' . esc_attr( $speed_seconds ) . 's;';
        }

        if ( ! empty( $settings['hw_skeleton_base_color'] ) ) {
            $base_color = sanitize_hex_color( $settings['hw_skeleton_base_color'] );
            if ( $base_color ) {
                $style_fragments[] = '--hw-skeleton-base-color: ' . $base_color . ';';
            }
        }

        if ( ! empty( $settings['hw_skeleton_highlight_color'] ) ) {
            $highlight_color = sanitize_hex_color( $settings['hw_skeleton_highlight_color'] );
            if ( $highlight_color ) {
                $style_fragments[] = '--hw-skeleton-highlight-color: ' . $highlight_color . ';';
            }
        }

        if ( ! empty( $style_fragments ) ) {
            // Append or create style attribute with collected fragments.
            $style_attr_index = null;
            foreach ( $attributes as $i => $attr ) {
                if ( 0 === strpos( $attr, 'style=' ) ) {
                    $style_attr_index = $i;
                    break;
                }
            }

            $style_snippet = implode( ' ', $style_fragments );

            if ( null === $style_attr_index ) {
                $attributes[] = 'style="' . $style_snippet . '"';
            } else {
                $attributes[ $style_attr_index ] = rtrim( substr( $attributes[ $style_attr_index ], 0, -1 ), '"' ) . ' ' . $style_snippet . '"';
            }
        }

        if ( ! empty( $settings['hw_skeleton_auto_apply'] ) && 'yes' === $settings['hw_skeleton_auto_apply'] ) {
            $attributes[] = 'data-hw-skeleton-auto="yes"';
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

        if ( ! array_key_exists( 'hw_skeleton_radius', $settings ) ) {
            $settings['hw_skeleton_radius'] = [ 'size' => 10, 'unit' => 'px' ];
        }

        if ( ! array_key_exists( 'hw_skeleton_style', $settings ) ) {
            $settings['hw_skeleton_style'] = 'shimmer';
        }

        if ( ! array_key_exists( 'hw_skeleton_speed', $settings ) ) {
            $settings['hw_skeleton_speed'] = [ 'size' => 1.35, 'unit' => 's' ];
        }

        if ( ! array_key_exists( 'hw_skeleton_direction', $settings ) ) {
            $settings['hw_skeleton_direction'] = 'ltr';
        }

        if ( ! array_key_exists( 'hw_skeleton_base_color', $settings ) ) {
            $settings['hw_skeleton_base_color'] = '#e0e0e0';
        }

        if ( ! array_key_exists( 'hw_skeleton_highlight_color', $settings ) ) {
            $settings['hw_skeleton_highlight_color'] = '#f5f5f5';
        }

        if ( ! array_key_exists( 'hw_skeleton_auto_apply', $settings ) ) {
            $settings['hw_skeleton_auto_apply'] = '';
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

        if ( ! array_key_exists( 'hw_skeleton_radius', $settings ) ) {
            $settings['hw_skeleton_radius'] = isset( $widget_settings['hw_skeleton_radius'] )
                ? $widget_settings['hw_skeleton_radius']
                : [ 'size' => 10, 'unit' => 'px' ];
        }

        if ( ! array_key_exists( 'hw_skeleton_style', $settings ) ) {
            $settings['hw_skeleton_style'] = isset( $widget_settings['hw_skeleton_style'] )
                ? $widget_settings['hw_skeleton_style']
                : 'shimmer';
        }

        if ( ! array_key_exists( 'hw_skeleton_speed', $settings ) ) {
            $settings['hw_skeleton_speed'] = isset( $widget_settings['hw_skeleton_speed'] )
                ? $widget_settings['hw_skeleton_speed']
                : [ 'size' => 1.35, 'unit' => 's' ];
        }

        if ( ! array_key_exists( 'hw_skeleton_direction', $settings ) ) {
            $settings['hw_skeleton_direction'] = isset( $widget_settings['hw_skeleton_direction'] )
                ? $widget_settings['hw_skeleton_direction']
                : 'ltr';
        }

        if ( ! array_key_exists( 'hw_skeleton_base_color', $settings ) ) {
            $settings['hw_skeleton_base_color'] = isset( $widget_settings['hw_skeleton_base_color'] )
                ? $widget_settings['hw_skeleton_base_color']
                : '#e0e0e0';
        }

        if ( ! array_key_exists( 'hw_skeleton_highlight_color', $settings ) ) {
            $settings['hw_skeleton_highlight_color'] = isset( $widget_settings['hw_skeleton_highlight_color'] )
                ? $widget_settings['hw_skeleton_highlight_color']
                : '#f5f5f5';
        }

        if ( ! array_key_exists( 'hw_skeleton_auto_apply', $settings ) ) {
            $settings['hw_skeleton_auto_apply'] = isset( $widget_settings['hw_skeleton_auto_apply'] )
                ? $widget_settings['hw_skeleton_auto_apply']
                : '';
        }

        return $settings;
    }
}
