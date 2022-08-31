<?php
function key_programs_list($data){
    ?>
    <h3>Коды доступа</h3>




    <div class="cd__program_details_warning"></div>
    <div class="cd__program_details">

    </div>

    <h4>Коды рассортированы по программам</h4>
    <div class="cd__programs_list cd_key_program">
        <?php foreach ($data as $program): ?>

        <div class="cd__programs_item" data-program_id="<?= $program[0]->id ?>">
            <div class="cd__programs_item_title"><?= $program[0]->title ?></div>
            <div class="cd__programs_item_description"><?= $program[0]->description ?></div>
        </div>

        <?php endforeach;?>
    </div>

<?php

}