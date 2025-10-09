<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class LDSelectedLessonsCompleted extends Base {

    public function get_id() {
        return 'ld-selected-lessons-completed';
    }

    public function get_name() {
        return __( 'Lessons Completed', 'hw-ele-woo-dynamic' );
    }

    public function get_group() {
        return 'LearnDash';
    }

    public function check( $args = [] ) {
        if ( ! function_exists( 'learndash_is_lesson_complete' ) || ! function_exists( 'learndash_get_course_id' ) ) {
            return false;
        }

        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            return false;
        }

        $selected = isset( $args['condition_settings']['selected_lessons'] ) ? (array) $args['condition_settings']['selected_lessons'] : [];
        $selected = array_filter( array_map( 'absint', $selected ) );

        if ( empty( $selected ) ) {
            return false;
        }

        $has_completed = false;

        foreach ( $selected as $lesson_id ) {
            if ( ! $lesson_id ) {
                continue;
            }

            if ( ! $this->is_lesson_post_type( $lesson_id ) ) {
                continue;
            }

            if ( $this->is_lesson_completed_by_user( $lesson_id, $user_id ) ) {
                $has_completed = true;
                break;
            }
        }

        $type = isset( $args['type'] ) ? $args['type'] : 'show';

        return ( 'hide' === $type ) ? ! $has_completed : $has_completed;
    }

    public function get_custom_controls() {
        return [
            'selected_lessons' => [
                'label'    => __( 'Select Lessons', 'hw-ele-woo-dynamic' ),
                'type'     => 'select2',
                'multiple' => true,
                'default'  => [],
                'options'  => $this->get_lessons_options(),
            ],
        ];
    }

    private function get_lessons_options() {
        $lessons = get_posts([
            'post_type'      => $this->get_lesson_post_type(),
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'fields'         => 'ids',
        ]);

        $options = [];

        foreach ( $lessons as $lesson_id ) {
            $options[ $lesson_id ] = get_the_title( $lesson_id );
        }

        return $options;
    }

    private function resolve_course_id( $step_id ) {
        $course_id = (int) learndash_get_course_id( $step_id );

        if ( ! $course_id && function_exists( 'learndash_get_step_course_id' ) ) {
            $course_id = (int) learndash_get_step_course_id( $step_id );
        }

        return $course_id;
    }

    private function is_lesson_post_type( $lesson_id ) {
        $lesson_slug = $this->get_lesson_post_type();
        return $lesson_slug === get_post_type( $lesson_id );
    }

    private function get_lesson_post_type() {
        return function_exists( 'learndash_get_post_type_slug' )
            ? learndash_get_post_type_slug( 'lesson' )
            : 'sfwd-lessons';
    }

    private function is_lesson_completed_by_user( $lesson_id, $user_id ) {
        $completed = learndash_is_lesson_complete( $user_id, $lesson_id );

        if ( $completed ) {
            return true;
        }

        $course_id = $this->resolve_course_id( $lesson_id );
        if ( $course_id ) {
            $completed = learndash_is_lesson_complete( $user_id, $lesson_id, $course_id );
        }

        return (bool) $completed;
    }

    public function is_for_fields() {
        return false;
    }

    public function need_value_detect() {
        return false;
    }
}
