<?php

namespace HelloWP\HWEleWooDynamic\Modules\Helpers;

class CartAjaxRefresh {

    private static bool $initialized = false;

    public static function init(): void {
        if (self::$initialized) {
            return;
        }

        self::$initialized = true;

        add_action('wp_ajax_hw_refresh_cart_values', [__CLASS__, 'handle_refresh']);
        add_action('wp_ajax_nopriv_hw_refresh_cart_values', [__CLASS__, 'handle_refresh']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_scripts']);
    }

    public static function enqueue_scripts(): void {
        wp_enqueue_script(
            'hw-cart-ajax-refresh',
            HW_ELE_DYNAMIC_URL . 'assets/cart-ajax-refresh.js',
            ['jquery'],
            HW_ELE_DYNAMIC_VERSION,
            true
        );

        wp_localize_script('hw-cart-ajax-refresh', 'hw_cart_refresh', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('hw_cart_refresh'),
        ]);
    }

    private const ALLOWED_TAGS = ['cart-values', 'cart-tax-values', 'free-shipping-amount'];
    private const MAX_ITEMS    = 20;

    public static function handle_refresh(): void {
        check_ajax_referer('hw_cart_refresh', 'nonce');

        if (!function_exists('WC') || null === WC()->cart) {
            if (function_exists('wc_load_cart')) {
                wc_load_cart();
            } else {
                wp_send_json_error('WooCommerce not available', 400);
            }
        }

        $items   = isset($_POST['items']) ? wp_unslash($_POST['items']) : [];
        $items   = array_slice((array) $items, 0, self::MAX_ITEMS);
        $results = [];

        foreach ($items as $item) {
            $tag = sanitize_text_field($item['tag'] ?? '');

            if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                $results[] = '';
                continue;
            }

            $settings = self::sanitize_settings((array) ($item['settings'] ?? []));
            $results[] = self::render_value($tag, $settings);
        }

        wp_send_json_success($results);
    }

    /**
     * Sanitize settings array from client.
     * - Rejects non-string values (blocks nested array attacks)
     * - Sanitizes keys with sanitize_key
     * - Uses wp_kses_post for the template field (preserves safe HTML)
     * - Uses sanitize_text_field for all other fields
     */
    private static function sanitize_settings(array $raw): array {
        $clean = [];

        foreach ($raw as $key => $value) {
            if (!is_string($value)) {
                continue;
            }

            $key = sanitize_key($key);

            $clean[$key] = ('template' === $key)
                ? wp_kses_post($value)
                : sanitize_text_field($value);
        }

        return $clean;
    }

    private static function render_value(string $tag, array $settings): string {
        $cart = WC()->cart;

        if ('cart-values' === $tag) {
            $hide_empty = 'yes' === ($settings['hide_if_cart_empty'] ?? 'no');

            if ($hide_empty && $cart->is_empty()) {
                return '';
            }

            switch ($settings['cart_value_type'] ?? 'cart_total') {
                case 'return_items':
                    $names = [];
                    foreach ($cart->get_cart() as $values) {
                        $names[] = esc_html($values['data']->get_name());
                    }
                    return esc_html(implode($settings['delimiter'] ?? ', ', $names));

                case 'cart_total':
                    $include_tax = 'yes' === ($settings['include_tax'] ?? 'no');
                    $total = $include_tax
                        ? $cart->get_cart_contents_total() + $cart->get_cart_contents_tax()
                        : $cart->get_cart_contents_total();

                    if ('yes' === ($settings['show_price_symbol'] ?? 'no')) {
                        return wp_kses_post(wc_price($total));
                    }
                    return esc_html(number_format(
                        $total,
                        wc_get_price_decimals(),
                        wc_get_price_decimal_separator(),
                        wc_get_price_thousand_separator()
                    ));

                case 'items_count':
                    return (string) intval($cart->get_cart_contents_count());
            }
        }

        if ('cart-tax-values' === $tag) {
            $tax_total = floatval($cart->get_taxes_total());

            if ('yes' === ($settings['show_price_symbol'] ?? 'no')) {
                return wp_kses_post(wc_price($tax_total));
            }
            return wp_kses_post(number_format(
                $tax_total,
                wc_get_price_decimals(),
                wc_get_price_decimal_separator(),
                wc_get_price_thousand_separator()
            ));
        }

        if ('free-shipping-amount' === $tag) {
            $min_amount = self::get_free_shipping_min_amount();
            if ($min_amount === false) {
                return '';
            }

            $cart_total   = self::get_cart_subtotal_for_shipping();
            $remaining    = max(0, $min_amount - $cart_total);
            $template     = $settings['template'] ?? '';

            return wp_kses_post(str_replace(
                ['{freeamount}', '{remainingamount}'],
                [wc_price($min_amount), wc_price($remaining)],
                $template
            ));
        }

        return '';
    }

    /**
     * Get the minimum free shipping threshold across all shipping zones.
     *
     * @return float|false
     */
    public static function get_free_shipping_min_amount() {
        $amounts = [];

        foreach (\WC_Shipping_Zones::get_zones() as $zone) {
            foreach ($zone['shipping_methods'] as $method) {
                if ('free_shipping' === $method->id && 'yes' === $method->enabled && !empty($method->min_amount)) {
                    $amounts[] = (float) preg_replace('/[^0-9.]+/', '', $method->min_amount);
                }
            }
        }

        return !empty($amounts) ? min($amounts) : false;
    }

    /**
     * Get cart subtotal used for free shipping comparison (matches WC logic).
     */
    public static function get_cart_subtotal_for_shipping(): float {
        $cart  = WC()->cart;
        $total = $cart->get_displayed_subtotal();

        if ('incl' === $cart->get_tax_price_display_mode()) {
            $total -= $cart->get_discount_tax();
        }

        $total -= $cart->get_discount_total();

        return max(0.0, (float) $total);
    }
}
