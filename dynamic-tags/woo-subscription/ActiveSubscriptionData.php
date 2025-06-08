<?php

namespace HelloWP\HWEleWooDynamic\WooTags\Subscription;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;


class ActiveSubscriptionData extends Tag {

    public function get_name() {
        return 'active-subscription-data';
    }

    public function get_title() {
        return __('Active Subscription Data', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras-user';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'data_type',
            [
                'label' => __('Data Type', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'status' => __('Status', 'hw-ele-woo-dynamic'),
                    'next_payment_date' => __('Next Payment Date', 'hw-ele-woo-dynamic'),
                    'trial_end' => __('Trial end', 'hw-ele-woo-dynamic')
                ],
                'default' => 'status',
            ]
        );

        $this->add_control(
            'date_format',
            [
                'label' => __('Date Format', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => __('Custom date format (e.g., Y-m-d). Leave blank to use the site\'s default format.', 'hw-ele-woo-dynamic'),
                'condition' => [
                    'data_type' => ['next_payment_date', 'trial_end'],
                ],
            ]
        );

        $this->add_control(
            'custom_text',
            [
                'label' => __('Custom Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => __('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'data_type' => 'trial_end',
                ],
            ]
        );

        $this->add_control(
            'custom_textarea',
            [
                'label' => __('Custom Text for Trial', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'Your trial ends in {remaining_days} days.',
                'condition' => [
                    'custom_text' => 'yes',
                    'data_type' => 'trial_end',
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
    
        $subscriptions = wcs_get_users_subscriptions($user_id);
        
        foreach ($subscriptions as $subscription) {
            if ($subscription->has_status(['active', 'pending-cancel'])) {
                switch ($settings['data_type']) {
                    case 'status':
                        echo esc_html(wcs_get_subscription_status_name($subscription->get_status()));
                        break;
                    case 'next_payment_date':
                        $this->render_next_payment_date($subscription, $settings['date_format']);
                        break;
                    case 'trial_end':
                        $this->render_date($subscription, 'trial_end', $settings['date_format']);
                        break;
                }
                break; 
            }
        }
    }

    public function render_date($subscription, $date_type, $custom_format) {
        $date = $subscription->get_date($date_type, 'site');
        if (!$date) {
            return; 
        }
        
        if ($date_type === 'trial_end' && $this->get_settings_for_display('custom_text') === 'yes') {
            $today = new \DateTime();
            $end_date = new \DateTime($date);
            $interval = $today->diff($end_date);
            $days_remaining = $interval->format('%a');
            $custom_text = $this->get_settings_for_display('custom_textarea');
            $output = str_replace('{remaining_days}', esc_html($days_remaining), esc_html($custom_text));
            echo wp_kses_post($output);
        } else {
            $format = !empty($custom_format) ? esc_html($custom_format) : get_option('date_format');
            echo wp_kses_post(date_i18n($format, strtotime($date)));
        }
    }
    

    private function render_next_payment_date($subscription, $custom_format) {
        $next_payment_date = $subscription->get_date('next_payment', 'site');
        $format = !empty($custom_format) ? esc_html($custom_format) : get_option('date_format');
        $output = $next_payment_date ? date_i18n($format, strtotime($next_payment_date)) : __('No upcoming payment', 'hw-ele-woo-dynamic');
        echo wp_kses_post($output);
    }
}
