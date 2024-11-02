<?php

namespace HelloWP\HWEleWooDynamic\WooTags\Membership;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Memberships;
class RestrictedProductsView extends Tag {

    public function get_name() {
        return 'restricted-products-view';
    }

    public function get_title() {
        return __('Restricted Products View (beta)', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras-user';
    }

    protected function _register_controls() {
        $this->add_control(
            'product_viewing_restricted_purchase_required_message',
            [
                'label' => esc_html__('Product Viewing Restricted (Purchase Required) Message', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('This product can only be viewed by members.', 'hw-ele-woo-dynamic'),
            ]
        );

        $this->add_control(
            'product_viewing_restricted_membership_required_message',
            [
                'label' => esc_html__('Product Viewing Restricted (Membership Required) Message', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('This product can only be viewed by members.', 'hw-ele-woo-dynamic'),
            ]
        );

        $this->add_control(
            'product_purchasing_restricted_purchase_required_message',
            [
                'label' => esc_html__('Product Buying Restricted (Purchase Required) Message', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('This product can only be purchased by members.', 'hw-ele-woo-dynamic'),
            ]
        );

        $this->add_control(
            'product_purchasing_restricted_membership_required_message',
            [
                'label' => esc_html__('Product Buying Restricted (Membership Required) Message', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('This product can only be purchased by members.', 'hw-ele-woo-dynamic'),
            ]
        );
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    public function render() {
        if (current_user_can('wc_memberships_access_all_restricted_content')) {
            echo '';
            return;
        }

        $product_id = get_the_ID();
        $settings = $this->get_settings_for_display();

        $viewing_restricted = wc_memberships_is_product_viewing_restricted($product_id);
        $purchasing_restricted = wc_memberships_is_product_purchasing_restricted($product_id);

        $message = ''; 

        if ($viewing_restricted && $purchasing_restricted) {
            $message = $settings['product_purchasing_restricted_membership_required_message'];
        } elseif ($viewing_restricted) {
            $message = $settings['product_viewing_restricted_membership_required_message'];
        } elseif ($purchasing_restricted) {
            $message = $settings['product_purchasing_restricted_purchase_required_message'];
        }

        echo esc_html($message);
    }
}
