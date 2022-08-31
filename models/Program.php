<?php
namespace Models;
class Program {

    protected string $title;
    protected string $description;
    protected string $image;
    protected int $director_id;

    public function __construct( string $title = '', string $description = '', string $image = '', int $director_id = 0) {
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->director_id = $director_id;
    }

    public function getProgramsByDirectorId(int $director_id){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__program";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT id 
        FROM $table WHERE director_id = %d
        ", $director_id
        ));
        return $data;
    }
    public function getProgramsContentByDirectorId(int $director_id){
        $programs_ids = $this->getProgramsByDirectorId($director_id);
        $programs_array = [];
        foreach ($programs_ids as $program){
            $programs_array[] = $this->getProgram($program->id);
        }
        return $programs_array;
    }
    public function getProgramsContentByStudentId(int $student_id){
        $programs_ids = $this->getProgramsByStudentId($student_id);
        $programs_array = [];
        foreach ($programs_ids as $program){
            $programs_array[] = $this->getProgram($program->program_id);
        }
        return $programs_array;
    }
    public function getProgramsByStudentId(int $student_id){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__students_programs";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT program_id 
        FROM $table WHERE student_id = %d
        ", $student_id
        ));
        return $data;
    }

    public function getProgramByKeyId(int $key_id){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__key";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT program_id 
        FROM $table WHERE id = %d
        ", $key_id
        ));
        return $data;
    }

    public function getProgram(int $id){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__program";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT * 
        FROM $table WHERE id = %d
        ", $id
        ));
        return $data;
    }

    public function createProgram(){
        global $wpdb;

        $table_name = $wpdb->prefix . "c_dash__program";
        $wpdb->insert( $table_name, [
            'title'         => $this->title,
            'description'   => $this->description,
            'director_id'   => $this->director_id,
            ]);
        return $wpdb->insert_id;
    }

}