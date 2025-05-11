<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

class PurchasedBadge extends Tag {

    public function get_name() {
        return 'purchased-badge';
    }

    public function get_title() {
        return __('Purchased Badge', 'hw-elementor-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'purchased_text',
            [
                'label' => __('Purchased Text', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => __('You have purchased this in the past. Buy again?', 'hw-elementor-woo-dynamic'),
            ]
        );
    }

    public function render() {
        if (!is_user_logged_in() || !is_product()) {
            return;
        }

        $user_id = get_current_user_id();
        $product_id = get_the_ID();
        
        if (wc_customer_bought_product('', $user_id, $product_id)) {
            $purchased_text = $this->get_settings('purchased_text');
            echo esc_html($purchased_text);
        }
    }
}
