<?php
/**
 * View: Dynamic Calculations Output
 *
 * @var array   $settings
 * @var mixed[] $values      – values mapped by token from widget->render()
 */

$raw                = trim( $settings['calculation_macros'] ?? '' );
$output_type        = $settings['output_type']        ?? 'plain';
$decimal_separator  = $settings['decimal_separator']  ?? '.';
$thousand_separator = $settings['thousand_separator'] ?? ',';
$decimals_count     = (int) ( $settings['decimals_count'] ?? 2 );

if ( '' === $raw ) {
    return;
}

$values['current_post_date'] = get_post_time( 'U', true );
$values['today']             = time();

if ( ! function_exists( 'evaluate_math_expression' ) ) {
    function evaluate_math_expression( string $expr ): float {
        $tokens = preg_split(
            '/\s*([\+\-\*\/\(\)])\s*/',
            $expr,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );
        $precedence   = [ '+' => 1, '-' => 1, '*' => 2, '/' => 2 ];
        $output_queue = [];
        $op_stack     = [];

        foreach ( $tokens as $token ) {
            if ( is_numeric( $token ) ) {
                $output_queue[] = $token;
            } elseif ( isset( $precedence[ $token ] ) ) {
                while ( $op_stack ) {
                    $top = end( $op_stack );
                    if ( $top === '(' ) {
                        break;
                    }
                    if ( $precedence[ $top ] >= $precedence[ $token ] ) {
                        $output_queue[] = array_pop( $op_stack );
                    } else {
                        break;
                    }
                }
                $op_stack[] = $token;
            } elseif ( $token === '(' ) {
                $op_stack[] = $token;
            } elseif ( $token === ')' ) {
                while ( $op_stack && end( $op_stack ) !== '(' ) {
                    $output_queue[] = array_pop( $op_stack );
                }
                array_pop( $op_stack );
            }
        }
        while ( $op_stack ) {
            $output_queue[] = array_pop( $op_stack );
        }

        $stack = [];
        foreach ( $output_queue as $token ) {
            if ( is_numeric( $token ) ) {
                $stack[] = $token + 0;
            } else {
                $b = array_pop( $stack );
                $a = array_pop( $stack );
                switch ( $token ) {
                    case '+':
                        $stack[] = $a + $b;
                        break;
                    case '-':
                        $stack[] = $a - $b;
                        break;
                    case '*':
                        $stack[] = $a * $b;
                        break;
                    case '/':
                        $stack[] = ( $b != 0 ? $a / $b : 0 );
                        break;
                }
            }
        }

        return $stack[0] ?? 0;
    }
}

if ( ! function_exists( 'dynamic_calc_format_number' ) ) {
    function dynamic_calc_format_number( $number, $settings ) {
        if ( empty( $settings['format_number'] ) || 'yes' !== $settings['format_number'] ) {
            return $number;
        }
        $decimals      = isset( $settings['decimals_count'] ) ? (int) $settings['decimals_count'] : 2;
        $dec_point     = $settings['decimal_separator']  ?? '.';
        $thousands_sep = $settings['thousand_separator'] ?? ',';
        return number_format( (float) $number, $decimals, $dec_point, $thousands_sep );
    }
}

$lines    = preg_split( '/\r?\n/', $raw );
$sections = [];
$current  = null;
foreach ( $lines as $line ) {
    if ( preg_match( '/^\[Operation:\s*(.+?)\]$/', $line, $m ) ) {
        if ( $current ) {
            $sections[] = $current;
        }
        $current = [ 'rawOp' => trim( $m[1] ), 'lines' => [] ];
    } elseif ( $current ) {
        $current['lines'][] = $line;
    }
}
if ( $current ) {
    $sections[] = $current;
}

if ( 'li' === $output_type ) {
    echo '<ul class="dynamic-calculation-list">';
} else {
    echo '<div class="dynamic-calculation-wrapper">';
}

foreach ( $sections as $sec ) {
    $rawOp = $sec['rawOp'];
    $lines = array_filter( $sec['lines'], 'strlen' );
    if ( empty( $lines ) ) {
        continue;
    }

    $operation = 'math';
    $unit      = 'days';
    $p1 = $s1 = $p2 = $s2 = '';
    $sep = ', ';

    if ( preg_match( '/^(\w+)(?:\s+(\w+))?\s*(?:\((.+)\))?$/', $rawOp, $m ) ) {
        $operation = strtolower( $m[1] );
        if ( ! empty( $m[2] ) ) {
            $unit = strtolower( $m[2] );
        }
        if ( ! empty( $m[3] ) ) {
            $inside = $m[3];
            $parts  = preg_split( '/\s*\|\|\s*/', $inside );
            if ( count( $parts ) >= 3 ) {
                list( $before, $sepCand, $after ) = $parts;
                $sep = $sepCand;
                list( $b1, $b2 ) = array_map( 'trim', explode( '|', $before ) + [ '', '' ] );
                list( $a1, $a2 ) = array_map( 'trim', explode( '|', $after  ) + [ '', '' ] );
                $p1 = $b1; $s1 = $b2;
                $p2 = $a1 ?: $p1; $s2 = $a2 ?: $s1;
            } else {
                list( $b1, $b2 ) = array_map( 'trim', explode( '|', $inside ) + [ '', '' ] );
                $p1 = $b1; $s1 = $b2;
                $p2 = $p1; $s2 = $s1;
            }
        }
    }

    preg_match_all( '/\%([^\%]+)\%/', implode( ' ', $lines ), $tok );
    $tokens = $tok[1];
    $nums   = array_map( function( $t ) use ( $values ) {
        $raw = $values[ $t ] ?? '';
        $clean = preg_replace( '/[^\d\.]/', '', (string) $raw );
        return floatval( $clean );
    }, $tokens );

    ob_start();
    switch ( $operation ) {
        case 'since':
        case 'datediff':
            if ( count( $tokens ) === 2 ) {
                $raw1 = $values[ $tokens[0] ] ?? '';
                $raw2 = $values[ $tokens[1] ] ?? '';
                $ts1  = intval( preg_replace( '/[^\d]/', '', (string) $raw1 ) );
                $ts2  = intval( preg_replace( '/[^\d]/', '', (string) $raw2 ) );
                $d1   = new \DateTime( "@{$ts1}" );
                $d2   = new \DateTime( "@{$ts2}" );
            } else {
                $d1 = new \DateTime( '@' . get_post_time( 'U', true ) );
                $d2 = new \DateTime();
            }
            $diff = $d1->diff( $d2 );
            switch ( $unit ) {
                case 'hours':
                    $value = $diff->days * 24 + $diff->h;
                    break;
                case 'minutes':
                    $value = $diff->days * 1440 + $diff->h * 60 + $diff->i;
                    break;
                case 'days':
                default:
                    $value = $diff->days;
                    break;
            }
            echo '<span class="calc-prefix">' . esc_html( $p1 )   . '</span>';
            echo '<span class="calc-value">'  . esc_html( $value ) . '</span>';
            echo '<span class="calc-suffix">' . esc_html( $s1 )   . '</span>';
            break;

        case 'minmax':
        case 'range':
            if ( $nums ) {
                $min = dynamic_calc_format_number( min( $nums ), $settings );
                $max = dynamic_calc_format_number( max( $nums ), $settings );
                echo '<span class="calc-prefix">' . esc_html( $p1 ) . '</span>';
                echo '<span class="calc-value">'  . esc_html( $min ) . '</span>';
                echo '<span class="calc-suffix">' . esc_html( $s1 ) . '</span>';
                if ( '' !== $sep ) {
                    echo '<span class="calc-sep">' . esc_html( $sep ) . '</span>';
                }
                echo '<span class="calc-prefix">' . esc_html( $p2 ) . '</span>';
                echo '<span class="calc-value">'  . esc_html( $max ) . '</span>';
                echo '<span class="calc-suffix">' . esc_html( $s2 ) . '</span>';
            }
            break;

        case 'sum':
            if ( $nums ) {
                $sum = dynamic_calc_format_number( array_sum( $nums ), $settings );
                echo '<span class="calc-prefix">' . esc_html( $p1 ) . '</span>';
                echo '<span class="calc-value">'  . esc_html( $sum ) . '</span>';
                echo '<span class="calc-suffix">' . esc_html( $s1 ) . '</span>';
            }
            break;

        case 'average':
        case 'avg':
            if ( $nums ) {
                $avg = dynamic_calc_format_number( array_sum( $nums ) / count( $nums ), $settings );
                echo '<span class="calc-prefix">' . esc_html( $p1 ) . '</span>';
                echo '<span class="calc-value">'  . esc_html( $avg ) . '</span>';
                echo '<span class="calc-suffix">' . esc_html( $s1 ) . '</span>';
            }
            break;

        case 'math':
        default:
    $expr = implode( ' ', $lines );

    foreach ( $tokens as $t ) {
        $raw   = $values[ $t ] ?? '';
        $clean = preg_replace( '/[^\d\.]/', '', (string) $raw );
        if ( '' === $clean ) {
            $clean = '0';
        }
        $expr = str_replace( "%{$t}%", $clean, $expr );
    }

    if ( preg_match( '/^[0-9\+\-\*\/\(\)\s\.]+$/', $expr ) ) {
        try {
            $res = evaluate_math_expression( $expr );
            $res = dynamic_calc_format_number( $res, $settings );
            echo '<span class="calc-prefix">' . esc_html( $p1 ) . '</span>';
            echo '<span class="calc-value">' . esc_html( $res ) . '</span>';
            echo '<span class="calc-suffix">' . esc_html( $s1 ) . '</span>';
        } catch ( \Throwable $e ) {

        }
    }
    break;
    }
    $item_html = ob_get_clean();

    if ( 'li' === $output_type ) {
        echo '<li class="dynamic-calculation-item">' . wp_kses_post( $item_html ) . '</li>';
    } else {
        echo '<span class="dynamic-calculation-item">' . wp_kses_post( $item_html ) . '</span>';
    }
}

if ( 'li' === $output_type ) {
    echo '</ul>';
} else {
    echo '</div>';
}
