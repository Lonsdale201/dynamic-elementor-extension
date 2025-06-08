<?php

namespace HelloWP\HWEleWooDynamic\WooTags\Subscription;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Subscriptions_Manager;

class ActiveSubscription extends Tag {

    public function get_name() {
        return 'active-subscription';
    }

    public function get_title() {
        return __('Active Subscription', 'hw-ele-woo-dynamic');
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
        if (!function_exists('wcs_get_users_subscriptions')) {
            echo '';
            return;
        }

        $user_id = get_current_user_id();
        if (!$user_id) {
            echo '';
            return;
        }

        $subscriptions = wcs_get_users_subscriptions($user_id);
        $output = [];

        foreach ($subscriptions as $subscription) {
            if ($subscription->has_status(['active', 'pending-cancel'])) {
                $subscription_url = $subscription->get_view_order_url();
                $items = $subscription->get_items();
                foreach ($items as $item) {
                    $product = $item->get_product();
                    if ($product) {
                        $product_name = $product->get_name();
                        $linkable = $this->get_settings_for_display('linkable') === 'yes';

                        $output[] = $linkable ? "<a href='" . esc_url($subscription_url) . "'>" . esc_html($product_name) . "</a>" : esc_html($product_name);
                        break; 
                    }
                }
                if (!empty($output)) {
                    break; 
                }
            }
        }

        echo wp_kses_post(implode(', ', $output));
    }
}
