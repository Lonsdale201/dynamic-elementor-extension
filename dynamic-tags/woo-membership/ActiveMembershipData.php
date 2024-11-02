<?php

namespace HelloWP\HWEleWooDynamic\WooTags\Membership;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

class ActiveMembershipData extends Tag {

    public function get_name() {
        return 'active-membership-data';
    }

    public function get_title() {
        return __('Active Membership Data', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras-user';
    }

    public function get_categories() {
        return [
            Module::TEXT_CATEGORY,
            Module::URL_CATEGORY   
        ];
    }
    protected function _register_controls() {
        $options = [];
        $plans = wc_memberships_get_membership_plans();
        foreach ($plans as $plan) {
            $options[$plan->get_slug()] = $plan->get_name();
        }

        $this->add_control(
            'selected_membership',
            [
                'label' => __('Select Membership', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT2,
                'options' => $options,
                'default' => [],
                'label_block' => true,
            ]
        );

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
                    'remaining_time' => __('Remaining time', 'hw-ele-woo-dynamic'),
                    'notes' => __('Notes', 'hw-ele-woo-dynamic'), 
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
                    'data_type' => ['member_since', 'expires', 'next_bill_on'],
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
                    'data_type' => ['expires', 'remaining_time'],
                ],
            ]
        );

        $this->add_control(
            'notes_display',
            [
                'label' => __('Notes Display', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'all' => __('Show All', 'hw-ele-woo-dynamic'),
                    'latest' => __('Show Latest', 'hw-ele-woo-dynamic'),
                ],
                'default' => 'all',
                'label_block' => true,
                'condition' => [
                    'data_type' => 'notes',
                ],
            ]
        );

        $this->add_control(
            'remaining_time_text',
            [
                'label' => __('Remaining Time Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'Your membership expires in {remaining_time} days.',
                'description' => __('Use {remaining_time} where you want to display the remaining days. If the membership has expired, "Expired" will be shown.', 'hw-ele-woo-dynamic'),
                'condition' => [
                    'data_type' => 'remaining_time',
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
        $user_id = get_current_user_id();
        if (!$user_id) {
            echo '';
            return;
        }
    
        $selected_membership_slug = $this->get_settings_for_display('selected_membership');
        $data_type = $this->get_settings_for_display('data_type');
        $user_memberships = wc_memberships_get_user_memberships($user_id);
        $date_format = $this->get_settings_for_display('date_format') ?: get_option('date_format');
        $notes_display = $this->get_settings_for_display('notes_display');
    
        foreach ($user_memberships as $membership) {
            $plan = $membership->get_plan();
            if ($plan->get_slug() === $selected_membership_slug) {
                switch ($data_type) {
                    case 'plan_name':
                        echo esc_html($plan->get_name());
                        break;
                    case 'status':
                        echo esc_html(wc_memberships_get_user_membership_status_name($membership->get_status()));
                        break;
                    case 'member_since':
                        $start_date = $membership->get_local_start_date('timestamp');
                        if ($start_date) {
                            echo '<time datetime="' . date('Y-m-d', $start_date) . '" title="' . esc_attr(date_i18n(wc_date_format(), $start_date)) . '">' . date_i18n($date_format, $start_date) . '</time>';
                        } else {
                            echo esc_html__('N/A', 'woocommerce-memberships');
                        }
                        break;
                    case 'expires':
                        $expiry_date = $membership->get_local_end_date('timestamp');
                        $subscription_id = get_post_meta($membership->get_id(), '_subscription_id', true);
                        $has_subscription = $subscription_id && function_exists('wcs_get_subscription') && wcs_get_subscription($subscription_id);
                        if ($expiry_date) {
                            echo '<time datetime="' . date('Y-m-d', $expiry_date) . '" title="' . esc_attr(date_i18n(wc_date_format(), $expiry_date)) . '">' . date_i18n($date_format, $expiry_date) . '</time>';
                        } else if ($has_subscription) {
                            echo ''; // Ha van előfizetés, akkor ne jelenítsük meg a "No Expiry Text"-et
                        } else {
                            echo esc_html($this->get_settings_for_display('no_expiry_text'));
                        }
                        break;
                    case 'next_bill_on':
                        $subscription_id = get_post_meta($membership->get_id(), '_subscription_id', true);
                        if ($subscription_id && function_exists('wcs_get_subscription')) {
                            $subscription = wcs_get_subscription($subscription_id);
                            if ($subscription && $subscription->get_date('next_payment')) {
                                $next_payment_date = $subscription->get_date('next_payment');
                                echo date_i18n($date_format, strtotime($next_payment_date));
                            } else {
                                echo '';
                            }
                        } else {
                            echo '';
                        }
                        break;
                    case 'remaining_time':
                        $expiry_date = $membership->get_local_end_date('timestamp');
                        $current_time = current_time('timestamp');
                        if ($expiry_date && $expiry_date > $current_time) {
                            $remaining = ceil(($expiry_date - $current_time) / DAY_IN_SECONDS);
                            echo str_replace('{remaining_time}', $remaining, $this->get_settings_for_display('remaining_time_text'));
                        } elseif ($expiry_date && $expiry_date <= $current_time) {
                            echo $this->get_settings_for_display('expired_text');
                        } else {
                            echo $this->get_settings_for_display('no_expiry_text');
                        }
                        break;
                    case 'notes':
                        $notes = $membership->get_notes();
                        $filtered_notes = array_filter($notes, function($note) {
                            return get_comment_meta($note->comment_ID, 'notified', true) === '1';
                        });
                        if ($notes_display === 'latest' && !empty($filtered_notes)) {
                            $latest_note = array_shift($filtered_notes);
                            echo '<div class="membership-note">' . esc_html($latest_note->comment_content) . '</div>';
                        } elseif ($notes_display === 'all') {
                            foreach ($filtered_notes as $note) {
                                echo '<div class="membership-note">' . esc_html($note->comment_content) . '</div>';
                            }
                        }
                        break;
                    case 'link':
                        if (function_exists('wc_memberships_get_members_area_url')) {
                            $members_area_url = wc_memberships_get_members_area_url($membership->get_plan());
                            echo esc_url($members_area_url);
                            return;
                        }
                        break;
                }
                return;
            }
        }
    
        echo ''; 
    }
    
    
    
}