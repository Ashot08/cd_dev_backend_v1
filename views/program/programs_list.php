<?php
function programs_list($data){
    ?>
    <?php
    $user_info = get_userdata(get_current_user_id());
    $is_company = false;
    if(is_user_logged_in() && isset($user_info->caps["customer_company"])){
        $is_company = true;
    }
    ?>



    <h3>Учебные Программы</h3>




    <?php

    function renderTerms($parent, $root_course_id, $level = 1){
        $args = [
            'taxonomy'      => [ 'wpm-category' ], // название таксономии с WP 4.5
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => true,
            'object_ids'    => null,
            'include'       => array(),
//            'exclude'       => array(41, 42,43,  44, 45, 47, 48, 49, 50, 52, 54, 55, 56, 58,59, 61, 62,63, 64, 65, 66, 67, 68, 69,70, 71,72, 73,
//                74, 75, 76, 77, 78, 79, 80, 81, 83, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98,99, 100, 101, 102, 103,
//                104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118),
            'exclude_tree'  => array(),
            'number'        => '',
            'fields'        => 'all',
            'count'         => false,
            'slug'          => '',
            'parent'         => $parent,
            'hierarchical'  => true,
            'child_of'      => 0,
           // 'get'           => 'all', // ставим all чтобы получить все термины
            'name__like'    => '',
            'pad_counts'    => false,
            'offset'        => '',
            'search'        => '',
            'cache_domain'  => 'core',
            'name'          => '',    // str/arr поле name для получения термина по нему. C 4.2.
            'childless'     => false, // true не получит (пропустит) термины у которых есть дочерние термины. C 4.2.
            'update_term_meta_cache' => true, // подгружать метаданные в кэш
            'meta_query'    => '',
        ];

        $terms = get_terms( $args );
        ?>
        <?php if($terms): ?>



            <ul class="cd__chapters_list_items cd__chapters_list_level_<?= $level++; ?>">

                <?php foreach ($terms as $term): ?>
                    <li data-chapter_id="<?= $term->term_id; ?>" class="cd__chapters_list_item">
                        <label class="cd__chapters_list_item_label">

                            <span><?= $term->name; ?></span>
                        </label>
                        <?php
                        $term_id = $term->term_id;
                        if($term_id){
                            renderTerms($term_id, $root_course_id, $level);
                        }
                        ?>
                    </li>
                <?php endforeach; ?>
            </ul>

        <?php endif; ?>


        <?php
    }
    ?>
<!--    <h5>Структура курсов:</h5>-->
<!--    <div class="cd__program_list_hierarchy_list">-->
<!--        --><?php
//        renderTerms(0,40);
//        ?>
<!--    </div>-->

    <style>
        .cd__program_list_hierarchy_list li{
            line-height: 1;
            padding-bottom: 20px;
        }
    </style>
    <script>
        // jQuery('.cd__program_list_hierarchy_list > ul').treeview({
        //     collapsed: false,
        //     animated: 'medium',
        //     unique: true
        // });


    </script>





    <?php if($is_company): ?>
        <div class="cd__create_program">
            <button data-action="show_create_program">Создать программу</button>
        </div>
    <?php endif; ?>

    <?php if(is_user_logged_in() && !$is_company): ?>
        <div style="margin: 60px 0; display: flex;">
            <input placeholder="Получить доступ к учебной программе" class type="text" name="access_key"><br>
            <button data-action="add_program_to_student">Ввести код доступа</button>
            <div class="cd_add_program_to_student_result"></div>
        </div>
    <?php endif; ?>


    <div class="cd__program_details">

    </div>

    <?php if($data): ?>
        <h4>Ваши учебные программы</h4>
    <?php elseif($is_company): ?>
        <h4>У вас пока нет учебных программ</h4>
    <?php else: ?>
        <h4>У вас пока нет учебных программ, обратитесь к руководителю за кодом доступа</h4>
    <?php endif; ?>


    <div class="cd__programs_list cd_program">
        <?php foreach ($data as $program): ?>

            <div class="cd__programs_item" data-program_id="<?= $program[0]->id ?>">
                <div class="cd__programs_item_title"><?= $program[0]->title ?></div>
                <div class="cd__programs_item_description"><?= $program[0]->description ?></div>
            </div>

        <?php endforeach;?>
    </div>


    <?php if($is_company): ?>
        <div style="margin: 60px 0; display: flex;">
<!--            <input class type="number" name="course_id"><br>-->
<!--            <button data-action="add_course_to_director">Добавить курс по ID</button>-->
            <button data-action="add_course_to_director">Получить доступные курсы</button>
        </div>
    <?php endif; ?>
<?php

}