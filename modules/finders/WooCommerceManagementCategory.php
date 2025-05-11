<?php
namespace HelloWP\HWEleWooDynamic\Modules\Finder;

use Elementor\Core\Common\Modules\Finder\Base_Category;

class WooCommerceManagementCategory extends Base_Category {
    public function get_id() {
        return 'woocommerce-management';
    }

    public function get_title() {
        return esc_html__('WooCommerce Management', 'hw-elementor-woo-dynamic');
    }

    public function get_category_items(array $options = []) {
        return [
            'create-new-product' => [
                'title' => esc_html__('Create New Product', 'hw-elementor-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('post-new.php?post_type=product'),
                'keywords' => [
                    __('create', 'hw-elementor-woo-dynamic'), 
                    __('product', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic'), 
                    __('woocommerce', 'hw-elementor-woo-dynamic')
                ]
            ],
            'create-new-product-category' => [
                'title' => esc_html__('Create New Product Category', 'hw-elementor-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('edit-tags.php?taxonomy=product_cat&post_type=product'),
                'keywords' => [
                    __('category', 'hw-elementor-woo-dynamic'), 
                    __('product', 'hw-elementor-woo-dynamic'), 
                    __('create', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic'), 
                    __('woocommerce', 'hw-elementor-woo-dynamic')
                ]
            ],
            'create-new-product-tag' => [
                'title' => esc_html__('Create New Product Tag', 'hw-elementor-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('edit-tags.php?taxonomy=product_tag&post_type=product'),
                'keywords' => [
                    __('tag', 'hw-elementor-woo-dynamic'), 
                    __('product', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic'), 
                    __('woocommerce', 'hw-elementor-woo-dynamic')
                ]
            ],
            'create-new-product-attribute' => [
                'title' => esc_html__('Create New Product Attribute', 'hw-elementor-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('edit.php?post_type=product&page=product_attributes'),
                'keywords' => [
                    __('attribute', 'hw-elementor-woo-dynamic'), 
                    __('product', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic'), 
                    __('woocommerce', 'hw-elementor-woo-dynamic')
                ]
            ],
            'create-new-coupon' => [
                'title' => esc_html__('Create New Coupon', 'hw-elementor-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('post-new.php?post_type=shop_coupon'),
                'keywords' => [
                    __('coupon', 'hw-elementor-woo-dynamic'), 
                    __('discount', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic'), 
                    __('woocommerce', 'hw-elementor-woo-dynamic')
                ]
            ],
            'create-new-shipping-zone' => [
                'title' => esc_html__('Create New Shipping Zone', 'hw-elementor-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('admin.php?page=wc-settings&tab=shipping&zone_id=new'),
                'keywords' => [
                    __('zone', 'hw-elementor-woo-dynamic'), 
                    __('shipping', 'hw-elementor-woo-dynamic'), 
                    __('create', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic'), 
                    __('woocommerce', 'hw-elementor-woo-dynamic')
                ]
            ],
            'create-new-shipping-class' => [
                'title' => esc_html__('Create New Shipping Class', 'hw-elementor-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('admin.php?page=wc-settings&tab=shipping&section=classes'),
                'keywords' => [
                    __('class', 'hw-elementor-woo-dynamic'), 
                    __('shipping', 'hw-elementor-woo-dynamic'), 
                    __('create', 'hw-elementor-woo-dynamic'), 
                    __('add new', 'hw-elementor-woo-dynamic'), 
                    __('woocommerce', 'hw-elementor-woo-dynamic')
                ]
            ],        
            'create-new-order' => [
                'title' => esc_html__('Create New Order', 'hw-elementor-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('post-new.php?post_type=shop_order'),
                'keywords' => [
                    __('create', 'hw-elementor-woo-dynamic'), 
                    __('order', 'hw-elementor-woo-dynamic'), 
                    __('new', 'hw-elementor-woo-dynamic'), 
                    __('woocommerce', 'hw-elementor-woo-dynamic')
                ]
            ]
        ];
    }
}