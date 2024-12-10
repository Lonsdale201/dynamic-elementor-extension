<?php
namespace HelloWP\HWEleWooDynamic\Modules\JEMacros;

class WCMembershipAccessAllProducts extends \Jet_Engine_Base_Macros {

    public function macros_tag() {
        return 'wc_membership_access_all_products';
    }

    public function macros_name() {
        return 'WC Membership Access All Products';
    }

    public function macros_args() {
        return [
            'access_type' => [
                'label'   => 'Access Type',
                'type'    => 'select',
                'options' => [
                    'both'     => 'Both',
                    'view'     => 'View',
                    'purchase' => 'Purchase',
                ],
                'default' => 'both',
            ],
        ];
    }

    public function macros_callback( $args = array() ) {
        $user_id = get_current_user_id();

        if ( !$user_id ) {
            return 'User is not logged in';
        }

        $active_memberships = wc_memberships_get_user_active_memberships( $user_id );

        if ( empty( $active_memberships ) ) {
            return "User does not have any active memberships";
        }

        $access_type = isset( $args['access_type'] ) ? $args['access_type'] : 'both';

        $accessible_products_ids = [];

        foreach ( $active_memberships as $membership ) {
            $membership_id = $membership->get_plan_id();
            $products = $this->get_products_accessible_by_membership( $user_id, $membership_id, $access_type );
            $accessible_products_ids = array_merge( $accessible_products_ids, $products );
        }

        $accessible_products_ids = array_unique( $accessible_products_ids );

        if ( empty( $accessible_products_ids ) ) {
            return "No products found for the user's memberships";
        }

        return implode( ', ', $accessible_products_ids );
    }

    /**
     * Retrieves products accessible based on the membership and access type.
     *
     * @param int    $user_id        The current user ID.
     * @param int    $membership_id  The membership plan ID.
     * @param string $access_type    The access type ('both', 'view', or 'purchase').
     * @return array                 List of product IDs.
     */
    protected function get_products_accessible_by_membership( $user_id, $membership_id, $access_type ) {
        $accessible_products_ids = [];

        $plan = wc_memberships_get_membership_plan( $membership_id );
        if ( ! $plan ) {
            return $accessible_products_ids;
        }


        $rules = $plan->get_product_restriction_rules();
        if ( empty( $rules ) ) {
            return $accessible_products_ids;
        }

        foreach ( $rules as $rule ) {
            if ( $access_type === 'both' || $rule->get_access_type() === $access_type ) {
                $object_ids = $rule->get_object_ids();

                foreach ( $object_ids as $product_id ) {
                    if ( wc_memberships_user_can( $user_id, $rule->get_access_type(), array( 'product' => $product_id ) ) ) {
                        $accessible_products_ids[] = $product_id;
                    }
                }
            }
        }

        return array_unique( $accessible_products_ids );
    }
}
