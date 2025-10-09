<?php
namespace HelloWP\HWEleWooDynamic\Modules\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widgets_Manager;
use Elementor\Elements_Manager;
use HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic\DynamicAddToCalendarWidget;
use HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic\DynamicAddToCartWidget;
use HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic\DynamicCalculationWidget;
use HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic\DynamicCheckboxWidget;
use HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic\DynamicOpeningHoursWidget;
use HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic\MemberShipCardWidget;
use HelloWP\HWEleWooDynamic\Modules\Helpers\Dependencies;    

class WidgetManager {

    public function __construct() {
        add_action( 'elementor/widgets/register', [ $this, 'register_new_widgets' ], 20 );
        add_action( 'elementor/elements/categories_registered', [ $this, 'add_widget_category' ] );
        add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_widget_styles' ] );
    }

    public function add_widget_category( Elements_Manager $elements_manager ) {
        $elements_manager->add_category(
            'dynamic-elements',
            [
                'title' => __( 'Dynamic Elements', 'hw-elementor-woo-dynamic' ),
                'icon'  => 'fa fa-plug',
            ]
        );
    }

    /**
     * Register new widgets
     *
     * @param Widgets_Manager $widgets_manager
     */
    public function register_new_widgets( Widgets_Manager $widgets_manager ) {
        if ( class_exists( \HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic\DynamicCheckboxWidget::class ) ) {
            $widgets_manager->register( new DynamicCheckboxWidget() );
        }

        if ( class_exists( \HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic\DynamicCalculationWidget::class ) ) {
            $widgets_manager->register( new DynamicCalculationWidget() );
        }

        if ( Dependencies::is_memberships_active()
        && class_exists( \HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic\MemberShipCardWidget::class )
        ) {
            $widgets_manager->register( new MemberShipCardWidget() );
        }

        if ( Dependencies::is_woocommerce_active()
        && class_exists( \HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic\DynamicAddToCartWidget::class )
        ) {
            $widgets_manager->register( new DynamicAddToCartWidget() );
        }

        if ( class_exists( \HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic\DynamicAddToCalendarWidget::class ) ) {
            $widgets_manager->register( new DynamicAddToCalendarWidget() );
        }

        if ( class_exists( \HelloWP\HWEleWooDynamic\Modules\Widgets\Dynamic\DynamicOpeningHoursWidget::class ) ) {
            $widgets_manager->register( new DynamicOpeningHoursWidget() );
        }
    }


    public function register_widget_styles() {
        wp_register_style(
            'hw-ele-woo-dynamic-css',
            plugins_url( 'widgets/assets/dynamic-extension.css', __DIR__ ),
            [],       
            '1.0.0'
        );
    }
}
