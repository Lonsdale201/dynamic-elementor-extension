<?php

namespace HelloWP\HWEleWooDynamic\WooTags\Membership;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

class MyAccountMembershipLink extends Tag {

    public function get_name() {
        return 'membership-myaccount-link';
    }

    public function get_title() {
        return __('Membership My Account Link', 'hw-elementor-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras-user';
    }

    public function get_categories() {
        return [Module::URL_CATEGORY];
    }

    public function render() {
        if (!function_exists('wc_memberships_get_members_area_endpoint')) {
            echo '';
            return;
        }

        // Get the memberships area endpoint slug from WooCommerce Memberships settings
        $members_area_endpoint = wc_memberships_get_members_area_endpoint();

        if (!$members_area_endpoint) {
            echo '';
            return;
        }

        // Construct the URL to the memberships area in the My Account page
        $myaccount_page_id = get_option('woocommerce_myaccount_page_id');
        if ($myaccount_page_id) {
            $myaccount_url = get_permalink($myaccount_page_id);
            $memberships_area_url = wc_get_endpoint_url($members_area_endpoint, '', $myaccount_url);

            echo esc_url($memberships_area_url);
        } else {
            echo '';
        }
    }
}
