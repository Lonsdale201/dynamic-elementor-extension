<?php
use Elementor\Icons_Manager;
use HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic\DynamicCheckboxWidget as CB;

/**
 * @var array $s     – widget settings (passed from the widget)
 * @var array $items – hw_only_checked() 
 */

/* -------------------------------------------------
 * ▸ Prep
 * ------------------------------------------------- */
$allowed_formats = [ 'plain', 'li_bullet', 'li_order', 'flex', 'grid' ];
$fmt             = in_array( $s['output_format'] ?? '', $allowed_formats, true ) ? $s['output_format'] : 'li_bullet';

$allowed_tags = [ 'div', 'span', 'p' ];
$tag          = in_array( $s['html_tag'] ?? '', $allowed_tags, true ) ? $s['html_tag'] : 'div';

$icon_data = is_array( $s['icon'] ?? [] ) ? $s['icon'] : [];
$icon_pos  = in_array( $s['icon_position'] ?? '', [ 'left', 'right' ], true ) ? $s['icon_position'] : 'left';

$delim = sanitize_text_field( $s['plain_delimiter'] ?? ', ' );

$allowed          = wp_kses_allowed_html( 'post' );
$allowed['svg']   = [ 'class' => true, 'xmlns' => true, 'viewbox' => true, 'width' => true, 'height' => true, 'fill' => true ];
$allowed['path']  = [ 'd' => true, 'fill' => true, 'fill-rule' => true ];

$make = [ CB::class, 'build_content' ];

/* -------------------------------------------------
 * ▸ 1) PLAINTEXT 
 * ------------------------------------------------- */
if ( 'plain' === $fmt ) {
	echo '<div class="dynamic-checkbox-wrapper dynamic-checkbox-wrapper--plain">';
	$count = count( $items );
	foreach ( $items as $i => $val ) {
		echo '<div class="dynamic-checkbox-item">';
			echo wp_kses( $make( $val, $tag, $icon_data, $icon_pos ), $allowed );
			if ( $i < $count - 1 ) {
				echo esc_html( $delim );
			}
		echo '</div>';
	}
	echo '</div>';
	return;
}

/* -------------------------------------------------
 * ▸ 2) UL / OL  
 * ------------------------------------------------- */
if ( in_array( $fmt, [ 'li_bullet', 'li_order' ], true ) ) {
	$list = ( 'li_bullet' === $fmt ) ? 'ul' : 'ol';
	printf(
		'<%1$s class="dynamic-checkbox-list dynamic-checkbox-list--%2$s">',
		esc_html( $list ),
		esc_attr( $fmt )
	);
	foreach ( $items as $val ) {
		echo '<li class="dynamic-checkbox-item">';
			printf(
				'<%1$s class="dynamic-checkbox-item-content">%2$s</%1$s>',
				tag_escape( $tag ),
				esc_html( $val )
			);
		echo '</li>';
	}
	printf( '</%s>', esc_html( $list ) );
	return;
}

/* -------------------------------------------------
 * ▸ 3) FLEX / GRID  + makrók
 * ------------------------------------------------- */
echo '<div class="dynamic-checkbox-wrapper dynamic-checkbox-wrapper--' . esc_attr( $fmt ) . '">';

$total = count( $items );

foreach ( $items as $i => $val ) {
	$idx      = $i + 1;
	$slug     = sanitize_title( $val );
	$rendered = '';

	if ( 'yes' === ( $s['advanced_output'] ?? '' ) && ! empty( $s['macro_rules'] ) ) {
		$rules = [];
		foreach ( preg_split( '/\r?\n/', trim( $s['macro_rules'] ) ) as $line ) {
			$line = trim( $line );
			if ( $line && preg_match( '/(\\%[^\\%]+\\%)/', $line, $m ) ) {
				$rules[ strtolower( $m[1] ) ] = $line;
			}
		}
		foreach ( $rules as $macro => $tpl ) {
			$hit = match ( $macro ) {
				'%even%'  => 0 === $idx % 2,
				'%odd%'   => 1 === $idx % 2,
				'%first%' => 1 === $idx,
				'%last%'  => $idx === $total,
				default   => false,
			};
			if ( ! $hit && preg_match( '/^\\%(\\d+)\\%$/',    $macro, $m ) ) { $hit = $idx === (int) $m[1]; }
			if ( ! $hit && preg_match( '/^\\%(\\d+)n\\%$/',   $macro, $m ) ) { $n = (int) $m[1]; $hit = ( $n > 0 && 0 === $idx % $n ); }
			if ( ! $hit && preg_match( '/^\\%value_([^\\%]+)\\%$/', $macro, $m ) ) { $hit = $slug === sanitize_title( $m[1] ); }

			if ( $hit ) {
				$inner = $make( $val, $tag, $icon_data, $icon_pos );
				$html  = ( false !== stripos( $tpl, $macro ) )
					? str_ireplace( $macro, $inner, $tpl )
					: $tpl . $inner . hw_auto_close( $tpl );

				$rendered = '<div class="dynamic-checkbox-item">' . $html . '</div>';
				break;
			}
		}
	}

	if ( $rendered ) {
		echo wp_kses( $rendered, $allowed );
	} else {
		echo '<div class="dynamic-checkbox-item">';
			echo wp_kses( $make( $val, $tag, $icon_data, $icon_pos ), $allowed );
		echo '</div>';
	}
}

echo '</div>';
