<?php

namespace HelloWP\HWEleWooDynamic\Modules\JEQuery;

class WCOrderHposQueryEditor extends \Jet_Engine\Query_Builder\Query_Editor\Base_Query
{
    public function get_id()
    {
        return QueryManager::get_slug();
    }

    public function get_name()
    {
        return __('WC Order HPOS Query', 'hw-ele-woo-dynamic');
    }

    public function editor_component_name()
    {
        return 'jet-wc-order-hpos-query';
    }

    public function editor_component_data()
    {
        $options = [];
        $statuses = function_exists('wc_get_order_statuses') ? wc_get_order_statuses() : [];

        foreach ($statuses as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return [
            'statuses' => $options,
            'default_per_page' => absint(get_option('posts_per_page', 10)),
            'payment_methods' => $this->get_payment_methods_for_js(),
        ];
    }

    public function editor_component_template()
    {
        ob_start();
        include HW_ELE_DYNAMIC_PATH . 'modules/je-query/templates/admin/query-editor.php';
        return ob_get_clean();
    }

    public function editor_component_file()
    {
        return HW_ELE_DYNAMIC_URL . 'modules/je-query/assets/js/query-editor.js';
    }

    private function get_payment_methods_for_js(): array
    {
        if (! function_exists('WC')) {
            return [];
        }

        $gateways = WC()->payment_gateways();

        if (! $gateways) {
            return [];
        }

        $options = [];

        foreach ($gateways->payment_gateways() as $gateway_id => $gateway) {
            $title = $gateway->get_title();

            $options[] = [
                'value' => (string) $gateway_id,
                'label' => $title ? (string) $title : (string) $gateway_id,
            ];
        }

        return $options;
    }
}
