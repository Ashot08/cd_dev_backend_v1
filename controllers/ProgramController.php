<?php
namespace Controllers;

use Models\Course;
use Models\Program;
use Models\Director;
use Models\Key;
use Models\Student;

use WP_Error;
use WP_Post;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

require_once __DIR__ . '/../views/program/programs_list.php';
require_once __DIR__ . '/../views/program/create_program.php';
require_once __DIR__ . '/../views/program/program_details.php';
require_once __DIR__ . '/../views/program/chapters_list.php';

class ProgramController{
    public function actionViewDirectorPrograms($director_id){
        $model = new Program();
        //$keyModel = new Key();
        //$studentModel = new Student();

        $user = get_current_user_id();
        $user_info = get_userdata(get_current_user_id());
        $is_company = false;
        if(is_user_logged_in() && isset($user_info->caps["customer_company"])){
            $is_company = true;
        }
        if(!$user)return false;
        if($is_company){
            $model = $model->getProgramsContentByDirectorId($user);
        }else{
            $programs_ids =  $model->getProgramsByStudentId($user);
            $programs = [];
            if(is_array($programs_ids) && !empty($programs_ids)){
                foreach ($programs_ids as $id){
                    $program = $model->getProgram($id->program_id)[0]->id;
                    if($program){
                        $programs[] = $model->getProgram($program);
                    }
                }
            }
            $model = $programs;
        }
        return programs_list($model);
    }
    public function actionViewCreateProgram($director_id){
        $model = new Course();
        $model = $model->getCoursesByDirectorId($director_id);
        return create_program($model);
    }
    public function actionViewChaptersList($course_id){
        return chapters_list($course_id);
    }
    public function actionViewProgramDetails($program_id){
        $model = new Course();
        $programModel = new Program();
        $model = $model->getCoursesByProgramId($program_id);
        $program_info = $programModel->getProgram($program_id)[0] ?? '';
        if($model){
            return program_details($model, $program_id, $program_info);
        }else{
            echo '';
        }

    }
    public function actionCreateProgram($director_id, $title, $description, $coursesIds, $image){
        if(!$title){
            echo 'errorName';
        }elseif (!$coursesIds){
            echo 'errorCoursesIds';
        }else{
            $model = new Program($title, $description, $image, $director_id);
            $programId = $model->createProgram();
            $course = new Course();
            foreach ($coursesIds as $courseId){
                $course->connectCourseWithProgram($courseId, $programId);
            }
            //echo 'success';
        }
    }
    public function actionAddCourseToDirector($director_id, $course_id){
        $model = new Director();
        $model->connectDirectorWithCourse($director_id, $course_id);
        echo 'Вы (id = ' . $director_id . ') зачислены на курс с ID = ' . $course_id;
    }
}





class REST_Program_Controller extends WP_REST_Controller {

    function __construct(int $user_id = 0){
        $this->namespace = 'courses_dashboard/v1';
        $this->rest_base = 'cd__programs';
        $this->user_id = $user_id;
    }

    function register_routes(){

        register_rest_route( $this->namespace, "/$this->rest_base&page=(?P<page>[\w]+)&offset=(?P<offset>[\w]+)", [
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
        global $wpdb;
        $programs = [];
        $offset   = $request["page"] * $request['offset'];
        $limit = $request['offset'];
        $table = $wpdb->prefix . "c_dash__program";

        $count = $wpdb->get_results($wpdb->prepare(
            "
            SELECT count(*) as 'count'
            FROM $table WHERE director_id = %d
            ", $this->user_id
            )
        );

        if(!$count) return (array('Не найдено'));

        $programs = $wpdb->get_results($wpdb->prepare(
            "
            SELECT * 
            FROM $table WHERE director_id = %d
            LIMIT $offset, $limit;
            ", $this->user_id
            )
        );
//
        return array(
            'count' => $count[0]->count,
            'programs' => $programs
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
