<?php
namespace HelloWP\HWEleWooDynamic\Modules\JEMacros;

class WCMembershipDiscountedProducts extends \Jet_Engine_Base_Macros {

    public function macros_tag() {
        return 'wc_membership_discounted_products';
    }

    public function macros_name() {
        return 'WC Membership Discounted Products';
    }

    public function macros_args() {
        // No arguments required for this macro
        return [];
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

        $discounted_products_ids = [];

        foreach ( $active_memberships as $membership ) {
            $membership_id = $membership->get_plan_id();
            $products = $this->get_discounted_products_by_membership( $user_id, $membership_id );
            $discounted_products_ids = array_merge( $discounted_products_ids, $products );
        }

        $discounted_products_ids = array_unique( $discounted_products_ids );

        if ( empty( $discounted_products_ids ) ) {
            return "No discounted products found for the user's memberships";
        }

        return implode( ', ', $discounted_products_ids );
    }

    /**
     * Retrieves products with discounts based on the membership.
     *
     * @param int $user_id        The current user ID.
     * @param int $membership_id  The membership plan ID.
     * @return array              List of product IDs with discounts.
     */
    protected function get_discounted_products_by_membership( $user_id, $membership_id ) {
        $discounted_products_ids = [];
        $all_rules = wc_memberships()->get_rules_instance()->get_rules( array(
            'rule_type'        => 'purchasing_discount',
            'exclude_inactive' => true,
        ) );
    
        if ( empty( $all_rules ) ) {
            return $discounted_products_ids;
        }
    
        foreach ( $all_rules as $rule ) {
            if ( (int) $rule->get_membership_plan_id() === (int) $membership_id ) {
                if ( method_exists( $rule, 'is_active' ) && ! $rule->is_active() ) {
                    continue;
                }
    
                $content_type      = $rule->get_content_type();       
                $content_type_name = $rule->get_content_type_name();  
                $object_ids        = $rule->get_object_ids();
    
                if ( 'post_type' === $content_type && 'product' === $content_type_name ) {
                    foreach ( $object_ids as $product_id ) {
                        if ( wc_memberships_user_can( $user_id, 'purchase', array( 'product' => $product_id ) ) ) {
                            $discounted_products_ids[] = $product_id;
                        }
                    }
                }
                elseif ( 'taxonomy' === $content_type && 'product_cat' === $content_type_name ) {
                    $cat_query_args = array(
                        'post_type'      => 'product',
                        'posts_per_page' => -1,
                        'fields'         => 'ids',
                        'tax_query'      => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field'    => 'term_id',
                                'terms'    => $object_ids,
                            ),
                        ),
                    );
    
                    $cat_query = new \WP_Query( $cat_query_args );
    
                    foreach ( $cat_query->posts as $product_id ) {
                        if ( wc_memberships_user_can( $user_id, 'purchase', array( 'product' => $product_id ) ) ) {
                            $discounted_products_ids[] = $product_id;
                        }
                    }
                }
            }
        }
    
        return array_unique( $discounted_products_ids );
    }
    
    
}
