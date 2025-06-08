<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;
use WC_Product;

class ProductAttributes extends Tag {

    public function get_name() {
        return 'product-attributes';
    }

    public function get_title() {
        return esc_html__('Product Attributes', 'hw-ele-woo-dynamic');
    }

    public function get_group() {
        return 'woo-extras';
    }

    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    protected function _register_controls() {
        // Display type
        $this->add_control('display_type', [
            'label'   => esc_html__('Display Type', 'hw-ele-woo-dynamic'),
            'type'    => Controls_Manager::SELECT,
            'default' => 'label/value',
            'options' => [
                'value'       => esc_html__('Value', 'hw-ele-woo-dynamic'),
                'label'       => esc_html__('Label', 'hw-ele-woo-dynamic'),
                'label/value' => esc_html__('Label/Value', 'hw-ele-woo-dynamic'),
            ],
        ]);

        // Label separator
        $this->add_control('label_separator', [
            'label'     => esc_html__('Label Separator', 'hw-ele-woo-dynamic'),
            'type'      => Controls_Manager::TEXT,
            'default'   => ': ',
            'condition' => [
                'display_type' => 'label/value',
                'output_style!' => 'table',
            ],
        ]);

        // Linkable?
        $this->add_control('linkable', [
            'label'        => esc_html__('Linkable', 'hw-ele-woo-dynamic'),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => esc_html__('Yes', 'hw-ele-woo-dynamic'),
            'label_off'    => esc_html__('No',  'hw-ele-woo-dynamic'),
            'return_value' => 'yes',
        ]);

        // Attribute selector
        $taxes   = wc_get_attribute_taxonomies();
        $options = [ 'all' => esc_html__('All', 'hw-ele-woo-dynamic') ];
        foreach ($taxes as $tax) {
            $options[$tax->attribute_name] = $tax->attribute_label;
        }
        $this->add_control('attribute_name', [
            'label'       => esc_html__('Attributes', 'hw-ele-woo-dynamic'),
            'type'        => Controls_Manager::SELECT2,
            'multiple'    => true,
            'options'     => $options,
            'default'     => ['all'],
            'label_block' => true,
        ]);

        // Output style
        $this->add_control('output_style', [
            'label'   => esc_html__('Output Style', 'hw-ele-woo-dynamic'),
            'type'    => Controls_Manager::SELECT,
            'default' => 'delimiter',
            'options' => [
                'ul'        => esc_html__('UL',    'hw-ele-woo-dynamic'),
                'ol'        => esc_html__('OL',    'hw-ele-woo-dynamic'),
                'delimiter' => esc_html__('Plain','hw-ele-woo-dynamic'),
                'table'     => esc_html__('Table','hw-ele-woo-dynamic'),
            ],
        ]);

        // Delimiter
        $this->add_control('delimiter', [
            'label'     => esc_html__('Delimiter', 'hw-ele-woo-dynamic'),
            'type'      => Controls_Manager::TEXT,
            'default'   => ', ',
            'condition' => ['output_style' => 'delimiter'],
        ]);

        // Table headers
        $this->add_control('table_header_1', [
            'label'     => esc_html__('Table Header 1', 'hw-ele-woo-dynamic'),
            'type'      => Controls_Manager::TEXT,
            'default'   => esc_html__('Attribute', 'hw-ele-woo-dynamic'),
            'condition' => ['output_style' => 'table'],
        ]);
        $this->add_control('table_header_2', [
            'label'     => esc_html__('Table Header 2', 'hw-ele-woo-dynamic'),
            'type'      => Controls_Manager::TEXT,
            'default'   => esc_html__('Value', 'hw-ele-woo-dynamic'),
            'condition' => ['output_style' => 'table'],
        ]);
    }

    public function render() {
        $s        = $this->get_settings_for_display();
        $dt       = $s['display_type'];
        $sep      = $s['label_separator'];
        $sel      = (array) $s['attribute_name'];
        $os       = $s['output_style'];
        $del      = $s['delimiter'];
        $linkable = ('yes' === $s['linkable']);
        $product  = wc_get_product(get_the_ID());
        if (! $product) {
            return;
        }


        $taxes = wc_get_attribute_taxonomies();
        $arch  = array_map(function($t){ return 'pa_' . $t->attribute_name; }, $taxes);

        $rows   = [];
        $values = [];

        foreach ($product->get_attributes() as $name => $attr) {
            $norm = str_replace('pa_', '', $name);
            if (! in_array('all', $sel, true) && ! in_array($norm, $sel, true)) {
                continue;
            }

            $terms = wc_get_product_terms($product->get_id(), $name, ['fields' => 'all']);
            $vals  = array_map(function($term) use($linkable, $arch, $name, $del) {
                $t = esc_html($term->name);
                if ($linkable && in_array($name, $arch, true)) {
                    $l = get_term_link($term);
                    if (! is_wp_error($l)) {
                        return '<a href="' . esc_url($l) . '">' . $t . '</a>';
                    }
                }
                return $t;
            }, $terms);

            $lbl = esc_html(wc_attribute_label($name));

            if ($os === 'table') {
                $cell2 = implode(esc_html($del), $vals);
            } elseif ($dt === 'label') {
                $cell2 = $lbl;
            } elseif ($dt === 'value') {
                $cell2 = implode(esc_html($del), $vals);
            } else {
                $cell2 = $lbl . esc_html($sep) . implode(esc_html($del), $vals);
            }

            $rows[]   = ['cell1' => $lbl, 'cell2' => $cell2];
            $values[] = $cell2;
        }

        if ($os === 'table') {
            if (empty($rows)) {
                return;
            }
            $h1 = trim($s['table_header_1']);
            $h2 = trim($s['table_header_2']);
            echo '<table class="product-attributes-table">';
            if ($h1 !== '' || $h2 !== '') {
                echo '<thead><tr>';
                echo '<th>' . esc_html($h1) . '</th>';
                echo '<th>' . esc_html($h2) . '</th>';
                echo '</tr></thead>';
            }
            echo '<tbody>';
            foreach ($rows as $r) {
                echo '<tr>';
                echo '<td>' . wp_kses_post($r['cell1']) . '</td>';
                echo '<td>' . wp_kses_post($r['cell2']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } elseif ($os === 'ul' || $os === 'ol') {
            echo '<' . esc_attr($os) . '>';
            foreach ($values as $it) {
                echo '<li>' . wp_kses_post($it) . '</li>';
            }
            echo '</' . esc_attr($os) . '>';
        } else {
            echo implode(esc_html($del), array_map('wp_kses_post', $values));
        }
    }
}