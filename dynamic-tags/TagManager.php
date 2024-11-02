<?php

namespace HelloWP\HWEleWooDynamic;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Manager;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies; 

class TagManager {
    private static $instance = null;

    /**
     * Singleton method to ensure only one instance of the class is loaded.
     *
     * @return TagManager
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor to register actions.
     */
    private function __construct() {
        add_action('elementor/dynamic_tags/register_tags', [$this, 'register_tags']);
    }

    /**
 * Registers custom dynamic tags for Elementor.
 *
 * @param Manager $dynamic_tags
 */
public function register_tags(Manager $dynamic_tags) {
    $options = get_option('dynamic_extension_settings');
    $enabled_tags = $options['enabled_tags'] ?? [];

    // Register groups for WooCommerce and LearnDash tags
    $dynamic_tags->register_group('woo-extras', [
        'title' => __('Woo Extras', 'hw-ele-woo-dynamic')
    ]);

    $dynamic_tags->register_group('woo-extras-user', [
        'title' => __('Woo Extras User', 'hw-ele-woo-dynamic')
    ]);

    $dynamic_tags->register_group('ld_extras_courses', [
        'title' => __('LearnDash', 'hw-ele-woo-dynamic')
    ]);

    $dynamic_tags->register_group('ld_extras_global', [
        'title' => __('LearnDash Global', 'hw-ele-woo-dynamic')
    ]);

    // Iterate over enabled tags and register them
    foreach ($enabled_tags as $tag_key => $value) {
        if (!empty($value)) {
            // Use Dependencies class for checking plugin requirements
            if ((strpos($tag_key, 'woo_subscriptions') !== false && !Dependencies::is_subscriptions_active()) ||
                (strpos($tag_key, 'woo_membership') !== false && !Dependencies::is_memberships_active()) ||
                ((strpos($tag_key, 'ld_extras_courses') !== false || strpos($tag_key, 'ld_extras_global') !== false) && !Dependencies::is_learndash_active())) {
                continue;
            }

            $tag_class = $this->map_tag_class($tag_key);
            if ($tag_class) {
                $dynamic_tags->register_tag($tag_class);
            }
        }
    }
}

    

    /**
     * Maps tag keys to their corresponding class names.
     *
     * @param string $tag_key
     * @return string|null
     */
    private function map_tag_class($tag_key) {
        $map = [
            'woo_extras_advanced_stock' => 'HelloWP\HWEleWooDynamic\WooTags\AdvancedStock',
            'woo_extras_advanced_sale_badge' => 'HelloWP\HWEleWooDynamic\WooTags\AdvancedSaleBadge',
            'woo_extras_featured_badge' => 'HelloWP\HWEleWooDynamic\WooTags\FeaturedBadge',
            'woo_extras_product_attributes' => 'HelloWP\HWEleWooDynamic\WooTags\ProductAttributes',
            'woo_extras_product_description' => 'HelloWP\HWEleWooDynamic\WooTags\ProductDescription',
            'woo_extras_product_height' => 'HelloWP\HWEleWooDynamic\WooTags\ProductHeight',
            'woo_extras_product_lenght' => 'HelloWP\HWEleWooDynamic\WooTags\ProductLength',
            'woo_extras_product_shipping_class' => 'HelloWP\HWEleWooDynamic\WooTags\ProductShippingClass',
            'woo_extras_product-tabs' => 'HelloWP\HWEleWooDynamic\WooTags\ProductTabs',
            'woo_extras_product_width' => 'HelloWP\HWEleWooDynamic\WooTags\ProductWidth',
            'woo_extras_product_weight' => 'HelloWP\HWEleWooDynamic\WooTags\ProductWeight',
            'woo_extras_purchased_badge' => 'HelloWP\HWEleWooDynamic\WooTags\PurchasedBadge',
            'woo_extras_sale_time' => 'HelloWP\HWEleWooDynamic\WooTags\SaleTime',
            'woo_extras_stock_quantity_extra' => 'HelloWP\HWEleWooDynamic\WooTags\StockQuantityExtra',
            'woo_extras_spec_badge' => 'HelloWP\HWEleWooDynamic\WooTags\SpecBadge',
            'woo_extras_stock_quantity' => 'HelloWP\HWEleWooDynamic\WooTags\StockQuantity',
            'woo_extras_product_gallery' => 'HelloWP\HWEleWooDynamic\WooTags\ProductGallery',
            'woo_extras_next_product' => 'HelloWP\HWEleWooDynamic\WooTags\NextProduct',
            'woo_extras_next_product_image' => 'HelloWP\HWEleWooDynamic\WooTags\NextProductImage',
            'woo_extras_prev_product' => 'HelloWP\HWEleWooDynamic\WooTags\PreviousProduct',
            'woo_extras_prev_product_image' => 'HelloWP\HWEleWooDynamic\WooTags\PreviousProductImage',
            'woo_extras_taxonomy-acf-meta' => 'HelloWP\HWEleWooDynamic\WooTags\TaxonomyACFMeta',
            'woo_extras_variable-price-range' => 'HelloWP\HWEleWooDynamic\WooTags\VariablePrice',

            'global_advanced_product_category' => 'HelloWP\HWEleWooDynamic\WooTags\AdvancedProductCategory',
            'global_free_shipping_amount' => 'HelloWP\HWEleWooDynamic\WooTags\FreeShippingAmount',

            'cart_value_cart_values' => 'HelloWP\HWEleWooDynamic\WooTags\CartValues',
            'cart_value_cart_tax_values' => 'HelloWP\HWEleWooDynamic\WooTags\CartTaxValues',

            'customer_specific_customer_details' => 'HelloWP\HWEleWooDynamic\WooTags\CustomerDetails',
            'customer_specific_completed_order' => 'HelloWP\HWEleWooDynamic\WooTags\CompletedOrder',
            'customer_specific_purchased_products' => 'HelloWP\HWEleWooDynamic\WooTags\PurchasedProducts',
            'customer_specific_total_spent' => 'HelloWP\HWEleWooDynamic\WooTags\TotalSpent',
            'customer_specific_my_account_endpoints' => 'HelloWP\HWEleWooDynamic\WooTags\MyAccountEndpoints',
            'customer_specific_last_order' => 'HelloWP\HWEleWooDynamic\WooTags\LastOrder',
            'customer_specific_user_role' => 'HelloWP\HWEleWooDynamic\WooTags\UserRole',
            'customer_specific_customer_logout' => 'HelloWP\HWEleWooDynamic\WooTags\CustomerLogout',

            'woo_membership_active_membership' => 'HelloWP\HWEleWooDynamic\WooTags\Membership\ActiveMembership',
            'woo_membership_my_account_membershipLink' => 'HelloWP\HWEleWooDynamic\WooTags\Membership\MyAccountMembershipLink',
            'woo_membership_active_membership_data' => 'HelloWP\HWEleWooDynamic\WooTags\Membership\ActiveMembershipData',
            'woo_membership_current_membership_data' => 'HelloWP\HWEleWooDynamic\WooTags\Membership\CurrentMembershipData',
            'woo_membership_restricted_products_view' => 'HelloWP\HWEleWooDynamic\WooTags\Membership\RestrictedProductsView',

            'woo_subscriptions_active_subscription' => 'HelloWP\HWEleWooDynamic\WooTags\Subscription\ActiveSubscription',
            'woo_subscriptions_my_account_subscriptionlink' => 'HelloWP\HWEleWooDynamic\WooTags\Subscription\MyAccountSubscriptionLink',
            'woo_subscriptions_active_subscription_data' => 'HelloWP\HWEleWooDynamic\WooTags\Subscription\ActiveSubscriptionData',

            'ld_extras_courses_ld_lessons' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\LessonsNumber',
            'ld_extras_courses_ld_quiz' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\QuizNumbers',
            'ld_extras_courses_awarded_points' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\AwardedPoints',
            'ld_extras_courses_enrolled_users_count' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\EnrolledUsersCount',
            'ld_extras_courses_last-activity' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\LastActivity',
            'ld_extras_courses_progress-percentage' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\CourseProgress',
            'ld_extras_courses_user-course-status' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\CourseStatus',
            'ld_extras_courses_access-expires' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\AccessExpires',
            'ld_extras_courses_course-materials' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\CourseMaterials',
            'ld_extras_courses_course-access-type' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\CourseAccessType',
            'ld_extras_courses_student-limit' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\StudentLimit',
            'ld_extras_courses_course-start-date' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\CourseStartDate',
            'ld_extras_courses_required-points' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\RequiredPoints',
            'ld_extras_courses_course-price' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\CoursePrice',
            'ld_extras_courses_topics-counter' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\TopicsCounter',
            'ld_extras_courses_certificates-link' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\CourseCertificatesLink',
            'ld_extras_courses_course-prerequisites-list' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\CoursePrerequisitesList',
            'ld_extras_courses_course-resume-text' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\CourseResumeText',
            'ld_extras_courses_course-resume' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\CourseResumeLink',
            'ld_extras_courses_course-content' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\CourseContent',
            'ld_extras_courses_course-groups' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\CoursePartOfGroup',

            'ld_extras_global_user-completed-courses-count' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\UserCompletedCourse',
            'ld_extras_global_user-course-points' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\UserCoursePoints',
            'ld_extras_global_user-available-courses-count' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\UserAvailableCoursesCount',
            'ld_extras_global_user-achieved-certificates-count' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\UserAchievedCertificatesCount',
            'ld_extras_global_user-groups-count' => 'HelloWP\HWEleWooDynamic\WooTags\LearnDash\UserGroupsCount',
        ];

        return $map[$tag_key] ?? null;
    }
}
