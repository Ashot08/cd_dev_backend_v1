<?php
namespace Controllers;



use Models\Program;
use Models\Key;
use Models\Student;

require_once __DIR__ . '/../views/key/key_programs_list.php';
require_once __DIR__ . '/../views/key/key_program_details.php';

class KeyController{
    public function actionViewDirectorPrograms($director_id){
        $model = new Program();
        $model = $model->getProgramsContentByDirectorId($director_id);
        return key_programs_list($model);
    }
    public function actionViewProgramKeys($program_id){
        $model = new Key();
        $student = new Student();
        $keysIds = $model->getKeysByProgramId($program_id);
        $keys = [];
        foreach ($keysIds as $key){
            $fullKey = $model->getKey($key->id);
            $student_id = $student->getStudentByKeyId($key->id)[0]->student_id;
            $fullKey[0]->active = $student_id ?? false;
            $keys[] = $fullKey;
        }



        if($keys){
            return key_program_details($keys, $program_id);
        }
        else{
            return false;
        }
    }
    public function actionCreateAndAttachKey($director_id, $program_id){
        $model = new Key();
        $model = $model->createAndAttachKey($director_id, $program_id);
        if($model){
            echo 'success';
        }else{
            echo 'db error';
        }
    }

}