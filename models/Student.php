<?php
namespace Models;
class Student {

    public function getStudentsByProgramId(int $program_id, int $from = 0, int $to = 1000 ){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__students_programs";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT student_id 
        FROM $table WHERE program_id = %d
        LIMIT $from, $to
        ", $program_id
        ));
        return $data;
    }

    public function connectStudentWithProgram(int $student_id, int $program_id){
        global $wpdb;
        $table_name = $wpdb->prefix . "c_dash__students_programs";
        $wpdb->insert( $table_name, [ 'student_id' => $student_id, 'program_id' =>  $program_id ]);
        return $wpdb->insert_id;
    }

    public function connectStudentWithKey(int $student_id, int $key_id){
        global $wpdb;
        $table_name = $wpdb->prefix . "c_dash__key";

        return $wpdb->update(
            $table_name, // указываем таблицу
            array('student_id' => $student_id), // поменяем имя
            array( // где
                'id' => $key_id,
            ),
            array( '%d' ),
            array( // формат для «где»
                '%d',
            )
        );
        //return $wpdb->insert_id;
    }

    public function getStudentByKeyId(int $key_id){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__key";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT student_id 
        FROM $table WHERE id = %d
        ", $key_id
        ));
        return $data;
    }

}
