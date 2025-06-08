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
 * Dynamic tag: Membership Plan Count
 *
 * Returns the count of WooCommerce Membership plans for the current user
 * filtered by státusz: Active, Expired vagy Cancelled.
 * Ha nincs találat, nem ír ki semmit → Elementor fallback érvényesül.
 */
class MembershipPlanCount extends Tag {

    public function get_name(): string {
        return 'membership-plan-count';
    }

    public function get_title(): string {
        return __( 'Membership Plan Count', 'hw-ele-woo-dynamic' );
    }

    public function get_group(): string {
        return 'woo-extras-user';
    }

    public function get_categories(): array {
        return [ Module::TEXT_CATEGORY ];
    }

    protected function _register_controls(): void {
        $this->add_control(
            'membership_status',
            [
                'label'   => __( 'Status to Count', 'hw-ele-woo-dynamic' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'active'    => __( 'Active',    'hw-ele-woo-dynamic' ),
                    'expired'   => __( 'Expired',   'hw-ele-woo-dynamic' ),
                    'cancelled' => __( 'Cancelled', 'hw-ele-woo-dynamic' ),
                ],
                'default' => 'active',
            ]
        );
    }

    public function render(): void {
        if ( ! function_exists( 'wc_memberships_get_user_memberships' ) ) {
            return;
        }

        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            return;
        }

        $status = $this->get_settings_for_display( 'membership_status' );
        $count  = 0;

        if ( 'active' === $status ) {
            // csak az aktív tagságok
            $memberships = wc_memberships_get_user_active_memberships( $user_id );
            $count = is_array( $memberships ) ? count( $memberships ) : 0;
        } else {
            // lekérjük az összes tag-jogosultságot, majd szűrjük
            $all = wc_memberships_get_user_memberships( $user_id );
            if ( is_array( $all ) ) {
                foreach ( $all as $m ) {
                    if ( $m instanceof WC_Memberships_User_Membership && $m->get_status() === $status ) {
                        $count++;
                    }
                }
            }
        }

        if ( $count > 0 ) {
            echo esc_html( $count );
        }
        // else: semmit sem írunk ki, így Elementor fallback-je megjelenik
    }
}
