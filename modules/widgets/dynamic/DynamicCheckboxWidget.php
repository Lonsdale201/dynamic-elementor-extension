<?php
/**
 * Dynamic Checkbox – Listing Grid + Current Post / User
 *
 * @package hw-ele-woo-dynamic
 */

namespace HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use HelloWP\HWEleWooDynamic\Modules\Widgets\QuerySource;
use WP_Post;
use WP_User;
use WP_Term;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* Jelöltek kiszűrése */
function hw_only_checked( $raw ): array {
	if ( is_string( $raw ) ) {
		$maybe = maybe_unserialize( $raw );
		if ( is_array( $maybe ) ) {
			$raw = $maybe;
		}
	}
	if ( is_array( $raw ) ) {
		if ( count( $raw ) === 1 && isset( $raw[0] ) && is_array( $raw[0] ) ) {
			$raw = $raw[0];
		} elseif ( array_keys( $raw ) === range( 0, count( $raw ) - 1 ) && is_array( $raw[0] ) ) {
			$raw = array_merge( ...array_values( $raw ) );
		}
	}
	$checked = [];
	if ( is_array( $raw ) ) {
		$assoc = array_keys( $raw ) !== range( 0, count( $raw ) - 1 );
		if ( $assoc ) {
			foreach ( $raw as $k => $v ) {
				if ( filter_var( $v, FILTER_VALIDATE_BOOLEAN ) ) {
					$checked[] = $k;
				}
			}
		} else {
			$checked = $raw;
		}
	}
	return array_values( array_unique( $checked ) );
}

function hw_auto_close( string $open ): string {
	if ( preg_match( '/<\s*([a-z0-9\-]+)/i', $open, $m ) ) {
		return '</' . strtolower( $m[1] ) . '>';
	}
	return '';
}

/* ──────────  WIDGET  ────────── */
class DynamicCheckboxWidget extends Widget_Base {

	public function get_name(): string  { return 'dynamic-checkbox'; }
	public function get_title(): string { return __( 'Dynamic Checkbox', 'hw-ele-woo-dynamic' ); }
	public function get_icon(): string  { return 'eicon-checkbox'; }
	public function get_categories(): array { return [ 'dynamic-elements' ]; }
	public function get_keywords(): array   { return [ 'dynamic', 'checkbox', 'list' ]; }
	public function has_widget_inner_wrapper(): bool { return false; }
	public function is_dynamic_content(): bool       { return true; }
	protected function get_upsale_data(): array {
		return [
            'condition'   => true,
			'title' => esc_html__( 'Oops, stuck huh?', 'hw-ele-woo-dynamic' ),
			'description' => esc_html__( 'No worries! If you’re not sure how to set up the widget or what it’s all about, check out our GitHub page!', 'hw-ele-woo-dynamic' ),
			'upgrade_url' => esc_url( 'https://lonsdale201.github.io/lonsdale-plugins.github.io/dynamic-ele-ext/widgets/dynamic-checkbox' ),
			'upgrade_text' => esc_html__( 'Open Github Wiki', 'hw-ele-woo-dynamic' ),
		];
	}

	/* ----- Controls ----- */
	protected function register_controls(): void {

		$this->start_controls_section(
			'section_content',
			[ 'label' => __( 'Content', 'hw-ele-woo-dynamic' ) ]
		);

		$this->add_control( 'query_source', [
			'label'   => __( 'Query source', 'hw-ele-woo-dynamic' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'listing_grid' => __( 'Listing Grid (loop object)', 'hw-ele-woo-dynamic' ),
				'current_post' => __( 'Current Post',             'hw-ele-woo-dynamic' ),
				'current_user' => __( 'Current User',             'hw-ele-woo-dynamic' ),
			],
			'default' => 'listing_grid',
		] );

		$this->add_control(
			'meta_field_id',
			[
				'label'       => __( 'Meta Field ID', 'hw-ele-woo-dynamic' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$this->add_control( 'html_tag', [
			'label'   => esc_html__( 'HTML tag', 'hw-ele-woo-dynamic' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'div'  => 'div',
				'span' => 'span',
				'p'    => 'p',
			],
			'default' => 'div',
		] );

		// 1) Output format
		$this->add_control( 'output_format', [
		'label'   => esc_html__( 'Output format', 'hw-ele-woo-dynamic' ),
		'type'    => Controls_Manager::SELECT,
		'options' => [
			'li_order'  => esc_html__( 'Li (order)',  'hw-ele-woo-dynamic' ),
			'li_bullet' => esc_html__( 'Li (bullet)', 'hw-ele-woo-dynamic' ),
			'plain'     => esc_html__( 'Plain',       'hw-ele-woo-dynamic' ),
			'flex'      => esc_html__( 'Flex',        'hw-ele-woo-dynamic' ),
			'grid'      => esc_html__( 'Grid',        'hw-ele-woo-dynamic' ),
		],
		'default' => 'li_bullet',
		] );

		$this->add_control(
			'icon',
			[
				'label'   => esc_html__( 'Icon', 'hw-ele-woo-dynamic' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => '',
					'library' => 'fa-solid',
				],
			]
		);
			

		$this->add_control(
			'flex_direction',
			[
				'label'   => __( 'Flex Direction', 'hw-ele-woo-dynamic' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'row'    => [ 'title' => __( 'Row',    'hw-ele-woo-dynamic' ), 'icon' => 'eicon-arrow-right' ],
					'column' => [ 'title' => __( 'Column', 'hw-ele-woo-dynamic' ), 'icon' => 'eicon-arrow-down'  ],
				],
				'default' => 'row',
				'toggle'  => false,

				'selectors_dictionary' => [
					'row'    => 'display:flex;flex-wrap:wrap;flex-direction:row;align-items:center;',
					'column' => 'display:flex;flex-wrap:wrap;flex-direction:column;align-items:flex-start;',
				],
				'selectors' => [
					'{{WRAPPER}} .dynamic-checkbox-wrapper--flex' => '{{VALUE}}',
				],
			]
		);
				
		$this->add_responsive_control(
			'layout_gap',
			[
			  'label'      => __( 'Items Gap', 'hw-ele-woo-dynamic' ),
			  'type'       => Controls_Manager::SLIDER,
			  'condition' => [
				'output_format' => [ 'flex', 'grid', 'plain' ],
				],
			  'size_units' => [ 'px', '%', 'em', 'rem' ],
			  'range'      => [
				'px' => [ 'min' => 0, 'max' => 100 ],
			  ],
			  'default'    => [ 'size' => 8, 'unit' => 'px' ],
			  'selectors'  => [
				'{{WRAPPER}} .dynamic-checkbox-wrapper--plain' => 'display: flex; flex-wrap: wrap; align-items: center; gap: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .dynamic-checkbox-wrapper--flex'  => 'display: flex; flex-wrap: wrap; gap: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .dynamic-checkbox-wrapper--grid'  => 'display:grid; grid-template-columns:repeat({{SIZE}},1fr); gap: {{SIZE}}{{UNIT}};',
			  ],
			]
		  );

		$this->add_control( 'order_type', [
			'label'   => esc_html__( 'Order type', 'hw-ele-woo-dynamic' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'default'     => esc_html__( 'Default',         'hw-ele-woo-dynamic' ),
				'random'      => esc_html__( 'Random',          'hw-ele-woo-dynamic' ),
				'value_asc'   => esc_html__( 'By value ASC',    'hw-ele-woo-dynamic' ),
				'value_desc'  => esc_html__( 'By value DESC',   'hw-ele-woo-dynamic' ),
			],
			'default' => 'default',
		] );		
	
		// 2) Delimiter
		$this->add_control( 'plain_delimiter', [
			'label'       => esc_html__( 'Delimiter', 'hw-ele-woo-dynamic' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => ', ',
			'condition'   => [
				'output_format' => 'plain',
			],
		] );
	
		$this->add_control( 'hide_if_empty', [
			'label'        => esc_html__( 'Hide if empty', 'hw-ele-woo-dynamic' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
			'separator'    => 'before',
		] );
	
		// 6) Fallback
		$this->add_control( 'fallback', [
			'label'       => esc_html__( 'Fallback', 'hw-ele-woo-dynamic' ),
			'type'        => Controls_Manager::TEXT,
			'dynamic'     => [
				'active' => true,
			],
			'description' => esc_html__( 'Fallback if no values are found', 'hw-ele-woo-dynamic' ),
		] );

		$this->add_responsive_control(
			'grid_columns',
			[
				'label'     => __( 'Grid Columns', 'hw-ele-woo-dynamic' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 12,
				'step'      => 1,
				'condition' => [ 'output_format' => 'grid' ],
				'selectors' => [
					'{{WRAPPER}} .dynamic-checkbox-wrapper' => 'display:grid;grid-template-columns:repeat({{SIZE}},1fr);',
				],
			]
		);

		// 4) Advanced output switcher
		$this->add_control( 'advanced_output', [
			'label'        => esc_html__( 'Advanced output', 'hw-ele-woo-dynamic' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
			'separator'    => 'before',
		] );

		$this->add_control( 'macro_rules', [
			'label'       => esc_html__( 'Macro rules', 'hw-ele-woo-dynamic' ),
			'type'        => Controls_Manager::TEXTAREA,
			'placeholder' => "%even% <div class=\"example\">\n%odd%  <div class=\"example2\">\n%3%   <span class=\"nth3\">#3</span>",
			'description' => esc_html__( "One command per line: MACROS (example:. %even%, %first%, %last%, %odd%, %3%, %3n% %value_checkboxmetadata%) + space + HTML/snippet.", 'hw-ele-woo-dynamic' ),
			'condition'   => [ 'advanced_output' => 'yes' ],
		] );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_field',
			[
				'label' => __( 'Items', 'hw-ele-woo-dynamic' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		
		// Field color
		$this->add_control(
			'field_color',
			[
				'label'     => __( 'Color', 'hw-ele-woo-dynamic' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-checkbox-item-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'hw-ele-woo-dynamic' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-checkbox-item'   => 'background-color: {{VALUE}};',
				],
			]
		);
		
		// Field typography
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'field_typography',
				'label'     => __( 'Typography', 'hw-ele-woo-dynamic' ),
				'selector'  => '{{WRAPPER}} .dynamic-checkbox-item-content',
				'separator' => 'before', 
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'       => __( 'Item Padding', 'hw-ele-woo-dynamic' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px', '%', 'em', 'rem', 'custom' ],
				'default'     => [
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => false,
				],
				'condition'   => [
					'output_format' => [ 'plain', 'flex', 'grid' ],
				],
				'selectors'   => [
					// '{{WRAPPER}} .dynamic-checkbox-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .dynamic-checkbox-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_margin',
			[
				'label'       => __( 'Item Margin', 'hw-ele-woo-dynamic' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px', '%', 'em', 'rem', 'custom' ],
				'default'     => [
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => false,
				],
				'condition'   => [
					'output_format' => [ 'plain', 'flex', 'grid' ],
				],
				'selectors'   => [
					'{{WRAPPER}} .dynamic-checkbox-item:not(:first-child):not(:last-child)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'item_border',
				'label' => 'Border',
				'selector' => '{{WRAPPER}} .dynamic-checkbox-item',
				'separator' => 'before', 
			]
		);

		$this->add_responsive_control(
			'item_border-radius',
			[
				'label'       => __( 'Border Radius', 'hw-ele-woo-dynamic' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px', '%', 'em', 'rem', 'custom' ],
				'default'     => [
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => false,
				],
				'condition'   => [
					'output_format' => [ 'plain', 'flex', 'grid' ],
				],
				'selectors'   => [
					// '{{WRAPPER}} .dynamic-checkbox-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .dynamic-checkbox-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => __( 'Icon', 'hw-ele-woo-dynamic' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'output_format' => [ 'plain', 'flex', 'grid' ],
					],
			]
		);
	
		$this->add_control(
			'icon_position',
			[
				'label'   => esc_html__( 'Icon Position', 'hw-ele-woo-dynamic' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'row'    => [
						'title' => esc_html__( 'Left', 'hw-ele-woo-dynamic' ),
						'icon'  => 'eicon-h-align-left',
					],
					'column' => [
						'title' => esc_html__( 'Top', 'hw-ele-woo-dynamic' ),
						'icon'  => 'eicon-v-align-top',
					],
				],
				'default'   => 'row',
				'selectors' => [
					'{{WRAPPER}} .dynamic-checkbox-item' => 'display: inline-flex; align-items: baseline; flex-direction: {{VALUE}};',
				],
			]
		);

		// Szín választó
		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Primary Color', 'hw-ele-woo-dynamic' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-checkbox-item-icon i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .dynamic-checkbox-item-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);
	
		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => __( 'Size', 'hw-ele-woo-dynamic' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'range'      => [
					'px'  => [ 'min' => 1, 'max' => 200 ],
					'em'  => [ 'min' => 0.1, 'max' => 10 ],
					'rem' => [ 'min' => 0.1, 'max' => 10 ],
				],
				'default'    => [
					'size' => 16,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-checkbox-item-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dynamic-checkbox-item-icon i'   => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_gap_horizontal',
			[
				'label'      => __( 'Icon Horizontal Gap', 'hw-ele-woo-dynamic' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom'],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 100 ],
				],
				'default'    => [ 'size' => 4, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-checkbox-item-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_gap_vertical',
			[
				'label'      => __( 'Icon Vertical Gap', 'hw-ele-woo-dynamic' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 100 ],
				],
				'default'    => [ 'size' => 2, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-checkbox-item-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

	$this->end_controls_section();
}

private static function build_content( string $val, string $tag, array $icon_data, string $icon_pos ): string {

        $html = sprintf(
            '<%1$s class="dynamic-checkbox-item-content">%2$s</%1$s>',
            tag_escape( $tag ),
            esc_html( $val )
        );

        if ( ! empty( $icon_data['value'] ) ) {
            ob_start();
            echo '<span class="dynamic-checkbox-item-icon">';
            \Elementor\Icons_Manager::render_icon( $icon_data, [ 'aria-hidden' => 'true' ] );
            echo '</span>';
            $ico = ob_get_clean();

            return ( 'left' === $icon_pos ) ? $ico . $html : $html . $ico;
        }

        return $html;
    }

/* ----- Render ----- */
protected function render(): void {
		$s     = $this->get_settings_for_display();
		$key   = sanitize_text_field( $s['meta_field_id'] ?? '' );
		if ( ! $key ) {
			echo wp_kses_post( $s['fallback'] ?? '' );
			return;
		}

		$raw   = QuerySource::get_meta( $key, $s['query_source'] );
		$items = hw_only_checked( $raw );

		if ( empty( $items ) ) {
			if ( 'yes' === ( $s['hide_if_empty'] ?? '' ) ) {
				return;
			}
			echo wp_kses_post( $s['fallback'] ?? '' );
			return;
		}

		switch ( $s['order_type'] ?? 'default' ) {
			case 'random':
				shuffle( $items );
				break;
			case 'value_asc':
				sort( $items, SORT_NATURAL | SORT_FLAG_CASE );
				break;
			case 'value_desc':
				rsort( $items, SORT_NATURAL | SORT_FLAG_CASE );
				break;
		}

		include __DIR__ . '/../views/dynamic-checkbox.php';
	}

}