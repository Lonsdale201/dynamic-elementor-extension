<?php

namespace HelloWP\HWEleWooDynamic\GlobalTags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class PostContent extends Data_Tag {

	public function get_name() {
		return 'global-post-content';
	}

	public function get_title() {
		return esc_html__( 'Post Content', 'hw-elementor-woo-dynamic' );
	}

	public function get_group() {
		return 'global-tags';
	}

	public function get_categories() {
		return [ TagsModule::TEXT_CATEGORY ];
	}

	protected function _register_controls() {
		$this->add_control(
			'filter_raw',
			[
				'label'        => esc_html__( 'Filter raw content', 'hw-elementor-woo-dynamic' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'hw-elementor-woo-dynamic' ),
				'label_off'    => esc_html__( 'No', 'hw-elementor-woo-dynamic' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);


		$this->add_control(
			'trim_output',
			[
				'label'        => esc_html__( 'Trim output', 'hw-elementor-woo-dynamic' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'hw-elementor-woo-dynamic' ),
				'label_off'    => esc_html__( 'No', 'hw-elementor-woo-dynamic' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'trim_length',
			[
				'label'     => esc_html__( 'Trim length (characters)', 'hw-elementor-woo-dynamic' ),
				'type'      => Controls_Manager::NUMBER,
				'condition' => [ 'trim_output' => 'yes' ],
				'placeholder' => 150,
				'default'   => 150,
				'min'       => 1,
			]
		);

		$this->add_control(
			'trim_suffix',
			[
				'label'     => esc_html__( 'Load more text', 'hw-elementor-woo-dynamic' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => [ 'trim_output' => 'yes' ],
				'default'   => '...',
			]
		);
	}

	public function get_value( array $options = [] ) {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			global $post;
			if ( isset( $post->ID ) ) {
				$post_id = (int) $post->ID;
			}
		}

		if ( ! $post_id ) {
			return '';
		}

		$post = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}

		$content = apply_filters( 'the_content', $post->post_content );

		$filter_raw = 'yes' === $this->get_settings( 'filter_raw' );
		if ( $filter_raw ) {
			$content = wp_strip_all_tags( $content );
		}

		$trim_requested = 'yes' === $this->get_settings( 'trim_output' );
		if ( $trim_requested ) {
			$length = (int) $this->get_settings( 'trim_length' );
			$suffix = (string) $this->get_settings( 'trim_suffix' );
			$suffix = $suffix !== '' ? $suffix : '...';

			$plain_text = $filter_raw ? $content : wp_strip_all_tags( $content );
			$use_mb = function_exists( 'mb_strlen' ) && function_exists( 'mb_substr' );
			$text_length = $use_mb ? mb_strlen( $plain_text, 'UTF-8' ) : strlen( $plain_text );

			if ( $length > 0 && $text_length > $length ) {
				$excerpt = $use_mb
					? mb_substr( $plain_text, 0, $length, 'UTF-8' )
					: substr( $plain_text, 0, $length );
				$content = $excerpt . $suffix;
			} else {
				$content = $plain_text;
			}
		}

		return $content;
	}

}
