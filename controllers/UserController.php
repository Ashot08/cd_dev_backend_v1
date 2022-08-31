<?php
namespace Controllers;


use WP_Error;
use WP_Post;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;



class REST_User_Controller extends WP_REST_Controller {

    function __construct(){
        $this->namespace = 'courses_dashboard/v1';
        $this->rest_base = 'cd__user';
    }

    function register_routes(){

        register_rest_route( $this->namespace, "/$this->rest_base&create", [
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'create_user' ],
                'permission_callback' => [ $this, 'create_user_permissions_check' ],
                'args'     => array(
                    'user_email' => array(
                        'type'     => 'string', // значение параметра должно быть строкой
                        'required' => true,     // параметр обязательный
                    ),
                )
            ],
            'schema' => [ $this, 'get_item_schema' ],
        ] );

    }

    function create_user_permissions_check( $request ){
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
    function create_user( $request ){
        $email = $request->get_param('user_email');

        $program_id = $request['program_id'];

        $is_valid_email = filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $email);
        $message = '';
        if($is_valid_email){
            $user_id = wp_insert_user( $request );

            if( ! is_wp_error( $user_id ) ){
                update_user_meta($user_id, 'user_snils', $request['user_snils']);
                update_user_meta($user_id, 'user_position', $request['user_position']);

                $creds = array();
                $creds['user_login'] = $request['user_login'];
                $creds['user_password'] = '123';
                $creds['remember'] = true;

                global $wpdb;
                $table_name = $wpdb->prefix . "c_dash__students_programs";
                $wpdb->insert( $table_name, [ 'student_id' => $user_id, 'program_id' =>  $program_id ]);

                $message = 'Студент успешно создан';
            }
            else {
                $message = $user_id->get_error_message();
            }
        }else{
            $message = 'Поле E-mail заполнено некорректно.';
        }

        return array(
            'status' => '',
            'message' => $message
        );

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
