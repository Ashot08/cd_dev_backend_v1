const loaderHtml = (fill = "#f80000") => {
    return `<div class="cd__loader">
                        <svg width="60px" height="60px" version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
                            <path fill="${fill}" d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
                                <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform>
                            </path>
                        </svg>
                    </div>`;
}

const data = {
    chapters: {
        // 85: [52, 54, 55, 56, 63, 64, 65, 67, 68, 81, 53, 58, 59, 61, 62, 69, 78, 79, 80, 83],
        // 66: [72, 73, 74, 75, 76, 77]
    }
}
function dynamicallyLoadScript(url) {
    var script = document.createElement("script");  // create a script DOM node
    script.src = url;  // set its src to the provided URL

    document.head.appendChild(script);  // add it to the end of the head section of the page (could change 'head' to 'body' to add it to the end of the body section instead)
}



/*----------Requests----------*/
dynamicallyLoadScript('/wp-content/plugins/courses_dashboard_2/ajax/requests/requests.js');
/*--------------------------------*/



/*----------Tabs Routing----------*/
dynamicallyLoadScript('/wp-content/plugins/courses_dashboard_2/ajax/tabsRouting/tabsRouting.js');
/*--------------------------------*/



/*----------Update Profile----------*/
jQuery(document).on('click','[data-action="cd__update_profile"]', function(){
    const resultBlock = jQuery('.cd__update_profile_result');
    resultBlock.html(loaderHtml());

    const first_name = jQuery('[name="first_name"]').val();
    const user_email = jQuery('[name="user_email"]').val();
    const user_position = jQuery('[name="user_position"]').val();
    const user_snils = jQuery('[name="user_snils"]').val();
    const user_inn = jQuery('[name="user_inn"]').val();
    const user_company_name = jQuery('[name="user_company_name"]').val();
    const billing_phone = jQuery('[name="billing_phone"]').val();

    cd__update_profile (
        first_name,
        user_email,
        user_position,
        user_snils,
        user_inn,
        user_company_name,
        billing_phone
    ).then(res=>resultBlock.html(res));
})
/*--------------------------------*/



/*--------Create Program----------*/

jQuery(document).ready(function(){
    jQuery(document).on('click','[data-action="show_create_program"]', function(){
       const resultBlock = jQuery(this).parent();
        resultBlock.html(loaderHtml());
        cd__content_request (1, 'cd__get_create_program_view' ).then(res=>resultBlock.html(res));
    })
})



jQuery(document).on('click', '.cd__step_toggler', function(){
    jQuery('.cd__step').slideToggle();
})

jQuery(document).on('click', '.cd__program_save', function(){
    const result_block = jQuery('.cd__steps_warnings');
    const name = jQuery('.cd__program_name_input').val();
    const description = jQuery('.cd__program_description_input').val();
    const buttonTitle = jQuery(this).text();
    jQuery(this).html(loaderHtml("#ffffff"));

    const action = jQuery(this).data('action');

    console.log(buttonTitle);
    cd__create_program_request( name, description, action, data).
    then((res) => {
        if(res === 'errorName'){
            result_block.html('<div>Вы не указали название для новой учебной программы</div>');
        }else if(res === 'errorCoursesIds'){
            result_block.html('<div>Нужно выбрать хотя бы один курс для новой учебной программы</div>');
        }else {
            result_block.html('<div class="success">Новая учебная программа "<strong>' + name + '</strong>" успешно добавлена</div>');
            jQuery('.cd__create_program').html('<button data-action="show_create_program">Создать программу</button>');
            loadContent('programs');
        }

        result_block.bounce({
            interval: 100,
            distance: 3,
            times: 15
        });
        jQuery('html, body').animate({ scrollTop: 0 }, "fast");
        jQuery(this).html(buttonTitle);

    })
})

/*--------------------------------*/



/*------------Shake animation--------------*/

	jQuery.fn.shake = function (settings) {
		settings = jQuery.extend({
			interval: 100,
			distance: 10,
			times: 4,
			complete: jQuery.noop
		}, settings);

		var $this = jQuery(this);

		for (var i = 0; i < settings.times + 1; i++) {
			$this.transition(
				{
					x: i % 2 == 0 ? settings.distance : settings.distance * -1
				},
				settings.interval
			);
		}

		$this.transition({ x: 0 }, settings.interval, function () {
			$this.removeAttr("style");
			settings.complete.call($this[0]);
		});
	};
	jQuery.fn.bounce = function (settings) {
		settings = jQuery.extend({
			interval: 100,
			distance: 10,
			times: 4,
			complete: jQuery.noop
		}, settings);

		var $this = jQuery(this);

		for (var i = 0; i < settings.times + 1; i++) {
			jQuery(this).transition(
				{ y: i % 2 == 0 ? settings.distance : settings.distance * -1 },
				settings.interval
			);
		}

		jQuery(this).transition({ y: 0 }, settings.interval, function () {
			$this.removeAttr("style");
			settings.complete.call($this[0]);
		});
	};

/*--------------------------------*/


/*-------Render chapters list------*/

jQuery(document).ready(function(){
    jQuery(document).on('click','[data-action="chose_by_chapter"]', function(){
        const course_id = jQuery(this).data('course_id');
        const result_block = jQuery('.cd__modal_result');
        result_block.html(loaderHtml());
        cd__content_request (course_id, 'cd__get_chapters_list' ).then(res=>result_block.html(res)).then(()=>{
            if( !jQuery.isEmptyObject(data.chapters) ){
                for(let chapter_key in data.chapters){
                    if (data.chapters.hasOwnProperty(chapter_key)) {
                        for(let chapter_id of data.chapters[chapter_key]){
                            jQuery(`input[data-chapter_id="${chapter_id}"]`).prop('checked', true);
                        }
                    }
                }
            }

            jQuery('.cd__chapters_list_level_1').treeview({
                collapsed: true,
                animated: 'medium',
                unique: false
            });
        });


    })
})

/*----------------------------------*/





/*--------Manual Chose chapters-------*/


jQuery(document).ready(function(){

    jQuery(document).on('click', '[data-action="submit_chosen_chapters"]', function(){
        const course_id = jQuery(this).data('course_id');
        data.chapters[course_id] = [];
        const resulbBlock = jQuery(`.cd__programs_item[data-course_id=${course_id}] .cd__chose_by_chapter_result`);
        resulbBlock.html('');
        jQuery(`.cd__chapters[data-course_id="${course_id}"] .cd__chapters_list_item_input:checked`).each(
            function(){
                data.chapters[course_id].push(jQuery(this).data('chapter_id'));
                resulbBlock.html(`<div>Количество выбранных разделов: ${data.chapters[course_id].length}</div>`);
            }
        )
        if(data.chapters[course_id].length){
            jQuery(`.cd__program_checkbox[data-course_id="${course_id}"]`).prop('checked', false);
        }
        console.dir(data)
    })

    jQuery(document).on('change', `.cd__chapters_list_item_input`, function(){
        const root_course_id = jQuery(this).data('root_course_id');

        if(jQuery(this).is(':checked')){
            const course_id = jQuery(this).data('chapter_id');
            jQuery(`.cd__chapters[data-course_id="${root_course_id}"]`)
                .find(`.cd__chapters_list_item[data-chapter_id="${course_id}"] .cd__chapters_list_item_input`)
                .prop('checked', true);
        }else{
            const course_id = jQuery(this).data('chapter_id');
            let parent_id = jQuery(this).data('parent_id');
            while(parent_id){
                const checkbox = jQuery(`.cd__chapters[data-course_id="${root_course_id}"]`)
                    .find(`.cd__chapters_list_item_input[data-chapter_id="${parent_id}"]`);

                if(checkbox.prop('disabled')) break;

                checkbox.prop('checked', false);
                parent_id = checkbox.data('parent_id');

            }

            jQuery(`.cd__chapters[data-course_id="${root_course_id}"]`)
                .find(`.cd__chapters_list_item[data-chapter_id="${course_id}"] .cd__chapters_list_item_input`)
                .prop('checked', false);
        }
    })

})



/*------------------------------------*/



/*-------Program List Item-------*/

jQuery(document).on('click', '.cd_program .cd__programs_item', function(){
    const program_id = jQuery(this).data('program_id');
    const result_block = jQuery('.cd__program_details');
    const title = jQuery(this).find('.cd__programs_item_title').html();
    result_block.show();
    result_block.html(loaderHtml());

    cd__content_request (program_id, 'cd__get_program_details' ).then((res) => {
        if(res){
            result_block.html(`<div class="success">Программа <span>"${title}"</span> включает следующие разделы и темы: </div>`);
            result_block.append(res);
            jQuery('html, body').animate({ scrollTop: result_block.offset().top }, 'slow');
        }else{
            result_block.html(`<div>В программе <span>"${title}"</span> нет ни одного курса: <div class="success">`);
            jQuery('html, body').animate({ scrollTop: result_block.offset().top }, 'slow');
        }
    })
})

/*--------------------------------*/


/*-------View Program Keys List-------*/

jQuery(document).on('click', '.cd_key_program .cd__programs_item', function(){
    const program_id = jQuery(this).data('program_id');
    const result_block = jQuery('.cd__program_details');
    const title = jQuery(this).find('.cd__programs_item_title').html();
    cd__content_request (program_id, 'cd__get_key_programs_details' ).then((res) => {
        if(res){
            result_block.html(`<div class="success">Для программы <span>"${title}"</span> имеются следующие ключи: </div>`);
            result_block.append(res);
            result_block.show(100);
            jQuery('html, body').animate({ scrollTop: result_block.offset().top }, 'slow');
        }else{
            result_block.show(1000);
            result_block.html(`<div class="warning">Для программы <span>"${title}"</span> у вас нет ни одного ключа. </div>`);
            result_block.append(`<div style="margin: 60px 0; display: flex;">
        <button data-program_id="${program_id}" data-action="cd__create_and_attach_key">Сгенерировать +1 код (техническая функция)</button>
        <div class="cd__create_and_attach_key_result"></div>
    </div>`)
            jQuery('html, body').animate({ scrollTop: result_block.offset().top }, 'slow');
        }
    })
})

/*--------------------------------*/


/*-------cd__create_and_attach_key-------*/

jQuery(document).on('click', '[data-action="cd__create_and_attach_key"]', function(){
    const program_id = jQuery(this).data('program_id');
    const result_block = jQuery('.cd__create_and_attach_key_result');

    cd__content_request (program_id, 'cd__create_and_attach_key' ).then((res) => {
        if(res){
            result_block.html(res);
        }else{
            result_block.html(`Ошибка`)
        }
    })
})

/*--------------------------------*/


/*-------Add course to director-----*/

jQuery(document).ready(function(){
    jQuery(document).on('click','[data-action="add_course_to_director"]', function(){
        //const course_id = jQuery(this).parent().find('input').val();
        const result_block = jQuery('.cd__steps_warnings');

        const courses_array = [84];
        for (const course of courses_array){
            cd__content_request (course, 'cd__add_course_to_director' ).then(res=>result_block.append(res));
        }

        //cd__content_request (course_id, 'cd__add_course_to_director' ).then(res=>result_block.html(res));
    })
})

/*----------------------------------*/


/*-------Student register key-------*/
jQuery(document).ready(function(){
    jQuery(document).on('click','[data-action="add_program_to_student"]', function(){
        const resultBlock = jQuery('.cd_add_program_to_student_result');
        const key = jQuery(this).parent().find('input').val();
        cd__key_request(key, 'cd__connect_student_with_program' ).then((res)=>{

            switch (res){
                case 'key_error':
                    resultBlock.html('Ошибка! Неверный код доступа');
                    break;
                case 'exist_before':
                    resultBlock.html('Ошибка! Ключ уже был использован ранее');
                    break;
                case 'success':
                    resultBlock.html('Ключ успешно активирован!');
                    window.location.reload();
                    break;
                case 'error':
                    resultBlock.html('Ошибка');
                    break;
            }

        });
    });
});
/*----------------------------------*/

/*-------Render Student list details-------*/
jQuery(document).ready(function(){
    jQuery(document).on('click','[data-action="show_program_students"]', function(){
        const resultBlock = jQuery('.cd__program_details');
        const program_id = jQuery(this).data('program_id');
        resultBlock.show();
        resultBlock.html(loaderHtml());
        cd__content_request (program_id, 'cd__get_students_control_details' ).then((res)=>{
            if(res === 'not_found'){
                resultBlock.html('Пользователей не найдено');
            }else{
                resultBlock.html(res);
            }

        });
    });
});
/*----------------------------------*/



/*-------Add new student form-------*/

jQuery(document).on('click', '[data-action="cd__send_add_new_student_form"]' , function (e) {
    const resultBlock = jQuery('.cd__add_new_student_form_result');
    const user_login = jQuery('[name="user_login"]').val();
    const first_name = jQuery('[name="first_name"]').val();
    const user_email = jQuery('[name="user_email"]').val();
    const user_position = jQuery('[name="user_position"]').val();
    const user_snils = jQuery('[name="snils"]').val();
    const program_id = jQuery(this).data('program_id');



    if(user_login && user_email){
        cd__add_new_student_form_request(user_login, first_name, user_email, user_position, user_snils, program_id).then(res => resultBlock.html(res));
    }else{
        resultBlock.html('<div class="scbt__notice_error">Заполните все обязательные поля</div>')
    }

});
/*----------------------------------*/


/*-------Add students mass form-------*/

jQuery(document).on('click', '[data-action="cd__add_students_mass"]' , function (e) {
    e.preventDefault();
    const resultBlock = jQuery('.cd__add_students_mass_result');
    const file = jQuery('[name="cd__students_mass_file"]');
    const program_id = jQuery(this).data('program_id');


    if(file){
        cd__add_students_mass_request(file, program_id).then(res => resultBlock.html(res));
    }else{
        resultBlock.html('<div class="scbt__notice_error">Загрузите файл с компьютера</div>')
    }

});
/*----------------------------------*/



/*-------Создание и скачивание docx файла Учебной программы-------*/

jQuery(document).on('click', '[data-action="cd__send_program_details_document"]', function(){
    const resultBlock = jQuery('.cd__send_program_details_document_result');
    resultBlock.html(loaderHtml());
    const full_name = jQuery(this).parent().find('[name="full_name"]').val();
    const short_name = jQuery(this).parent().find('[name="short_name"]').val();
    const program_name = jQuery(this).parent().find('[name="program_name"]').val();
    const hours = jQuery(this).parent().find('[name="hours"]').val();
    const director_post = jQuery(this).parent().find('[name="director_post"]').val();
    const director_name = jQuery(this).parent().find('[name="director_name"]').val();
    
    const courses = jQuery(this).data('courses').toString().split(',');

    cd__send_program_details_document ( full_name, short_name, program_name, hours, director_post, director_name, courses ).then((res)=>{
        setTimeout(function(){
            resultBlock.html(res);
        }, 1500);

    });
})

/*----------------------------------*/



/*-------Создание и скачивание excel файла Контроля студентов-------*/

jQuery(document).on('click', '[data-action="cd__send_student_control_details_document"]', function(){
    const resultBlock = jQuery('.cd__send_program_details_document_result');
    resultBlock.html(loaderHtml());
    const full_name          = jQuery(this).parent().find('[name="full_name"]').val();
    const program_name       = jQuery(this).parent().find('[name="program_name"]').val();
    const hours              = jQuery(this).parent().find('[name="hours"]').val();
    const comission_lead     = jQuery(this).parent().find('[name="comission_lead"]').val();
    const comission_member_1 = jQuery(this).parent().find('[name="comission_member_1"]').val();
    const comission_member_2 = jQuery(this).parent().find('[name="comission_member_2"]').val();
    const reg_number         = jQuery(this).parent().find('[name="reg_number"]').val();
    const date               = jQuery(this).parent().find('[name="date"]').val();
    let   users_ids          = [];
    jQuery('.cd__table_select_user_checkbox').each(function(){
        const is_checked = jQuery(this).prop('checked');
        if(is_checked){
            users_ids.push(jQuery(this).data('student_id'));
        }
    })
    if(users_ids.length === 0){
        resultBlock.html('Выберите студентов, которых нужно выгрузить');
    }else{
        cd__send_student_control_details_document (
            full_name,
            program_name,
            hours,
            date,
            comission_lead,
            comission_member_1,
            comission_member_2,
            reg_number,
            users_ids
        ).then((res)=>{
            setTimeout(function(){
                resultBlock.html(res);
            }, 1500);

        });
    }
})

/*----------------------------------*/



/*-------Создание и скачивание excel файла Списка студентов -------*/

jQuery(document).on('click', '[data-action="cd__student_control_details_download_students_info"]', function(){
    const resultBlock = jQuery('.cd__student_control_details_download_students_info_result');
    resultBlock.html(loaderHtml());
    let   users_ids          = [];
    jQuery('.cd__table_select_user_checkbox').each(function(){
        const is_checked = jQuery(this).prop('checked');
        if(is_checked){
            users_ids.push(jQuery(this).data('student_id'));
        }
    })
    if(users_ids.length === 0){
        resultBlock.html('Выберите студентов, которых нужно выгрузить');
    }else{
        cd__student_control_details_download_students_info (
            users_ids
        ).then((res)=>{
            setTimeout(function(){
                resultBlock.html(res);
            }, 1500);

        });
    }
})

/*----------------------------------*/