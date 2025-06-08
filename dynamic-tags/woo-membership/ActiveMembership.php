<?php
namespace HelloWP\HWEleWooDynamic\WooTags\Membership;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Memberships_User_Membership;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Dynamic tag: Active Membership
 *
 * Lists the current user’s active membership plans.
 * Return Value: name|slug|id
 * Output Format: plain|li
 * Plain esetén megadható delimiter.
 */
class ActiveMembership extends Tag {

    public function get_name(): string {
        return 'active-membership';
    }

    public function get_title(): string {
        return __( 'Active Membership', 'hw-ele-woo-dynamic' );
    }

    public function get_group(): string {
        return 'woo-extras-user';
    }

    public function get_categories(): array {
        return [ Module::TEXT_CATEGORY ];
    }

    protected function _register_controls(): void {
        $this->add_control(
            'linkable',
            [
                'label'        => __( 'Linkable (only Name)', 'hw-ele-woo-dynamic' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'hw-ele-woo-dynamic' ),
                'label_off'    => __( 'No',  'hw-ele-woo-dynamic' ),
                'return_value' => 'yes',
                'default'      => 'no',
            ]
        );

        // Return Value
        $this->add_control(
            'return_value',
            [
                'label'   => __( 'Return Value', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'name' => __( 'Name',        'hw-ele-woo-dynamic' ),
                    'slug' => __( 'Slug',        'hw-ele-woo-dynamic' ),
                    'id'   => __( 'ID',          'hw-ele-woo-dynamic' ),
                ],
                'default' => 'name',
            ]
        );

        // Output Format
        $this->add_control(
            'output_format',
            [
                'label'   => __( 'Output Format', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'plain' => __( 'Plain', 'hw-ele-woo-dynamic' ),
                    'li'    => __( 'List',  'hw-ele-woo-dynamic' ),
                ],
                'default' => 'plain',
            ]
        );

        // Delimiter for plain
        $this->add_control(
            'plain_delimiter',
            [
                'label'       => __( 'Plain Delimiter', 'hw-ele-woo-dynamic' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => ', ',
                'condition'   => [
                    'output_format' => 'plain',
                ],
            ]
        );
    }

    public function render(): void {
        if ( ! function_exists( 'wc_memberships_get_user_active_memberships' ) ) {
            return;
        }

        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            return;
        }

        $memberships = wc_memberships_get_user_active_memberships( $user_id );
        if ( ! is_array( $memberships ) || empty( $memberships ) ) {
            return;
        }

        $settings     = $this->get_settings_for_display();
        $return_value = $settings['return_value'];
        $linkable     = ( 'yes' === $settings['linkable'] && 'name' === $return_value );
        $delimiter    = $settings['plain_delimiter'] ?? ', ';
        $output_fmt   = $settings['output_format'];

        $items = [];
        foreach ( $memberships as $membership ) {
            if ( ! $membership instanceof WC_Memberships_User_Membership ) {
                continue;
            }
            $plan = $membership->get_plan();
            if ( ! $plan ) {
                continue;
            }

            switch ( $return_value ) {
                case 'slug':
                    $val = $plan->get_slug();
                    break;
                case 'id':
                    $val = $plan->get_id();
                    break;
                case 'name':
                default:
                    $val = $plan->get_name();
            }

            if ( $linkable ) {
                $url = wc_memberships_get_members_area_url( $plan );
                $items[] = '<a href="' . esc_url( $url ) . '">' . esc_html( $val ) . '</a>';
            } else {
                $items[] = esc_html( $val );
            }
        }

        if ( empty( $items ) ) {
            return;
        }

        if ( 'li' === $output_fmt ) {
            echo wp_kses_post('<ul class="dynamic-membership-list">');
            foreach ( $items as $li ) {
                echo wp_kses_post('<li>' . $li . '</li>');
            }
            echo wp_kses_post('</ul>');
        } else {
            // plain
            echo wp_kses_post( implode( esc_html($delimiter), $items ) );
        }
    }
}
