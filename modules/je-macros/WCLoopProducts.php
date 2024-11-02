<?php

namespace HelloWP\HWEleWooDynamic\Modules\JEMacros;

class WCLoopProducts extends \Jet_Engine_Base_Macros {

    /**
     * Macro tag identifier
     *
     * @return string
     */
    public function macros_tag() {
        return 'wc_loop_products';
    }

    /**
     * Macro display name
     *
     * @return string
     */
    public function macros_name() {
        return 'WC Loop Products';
    }

    /**
     * Macro arguments (empty as none are required)
     *
     * @return array
     */
    public function macros_args() {
        return array(); 
    }

    /**
     * Callback to retrieve product IDs based on current context
     *
     * @param array $args
     * @return string Comma-separated product IDs or an error message
     */
    public function macros_callback( $args = array() ) {
        $query_args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'fields'         => 'ids', // Requesting only the IDs for efficiency
        );

        // Check if we're on the WooCommerce Shop or a Product Taxonomy page
        if ( is_shop() || is_product_taxonomy() ) {
            if ( is_product_taxonomy() ) {
                $current_term = get_queried_object_id();
                $current_taxonomy = get_queried_object()->taxonomy;
                // Restrict query to the current term in the product taxonomy
                $query_args['tax_query'] = array(
                    array(
                        'taxonomy' => $current_taxonomy,
                        'field'    => 'term_id',
                        'terms'    => $current_term,
                    ),
                );
            }
        } else {
            // Return message if not in a valid WooCommerce context
            return 'No valid context found';
        }

        // Query products based on the defined arguments
        $products = get_posts( $query_args );

        // Return message if no products are found in the current context
        if ( empty( $products ) ) {
            return 'No products found';
        }

        // Return a comma-separated list of product IDs
        return implode( ',', $products );
    }
}
