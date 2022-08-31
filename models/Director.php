<?php
namespace Models;
class Director {

    public function connectDirectorWithProgram(int $director_id, int $program_id){
        global $wpdb;
        $table_name = $wpdb->prefix . "c_dash__directors_programs";
        $wpdb->insert( $table_name, [ 'director_id' => $director_id, 'program_id' =>  $program_id ]);
        return $wpdb->insert_id;
    }
    public function connectDirectorWithCourse(int $director_id, int $course_id){
        global $wpdb;
        $table_name = $wpdb->prefix . "c_dash__directors_courses";
        $wpdb->insert( $table_name, [ 'director_id' => $director_id, 'course_id' =>  $course_id ]);
        return $wpdb->insert_id;
    }
}
