<?php
/**
 * The template for displaying Account Page
 */


get_header();

?>

<div class="cd__account_wrapper">
    <div class="cd__account">



            <script defer="defer" src="/wp-content/plugins/courses_dashboard_2/templates/static/js/main.7bd33e47.js"></script>
            <link href="/wp-content/plugins/courses_dashboard_2/templates/static/css/main.073c9b0a.css" rel="stylesheet">


        <div id="root"></div>
        <?php
        $user_name = 'Unknown';
        $user_login = 'Unknown';
        $user_info = get_userdata(get_current_user_id());
        if($user_info){
            $user_name = $user_info->data->display_name;
            $user_login = $user_info->data->user_login;
            ?>
            <div class="cd__lk_header">
                <div class="cd__lk_header_title">
                    Добро пожаловать, <?php echo $user_name ?: $user_login; ?>!
                </div>
            </div>
            <?php
        }

        ?>



        <?php

        if(is_user_logged_in() && isset($user_info->caps["customer_company"])):?>

            <div class="cd__tabs" data-user_id="<?= get_current_user_id(); ?>">
                <ul class="cd__tabs_nav">
                    <li data-tab="programs" class="cd__tabs_nav_item">Учебные Программы</li>
                    <li data-tab="students_control" class="cd__tabs_nav_item">Контроль студентов</li>
                    <li data-tab="keys" class="cd__tabs_nav_item">Коды доступа</li>
                    <li data-tab="statistic" class="cd__tabs_nav_item">Статистика посещений</li>
<!--                    <li data-tab="courses" class="cd__tabs_nav_item">Курсы</li>-->
                    <li data-tab="profile" class="cd__tabs_nav_item">Профиль</li>
                </ul>
                <div class="cd__steps_warnings"></div>
                <div class="cd__tabs_content"></div>
            </div>

        <?php elseif (is_user_logged_in()): ?>

            <div class="cd__tabs" data-user_id="<?= get_current_user_id(); ?>">
                <ul class="cd__tabs_nav">
                    <li data-tab="programs" class="cd__tabs_nav_item">Учебные Программы</li>
                    <li data-tab="profile" class="cd__tabs_nav_item">Профиль</li>
                </ul>
                <div class="cd__steps_warnings"></div>
                <div class="cd__tabs_content"></div>
            </div>

        <?php else: ?>
            <div class="scbt__account_tabs">

                <div class="scbt__account_tabs_nav">
                    <div class="scbt__account_tab active">Войти</div>
                    <div class="scbt__account_tab">Зарегистрироваться</div>
                </div>
                <div class="scbt__account_forms_content">
                    <div class="scbt__account_form active">
                        <?php wp_login_form(array('label_username' => 'Логин или Email')); ?>
                    </div>
                    <div class="scbt__account_form">
                        <form class="scbt__register_form" action="" method="post" enctype="multipart/form-data">
                            <div>
                                <input class="user_name cd__user_login_input" required type="text" placeholder="Логин"
                                       value="" name="user_login" id="login-user">
                            </div>
                            <div>
                                <input class="first_name" required type="text" placeholder="Фамилия Имя Отчество"
                                       value="" name="first_name" id="first_name">
                            </div>
                            <div>
                                <input class="billing_phone" required type="text" placeholder="Телефон"
                                       value="" name="billing_phone" id="billing_phone">
                            </div>
                            <div>
                                <input class="user_email" required type="email" placeholder="E-mail"
                                       value="" name="user_email" id="email-user">
                            </div>
                            <div>
                                <input class="user_position" required type="text" placeholder="Должность"
                                       value="" name="user_position" id="user_position">
                            </div>
                            <div>
                                <label for="">
                                    <input class="cd__reg_form_snils_input" type="text" name="snils" placeholder="СНИЛС">
                                </label>
                            </div>
                            <div class="form_extend">
                                <div>
                                    <input class="user_password" placeholder="Пароль" required="" id="primary-pass-user" type="password" name="user_pass">
                                </div>

                                <div class="scbt_diler_register_fields">
                                    <div>
                                        <input class="user_company_name" required type="text" placeholder="Наименование компании"
                                               value="" name="user_company_name" id="user_company_name">
                                    </div>
                                    <div>
                                        <input class="user_inn" required type="text" placeholder="ИНН"
                                               value="" name="user_inn" id="user_inn">
                                    </div>
                                </div>

                                <div>
                                    <label>
                                        <span>Аккаунт</span>
                                        <span class="colon">:</span>
                                    </label>
                                    <div>
                                        <select name="user_role" id="user_role" class="user_role">
                                            <option value="customer" selected>Физическое лицо</option>
                                            <option value="customer_company">Юридическое лицо</option>
                                        </select>
                                    </div>
                                </div>
                                <label class="scbt__checkbox active" for="scbt__checkbox_accept">
                                    <div class="scbt__checkbox_square" >
                                        <span>✓</span>
                                    </div>
                                    Нажимая кнопку "Зарегистрироваться" я даю свое согласие на обработку персональной информации в соответствии с <a href="/wp-content/uploads/2022/08/polozhenie-o-personalnyh-dannyh-2022_p.pdf">Политикой конфиденциальности</a>
                                </label>
                                <input id="scbt__checkbox_accept" type="checkbox" name="accept" checked>
                            </div>
                            <div class="form-block-rcl scbt__submit_register_wrapper">
                                <button class="scbt__submit">Зарегистрироваться</button>
                            </div>
                            <div class="form-block-rcl scbt__register_result">

                            </div>
                        </form>
                    </div>
                </div>

            </div>
        <?php endif; ?>
    </div>
</div>

<div class="cd__modal_wrapper">
    <div class="cd__modal">
        <div class="cd__modal_back cd__modal_toggler"></div>
        <div class="cd__modal_content">
            <div class="cd__modal_close cd__modal_toggler">
                <svg style="width:36px;height:36px" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V5H19V19M17,8.4L13.4,12L17,15.6L15.6,17L12,13.4L8.4,17L7,15.6L10.6,12L7,8.4L8.4,7L12,10.6L15.6,7L17,8.4Z"></path>
                </svg>
            </div>
            <div class="cd__modal_result">

            </div>
        </div>
    </div>
</div>


<?php

get_footer();
