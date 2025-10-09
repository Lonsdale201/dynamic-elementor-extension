<?php
namespace HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;
use HelloWP\HWEleWooDynamic\Modules\Widgets\QuerySource;

class MemberShipCardWidget extends Widget_Base {

    public function get_name() {
        return 'membership_cards';
    }

    public function get_title() {
        return __( 'Membership Cards', 'hw-ele-woo-dynamic' );
    }

    public function get_icon() {
        return 'eicon-call-to-action';
    }

    public function get_categories() {
        return [ 'dynamic-elements' ];
    }

    public function has_widget_inner_wrapper(): bool { return false; }
	public function is_dynamic_content(): bool       { return true; }

    public function get_style_depends() {
        return [ 'hw-ele-woo-dynamic-css' ];
    }

    protected function get_upsale_data(): array {
		return [
            'condition'   => true,
			'title' => esc_html__( 'Oops, stuck huh?', 'hw-ele-woo-dynamic' ),
			'description' => esc_html__( 'No worries! If you’re not sure how to set up the widget or what it’s all about, check out our GitHub page!', 'hw-ele-woo-dynamic' ),
			'upgrade_url' => esc_url( 'https://lonsdale201.github.io/lonsdale-plugins.github.io/dynamic-ele-ext/widgets/membership-cards' ),
			'upgrade_text' => esc_html__( 'Open Github Wiki', 'hw-ele-woo-dynamic' ),
		];
	}

    protected function register_controls() {
        // === CARD TAB ===
        $this->start_controls_section(
            'section_card',
            [
                'label' => __( 'Card', 'hw-ele-woo-dynamic' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        // 2) Megjelenítendő mezők
        $this->add_control(
            'display_fields',
            [
                'label'       => __( 'Fields to display', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::SELECT2,
                'options'     => [
                    'plan_name'    => __( 'Plan Name', 'hw-ele-woo-dynamic' ),
                    'expiry_date'  => __( 'Expiry Date', 'hw-ele-woo-dynamic' ),
                    'status'       => __( 'Status', 'hw-ele-woo-dynamic' ),
                    'member_since' => __( 'Member Since', 'hw-ele-woo-dynamic' ),
                ],
                'multiple'    => true,
                'label_block' => true,
                'description' => __( 'Choose which type of details want to show in your card', 'hw-ele-woo-dynamic' ),
            ]
        );

        $this->add_control(
            'linkable_card',
            [
                'label'        => __( 'Linkable Card', 'hw-ele-woo-dynamic' ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'no',
                'description'  => __( 'When enabled, clicking the card will take the user to the membership details page.', 'hw-ele-woo-dynamic' ),
            ]
        );

        $this->add_responsive_control(
            'column_number',
            [
                'label'             => __( 'Column Number', 'hw-ele-woo-dynamic' ),
                'type'              => Controls_Manager::NUMBER,
                'min'               => 1,
                'max'               => 6,
                'step'              => 1,
                'default'           => 3,
                'tablet_default'    => 2,
                'mobile_default'    => 1,
                'description'       => __( 'In how many columns should the cards appear?', 'hw-ele-woo-dynamic' ),
                'selectors'         => [
                    '{{WRAPPER}} .membership-cards-wrapper' => 'display: grid; grid-template-columns: repeat({{VALUE}}, 1fr); gap: 20px;',
                ],
            ]
        );

        	/* ikon választó */
		$this->add_control(
			'card_icon',
			[
				'label'   => __( 'Icon', 'hw-ele-woo-dynamic' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'    => '',
					'library'  => 'fa-solid',
				],
			]
		);

        $this->end_controls_section();

        // === QUERY TAB ===
        $this->start_controls_section(
            'section_query',
            [
                'label' => __( 'Query', 'hw-ele-woo-dynamic' ),
            ]
        );

        // 1) Státusz‐szűrő
        $statuses = QuerySource::get_membership_statuses();
        $this->add_control(
            'filter_statuses',
            [
                'label'       => __( 'Filter by Membership Statuses', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::SELECT2,
                'options'     => $statuses,
                'multiple'    => true,
                'label_block' => true,
                'description' => __( 'Choose which status cards to display. If not selected, all.', 'hw-ele-woo-dynamic' ),
            ]
        );

        $this->end_controls_section();

        // === PLAN NAME SETTINGS (conditional) ===
        $this->start_controls_section(
            'section_plan_name',
            [
                'label'     => __( 'Plan Name Settings', 'hw-ele-woo-dynamic' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'display_fields' => 'plan_name',
                ],
            ]
        );
        $this->add_control(
            'plan_name_prefix',
            [
                'label'       => __( 'Name Prefix', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [ 'active' => true ],
                'placeholder' => __( 'Prefix before plan name', 'hw-ele-woo-dynamic' ),
            ]
        );
        $this->add_control(
            'plan_name_suffix',
            [
                'label'       => __( 'Name Suffix', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [ 'active' => true ],
                'placeholder' => __( 'Suffix after the name plan', 'hw-ele-woo-dynamic' ),
            ]
        );
        $this->add_control(
            'plan_name_description',
            [
                'label'       => __( 'Description under Plan Name', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::WYSIWYG,
                'dynamic'     => [ 'active' => true ],
                'description' => __( 'Optional text under the plan name.', 'hw-ele-woo-dynamic' ),
            ]
        );
        $this->end_controls_section();

        // === EXPIRY DATE SETTINGS (conditional) ===
        $this->start_controls_section(
            'section_expiry_date',
            [
                'label'     => __( 'Expiry Date Settings', 'hw-ele-woo-dynamic' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'display_fields' => 'expiry_date',
                ],
            ]
        );
        $this->add_control(
            'expiry_prefix',
            [
                'label'       => __( 'Expiry Prefix', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [ 'active' => true ],
                'placeholder' => __( 'Prefix before the date', 'hw-ele-woo-dynamic' ),
            ]
        );
        if ( Dependencies::is_subscriptions_active() ) {
            $this->add_control(
                'next_bill_prefix',
                [
                    'label'       => __( 'Next Bill Prefix', 'hw-ele-woo-dynamic' ),
                    'type'        => Controls_Manager::TEXT,
                    'dynamic'     => [ 'active' => true ],
                    'placeholder' => __( 'Advance to the next renewal date', 'hw-ele-woo-dynamic' ),
                ]
            );
        }
        $this->add_control(
            'expiry_suffix',
            [
                'label'       => __( 'Expiry Suffix', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [ 'active' => true ],
                'placeholder' => __( 'Suffix after the date', 'hw-ele-woo-dynamic' ),
            ]
        );
        $this->add_control(
            'expiry_date_format',
            [
                'label'       => __( 'Date Format', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => get_option( 'date_format' ),
                'description' => __( 'WP format compatible with local settings (e.g. Y-m-d).', 'hw-ele-woo-dynamic' ),
            ]
        );
        $this->add_control(
            'unlimited_text',
            [
                'label'       => __( 'Unlimited Text', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [ 'active' => true ],
                'placeholder' => __( 'Example.: "Unlimited"', 'hw-ele-woo-dynamic' ),
            ]
        );
        $this->add_control(
            'hide_if_unlimited',
            [
                'label'        => __( 'Hide if Unlimited', 'hw-ele-woo-dynamic' ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
                'description'  => __( 'If there is no date and it is on, hide it.', 'hw-ele-woo-dynamic' ),
            ]
        );

        $this->end_controls_section();

        // === STATUS SETTINGS (conditional) ===
        $this->start_controls_section(
            'section_status',
            [
                'label'     => __( 'Status Settings', 'hw-ele-woo-dynamic' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'display_fields' => 'status',
                ],
            ]
        );
        $this->add_control(
            'status_prefix',
            [
                'label'       => __( 'Status Prefix', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [ 'active' => true ],
                'placeholder' => __( 'Text before the status', 'hw-ele-woo-dynamic' ),
            ]
        );
        $this->add_control(
			'status_as_badge',
			[
				'label' => esc_html__( 'Show status as badge', 'hw-ele-woo-dynamic' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'hw-ele-woo-dynamic' ),
				'label_off' => esc_html__( 'Off', 'hw-ele-woo-dynamic' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
        $this->end_controls_section();

        // === MEMBER SINCE SETTINGS (conditional) ===
        $this->start_controls_section(
            'section_member_since',
            [
                'label'     => __( 'Member Since Settings', 'hw-ele-woo-dynamic' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'display_fields' => 'member_since',
                ],
            ]
        );
        $this->add_control(
            'member_since_prefix',
            [
                'label'       => __( 'Prefix', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [ 'active' => true ],
                'placeholder' => __( 'Text before the date', 'hw-ele-woo-dynamic' ),
            ]
        );
        $this->add_control(
            'member_since_suffix',
            [
                'label'       => __( 'Suffix', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [ 'active' => true ],
                'placeholder' => __( 'Text after the date', 'hw-ele-woo-dynamic' ),
            ]
        );
        $this->add_control(
            'member_since_date_format',
            [
                'label'       => __( 'Date Format', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => get_option( 'date_format' ),
                'description' => __( 'Format compatible with WP local settings.', 'hw-ele-woo-dynamic' ),
            ]
        );
        $this->end_controls_section();
         // === STYLE ▸ Card =====
         $this->start_controls_section(
            'section_style_card',
            [
                'label' => __( 'Card', 'hw-ele-woo-dynamic' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // Háttérszín
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'card_background',
                'label'    => __( 'Card Background', 'hw-ele-woo-dynamic' ),
                'types'    => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .membership-card',
            ]
        );


        $this->add_responsive_control(
            'card_padding',
            [
                'label'      => __( 'Padding', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%', 'custom' ],
                'selectors'  => [
                    '{{WRAPPER}} .membership-card' =>
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
            ]
        );

        // Tartalmak közti távolság
        $this->add_responsive_control(
            'card_card_gap',
            [
                'label'      => __( 'Card Gap', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
                'default'    => [ 'size' => 20, 'unit' => 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} .membership-cards-wrapper' =>
                        'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_content_gap',
            [
                'label'      => __( 'Content Gap', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
                'default'    => [ 'size' => 5, 'unit' => 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} .membership-card-body' =>
                        'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'card_border',
                'selector' => '{{WRAPPER}} .membership-card',
            ]
        );

        $this->add_responsive_control(
            'card_border_radius',
            [
                'label'      => __( 'Border Radius', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'default'    => [
                    'top'    => '0',
                    'right'  => '0',
                    'bottom' => '0',
                    'left'   => '0',
                    'unit'   => 'px',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .membership-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        

        $this->end_controls_section();

        /* ===== STYLE ▸ Icon ===== */
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => __( 'Icon', 'hw-ele-woo-dynamic' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
            'icon_vertical_align',
            [
                'label'   => __( 'Icon Vertical Alignment', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __( 'Top',    'hw-ele-woo-dynamic' ),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'center'     => [
                        'title' => __( 'Center', 'hw-ele-woo-dynamic' ),
                        'icon'  => 'eicon-v-align-middle',
                    ],
                    'flex-end'   => [
                        'title' => __( 'Bottom','hw-ele-woo-dynamic' ),
                        'icon'  => 'eicon-v-align-bottom',
                    ],
                ],
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .membership-card' => 'align-items: {{VALUE}};',
                ],
            ]
        );
        

		/* Méret az ikonra */
		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => __( 'Icon Size', 'hw-ele-woo-dynamic' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 8, 'max' => 128 ] ],
				'default'    => [ 'size' => 24, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .membership-card-icon i'   => 'font-size:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .membership-card-icon svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				],
			]
		);

		/* Ikon-box (konténer) méret */
		$this->add_responsive_control(
			'icon_box_size',
			[
				'label'      => __( 'Icon Box Size', 'hw-ele-woo-dynamic' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 24, 'max' => 200 ] ],
				'default'    => [ 'size' => 48, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .membership-card-icon' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};min-width:{{SIZE}}{{UNIT}};display:flex;align-items:center;justify-content:center;',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __( 'Icon Color', 'hw-ele-woo-dynamic' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .membership-card-icon i'   => 'color:{{VALUE}};',
					'{{WRAPPER}} .membership-card-icon svg' => 'fill:{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg',
			[
				'label'     => __( 'Icon Background', 'hw-ele-woo-dynamic' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .membership-card-icon' => 'background-color:{{VALUE}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'icon_border',
                'selector' => '{{WRAPPER}} .membership-card-icon',
            ]
        );

        $this->add_responsive_control(
            'icon_border_radius',
            [
                'label'      => __( 'Border Radius', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'default'    => [
                    'top'    => '0',
                    'right'  => '0',
                    'bottom' => '0',
                    'left'   => '0',
                    'unit'   => 'px',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .membership-card-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->end_controls_section(); 

         // === STYLE ▸ Plan Name =====
        $this->start_controls_section(
            'section_style_plan',
            [
                'label'     => __( 'Plan Name', 'hw-ele-woo-dynamic' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [ 'display_fields' => 'plan_name' ],
            ]
        );
        // plan name
        $this->add_control(
            'plan_style_heading',
            [
                'label' => __( 'Plan Name Style', 'hw-ele-woo-dynamic' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before', 
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'plan_name_typography',
                'selector' => '{{WRAPPER}} .membership-card-body .plan-name',
            ]
        );
        $this->add_control(
            'plan_name_color',
            [
                'label'     => __( 'Name Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .membership-card-body .plan-name' =>
                        'color: {{VALUE}};'
                ],
            ]
        );
        $this->add_responsive_control(
            'plan_name_margin',
            [
                'label'      => __( 'Content spacing', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
                'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
                'selectors'  => [
                    '{{WRAPPER}} .membership-card-body .membership-plan-name' =>
                        'margin-bottom: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        // plan desc
        $this->add_control(
        'plan_desc_style_heading',
            [
                'label' => __( 'Plan Desciprtion Style', 'hw-ele-woo-dynamic' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before', 
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'plan_desc_name_typography',
                'selector' => '{{WRAPPER}} .membership-card-body .plan-name-description p',
            ]
        );

        $this->add_control(
            'plan_desc_color',
            [
                'label'     => __( 'Desciprtion Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .membership-card-body .plan-name-description p' =>
                        'color: {{VALUE}};'
                ],
            ]
        );

        //prefix
        $this->add_control(
            'plan_prefix_style_heading',
            [
                'label' => __( 'Prefix Style', 'hw-ele-woo-dynamic' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before', 
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'plan_name_prefix_typography',
                'selector' => '{{WRAPPER}} .membership-card-body .plan-name-prefix',
            ]
        );

        $this->add_control(
            'plan_name_prefix_color',
            [
                'label'     => __( 'Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .membership-card-body .plan-name-prefix' =>
                        'color: {{VALUE}};'
                ],
            ]
        );

        //shuffix
        $this->add_control(
        'plan_suffix_style_heading',
        [
            'label' => __( 'Suffix Style', 'hw-ele-woo-dynamic' ),
            'type'  => Controls_Manager::HEADING,
            'separator' => 'before', 
        ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'plan_name_suffix_typography',
                'selector' => '{{WRAPPER}} .membership-card-body .plan-name-suffix',
            ]
        );

        $this->add_control(
            'plan_name_suffix_color',
            [
                'label'     => __( 'Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .membership-card-body .plan-name-suffix' =>
                        'color: {{VALUE}};'
                ],
            ]
        );
        $this->end_controls_section(); 

        // === STYLE ▸ Expiry  =====
        $this->start_controls_section(

            'section_style_expiry',
            [
                'label'     => __( 'Expiry Date', 'hw-ele-woo-dynamic' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [ 'display_fields' => 'expiry_date' ],
            ]
        );

        $this->add_control(
            'plan_expiry_style_heading',
            [
                'label' => __( 'Expiry Date Style', 'hw-ele-woo-dynamic' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before', 
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'plan_expiry_typography',
                'selector' => '{{WRAPPER}} .membership-card-body .expiry-date',
            ]
        );

        $this->add_control(
            'plan_expiry_color',
            [
                'label'     => __( 'Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .membership-card-body .expiry-date' =>
                        'color: {{VALUE}};'
                ],
            ]
        );

        // prefix exp date styles
        $this->add_control(
            'plan_expiry_prefix_style_heading',
            [
                'label' => __( 'Prefix Expiry Style', 'hw-ele-woo-dynamic' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before', 
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'plan_expiry_prefix_typography',
                'selector' => '{{WRAPPER}} .membership-card-body .expiry-prefix',
            ]
        );

        $this->add_control(
            'plan_expiry_prefix_color',
            [
                'label'     => __( 'Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .membership-card-body .expiry-prefix' =>
                        'color: {{VALUE}};'
                ],
            ]
        );

        // next bill
        if ( Dependencies::is_subscriptions_active() ) {
            $this->add_control(
            'plan_next_bill_style_heading',
            [
                'label' => __( 'Next Bill Style', 'hw-ele-woo-dynamic' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before', 
            ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name'     => 'plan_next_bill_typography',
                    'selector' => '{{WRAPPER}} .membership-card-body .next-bill-prefix',
                ]
            );

            $this->add_control(
                'plan_next_bill_color',
                [
                    'label'     => __( 'Color', 'hw-ele-woo-dynamic' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .membership-card-body .next-bill-prefix' =>
                            'color: {{VALUE}};'
                    ],
                ]
            );
        }

        // suffix

        $this->add_control(
            'plan_expiry_suffix_style_heading',
            [
                'label' => __( 'Suffix Expiry Style', 'hw-ele-woo-dynamic' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before', 
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'plan_expiry_suffix_typography',
                'selector' => '{{WRAPPER}} .membership-card-body .expiry-suffix',
            ]
        );

        $this->add_control(
            'plan_expiry_suffix_color',
            [
                'label'     => __( 'Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .membership-card-body .expiry-suffix' =>
                        'color: {{VALUE}};'
                ],
            ]
        );

        // unlimited text

        $this->add_control(
            'plan_unlimited_style_heading',
            [
                'label' => __( 'Unlimited text Style', 'hw-ele-woo-dynamic' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before', 
            ]
        );
        
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'plan_unlimited_typography',
                'selector' => '{{WRAPPER}} .membership-card-body .expiry-unlimited',
            ]
        );

        $this->add_control(
            'plan_unlimited_color',
            [
                'label'     => __( 'Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .membership-card-body .expiry-unlimited' =>
                        'color: {{VALUE}};'
                ],
            ]
        );

        $this->end_controls_section();

        // === STYLE ▸ Status  =====
        $this->start_controls_section(

        'section_style_status',
        [
            'label'     => __( 'Status', 'hw-ele-woo-dynamic' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'display_fields' => 'status' ],
        ]
        );

        $this->add_control(
            'plan_status_badge_style_heading',
            [
                'label' => __( 'Status badge', 'hw-ele-woo-dynamic' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before', 
                'condition'  => [
                    'status_as_badge' => 'yes',
                ],
            ]
        );

        // Badge top offset
        $this->add_responsive_control(
            'status_badge_top',
            [
              'label'      => __( 'Badge Top Offset', 'hw-ele-woo-dynamic' ),
              'type'       => Controls_Manager::SLIDER,
              'size_units' => [ 'px', '%', 'custom' ],
              'range'      => [ 'px' => [ 'min' => -100, 'max' => 200 ] ],
              'default'    => [ 'size' => 0, 'unit' => 'px' ],
              'selectors'  => [
                '{{WRAPPER}} .membership-status-badge' => 'top: {{SIZE}}{{UNIT}};',
              ],
              'condition'  => [
                'status_as_badge' => 'yes',
            ],
            ]
          );
          
          $this->add_responsive_control(
            'status_badge_left',
            [
              'label'      => __( 'Badge Right Offset', 'hw-ele-woo-dynamic' ),
              'type'       => Controls_Manager::SLIDER,
              'size_units' => [ 'px', '%', 'custom' ],
              'range'      => [ 'px' => [ 'min' => -100, 'max' => 200 ] ],
              'default'    => [ 'size' => 0, 'unit' => 'px' ],
              'selectors'  => [
                '{{WRAPPER}} .membership-status-badge' => 'right: {{SIZE}}{{UNIT}};',
              ],
              'condition'  => [
                'status_as_badge' => 'yes',
            ],
            ]
          );

          $this->add_responsive_control(
            'status_badge_padding',
            [
                'label'      => __( 'Padding', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%', 'custom' ],
                'selectors'  => [
                    '{{WRAPPER}} .membership-status-badge' =>
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
                'condition'  => [
                    'status_as_badge' => 'yes',
                ],
            ]
        );

          $this->add_control(
            'plan_status_style_heading',
            [
                'label' => __( 'Status Style', 'hw-ele-woo-dynamic' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before', 
                'condition'  => [
                    'status_as_badge' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'plan_status_typography',
                'selector' => '{{WRAPPER}} .membership-card .status-text',
            ]
        );

        $this->add_control(
            'plan_status_background_color',
            [
                'label'     => __( 'Background Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .membership-card .membership-status' =>
                        'background-color: {{VALUE}};'
                ],
            ]
        ); 

        $this->add_control(
            'plan_status_text_color',
            [
                'label'     => __( 'Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .membership-card .status-text' =>
                        'color: {{VALUE}};'
                ],
            ]
        ); 

        $this->add_control(
            'plan_status_prefix_style_heading',
            [
                'label' => __( 'Prefix Style', 'hw-ele-woo-dynamic' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before', 
            ]
        );

        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'plan_status_prefix_typography',
                'selector' => '{{WRAPPER}} .membership-card .status-prefix',
            ]
        );

        $this->add_control(
            'plan_status_prefix_text_color',
            [
                'label'     => __( 'Color', 'hw-ele-woo-dynamic' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .membership-card .status-prefix' =>
                        'color: {{VALUE}};'
                ],
            ]
        ); 

        $this->add_responsive_control(
            'plan_status_gap',
            [
                'label'      => __( 'Prefix Gap', 'hw-ele-woo-dynamic' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
                'default'    => [ 'size' => 6, 'unit' => 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} .membership-status' =>
                        'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

    $this->end_controls_section();

    // === STYLE ▸ member since  =====
    $this->start_controls_section(

        'section_style_member_since',
        [
            'label'     => __( 'Member Since', 'hw-ele-woo-dynamic' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'display_fields' => 'status' ],
        ]
    );

    $this->add_control(
        'plan_member_since_style_heading',
        [
            'label' => __( 'Mmber Since Style', 'hw-ele-woo-dynamic' ),
            'type'  => Controls_Manager::HEADING,
            'separator' => 'before', 
        ]
    );

    $this->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name'     => 'plan_member_since_typography',
            'selector' => '{{WRAPPER}} .membership-card .member-since-date',
        ]
    );

    $this->add_control(
        'plan_member_since_text_color',
        [
            'label'     => __( 'Color', 'hw-ele-woo-dynamic' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .membership-card .member-since-date' =>
                    'color: {{VALUE}};'
            ],
        ]
    ); 
    // prefix styles
    $this->add_control(
        'plan_member_since_prefix_style_heading',
        [
            'label' => __( 'Prefix Style', 'hw-ele-woo-dynamic' ),
            'type'  => Controls_Manager::HEADING,
            'separator' => 'before', 
        ]
    );

    
    $this->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name'     => 'plan_member_since_prefix_typography',
            'selector' => '{{WRAPPER}} .membership-card .member-since-prefix',
        ]
    );

    $this->add_control(
        'plan_member_since_prefix_text_color',
        [
            'label'     => __( 'Color', 'hw-ele-woo-dynamic' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .membership-card .member-since-prefix' =>
                    'color: {{VALUE}};'
            ],
        ]
    ); 
    // suffix styles
    $this->add_control(
        'plan_member_since_suffix_style_heading',
        [
            'label' => __( 'Suffix Style', 'hw-ele-woo-dynamic' ),
            'type'  => Controls_Manager::HEADING,
            'separator' => 'before', 
        ]
    );

    
    $this->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name'     => 'plan_member_since_suffix_typography',
            'selector' => '{{WRAPPER}} .membership-card .member-since-suffix',
        ]
    );

    $this->add_control(
        'plan_member_since_suffix_text_color',
        [
            'label'     => __( 'Color', 'hw-ele-woo-dynamic' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .membership-card .member-since-suffix' =>
                    'color: {{VALUE}};'
            ],
        ]
    ); 



    $this->end_controls_section();

    }

    protected function render() {
        if ( ! Dependencies::is_memberships_active() ) {
            echo '<p>' . esc_html__( 'WooCommerce Memberships plugin is not active.', 'hw-ele-woo-dynamic' ) . '</p>';
            return;
        }

        $settings = $this->get_settings_for_display();
        $user_id  = get_current_user_id();
        $all      = wc_memberships_get_user_memberships( $user_id );

        if ( empty( $all ) ) {
            echo '<p>' . esc_html__( 'You have no active membership.', 'hw-ele-woo-dynamic' ) . '</p>';
            return;
        }

        // Szűrés
        $filter_statuses = $settings['filter_statuses'] ?? [];
        $cards = array_filter( $all, function( $um ) use ( $filter_statuses ) {
            return empty( $filter_statuses ) || in_array( $um->get_status(), $filter_statuses, true );
        });

        if ( empty( $cards ) ) {
            echo '<p>' . esc_html__( 'You do not have a corresponding membership for the selected statuses.', 'hw-ele-woo-dynamic' ) . '</p>';
            return;
        }

        // Betöltjük a template-et
        $template = __DIR__ . '/../views/membership-card.php';
        if ( file_exists( $template ) ) {
            include $template;
        }
    }
}
