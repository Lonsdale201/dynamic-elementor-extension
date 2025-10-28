<?php

namespace HelloWP\HWEleWooDynamic\WooTags;

use Elementor\Core\DynamicTags\Tag;
use WC_Order;

abstract class AbstractOrderTag extends Tag
{
    public function get_group()
    {
        return 'woo-extras-user';
    }

    /**
     * Try to resolve the current WC_Order based on the JetEngine listing context
     * or the current global post.
     */
    protected function get_current_order(): ?WC_Order
    {
        if (! function_exists('wc_get_order')) {
            return null;
        }

        if (function_exists('jet_engine')) {
            $manager = jet_engine()->listings->data ?? null;

            if ($manager) {
                $object = method_exists($manager, 'get_current_object') ? $manager->get_current_object() : null;
                $order = $this->cast_to_order($object);

                if ($order) {
                    return $order;
                }

                if (method_exists($manager, 'get_current_object_id')) {
                    $order = $this->cast_to_order($manager->get_current_object_id());
                    if ($order) {
                        return $order;
                    }
                }
            }
        }

        $maybe_global = wc_get_order(get_the_ID());
        if ($maybe_global instanceof WC_Order) {
            return $maybe_global;
        }

        return null;
    }

    /**
     * Normalize different data structures into a WC_Order instance.
     *
     * @param mixed $source
     */
    protected function cast_to_order($source): ?WC_Order
    {
        if (! function_exists('wc_get_order')) {
            return null;
        }

        if ($source instanceof WC_Order) {
            return $source;
        }

        if (is_numeric($source)) {
            return $this->maybe_order(wc_get_order((int) $source));
        }

        if (is_array($source)) {
            if (isset($source['id'])) {
                return $this->maybe_order(wc_get_order((int) $source['id']));
            }
            if (isset($source['ID'])) {
                return $this->maybe_order(wc_get_order((int) $source['ID']));
            }
        }

        if (is_object($source)) {
            if ($source instanceof \WP_Post) {
                $maybe_id = $source->ID ?? null;
                if ($maybe_id) {
                    return $this->maybe_order(wc_get_order((int) $maybe_id));
                }
            } elseif ($source instanceof \WP_User) {
                $maybe_id = $source->ID ?? null;
                if ($maybe_id) {
                    return $this->maybe_order(wc_get_order((int) $maybe_id));
                }
            } elseif ($source instanceof \WP_Term) {
                $maybe_id = $source->term_id ?? null;
                if ($maybe_id) {
                    return $this->maybe_order(wc_get_order((int) $maybe_id));
                }
            }

            if (isset($source->order) && $source->order instanceof WC_Order) {
                return $source->order;
            }

            if (method_exists($source, 'get_id')) {
                $maybe_id = $source->get_id();
                if ($maybe_id) {
                    return $this->maybe_order(wc_get_order((int) $maybe_id));
                }
            }
        }

        return null;
    }

    private function maybe_order($value): ?WC_Order
    {
        return ($value instanceof WC_Order) ? $value : null;
    }
}
