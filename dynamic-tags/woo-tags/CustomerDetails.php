<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Customer;

class CustomerDetails extends Tag {

    public function get_name() {
        return 'custom-details';
    }

    public function get_title() {
        return __('Customer Details', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras-user';
    }

    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    protected function _register_controls() {
        $this->add_control(
            'detail_type',
            [
                'label' => __('Detail Type', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'billing' => __('Billing Details', 'hw-ele-woo-dynamic'),
                    'shipping' => __('Shipping Details', 'hw-ele-woo-dynamic'),
                ],
                'default' => 'billing',
            ]
        );

        $this->add_control(
            'billing_detail',
            [
                'label' => __('Billing Detail', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'billing_full_name' => __('Full Name', 'hw-ele-woo-dynamic'),
                    'billing_first_name' => __('First Name', 'hw-ele-woo-dynamic'),
                    'billing_last_name' => __('Last Name', 'hw-ele-woo-dynamic'),
                    'billing_company' => __('Company', 'hw-ele-woo-dynamic'),
                    'billing_city'  => __('City', 'hw-ele-woo-dynamic'),
                    'billing_address_1' => __('Address Line 1', 'hw-ele-woo-dynamic'),
                    'billing_phone' => __('Phone', 'hw-ele-woo-dynamic'),
                    'billing_email' => __('Email', 'hw-ele-woo-dynamic'),
                ],
                'default' => 'billing_full_name',
                'condition' => [
                    'detail_type' => 'billing',
                ],
            ]
        );

        $this->add_control(
            'shipping_detail',
            [
                'label' => __('Shipping Detail', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'shipping_full_name' => __('Full Name', 'hw-ele-woo-dynamic'),
                    'shipping_first_name' => __('First Name', 'hw-ele-woo-dynamic'),
                    'shipping_last_name' => __('Last Name', 'hw-ele-woo-dynamic'),
                    'shipping_company' => __('Company', 'hw-ele-woo-dynamic'),
                    'shipping_city'  => __('City', 'hw-ele-woo-dynamic'),
                    'shipping_address_1' => __('Address Line 1', 'hw-ele-woo-dynamic'),
                    'shipping_phone' => __('Phone', 'hw-ele-woo-dynamic'),
                ],
                'default' => 'shipping_full_name',
                'condition' => [
                    'detail_type' => 'shipping',
                ],
            ]
        );

        $this->add_control(
            'name_output_format',
            [
                'label' => __('Fullname Output', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'first_and_last' => __('First Name Last Name', 'hw-ele-woo-dynamic'),
                    'last_and_first' => __('Last Name First Name', 'hw-ele-woo-dynamic'),
                ],
                'default' => 'first_and_last',
                'condition' => [
                    'detail_type' => 'billing',
                    'billing_detail' => 'billing_full_name',
                ],
            ]
        );

        $this->add_control(
            'name_output_format_shipping',
            [
                'label' => __('Fullname Output', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'first_and_last' => __('First Name Last Name', 'hw-ele-woo-dynamic'),
                    'last_and_first' => __('Last Name First Name', 'hw-ele-woo-dynamic'),
                ],
                'default' => 'first_and_last',
                'condition' => [
                    'detail_type' => 'shipping',
                    'shipping_detail' => 'shipping_full_name',
                ],
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings_for_display();
        $user_id = get_current_user_id();


        if (!$user_id) {
            echo '';
            return;
        }

        if ('billing' === $settings['detail_type']) {
            switch ($settings['billing_detail']) {
                case 'billing_full_name':
                    $first_name = get_user_meta($user_id, 'billing_first_name', true);
                    $last_name = get_user_meta($user_id, 'billing_last_name', true);
                    $value = ('first_and_last' === $settings['name_output_format']) ? "$first_name $last_name" : "$last_name $first_name";
                    break;
                case 'billing_first_name':
                    $value = get_user_meta($user_id, 'billing_first_name', true);
                    break;
                case 'billing_last_name':
                    $value = get_user_meta($user_id, 'billing_last_name', true);
                    break;
                case 'billing_company':
                    $value = get_user_meta($user_id, 'billing_company', true);
                break;
                case 'billing_city':
                    $value = get_user_meta($user_id, 'billing_city', true);
                    break;
                case 'billing_address_1':
                    $value = get_user_meta($user_id, 'billing_address_1', true);
                    break;
                case 'billing_phone':
                    $value = get_user_meta($user_id, 'billing_phone', true);
                    break;
                case 'billing_email':
                    $value = get_user_meta($user_id, 'billing_email', true);
                    break;
            }
        } elseif ('shipping' === $settings['detail_type']) {
            switch ($settings['shipping_detail']) {
                case 'shipping_full_name':
                    $first_name = get_user_meta($user_id, 'shipping_first_name', true);
                    $last_name = get_user_meta($user_id, 'shipping_last_name', true);
                    $format = $settings['name_output_format_shipping'];
                    $value = $format === 'first_and_last' ? "$first_name $last_name" : "$last_name $first_name";
                    break;
                case 'shipping_first_name':
                    $value = get_user_meta($user_id, 'shipping_first_name', true);
                    break;
                case 'shipping_last_name':
                    $value = get_user_meta($user_id, 'shipping_last_name', true);
                    break;
                case 'shipping_company':
                    $value = get_user_meta($user_id, 'shipping_company', true);
                    break;
                case 'shipping_city':
                    $value = get_user_meta($user_id, 'shipping_city', true);
                    break;
                case 'shipping_address_1':
                    $value = get_user_meta($user_id, 'shipping_address_1', true);
                    break;
                case 'shipping_phone':
                    $value = get_user_meta($user_id, 'shipping_phone', true);
                    break;
            }
        }
        echo wp_kses_post($value ?? '');
    }
}