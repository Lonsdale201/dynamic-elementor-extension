<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

/**
 * CourseAccessType Elementor Dynamic Tag
 *
 * Displays the access type for a course in Elementor.
 */
class CourseAccessType extends Tag {

    /**
     * Get the unique name of the tag.
     *
     * @return string
     */
    public function get_name() {
        return 'course-access-type';
    }

    /**
     * Get the title of the tag displayed in the Elementor panel.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Course Access Type', 'hw-ele-woo-dynamic' );
    }

    /**
     * Get the group of the tag.
     *
     * @return string
     */
    public function get_group() {
        return 'ld_extras_courses';
    }

    /**
     * Get the categories of the tag.
     *
     * Registers the tag in the text category.
     *
     * @return array
     */
    public function get_categories() {
        return [ Module::TEXT_CATEGORY ];
    }

    /**
     * Register controls for the access type tag.
     *
     * Adds controls for selecting format type, custom labels, and enrolled text.
     *
     * @return void
     */
    protected function _register_controls() {
        // Control for choosing the format type between default or custom.
        $this->add_control(
            'format_type',
            [
                'label' => __( 'Format Type', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'default' => __( 'Learndash Default', 'hw-ele-woo-dynamic' ),
                    'custom' => __( 'Custom Format', 'hw-ele-woo-dynamic' ),
                ],
                'default' => 'default',
            ]
        );

        // Custom text controls for each access type.
        $this->add_text_control_for_course_access_type('open', __( 'Open', 'hw-ele-woo-dynamic' ));
        $this->add_text_control_for_course_access_type('free', __( 'Free', 'hw-ele-woo-dynamic' ));
        $this->add_text_control_for_course_access_type('paynow', __( 'Buy Now', 'hw-ele-woo-dynamic' ));
        $this->add_text_control_for_course_access_type('subscribe', __( 'Subscription', 'hw-ele-woo-dynamic' ));
        $this->add_text_control_for_course_access_type('closed', __( 'Closed', 'hw-ele-woo-dynamic' ));

        // Control for user enrolled custom text.
        $this->add_control(
            'custom_text_enrolled',
            [
                'label' => __( 'If User Enrolled', 'hw-ele-woo-dynamic' ),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'format_type' => 'custom',
                ],
            ]
        );
    }

    /**
     * Adds custom text controls based on access type.
     *
     * Dynamically generates a text control for each type of course access.
     *
     * @param string $type  Access type identifier.
     * @param string $label Label to display in Elementor panel.
     * @return void
     */
    private function add_text_control_for_course_access_type($type, $label) {
        $this->add_control(
            "custom_text_{$type}",
            [
                'label' => $label,
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'format_type' => 'custom',
                ],
            ]
        );
    }

    /**
     * Render the course access type.
     *
     * Displays the appropriate access type text based on format type and custom settings.
     *
     * @return void
     */
    public function render() {
        global $post;

        // Exit if the post type is not a course.
        if ('sfwd-courses' !== get_post_type($post)) {
            echo '';
            return;
        }

        $course_id = $post->ID;

        // Retrieve user-defined settings.
        $settings = $this->get_settings();
        $format_type = $settings['format_type'];

        // Check if the user is enrolled in the course.
        $is_enrolled = sfwd_lms_has_access($course_id);

        // If custom format and user is enrolled, show enrolled text.
        if ($format_type === 'custom' && $is_enrolled && !empty($settings['custom_text_enrolled'])) {
            echo esc_html($settings['custom_text_enrolled']);
            return;
        }

        // Fetch the course access type.
        $access_type = learndash_get_course_meta_setting($course_id, 'course_price_type');

        // Get the appropriate text to display based on format type and settings.
        $access_type_text = $this->get_access_type_text($access_type, $settings, $format_type);
        echo esc_html($access_type_text);
    }

    /**
     * Retrieve access type text based on format and custom settings.
     *
     * Returns either default text or custom text if specified by the user.
     *
     * @param string $access_type The access type from LearnDash.
     * @param array $settings The settings provided by the user in Elementor.
     * @param string $format_type The format type selected by the user.
     * @return string
     */
    private function get_access_type_text($access_type, $settings, $format_type) {
        // Use custom text if format type is set to custom and a custom text exists.
        if ($format_type === 'custom' && isset($settings["custom_text_{$access_type}"])) {
            return $settings["custom_text_{$access_type}"];
        }

        // Default text for each access type if no custom text is set.
        switch ($access_type) {
            case 'open':
                return __( 'Open', 'learndash' );
            case 'free':
                return __( 'Free', 'learndash' );
            case 'paynow':
                return __( 'Buy Now', 'learndash' );
            case 'subscribe':
                return __( 'Subscription', 'learndash' );
            case 'closed':
                return __( 'Closed', 'learndash' );
            default:
                return __( 'Unknown', 'learndash' );
        }
    }
}
