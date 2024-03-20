<?php

namespace HelloWP\HWEleWooDynamic;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Manager;

class TagManager {
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('elementor/dynamic_tags/register_tags', [$this, 'register_tags']);
    }

    public function register_tags(Manager $dynamic_tags) {
        $dynamic_tags->register_group('woo-extras', [
            'title' => __('Woo Extras', 'hw-ele-woo-dynamic')
        ]);

        $dynamic_tags->register_group('woo-extras-user', [
            'title' => __('Woo Extras User', 'hw-ele-woo-dynamic')
        ]);

        // Woo Dynamic tags
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\AdvancedStock');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\AdvancedSaleBadge');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\CartValues');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\CartTaxValues');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\FreeShippingAmount');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\ProductHeight');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\ProductWidth');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\ProductWeight');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\ProductAttributes');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\SaleTime');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\StockQuantityExtra');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\SpecBadge');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\StockQuantity');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\FeaturedBadge');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\ProductGallery');

        // Customer Dynamic tags
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\CustomerDetails');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\CompletedOrder');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\TotalSpent');
        $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\LastOrder');

        if (class_exists('WC_Memberships')) {
            $dynamic_tags->register_tag('HelloWP\HWEleWooDynamic\WooTags\Membership\ActiveMembership');
        }
    }
}


