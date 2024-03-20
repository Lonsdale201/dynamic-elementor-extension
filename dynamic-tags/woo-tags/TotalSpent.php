<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Customer;

class TotalSpent extends Tag {

    public function get_name() {
        return 'total-spent';
    }

    public function get_title() {
        return __('Total Spent', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras-user';
    }

    public function get_categories() {
        return [Module::NUMBER_CATEGORY, Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'formatted_output',
            [
                'label' => __('Formatted Output', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'hw-ele-woo-dynamic'),
                'label_off' => __('No', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
    }

    public function render() {
        $user_id = get_current_user_id();

        if (!$user_id) {
            echo '';
            return;
        }

        $customer = new WC_Customer($user_id);
        $total_spent = $customer->get_total_spent();

        if ('yes' === $this->get_settings('formatted_output')) {
            echo wc_price($total_spent);
        } else {
            echo esc_html($total_spent);
        }
    }
}
