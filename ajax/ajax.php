<?php

use Controllers\ProgramController;
use Controllers\StudentController;
use Controllers\KeyController;
use Controllers\ProfileController;
use PhpOffice\PhpWord\Element\Table;

function cd__register_new_user($userdata, $error_text, $success_text){
    
    $email = $userdata['user_email'];
    $is_valid_email = filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $email);
    $message = '';
    if($is_valid_email){
        $user_id = wp_insert_user( $userdata );

        if( ! is_wp_error( $user_id ) ){
            update_user_meta($user_id, 'user_snils', $userdata['user_snils']);
            update_user_meta($user_id, 'user_position', $userdata['user_position']);

            $creds = array();
            $creds['user_login'] = $userdata['user_login'];
            $creds['user_password'] = '123';
            $creds['remember'] = true;

            $studentController = new StudentController();
            $studentController->actionOnlyConnectStudentWithProgram($user_id, $userdata['program_id']);

            $message = '<div class="scbt__notice_success">' . $success_text . 'Студент успешно создан. </div>';
        }
        else {
            $message = '<div class="scbt__notice_error">' . $error_text . ' ' . $user_id->get_error_message() . '</div>';
        }
    }else{
        $message = '<div class="scbt__notice_error">' . $error_text . ' Поле E-mail заполнено некорректно.</div>';
    }
    return $message;
}



add_action('wp_ajax_cd__get_director_programs_list', 'cd__get_director_programs_list');
add_action('wp_ajax_nopriv_cd__get_director_programs_list', 'cd__get_director_programs_list');
function cd__get_director_programs_list(){
    $director_id = $_POST['id'];
    $controller = new ProgramController();
    $controller->actionViewDirectorPrograms($director_id);
    wp_die();
}

add_action('wp_ajax_cd__get_profile', 'cd__get_profile');
add_action('wp_ajax_nopriv_cd__get_profile', 'cd__get_profile');
function cd__get_profile(){
    $controller = new ProfileController();
    $controller->actionViewProfile();
    wp_die();
}

add_action('wp_ajax_cd__get_students_control_details', 'cd__get_students_control_details');
add_action('wp_ajax_nopriv_cd__get_students_control_details', 'cd__get_students_control_details');
function cd__get_students_control_details(){
    $program_id = $_POST['id'];
    $controller = new StudentController();
    $controller->actionViewStudentsControlDetails($program_id);
    wp_die();
}


add_action('wp_ajax_cd__get_students_control_programs_list', 'cd__get_students_control_programs_list');
add_action('wp_ajax_nopriv_cd__get_students_control_programs_list', 'cd__get_students_control_programs_list');
function cd__get_students_control_programs_list(){
    $director_id = $_POST['id'];
    $controller = new StudentController();
    $controller->actionViewDirectorPrograms($director_id);
    wp_die();
}

add_action('wp_ajax_cd__get_keys_programs_list', 'cd__get_keys_programs_list');
add_action('wp_ajax_nopriv_cd__get_keys_programs_list', 'cd__get_keys_programs_list');
function cd__get_keys_programs_list(){
    $director_id = get_current_user_id();
    $controller = new KeyController();
    $controller->actionViewDirectorPrograms($director_id);
    wp_die();
}

add_action('wp_ajax_cd__get_key_programs_details', 'cd__get_key_programs_details');
add_action('wp_ajax_nopriv_cd__get_key_programs_details', 'cd__get_key_programs_details');
function cd__get_key_programs_details(){
    $program_id = $_POST['id'];
    $controller = new KeyController();
    $controller->actionViewProgramKeys($program_id);
    wp_die();
}

add_action('wp_ajax_cd__create_and_attach_key', 'cd__create_and_attach_key');
add_action('wp_ajax_nopriv_cd__create_and_attach_key', 'cd__create_and_attach_key');
function cd__create_and_attach_key(){
    $director_id = get_current_user_id();
    $program_id = $_POST['id'];
    $controller = new KeyController();
    $controller->actionCreateAndAttachKey($director_id, $program_id);
    wp_die();
}



add_action('wp_ajax_cd__get_create_program_view', 'cd__get_create_program_view');
add_action('wp_ajax_nopriv_cd__get_create_program_view', 'cd__get_create_program_view');
function cd__get_create_program_view(){
    //$director_id = $_POST['id'];
    $director_id = get_current_user_id();
    $controller = new ProgramController();
    $controller->actionViewCreateProgram($director_id);
    wp_die();
}

add_action('wp_ajax_cd__get_program_details', 'cd__get_program_details');
add_action('wp_ajax_nopriv_cd__get_program_details', 'cd__get_program_details');
function cd__get_program_details(){
    $program_id = $_POST['id'];
    $controller = new ProgramController();
    $controller->actionViewProgramDetails($program_id);
    wp_die();
}

add_action('wp_ajax_cd__create_new_program', 'cd__create_new_program');
add_action('wp_ajax_nopriv_cd__create_new_program', 'cd__create_new_program');
function cd__create_new_program(){
    $director_id = get_current_user_id();
    $name = $_POST['name'];
    $description = $_POST['description'];
    $courses = $_POST['courses'] ?? [];

    $controller = new ProgramController();
    $controller->actionCreateProgram($director_id, $name, $description, $courses, '' );
    wp_die();
}


add_action('wp_ajax_cd__create_new_separate_programs', 'cd__create_new_separate_programs');
add_action('wp_ajax_nopriv_cd__create_new_separate_programs', 'cd__create_new_separate_programs');
function cd__create_new_separate_programs(){
    $director_id = get_current_user_id();
    $name = $_POST['name'];
    $description = $_POST['description'];
    $courses = $_POST['courses']['chapters'] ?? [];

    if(!$name){
        echo 'errorName';
        wp_die();
    }elseif(!$courses){
        echo 'errorCoursesIds';
        wp_die();
    }
    foreach ($courses as $key => $value){
        $title = $name . ' (' . get_term($key, 'wpm-category')->name .')';
        $courses_arr = $value;

        $controller = new ProgramController();
        $controller->actionCreateProgram( $director_id, $title, $description, $courses_arr, '' );
    }

    wp_die();
}


add_action('wp_ajax_cd__add_course_to_director', 'cd__add_course_to_director');
add_action('wp_ajax_nopriv_cd__add_course_to_director', 'cd__add_course_to_director');
function cd__add_course_to_director(){
    $course_id = $_POST['id'];
    $director_id = get_current_user_id();

    $controller = new ProgramController();
    $controller->actionAddCourseToDirector($director_id, $course_id);
    wp_die();
}

add_action('wp_ajax_cd__get_chapters_list', 'cd__get_chapters_list');
add_action('wp_ajax_nopriv_cd__get_chapters_list', 'cd__get_chapters_list');
function cd__get_chapters_list(){
    $course_id = $_POST['id'];
    $controller = new ProgramController();
    $controller->actionViewChaptersList($course_id);
    wp_die();
}

add_action('wp_ajax_cd__connect_student_with_program', 'cd__connect_student_with_program');
add_action('wp_ajax_nopriv_cd__connect_student_with_program', 'cd__connect_student_with_program');
function cd__connect_student_with_program(){
    $key = $_POST['key'];
    $controller = new StudentController();
    $controller->actionConnectStudentWithProgram($key);
    wp_die();
}




// Создание нового пользователя и добавление его к программе
//--------------------------------------------------------------------
add_action('wp_ajax_cd__add_new_student', 'cd__add_new_student');
add_action('wp_ajax_nopriv_cd__add_new_student', 'cd__add_new_student');
function cd__add_new_student(){
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL)
            && preg_match('/@.+\./', $email);
    }

    // Проверим защитные поля
    //if( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'vb_new_user' ) ){
    //	die( 'Оооопс, попробуйте еще раз, но чуть позже.' );
    //}

    // Данные переданные в $_POST
    $userdata = [
        'user_login' => $_POST['user_login'],
        'user_pass'  => $_POST['userPassword'] ?? '123',
        'user_email' => $_POST['user_email'],
        'role'       => 'customer',
        'first_name' => $_POST['first_name'],
        'user_snils' => $_POST['user_snils'],
        'program_id' => $_POST['program_id']
    ];

    /**
     * Проверять/очищать передаваемые поля не обязательно,
     * WP сделает это сам.
     */
    $is_valid_email = isValidEmail($_POST['user_email']);

    if($is_valid_email){
        $user_id = wp_insert_user( $userdata );
        if( ! is_wp_error( $user_id ) ){

            update_user_meta($user_id, 'user_snils', $userdata['user_snils']);
          //  update_user_meta($user_id, 'billing_phone', $_POST['billing_phone']);
         //   update_user_meta($user_id, 'user_company_name', $_POST['user_company_name']);
         //   update_user_meta($user_id, 'user_inn', $_POST['user_inn']);
            update_user_meta($user_id, 'user_position', $_POST['user_position']);

            $creds = array();
            $creds['user_login'] = $userdata['user_login'];
            $creds['user_password'] = '123';
            $creds['remember'] = true;

            //$user = wp_signon( $creds, false );

            $studentController = new StudentController();
            $studentController->actionOnlyConnectStudentWithProgram($user_id, $userdata['program_id']);

            echo '<div class="scbt__notice_success">Студент успешно создан. </div>';
        }
        else {
            echo '<div class="scbt__notice_error">' . $user_id->get_error_message() . '</div>';
        }
    }else{
        echo '<div class="scbt__notice_error">Поле E-mail заполнено некорректно.</div>';
    }


    // возврат

    wp_die();
}

//--------------------------------------------------------------------




add_action('wp_ajax_cd__add_students_mass', 'cd__add_students_mass');
add_action('wp_ajax_nopriv_cd__add_students_mass', 'cd__add_students_mass');
function cd__add_students_mass() {

    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Html');
    $rows_count = count( $worksheet->toArray() );
    //$cell = $worksheet->getCell('A2');
    //$message = $writer->save('php://output');
    if($rows_count < 2){
        echo '<div class="scbt__notice_error">Ваш excel документ не содержит пользователей</div>';
        wp_die();
    }
    for ($i = 1; $i < $rows_count; $i++) {
        $userdata = [];
        $row_number = $i + 1;
        $userdata = [
            'user_login' => $worksheet->getCell("A$row_number")->getValue(),
            'first_name' => $worksheet->getCell("B$row_number")->getValue(),
            'user_snils' => $worksheet->getCell("C$row_number")->getValue(),
            'user_email' => $worksheet->getCell("D$row_number")->getValue(),
            'user_position' => $worksheet->getCell("E$row_number")->getValue(),
            'user_pass' => '123',
            'role' => 'customer',
            'program_id' => $_POST['program_id']
        ];


        $result = cd__register_new_user($userdata, "Для пользователя из строки №$row_number ", "Из строки №$row_number " );
        echo $result;
    }

    wp_die();
}


//Обновление полей профиля
//--------------------------------------------------------------------

add_action('wp_ajax_cd__update_profile', 'cd__update_profile');
add_action('wp_ajax_nopriv_cd__update_profile', 'cd__update_profile');
function cd__update_profile() {
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL)
            && preg_match('/@.+\./', $email);
    }

    $user_id = get_current_user_id();
    $userdata = [
        'ID'       => $user_id,
    ];
    $first_name         = $_POST[ 'first_name' ] ?? null;
    $user_email         = $_POST[ 'user_email' ] ?? null;
    $billing_phone         = $_POST[ 'billing_phone' ] ?? null;

    $user_position      = $_POST[ 'user_position' ] ?? null;
    $user_snils         = $_POST[ 'user_snils' ] ?? null;
    $user_inn           = $_POST[ 'user_inn' ] ?? null;
    $user_company_name  = $_POST[ 'user_company_name' ] ?? null;


    if($first_name)        $userdata['first_name'] = $first_name;
    if($user_email)        $userdata['user_email'] = $user_email;



    $is_valid_email = isValidEmail($user_email);

    if($is_valid_email){
        wp_update_user( $userdata );

        if ( is_wp_error( $user_id ) ) {
            // Произошла ошибка, возможно такого пользователя не существует.
            echo '<div class="scbt__notice_error">Произошла ошибка</div>';
        }
        else {
            // Все ОК!
            if($user_position) update_user_meta($user_id, 'user_position', $user_position);
            if($user_snils) update_user_meta($user_id, 'user_snils', $user_snils);
            if($user_inn) update_user_meta($user_id, 'user_inn', $user_inn);
            if($user_company_name) update_user_meta($user_id, 'user_company_name', $user_company_name);
            if($billing_phone) update_user_meta($user_id, 'billing_phone', $billing_phone);

            echo '<div class="scbt__notice_success">Данные успешно обновлены </div>';
        }
    }else{
        echo '<div class="scbt__notice_error">Поле E-mail заполнено некорректно.</div>';
    }


    wp_die();

}

//--------------------------------------------------------------------



// Создание и скачивание docx файла Учебной программы
//--------------------------------------------------------------------
add_action('wp_ajax_cd__send_program_details_document', 'cd__send_program_details_document');
add_action('wp_ajax_nopriv_cd__send_program_details_document', 'cd__send_program_details_document');
function cd__send_program_details_document(){
    $phpWord = new  \PhpOffice\PhpWord\PhpWord();
    $phpWord->setDefaultFontName('Times New Roman');
    $phpWord->setDefaultFontSize(14);

    $properties = $phpWord->getDocInfo();
    $properties->setCreator('Учебный Центр');
    $properties->setCompany('My factory');
    $properties->setTitle('My title');
    $properties->setDescription('My description');
    $properties->setCategory('My category');
    $properties->setLastModifiedBy('My name');
    $properties->setCreated(mktime(0, 0, 0, 3, 12, 2014));
    $properties->setModified(mktime(0, 0, 0, 3, 14, 2014));
    $properties->setSubject('My subject');
    $properties->setKeywords('my, key, word');


    $full_name =     !$_POST['full_name']     ?  '_______________' : str_replace(array('\\'), '', $_POST['full_name']);
    $short_name =    !$_POST['short_name']    ?  '_______________' : str_replace(array('\\'), '', $_POST['short_name']);
    $program_name =  !$_POST['program_name']  ?  '_______________' : str_replace(array('\\'), '', $_POST['program_name']);
    $hours =         !$_POST['hours']         ?  '_______________' : str_replace(array('\\'), '', $_POST['hours']);
    $director_post = !$_POST['director_post'] ?  '_______________' : str_replace(array('\\'), '', $_POST['director_post']);
    $director_name = !$_POST['director_name'] ?  '_______________' : str_replace(array('\\'), '', $_POST['director_name']);
    $courses =       !$_POST['courses'] ?  [] : $_POST['courses'];


    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($_SERVER["DOCUMENT_ROOT"] . '/wp-content/plugins/courses_dashboard_2/views/program/template.docx');
    $templateProcessor->setValue('full_name', htmlspecialchars($full_name));
    $templateProcessor->setValue('short_name', htmlspecialchars($short_name));
    $templateProcessor->setValue('program_name', htmlspecialchars($program_name));
    $templateProcessor->setValue('hours', htmlspecialchars($hours));
    $templateProcessor->setValue('director_post', htmlspecialchars($director_post));
    $templateProcessor->setValue('director_name', htmlspecialchars($director_name));



    $section = $phpWord->addSection();

    $section->addPageBreak();

    $table = new Table(array('borderSize' => 12, 'borderColor' => '333333', 'cellSpacing' => 150 ));
    $row = $table->addRow();
    $row->addCell(1000, array('vMerge' => 'restart', 'borderColor' => '006699'))->addText('№ п/п');
    $row->addCell(3500, array('vMerge' => 'restart', 'borderColor' => '006699'))->addText('Наименование разделов');
    $row->addCell(1500, array('vMerge' => 'restart', 'borderColor' => '006699'))->addText('Всего часов');
    $row->addCell(1500, array('gridSpan' => 2, 'vMerge' => 'restart', 'borderColor' => '006699'))->addText('В том числе');

    $row = $table->addRow();
    $row->addCell(1000, array('vMerge' => 'continue', 'borderColor' => '006699'));
    $row->addCell(3500, array('vMerge' => 'continue', 'borderColor' => '006699'));
    $row->addCell(1500, array('vMerge' => 'continue', 'borderColor' => '006699'));
    $row->addCell(1500, array('borderColor' => '006699'))->addText('лекции');
    $row->addCell(1500, array('borderColor' => '006699'))->addText('практические занятия, самоподготовка');

    $counter = 1;
    foreach ($courses as $course) {
        $row = $table->addRow();
        $term = get_term( $course, 'wpm-category' );
        $row->addCell(1000, array('borderColor' => '006699'))->addText($counter++);
        $row->addCell(3500, array('borderColor' => '006699'))->addText($term->name);
        $row->addCell(1500, array('borderColor' => '006699'))->addText( get_term_meta( $term->term_id, 'cd__course_input_hours', true ) );
        $row->addCell(1500, array('borderColor' => '006699'))->addText('');
        $row->addCell(1500, array('borderColor' => '006699'))->addText('');
    }

    $templateProcessor->setComplexBlock('program_plan', $table);



    $section = $phpWord->addSection();
    $section->addPageBreak();

    $table = new Table(array('borderSize' => 12, 'borderColor' => '333333', 'cellSpacing' => 150 ));
    $row = $table->addRow();
    $row->addCell(1000, array('vMerge' => 'restart'))->addText('№ п/п');
    $row->addCell(3500, array('vMerge' => 'restart'))->addText('Наименование разделов');
    $row->addCell(1500, array('vMerge' => 'restart'))->addText('Всего часов');
    $row->addCell(1500, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('В том числе');

    $row = $table->addRow();
    $row->addCell(1000, array('vMerge' => 'continue'));
    $row->addCell(3500, array('vMerge' => 'continue'));
    $row->addCell(1500, array('vMerge' => 'continue'));
    $row->addCell(1500)->addText('лекции');
    $row->addCell(1500)->addText('практические занятия, самоподготовка');

    $counter = 1;
    foreach ($courses as $course) {
        $row = $table->addRow();
        $term = get_term( $course, 'wpm-category' );
        $row->addCell(1000)->addText($counter, array('bold' => true, 'size' => 13));
        $row->addCell(3500)->addText($term->name, array('bold' => true, 'size' => 13));
        $row->addCell(1500)->addText('');
        $row->addCell(1500)->addText('');
        $row->addCell(1500)->addText('');

        $posts = get_posts(
            array(
                'post_type' => 'wpm-page',
                'numberposts' => 999,
                'orderby' => 'menu_order',
                'order'       => 'ASC',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'wpm-category',
                        'field' => 'slug',
                        'terms' => array($term->slug),
                        'operator' => 'IN',
                    )
                )
            )
        );
        $subcounter = 1;
        foreach( $posts as $post ){
            setup_postdata( $post );
            $row = $table->addRow();
            $term = get_term( $course, 'wpm-category' );
            $row->addCell(1000)->addText($counter . '.' . $subcounter++);
            $row->addCell(3500)->addText($post->post_title);
            $row->addCell(1500)->addText('');
            $row->addCell(1500)->addText('');
            $row->addCell(1500)->addText('');
        }
        $counter++;
        wp_reset_postdata();

    }

    $templateProcessor->setComplexBlock('program_plan_2', $table);


    header("Content-Description: File Transfer");
    header('Content-Disposition: attachment; filename="scbt.docx"');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');


    $pathToSave = __DIR__ . '/../views/program/template_result.docx';
    $templateProcessor->saveAs($pathToSave);
    echo ('<a href="/wp-content/plugins/courses_dashboard_2/views/program/template_result.docx"> 
                Скачать файл  
               <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M5,20H19V18H5M19,9H15V3H9V9H5L12,16L19,9Z" />
                </svg> 
            </a>');
    wp_die();
}
//--------------------------------------------------------------------



// Создание и скачивание excel файла из Контроля студентов (Протокол)
//--------------------------------------------------------------------
add_action('wp_ajax_cd__send_student_control_details_document', 'cd__send_student_control_details_document');
add_action('wp_ajax_nopriv_cd__send_student_control_details_document', 'cd__send_student_control_details_document');
function cd__send_student_control_details_document(){
    $full_name =          !$_POST['full_name']          ?  '_______________' : $_POST['full_name'];
    $program_name =       !$_POST['program_name']       ?  '_______________' : $_POST['program_name'];
    $hours =              !$_POST['hours']              ?  '_______________' : $_POST['hours'];
    $date =               !$_POST['date']               ?  '_______________' : date("d.m.Y", strtotime($_POST['date']));
    $comission_lead =     !$_POST['comission_lead']     ?  '_______________' : $_POST['comission_lead'];
    $comission_member_1 = !$_POST['comission_member_1'] ?  '_______________' : $_POST['comission_member_1'];
    $comission_member_2 = !$_POST['comission_member_2'] ?  '_______________' : $_POST['comission_member_2'];
    $reg_number =         !$_POST['reg_number']         ?  '_______________' : $_POST['reg_number'];
    $users_ids =          !$_POST['users_ids']           ?  [] : $_POST['users_ids'];


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

    echo ('<a href="/wp-content/plugins/courses_dashboard_2/views/students_control/result.xlsx"> 
                Скачать файл  
               <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M5,20H19V18H5M19,9H15V3H9V9H5L12,16L19,9Z" />
                </svg> 
            </a>');
    wp_die();
}
//--------------------------------------------------------------------




// Создание и скачивание excel файла списка студентов из Контроля студентов
//--------------------------------------------------------------------
add_action('wp_ajax_cd__student_control_details_download_students_info', 'cd__student_control_details_download_students_info');
add_action('wp_ajax_nopriv_cd__student_control_details_download_students_info', 'cd__student_control_details_download_students_info');
function cd__student_control_details_download_students_info(){
    $users_ids = !$_POST['users_ids'] ? [] : $_POST['users_ids'];

    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
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

    echo ('<a href="/wp-content/plugins/courses_dashboard_2/views/students_control/result.xlsx"> 
                Скачать файл  
               <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M5,20H19V18H5M19,9H15V3H9V9H5L12,16L19,9Z" />
                </svg> 
            </a>');
    wp_die();
}
//--------------------------------------------------------------------
