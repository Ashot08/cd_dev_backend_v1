<?php
namespace Controllers;

use Models\Course;
use Models\Program;




class AccessController{
    public function actionGetUserCourses(){
        $user = get_current_user_id();
        $user_info = get_userdata(get_current_user_id());
        $is_company = false;
        $programs = [];
        $courses = [];
        $model = new Program();


        if(is_user_logged_in() && isset($user_info->caps["customer_company"])){
            $is_company = true;
        }

        if($is_company){
            $programs = $model->getProgramsByDirectorId($user);
            foreach ($programs as $program){
                $model = new Course();
                $program_courses = $model->getCoursesByProgramId($program->id);
                foreach ($program_courses as $course){
                    $courses[] = $course->course_id;
                }
            }
        }elseif($user){
            $programs = $model->getProgramsByStudentId($user);

            foreach ($programs as $program){
                $model = new Course();
                $program_courses = $model->getCoursesByProgramId($program->program_id);
                foreach ($program_courses as $course){
                    $courses[] = $course->course_id;
                }
            }
        }

        return array_unique($courses);
    }
}