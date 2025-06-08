<?php
/**
 * Dynamic Calculations 
 *
 * @package hw-ele-woo-dynamic
 */

namespace HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic;


use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use HelloWP\HWEleWooDynamic\Modules\Widgets\QuerySource;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class DynamicCalculationWidget extends Widget_Base {

    public function get_name(): string {
        return 'dynamic-calculation';
    }

    public function get_title(): string {
        return __( 'Dynamic Calculations', 'hw-ele-woo-dynamic' );
    }

    public function get_icon(): string {
        return 'eicon-thumbnails-half';
    }

    public function get_categories(): array {
        return [ 'dynamic-elements' ];
    }

    public function is_dynamic_content(): bool {
        return true;
    }

    protected function get_upsale_data(): array {
		return [
            'condition'   => true,
			'title' => esc_html__( 'Oops, stuck huh?', 'hw-ele-woo-dynamic' ),
			'description' => esc_html__( 'No worries! If you’re not sure how to set up the widget or what it’s all about, check out our GitHub page!', 'hw-ele-woo-dynamic' ),
			'upgrade_url' => esc_url( 'https://lonsdale201.github.io/lonsdale-plugins.github.io/dynamic-ele-ext/widgets/dynamic-calculations' ),
			'upgrade_text' => esc_html__( 'Open Github Wiki', 'hw-ele-woo-dynamic' ),
		];
	}

    protected function register_controls(): void {

        // ────────── Content tab ──────────
        $this->start_controls_section(
            'section_content',
            ['label' => __( 'Content', 'hw-ele-woo-dynamic' )]
        );

        // Query source
        $this->add_control(
            'query_source',
            [
                'label'   => __( 'Query source', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'listing_grid' => __( 'Listing Grid (loop object)', 'hw-ele-woo-dynamic' ),
                    'current_post' => __( 'Current Post',              'hw-ele-woo-dynamic' ),
                    'current_user' => __( 'Current User',              'hw-ele-woo-dynamic' ),
                ],
                'default' => 'listing_grid',
            ]
        );

        $this->add_control(
            'format_number',
            [
                'label'        => __( 'Format Number', 'hw-ele-woo-dynamic' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'hw-ele-woo-dynamic' ),
                'label_off'    => __( 'No', 'hw-ele-woo-dynamic' ),
                'return_value' => 'yes',
                'default'      => '',
            ]
        );
        
        // Decimal separator
        $this->add_control(
            'decimal_separator',
            [
                'label'     => __( 'Decimal Separator', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => '.',
                'condition' => [ 'format_number' => 'yes' ],
            ]
        );
        
        // Thousand separator
        $this->add_control(
            'thousand_separator',
            [
                'label'     => __( 'Thousand Separator', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => ',',
                'condition' => [ 'format_number' => 'yes' ],
            ]
        );
        
        // Decimal points
        $this->add_control(
            'decimals_count',
            [
                'label'     => __( 'Decimal Points', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::NUMBER,
                'default'   => 2,
                'min'       => 0,
                'max'       => 10,
                'step'      => 1,
                'condition' => [ 'format_number' => 'yes' ],
            ]
        );

        // Macros textarea
        $this->add_control(
            'calculation_macros',
            [
                'label'       => __( 'Calculation Macros', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXTAREA,
                'rows'        => 10,
                'description' => __( 'Each “[Operation: …]” defines an operation type. Use variables (e.g. meta values) in the form %meta_key%.', 'hw-ele-woo-dynamic' ),
                'placeholder' => "[Operation: Math]\n%price1% - %price2%\n\n[Operation: MinMax (Min|Max|| - ||Min2|Max2)]\n%a%, %b%, %c%",
            ]
        );

        // Output format
        $this->add_control(
            'output_type',
            [
                'label'   => __( 'Output type', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'plain' => __( 'Plain', 'hw-ele-woo-dynamic' ),
                    'li'    => __( 'List',  'hw-ele-woo-dynamic' ),
                ],
                'default' => 'plain',
            ]
        );

        $this->end_controls_section();

        // ────────── Style tab ──────────
        $this->start_controls_section(
            'section_style_gaps',
            [
                'label' => __( 'Gaps / Spacings', 'hw-ele-woo-dynamic' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'      => 'value_typography',
                'label'     => __( 'Value Typography', 'hw-ele-woo-dynamic' ),
                'selector'  => '{{WRAPPER}} .calc-value',
            ]
        );
        
        // Color for value
        $this->add_control(
            'value_color',
            [
                'label'     => __( 'Value Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calc-value' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Prefix gap
        $this->add_responsive_control(
            'prefix_gap',
            [
                'label'      => __( 'Prefix Gap', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
                'default'    => [ 'size' => 6, 'unit' => 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} .calc-prefix' => 'margin-right: {{SIZE}}{{UNIT}};', 
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'prefix_typography',
                'label'    => __( 'Prefix Typography', 'hw-ele-woo-dynamic' ),
                'selector' => '{{WRAPPER}} .calc-prefix',
            ]
        );

        $this->add_control(
            'prefix_color',
            [
                'label'     => __( 'Prefix Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calc-prefix' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Separator gap
        $this->add_responsive_control(
            'sep_gap',
            [
                'label'      => __( 'Separator Gap', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
                'default'    => [ 'size' => 6, 'unit' => 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} .calc-sep' => 'margin: 0 {{SIZE}}{{UNIT}};', 
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'sep_typography',
                'label'    => __( 'Separator Typography', 'hw-ele-woo-dynamic' ),
                'selector' => '{{WRAPPER}} .calc-sep',
            ]
        );
        
        // Separator Color
        $this->add_control(
            'sep_color',
            [
                'label'     => __( 'Separator Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calc-sep' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Suffix gap
        $this->add_responsive_control(
            'suffix_gap',
            [
                'label'      => __( 'Suffix Gap', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
                'default'    => [ 'size' => 6, 'unit' => 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} .calc-suffix' => 'margin-left: {{SIZE}}{{UNIT}};', 
                ],
            ]
        );

        // Suffix Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'suffix_typography',
                'label'    => __( 'Suffix Typography', 'hw-ele-woo-dynamic' ),
                'selector' => '{{WRAPPER}} .calc-suffix',
            ]
        );

        // Suffix Color
        $this->add_control(
            'suffix_color',
            [
                'label'     => __( 'Suffix Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calc-suffix' => 'color: {{VALUE}};',
                ],
            ]
        );


        // Vertical gap between list items (only if output is list)
        $this->add_responsive_control(
            'li_vertical_gap',
            [
                'label'      => __( 'List Item Gap', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
                'default'    => [ 'size' => 8, 'unit' => 'px' ],
                'condition'  => [
                    'output_type' => 'li',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .dynamic-calculation-list .dynamic-calculation-item'        => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .dynamic-calculation-list .dynamic-calculation-item:last-child' => 'margin-bottom: 0;',
                ],
            ]
        );


        $this->end_controls_section();
    }

  protected function render(): void {
    $settings = $this->get_settings_for_display();

    $raw   = trim( $settings['calculation_macros'] ?? '' );
    $lines = preg_split( '/\r?\n/', $raw, -1, PREG_SPLIT_NO_EMPTY );
    if ( ! $lines ) {
        return;
    }

    $values = [];
    foreach ( $lines as $line ) {
        if ( preg_match_all( '/\%([^\%]+)\%/', $line, $m ) ) {
            foreach ( $m[1] as $token ) {
                $raw_val = QuerySource::get_meta( $token, $settings['query_source'] );
                $values[ $token ] = is_numeric( $raw_val ) ? $raw_val + 0 : $raw_val;
            }
        }
    }

    include __DIR__ . '/../views/dynamic-calculation.php';
}

}