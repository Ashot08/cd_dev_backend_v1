<?php

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);


wp_enqueue_script( 'transist', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.transit/0.9.12/jquery.transit.min.js' , array('jquery'));
wp_enqueue_script( 'treeview', plugins_url( '/courses_dashboard_2/libs/treeview/jquery.treeview.js' ), array('jquery'));
wp_enqueue_script( 'inputmask', plugins_url( '/courses_dashboard_2/libs/inputmask/jquery.inputmask.min.js' ), array('jquery'));
wp_enqueue_script( 'courses_dashboard_ajax', plugins_url( '/courses_dashboard_2/ajax/ajax.js' ), array('jquery'));
wp_enqueue_script( 'script', plugins_url( '/courses_dashboard_2/js/script.js' ), array('jquery'));



wp_enqueue_style('courses_dashboard_css', plugins_url('/courses_dashboard_2/css/style.css'));
wp_enqueue_style('treeview_css', plugins_url('/courses_dashboard_2/libs/treeview/jquery.treeview.css'));



require_once __DIR__ . '/classes/WP_Term_Image.php';
add_action( 'admin_init', '\\Kama\\WP_Term_Image::init' );

use Controllers\AccessController;
use Controllers\REST_Program_Controller;
use Controllers\REST_Student_Controller;
use Controllers\REST_User_Controller;
use Controllers\REST_Protocol_Controller;

require_once __DIR__ . '/controllers/ProgramController.php';
require_once __DIR__ . '/controllers/StudentController.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/ProtocolController.php';
require_once __DIR__ . '/controllers/KeyController.php';
require_once __DIR__ . '/controllers/AccessController.php';
require_once __DIR__ . '/controllers/ProfileController.php';
require_once __DIR__ . '/models/Program.php';
require_once __DIR__ . '/models/Key.php';
require_once __DIR__ . '/models/Student.php';
require_once __DIR__ . '/models/Course.php';
require_once __DIR__ . '/models/Director.php';
require_once __DIR__ . '/ajax/ajax.php';
require_once __DIR__ . '/libs/vendor/autoload.php';



// Регистрация типов данных
//--------------------------------------------------------------------

add_action( 'init', 'register_post_types' );

function register_post_types(){

    register_post_type( 'cd__video', [
        'label'  => 'cd__video',
        'labels' => [
            'name'               => 'Видео', // основное название для типа записи
            'singular_name'      => 'Видео', // название для одной записи этого типа
            'add_new'            => 'Добавить Видео', // для добавления новой записи
            'add_new_item'       => 'Добавление Видео', // заголовка у вновь создаваемой записи в админ-панели.
            'edit_item'          => 'Редактирование Видео', // для редактирования типа записи
            'new_item'           => 'Новое Видео', // текст новой записи
            'view_item'          => 'Смотреть Видео', // для просмотра записи этого типа.
            'search_items'       => 'Искать Видео', // для поиска по этим типам записи
            'not_found'          => 'Не найдено', // если в результате поиска ничего не было найдено
            'not_found_in_trash' => 'Не найдено в корзине', // если не было найдено в корзине
            'parent_item_colon'  => '', // для родителей (у древовидных типов)
            'menu_name'          => 'Видео', // название меню
        ],
        'description'         => '',
        'public'              => true,
        // 'publicly_queryable'  => null, // зависит от public
        // 'exclude_from_search' => null, // зависит от public
        // 'show_ui'             => null, // зависит от public
        // 'show_in_nav_menus'   => null, // зависит от public
        'show_in_menu'        => null, // показывать ли в меню адмнки
        // 'show_in_admin_bar'   => null, // зависит от show_in_menu
        'show_in_rest'        => null, // добавить в REST API. C WP 4.7
        'rest_base'           => null, // $post_type. C WP 4.7
        'menu_position'       => null,
        'menu_icon'           => null,
        //'capability_type'   => 'post',
        //'capabilities'      => 'post', // массив дополнительных прав для этого типа записи
        //'map_meta_cap'      => null, // Ставим true чтобы включить дефолтный обработчик специальных прав
        'hierarchical'        => false,
        'supports'            => [ 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats' ], // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
        'taxonomies'          => ['wpm-category'],
        'has_archive'         => false,
        'rewrite'             => true,
        'query_var'           => true,
    ] );

}

//--------------------------------------------------------------------



// Подключаем скрипту courses_dashboard_ajax атрибут type="module"
//--------------------------------------------------------------------
add_filter('script_loader_tag', 'add_type_attribute' , 10, 3);

function add_type_attribute($tag, $handle, $src) {
    // if not your script, do nothing and return original $tag
    if ( 'courses_dashboard_ajax' !== $handle ) {
        return $tag;
    }
    // change the script tag by adding type="module" and return it.
    $tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
    return $tag;
}
//--------------------------------------------------------------------


// Кастомные поля для курсов
//--------------------------------------------------------------------
add_action( 'wpm-category_add_form_fields', 'true_add_cat_fields' );

function true_add_cat_fields( $taxonomy ) {

    echo '<div style="padding: 10px;background-color: #fff;border: 1px solid #d2f3f4;">
    <h4>
        Возможность выбрать при создании Программы:
    </h4>    
    <input type="radio" checked id="cd__course_input_status_1"
     name="cd__course_input_status" value="1">
    <label for="cd__course_input_status_1">Стандартное поведение</label><br>

    <input type="radio" id="cd__course_input_status_2"
     name="cd__course_input_status" value="2">
    <label for="cd__course_input_status_2">Всегда выбран</label><br>

    <input type="radio" id="cd__course_input_status_3"
     name="cd__course_input_status" value="3">
    <label for="cd__course_input_status_3">Нет возможности выбирать (наследует от родителя)</label><br>
    
    <input type="radio" id="cd__course_input_status_4"
     name="cd__course_input_status" value="4">
    <label for="cd__course_input_status_4">Выбран, если выбран хотя бы один из соседних</label><br></div>';


    echo '<div style="padding: 10px;background-color: #fff;border: 1px solid #d2f3f4;margin: 20px 0;">
    <h4>
        Нагрузка в часах:
    </h4>    
    <input type="number" checked id="cd__course_input_hours"
     name="cd__course_input_hours" >
    <label for="cd__course_input_hours">Сколько времени требуется для прохождения курса</label>
    </div>';


    echo '<div style="padding: 10px;background-color: #fff;border: 1px solid #d2f3f4;margin: 20px 0;">
    <h4>
        Развернут в списке:
    </h4>    
    <input type="checkbox" id="cd__course_input_open_list"
     name="cd__course_input_open_list" >
    <label for="cd__course_input_open_list">(представлен в развёрнутом виде в списке курсов при создании программы)</label>
    </div>';
}

add_action( 'wpm-category_edit_form_fields', 'true_edit_term_fields', 10, 2 );

function true_edit_term_fields( $term, $taxonomy ) {

    // сначала получаем значения этих полей
    // заголовок
    $cd__course_input_status = get_term_meta( $term->term_id, 'cd__course_input_status', true );
    $cd__course_input_status_1 = '';
    $cd__course_input_status_2 = '';
    $cd__course_input_status_3 = '';
    $cd__course_input_status_4 = '';
    if($cd__course_input_status === '1') $cd__course_input_status_1 = 'checked';
    if($cd__course_input_status === '2') $cd__course_input_status_2 = 'checked';
    if($cd__course_input_status === '3') $cd__course_input_status_3 = 'checked';
    if($cd__course_input_status === '4') $cd__course_input_status_4 = 'checked';

    $cd__course_input_hours = get_term_meta( $term->term_id, 'cd__course_input_hours', true );
    $cd__course_input_open_list = get_term_meta( $term->term_id, 'cd__course_input_open_list', true );
    if($cd__course_input_open_list) $cd__course_input_open_list = 'checked';

    echo '<div style="padding: 10px;background-color: #fff;border: 1px solid #d2f3f4;">
    <h4>
        Возможность выбрать при создании Программы:
    </h4>    
    <input type="radio" ' . $cd__course_input_status_1 . ' id="cd__course_input_status_1"
     name="cd__course_input_status" value="1">
    <label for="cd__course_input_status_1">Стандартное поведение</label><br>

    <input type="radio" ' . $cd__course_input_status_2 . ' id="cd__course_input_status_2"
     name="cd__course_input_status" value="2">
    <label for="cd__course_input_status_2">Всегда выбран</label><br>

    <input type="radio" ' . $cd__course_input_status_3 . ' id="cd__course_input_status_3"
     name="cd__course_input_status" value="3">
    <label for="cd__course_input_status_3">Нет возможности выбирать (наследует от родителя)</label><br>
    
    <input type="radio" ' . $cd__course_input_status_4 . ' id="cd__course_input_status_4"
     name="cd__course_input_status" value="4">
    <label for="cd__course_input_status_4">Выбран, если выбран хотя бы один из соседних</label><br></div>';


    echo '<div style="padding: 10px;background-color: #fff;border: 1px solid #d2f3f4;margin: 20px 0;">
    <h4>
        Нагрузка в часах:
    </h4>    
    <input type="number" checked id="cd__course" value="' .  $cd__course_input_hours . '" name="cd__course_input_hours">
    <label for="cd__course_input_hours">Сколько времени требуется для прохождения курса</label>
    </div>';

    echo '<div style="padding: 10px;background-color: #fff;border: 1px solid #d2f3f4;margin: 20px 0;">
    <h4>
        Развернут в списке:
    </h4>    
    <input 
      type="checkbox"     
      id="cd__course_input_open_list"
      name="cd__course_input_open_list" 
      ' . $cd__course_input_open_list . '
      >
    <label for="cd__course_input_open_list">(представлен в развёрнутом виде в списке курсов при создании программы)</label>
    </div>';
}

add_action( 'created_wpm-category', 'true_save_term_fields' );
add_action( 'edited_wpm-category', 'true_save_term_fields' );

function true_save_term_fields( $term_id ) {
    if( isset( $_POST[ 'cd__course_input_status' ] ) ) {
        update_term_meta( $term_id, 'cd__course_input_status', $_POST[ 'cd__course_input_status' ] );
    } else {
        delete_term_meta( $term_id, 'cd__course_input_status' );
    }


    if( isset( $_POST[ 'cd__course_input_hours' ] ) ) {
        update_term_meta( $term_id, 'cd__course_input_hours', $_POST[ 'cd__course_input_hours' ] );
    } else {
        delete_term_meta( $term_id, 'cd__course_input_hours' );
    }

    if( isset( $_POST[ 'cd__course_input_open_list' ] ) ) {
        update_term_meta( $term_id, 'cd__course_input_open_list', $_POST[ 'cd__course_input_open_list' ] );
    } else {
        delete_term_meta( $term_id, 'cd__course_input_open_list' );
    }
}


//--------------------------------------------------------------------



// Удаление строки из базы данных
//--------------------------------------------------------------------
//function deleteCourseFromDirectorsCourses(int $course_id){
//    global $wpdb;
//    $table = $wpdb->prefix . "c_dash__directors_courses";
//    $data = $wpdb->get_results($wpdb->prepare(
//        "
//        DELETE
//        FROM $table WHERE course_id = %d
//        ", $course_id
//    ));
//    return $data;
//}
//deleteCourseFromDirectorsCourses(40);
//--------------------------------------------------------------------




// Добавление колонки базы данных
//--------------------------------------------------------------------

//function addDbColumn(){
//    global $wpdb;
//    $table = $wpdb->prefix . "c_dash__students_programs";
//    $wpdb->query(
//        "
//        ALTER TABLE $table
//        ADD COLUMN start_date datetime DEFAULT NOW()
//        "
//    );
//}
//addDbColumn();

//function updateDbColumn(){
//    global $wpdb;
//    $table = $wpdb->prefix . "c_dash__students_programs";
//    $wpdb->query(
//        "
//        UPDATE $table SET start_date = '0000-00-00 00:00:00'
//        "
//    );
//}
//updateDbColumn();

//--------------------------------------------------------------------


// Шорткод блока видео для cd__video post type
//--------------------------------------------------------------------

//--------------------------------------------------------------------


// Регистрируем маршруты
add_action( 'rest_api_init', 'cd__register_routes' );
function cd__register_routes() {
    //$user_id = get_current_user_id();
    $user_id = 2;

    $programController = new REST_Program_Controller($user_id);
    $studentController = new REST_Student_Controller();
    $userController = new REST_User_Controller();
    $protocolController = new REST_Protocol_Controller();
    $programController->register_routes();
    $studentController->register_routes();
    $userController->register_routes();
    $protocolController->register_routes();
}

