<?php
function students_control_list($data){
    ?>
    <h3>Контроль студентов</h3>

    <div class="cd__program_details">

    </div>

    <div class="cd__programs_list">
        <?php foreach ($data as $program): ?>

            <div class="cd__programs_item" data-program_id="<?= $program[0]->id ?>" data-action="show_program_students" >
                <div class="cd__programs_item_title"><?= $program[0]->title ?></div>
                <div class="cd__programs_item_description"><?= $program[0]->description ?></div>
            </div>

        <?php endforeach;?>
    </div>

    <?php

}