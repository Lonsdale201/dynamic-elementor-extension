<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

class TaxonomyACFMeta extends Tag {

    public function get_name() {
        return 'taxonomy-acf-meta';
    }

    public function get_title() {
        return esc_html__('Taxonomy ACF Meta', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [Module::TEXT_CATEGORY];
    }

    protected function _register_controls() {
        $this->add_control(
            'acf_meta_key',
            [
                'label' => esc_html__('ACF Meta Key', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => esc_html__('Enter the ACF meta key (field name) to retrieve.', 'hw-ele-woo-dynamic'),
            ]
        );

        $this->add_control(
            'read_more_switcher',
            [
                'label' => __('Enable Read More', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_off' => __('Off', 'hw-ele-woo-dynamic'),
                'label_on' => __('On', 'hw-ele-woo-dynamic'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'trim_length',
            [
                'label' => __('Character Length', 'hw-ele-woo-dynamic'),
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
                'label' => __('Read More Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Read More', 'hw-ele-woo-dynamic'),
                'condition' => [
                    'read_more_switcher' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'read_less_text',
            [
                'label' => __('Read Less Text', 'hw-ele-woo-dynamic'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Read Less', 'hw-ele-woo-dynamic'),
                'condition' => [
                    'read_more_switcher' => 'yes',
                ],
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings();
        $acf_meta_key = $settings['acf_meta_key']; 

        // Check if ACF's get_field function exists
        if (empty($acf_meta_key) || !function_exists('get_field')) {
            return; 
        }

        if (is_tax('product_cat')) {
            $queried_object = get_queried_object();

            $acf_value = get_field($acf_meta_key, $queried_object);

            if (empty($acf_value)) {
                return;
            }

            if ('yes' === $settings['read_more_switcher']) {
                $trim_length = !empty($settings['trim_length']) ? $settings['trim_length'] : 100;
                $read_more_text = !empty($settings['read_more_text']) ? $settings['read_more_text'] : __('Read More', 'hw-ele-woo-dynamic');
                $read_less_text = !empty($settings['read_less_text']) ? $settings['read_less_text'] : __('Read Less', 'hw-ele-woo-dynamic');

                if (strlen($acf_value) > $trim_length) {
                    $trimmed_acf_value = substr($acf_value, 0, $trim_length) . '...';

                    echo '<div id="acf-meta-description">';
                    echo '<div class="acf-meta-short">' . wp_kses_post($trimmed_acf_value) . '</div>';
                    echo '<div class="acf-meta-full" style="display: none;">' . wp_kses_post($acf_value) . '</div>';
                    echo '<a href="#" class="acf-read-more" data-expanded="false">' . esc_html($read_more_text) . '</a>';
                    echo '</div>';

                    echo '
                    <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const readMoreLink = document.querySelector(".acf-read-more");
                        const shortDescription = document.querySelector(".acf-meta-short");
                        const fullDescription = document.querySelector(".acf-meta-full");

                        readMoreLink.addEventListener("click", function(e) {
                            e.preventDefault();
                            const isExpanded = this.getAttribute("data-expanded") === "true";
                            if (isExpanded) {
                                shortDescription.style.display = "block";
                                fullDescription.style.display = "none";
                                this.innerText = "' . esc_js($read_more_text) . '";
                                this.setAttribute("data-expanded", "false");
                            } else {
                                shortDescription.style.display = "none";
                                fullDescription.style.display = "block";
                                this.innerText = "' . esc_js($read_less_text) . '";
                                this.setAttribute("data-expanded", "true");
                            }
                        });
                    });
                    </script>
                    ';
                    return;
                }
            }

            echo wp_kses_post($acf_value);
        }
    }
}
