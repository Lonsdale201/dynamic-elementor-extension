<?php
namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

/**
 * Class LDUserCurrentCourseNotStarted
 *
 * Checks if the current logged-in user has NOT started the current LearnDash course.
 */
class LDUserCurrentCourseNotStarted extends Base {

    /**
     * Condition ID (slug) used internally by JetEngine
     *
     * @return string
     */
    public function get_id() {
        return 'ld-user-current-course-not-started';
    }

    /**
     * Display name in the JetEngine admin UI
     *
     * @return string
     */
    public function get_name() {
        return __( 'Course Not Started', 'hw-elementor-woo-dynamic' );
    }

    /**
     * Group name under which it appears in the JetEngine condition selection
     *
     * @return string
     */
    public function get_group() {
        return 'LearnDash';
    }

    /**
     * The main check function to determine if the user has NOT started the current course.
     * 
     * @param array $args Condition arguments (e.g. "type" => "show" or "hide")
     * @return bool true if condition is met, false otherwise
     */
    public function check( $args = array() ) {
        // Get current user
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            // Not logged in => "not started" értelmezhetetlen, visszatérünk false-szal
            return false;
        }

        // Get current post/course
        global $post;
        $course_id = isset( $post->ID ) ? $post->ID : 0;

        // Check if it's indeed a LearnDash course
        if ( 'sfwd-courses' !== get_post_type( $course_id ) ) {
            return false;
        }

        // Ellenőrizzük, hogy a felhasználó hozzáfér-e a kurzushoz (beiratkozott-e).
        // Ha nincs beiratkozva, akkor sem értelmezhető a "not started" – tipikusan false lesz.
        if ( ! sfwd_lms_has_access( $course_id, $user_id ) ) {
            return false;
        }

        // Lekérdezzük a kurzus-haladást
        $progress = learndash_course_progress( [
            'user_id'   => $user_id,
            'course_id' => $course_id,
            'array'     => true,
        ] );

        // "Not Started" = completed = 0. (Feltételezve, hogy a user be van iratkozva,
        // de semmit nem fejezett be.)
        $not_started = false;
        if ( isset( $progress['completed'] ) ) {
            $not_started = ( 0 === (int) $progress['completed'] );
        }

        // Irányítás, hogy "show" vagy "hide" logikát kér-e a felhasználó
        $type = isset( $args['type'] ) ? $args['type'] : 'show';

        // Ha "hide" => invertáljuk a $not_started-et
        return ( 'hide' === $type )
            ? ! $not_started
            : $not_started;
    }

    /**
     * We don't use this condition for form fields
     *
     * @return bool
     */
    public function is_for_fields() {
        return false;
    }

    /**
     * We don't need a value detection for this condition
     *
     * @return bool
     */
    public function need_value_detect() {
        return false;
    }
}
