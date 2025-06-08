<?php
namespace HelloWP\HWEleWooDynamic\Modules\Finder;

use Elementor\Core\Common\Modules\Finder\Base_Category;

class WooCommerceManagementCategory extends Base_Category {
    public function get_id() {
        return 'woocommerce-management';
    }

    public function get_title() {
        return esc_html__('WooCommerce Management', 'hw-ele-woo-dynamic');
    }

    public function get_category_items(array $options = []) {
        return [
            'create-new-product' => [
                'title' => esc_html__('Create New Product', 'hw-ele-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('post-new.php?post_type=product'),
                'keywords' => [
                    __('create', 'hw-ele-woo-dynamic'), 
                    __('product', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic'), 
                    __('woocommerce', 'hw-ele-woo-dynamic')
                ]
            ],
            'create-new-product-category' => [
                'title' => esc_html__('Create New Product Category', 'hw-ele-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('edit-tags.php?taxonomy=product_cat&post_type=product'),
                'keywords' => [
                    __('category', 'hw-ele-woo-dynamic'), 
                    __('product', 'hw-ele-woo-dynamic'), 
                    __('create', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic'), 
                    __('woocommerce', 'hw-ele-woo-dynamic')
                ]
            ],
            'create-new-product-tag' => [
                'title' => esc_html__('Create New Product Tag', 'hw-ele-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('edit-tags.php?taxonomy=product_tag&post_type=product'),
                'keywords' => [
                    __('tag', 'hw-ele-woo-dynamic'), 
                    __('product', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic'), 
                    __('woocommerce', 'hw-ele-woo-dynamic')
                ]
            ],
            'create-new-product-attribute' => [
                'title' => esc_html__('Create New Product Attribute', 'hw-ele-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('edit.php?post_type=product&page=product_attributes'),
                'keywords' => [
                    __('attribute', 'hw-ele-woo-dynamic'), 
                    __('product', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic'), 
                    __('woocommerce', 'hw-ele-woo-dynamic')
                ]
            ],
            'create-new-coupon' => [
                'title' => esc_html__('Create New Coupon', 'hw-ele-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('post-new.php?post_type=shop_coupon'),
                'keywords' => [
                    __('coupon', 'hw-ele-woo-dynamic'), 
                    __('discount', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic'), 
                    __('woocommerce', 'hw-ele-woo-dynamic')
                ]
            ],
            'create-new-shipping-zone' => [
                'title' => esc_html__('Create New Shipping Zone', 'hw-ele-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('admin.php?page=wc-settings&tab=shipping&zone_id=new'),
                'keywords' => [
                    __('zone', 'hw-ele-woo-dynamic'), 
                    __('shipping', 'hw-ele-woo-dynamic'), 
                    __('create', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic'), 
                    __('woocommerce', 'hw-ele-woo-dynamic')
                ]
            ],
            'create-new-shipping-class' => [
                'title' => esc_html__('Create New Shipping Class', 'hw-ele-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('admin.php?page=wc-settings&tab=shipping&section=classes'),
                'keywords' => [
                    __('class', 'hw-ele-woo-dynamic'), 
                    __('shipping', 'hw-ele-woo-dynamic'), 
                    __('create', 'hw-ele-woo-dynamic'), 
                    __('add new', 'hw-ele-woo-dynamic'), 
                    __('woocommerce', 'hw-ele-woo-dynamic')
                ]
            ],        
            'create-new-order' => [
                'title' => esc_html__('Create New Order', 'hw-ele-woo-dynamic'),
                'icon' => 'edit',
                'url' => admin_url('post-new.php?post_type=shop_order'),
                'keywords' => [
                    __('create', 'hw-ele-woo-dynamic'), 
                    __('order', 'hw-ele-woo-dynamic'), 
                    __('new', 'hw-ele-woo-dynamic'), 
                    __('woocommerce', 'hw-ele-woo-dynamic')
                ]
            ]
        ];
    }
}