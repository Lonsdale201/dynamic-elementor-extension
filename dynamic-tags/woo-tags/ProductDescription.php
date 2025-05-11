<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class ProductDescription extends Tag {

    public function get_name() {
        return 'product-description';
    }

    public function get_title() {
        return __('Product Description', 'hw-elementor-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function register_controls() {
        $this->add_control(
            'read_more_switcher',
            [
                'label' => __('Read More', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_off' => __('Off', 'hw-elementor-woo-dynamic'),
                'label_on' => __('On', 'hw-elementor-woo-dynamic'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'trim_length',
            [
                'label' => __('Character Length', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::NUMBER,
                'default' => 100,
                'condition' => [
                    'read_more_switcher' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'read_more_text',
            [
                'label' => __('Read More Text', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Read More', 'hw-elementor-woo-dynamic'),
                'condition' => [
                    'read_more_switcher' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'read_more_anchor',
            [
                'label' => __('Anchor ID', 'hw-elementor-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => '#read-more',
                'condition' => [
                    'read_more_switcher' => 'yes',
                ],
            ]
        );
    }

    public function render() {
        $product = wc_get_product(get_the_ID());

        if (!$product) {
            return;
        }

        $description = $product->get_description();

        if (empty($description)) {
            return;
        }

        $settings = $this->get_settings();

        if ('yes' === $settings['read_more_switcher']) {
            $trim_length = !empty($settings['trim_length']) ? $settings['trim_length'] : 100;
            $read_more_text = !empty($settings['read_more_text']) ? $settings['read_more_text'] : __('Read More', 'hw-elementor-woo-dynamic');
            $anchor = !empty($settings['read_more_anchor']) ? $settings['read_more_anchor'] : '#read-more';

            if (strlen($description) > $trim_length) {
                $trimmed_description = substr($description, 0, $trim_length) . '...';
                echo '<div id="description">';
                echo wp_kses_post($trimmed_description);
                echo ' <a href="' . esc_url($anchor) . '" aria-expanded="false">' . esc_html($read_more_text) . '</a>';
                echo '</div>';
                return;
            }
        }

        echo wp_kses_post($description);
    }
}
