<?php


function students_control_details($data, $program_id, $program_info){
    $users_array = [];
    $program_title = $program_info->title ?? '';

    $courseModel = new \Models\Course();
    $program_courses = $courseModel->getCoursesByProgramId($program_id);

    foreach ($data as $user){
        $users_array[] = $user->student_id;
    }
    $users_array = array_unique($users_array);
    ?>

    <div class="cd__tab_details_title">
        Студенты программы <strong><?= $program_title; ?></strong>:
    </div>

<?php if($users_array): ?>

    <table class="cd__table">
        <tbody>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Логин</th>
            <th>Email</th>
            <th>Пароль</th>
            <th>СНИЛС</th>
            <th>Прогресс</th>

<!--            <th>Прогресс</th>-->
            <th>
                <label class="cd__table_select_all_label">
                    <input type="checkbox" data-action="cd__table_select_all">
                    Все
                </label>
            </th>
        </tr>
        <?php foreach ($users_array as $student_id):?>

            <?php
            $user_info = get_userdata($student_id);
            $user_id = $user_info->data->ID;
            $user_name = $user_info->data->display_name;
            $user_login = $user_info->data->user_login;
            $user_email = $user_info->data->user_email;
            $user_snils = get_user_meta( $user_id, 'user_snils', true );
            $user_pass = '123';

            $total_progress = 0;

            foreach ($program_courses as $course){
                $term = get_term($course->course_id);
                $course_category = new MBLCategory($term);
                $total_progress += $course_category->getProgress($user_id) ?? '0';
            }

            ?>

            <tr>
                <td><?php echo $user_id; ?></td>
                <td><?php echo $user_name; ?></td>
                <td><?php echo $user_login; ?></td>
                <td><?php echo $user_email; ?></td>
                <td><?php echo $user_pass; ?></td>
                <td><?php echo $user_snils; ?></td>
                <td>
                    <?php echo number_format($total_progress/count($program_courses), 1, '.', ''); ?>%
                </td>
                <td>
                    <label>
                        <input class="cd__table_select_user_checkbox" data-action="cd__select_item" data-student_id="<?= $user_id; ?>" type="checkbox">
                    </label>
                </td>
            </tr>

            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>

    <div>
        <br>
        <button data-action="toggle__add_new_student_form">Добавить слушателя</button>
        <br><br>
        <div class="cd__add_new_student_form">
            <label for="">
                <input type="text" name="user_login" placeholder="Логин" required>
            </label>
            <label for="">
                <input type="text" name="first_name" placeholder="ФИО">
            </label>
            <label for="">
                <input class="cd__snils_input" type="text" name="snils" placeholder="СНИЛС">
            </label>
            <label for="">
                <input type="email" name="user_email" placeholder="email" required>
            </label>
            <label for="">
                <input type="email" name="user_position" placeholder="Должность">
            </label>
            <button data-program_id="<?= $program_id; ?>" data-action="cd__send_add_new_student_form">
                Добавить
            </button>
            <div class="cd__add_new_student_form_result"></div>
        </div>
    </div>

    <div>
        <button class="cd__hidden_form_toggler">
            <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                <path fill="currentColor" d="M21.17 3.25Q21.5 3.25 21.76 3.5 22 3.74 22 4.08V19.92Q22 20.26 21.76 20.5 21.5 20.75 21.17 20.75H7.83Q7.5 20.75 7.24 20.5 7 20.26 7 19.92V17H2.83Q2.5 17 2.24 16.76 2 16.5 2 16.17V7.83Q2 7.5 2.24 7.24 2.5 7 2.83 7H7V4.08Q7 3.74 7.24 3.5 7.5 3.25 7.83 3.25M7 13.06L8.18 15.28H9.97L8 12.06L9.93 8.89H8.22L7.13 10.9L7.09 10.96L7.06 11.03Q6.8 10.5 6.5 9.96 6.25 9.43 5.97 8.89H4.16L6.05 12.08L4 15.28H5.78M13.88 19.5V17H8.25V19.5M13.88 15.75V12.63H12V15.75M13.88 11.38V8.25H12V11.38M13.88 7V4.5H8.25V7M20.75 19.5V17H15.13V19.5M20.75 15.75V12.63H15.13V15.75M20.75 11.38V8.25H15.13V11.38M20.75 7V4.5H15.13V7Z" />
            </svg>
            Сформировать протокол
        </button>
        <div class="cd__hidden_form_box cd__send_program_details_document_form">
            <h4>Заполните нужные поля</h4>

            <label for="">
                <input type="text" name="full_name" placeholder="Полное наименование организации">
            </label>
            <label for="">
                <input type="text" name="program_name" placeholder="Название программы" value="<?= $program_title; ?>">
            </label>
            <label for="">
                Дата создания приказа руководителя
                <input type="date" name="date" placeholder="Дата создания приказа руководителя">
            </label>
            <label for="">
                <input type="text" name="reg_number" placeholder="Номер приказа">
            </label>
            <label for="">
                <input type="text" name="comission_lead" placeholder="Председатель">
            </label>
            <label for="">
                <input type="text" name="comission_member_1" placeholder="Член комиссии №1">
            </label>
            <label for="">
                <input type="text" name="comission_member_2" placeholder="Член комиссии №2">
            </label>
            <label for="">
                <input type="text" name="hours" placeholder="Объем программы в часах">
            </label>

            <button data-action="cd__send_student_control_details_document">
                Сгенерировать
            </button>
            <div class="cd__send_program_details_document_result"></div>
        </div>

        <button data-action="cd__student_control_details_download_students_info" class="">
            <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                <path fill="currentColor" d="M21.17 3.25Q21.5 3.25 21.76 3.5 22 3.74 22 4.08V19.92Q22 20.26 21.76 20.5 21.5 20.75 21.17 20.75H7.83Q7.5 20.75 7.24 20.5 7 20.26 7 19.92V17H2.83Q2.5 17 2.24 16.76 2 16.5 2 16.17V7.83Q2 7.5 2.24 7.24 2.5 7 2.83 7H7V4.08Q7 3.74 7.24 3.5 7.5 3.25 7.83 3.25M7 13.06L8.18 15.28H9.97L8 12.06L9.93 8.89H8.22L7.13 10.9L7.09 10.96L7.06 11.03Q6.8 10.5 6.5 9.96 6.25 9.43 5.97 8.89H4.16L6.05 12.08L4 15.28H5.78M13.88 19.5V17H8.25V19.5M13.88 15.75V12.63H12V15.75M13.88 11.38V8.25H12V11.38M13.88 7V4.5H8.25V7M20.75 19.5V17H15.13V19.5M20.75 15.75V12.63H15.13V15.75M20.75 11.38V8.25H15.13V11.38M20.75 7V4.5H15.13V7Z" />
            </svg>
            Скачать данные студентов
        </button>
        <div class="cd__student_control_details_download_students_info_result"></div>
    </div>

    <script>
        /* Inputmask
        **********************************************/
        jQuery(document).ready(function(){
            jQuery(".cd__snils_input").inputmask("999-999-999 99");
            //jQuery("#example2").inputmask();
        });
        /*********************************************/
    </script>
<?php
}



