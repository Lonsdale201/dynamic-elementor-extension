<?php

namespace HelloWP\HWEleWooDynamic\Modules\Helpers;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Helper class to handle LearnDash course queries and visibility.
 */
class LDQuery {

    /**
     * Get the current course ID if on a single course page.
     *
     * @return int|null
     */
    public static function get_course_id() {
        global $post;

        if (is_a($post, 'WP_Post') && 'sfwd-courses' === get_post_type($post)) {
            return $post->ID;
        }

        return null;
    }

    /**
     * Check if the current user has access to the course.
     *
     * @param int $course_id
     * @return bool
     */
    public static function user_has_access($course_id) {
        return sfwd_lms_has_access($course_id, get_current_user_id());
    }

    /**
     * Get the visibility options for LearnDash-related tags.
     *
     * @return array
     */
    public static function get_visibility_options() {
        return [
            'everyone' => __('Everybody', 'hw-elementor-woo-dynamic'),
            'enrolled' => __('Only Students', 'hw-elementor-woo-dynamic'),
        ];
    }

    /**
     * Get the number of lessons for a specific course.
     *
     * @param int $course_id
     * @return int
     */
    public static function get_lessons_count($course_id) {
        $lessons = learndash_get_course_lessons_list($course_id);
        return count($lessons);
    }

    /**
     * Get the number of completed lessons for the current user in a specific course.
     *
     * @param int $course_id
     * @return int
     */
    public static function get_completed_lessons_count($course_id) {
        return learndash_course_get_completed_steps(get_current_user_id(), $course_id);
    }

    /**
     * Get the quizzes from a course, including quizzes in lessons and topics.
     *
     * @param int $course_id
     * @return array
     */
    public static function get_quizzes_in_course($course_id) {
        $quizzes = [];
        $lessons = learndash_get_lesson_list($course_id);
        foreach ($lessons as $lesson) {
            $lesson_quizzes = learndash_get_lesson_quiz_list($lesson->ID);
            if (is_array($lesson_quizzes)) {
                $quizzes = array_merge($quizzes, $lesson_quizzes);
            }

            $topics = learndash_topic_dots($lesson->ID, false, 'array');
            if (is_array($topics)) {
                foreach ($topics as $topic) {
                    $topic_quizzes = learndash_get_lesson_quiz_list($topic->ID);
                    if (is_array($topic_quizzes)) {
                        $quizzes = array_merge($quizzes, $topic_quizzes);
                    }
                }
            }
        }

        $course_quizzes = learndash_get_course_quiz_list($course_id);
        if (is_array($course_quizzes)) {
            $quizzes = array_merge($quizzes, $course_quizzes);
        }

        return $quizzes;
    }

    /**
     * Count the number of quizzes in a course, including lessons and topics.
     *
     * @param int $course_id
     * @return int
     */
    public static function count_quizzes_in_course($course_id) {
        $quizzes = self::get_quizzes_in_course($course_id);
        return count($quizzes);
    }


    /**
     * Get the last activity date for a specific course and user.
     *
     * @param int $course_id
     * @param int $user_id
     * @return string|null Formázott dátum vagy null, ha nincs aktivitás.
     */
    public static function get_last_activity_date( $course_id, $user_id ) {
        $args = array(
            'course_id'     => $course_id,
            'post_id'       => $course_id,
            'user_id'       => $user_id,
            'activity_type' => 'course',
            'order'         => 'DESC',
            'per_page'      => 1,
        );

        $activity = learndash_get_user_activity( $args );

        if ( ! empty( $activity ) && isset( $activity->activity_updated ) ) {
            return $activity->activity_updated;
        }

        return null;
    }

    public static function get_topics_count( $course_id ) {
        $lessons = learndash_get_lesson_list( $course_id );
        $topic_count = 0;

        if ( ! empty( $lessons ) && is_array( $lessons ) ) {
            foreach ( $lessons as $lesson ) {
                $topics = learndash_topic_dots( $lesson->ID, false, 'array' );
                if ( is_array( $topics ) ) {
                    $topic_count += count( $topics );
                }
            }
        }

        return $topic_count;
    }

    public static function get_completed_topics_count( $course_id, $user_id = 0 ) {
        if ( 0 === $user_id ) {
            $user_id = get_current_user_id();
        }
        if ( ! $user_id ) {
            return 0;
        }

        $lessons = learndash_get_lesson_list( $course_id );
        $completed_topic_count = 0;

        if ( ! empty( $lessons ) && is_array( $lessons ) ) {
            foreach ( $lessons as $lesson ) {
                $topics = learndash_topic_dots( $lesson->ID, false, 'array' );
                if ( is_array( $topics ) ) {
                    foreach ( $topics as $topic_post ) {
                        $topic_id = $topic_post->ID;
                        if ( learndash_is_lesson_complete( $user_id, $topic_id, $course_id ) ) {
                            $completed_topic_count++;
                        }
                    }
                    
                }
            }
        }

        return $completed_topic_count;
    }


}
