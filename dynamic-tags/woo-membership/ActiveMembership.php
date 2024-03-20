<?php

namespace HelloWP\HWEleWooDynamic\WooTags\Membership;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Memberships_User_Membership;

class ActiveMembership extends Tag {

    public function get_name() {
        return 'active-membership';
    }

    public function get_title() {
        return __('Active Membership', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras-user';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'linkable',
            [
                'label' => __('Linkable', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => __('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
    }

    public function render() {
        if (!class_exists('WC_Memberships')) {
            echo '';
            return;
        }

        $user_id = get_current_user_id();
        if (!$user_id) {
            echo '';
            return;
        }

        $active_memberships = wc_memberships_get_user_active_memberships($user_id);
        $output = [];

        foreach ($active_memberships as $membership) {
            $plan = $membership->get_plan();
            if (!is_null($plan)) {
                $plan_name = $plan->get_name();
                $linkable = $this->get_settings_for_display('linkable') === 'yes';
                $membership_url = $linkable ? wc_memberships_get_members_area_url($plan) : '';
                $output[] = $linkable ? "<a href='" . esc_url($membership_url) . "'>" . esc_html($plan_name) . "</a>" : esc_html($plan_name);
            }
        }

        echo implode(', ', $output);
    }
}
