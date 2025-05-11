<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

/**
 * CourseCertificatesLink Elementor Dynamic Tag
 *
 * Displays the certificate link for a completed course in Elementor if available.
 */
class CourseCertificatesLink extends Tag {

    /**
     * Returns the unique identifier for this tag.
     *
     * @return string
     */
    public function get_name() {
        return 'certificates-link';
    }

    /**
     * Returns the tag's title displayed in the Elementor editor.
     *
     * @return string
     */
    public function get_title() {
        return __( 'Certificates Link', 'hw-elementor-woo-dynamic' );
    }

    /**
     * Returns the group ID for this tag.
     *
     * @return string
     */
    public function get_group() {
        return 'ld_extras_courses';
    }

    /**
     * Returns the categories this tag belongs to.
     *
     * @return array
     */
    public function get_categories() {
        return [ Module::URL_CATEGORY ];
    }

    /**
     * Renders the course certificate link if available for the current user.
     *
     * Checks if the current post is a LearnDash course, retrieves the course certificate link
     * for the current user, and outputs the URL if it exists.
     *
     * @return void
     */
    public function render() {
        global $post;

        // Verify if the post type is a LearnDash course
        if ( 'sfwd-courses' !== get_post_type( $post ) ) {
            return;
        }

        $course_id = $post->ID;
        $user_id = get_current_user_id();

        // Retrieve the certificate link for the current course and user
        $certificate_link = learndash_get_course_certificate_link( $course_id, $user_id );

        // Output the certificate link URL if available
        if ( ! empty( $certificate_link ) ) {
            echo esc_url( $certificate_link );
        }
    }
}
