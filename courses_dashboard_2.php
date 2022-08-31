<?php
/*
 * Plugin Name: courses_dashboard_2
 * Author URI:  https://kwork.ru/user/ashot08
 * Author:      Ashot08
*/
if (!defined('ABSPATH')) { exit; }
include_once('functions.php');


/*-------------------Подключение шаблона для страницы "аккаунт"--------------------*/


add_filter( 'theme_templates', 'add_my_template_to_list', 10, 4 );
add_filter( 'template_include', 'my_plugin_template_include' );

// Добавляем в список свои шаблоны для страниц
function add_my_template_to_list( $templates, $wp_theme, $post, $post_type ) {
    if ( 'page' === $post_type ) {
        // Дополняем массив шаблонов своими собственными
        $templates += my_plugin_templates();
    }

    return $templates;
}

// Формируем массив с шаблонами
function my_plugin_templates() {
    $base_path = basename( __DIR__ );

    return [
        $base_path . '/templates/account.php' => 'Шаблон страницы Аккаунт',
        //$base_path . '/templates/page-tpl-2.php' => 'Шаблон из плагина №2',
    ];
}

// Подключает шаблон страницы из плагина
function my_plugin_template_include( $template ) {
    // Если это не страница - возвращаем что есть

    if ( ! is_page('account') ) {
        return $template;
    }

    // Получаем сохранённый шаблон
    $path_slug = get_post_meta( get_the_ID(), '_wp_page_template', true );

    // Если шаблон не плагина - возвращаем что есть
    if ( ! in_array( $path_slug, array_keys( my_plugin_templates() ) ) ) {
        return $template;
    }

    // Создаем полный путь к файлу
    $path_file = wp_normalize_path( WP_PLUGIN_DIR . '/' . $path_slug );

    // Проверяем, есть ли физически файл шаблона и, если да - отдаем движку
    if ( file_exists( $path_file ) ) {
        return $path_file;
    }

    return $template;
}

/*----------------------------------------------------------*/


function courses_dashboard_activate() {
    create_program_table();
    create_key_table();
    create_directors_courses_table();
    create_programs_courses_table();
    create_students_courses_table();
    create_students_programs_table();
}


/*------------------- Создание таблиц в БД --------------------*/


function create_program_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__program";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
              create_date datetime DEFAULT NOW() NOT NULL,
              title TINYTEXT NOT NULL,
              image TINYTEXT,
              description TEXT,
              director_id bigint(20) UNSIGNED NOT NULL,
              PRIMARY KEY (id),
              foreign key (director_id) references wp_users (ID) ON UPDATE CASCADE ON DELETE CASCADE
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql);
    }
}
function create_key_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__key";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
              start_date datetime DEFAULT NOW() NOT NULL,
              access_key TINYTEXT NOT NULL,
              duration int DEFAULT 36500 NOT NULL,
              director_id bigint(20) UNSIGNED,
              student_id bigint(20) UNSIGNED,
              program_id bigint(20) UNSIGNED,
              PRIMARY KEY (id),
              foreign key (director_id) references wp_users (ID) ON UPDATE CASCADE ON DELETE SET NULL,
              foreign key (student_id) references wp_users (ID) ON UPDATE CASCADE ON DELETE SET NULL,
              foreign key (program_id) references wp_c_dash__program (id) ON UPDATE CASCADE ON DELETE SET NULL
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}

function create_directors_courses_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__directors_courses";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              director_id bigint(20) UNSIGNED NOT NULL,
              course_id bigint(20) UNSIGNED NOT NULL,
              PRIMARY KEY (director_id, course_id),
              foreign key (director_id) references wp_users (ID) ON UPDATE CASCADE ON DELETE CASCADE,
              foreign key (course_id) references wp_terms (term_id) ON UPDATE CASCADE ON DELETE CASCADE
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}

function create_programs_courses_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__programs_courses";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              program_id bigint(20) UNSIGNED NOT NULL,
              course_id bigint(20) UNSIGNED NOT NULL,
              PRIMARY KEY (program_id, course_id),
              foreign key (program_id) references wp_c_dash__program (id) ON UPDATE CASCADE ON DELETE CASCADE,
              foreign key (course_id) references wp_terms (term_id) ON UPDATE CASCADE ON DELETE CASCADE
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}

function create_students_courses_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__students_courses";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              student_id bigint(20) UNSIGNED NOT NULL,
              course_id bigint(20) UNSIGNED NOT NULL,
              PRIMARY KEY (student_id, course_id),
              foreign key (student_id) references wp_users (ID) ON UPDATE CASCADE ON DELETE CASCADE,
              foreign key (course_id) references wp_terms (term_id) ON UPDATE CASCADE ON DELETE CASCADE
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}
function create_students_programs_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__students_programs";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              student_id bigint(20) UNSIGNED NOT NULL,
              program_id bigint(20) UNSIGNED NOT NULL,
              PRIMARY KEY (student_id, program_id),
              foreign key (student_id) references wp_users (ID) ON UPDATE CASCADE ON DELETE CASCADE,
              foreign key (program_id) references wp_c_dash__program (id) ON UPDATE CASCADE ON DELETE CASCADE
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}



function drop_all_tables(){
    drop_table('c_dash__directors_courses');
    drop_table('c_dash__programs_courses');
    drop_table('c_dash__students_courses');
    drop_table('c_dash__students_programs');
    drop_table('c_dash__key');
    drop_table('c_dash__program');
}


function drop_table($table_name){
        global $wpdb;
        $table_name = $wpdb->prefix . $table_name;
        $sql = "DROP TABLE IF EXISTS $table_name";
        $result = $wpdb->query($sql);
}

/*-------------------------------------------------------------------------*/




register_activation_hook( __FILE__, 'courses_dashboard_activate' );
