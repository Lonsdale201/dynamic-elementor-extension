<?php
/**
 * View: Membership Cards
 *
 * @var array                              $settings
 * @var \WC_Memberships_User_Membership[]  $cards
 */

use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;
use Elementor\Icons_Manager;

echo '<div class="membership-cards-wrapper">';

foreach ( $cards as $membership ) {

    $status_slug = $membership->get_status();
    $status_label = '';

    if ( function_exists( 'wc_memberships_get_user_membership_status_name' ) ) {
        $status_label = (string) wc_memberships_get_user_membership_status_name( $status_slug );
    } elseif ( method_exists( $membership, 'get_status_label' ) ) {
        $status_label = (string) $membership->get_status_label();
    }

    if ( '' === $status_label ) {
        $status_label = ucfirst( (string) $status_slug );
    }

    $card_linkable = ( 'yes' === ( $settings['linkable_card'] ?? '' ) );
    $card_url      = '';

    if ( $card_linkable && method_exists( $membership, 'get_view_membership_url' ) ) {
        $card_url = (string) $membership->get_view_membership_url();
    }

    if ( $card_linkable && ! $card_url && function_exists( 'wc_memberships_get_members_area_url' ) ) {
        $plan = $membership->get_plan();
        if ( $plan ) {
            $card_url = (string) wc_memberships_get_members_area_url( $plan );
        }
    }

    $card_tag     = ( $card_linkable && $card_url ) ? 'a' : 'div';
    $card_classes = 'membership-card' . ( ( 'a' === $card_tag ) ? ' membership-card--linkable' : '' );

    $card_attributes = sprintf( ' class="%s"', esc_attr( $card_classes ) );

    if ( 'a' === $card_tag ) {
        $card_attributes .= sprintf( ' href="%s"', esc_url( $card_url ) );
    }

    echo sprintf( '<%1$s%2$s>', $card_tag, $card_attributes );

    if ( 'yes' === ( $settings['status_as_badge'] ?? '' ) ) {
        echo '<div class="membership-status membership-status-badge membership-status-badge--' . esc_attr( $status_slug ) . '">';
            if ( ! empty( $settings['status_prefix'] ) ) {
                echo '<span class="status-prefix">' . wp_kses_post( $settings['status_prefix'] ) . '</span>';
            }
            echo '<span class="status-text">' . esc_html( $status_label ) . '</span>';
        echo '</div>';
    }
    

    // ==== Icon ====
    if ( ! empty( $settings['card_icon']['value'] ) ) {
        echo '<span class="membership-card-icon">';
        Icons_Manager::render_icon( $settings['card_icon'], [ 'aria-hidden' => 'true' ] );
        echo '</span>';
    }

    echo '<div class="membership-card-body">';

    // ===== Plan Name =====
    if ( in_array( 'plan_name', $settings['display_fields'], true ) ) {
        echo '<div class="membership-plan-name">';
        if ( ! empty( $settings['plan_name_prefix'] ) ) {
            echo '<span class="plan-name-prefix">' . wp_kses_post( $settings['plan_name_prefix'] ) . '</span>';
        }
        echo '<span class="plan-name">' . esc_html( $membership->get_plan()->get_name() ) . '</span>';
        if ( ! empty( $settings['plan_name_suffix'] ) ) {
            echo '<span class="plan-name-suffix">' . wp_kses_post( $settings['plan_name_suffix'] ) . '</span>';
        }
        if ( ! empty( $settings['plan_name_description'] ) ) {
            echo '<div class="plan-name-description">';
                echo wp_kses_post( $settings['plan_name_description'] );
            echo '</div>';
        }
        
        echo '</div>'; // .membership-plan-name
    }

    // ===== Other fields =====
    foreach ( $settings['display_fields'] as $field ) {
        if ( 'plan_name' === $field ) {
            continue;
        }
        if ( 'status' === $field && 'yes' === ( $settings['status_as_badge'] ?? '' ) ) {
            continue;
        }
        switch ( $field ) {

        case 'expiry_date':
            echo '<div class="membership-expiry">';

        $next_ts = false;
        if ( Dependencies::is_subscriptions_active() ) {
            $plan_products = (array) $membership->get_plan()->get_product_ids();
            $subs = wcs_get_users_subscriptions( get_current_user_id() );
            foreach ( $subs as $sub ) {
                if ( ! $sub->has_status( [ 'active', 'pending-cancel' ] ) ) {
                    continue;
                }
                foreach ( $sub->get_items() as $item ) {
                    if ( in_array( $item->get_product_id(), $plan_products, true ) ) {
                        $next_ts = intval( $sub->get_time( 'next_payment', 'site' ) );
                        break 2;
                    }
                }
            }
        }

        if ( $next_ts ) {
            if ( ! empty( $settings['next_bill_prefix'] ) ) {
                echo '<span class="next-bill-prefix">'
                    . wp_kses_post( $settings['next_bill_prefix'] )
                    . '</span>';
            }
            echo '<span class="next-bill-date">'
                . esc_html( date_i18n( $settings['expiry_date_format'], $next_ts ) )
                . '</span>';
        } else {
            $end = $membership->get_end_date( 'edit' );
            if ( $end ) {
                if ( ! empty( $settings['expiry_prefix'] ) ) {
                    echo '<span class="expiry-prefix">'
                        . wp_kses_post( $settings['expiry_prefix'] )
                        . '</span>';
                }
                echo '<span class="expiry-date">'
                    . esc_html( date_i18n( $settings['expiry_date_format'], strtotime( $end ) ) )
                    . '</span>';
                if ( ! empty( $settings['expiry_suffix'] ) ) {
                    echo '<span class="expiry-suffix">'
                        . wp_kses_post( $settings['expiry_suffix'] )
                        . '</span>';
                }
            } elseif ( 'yes' !== ( $settings['hide_if_unlimited'] ?? '' ) ) {
                echo '<span class="expiry-unlimited">'
                    . esc_html( $settings['unlimited_text'] ?: __( 'Unlimited', 'hw-elementor-woo-dynamic' ) )
                    . '</span>';
            }
        }

        echo '</div>'; // .membership-expiry
            break;

            case 'status':
                if ( in_array( 'status', $settings['display_fields'], true ) && 'yes' !== ( $settings['status_as_badge'] ?? '' ) ) {
                    echo '<div class="membership-status">';
                        if ( ! empty( $settings['status_prefix'] ) ) {
                            echo '<span class="status-prefix">' . wp_kses_post( $settings['status_prefix'] ) . '</span>';
                        }
                        echo '<span class="status-text">' . esc_html( $status_label ) . '</span>';
                        if ( ! empty( $settings['status_suffix'] ) ) {
                            echo '<span class="status-suffix">' . wp_kses_post( $settings['status_suffix'] ) . '</span>';
                        }
                    echo '</div>';
                }
                break;

            case 'member_since':
                $start = $membership->get_start_date( 'edit' );
                if ( $start ) {
                    echo '<div class="membership-member-since">';
                    if ( ! empty( $settings['member_since_prefix'] ) ) {
                        echo '<span class="member-since-prefix">' . wp_kses_post( $settings['member_since_prefix'] ) . '</span>';
                    }
                    echo '<span class="member-since-date">' . esc_html( date_i18n( $settings['member_since_date_format'], strtotime( $start ) ) ) . '</span>';
                    if ( ! empty( $settings['member_since_suffix'] ) ) {
                        echo '<span class="member-since-suffix">' . wp_kses_post( $settings['member_since_suffix'] ) . '</span>';
                    }
                    echo '</div>'; // .membership-member-since
                }
                break;
        }
    }

    echo '</div>';   // .membership-card-body
    echo sprintf( '</%s>', $card_tag );
}

echo '</div>';       // .membership-cards-wrapper
