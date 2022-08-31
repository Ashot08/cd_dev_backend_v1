<?php
function profile($user_ID)
{

    // если пользователь не авторизован, отправляем его на страницу входа
    if (!$user_ID) {
        header('location:' . site_url() . '/account');
        exit;
    } else {
        $userdata = get_user_by('id', $user_ID);
    }
    ?>
    <?php
        $user_id = get_current_user_id();
        $user_info = get_userdata(get_current_user_id($user_id));
        $user_logged_in = is_user_logged_in();
        $is_company = false;
        if ($user_logged_in && isset($user_info->caps["customer_company"])) {
            $is_company = true;
        }
    ?>
    <div class="cd__profile">
<!--        <form action="" method="POST">-->

            <div>
                <input class="first_name" required type="text" placeholder="Фамилия Имя Отчество" value="<?= get_user_meta( $user_id, 'first_name', true ); ?>" name="first_name">
            </div>
            <div>
                <input class="billing_phone" required type="text" placeholder="Телефон" value="<?= get_user_meta( $user_id, 'billing_phone', true ); ?>" name="billing_phone">
            </div>
            <div>
                <input class="user_email" required type="email" placeholder="E-mail" value="<?= $user_info->data->user_email; ?>" name="user_email">
            </div>
            <div>
                <input class="user_position" required type="text" placeholder="Должность" value="<?= get_user_meta( $user_id, 'user_position', true ); ?>" name="user_position">
            </div>
            <div>
                <label for="">
                    <input class="cd__reg_form_snils_input" type="text" name="user_snils" value="<?= get_user_meta( $user_id, 'user_snils', true ); ?>" placeholder="СНИЛС">
                </label>
            </div>

            <?php if($is_company): ?>
                <div>
                    <div>
                        <input class="user_company_name" required type="text" placeholder="Наименование компании" value="<?= get_user_meta( $user_id, 'user_company_name', true ); ?>" name="user_company_name">
                    </div>
                    <div>
                        <input class="user_inn" required type="text" placeholder="ИНН" value="<?= get_user_meta( $user_id, 'user_inn', true ); ?>" name="user_inn" >
                    </div>
                </div>
            <?php endif; ?>

<!--            <label for="">-->
<!--                Смена пароля-->
<!--                <input type="password" name="pwd1" placeholder="Старый пароль"/>-->
<!--            </label>-->
<!---->
<!--            <input type="password" name="pwd2" placeholder="Новый пароль"/>-->
<!--            <input type="password" name="pwd3" placeholder="Повторите новый пароль"/>-->

            <button data-action="cd__update_profile">Сохранить</button>
<!--        </form>-->
        <div class="cd__update_profile_result"></div>
    </div>
    <script>
            jQuery(".cd__reg_form_snils_input").inputmask("999-999-999 99");
    </script>

    <?php
}