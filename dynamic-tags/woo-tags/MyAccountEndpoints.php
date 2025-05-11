<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

class MyAccountEndpoints extends Tag {

    public function get_name() {
        return 'my-account-menu-links';
    }

    public function get_title() {
        return esc_html__('My Account Menu Links', 'hw-elementor-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras-user';
    }

    public function get_categories() {
        return [Module::URL_CATEGORY];
    }

    protected function _register_controls() {
        $menu_items = wc_get_account_menu_items();

        $options = [];
        foreach ($menu_items as $endpoint => $label) {
            $options[$endpoint] = $label;
        }

        $this->add_control(
            'selected_endpoint',
            [
                'label' => esc_html__('Select Endpoint', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SELECT,
                'options' => $options,
                'default' => array_keys($options)[0],
            ]
        );
    }

    public function render() {
        $selected_endpoint = $this->get_settings('selected_endpoint');

        $endpoint_url = wc_get_account_endpoint_url($selected_endpoint);
        echo esc_url($endpoint_url);
    }
}
