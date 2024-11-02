<?php

namespace HelloWP\HWEleWooDynamic\Modules\JEMacros;

class LDCourseAccessTypeQuery extends \Jet_Engine_Base_Macros {

    /**
     * Macro tag identifier
     *
     * @return string
     */
    public function macros_tag() {
        return 'ld_course_access_type_query';
    }

    /**
     * Macro display name
     *
     * @return string
     */
    public function macros_name() {
        return 'LD Course Access Type Query';
    }

    /**
     * Macro arguments
     *
     * @return array
     */
    public function macros_args() {
        return array(
            'access_type' => array(
                'label'   => 'Access Type',
                'type'    => 'select',
                'options' => array(
                    'open'       => 'Open',
                    'free'       => 'Free',
                    'paynow'     => 'Buy Now',
                    'subscribe'  => 'Subscription',
                    'closed'     => 'Closed'
                ),
                'default' => 'open',
            ),
        );
    }

    /**
     * Callback to retrieve course IDs based on selected access type
     *
     * @param array $args
     * @return string Comma-separated course IDs or an error message
     */
    public function macros_callback( $args = array() ) {
        $access_type_selected = !empty( $args['access_type'] ) ? $args['access_type'] : 'open';

        // Retrieve course IDs by selected access type
        $course_ids = learndash_get_posts_by_price_type( 'sfwd-courses', $access_type_selected );

        // Return comma-separated course IDs or message if no courses found
        return is_array( $course_ids ) && !empty($course_ids) ? implode( ',', $course_ids ) : 'No courses found for selected access type';
    }
}
