<?php
namespace HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic;

use DateTimeImmutable;
use DateTimeZone;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class DynamicAddToCalendarWidget extends Widget_Base {

    public function get_name(): string {
        return 'dynamic_add_to_calendar';
    }

    public function get_title(): string {
        return __( 'Dynamic Add to Calendar', 'hw-ele-woo-dynamic' );
    }

    public function get_icon(): string {
        return 'eicon-calendar';
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

    public function get_style_depends(): array {
        return [ 'hw-ele-woo-dynamic-css' ];
    }

    protected function get_upsale_data(): array {
        return [
            'condition'   => true,
            'title'       => esc_html__( 'Need a hand?', 'hw-ele-woo-dynamic' ),
            'description' => esc_html__( 'Visit the documentation for usage tips and dynamic tags examples.', 'hw-ele-woo-dynamic' ),
            'upgrade_url' => esc_url( 'https://lonsdale201.github.io/lonsdale-plugins.github.io/dynamic-ele-ext/widgets/dynamic-add-to-calendar' ),
            'upgrade_text'=> esc_html__( 'Open Github Wiki', 'hw-ele-woo-dynamic' ),
        ];
    }

    protected function register_controls(): void {
        // =========================
        // Content ▸ Button
        // =========================
        $this->start_controls_section(
            'section_button',
            [ 'label' => __( 'Button', 'hw-ele-woo-dynamic' ) ]
        );

        $this->add_control(
            'button_text',
            [
                'label'       => __( 'Button Text', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => __( 'Add to calendar', 'hw-ele-woo-dynamic' ),
                'placeholder' => __( 'Add to calendar', 'hw-ele-woo-dynamic' ),
                'dynamic'     => [ 'active' => true ],
            ]
        );

        $this->add_control(
            'button_icon',
            [
                'label'       => __( 'Icon', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::ICONS,
                'label_block' => true,
                'default'     => [
                    'value'   => 'fas fa-calendar-plus',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
            'icon_position',
            [
                'label'   => __( 'Icon Position', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left'  => [ 'title' => __( 'Left',  'hw-ele-woo-dynamic' ), 'icon' => 'eicon-h-align-left' ],
                    'right' => [ 'title' => __( 'Right', 'hw-ele-woo-dynamic' ), 'icon' => 'eicon-h-align-right' ],
                ],
                'default' => 'left',
                'toggle'  => false,
            ]
        );

        $this->add_responsive_control(
            'icon_gap',
            [
                'label'      => __( 'Icon Gap', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px'  => [ 'min' => 0, 'max' => 60 ],
                    'em'  => [ 'min' => 0, 'max' => 5 ],
                    'rem' => [ 'min' => 0, 'max' => 5 ],
                ],
                'default'    => [ 'size' => 8, 'unit' => 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} .dynamic-add-to-calendar .hwdw-icon.hwdw-icon-left'  => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .dynamic-add-to-calendar .hwdw-icon.hwdw-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_alignment',
            [
                'label'                => __( 'Alignment', 'hw-ele-woo-dynamic' ),
                'type'                 => Controls_Manager::CHOOSE,
                'options'              => [
                    'left'      => [ 'title' => __( 'Left',  'hw-ele-woo-dynamic' ), 'icon' => 'eicon-h-align-left'   ],
                    'center'    => [ 'title' => __( 'Center','hw-ele-woo-dynamic' ), 'icon' => 'eicon-h-align-center' ],
                    'right'     => [ 'title' => __( 'Right', 'hw-ele-woo-dynamic' ), 'icon' => 'eicon-h-align-right'  ],
                    'fullwidth' => [ 'title' => __( 'Full',  'hw-ele-woo-dynamic' ), 'icon' => 'eicon-h-align-stretch'],
                ],
                'default'             => 'left',
                'prefix_class'        => 'hwdw-align-',
                'selectors_dictionary'=> [
                    'left'      => 'left',
                    'center'    => 'center',
                    'right'     => 'right',
                    'fullwidth' => 'left',
                ],
                'selectors'           => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'fullwidth_content_alignment',
            [
                'label'   => __( 'Fullwidth Content Alignment', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start'    => [ 'title' => __( 'Start', 'hw-ele-woo-dynamic' ), 'icon' => 'eicon-text-align-left' ],
                    'center'        => [ 'title' => __( 'Center', 'hw-ele-woo-dynamic' ), 'icon' => 'eicon-text-align-center' ],
                    'flex-end'      => [ 'title' => __( 'End', 'hw-ele-woo-dynamic' ), 'icon' => 'eicon-text-align-right' ],
                    'space-between' => [ 'title' => __( 'Space Between', 'hw-ele-woo-dynamic' ), 'icon' => 'eicon-text-align-justify' ],
                ],
                'default'   => 'center',
                'condition' => [ 'button_alignment' => 'fullwidth' ],
                'selectors' => [
                    '{{WRAPPER}} .dynamic-add-to-calendar-content' => 'justify-content: {{VALUE}};',
                    '{{WRAPPER}} .dynamic-bulk-addtocart-content'  => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // =========================
        // Content ▸ Calendar
        // =========================
        $this->start_controls_section(
            'section_calendar',
            [ 'label' => __( 'Calendar', 'hw-ele-woo-dynamic' ) ]
        );

        $this->add_control(
            'calendar_provider',
            [
                'label'   => __( 'Provider', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'google'  => __( 'Google Calendar', 'hw-ele-woo-dynamic' ),
                    'outlook' => __( 'Outlook / Hotmail', 'hw-ele-woo-dynamic' ),
                ],
                'default' => 'google',
            ]
        );

        $this->add_control(
            'event_title',
            [
                'label'       => __( 'Event Name', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [ 'active' => true ],
                'placeholder' => __( 'Company meeting', 'hw-ele-woo-dynamic' ),
            ]
        );

        $this->add_control(
            'event_description',
            [
                'label'       => __( 'Event Description', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXTAREA,
                'dynamic'     => [ 'active' => true ],
                'placeholder' => __( 'Agenda, notes, links…', 'hw-ele-woo-dynamic' ),
                'rows'        => 4,
            ]
        );

        $this->add_control(
            'event_location',
            [
                'label'       => __( 'Location', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [ 'active' => true ],
                'placeholder' => __( 'Office HQ or video call link', 'hw-ele-woo-dynamic' ),
            ]
        );

        $this->add_control(
            'event_start',
            [
                'label'       => __( 'Start Date & Time', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::DATE_TIME,
                'default'     => gmdate( 'Y-m-d H:i' ),
                'picker_options' => [ 'enableTime' => true ],
            ]
        );

        $this->add_control(
            'event_end',
            [
                'label'       => __( 'End Date & Time', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::DATE_TIME,
                'picker_options' => [ 'enableTime' => true ],
                'description' => __( 'Leave empty to default to one hour after the start time.', 'hw-ele-woo-dynamic' ),
            ]
        );

        $this->end_controls_section();

        // =========================
        // Style ▸ Button
        // =========================
        $this->start_controls_section(
            'section_style_button',
            [
                'label' => __( 'Button', 'hw-ele-woo-dynamic' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'button_label_typography',
                'selector' => '{{WRAPPER}} .dynamic-add-to-calendar .hwdw-button-label',
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label'      => __( 'Padding', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'default'    => [
                    'top'    => 12,
                    'right'  => 24,
                    'bottom' => 12,
                    'left'   => 24,
                    'unit'   => 'px',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .dynamic-add-to-calendar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'button_style_tabs' );

        $this->start_controls_tab( 'button_style_normal', [ 'label' => __( 'Normal', 'hw-ele-woo-dynamic' ) ] );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'button_background_normal',
                'types'    => [ 'classic', 'gradient' ],
                'exclude'  => [ 'image' ],
                'selector' => '{{WRAPPER}} .dynamic-add-to-calendar',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'button_box_shadow_normal',
                'selector' => '{{WRAPPER}} .dynamic-add-to-calendar',
                'exclude'  => [ 'box_shadow_position' ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label'     => __( 'Text Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-add-to-calendar .hwdw-button-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'button_border',
                'selector' => '{{WRAPPER}} .dynamic-add-to-calendar',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab( 'button_style_hover', [ 'label' => __( 'Hover', 'hw-ele-woo-dynamic' ) ] );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'button_background_hover',
                'types'    => [ 'classic', 'gradient' ],
                'exclude'  => [ 'image', 'video' ],
                'selector' => '{{WRAPPER}} .dynamic-add-to-calendar:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'button_box_shadow_hover',
                'selector' => '{{WRAPPER}} .dynamic-add-to-calendar:hover',
                'exclude'  => [ 'box_shadow_position' ],
            ]
        );

        $this->add_control(
            'button_text_color_hover',
            [
                'label'     => __( 'Text Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-add-to-calendar:hover .hwdw-button-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'button_border_hover',
                'selector' => '{{WRAPPER}} .dynamic-add-to-calendar:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'button_transition',
            [
                'type'      => Controls_Manager::HIDDEN,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-add-to-calendar' => 'transition: all .3s ease;',
                ],
            ]
        );

        $this->end_controls_section();

        // =========================
        // Style ▸ Icon
        // =========================
        $this->start_controls_section(
            'section_style_icon',
            [
                'label' => __( 'Icon', 'hw-ele-woo-dynamic' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_icon_color',
            [
                'label'     => __( 'Icon Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-add-to-calendar .hwdw-icon svg' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .dynamic-add-to-calendar .hwdw-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_icon_color_hover',
            [
                'label'     => __( 'Icon Color Hover', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-add-to-calendar:hover .hwdw-icon svg' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .dynamic-add-to-calendar:hover .hwdw-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label'      => __( 'Icon Size', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range'      => [
                    'px'  => [ 'min' => 8,  'max' => 128 ],
                    'em'  => [ 'min' => 0.5,'max' => 8 ],
                    'rem' => [ 'min' => 0.5,'max' => 8 ],
                ],
                'default'    => [ 'size' => 16, 'unit' => 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} .hwdw-icon i'   => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .hwdw-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render(): void {
        $settings = $this->get_settings_for_display();
        $calendar_url = $this->build_calendar_url( $settings );

        if ( empty( $calendar_url ) ) {
            echo '<p>' . esc_html__( 'Calendar link is not available. Please configure the widget settings.', 'hw-ele-woo-dynamic' ) . '</p>';
            return;
        }

        $template = __DIR__ . '/../views/dynamic-add-to-calendar.php';

        if ( ! file_exists( $template ) ) {
            echo '<p>' . esc_html__( 'Template missing.', 'hw-ele-woo-dynamic' ) . '</p>';
            return;
        }

        $calendar_url = esc_url_raw( $calendar_url );

        include $template;
    }

    private function build_calendar_url( array $settings ): string {
        $title = isset( $settings['event_title'] ) ? $this->clean_text( (string) $settings['event_title'] ) : '';
        $start_raw = $settings['event_start'] ?? '';

        if ( '' === trim( $title ) || '' === trim( (string) $start_raw ) ) {
            return '';
        }

        $start = $this->parse_datetime( (string) $start_raw );
        if ( ! $start ) {
            return '';
        }

        $end_raw = $settings['event_end'] ?? '';
        $end     = $end_raw ? $this->parse_datetime( (string) $end_raw ) : null;

        if ( ! $end || $end <= $start ) {
            $end = $start->modify( '+1 hour' );
        }

        $provider    = $settings['calendar_provider'] ?? 'google';
        $description = isset( $settings['event_description'] ) ? $this->clean_text( (string) $settings['event_description'] ) : '';
        $location    = isset( $settings['event_location'] ) ? $this->clean_text( (string) $settings['event_location'] ) : '';

        switch ( $provider ) {
            case 'outlook':
                $base    = 'https://outlook.live.com/calendar/0/deeplink/compose';
                $params  = [
                    'subject' => $title,
                    'body'    => $description,
                    'location'=> $location,
                    'startdt' => $this->format_datetime_outlook( $start ),
                    'enddt'   => $this->format_datetime_outlook( $end ),
                ];
                break;

            case 'google':
            default:
                $base   = 'https://calendar.google.com/calendar/render';
                $params = [
                    'action'  => 'TEMPLATE',
                    'text'    => $title,
                    'details' => $description,
                    'location'=> $location,
                    'dates'   => sprintf( '%s/%s', $this->format_datetime_google( $start ), $this->format_datetime_google( $end ) ),
                ];
                break;
        }

        $params = array_filter( $params, static function ( $value ): bool {
            return '' !== trim( (string) $value );
        } );

        return add_query_arg( $params, $base );
    }

    private function parse_datetime( string $value ): ?DateTimeImmutable {
        $value = trim( $value );
        if ( '' === $value ) {
            return null;
        }

        $timezone = wp_timezone();
        $formats  = [
            'Y-m-d H:i',
            'Y-m-d\TH:i',
            DateTimeImmutable::ATOM,
        ];

        foreach ( $formats as $format ) {
            $date = DateTimeImmutable::createFromFormat( $format, $value, $timezone );
            if ( $date instanceof DateTimeImmutable ) {
                return $date;
            }
        }

        try {
            return new DateTimeImmutable( $value, $timezone );
        } catch ( \Exception $e ) {
            return null;
        }
    }

    private function format_datetime_google( DateTimeImmutable $datetime ): string {
        $utc = $datetime->setTimezone( new DateTimeZone( 'UTC' ) );
        return $utc->format( 'Ymd\THis\Z' );
    }

    private function format_datetime_outlook( DateTimeImmutable $datetime ): string {
        $utc = $datetime->setTimezone( new DateTimeZone( 'UTC' ) );
        return $utc->format( 'Y-m-d\TH:i:s\Z' );
    }

    private function clean_text( string $value ): string {
        $value = wp_strip_all_tags( $value );
        $value = preg_replace( '/\s+/u', ' ', $value );
        return trim( $value );
    }
}
