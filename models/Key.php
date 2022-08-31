<?php
namespace Models;
class Key {
    private function generateKey(){
        $alphabet = '1234567890abc';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 36; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return $key = implode($pass);
    }

    private function insertKey($key, $director_id, $program_id){
        global $wpdb;

        $table_name = $wpdb->prefix . "c_dash__key";
        $wpdb->insert( $table_name, [ 'access_key' =>  $key, 'director_id' => $director_id, 'program_id' => $program_id ]);
        return $wpdb->insert_id;
    }


    private function connectKeyWithStudent(int $key_id, int $student_id){
        global $wpdb;
        $table_name = $wpdb->prefix . "c_dash__students_keys";
        $wpdb->insert( $table_name, [ 'key_id' => $key_id, 'student_id' =>  $student_id ]);
        return $wpdb->insert_id;
    }

    public function createAndAttachKey($director_id, $program_id){
        $key = $this->generateKey();
        return $this->insertKey($key, $director_id, $program_id);
    }

    public function getKeysByDirectorId(int $director_id){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__directors_keys";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT key_id 
        FROM $table WHERE director_id = %d
        ", $director_id
        ));
        return $data;
    }

    public function getKeysByStudentId(int $student_id){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__key";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT id 
        FROM $table WHERE student_id = %d
        ", $student_id
        ));
        return $data;
    }

    public function getKeysByProgramId(int $program_id){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__key";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT id 
        FROM $table WHERE program_id = %d
        ", $program_id
        ));
        return $data;
    }

    public function getKey(int $id){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__key";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT * 
        FROM $table WHERE id = %d
        ", $id
        ));
        return $data;
    }
    public function getKeyByAccessKey(string $key){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__key";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT * 
        FROM $table WHERE access_key = %s
        ", $key
        ));
        return $data;
    }
}