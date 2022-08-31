<?php
namespace Controllers;



use Models\Program;
use Models\Student;
use Models\Key;

use WP_Error;
use WP_Post;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

use MBLCategory;

require_once __DIR__ . '/../views/students_control/students_control_list.php';
require_once __DIR__ . '/../views/students_control/students_control_details.php';

class StudentController{
    public function actionViewDirectorPrograms($director_id){
        $model = new Program();
        $model = $model->getProgramsContentByDirectorId($director_id);
        return students_control_list($model);
    }

    public function actionViewStudentsControlDetails($program_id){
        $model = new Student();
        $programModel = new Program();
        $model = $model->getStudentsByProgramId($program_id);
        $program_info = $programModel->getProgram($program_id)[0] ?? '';
        if(empty($model)){
            echo 'not_found';
        }else{
            return students_control_details($model, $program_id, $program_info);
        }

    }

    public function actionConnectStudentWithProgram($access_key){
        $student_id = get_current_user_id();
        $model = new Program();
        $keyModel = new Key();
        $studentModel = new Student();
        $key = $keyModel->getKeyByAccessKey($access_key);
        $is_key_exist_before = false;
        $is_connection_completed = false;

        if(is_array($key) && !empty($key)){
            $is_key_exist_before = $studentModel->getStudentByKeyId($key[0]->id)[0]->student_id;
        }else{
            echo 'key_error';
            return;
        }
        if($is_key_exist_before){
            echo 'exist_before';
            return;
        }else{
            $is_connection_completed = $studentModel->connectStudentWithKey($student_id, $key[0]->id);
        }
        if($is_connection_completed){
            $program_id = $model->getProgramByKeyId($key[0]->id);
            $studentModel->connectStudentWithProgram($student_id, $program_id[0]->program_id);
            echo 'success';
        }else{
            echo 'error';
        }

    }
    function actionOnlyConnectStudentWithProgram($student_id, $program_id){
        $studentModel = new Student();
        if($studentModel->connectStudentWithProgram($student_id, $program_id)) echo 'success';
    }

}


class REST_Student_Controller extends WP_REST_Controller {

    function __construct(){
        $this->namespace = 'courses_dashboard/v1';
        $this->rest_base = 'cd__students';
    }

    function register_routes(){

        register_rest_route( $this->namespace, "/$this->rest_base&program_id=(?P<program_id>[\w]+)&page=(?P<page>[\w]+)&offset=(?P<offset>[\w]+)", [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_items' ],
                'permission_callback' => [ $this, 'get_items_permissions_check' ],
            ],
            'schema' => [ $this, 'get_item_schema' ],
        ] );


        register_rest_route( $this->namespace, "/$this->rest_base/(?P<id>[\w]+)", [
            [
                'methods'   => 'GET',
                'callback'  => [ $this, 'get_item' ],
                'permission_callback' => [ $this, 'get_item_permissions_check' ],
            ],
            'schema' => [ $this, 'get_item_schema' ],
        ] );
    }

    function get_items_permissions_check( $request ){
//        if ( ! current_user_can( 'read' ) ){
//            return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the post resource.' ), [ 'status' => $this->error_status_code() ] );
//        }
        return true;
    }

    /**
     * Получает последние посты и отдает их в виде rest ответа.
     *
     * @param WP_REST_Request $request Текущий запрос.
     *
     * @return WP_Error|array
     */
    function get_items( $request ){

        $offset   = $request["page"] * $request['offset'];
        $limit = $request['offset'];

        global $wpdb;
        $table = $wpdb->prefix . "c_dash__students_programs";

        $count = $wpdb->get_results($wpdb->prepare(
            "
                SELECT count(*) as 'count'
                FROM $table WHERE program_id = %d
                ",  $request['program_id']
            )
        );

        $students = $wpdb->get_results($wpdb->prepare(
            "
                SELECT student_id, start_date 
                FROM $table WHERE program_id = %d
                LIMIT $offset, $limit;
                ", $request['program_id']
            )
        );

        $table = $wpdb->prefix . "c_dash__programs_courses";
        $program_courses = $wpdb->get_results($wpdb->prepare(
            "
        SELECT course_id 
        FROM $table WHERE program_id = %d
        ", $request['program_id']
        ));

        $studentsResult = [];
        foreach ($students as $student){

            $user_info = get_userdata($student->student_id);
            $user_id = $user_info->data->ID;
            $user_name = $user_info->data->display_name;
            $user_login = $user_info->data->user_login;
            $user_email = $user_info->data->user_email;
            $user_snils = get_user_meta($user_id, 'user_snils', true);
            $user_pass = '123';

            $total_progress = 0;

            foreach ($program_courses as $course) {
                $term = get_term($course->course_id);
                $course_category = new MBLCategory($term);
                $total_progress += $course_category->getProgress($user_id) ?? '0';
            }

            $studentsResult[] = [
                'user_info' => $user_info,
                'user_id' => $user_id,
                'user_name' => $user_name,
                'user_login' => $user_login,
                'user_email' => $user_email,
                'user_snils' => $user_snils,
                'user_pass' => $user_pass,
                'total_progress' => $total_progress
            ];

        }
//
        return array(
            'count' => $count[0]->count,
            'students' => $studentsResult
        );
//        foreach( $programs as $program ){
//            $response = $this->prepare_item_for_response( $program, $request );
//            $data[] = $this->prepare_response_for_collection( $response );
//        }
//        return $data;
    }

    ## Проверка права доступа.
    function get_item_permissions_check( $request ){
        return $this->get_items_permissions_check( $request );
    }

    /**
     * Получает отдельный ресурс.
     *
     * @param WP_REST_Request $request Текущий запрос.
     *
     * @return array
     */
    function get_item( $request ){
        global $wpdb;
        $id = (int) $request['id'];
        $table = $wpdb->prefix . "c_dash__program";
        $programs = $wpdb->get_results($wpdb->prepare(
            "
        SELECT * 
        FROM $table WHERE id = %d
        ", $id
        ));
//
        if( ! $programs )
            return array($this->user_id);
        return $programs;
        //return $this->prepare_item_for_response( $programs, $request );
    }

    /**
     * Собирает данные ресурса в соответствии со схемой ресурса.
     *
     * @param WP_Post         $post    Объект ресурса, из которого будут взяты оригинальные данные.
     * @param WP_REST_Request $request Текущий запрос.
     *
     * @return array
     */
    function prepare_item_for_response( $post, $request ){

        $post_data = [];

        $schema = $this->get_item_schema();

        // We are also renaming the fields to more understandable names.
        if ( isset( $schema['properties']['id'] ) )
            $post_data['program_id'] = (int) $post->ID;
        $post_data['title'] = (string) $post->post_title;

        if ( isset( $schema['properties']['content'] ) )
            $post_data['content'] = apply_filters( 'the_content', $post->post_content, $post );

        return $post_data;
    }

    /**
     * Подготавливает ответ отдельного ресурса для добавления его в коллекцию ресурсов.
     *
     * @param WP_REST_Response $response Response object.
     *
     * @return array|mixed Response data, ready for insertion into collection data.
     */
    function prepare_response_for_collection( $response ){

        if ( ! ( $response instanceof WP_REST_Response ) ){
            return $response;
        }

        $data = (array) $response->get_data();
        $server = rest_get_server();

        if ( method_exists( $server, 'get_compact_response_links' ) ){
            $links = call_user_func( [ $server, 'get_compact_response_links' ], $response );
        }
        else {
            $links = call_user_func( [ $server, 'get_response_links' ], $response );
        }

        if ( ! empty( $links ) ){
            $data['_links'] = $links;
        }

        return $data;
    }

    ## Схема ресурса.
    function get_item_schema(){
        $schema = [
            // показывает какую версию схемы мы используем - это draft 4
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            // определяет ресурс который описывает схема
            'title'      => 'vehicle',
            'type'       => 'object',
            // в JSON схеме нужно указывать свойства в атрибуете 'properties'.
            'properties' => [
                'id' => [
                    'description' => 'Unique identifier for the object.',
                    'type'        => 'integer',
                    'context'     => [ 'view', 'edit', 'embed' ],
                    'readonly'    => true,
                ],
                'vin' => [
                    'description' => 'VIN code of vehicle.',
                    'type'        => 'string',
                ],
                // TODO добавить поля
                // []
            ],
        ];

        return $schema;
    }

    ## Устанавливает HTTP статус код для авторизации.
    function error_status_code(){
        return is_user_logged_in() ? 403 : 401;
    }

}
