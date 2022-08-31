<?php
namespace Controllers;


use WP_Error;
use WP_Post;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;



class REST_Protocol_Controller extends WP_REST_Controller {

    function __construct(){
        $this->namespace = 'courses_dashboard/v1';
        $this->rest_base = 'cd__protocol';
    }

    function register_routes(){

        register_rest_route( $this->namespace, "/$this->rest_base&students", [
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'get_students_protocol' ],
                'permission_callback' => [ $this, 'get_students_protocol_permissions_check' ],
            ],
            'schema' => [ $this, 'get_item_schema' ],
        ] );
        register_rest_route( $this->namespace, "/$this->rest_base&students_export", [
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'export_students_to_excel' ],
                'permission_callback' => [ $this, 'export_students_to_excel_permissions_check' ],
            ],
            'schema' => [ $this, 'export_students_to_excel_schema' ],
        ] );


    }

    function get_students_protocol_permissions_check( $request ){
//        if ( ! current_user_can( 'read' ) ){
//            return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the post resource.' ), [ 'status' => $this->error_status_code() ] );
//        }
        return true;
    }
    function export_students_to_excel_permissions_check( $request ){
        return true;
    }

    /**
     * Получает последние посты и отдает их в виде rest ответа.
     *
     * @param WP_REST_Request $request Текущий запрос.
     *
     * @return WP_Error|array
     */
    function get_students_protocol( $request ){

        $full_name =          !$request['full_name']          ?  '_______________' : $request['full_name'];
        $program_name =       !$request['program_name']       ?  '_______________' : $request['program_name'];
        $hours =              !$request['hours']              ?  '_______________' : $request['hours'];
        $date =               !$request['date']               ?  '_______________' : date("d.m.Y", strtotime($request['date']));
        $comission_lead =     !$request['comission_lead']     ?  '_______________' : $request['comission_lead'];
        $comission_member_1 = !$request['comission_member_1'] ?  '_______________' : $request['comission_member_1'];
        $comission_member_2 = !$request['comission_member_2'] ?  '_______________' : $request['comission_member_2'];
        $reg_number =         !$request['reg_number']         ?  '_______________' : $request['reg_number'];
        $users_ids =          !$request['users_ids']           ?  [] : $request['users_ids'];


        if(empty($users_ids)){
            return [
                'status' => 'false',
                'message' => 'Выберите студентов, которых нужно выгрузить'
            ];
        }


        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(__DIR__ . '/../views/students_control/template.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->getCell('C3')->setValue('Протокол №' . $reg_number . ' от ' . date("d.m.Y"));
        $worksheet->getCell('C6')->setValue($full_name);
        $worksheet->getCell('B8')->setValue("В соответствии с приказом руководителя организации от $date №$reg_number комиссия в составе:");
        $worksheet->getCell('D10')->setValue($comission_lead);
        $worksheet->getCell('D12')->setValue($comission_member_1);
        $worksheet->getCell('D14')->setValue($comission_member_2);
        $worksheet->getCell('B16')->setValue("провела проверку знаний требований охраны труда по программе: \"$program_name\" в объеме $hours часов");

        $users_counter = 1;
        $start_row = 18;
        foreach ($users_ids as $id){
            $user_info = get_userdata($id);
            $user_name = $user_info->data->display_name;
            $user_position = get_user_meta($id, 'user_position', true);

            $worksheet->insertNewRowBefore($start_row + $users_counter);
            $worksheet->getCell('B' . ($start_row + $users_counter))->setValue($users_counter);
            $worksheet->getCell('C' . ($start_row + $users_counter))->setValue($user_name);
            $worksheet->getCell('D' . ($start_row + $users_counter))->setValue($user_position);
            $worksheet->getCell('E' . ($start_row + $users_counter))->setValue($full_name);
            $worksheet->getCell('F' . ($start_row + $users_counter))->setValue('');
//        $worksheet->getCell('G' . ($start_row + $users_counter))->setValue($reg_number);
            $users_counter++;
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save(__DIR__ . '/../views/students_control/result.xlsx');


        return [
            'status' => 'true',
            'message' => '/wp-content/plugins/courses_dashboard_2/views/students_control/result.xlsx'
        ];
    }

    function export_students_to_excel( $request ){
        $users_ids = !$request['users_ids'] ? [] : $request['users_ids'];

        if(empty($users_ids)){
            return [
                'status' => 'false',
                'message' => 'Выберите студентов, которых нужно выгрузить'
            ];
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->getCell('A1')->setValue('Имя');
        $worksheet->getCell('B1')->setValue('Должность');
        $worksheet->getCell('C1')->setValue('Логин');
        $worksheet->getCell('D1')->setValue('Email');
        $worksheet->getCell('E1')->setValue('Пароль');
        $worksheet->getCell('F1')->setValue('СНИЛС');

        $users_counter = 1;
        foreach ($users_ids as $id){
            $users_counter++;
            $user_info = get_userdata($id);
            $user_name = $user_info->data->display_name;
            $user_login = $user_info->data->user_login;
            $user_email = $user_info->data->user_email;
            $user_position = get_user_meta($id, 'user_position', true);
            $user_snils = get_user_meta($id, 'user_snils', true);

            $worksheet->getCell('A' . ($users_counter))->setValue($user_name);
            $worksheet->getCell('B' . ($users_counter))->setValue($user_position);
            $worksheet->getCell('C' . ($users_counter))->setValue($user_login);
            $worksheet->getCell('D' . ($users_counter))->setValue($user_email);
            $worksheet->getCell('E' . ($users_counter))->setValue('123');
            $worksheet->getCell('F' . ($users_counter))->setValue($user_snils);
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save(__DIR__ . '/../views/students_control/result.xlsx');

        return [
            'status' => 'true',
            'message' => '/wp-content/plugins/courses_dashboard_2/views/students_control/result.xlsx'
        ];
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

    function export_students_to_excel_schema()
    {
        $schema = [
            // показывает какую версию схемы мы используем - это draft 4
            '$schema' => 'http://json-schema.org/draft-04/schema#',
            // определяет ресурс который описывает схема
            'title' => 'vehicle',
            'type' => 'object',
            // в JSON схеме нужно указывать свойства в атрибуете 'properties'.
            'properties' => [
                'id' => [
                    'description' => 'Unique identifier for the object.',
                    'type' => 'integer',
                    'context' => ['view', 'edit', 'embed'],
                    'readonly' => true,
                ],
                'vin' => [
                    'description' => 'VIN code of vehicle.',
                    'type' => 'string',
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
