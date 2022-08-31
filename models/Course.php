<?php
namespace Models;
class Course {

    public function getCoursesByProgramId(int $program_id){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__programs_courses";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT course_id 
        FROM $table WHERE program_id = %d
        ", $program_id
        ));
        return $data;
    }
    public function getCoursesByDirectorId(int $director_id){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__directors_courses";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT course_id 
        FROM $table WHERE director_id = %d
        ", $director_id
        ));
        return $data;
    }

    public function getCoursesByStudentId(int $student_id){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__students_courses";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT course_id 
        FROM $table WHERE student_id = %d
        ", $student_id
        ));
        return $data;
    }

    public function connectCourseWithProgram(int $course_id, int $program_id){
        global $wpdb;
        $table_name = $wpdb->prefix . "c_dash__programs_courses";
        $wpdb->insert( $table_name, [ 'course_id' => $course_id, 'program_id' =>  $program_id ]);
        return $wpdb->insert_id;
    }

}