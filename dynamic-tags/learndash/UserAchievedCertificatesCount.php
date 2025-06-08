<?php
namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

/**
 * UserAchievedCertificatesCount Elementor Dynamic Tag
 *
 * Displays the total number of certificates the logged-in user has achieved.
 */
class UserAchievedCertificatesCount extends Tag {

    public function get_name() {
        return 'user-achieved-certificates-count';
    }

    public function get_title() {
        return __( 'User Achieved Certificates Count', 'hw-ele-woo-dynamic' );
    }

    public function get_group() {
        return 'ld_extras_global';
    }

    public function get_categories() {
        return [ Module::TEXT_CATEGORY, Module::NUMBER_CATEGORY ];
    }

    /**
     * Register controls for Elementor editor.
     */
    protected function _register_controls() {
        // No additional controls required for this tag.
    }

    /**
     * Render the number of achieved certificates or an empty value if none.
     */
    public function render() {
        // Return empty if the user is not logged in.
        if ( ! is_user_logged_in() ) {
            echo ''; // Empty if not logged in
            return;
        }

        $user_id = get_current_user_id();
        
        // Retrieve the count of achieved certificates.
        $certificates_count = learndash_get_certificate_count( $user_id );

        // Display the certificate count if greater than zero, otherwise output empty.
        if ( $certificates_count > 0 ) {
            echo esc_html( $certificates_count );
        } else {
            echo ''; // Empty if no certificates
        }
    }
}
