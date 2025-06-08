<?php

namespace HelloWP\HWEleWooDynamic\WooTags\Membership;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Memberships_User_Membership;
use WC_Memberships;

class CurrentMembershipData extends Tag {

    public function get_name() {
        return 'current-membership-data';
    }

    public function get_title() {
        return __('Current Membership Data', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras-user';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY, Module::URL_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'data_type',
            [
                'label' => __('Data Type', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'plan_name' => __('Plan Name', 'hw-ele-woo-dynamic'),
                    'status' => __('Status', 'hw-ele-woo-dynamic'),
                    'member_since' => __('Member Since', 'hw-ele-woo-dynamic'), 
                    'next_bill_on' => __('Next Bill On', 'hw-ele-woo-dynamic'),
                    'expires' => __('Expires', 'hw-ele-woo-dynamic'),
                    'remaining_time' => __('Remaining Time', 'hw-ele-woo-dynamic'),
                    'link' => __('Link', 'hw-ele-woo-dynamic')
                ],
                'default' => 'plan_name',
            ]
        );

        $this->add_control(
            'date_format',
            [
                'label' => __('Date Format', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => __('Enter the date format (e.g., Y-m-d). Leave blank to use the site\'s default format.', 'hw-ele-woo-dynamic'),
                'condition' => [
                    'data_type' => ['member_since', 'next_bill_on', 'expires'],
                ],
            ]
        );

        $this->add_control(
            'remaining_time_text',
            [
                'label' => __('Remaining Time Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'Your membership expires in {remaining_time} days.',
                'description' => __('Use {remaining_time} where you want to display the remaining days.', 'hw-ele-woo-dynamic'),
                'condition' => [
                    'data_type' => 'remaining_time',
                ],
            ]
        );

        $this->add_control(
            'no_expiry_text',
            [
                'label' => __('No Expiry Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Never expires', 'hw-ele-woo-dynamic'),
                'description' => __('Text to display when there is no expiry date.', 'hw-ele-woo-dynamic'),
                'condition' => [
                    'data_type' => 'expires',
                ],
            ]
        );

        $this->add_control(
            'expired_text',
            [
                'label' => __('Expired Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => 'Expired',
                'description' => __('Text to display when the membership has expired.', 'hw-ele-woo-dynamic'),
                'condition' => [
                    'data_type' => 'remaining_time',
                ],
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings_for_display();
        $user_id = get_current_user_id();
        $user_membership_id = get_the_ID();
        $date_format = $settings['data_type'] === 'member_since' || $settings['data_type'] === 'next_bill_on' || $settings['data_type'] === 'expires' ? ($settings['date_format'] ?: get_option('date_format')) : '';

        $membership = new WC_Memberships_User_Membership($user_membership_id);
        if (!$membership || $membership->get_user_id() !== $user_id) {
            return;
        }

        $members_area_endpoint = get_option('woocommerce_memberships_members_area_endpoint', 'members-area');
        $subscription_id = get_post_meta($membership->get_id(), '_subscription_id', true);
        $subscription = function_exists('wcs_get_subscription') ? wcs_get_subscription($subscription_id) : null;
        $has_subscription = $subscription && $subscription->get_date('next_payment');

        switch ($settings['data_type']) {
            case 'plan_name':
                $plan = $membership->get_plan();
                echo esc_html($plan ? $plan->get_name() : '');
                break;
            case 'status':
                echo esc_html(wc_memberships_get_user_membership_status_name($membership->get_status()));
                break;
            case 'member_since':
                $start_date = $membership->get_local_start_date('timestamp');
                echo $start_date ? '<time datetime="' . esc_attr(date('Y-m-d', $start_date)) . '" title="' . esc_attr(date_i18n(wc_date_format(), $start_date)) . '">' . esc_html(date_i18n($date_format, $start_date)) . '</time>' : esc_html__('N/A', 'woocommerce-memberships');
                break;
            case 'next_bill_on':
                if ($has_subscription) {
                    $next_payment_date = $subscription->get_date('next_payment');
                    echo esc_html(date_i18n($date_format, strtotime($next_payment_date)));
                } else {
                    echo '';
                }
                break;
            case 'expires':
                $expiry_date = $membership->get_local_end_date('timestamp');
                if ($expiry_date) {
                    echo '<time datetime="' . esc_attr(date('Y-m-d', $expiry_date)) . '" title="' . esc_attr(date_i18n(wc_date_format(), $expiry_date)) . '">' . esc_html(date_i18n($date_format, $expiry_date)) . '</time>';
                } elseif ($has_subscription) {
                    echo '';
                } else {
                    echo esc_html($settings['no_expiry_text']);
                }
                break;
            case 'remaining_time':
                $expiry_date = $membership->get_local_end_date('timestamp');
                $current_time = current_time('timestamp');
                if ($expiry_date && $expiry_date > $current_time) {
                    $remaining = ceil(($expiry_date - $current_time) / DAY_IN_SECONDS);
                    echo esc_html(str_replace('{remaining_time}', $remaining, $settings['remaining_time_text']));
                } elseif ($expiry_date && $expiry_date <= $current_time) {
                    echo esc_html($settings['expired_text']);
                } else {
                    echo esc_html($settings['no_expiry_text']);
                }
                break;
            case 'link':
                if (function_exists('wc_memberships_get_members_area_url')) {
                    if ($membership->is_active()) {
                        $members_area_url = wc_memberships_get_members_area_url($membership->get_plan(), 'my-membership-content');
                    } else {
                        $myaccount_page_id = get_option('woocommerce_myaccount_page_id');
                        $myaccount_url = get_permalink($myaccount_page_id);
                        $members_area_url = wc_get_endpoint_url('members-area', '', $myaccount_url);
                    }
                    echo esc_url($members_area_url);
                    return;
                }
                break;
        }
    }
}
