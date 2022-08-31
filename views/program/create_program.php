<?php
function create_program($data){
    ?>

    <h4>Создать учебную программу</h4>

    <div class="cd__creation_steps">

        <div class="cd__step_1 cd__step">
            <div class="cd__step_title">Шаг 1</div>
            <p>Придумайте название и описание для учебной программы:</p>
            <label for="">
                Название
                <input class="cd__program_name_input" type="text" name="program_name" placeholder="Название программы">
            </label>

            <label>
                Описание
                <textarea class="cd__program_description_input" name="program_description" id="" cols="30" rows="10" placeholder="Описание программы"></textarea>
            </label>
            <div>
                <button class="cd__step_toggler">Далее 
                <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z" />
                </svg></button>
            </div>
        </div>

        <div class="cd__step_2 cd__step">
            <div class="cd__step_title">Шаг 2</div>
            <p>Выберите нужные курсы:</p>
            <div class="cd__programs_list">




                <?php
                $terms_array = [];
                ?>
                <?php foreach ($data as $course): ?>
                    <?php $term = get_term( $course->course_id, 'wpm-category' );?>
                    <?php
                    if($course->course_id){
                        $terms_array[$course->course_id] = $term;
                    }
                    ?>
                <?php endforeach; ?>

                <?php foreach ($terms_array as $term): ?>
                    <?php if($term): ?>

                        <?php
                        $image_id = get_term_meta( $term->term_id, '_thumbnail_id', 1 );
                        $image_url = wp_get_attachment_image_url( $image_id, 'full' );
                        ?>

                        <label class="cd__programs_item" data-course_id="<?= $term->term_id ?>">
                            <img src="<?= $image_url; ?>" alt="<?= $term->name ?>">
                            <div class="cd__programs_item_title"><?= $term->name ?></div>
<!--                            <div class="cd__program_checkbox_wrapper">-->
<!--                                <input class="cd__program_checkbox" type="checkbox" data-course_id="--><?//= $term->term_id ?><!--">-->
<!--                                Взять весь курс-->
<!--                            </div>-->
                            <div>
                                <button class="cd__modal_toggler" data-course_id="<?= $term->term_id ?>" data-action="chose_by_chapter">Выбрать по разделам</button>
                            </div>
                            <div class="cd__chose_by_chapter_result">

                            </div>

                        </label>
                    <?php endif; ?>
                <?php endforeach; ?>



            </div>
            <div class="cd__step_toggler_wrapper">
                <button class="cd__step_toggler">
                    <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z" />
                    </svg>
                    Назад
                    </button>
                    <button class="cd__program_save" data-action="cd__create_new_program">Создать Общую</button>
                    <button class="cd__program_save"  data-action="cd__create_new_separate_programs">Создать По Отдельности</button>
            </div>
        </div>
    </div>




    <?php

}