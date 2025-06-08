<?php
namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;

class ProductTabEmpty extends Base {

    public function get_id() {
        return 'wc-product-tab-empty';
    }

    public function get_name() {
        return __( 'Product Tab Empty', 'hw-ele-woo-dynamic' );
    }

    public function get_group() {
        return 'WooCommerce Extra';
    }

    public function get_custom_controls() {
        return [
            'tab_to_check' => [
                'label'   => __( 'Select Tab', 'hw-ele-woo-dynamic' ),
                'type'    => 'select',
                'options' => $this->get_all_tabs(),
                'default' => '',
            ],
        ];
    }

    public function check( $args = [] ) {
        $product_id = get_the_ID();
        if ( ! $product_id ) {
            return false;
        }

        $tab_id = $args['condition_settings']['tab_to_check'] ?? '';
        if ( empty( $tab_id ) ) {
            return false;
        }

        $tab_content = $this->get_tab_content( $tab_id );
        $has_content = ! empty( trim( $tab_content ) );

        $type = $args['type'] ?? 'show';
        return ( 'hide' === $type ) ? ! $has_content : $has_content;
    }

    public function is_for_fields() {
        return false;
    }

    public function need_value_detect() {
        return false;
    }

    /**
     * Retrieve all available tabs (default WooCommerce + Tab Manager).
     *
     * @return array
     */
    private function get_all_tabs() {
        $options    = [];
        $product_id = get_the_ID();
        if ( ! $product_id ) {
            return $options;
        }

        // Backup global variables
        global $product, $post;
        $backup_product = $product;
        $backup_post    = $post;

        $post    = get_post( $product_id );
        $product = wc_get_product( $product_id );
        if ( ! $product ) {
            $product = $backup_product;
            $post    = $backup_post;
            return $options;
        }

        $default_tabs = apply_filters( 'woocommerce_product_tabs', [] );

        $product = $backup_product;
        $post    = $backup_post;

        if ( is_array( $default_tabs ) ) {
            foreach ( $default_tabs as $key => $tab ) {
                if ( 'reviews' === $key ) {
                    continue;
                }
                $options[ $key ] = '[Default] ' . ( $tab['title'] ?? ucfirst( $key ) );
            }
        }

        if ( Dependencies::is_tab_manager_active() ) {
            $tab_posts = get_posts( [
                'post_type'      => 'wc_product_tab',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
            ] );

            foreach ( $tab_posts as $tab ) {
                $options[ 'tm_' . $tab->ID ] = '[Tab Manager] ' . $tab->post_title;
            }
        }

        return $options;
    }

    /**
     * Get the rendered content for a specific tab (default or Tab Manager).
     *
     * @param string $selected_tab
     * @return string
     */
    private function get_tab_content( $selected_tab ) {
        $product_id = get_the_ID();
        if ( ! $product_id ) {
            return '';
        }

        // Tab Manager tab logic
        if ( str_starts_with( $selected_tab, 'tm_' ) ) {
            $tab_id   = str_replace( 'tm_', '', $selected_tab );
            $tab_post = get_post( $tab_id );
            if ( $tab_post && 'wc_product_tab' === $tab_post->post_type ) {
                return $tab_post->post_content;
            }
            return '';
        }

        global $product, $post;
        $backup_product = $product;
        $backup_post    = $post;

        $post    = get_post( $product_id );
        $product = wc_get_product( $product_id );
        if ( ! $product ) {
            $product = $backup_product;
            $post    = $backup_post;
            return '';
        }

        $tabs    = apply_filters( 'woocommerce_product_tabs', [] );
        $content = '';

        if ( isset( $tabs[ $selected_tab ] ) && is_callable( $tabs[ $selected_tab ]['callback'] ) ) {
            ob_start();
            call_user_func( $tabs[ $selected_tab ]['callback'], $selected_tab, $tabs[ $selected_tab ] );
            $content = ob_get_clean();
        }

        $product = $backup_product;
        $post    = $backup_post;

        return $content;
    }
}
