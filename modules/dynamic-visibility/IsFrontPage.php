<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class IsFrontPage extends Base {

    /**
     * Retrieves a unique identifier for this condition.
     *
     * @return string
     */
    public function get_id() {
        return 'is-front-page';
    }

    /**
     * Retrieves the display name for this condition.
     *
     * @return string
     */
    public function get_name() {
        return __('Is Front Page', 'hw-elementor-woo-dynamic');
    }

    /**
     * Assigns this condition to the custom group.
     *
     * @return string
     */
    public function get_group() {
        return 'Other';
    }

    /**
     * Checks if the current page is the front page.
     *
     * @param array $args Array containing parameters for display conditions.
     * @return bool True if the current page is the front page and the condition is set to "show", false otherwise.
     */
    public function check($args = []) {
        $front_page_id = get_option('page_on_front');
        $current_page_id = get_the_ID();
        $is_front_page = ($front_page_id == $current_page_id);

        $type = isset($args['type']) ? $args['type'] : 'show';

        return ('hide' === $type) ? !$is_front_page : $is_front_page;
    }

    /**
     * Indicates that this condition is not field-specific.
     *
     * @return bool
     */
    public function is_for_fields() {
        return false;
    }

    /**
     * Indicates that this condition does not require value detection.
     *
     * @return bool
     */
    public function need_value_detect() {
        return false;
    }
}
