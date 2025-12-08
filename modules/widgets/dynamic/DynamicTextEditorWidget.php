<?php
namespace HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use HelloWP\HWEleWooDynamic\Modules\Widgets\QuerySource;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DynamicTextEditorWidget extends Widget_Base {

	public function get_name(): string {
		return 'dynamic-text-editor';
	}

	public function get_title(): string {
		return __( 'Dynamic Text Editor', 'hw-ele-woo-dynamic' );
	}

	public function get_icon(): string {
		return 'eicon-text-area';
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
            'title'       => esc_html__( 'Need a hand?', 'hw-ele-woo-dynamic' ),
            'description' => esc_html__( 'Visit the documentation for usage tips and dynamic tags examples.', 'hw-ele-woo-dynamic' ),
            'upgrade_url' => esc_url( 'https://lonsdale201.github.io/lonsdale-plugins.github.io/dynamic-ele-ext/widgets/dynamic-text' ),
            'upgrade_text'=> esc_html__( 'Open Github Wiki', 'hw-ele-woo-dynamic' ),
        ];
    }

	protected function register_controls(): void {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'hw-ele-woo-dynamic' ),
			]
		);

		$this->add_control(
			'query_source',
			[
				'label'   => __( 'Query source', 'hw-ele-woo-dynamic' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'listing_grid' => __( 'Listing Grid (loop object)', 'hw-ele-woo-dynamic' ),
					'current_post' => __( 'Current Post', 'hw-ele-woo-dynamic' ),
					'current_user' => __( 'Current User', 'hw-ele-woo-dynamic' ),
				],
				'default' => 'listing_grid',
			]
		);

		$this->add_control(
			'content',
			[
				'label'       => __( 'Content', 'hw-ele-woo-dynamic' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => __( '<p>Use {{meta_key|format|fallback}} macros to pull dynamic values.</p>', 'hw-ele-woo-dynamic' ),
				'description' => __( 'Example: {{event_date|Y.m.d|No date}} or {{price|number:2:,|0}}.', 'hw-ele-woo-dynamic' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label' => __( 'Text', 'hw-ele-woo-dynamic' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .dynamic-text-editor',
			]
		);

		$this->add_responsive_control(
			'text_alignment',
			[
				'label'   => __( 'Alignment', 'hw-ele-woo-dynamic' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [
						'title' => __( 'Left', 'hw-ele-woo-dynamic' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'hw-ele-woo-dynamic' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'hw-ele-woo-dynamic' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'left',
				'toggle'    => false,
				'selectors' => [
					'{{WRAPPER}} .dynamic-text-editor' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'paragraph_spacing',
			[
				'label'      => __( 'Paragraph Spacing', 'hw-ele-woo-dynamic' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px'  => [ 'min' => 0,  'max' => 60 ],
					'em'  => [ 'min' => 0,  'max' => 5 ],
					'rem' => [ 'min' => 0,  'max' => 5 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .dynamic-text-editor p' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Text Color', 'hw-ele-woo-dynamic' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dynamic-text-editor' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$content  = $settings['content'] ?? '';

		if ( '' === trim( (string) $content ) ) {
			return;
		}

		$query_source = $settings['query_source'] ?? 'listing_grid';
		$rendered     = $this->replace_macros( $content, $query_source );

		if ( '' === trim( (string) $rendered ) ) {
			return;
		}

		$template = __DIR__ . '/../views/dynamic-text-editor.php';

		if ( ! file_exists( $template ) ) {
			echo '<p>' . esc_html__( 'Template missing.', 'hw-ele-woo-dynamic' ) . '</p>';
			return;
		}

		$content = $rendered;
		include $template;
	}

	private function replace_macros( string $content, string $query_source ): string {
		$pattern = '/\{\{\s*([^{}]+)\s*\}\}/';

		return (string) preg_replace_callback(
			$pattern,
			function ( array $matches ) use ( $query_source ) {
				$replacement = $this->resolve_macro( $matches[1], $query_source );

				return $replacement;
			},
			$content
		);
	}

	private function resolve_macro( string $raw, string $query_source ): string {
		$parts = array_map( 'trim', explode( '|', $raw ) );

		$key      = $parts[0] ?? '';
		$format   = $parts[1] ?? '';
		$fallback = $parts[2] ?? '';

		if ( '' === $key ) {
			return '';
		}

		$value = QuerySource::get_meta( $key, $query_source );

		if ( $this->is_empty_value( $value ) ) {
			return '' !== $fallback ? wp_kses_post( $fallback ) : '';
		}

		return esc_html( $this->format_value( $value, $format ) );
	}

	/**
	 * @param mixed $value Value from meta.
	 */
	private function is_empty_value( $value ): bool {
		if ( null === $value ) {
			return true;
		}

		if ( is_array( $value ) ) {
			foreach ( $value as $item ) {
				if ( ! $this->is_empty_value( $item ) ) {
					return false;
				}
			}

			return true;
		}

		if ( is_bool( $value ) ) {
			return false;
		}

		if ( is_numeric( $value ) ) {
			return false;
		}

		return '' === trim( (string) $value );
	}

	/**
	 * @param mixed  $value  Raw value.
	 * @param string $format Formatting param.
	 */
	private function format_value( $value, string $format ): string {
		$maybe_array = maybe_unserialize( $value );
		if ( is_array( $maybe_array ) ) {
			$value = $maybe_array;
		}

		if ( is_array( $value ) ) {
			return $this->format_array_value( $value, $format );
		}

		if ( '' === $format ) {
			return $this->stringify_scalar( $value );
		}

		if ( $this->is_number_format( $format ) && is_numeric( $value ) ) {
			return $this->format_number( (float) $value, $format );
		}

		if ( $this->looks_like_date_format( $format ) ) {
			return $this->format_date( $value, $format );
		}

		return $this->stringify_scalar( $value );
	}

	private function format_array_value( array $value, string $format ): string {
		$items = $this->normalize_multi_value( $value );

		if ( empty( $items ) ) {
			return '';
		}

		$delimiter = '' !== $format ? $format : ', ';

		return implode(
			$delimiter,
			array_map(
				static function ( $item ) {
					return (string) $item;
				},
				$items
			)
		);
	}

	private function stringify_scalar( $value ): string {
		if ( is_scalar( $value ) || ( is_object( $value ) && method_exists( $value, '__toString' ) ) ) {
			return (string) $value;
		}

		return '';
	}

	private function is_number_format( string $format ): bool {
		return 1 === preg_match( '/^number\b/i', $format );
	}

	private function format_number( float $value, string $format ): string {
		$defaults = [
			'decimals'          => 0,
			'decimal_separator' => '.',
			'thousand_separator'=> ',',
		];

		$options = array_merge( $defaults, $this->parse_number_format( $format ) );

		return number_format(
			$value,
			$options['decimals'],
			$options['decimal_separator'],
			$options['thousand_separator']
		);
	}

	private function parse_number_format( string $format ): array {
		$payload = '';
		if ( preg_match( '/^number(?::|\()(.*)$/i', trim( $format ), $matches ) ) {
			$payload = trim( $matches[1] );
		}

		$payload = trim( $payload, "): \t\n\r\0\x0B" );

		if ( '' === $payload ) {
			return [];
		}

		$parts = array_map( 'trim', explode( ',', $payload ) );
		$parts = array_map(
			static function ( $part ) {
				return '' === $part ? null : $part;
			},
			$parts
		);

		return [
			'decimals'           => isset( $parts[0] ) ? (int) $parts[0] : 0,
			'decimal_separator'  => $parts[1] ?? '.',
			'thousand_separator' => $parts[2] ?? ',',
		];
	}

	private function looks_like_date_format( string $format ): bool {
		return '' !== $format && 1 === preg_match( '/[dDjlNSwzWFmMntLoYyaAgGhHis]/', $format );
	}

	/**
	 * @param mixed  $value  Original value.
	 * @param string $format Date format.
	 */
	private function format_date( $value, string $format ): string {
		$timestamp = null;

		if ( is_numeric( $value ) ) {
			$timestamp = (int) $value;
		} else {
			$time = strtotime( (string) $value );
			if ( false !== $time ) {
				$timestamp = $time;
			}
		}

		if ( ! $timestamp ) {
			return $this->stringify_scalar( $value );
		}

		return wp_date( $format, $timestamp );
	}

	/**
	 * Normalizes checkbox/multi-select arrays into a flat list of selected labels.
	 *
	 * @param array $value Raw array retrieved from meta.
	 *
	 * @return array
	 */
	private function normalize_multi_value( array $value ): array {
		$checkbox = $this->extract_checkbox_values( $value );

		if ( null !== $checkbox ) {
			return $checkbox;
		}

		$flat = [];
		array_walk_recursive(
			$value,
			static function ( $item ) use ( &$flat ) {
				if ( is_scalar( $item ) || ( is_object( $item ) && method_exists( $item, '__toString' ) ) ) {
					$flat[] = (string) $item;
				}
			}
		);

		$flat = array_filter(
			$flat,
			static function ( $item ) {
				return '' !== trim( (string) $item );
			}
		);

		return array_values( array_unique( $flat ) );
	}

	/**
	 * Attempts to detect checkbox style storage formats (array of truthy flags).
	 *
	 * @param mixed $raw Raw array or serialized representation.
	 *
	 * @return array|null
	 */
	private function extract_checkbox_values( $raw ): ?array {
		if ( is_string( $raw ) ) {
			$maybe = maybe_unserialize( $raw );
			if ( is_array( $maybe ) ) {
				$raw = $maybe;
			}
		}

		if ( ! is_array( $raw ) ) {
			return null;
		}

		$prepared = $raw;

		if ( 1 === count( $prepared ) && isset( $prepared[0] ) && is_array( $prepared[0] ) ) {
			$prepared = $prepared[0];
		} elseif ( array_keys( $prepared ) === range( 0, count( $prepared ) - 1 ) ) {
			$all_nested = array_filter(
				$prepared,
				static function ( $item ) {
					return is_array( $item );
				}
			);

			if ( ! empty( $all_nested ) && count( $all_nested ) === count( $prepared ) ) {
				$prepared = array_merge( ...array_values( $prepared ) );
			}
		}

		if ( ! is_array( $prepared ) ) {
			return null;
		}

		$is_assoc = array_keys( $prepared ) !== range( 0, count( $prepared ) - 1 );

		if ( $is_assoc ) {
			$checked = [];

			foreach ( $prepared as $key => $flag ) {
				if ( $this->is_truthy_checkbox_flag( $flag ) ) {
					$checked[] = (string) $key;
				}
			}

			return $checked;
		}

		$scalars = array_filter(
			$prepared,
			static function ( $item ) {
				return is_scalar( $item ) || ( is_object( $item ) && method_exists( $item, '__toString' ) );
			}
		);

		if ( count( $scalars ) !== count( $prepared ) ) {
			return null;
		}

		return array_values(
			array_filter(
				array_map(
					static function ( $item ) {
						return (string) $item;
					},
					$prepared
				),
				static function ( $item ) {
					return '' !== trim( (string) $item );
				}
			)
		);
	}

	/**
	 * Determines if a checkbox flag should count as "checked".
	 *
	 * @param mixed $flag Raw stored flag.
	 */
	private function is_truthy_checkbox_flag( $flag ): bool {
		if ( is_bool( $flag ) ) {
			return $flag;
		}

		if ( is_numeric( $flag ) ) {
			return abs( (int) $flag ) > 0;
		}

		if ( is_string( $flag ) ) {
			$filtered = filter_var( $flag, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
			if ( null !== $filtered ) {
				return $filtered;
			}

			$flag = strtolower( trim( $flag ) );

			return in_array( $flag, [ 'yes', 'on', 'true', '1' ], true );
		}

		return false;
	}
}
