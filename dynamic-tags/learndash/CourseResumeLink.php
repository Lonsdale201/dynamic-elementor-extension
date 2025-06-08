<?php
/**
 * CourseResumeLink Elementor Dynamic Tag (LearnDash)
 *
 * @package hw-ele-woo-dynamic
 */

namespace HelloWP\HWEleWooDynamic\WooTags\LearnDash;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

class CourseResumeLink extends Tag {

	public function get_name()  { return 'course-resume'; }
	public function get_title() { return __( 'Course Resume URL', 'hw-ele-woo-dynamic' ); }
	public function get_group() { return 'ld_extras_courses'; }
	public function get_categories() { return [ Module::URL_CATEGORY ]; }

	protected function _register_controls() {

		$this->add_control(
			'not_started_behaviour',
			[
				'label'   => __( 'Not-started behaviour', 'hw-ele-woo-dynamic' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'course_page' => __( 'Default – open course page', 'hw-ele-woo-dynamic' ),
					'first_lesson'=> __( 'Open first lesson (if enrolled)', 'hw-ele-woo-dynamic' ),
				],
				'default' => 'course_page',
				'description' => __( 'What should happen when the learner owns the course but has 0% progress?', 'hw-ele-woo-dynamic' ),
			]
		);
	}

public function render() {
    global $post;

    if ( 'sfwd-courses' !== get_post_type( $post ) ) {
        echo '';
        return;
    }

    $course_id  = $post->ID;
    $user_id    = get_current_user_id();
    $behaviour  = $this->get_settings( 'not_started_behaviour' ) ?: 'course_page';

    if ( $user_id && sfwd_lms_has_access( $course_id, $user_id ) ) {
        $progress = learndash_course_progress( [
            'user_id'   => $user_id,
            'course_id' => $course_id,
            'array'     => true,
        ] );

        if ( isset( $progress['completed'], $progress['total'] ) ) {
            if ( 0 === (int) $progress['completed'] ) {
                if ( 'first_lesson' === $behaviour ) {
                    $lesson_ids   = learndash_course_get_steps_by_type( $course_id, 'sfwd-lessons', 'ids' );
                    $first_step_id = ! empty( $lesson_ids ) ? $lesson_ids[0] : false;

                    if ( $first_step_id ) {
                        echo esc_url( get_permalink( $first_step_id ) );
                        return;
                    }
                }
                echo esc_url( get_permalink( $course_id ) );
                return;
            }

            if ( (int) $progress['completed'] === (int) $progress['total'] ) {
                echo esc_url( get_permalink( $course_id ) );
                return;
            }

            $resume_step_id = learndash_user_progress_get_first_incomplete_step( $user_id, $course_id );
            if ( $resume_step_id ) {
                $resume_step_id = learndash_user_progress_get_parent_incomplete_step( $user_id, $course_id, $resume_step_id );
                echo esc_url( get_permalink( $resume_step_id ) );
            } else {
                echo esc_url( get_permalink( $course_id ) );
            }
            return;
        }
    }

    echo esc_url( get_permalink( $course_id ) );
}


}
