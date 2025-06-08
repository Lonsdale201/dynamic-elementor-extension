<?php
namespace HelloWP\HWEleWooDynamic\GlobalTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use HelloWP\HWEleWooDynamic\Modules\Widgets\QuerySource;

class DynamicCalculation extends Tag {

	/* ========== META ========= */

	public function get_name()        { return 'dynamic-calculation-global'; }
	public function get_title()       { return __( 'Dynamic Calculation', 'hw-ele-woo-dynamic' ); }
	public function get_group()       { return 'global-tags'; }
	public function get_categories()  { return [ Module::TEXT_CATEGORY ]; }

	/* ========== CONTROLS ========= */

	protected function _register_controls() {

		// ­— képlet típusa
		$this->add_control( 'formula_type', [
			'label'   => __( 'Calculation formula', 'hw-ele-woo-dynamic' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [ 'math' => __( 'Math calc', 'hw-ele-woo-dynamic' ) ],
			'default' => 'math',
		] );

		// ­— lekérdezési forrás
		$this->add_control( 'query_source', [
			'label'   => __( 'Query source', 'hw-ele-woo-dynamic' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'listing_grid' => __( 'Listing Grid', 'hw-ele-woo-dynamic' ),
				'current_post' => __( 'Current Post', 'hw-ele-woo-dynamic' ),
				'current_user' => __( 'Current User', 'hw-ele-woo-dynamic' ),
				'current_term' => __( 'Current Term', 'hw-ele-woo-dynamic' ),
			],
			'default' => 'current_post',
		] );

		// ­— maga a képlet
		$this->add_control( 'calculation_formula', [
			'label'       => __( 'Calculation Formula', 'hw-ele-woo-dynamic' ),
			'type'        => Controls_Manager::TEXTAREA,
			'rows'        => 4,
			'description' => __( 'Use %meta_key% placeholders. Example: (%price1%+%price2%)/%qty%', 'hw-ele-woo-dynamic' ),
			'placeholder' => '(%value1% + %value2%) * 1.23',
		] );
	}

	/* ========== RENDER ========= */

	public function render() {

		$settings = $this->get_settings_for_display();
		$formula  = trim( $settings['calculation_formula'] ?? '' );
		if ( $formula === '' ) {
			return;
		}

		/* ——— 1. Token-helyettesítés ——— */
		preg_match_all( '/%([^%]+)%/', $formula, $m );
		$expr = $formula;

	foreach ( $m[1] as $meta_key ) {
        $raw = QuerySource::get_meta( $meta_key, $settings['query_source'] );
        $sanitized = filter_var( (string) $raw, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND );
        $val       = $sanitized !== '' ? (float) $sanitized : 0.0;
        $expr      = str_replace( "%{$meta_key}%", (string) $val, $expr );
        }


		/* ——— 2. Biztonsági karakterszűrés ——— */
		if (
			'math' !== $settings['formula_type'] ||
			! preg_match( '~^[0-9+\-*/<>!=&|() \t\r\n\.]+$~', $expr )
		) {
			return;
		}

		/* ——— 3. Kiértékelés ——— */
		$result = self::evaluate_expression( $expr );
		echo esc_html( $result );
	}

	/* ========== KIFEJEZÉS-KIÉRTÉKELŐ ========= */

	/**
	 * Infix kifejezés kiértékelése (biztonságos, nincs eval).
	 * Támogatott: + - * / < > <= >= == != && || !  valamint zárójelek.
	 * Minden igaz/hamis eredményt 1/0-ként ad vissza.
	 */
	private static function evaluate_expression( string $expression ): float {

		/* --- 3.1 Tokenizálás --- */
		$tokens = preg_split(
			'~\s*(\|\||&&|==|!=|<=|>=|[+\-*\/<>!()])\s*~',
			$expression,
			-1,
			PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
		);

		if ( $tokens === false ) {
			return 0.0;
		}

		/* --- 3.2 Precedencia táblázat ( nagyobb szám = magasabb ) --- */
		$prec = [
			'!'  => 5,
			'*'  => 4, '/'  => 4,
			'+'  => 3, '-'  => 3,
			'<'  => 2, '>'  => 2, '<=' => 2, '>=' => 2, '==' => 2, '!=' => 2,
			'&&' => 1,
			'||' => 0,
		];

		/* --- 3.3 Shunting-yard: infix ⇒ postfix --- */
		$out = [];
		$ops = [];

		foreach ( $tokens as $tk ) {

			if ( is_numeric( $tk ) ) {                // operandus
				$out[] = $tk;
				continue;
			}

			if ( $tk === '(' ) {
				$ops[] = $tk;
				continue;
			}
			if ( $tk === ')' ) {
				while ( $ops && end( $ops ) !== '(' ) {
					$out[] = array_pop( $ops );
				}
				array_pop( $ops ); // kidobjuk a '('-t
				continue;
			}

			// operátor
			while ( $ops ) {
				$top = end( $ops );
				if ( $top === '(' ) {
					break;
				}
				if ( $prec[ $top ] >= $prec[ $tk ] ) {
					$out[] = array_pop( $ops );
				} else {
					break;
				}
			}
			$ops[] = $tk;
		}
		while ( $ops ) { $out[] = array_pop( $ops ); }

		/* --- 3.4 Postfix értékelés --- */
		$st = [];
		foreach ( $out as $tk ) {

			if ( is_numeric( $tk ) ) {
				$st[] = (float) $tk;
				continue;
			}

			/* — unáris ! — */
			if ( $tk === '!' ) {
				$b = array_pop( $st );
				$st[] = $b ? 0 : 1;
				continue;
			}

			/* — bináris — */
			$b = array_pop( $st );
			$a = array_pop( $st );

			switch ( $tk ) {
				case '+':  $st[] = $a + $b;               break;
				case '-':  $st[] = $a - $b;               break;
				case '*':  $st[] = $a * $b;               break;
				case '/':  $st[] = $b != 0 ? $a / $b : 0; break;

				case '<':  $st[] = ( $a  <  $b ) ? 1 : 0; break;
				case '>':  $st[] = ( $a  >  $b ) ? 1 : 0; break;
				case '<=': $st[] = ( $a <=  $b ) ? 1 : 0; break;
				case '>=': $st[] = ( $a >=  $b ) ? 1 : 0; break;
				case '==': $st[] = ( $a ==  $b ) ? 1 : 0; break;
				case '!=': $st[] = ( $a !=  $b ) ? 1 : 0; break;

				case '&&': $st[] = ( $a && $b ) ? 1 : 0;  break;
				case '||': $st[] = ( $a || $b ) ? 1 : 0;  break;
			}
		}

		return $st[0] ?? 0;
	}
}
