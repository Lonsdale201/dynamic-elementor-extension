<?php
namespace HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use ElementorPro\Modules\QueryControl\Module as QueryControlModule;

class DynamicAddToCartWidget extends Widget_Base {

    public function get_name(): string {
        return 'dynamic_add_to_cart';
    }

    public function get_title(): string {
        return __( 'Dynamic Bulk Add to Cart', 'hw-ele-woo-dynamic' );
    }

    public function get_icon(): string {
        return 'eicon-cart-light';
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

    public function get_style_depends() {
        return [ 'hw-ele-woo-dynamic-css' ];
    }

    protected function get_upsale_data(): array {
        return [
            'condition'   => true,
            'title'       => esc_html__( 'Oops, stuck huh?', 'hw-ele-woo-dynamic' ),
            'description' => esc_html__( 'No worries! If you’re not sure how to set up the widget or what it’s all about, check out our GitHub page!', 'hw-ele-woo-dynamic' ),
            'upgrade_url' => esc_url( 'https://lonsdale201.github.io/lonsdale-plugins.github.io/dynamic-ele-ext/widgets/dynamic-bulk-add-to-cart' ),
            'upgrade_text'=> esc_html__( 'Open Github Wiki', 'hw-ele-woo-dynamic' ),
        ];
    }

    protected function register_controls(): void {
        // === CONTENT SECTION ===
        $this->start_controls_section(
            'section_content',
            [ 'label' => __( 'Content', 'hw-ele-woo-dynamic' ) ]
        );

        $this->add_control(
            'source',
            [
                'label'   => __( 'Source', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'relation' => __( 'JetEngine relation', 'hw-ele-woo-dynamic' ),
                    'manual'   => __( 'Manual',              'hw-ele-woo-dynamic' ),
                ],
                'default' => 'relation',
            ]
        );

        $this->add_control(
            'relation_id',
            [
                'label'       => __( 'JetEngine Relations ID', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'description' => __( 'Provide the relation identifier (ID) that you will use as the source.', 'hw-ele-woo-dynamic' ),
                'condition'   => [ 'source' => 'relation' ],
            ]
        );

        $this->add_control(
            'redirect',
            [
                'label'   => __( 'Redirect', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'cart'     => __( 'Cart',     'hw-ele-woo-dynamic' ),
                    'checkout' => __( 'Checkout', 'hw-ele-woo-dynamic' ),
                ],
                'default' => 'cart',
            ]
        );

        $this->add_control(
            'button_label',
            [
                'label'   => __( 'Button Label', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::TEXT,
                'default' => __( 'Add to cart', 'hw-ele-woo-dynamic' ),
                'dynamic' => [ 'active' => true ],
            ]
        );

        $this->add_control(
            'icon',
            [
                'label'       => __( 'Icon', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::ICONS,
                'label_block' => true,
                'default'     => [
                    'value'   => 'fas fa-shopping-cart',
                    'library' => 'fa-solid',
                ],
                'description' => __( 'Choose icon', 'hw-ele-woo-dynamic' ),
            ]
        );

        $this->add_control(
            'icon_position',
            [
                'label'   => __( 'Icon Position', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left'  => [ 'title' => __( 'Left',  'hw-ele-woo-dynamic' ), 'icon' => 'eicon-h-align-left' ],
                    'right' => [ 'title' => __( 'Right', 'hw-ele-woo-dynamic' ), 'icon' => 'eicon-h-align-right'],
                ],
                'default' => 'left',
                'toggle'  => false,
            ]
        );

        // Alerts…
        $this->add_control(
            'custom_panel_alert_first',
            [
                'type'      => Controls_Manager::ALERT,
                'alert_type'=> 'info',
                'heading'   => esc_html__( 'Notice!', 'hw-ele-woo-dynamic' ),
                'content'   => esc_html__( 'This widget only supports the Post type and not CCT.', 'hw-ele-woo-dynamic' ),
                'condition' => [ 'source' => 'relation' ],
            ]
        );
        $this->add_control(
            'custom_panel_alert_sec',
            [
                'type'      => Controls_Manager::ALERT,
                'alert_type'=> 'info',
                'heading'   => esc_html__( 'Notice!', 'hw-ele-woo-dynamic' ),
                'content'   => esc_html__( 'Relation child must be WooCommerce Product.', 'hw-ele-woo-dynamic' ),
                'condition' => [ 'source' => 'relation' ],
            ]
        );
        $this->add_control(
            'custom_panel_alert_third',
            [
                'type'      => Controls_Manager::ALERT,
                'alert_type'=> 'warning',
                'heading'   => esc_html__( 'JetEngine Relation ID', 'hw-ele-woo-dynamic' ),
                'content'   => esc_html__( 'Provide the Relation ID from JetEngine.', 'hw-ele-woo-dynamic' ),
                'condition' => [ 'source' => 'relation' ],
            ]
        );

        // Manual repeater
        $repeater = new Repeater();
        $repeater->add_control(
            'product_id',
            [
                'label'        => __( 'Product', 'hw-ele-woo-dynamic' ),
                'type'         => QueryControlModule::QUERY_CONTROL_ID,
                'autocomplete' => [
                    'object'   => QueryControlModule::QUERY_OBJECT_POST,
                    'query'    => [ 'post_type'=>'product','posts_per_page'=>-1 ],
                    'display'  => 'minimal',
                    'by_field' => 'ID',
                ],
                'multiple'=> false,
            ]
        );
        $repeater->add_control(
            'quantity',
            [
                'label'   => __( 'Quantity', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 1,
                'min'     => 1,
                'dynamic' => [ 'active' => true ],
            ]
        );
        $this->add_control(
            'manual_list',
            [
                'label'       => __( 'Manual Products', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [ [ 'product_id'=>'','quantity'=>1 ] ],
                'title_field' => '{{{ product_id }}} x {{{ quantity }}}',
                'condition'   => [ 'source'=>'manual' ],
            ]
        );

        $this->end_controls_section();



        // ——————————————————————
        // STYLE ▸ Button
        // ——————————————————————
        $this->start_controls_section(
            'section_style_button',
            [
                'label' => __( 'Button', 'hw-ele-woo-dynamic' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

          // Wrapper alignment (text-align)
        $this->add_responsive_control(
            'wrapper_alignment',
            [
                'label'   => __( 'Wrapper Position', 'hw' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left'      => [ 'title' => __( 'Left',  'hw' ), 'icon' => 'eicon-h-align-left'   ],
                    'center'    => [ 'title' => __( 'Center','hw' ), 'icon' => 'eicon-h-align-center' ],
                    'right'     => [ 'title' => __( 'Right', 'hw' ), 'icon' => 'eicon-h-align-right'  ],
                    'fullwidth' => [ 'title' => __( 'Full',  'hw' ), 'icon' => 'eicon-h-align-stretch'],
                ],
                'default'      => 'left',

                'prefix_class' => 'hwdw-align-',

                'selectors'    => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'text_wrapper_alignment',
            [
                'label'   => __( 'Text Align (Fullwidth only)', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start'   => [
                        'title' => __( 'Start', 'hw-ele-woo-dynamic' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'       => [
                        'title' => __( 'Center', 'hw-ele-woo-dynamic' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'flex-end'     => [
                        'title' => __( 'End', 'hw-ele-woo-dynamic' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                    'space-between'=> [
                        'title' => __( 'Space Between', 'hw-ele-woo-dynamic' ),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                ],
                'default'   => 'center',
                'condition' => [
                    'wrapper_alignment' => 'fullwidth',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dynamic-bulk-addtocart-content' => 'justify-content: {{VALUE}};',
                ],
            ]
        );


        // Button label typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'button_label_typography',
                'selector' => '{{WRAPPER}} .dynamic-bulk-addtocart .hwdw-button-label',
            ]
        );

        $this->add_responsive_control(
			'button_padding',
			[
				'label'       => __( 'Padding', 'hw-ele-woo-dynamic' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px', '%', 'em', 'rem', 'custom' ],
				'default'     => [
					'top'      => 12,
					'right'    => 24,
					'bottom'   => 12,
					'left'     => 24,
					'unit'     => 'px',
                    'isLinked' => false,
				],
				'selectors'   => [
					'{{WRAPPER}} .dynamic-bulk-addtocart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        // Start Normal / Hover tabs
        $this->start_controls_tabs( 'button_style_tabs' );

        // — Normal tab —
        $this->start_controls_tab(
            'button_style_normal',
            [ 'label' => __( 'Normal', 'hw-ele-woo-dynamic' ) ]
        );

        // Background group control
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'button_background_normal',
                'label'    => __( 'Background', 'hw-ele-woo-dynamic' ),
                'types'    => [ 'classic', 'gradient' ],
                'exclude'  => [ 'image' ],
                'selector' => '{{WRAPPER}} .dynamic-bulk-addtocart',
            ]
        );

        // Box shadow control
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'button_box_shadow_normal',
                'label'    => __( 'Box Shadow', 'hw-ele-woo-dynamic' ),
                'selector' => '{{WRAPPER}} .dynamic-bulk-addtocart',
                'exclude'  => [ 'box_shadow_position' ],
            ]
        );

        // Text color
        $this->add_control(
            'button_text_color',
            [
                'label'     => __( 'Text Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-bulk-addtocart .hwdw-button-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'button_border',
                'label'    => __( 'Border', 'hw-ele-woo-dynamic' ),
                'selector' => '{{WRAPPER}} .dynamic-bulk-addtocart',
                'separator'=> 'before',
            ]
        );

        $this->end_controls_tab();


        // — Hover tab —
        $this->start_controls_tab(
            'button_style_hover',
            [ 'label' => __( 'Hover', 'hw-ele-woo-dynamic' ) ]
        );

        // Background on hover
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'button_background_hover',
                'label'    => __( 'Background (Hover)', 'hw-ele-woo-dynamic' ),
                'types'    => [ 'classic', 'gradient' ],
                'exclude'  => [ 'image', 'video' ],
                'selector' => '{{WRAPPER}} .dynamic-bulk-addtocart:hover',
            ]
        );

        // Box shadow on hover
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'button_box_shadow_hover',
                'label'    => __( 'Box Shadow', 'hw-ele-woo-dynamic' ),
                'selector' => '{{WRAPPER}} .dynamic-bulk-addtocart:hover',
                'exclude'  => [ 'box_shadow_position' ],
            ]
        );

        // Text color on hover
        $this->add_control(
            'button_text_color_hover',
            [
                'label'     => __( 'Text Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-bulk-addtocart:hover .hwdw-button-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'button_border_hover',
                'label'    => __( 'Border', 'hw-ele-woo-dynamic' ),
                'selector' => '{{WRAPPER}} .dynamic-bulk-addtocart:hover',
                'separator'=> 'before',
            ]
        );

        $this->end_controls_tab();



        $this->add_control(
            'button_transition',
            [
                'type'      => Controls_Manager::HIDDEN,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-bulk-addtocart' => 'transition: all .3s ease;',
                ],
            ]
        );

        $this->end_controls_section();



        // ——————————————————————
        // STYLE ▸ Icon
        // ——————————————————————
        $this->start_controls_section(
            'section_style_icon',
            [
                'label' => __( 'Icon', 'hw-ele-woo-dynamic' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // icon color
        $this->add_control(
            'button_icon_color',
            [
                'label'     => __( 'Icon Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-bulk-addtocart .hwdw-icon svg' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .dynamic-bulk-addtocart .hwdw-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        // icon color hover
        $this->add_control(
            'button_icon_color_hover',
            [
                'label'     => __( 'Icon Color Hover', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dynamic-bulk-addtocart:hover .hwdw-icon svg' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .dynamic-bulk-addtocart:hover .hwdw-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        // Ikon méret
        $this->add_responsive_control(
            'icon_size',
            [
                'label'      => __( 'Icon Size', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px','em','rem','custom'],
                'range'      => [
                    'px'=>['min'=>8,'max'=>128],
                    'em'=>['min'=>0.5,'max'=>8],
                    'rem'=>['min'=>0.5,'max'=>8],
                ],
                'default'=>['size'=>16,'unit'=>'px'],
                'selectors'=>[
                    '{{WRAPPER}} .hwdw-icon i'   => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .hwdw-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Ikon-gap
        $this->add_responsive_control(
            'icon_gap',
            [
                'label'      => __( 'Icon gap', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::SLIDER,
                'selectors'  => [
                '{{WRAPPER}} .dynamic-bulk-addtocart-content' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
            );

        $this->end_controls_section();
    }

    protected function render(): void {
        $settings = $this->get_settings_for_display();
        $post_id  = get_the_ID();
        $pairs    = [];

        // — relation esetén —
        if ( 'relation' === $settings['source'] ) {
            $rel_id = trim( $settings['relation_id'] );
            if ( empty( $rel_id ) || ! function_exists('jet_engine') || ! property_exists(jet_engine(),'relations') ) {
                echo '<p>Relation not available.</p>'; return;
            }
            $active   = jet_engine()->relations->get_active_relations();
            if ( empty($active[$rel_id]) ) {
                echo '<p>Relation ID not found.</p>'; return;
            }
            $children = $active[$rel_id]->get_children($post_id);
            if ( empty($children) ) {
                echo '<p>No related products.</p>'; return;
            }
            foreach ( wp_list_pluck($children,'child_object_id') as $id ) {
                $pairs[] = intval($id).':1';
            }
        }

        // — manual esetén —
        if ( 'manual' === $settings['source'] ) {
            foreach ( $settings['manual_list'] as $item ) {
                $pid = intval($item['product_id']);
                $qty = max(1,intval($item['quantity']));
                if ($pid) {
                    $pairs[] = $pid.':'.$qty;
                }
            }
            if ( empty($pairs) ) {
                echo '<p>You need to add product in the list</p>'; return;
            }
        }

        // Betöltjük a view-t
        $template = __DIR__ . '/../views/dynamic-add-to-cart.php';
        if ( file_exists($template) ) {
            include $template;
        }
    }
}
