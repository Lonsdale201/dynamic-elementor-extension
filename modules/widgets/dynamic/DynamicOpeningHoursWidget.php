<?php
namespace HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use HelloWP\HWEleWooDynamic\Modules\Widgets\QuerySource;

class DynamicOpeningHoursWidget extends Widget_Base {

    private const DAYS = [
        'monday'    => [ 'label_default' => 'Monday',    'order' => 1 ],
        'tuesday'   => [ 'label_default' => 'Tuesday',   'order' => 2 ],
        'wednesday' => [ 'label_default' => 'Wednesday', 'order' => 3 ],
        'thursday'  => [ 'label_default' => 'Thursday',  'order' => 4 ],
        'friday'    => [ 'label_default' => 'Friday',    'order' => 5 ],
        'saturday'  => [ 'label_default' => 'Saturday',  'order' => 6 ],
        'sunday'    => [ 'label_default' => 'Sunday',    'order' => 0 ],
    ];

    public function get_name(): string {
        return 'dynamic-opening-hours';
    }

    public function get_title(): string {
        return __( 'Dynamic Opening Hours', 'hw-ele-woo-dynamic' );
    }

    public function get_icon(): string {
        return 'eicon-clock';
    }

    public function get_categories(): array {
        return [ 'dynamic-elements' ];
    }

    public function has_widget_inner_wrapper(): bool {
        return false;
    }

    public function is_dynamic_content(): bool {
        return true;
    }

    protected function get_upsale_data(): array {
        return [
            'condition'   => true,
            'title'       => esc_html__( 'Need a hand?', 'hw-ele-woo-dynamic' ),
            'description' => esc_html__( 'Visit the documentation for setup tips and examples.', 'hw-ele-woo-dynamic' ),
            'upgrade_url' => esc_url( 'https://lonsdale201.github.io/lonsdale-plugins.github.io/dynamic-ele-ext/widgets/dynamic-opening-hours' ),
            'upgrade_text'=> esc_html__( 'Open Github Wiki', 'hw-ele-woo-dynamic' ),
        ];
    }

    public function get_style_depends(): array {
        return [ 'hw-ele-woo-dynamic-css' ];
    }

    protected function register_controls(): void {
        $this->register_general_controls();
        $this->register_day_controls();
        $this->register_day_label_controls();
        $this->register_style_controls();
    }

    private function register_general_controls(): void {
        $this->start_controls_section(
            'section_general',
            [ 'label' => __( 'Content', 'hw-ele-woo-dynamic' ) ]
        );

        $this->add_control(
            'query_source',
            [
                'label'   => __( 'Query Source', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'listing_grid' => __( 'Listing Grid (loop object)', 'hw-ele-woo-dynamic' ),
                    'current_post' => __( 'Current Post',             'hw-ele-woo-dynamic' ),
                    'current_user' => __( 'Current User',             'hw-ele-woo-dynamic' ),
                ],
                'default' => 'listing_grid',
            ]
        );

        $this->add_control(
            'title_text',
            [
                'label'       => __( 'Title', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => __( 'Opening hours', 'hw-ele-woo-dynamic' ),
                'placeholder' => __( 'Opening hours', 'hw-ele-woo-dynamic' ),
                'dynamic'     => [ 'active' => true ],
            ]
        );

        $this->add_control(
            'intro_description',
            [
                'label'       => __( 'Description', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::WYSIWYG,
                'default'     => '',
                'dynamic'     => [ 'active' => true ],
            ]
        );

        $this->add_control(
            'time_am_label',
            [
                'label'       => __( 'AM Label', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => __( 'AM', 'hw-ele-woo-dynamic' ),
                'dynamic'     => [ 'active' => true ],
            ]
        );

        $this->add_control(
            'time_pm_label',
            [
                'label'       => __( 'PM Label', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => __( 'PM', 'hw-ele-woo-dynamic' ),
                'dynamic'     => [ 'active' => true ],
            ]
        );

        $this->add_control(
            'display_type',
            [
                'label'   => __( 'Display Type', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'status' => __( 'Status badge', 'hw-ele-woo-dynamic' ),
                    'list'   => __( 'Weekly list', 'hw-ele-woo-dynamic' ),
                ],
                'default' => 'list',
            ]
        );

        $this->add_control(
            'hide_status_in_list',
            [
                'label'        => __( 'Hide Status Badge', 'hw-ele-woo-dynamic' ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
                'condition'    => [ 'display_type' => 'list' ],
            ]
        );

        $this->add_control(
            'opening_text',
            [
                'label'       => __( 'Open Text', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => __( 'Open now', 'hw-ele-woo-dynamic' ),
                'dynamic'     => [ 'active' => true ],
            ]
        );

        $this->add_control(
            'closed_text',
            [
                'label'       => __( 'Closed Text', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => __( 'Closed', 'hw-ele-woo-dynamic' ),
                'dynamic'     => [ 'active' => true ],
            ]
        );

        $this->add_control(
            'fallback_text',
            [
                'label'       => __( 'Fallback Text', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => '',
                'description' => __( 'Displayed when no opening hours data is available for any day.', 'hw-ele-woo-dynamic' ),
                'dynamic'     => [ 'active' => true ],
            ]
        );

        $this->end_controls_section();
    }

    private function register_day_controls(): void {
        foreach ( self::DAYS as $day_key => $meta ) {
            $this->start_controls_section(
                'section_day_' . $day_key,
                [ 'label' => sprintf( __( '%s Settings', 'hw-ele-woo-dynamic' ), ucfirst( $day_key ) ) ]
            );

            $this->add_control(
                $day_key . '_meta',
                [
                    'label'       => __( 'Meta Field ID', 'hw-ele-woo-dynamic' ),
                    'type'        => Controls_Manager::TEXT,
                    'dynamic'     => [ 'active' => true ],
                    'placeholder' => sprintf( '%s_meta_id', $day_key ),
                    'description' => __( 'Accepted formats: 10:00-14:00 or 10 AM – 2 PM. Leave empty to mark the day as closed.', 'hw-ele-woo-dynamic' ),
                ]
            );

            $this->end_controls_section();
        }
    }

    private function register_day_label_controls(): void {
        $this->start_controls_section(
            'section_day_labels',
            [ 'label' => __( 'Day Labels', 'hw-ele-woo-dynamic' ) ]
        );

        foreach ( self::DAYS as $day_key => $data ) {
            $this->add_control(
                $day_key . '_label',
                [
                    'label'       => sprintf( __( '%s Label', 'hw-ele-woo-dynamic' ), ucfirst( $day_key ) ),
                    'type'        => Controls_Manager::TEXT,
                    'default'     => $this->get_default_day_label( $day_key ),
                    'dynamic'     => [ 'active' => true ],
                ]
            );
        }

        $this->end_controls_section();
    }

    private function register_style_controls(): void {
        // Title
        $this->start_controls_section(
            'section_style_title',
            [
                'label' => __( 'Title', 'hw-ele-woo-dynamic' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .dynamic-opening-hours__title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => __( 'Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-opening-hours__title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'title_border',
                'selector' => '{{WRAPPER}} .dynamic-opening-hours__title',
            ]
        );

        $this->add_responsive_control(
            'title_padding',
            [
                'label'      => __( 'Padding', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .dynamic-opening-hours__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Description
        $this->start_controls_section(
            'section_style_description',
            [
                'label' => __( 'Description', 'hw-ele-woo-dynamic' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'description_typography',
                'selector' => '{{WRAPPER}} .dynamic-opening-hours__description',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label'     => __( 'Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-opening-hours__description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Status
        $this->start_controls_section(
            'section_style_status',
            [
                'label' => __( 'Status Badge', 'hw-ele-woo-dynamic' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'status_typography',
                'selector' => '{{WRAPPER}} .dynamic-opening-hours__status',
            ]
        );

        $this->add_responsive_control(
            'status_padding',
            [
                'label'      => __( 'Padding', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .dynamic-opening-hours__status' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'status_open_color',
            [
                'label'     => __( 'Open Text Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-opening-hours__status--open' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'status_open_background',
            [
                'label'     => __( 'Open Background', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-opening-hours__status--open' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'status_closed_color',
            [
                'label'     => __( 'Closed Text Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-opening-hours__status--closed' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'status_closed_background',
            [
                'label'     => __( 'Closed Background', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-opening-hours__status--closed' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'status_border',
                'selector' => '{{WRAPPER}} .dynamic-opening-hours__status',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'status_box_shadow',
                'selector' => '{{WRAPPER}} .dynamic-opening-hours__status',
            ]
        );

        $this->add_responsive_control(
            'status_border_radius',
            [
                'label'      => __( 'Border Radius', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .dynamic-opening-hours__status' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Day rows
        $this->start_controls_section(
            'section_style_days',
            [
                'label' => __( 'Day Rows', 'hw-ele-woo-dynamic' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'day_label_typography',
                'selector' => '{{WRAPPER}} .dynamic-opening-hours__day-label',
            ]
        );

        $this->add_control(
            'day_label_color',
            [
                'label'     => __( 'Day Label Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-opening-hours__day-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'time_typography',
                'selector' => '{{WRAPPER}} .dynamic-opening-hours__day-time',
            ]
        );

        $this->add_control(
            'time_color',
            [
                'label'     => __( 'Time Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-opening-hours__day-time' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'day_row_gap',
            [
                'label'      => __( 'Row Gap', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .dynamic-opening-hours__list' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'day_row_border',
                'selector' => '{{WRAPPER}} .dynamic-opening-hours__day',
            ]
        );

        $this->add_responsive_control(
            'day_row_padding',
            [
                'label'      => __( 'Row Padding', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .dynamic-opening-hours__day' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'divider_enable',
            [
                'label'        => __( 'Enable Row Divider', 'hw-ele-woo-dynamic' ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
            ]
        );

        $this->add_responsive_control(
            'divider_thickness',
            [
                'label'      => __( 'Divider Thickness', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [ 'px' => [ 'min' => 0, 'max' => 10 ] ],
                'default'    => [ 'size' => 1, 'unit' => 'px' ],
                'condition'  => [ 'divider_enable' => 'yes' ],
                'selectors'  => [
                    '{{WRAPPER}} .dynamic-opening-hours__day:not(:last-child)' => 'border-bottom-width: {{SIZE}}{{UNIT}}; border-bottom-style: solid;',
                ],
            ]
        );

        $this->add_control(
            'divider_color',
            [
                'label'     => __( 'Divider Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => [ 'divider_enable' => 'yes' ],
                'selectors' => [
                    '{{WRAPPER}} .dynamic-opening-hours__day:not(:last-child)' => 'border-bottom-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'divider_spacing',
            [
                'label'      => __( 'Divider Spacing', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'condition'  => [ 'divider_enable' => 'yes' ],
                'selectors'  => [
                    '{{WRAPPER}} .dynamic-opening-hours__day:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'row_background_even',
            [
                'label'     => __( 'Even Row Background', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-opening-hours__day:nth-child(even)' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'row_text_even_color',
            [
                'label'     => __( 'Even Row Text Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-opening-hours__day:nth-child(even):not(.dynamic-opening-hours__day--today) .dynamic-opening-hours__day-label' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .dynamic-opening-hours__day:nth-child(even):not(.dynamic-opening-hours__day--today) .dynamic-opening-hours__day-time'  => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'row_background_odd',
            [
                'label'     => __( 'Odd Row Background', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-opening-hours__day:nth-child(odd)' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'row_text_odd_color',
            [
                'label'     => __( 'Odd Row Text Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-opening-hours__day:nth-child(odd):not(.dynamic-opening-hours__day--today) .dynamic-opening-hours__day-label' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .dynamic-opening-hours__day:nth-child(odd):not(.dynamic-opening-hours__day--today) .dynamic-opening-hours__day-time'  => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Highlight
        $this->start_controls_section(
            'section_style_highlight',
            [
                'label' => __( 'Highlight (Today)', 'hw-ele-woo-dynamic' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'highlight_typography',
                'selector' => '{{WRAPPER}} .dynamic-opening-hours__day--today .dynamic-opening-hours__day-label, {{WRAPPER}} .dynamic-opening-hours__day--today .dynamic-opening-hours__day-time',
            ]
        );

        $this->add_control(
            'highlight_text_color',
            [
                'label'     => __( 'Text Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-opening-hours__day.dynamic-opening-hours__day--today .dynamic-opening-hours__day-label' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .dynamic-opening-hours__day.dynamic-opening-hours__day--today .dynamic-opening-hours__day-time'  => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'highlight_background',
            [
                'label'     => __( 'Background', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-opening-hours__day--today' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render(): void {
        $settings = $this->get_settings_for_display();
        $days     = $this->build_days_data( $settings );
        $status   = $this->determine_status( $settings, $days );

        $description_html         = $settings['intro_description'] ?? '';
        $description_has_content  = '' !== trim( wp_strip_all_tags( $description_html ) );
        $fallback_text            = isset( $settings['fallback_text'] ) ? trim( (string) $settings['fallback_text'] ) : '';
        $has_opening_hours_data   = $this->has_opening_hours_data( $days );
        $should_show_fallback     = ( '' !== $fallback_text ) && ! $has_opening_hours_data;

        if ( ! $has_opening_hours_data && '' === $fallback_text ) {
            return;
        }

        $display_type = $settings['display_type'] ?? 'list';

        $template = __DIR__ . '/../views/dynamic-opening-hours.php';
        if ( ! file_exists( $template ) ) {
            echo '<p>' . esc_html__( 'Template missing.', 'hw-ele-woo-dynamic' ) . '</p>';
            return;
        }

        include $template;
    }

    private function has_opening_hours_data( array $days ): bool {
        foreach ( $days as $day ) {
            $raw       = isset( $day['raw'] ) ? trim( (string) $day['raw'] ) : '';
            $formatted = isset( $day['formatted'] ) ? trim( (string) $day['formatted'] ) : '';

            if ( '' !== $raw || '' !== $formatted ) {
                return true;
            }
        }

        return false;
    }

    private function build_days_data( array $settings ): array {
        $results = [];
        $query_source = $settings['query_source'] ?? 'listing_grid';
        $timezone = wp_timezone();

        $am_label = $settings['time_am_label'] ?? __( 'AM', 'hw-ele-woo-dynamic' );
        $pm_label = $settings['time_pm_label'] ?? __( 'PM', 'hw-ele-woo-dynamic' );

        foreach ( self::DAYS as $day_key => $meta ) {
            $meta_key = $settings[ $day_key . '_meta' ] ?? '';
            $label    = $settings[ $day_key . '_label' ] ?? $this->get_default_day_label( $day_key );

            $raw_value = '';
            if ( $meta_key ) {
                $raw_meta = QuerySource::get_meta( $meta_key, $query_source );
                $raw_value = $this->stringify_meta_value( $raw_meta );
            }

            $intervals = $this->parse_intervals( $raw_value, $meta['order'], $timezone );

            $formatted = $this->format_intervals_for_display( $intervals, $am_label, $pm_label );
            $display   = '' !== trim( $formatted ) ? $formatted : $raw_value;

            $results[ $day_key ] = [
                'label'      => $label,
                'raw'        => $raw_value,
                'display'    => $display,
                'formatted'  => $formatted,
                'intervals'  => $intervals,
                'is_closed'  => empty( $intervals ) && '' === trim( $raw_value ),
                'is_today'   => $this->is_today( $meta['order'] ),
                'order'      => $meta['order'],
            ];
        }

        return $results;
    }

    private function determine_status( array $settings, array $days ): array {
        $current_day = $this->get_current_day_key();
        $opening = $settings['opening_text'] ?? __( 'Open now', 'hw-ele-woo-dynamic' );
        $closed  = $settings['closed_text'] ?? __( 'Closed', 'hw-ele-woo-dynamic' );

        $is_open = false;
        if ( isset( $days[ $current_day ] ) ) {
            $is_open = $this->is_open_now( $days[ $current_day ]['intervals'] );
        }

        return [
            'is_open' => $is_open,
            'text'    => $is_open ? $opening : $closed,
        ];
    }

    private function stringify_meta_value( $value ): string {
        if ( is_array( $value ) ) {
            $flat = [];
            array_walk_recursive( $value, static function ( $item ) use ( &$flat ) {
                if ( is_string( $item ) || is_numeric( $item ) ) {
                    $flat[] = trim( (string) $item );
                }
            } );
            $value = implode( ', ', array_filter( $flat ) );
        }

        $value = trim( (string) $value );
        return $value;
    }

    private function parse_intervals( string $raw_value, int $day_order, DateTimeZone $timezone ): array {
        if ( '' === $raw_value ) {
            return [];
        }

        $normalized = str_replace( [ '\u2013', '\u2014', '\u2012', '–', '—' ], '-', $raw_value );
        $normalized = str_replace( [ '\u2012' ], '-', $normalized );
        $normalized = str_replace( [ ' to ' ], '-', $normalized );

        $chunks = preg_split( '/[\n;,]+/u', $normalized );
        $chunks = array_filter( array_map( 'trim', (array) $chunks ) );

        $intervals = [];
        $day_base  = $this->get_day_reference( $day_order, $timezone );

        foreach ( $chunks as $chunk ) {
            if ( preg_match( '/^(closed|off)$/i', $chunk ) ) {
                return [];
            }

            if ( ! str_contains( $chunk, '-' ) ) {
                continue;
            }

            [ $start_raw, $end_raw ] = array_map( 'trim', explode( '-', $chunk, 2 ) );

            $start = $this->parse_time_token( $start_raw, $day_base );
            $end   = $this->parse_time_token( $end_raw, $day_base );

            if ( ! $start || ! $end ) {
                continue;
            }

            if ( $end <= $start ) {
                $end = $end->add( new DateInterval( 'PT24H' ) );
            }

            $intervals[] = [ 'start' => $start, 'end' => $end ];
        }

        return $intervals;
    }

    private function get_day_reference( int $day_order, DateTimeZone $timezone ): DateTimeImmutable {
        $now        = new DateTimeImmutable( 'now', $timezone );
        $todayOrder = (int) $now->format( 'w' );
        $diff       = $day_order - $todayOrder;
        if ( $diff === 0 ) {
            return $now->setTime( 0, 0 );
        }

        return $now->modify( sprintf( '%+d day', $diff ) )->setTime( 0, 0 );
    }

    private function parse_time_token( string $token, DateTimeImmutable $day_base ): ?DateTimeImmutable {
        $token = strtolower( trim( $token ) );
        if ( '' === $token ) {
            return null;
        }

        $token = str_replace( [ '.', 'am', 'pm' ], [ ':', ' am', ' pm' ], $token );
        $token = preg_replace( '/\s+/', ' ', $token );

        $formats = [
            'H:i',
            'H',
            'G:i',
            'G',
            'g:i a',
            'g a',
            'g:i\sa',
            'g\sa',
        ];

        foreach ( $formats as $format ) {
            $date = DateTimeImmutable::createFromFormat( $format, $token, $day_base->getTimezone() );
            if ( $date instanceof DateTimeImmutable ) {
                return $day_base->setTime( (int) $date->format( 'H' ), (int) $date->format( 'i' ) );
            }
        }

        try {
            $date = new DateTimeImmutable( $token, $day_base->getTimezone() );
            return $day_base->setTime( (int) $date->format( 'H' ), (int) $date->format( 'i' ) );
        } catch ( \Exception $e ) {
            return null;
        }
    }

    private function is_today( int $day_order ): bool {
        $nowOrder = (int) current_time( 'w' );
        return $nowOrder === $day_order;
    }

    private function get_current_day_key(): string {
        $order = (int) current_time( 'w' );
        foreach ( self::DAYS as $key => $data ) {
            if ( $data['order'] === $order ) {
                return $key;
            }
        }
        return 'sunday';
    }

    private function is_open_now( array $intervals ): bool {
        if ( empty( $intervals ) ) {
            return false;
        }

        $now = new DateTimeImmutable( 'now', wp_timezone() );

        foreach ( $intervals as $interval ) {
            if ( $now >= $interval['start'] && $now < $interval['end'] ) {
                return true;
            }
        }

        return false;
    }

    private function get_default_day_label( string $day_key ): string {
        switch ( $day_key ) {
            case 'monday':
                return __( 'Monday', 'hw-ele-woo-dynamic' );
            case 'tuesday':
                return __( 'Tuesday', 'hw-ele-woo-dynamic' );
            case 'wednesday':
                return __( 'Wednesday', 'hw-ele-woo-dynamic' );
            case 'thursday':
                return __( 'Thursday', 'hw-ele-woo-dynamic' );
            case 'friday':
                return __( 'Friday', 'hw-ele-woo-dynamic' );
            case 'saturday':
                return __( 'Saturday', 'hw-ele-woo-dynamic' );
            case 'sunday':
            default:
                return __( 'Sunday', 'hw-ele-woo-dynamic' );
        }
    }

    private function format_intervals_for_display( array $intervals, string $am_label, string $pm_label ): string {
        if ( empty( $intervals ) ) {
            return '';
        }

        $time_format = get_option( 'time_format', 'H:i' );
        $parts       = [];

        foreach ( $intervals as $interval ) {
            $start_str = $interval['start']->format( $time_format );
            $end_str   = $interval['end']->format( $time_format );

            $start_str = $this->replace_meridiem_label( $start_str, $am_label, $pm_label );
            $end_str   = $this->replace_meridiem_label( $end_str, $am_label, $pm_label );

            $parts[]   = sprintf( '%s – %s', $start_str, $end_str );
        }

        return implode( ', ', $parts );
    }

    private function replace_meridiem_label( string $time, string $am_label, string $pm_label ): string {
        $time = preg_replace_callback(
            '/\b(am|pm)\b/i',
            static function ( $matches ) use ( $am_label, $pm_label ) {
                $match = strtolower( $matches[1] );
                if ( 'am' === $match ) {
                    return $am_label;
                }
                if ( 'pm' === $match ) {
                    return $pm_label;
                }
                return '';
            },
            $time
        );

        return trim( preg_replace( '/\s+/', ' ', $time ) );
    }
}
