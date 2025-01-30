<?php

namespace HelloWP\HWEleWooDynamic\Modules\WPTopBar;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class TopBarSettings {
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_before_admin_bar_render', [$this, 'add_product_info_to_admin_bar'], 100);
        if (\HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies::is_learndash_active()) {
            add_action('wp_before_admin_bar_render', [$this, 'add_learndash_info_to_admin_bar'], 110);
        }
    }

    public function add_product_info_to_admin_bar() {
        if (!is_product() || !is_user_logged_in() || !current_user_can('manage_woocommerce')) {
            return;
        }

        global $wp_admin_bar, $product;
        $product_id = get_the_ID();
        $product = wc_get_product($product_id);
        if (!$product) {
            return;
        }

        $options = get_option('dynamic_extension_settings');
        $enabled_tags = $options['enabled_tags'] ?? [];

        $shouldAddProductInfo = !empty($enabled_tags['wp_bar_products_informations_product_type']) || !empty($enabled_tags['wp_bar_products_informations_product_sku']);

        if ($shouldAddProductInfo) {
            $wp_admin_bar->add_node([
                'id'    => 'product_info',
                'title' => __('Product Info', 'hw-ele-woo-dynamic')
            ]);
        }

        // Product Type
        if (!empty($enabled_tags['wp_bar_products_informations_product_type'])) {
            $product_types = wc_get_product_types();
            $product_type_label = $product_types[$product->get_type()] ?? __('Unknown', 'woocommerce');
            $wp_admin_bar->add_node([
                'parent' => 'product_info',
                'id'     => 'product_type',
                'title'  => sprintf(__('Type: %s', 'hw-ele-woo-dynamic'), $product_type_label),
                'href'   => false
            ]);
        }

        // Product SKU
        if (!empty($enabled_tags['wp_bar_products_informations_product_sku'])) {
            $sku_label = __('SKU', 'woocommerce');
            $sku = $product->get_sku() ?: __('N/A', 'woocommerce');
            $wp_admin_bar->add_node([
                'parent' => 'product_info',
                'id'     => 'product_sku',
                'title'  => sprintf(__('%s: %s', 'hw-ele-woo-dynamic'), $sku_label, $sku),
                'href'   => false
            ]);
        }

        $this->add_inventory_node($wp_admin_bar, $product, $enabled_tags);
        $this->add_shipping_class_node($wp_admin_bar, $product, $enabled_tags);
        $this->add_product_status_node($wp_admin_bar, $product, $enabled_tags);
    }

        private function add_shipping_class_node($wp_admin_bar, $product, $enabled_tags) {
            if (!empty($enabled_tags['wp_bar_products_informations_shipping_class']) && !$wp_admin_bar->get_node('shipping_class')) {
                $shipping_class = $product->get_shipping_class() ?: __('No shipping class', 'woocommerce');
                $shipping_class_label = __('Shipping Class', 'hw-ele-woo-dynamic');
                $wp_admin_bar->add_node([
                    'id'     => 'shipping_class',
                    'title'  => sprintf(__('%s: %s', 'hw-ele-woo-dynamic'), $shipping_class_label, $shipping_class),
                    'href'   => false
                ]);
            }
        }

        private function add_product_status_node($wp_admin_bar, $product, $enabled_tags) {
            if (!empty($enabled_tags['wp_bar_products_informations_product_status'])) {
                $product_status = get_post_status($product->get_id());
                $statuses = get_post_statuses();
                $status_label = $statuses[$product_status] ?? __('Unknown', 'woocommerce');
                $product_status_label = __('Product Status', 'hw-ele-woo-dynamic');
        
                $wp_admin_bar->add_node([
                    'id'     => 'product_status',
                    'title'  => sprintf(__('%s: %s', 'hw-ele-woo-dynamic'), $product_status_label, $status_label),
                    'href'   => false
                ]);
            }
        }
    
        private function add_inventory_node($wp_admin_bar, $product, $enabled_tags) {
            if (!empty($enabled_tags['wp_bar_products_informations_product_inventory'])) {
                $low_stock_amount = $product->get_low_stock_amount();
                if ($low_stock_amount === '') {
                    $low_stock_amount = get_option('woocommerce_notify_low_stock_amount');
                }
                $stock_quantity = $product->get_stock_quantity();
                $stock_status = $product->get_stock_status();
                $color = ($stock_status === 'outofstock') ? '#E53935' : ($stock_status === 'onbackorder' ? '#FFB300' : '#43A047');
                $status_text = ($stock_status === 'outofstock') ? __('Out of stock', 'woocommerce') : __('In stock', 'woocommerce');
                if ($stock_status === 'onbackorder') {
                    $status_text = __('On backorder', 'woocommerce');
                } elseif ($stock_quantity !== null && $stock_quantity <= $low_stock_amount) {
                    $status_text = __('Low stock', 'woocommerce') . " ($stock_quantity)";
                } elseif ($stock_quantity > $low_stock_amount || $stock_quantity > 0) {
                    $status_text = __('Stock', 'woocommerce') . " ($stock_quantity)";
                }

                $wp_admin_bar->add_node([
                    'id'     => 'product_stock_status',
                    'title'  => "<div style='background-color: $color; color: #FFFFFF; padding-left: 5px; padding-right: 5px;'>$status_text</div>",
                    'href'   => false
                ]);
            }
        }

    /**
     * LearnDash details
     */
    public function add_learndash_info_to_admin_bar() {
        if (!is_user_logged_in() || !current_user_can('edit_posts')) {
            return;
        }

        if (!is_singular(array('sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz'))) {
            return;
        }

        $options = get_option('dynamic_extension_settings');
        $enabled_tags = $options['enabled_tags'] ?? [];

        $has_ld_bar = (
            !empty($enabled_tags['wp_bar_learndash_informations_ld_bar_lessons']) ||
            !empty($enabled_tags['wp_bar_learndash_informations_ld_bar_topics']) ||
            !empty($enabled_tags['wp_bar_learndash_informations_ld_bar_quizes'])
        );

        if (!$has_ld_bar) {
            return; 
        }

        global $wp_admin_bar;
        $wp_admin_bar->add_node([
            'id'    => 'learndash_course_info',
            'title' => __('Course Info', 'hw-ele-woo-dynamic'),
            'href'  => false,
        ]);

        $post_id = get_the_ID();
        $course_id = $post_id;
        if (function_exists('learndash_get_course_id')) {
            $course_id = learndash_get_course_id($post_id);
            if (empty($course_id)) {
                $course_id = $post_id;
            }
        }

        if (!empty($enabled_tags['wp_bar_learndash_informations_ld_bar_lessons'])) {
            $this->add_learndash_lessons_submenu($wp_admin_bar, $course_id);
        }

        if (!empty($enabled_tags['wp_bar_learndash_informations_ld_bar_topics'])) {
            $this->add_learndash_topics_submenu($wp_admin_bar, $course_id);
        }

        if (!empty($enabled_tags['wp_bar_learndash_informations_ld_bar_quizes'])) {
            $this->add_learndash_quizzes_submenu($wp_admin_bar, $course_id);
        }
    }

    /**
     * Lessons
     */
    private function add_learndash_lessons_submenu($wp_admin_bar, $course_id) {
        $wp_admin_bar->add_node([
            'parent' => 'learndash_course_info',
            'id'     => 'learndash_lessons',
            'title'  => __('Lessons', 'hw-ele-woo-dynamic'),
            'href'   => false,
        ]);

        $args = [
            'post_type'      => 'sfwd-lessons',
            'posts_per_page' => -1,
            'order'          => 'ASC',
            'orderby'        => 'menu_order',
            'meta_query'     => [
                [
                    'key'   => 'course_id', 
                    'value' => $course_id
                ]
            ]
        ];
        $lessons = get_posts($args);

        if (!empty($lessons)) {
            foreach ($lessons as $lesson) {
                $lesson_id = $lesson->ID;
                $title     = get_the_title($lesson_id);
                
                $wp_admin_bar->add_node([
                    'parent' => 'learndash_lessons',
                    'id'     => 'learndash_lesson_' . $lesson_id,
                    'title'  => $title,
                    'href'   => get_edit_post_link($lesson_id),
                    'meta'   => ['target' => '_blank'],
                ]);
            }
        } else {
            $wp_admin_bar->add_node([
                'parent' => 'learndash_lessons',
                'id'     => 'no_lessons_found',
                'title'  => __('No lessons found.', 'hw-ele-woo-dynamic'),
                'href'   => false,
            ]);
        }
    }

    /**
     * Topics
     */
    private function add_learndash_topics_submenu($wp_admin_bar, $course_id) {
        $wp_admin_bar->add_node([
            'parent' => 'learndash_course_info',
            'id'     => 'learndash_topics',
            'title'  => __('Topics', 'hw-ele-woo-dynamic'),
            'href'   => false,
        ]);

        $args = [
            'post_type'      => 'sfwd-topic',
            'posts_per_page' => -1,
            'order'          => 'ASC',
            'orderby'        => 'menu_order',
            'meta_query'     => [
                [
                    'key'   => 'course_id', 
                    'value' => $course_id
                ]
            ]
        ];
        $topics = get_posts($args);

        if (!empty($topics)) {
            foreach ($topics as $topic) {
                $topic_id = $topic->ID;
                $title    = get_the_title($topic_id);
                
                $wp_admin_bar->add_node([
                    'parent' => 'learndash_topics',
                    'id'     => 'learndash_topic_' . $topic_id,
                    'title'  => $title,
                    'href'   => get_edit_post_link($topic_id),
                    'meta'   => ['target' => '_blank'],
                ]);
            }
        } else {
            $wp_admin_bar->add_node([
                'parent' => 'learndash_topics',
                'id'     => 'no_topics_found',
                'title'  => __('No topics found.', 'hw-ele-woo-dynamic'),
                'href'   => false,
            ]);
        }
    }

    /**
     * Quiz
     */
    private function add_learndash_quizzes_submenu($wp_admin_bar, $course_id) {
        $wp_admin_bar->add_node([
            'parent' => 'learndash_course_info',
            'id'     => 'learndash_quizzes',
            'title'  => __('Quizzes', 'hw-ele-woo-dynamic'),
            'href'   => false,
        ]);

        $args = [
            'post_type'      => 'sfwd-quiz',
            'posts_per_page' => -1,
            'order'          => 'ASC',
            'orderby'        => 'menu_order',
            'meta_query'     => [
                [
                    'key'   => 'course_id', 
                    'value' => $course_id
                ]
            ]
        ];
        $quizzes = get_posts($args);

        if (!empty($quizzes)) {
            foreach ($quizzes as $quiz) {
                $quiz_id = $quiz->ID;
                $title   = get_the_title($quiz_id);
                
                $wp_admin_bar->add_node([
                    'parent' => 'learndash_quizzes',
                    'id'     => 'learndash_quiz_' . $quiz_id,
                    'title'  => $title,
                    'href'   => get_edit_post_link($quiz_id),
                    'meta'   => ['target' => '_blank'],
                ]);
            }
        } else {
            $wp_admin_bar->add_node([
                'parent' => 'learndash_quizzes',
                'id'     => 'no_quizzes_found',
                'title'  => __('No quizzes found.', 'hw-ele-woo-dynamic'),
                'href'   => false,
            ]);
        }
    }
    
}
