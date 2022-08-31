<?php
function key_program_details($data, $program_id){
?>
    <?php if($data): ?>
        <table>
            <tbody>
            <tr>
                <th>Ключ</th>
                <th>Действие</th>
                <th>Активирован</th>
            </tr>
            <?php
            foreach ($data as $user_key) {
                //$user_key->student_id = cd__get_from_table('courses_dashboard__students_keys', 'key_id', $user_key->id)[0];
                ?>
                <tr>
                    <td><?php echo $user_key[0]->access_key; ?></td>
                    <td><button data-action="cd__copy_key_to_clipboard" data-text="<?= $user_key[0]->access_key; ?>" class="cd__button-small">Скопировать ключ</button><span class="cd__key_copy_result"></span></td>
                    <td><?php echo $user_key[0]->active ? 'Да' : 'Нет'; ?></td>
                </tr>
                <?php
            }?>
            </tbody>
        </table>
    <?php endif; ?>

    <div style="margin: 60px 0; display: flex;">
        <button data-program_id="<?= $program_id; ?>" data-action="cd__create_and_attach_key">Сгенерировать +1 код (техническая функция)</button>
        <div class="cd__create_and_attach_key_result"></div>
    </div>
<?php
}