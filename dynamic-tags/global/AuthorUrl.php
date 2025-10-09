<?php

namespace HelloWP\HWEleWooDynamic\GlobalTags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class AuthorUrl extends Data_Tag {

	public function get_name() {
		return 'global-author-url';
	}

	public function get_title() {
		return esc_html__( 'Author URL', 'hw-elementor-woo-dynamic' );
	}

	public function get_group() {
		return 'global-tags';
	}

	public function get_categories() {
		return [ TagsModule::URL_CATEGORY ];
	}

	protected function _register_controls() {
	}

	public function get_value( array $options = [] ) {
		$user_id = 0;

		$queried = get_queried_object();
		if ( $queried instanceof \WP_User ) {
			$user_id = (int) $queried->ID;
		}

		if ( ! $user_id && get_the_ID() ) {
			$user_id = (int) get_post_field( 'post_author', get_the_ID() );
		}

		if ( ! $user_id ) {
			return '';
		}

		$url = get_author_posts_url( $user_id );

		return esc_url( $url );
	}
}
