<?php
/**
 * Core: Query Source (meta lekérdezések kezelése)
 *
 * @package hw-elementor-woo-dynamic
 */

namespace HelloWP\HWEleWooDynamic\Modules\Widgets;

use WC_Memberships_Membership_Plan;
use WP_Post;
use WP_User;
use WP_Term;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuerySource {

public static function get_meta( string $key, string $query_source ) {
    // 1) LISTING GRID (JetEngine)
    if ( 'listing_grid' === $query_source && function_exists( 'jet_engine' ) ) {
        $store  = jet_engine()->listings->data ?? null;
        $object = $store ? $store->get_current_object() : null;
        if ( ! $object ) {
            return null;
        }
        // JetEngine  meta
        if ( method_exists( $store, 'get_meta' ) ) {
            return $store->get_meta( $key );
        }
        // fallback WP / ACF
        return self::get_wp_meta_by_object( $object, $key );
    }

    // 2) CURRENT POST
    if ( 'current_post' === $query_source ) {
        $post_id = get_the_ID();
        return self::get_wp_meta( $post_id, 'post', $key );
    }

    // 3) CURRENT USER
    if ( 'current_user' === $query_source ) {
        $uid = get_current_user_id();
        if ( ! $uid ) {
            return null;
        }
        return self::get_wp_meta( $uid, 'user', $key );
    }

    return null;
}

protected static function get_wp_meta_by_object( $object, string $key ) {
    if ( $object instanceof WP_Post ) {
        return self::get_wp_meta( $object->ID, 'post', $key );
    }
    if ( $object instanceof WP_User ) {
        return self::get_wp_meta( $object->ID, 'user', $key );
    }
    if ( $object instanceof WP_Term ) {
        return self::get_wp_meta( $object->term_id, 'term', $key );
    }
    return null;
}

protected static function get_wp_meta( int $id, string $type, string $key ) {
    $context = $type === 'post' ? $id : "{$type}_{$id}";
    if ( function_exists( 'get_field' ) ) {
        $acf = get_field( $key, $context );
        if ( false !== $acf && null !== $acf ) {
            return $acf;
        }
    }
    switch ( $type ) {
        case 'post':
            return get_post_meta( $id, $key, true );
        case 'user':
            return get_user_meta( $id, $key, true );
        case 'term':
            return get_term_meta( $id, $key, true );
    }
    return null;
}


	/**
     * Visszaadja az összes elérhető WooCommerce Memberships plan-t [id => name].
     *
     * @return array
     */
    public static function get_all_memberships(): array {
        if ( ! function_exists( 'wc_memberships_get_membership_plans' ) ) {
            return [];
        }
        $plans = wc_memberships_get_membership_plans(); 
        $options = [];
        foreach ( $plans as $plan ) {
            if ( $plan instanceof WC_Memberships_Membership_Plan ) {
                $options[ $plan->get_id() ] = $plan->get_name();
            }
        }
        return $options;
    }

	public static function get_membership_statuses(): array {
		if ( ! function_exists( 'wc_memberships_get_user_membership_statuses' ) ) {
			return [];
		}
	
		$raw_statuses = wc_memberships_get_user_membership_statuses( true, false );
	
		$options = [];
	
		foreach ( $raw_statuses as $slug => $status ) {
			if ( is_object( $status ) && isset( $status->label ) ) {
				$options[ $slug ] = (string) $status->label;
			}
			elseif ( is_array( $status ) && isset( $status['label'] ) ) {
				$options[ $slug ] = (string) $status['label'];
			}
			else {
				$options[ $slug ] = (string) $status;
			}
		}
	
		return $options;
	}
}
