<?php

namespace HelloWP\HWEleWooDynamic\Modules\DynamicVisibility;

use Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base;

class LDSelectedQuizzesCompleted extends Base {

    public function get_id() {
        return 'ld-selected-quizzes-completed';
    }

    public function get_name() {
        return __( 'Quizzes Completed', 'hw-ele-woo-dynamic' );
    }

    public function get_group() {
        return 'LearnDash';
    }

    public function check( $args = [] ) {
        if ( ! function_exists( 'learndash_is_quiz_complete' ) || ! function_exists( 'learndash_get_post_type_slug' ) ) {
            return false;
        }

        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            return false;
        }

        $selected = isset( $args['condition_settings']['selected_quizzes'] ) ? (array) $args['condition_settings']['selected_quizzes'] : [];
        $selected = array_filter( array_map( 'absint', $selected ) );

        if ( empty( $selected ) ) {
            return false;
        }

        $quiz_post_type = $this->get_quiz_post_type();
        $has_completed  = false;

        foreach ( $selected as $quiz_id ) {
            if ( ! $quiz_id || $quiz_post_type !== get_post_type( $quiz_id ) ) {
                continue;
            }

            if ( $this->is_quiz_completed_by_user( $quiz_id, $user_id ) ) {
                $has_completed = true;
                break;
            }
        }

        $type = isset( $args['type'] ) ? $args['type'] : 'show';

        return ( 'hide' === $type ) ? ! $has_completed : $has_completed;
    }

    public function get_custom_controls() {
        return [
            'selected_quizzes' => [
                'label'    => __( 'Select Quizzes', 'hw-ele-woo-dynamic' ),
                'type'     => 'select2',
                'multiple' => true,
                'default'  => [],
                'options'  => $this->get_quiz_options(),
            ],
        ];
    }

    private function get_quiz_options() {
        $quiz_post_type = $this->get_quiz_post_type();

        $quizzes = get_posts([
            'post_type'      => $quiz_post_type,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'fields'         => 'ids',
        ]);

        $options = [];

        foreach ( $quizzes as $quiz_id ) {
            $options[ $quiz_id ] = get_the_title( $quiz_id );
        }

        return $options;
    }

    private function get_quiz_post_type() {
        return function_exists( 'learndash_get_post_type_slug' )
            ? learndash_get_post_type_slug( 'quiz' )
            : 'sfwd-quiz';
    }

    private function is_quiz_completed_by_user( $quiz_id, $user_id ) {
        $course_id = (int) learndash_get_course_id( $quiz_id );
        $lesson_id = (int) get_post_meta( $quiz_id, 'lesson', true );

        if ( $lesson_id && 'sfwd-lessons' !== get_post_type( $lesson_id ) && function_exists( 'learndash_get_post_type_slug' ) ) {
            $lesson_slug = learndash_get_post_type_slug( 'lesson' );
            if ( $lesson_slug !== get_post_type( $lesson_id ) ) {
                $lesson_id = 0;
            }
        }

        if ( $course_id && ! $lesson_id && function_exists( 'learndash_get_course_lessons_list' ) ) {
            $lessons = learndash_get_course_lessons_list( $course_id, $user_id );
            foreach ( $lessons as $lesson ) {
                $quiz_list = learndash_get_lesson_quiz_list( $lesson['post']->ID );
                foreach ( $quiz_list as $quiz ) {
                    if ( (int) $quiz['post']->ID === $quiz_id ) {
                        $lesson_id = (int) $lesson['post']->ID;
                        break 2;
                    }
                }
            }
        }

        $completed = learndash_is_quiz_complete( $user_id, $quiz_id, $course_id, $lesson_id );

        if ( ! $completed ) {
            $completed = learndash_is_quiz_complete( $user_id, $quiz_id );
        }

        if ( ! $completed && function_exists( 'learndash_user_quiz_check' ) ) {
            $completed = learndash_user_quiz_check( $user_id, $quiz_id );
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
