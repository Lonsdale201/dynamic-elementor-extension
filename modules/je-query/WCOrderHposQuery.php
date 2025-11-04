<?php

namespace HelloWP\HWEleWooDynamic\Modules\JEQuery;

use WC_Order_Query;

class WCOrderHposQuery extends \Jet_Engine\Query_Builder\Queries\Base_Query
{
    private ?WC_Order_Query $current_query = null;

    /**
     * @var array{orders: array, total: int, max_num_pages: int}|null
     */
    private ?array $current_results = null;

    private bool $is_paginated = false;

    private int $items_per_page = 0;

    /** @var int[] */
    private array $include_products = [];

    /** @var int[] */
    private array $exclude_products = [];

    public function _get_items()
    {
        $results = $this->get_results();

        return $results['orders'];
    }

    public function get_items_total_count()
    {
        $cached = $this->get_cached_data('count');

        if (false !== $cached) {
            return $cached;
        }

        $results = $this->get_results();
        $total = $results['total'];

        $this->update_query_cache($total, 'count');

        return $total;
    }

    public function get_current_items_page()
    {
        if (! $this->is_paginated) {
            return 1;
        }

        $query = $this->get_current_order_query();
        $page = $query ? absint($query->get('page')) : 1;

        return $page > 0 ? $page : 1;
    }

    public function get_items_pages_count()
    {
        if ($this->is_paginated) {
            $results = $this->get_results();
            $pages = $results['max_num_pages'];

            return $pages > 0 ? $pages : 1;
        }

        $per_page = $this->get_items_per_page();
        $total = $this->get_items_total_count();

        if (! $per_page) {
            return 1;
        }

        return (int) ceil($total / $per_page);
    }

    public function get_items_page_count()
    {
        if (! $this->is_paginated) {
            return $this->get_items_total_count();
        }

        $total = $this->get_items_total_count();
        $per_page = $this->get_items_per_page();

        if (! $per_page) {
            return $total;
        }

        $pages = $this->get_items_pages_count();
        $current_page = $this->get_current_items_page();

        if ($current_page < $pages) {
            return $per_page;
        }

        $remainder = $total % $per_page;

        return $remainder > 0 ? $remainder : $per_page;
    }

    public function get_items_per_page()
    {
        if (! $this->items_per_page) {
            $this->get_query_args();
        }

        return $this->items_per_page;
    }

    public function set_filtered_prop($prop = '', $value = null)
    {
        switch ($prop) {
            case '_page':
            case 'page':
                $this->final_query['page'] = absint($value);
                break;
            case '_items_per_page':
            case 'limit':
            case 'per_page':
                $this->final_query['per_page'] = absint($value);
                break;
            case 'status':
            case 'statuses':
                $this->final_query['statuses'] = (array) $value;
                break;
            case 'customer':
                $this->final_query['customer'] = $value;
                break;
            case 'payment_methods':
                $this->final_query['payment_methods'] = (array) $value;
                break;
            case 'date_after':
                $this->final_query['date_after'] = $value;
                break;
            case 'date_before':
                $this->final_query['date_before'] = $value;
                break;
            case 'offset':
                $this->final_query['offset'] = $value;
                break;
            case 'include_products':
                $this->final_query['include_products'] = $this->normalize_product_ids($value);
                break;
            case 'exclude_products':
                $this->final_query['exclude_products'] = $this->normalize_product_ids($value);
                break;
            default:
                $this->merge_default_props($prop, $value);
                break;
        }

        $this->reset_query();
    }

    public function get_args_to_dynamic()
    {
        return ['customer', 'payment_methods', 'date_after', 'date_before', 'offset'];
    }

    public function get_args_to_explode()
    {
        return ['statuses'];
    }

    public function reset_query()
    {
        $this->current_query = null;
        $this->current_results = null;
    }

    public function get_query_args()
    {
        if (null === $this->final_query) {
            $this->setup_query();
        }

        $args = $this->final_query;

        $this->include_products = $this->parse_product_ids($args['include_products'] ?? '');
        $this->exclude_products = $this->parse_product_ids($args['exclude_products'] ?? '');

        $paginate = $this->prepare_paginate_flag($args['paginate'] ?? true);
        $per_page = $this->prepare_per_page($args['per_page'] ?? null);
        $offset   = $this->prepare_offset($args['offset'] ?? 0);

        if ($offset > 0) {
            $paginate = false;
        }

        if ($per_page === -1) {
            $paginate = false;
            $offset   = 0;
        }

        $query_args = [
            'paginate' => $paginate,
            'return'   => 'objects',
        ];

        if ($per_page !== -1) {
            $query_args['limit'] = $per_page;
        } else {
            $query_args['limit'] = -1;
        }

        if ($paginate) {
            $page = ! empty($args['page']) ? absint($args['page']) : 0;

            if (! $page && ! empty($args['paged'])) {
                $page = absint($args['paged']);
            }

            $query_args['page'] = $page > 0 ? $page : 1;
        } else {
            unset($query_args['page']);
        }

        if ($offset > 0) {
            $query_args['offset'] = $offset;
        }

        $query_args['status'] = $this->prepare_statuses($args['statuses'] ?? []);

        $payment_methods = $this->prepare_payment_methods($args['payment_methods'] ?? []);
        if (! empty($payment_methods)) {
            $query_args['payment_method'] = $payment_methods;
        }

        if (! empty($args['customer'])) {
            $query_args = array_merge($query_args, $this->prepare_customer_arg($args['customer']));
        }

        $date_filters = $this->prepare_date_filters(
            $args['date_after'] ?? '',
            $args['date_before'] ?? ''
        );

        if (! empty($date_filters)) {
            $query_args['date_query'] = array_merge($query_args['date_query'] ?? [], $date_filters);
        }

        if (! empty($args['orderby'])) {
            $query_args['orderby'] = sanitize_key($args['orderby']);
        }

        if (! empty($args['order'])) {
            $order = strtoupper($args['order']);
            $query_args['order'] = 'ASC' === $order ? 'ASC' : 'DESC';
        }

        $query_args = $this->apply_product_filters($query_args);

        $this->is_paginated = $paginate;
        $this->items_per_page = $per_page === -1 ? 0 : $per_page;

        /**
         * Filter query arguments passed into WC_Order_Query.
         *
         * @param array              $query_args Query arguments.
         * @param WCOrderHposQuery   $query      Current query instance.
         */
        return apply_filters('hw_ele_woo_dynamic/query-builder/wc-order-hpos/args', $query_args, $this);
    }

    private function get_current_order_query(): ?WC_Order_Query
    {
        if (null !== $this->current_query) {
            return $this->current_query;
        }

        $args = $this->get_query_args();

        $this->current_query = new WC_Order_Query($args);

        return $this->current_query;
    }

    private function get_results(): array
    {
        if (null !== $this->current_results) {
            return $this->current_results;
        }

        $query = $this->get_current_order_query();

        if (! $query) {
            $this->current_results = [
                'orders' => [],
                'total' => 0,
                'max_num_pages' => 0,
            ];

            return $this->current_results;
        }

        $raw = $query->get_orders();

        if ($this->is_paginated) {
            $orders = [];
            $total = 0;
            $pages = 0;

            if (is_object($raw)) {
                $orders = isset($raw->orders) && is_array($raw->orders) ? $raw->orders : [];
                $total = isset($raw->total) ? absint($raw->total) : count($orders);
                $pages = isset($raw->max_num_pages) ? absint($raw->max_num_pages) : 0;
            } elseif (is_array($raw)) {
                $orders = isset($raw['orders']) && is_array($raw['orders']) ? $raw['orders'] : [];
                $total = isset($raw['total']) ? absint($raw['total']) : count($orders);
                $pages = isset($raw['max_num_pages']) ? absint($raw['max_num_pages']) : 0;
            }

            $this->current_results = [
                'orders' => $orders,
                'total' => $total,
                'max_num_pages' => $pages,
            ];
        } else {
            $orders = is_array($raw) ? $raw : [];

            $this->current_results = [
                'orders' => $orders,
                'total' => count($orders),
                'max_num_pages' => 1,
            ];
        }

        return $this->current_results;
    }

    private function prepare_statuses($statuses): array
    {
        $available = function_exists('wc_get_order_statuses') ? array_keys(wc_get_order_statuses()) : [];

        if (empty($statuses)) {
            return $available;
        }

        $normalized = [];

        foreach ((array) $statuses as $status) {
            $status = is_string($status) ? trim($status) : '';

            if ('' === $status) {
                continue;
            }

            if (0 !== strpos($status, 'wc-')) {
                $status = 'wc-' . ltrim($status, ' -');
            }

            $status = sanitize_key($status);

            if (function_exists('wc_is_order_status') && ! wc_is_order_status($status)) {
                continue;
            }

            $normalized[] = $status;
        }

        $normalized = array_unique($normalized);

        return ! empty($normalized) ? $normalized : $available;
    }

    private function prepare_payment_methods($methods): array
    {
        if (empty($methods)) {
            return [];
        }

        $methods = array_filter(array_map(static function ($method) {
            if (is_array($method) && isset($method['value'])) {
                $method = $method['value'];
            }

            $method = is_string($method) || is_numeric($method) ? trim((string) $method) : '';

            return $method !== '' ? sanitize_key($method) : '';
        }, (array) $methods));

        return array_values(array_unique($methods));
    }

    private function prepare_customer_arg($value): array
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        $value = is_string($value) || is_numeric($value) ? trim((string) $value) : '';

        if ('' === $value || 'all' === strtolower($value)) {
            return [];
        }

        $aliases = ['current', 'current_user', 'current-user', 'current user', 'currentuser'];

        if (in_array(strtolower($value), $aliases, true)) {
            $user_id = get_current_user_id();

            return $user_id ? ['customer_id' => absint($user_id)] : [];
        }

        if (is_numeric($value)) {
            return ['customer_id' => absint($value)];
        }

        if (is_email($value)) {
            return ['customer' => sanitize_email($value)];
        }

        return ['customer' => sanitize_text_field($value)];
    }

    private function normalize_product_ids($value): string
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        if (! is_string($value)) {
            return '';
        }

        $ids = array_filter(array_map(static function ($id) {
            return absint(trim((string) $id));
        }, preg_split('/[\s,]+/', $value, -1, PREG_SPLIT_NO_EMPTY)));

        $ids = array_values(array_unique(array_filter($ids)));

        return implode(',', $ids);
    }

    /**
     * @param string|array $value
     * @return int[]
     */
    private function parse_product_ids($value): array
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        if (! is_string($value) || '' === trim($value)) {
            return [];
        }

        $ids = array_filter(array_map(static function ($id) {
            return absint(trim((string) $id));
        }, preg_split('/[\s,]+/', $value, -1, PREG_SPLIT_NO_EMPTY)));

        return array_values(array_unique(array_filter($ids)));
    }

    private function apply_product_filters(array $query_args): array
    {
        if (empty($this->include_products) && empty($this->exclude_products)) {
            return $query_args;
        }

        global $wpdb;

        if (! empty($this->include_products)) {
            $order_ids = $this->get_order_ids_for_products($this->include_products, $wpdb);

            if (empty($order_ids)) {
                $query_args['post__in'] = [0];
            } else {
                $existing = isset($query_args['post__in']) ? array_map('absint', (array) $query_args['post__in']) : [];

                if (! empty($existing)) {
                    $order_ids = array_values(array_intersect($existing, $order_ids));
                }

                $query_args['post__in'] = ! empty($order_ids) ? $order_ids : [0];
            }
        }

        if (! empty($this->exclude_products)) {
            $order_ids = $this->get_order_ids_for_products($this->exclude_products, $wpdb);

            if (! empty($order_ids)) {
                $existing = isset($query_args['post__not_in']) ? array_map('absint', (array) $query_args['post__not_in']) : [];
                $query_args['post__not_in'] = array_values(array_unique(array_merge($existing, $order_ids)));
            }
        }

        return $query_args;
    }

    /**
     * @param int[] $product_ids
     */
    private function get_order_ids_for_products(array $product_ids, \wpdb $wpdb): array
    {
        if (empty($product_ids)) {
            return [];
        }

        $product_ids = array_values(array_unique(array_filter(array_map('absint', $product_ids))));

        if (empty($product_ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($product_ids), '%d'));
        $table_items   = $wpdb->prefix . 'woocommerce_order_items';
        $table_meta    = $wpdb->prefix . 'woocommerce_order_itemmeta';

        $sql = "SELECT DISTINCT oi.order_id
            FROM {$table_items} AS oi
            INNER JOIN {$table_meta} AS oim ON oi.order_item_id = oim.order_item_id
            WHERE oi.order_item_type = 'line_item'
            AND (
                (oim.meta_key = '_product_id' AND oim.meta_value IN ({$placeholders}))
                OR (oim.meta_key = '_variation_id' AND oim.meta_value IN ({$placeholders}))
            )";

        $prepared = $wpdb->prepare($sql, array_merge($product_ids, $product_ids));
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $results = $wpdb->get_col($prepared);

        return array_values(array_unique(array_map('absint', $results)));
    }

    private function prepare_date_filters($after, $before): array
    {
        $after = $this->normalize_date_input($after, 'start');
        $before = $this->normalize_date_input($before, 'end');

        $filter = [ 'column' => 'date_created_gmt', 'inclusive' => true ];

        if ($after) {
            $filter['after'] = (string) $after;
        }

        if ($before) {
            $filter['before'] = (string) $before;
        }

        if (! isset($filter['after']) && ! isset($filter['before'])) {
            return [];
        }

        return [ $filter ];
    }

    private function normalize_date_input($value, string $boundary = 'raw'): string
    {
        while (is_array($value)) {
            if (isset($value['start']) || isset($value['end'])) {
                if ('end' === $boundary && isset($value['end'])) {
                    $value = $value['end'];
                    continue;
                } elseif ('start' === $boundary && isset($value['start'])) {
                    $value = $value['start'];
                    continue;
                }
            }

            if (isset($value['value'])) {
                $value = $value['value'];
                continue;
            }

            if (isset($value['date'])) {
                $value = $value['date'];
                continue;
            }

            if (isset($value['timestamp'])) {
                $timestamp_value = $value['timestamp'];

                if (is_array($timestamp_value)) {
                    if (isset($timestamp_value['timestamp'])) {
                        $timestamp_value = $timestamp_value['timestamp'];
                    } elseif (isset($timestamp_value['date'])) {
                        $timestamp_value = $timestamp_value['date'];
                    } else {
                        $timestamp_value = reset($timestamp_value);
                    }
                }

                if (is_numeric($timestamp_value) && ctype_digit((string) $timestamp_value)) {
                    return $this->format_timestamp((int) $timestamp_value, $boundary);
                }

                $value = $timestamp_value;
                continue;
            }

            if (isset($value['year'], $value['month'], $value['day'])) {
                $hours   = $value['hour'] ?? 0;
                $minutes = $value['minute'] ?? 0;
                $seconds = $value['second'] ?? 0;

                $value = sprintf('%04d-%02d-%02d %02d:%02d:%02d',
                    (int) $value['year'],
                    (int) $value['month'],
                    (int) $value['day'],
                    (int) $hours,
                    (int) $minutes,
                    (int) $seconds
                );
                break;
            }

            $value = reset($value);
        }

        $value = is_string($value) || is_numeric($value) ? trim((string) $value) : '';

        if ('' === $value) {
            return '';
        }

        if (is_numeric($value) && ctype_digit((string) $value)) {
            return $this->format_timestamp((int) $value, $boundary);
        }

        $timestamp = strtotime($value);

        if (false !== $timestamp) {
            return $this->format_timestamp($timestamp, $boundary);
        }

        return sanitize_text_field($value);
    }

    private function format_timestamp(int $timestamp, string $boundary): string
    {
        switch ($boundary) {
            case 'start':
                $timestamp = strtotime('midnight', $timestamp);
                break;
            case 'end':
                $timestamp = strtotime('tomorrow', $timestamp) - 1;
                break;
        }

        return wp_date('Y-m-d H:i:s', $timestamp);
    }

    private function prepare_paginate_flag($value): bool
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        if (function_exists('wc_string_to_bool')) {
            return wc_string_to_bool($value);
        }

        $parsed = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if (null === $parsed) {
            return (bool) $value;
        }

        return $parsed;
    }

    private function prepare_per_page($value): int
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        if (null === $value || '' === $value) {
            return $this->get_default_per_page();
        }

        $int = (int) $value;

        if (-1 === $int) {
            return -1;
        }

        if ($int <= 0) {
            return $this->get_default_per_page();
        }

        return $int;
    }

    private function get_default_per_page(): int
    {
        $value = absint(get_option('posts_per_page', 10));
        return $value > 0 ? $value : 10;
    }

    private function prepare_offset($value): int
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        if (null === $value || '' === $value) {
            return 0;
        }

        $int = (int) $value;

        return $int > 0 ? $int : 0;
    }
}
