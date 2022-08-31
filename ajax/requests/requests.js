function cd__content_request (id, action ) {
    return jQuery.ajax(
        {
            method: 'Post',
            url: ajaxUrl.url,
            data: {
                action,
                id
            }
        },
    )
}
function cd__key_request (key, action ) {
    return jQuery.ajax(
        {
            method: 'Post',
            url: ajaxUrl.url,
            data: {
                action,
                key
            }
        },
    )
}
function cd__create_program_request (name= '', description= '', action, courses ) {

    let courses_arr = [];
    if(action === 'cd__create_new_program'){
        const chaptersArr = Object.keys(courses.chapters).map(function(k){return courses.chapters[k]});
        for(let arr of chaptersArr){
            courses_arr = courses_arr.concat(arr);
        }
    }else{
        courses_arr = courses;
    }


    return jQuery.ajax(
        {
            method: 'Post',
            url: ajaxUrl.url,
            data: {
                action,
                name,
                description,
                courses: courses_arr,
            }
        },
    )
}


function cd__add_new_student_form_request ( user_login, first_name, user_email, user_position, user_snils, program_id ) {
    return jQuery.ajax(
        {
            method: 'Post',
            url: ajaxUrl.url,
            data: {
                action: 'cd__add_new_student',
                user_login,
                first_name,
                user_email,
                user_position,
                user_snils,
                program_id
            }
        },
    )
}

function cd__add_students_mass_request ( file, program_id ) {
    let fd = new FormData();
    fd.append("file", file[0].files[0]);
    fd.append("caption", 'cd__add_students_mass_request');
    fd.append('action', 'cd__add_students_mass');
    fd.append('program_id', program_id);
    return jQuery.ajax(
        {
            method: 'Post',
            url: ajaxUrl.url,
            contentType: false,
            processData: false,
            data: fd,
            // data: {
            //     action: 'cd__add_students_mass',
            //     file,
            // }
        },
    )
}

function cd__update_profile ( first_name, user_email, user_position, user_snils, user_inn, user_company_name, billing_phone ) {
    return jQuery.ajax(
        {
            method: 'Post',
            url: ajaxUrl.url,
            data: {
                action: 'cd__update_profile',
                first_name,
                user_email,
                user_position,
                user_snils,
                user_inn,
                user_company_name,
                billing_phone
            }
        },
    )
}


function cd__send_program_details_document ( full_name, short_name, program_name, hours, director_post, director_name, courses ) {
    return jQuery.ajax(
        {
            method: 'Post',
            url: ajaxUrl.url,
            data: {
                action: 'cd__send_program_details_document',
                full_name,
                short_name,
                program_name,
                hours,
                director_post,
                director_name,
                courses
            }
        },
    )
}

function cd__send_student_control_details_document (
    full_name,
    program_name,
    hours,
    date,
    comission_lead,
    comission_member_1,
    comission_member_2,
    reg_number,
    users_ids) {
    return jQuery.ajax(
        {
            method: 'Post',
            url: ajaxUrl.url,
            data: {
                action: 'cd__send_student_control_details_document',
                full_name,
                program_name,
                hours,
                date,
                comission_lead,
                comission_member_1,
                comission_member_2,
                reg_number,
                users_ids
            }
        },
    )
}


function cd__student_control_details_download_students_info (users_ids) {
    return jQuery.ajax(
        {
            method: 'Post',
            url: ajaxUrl.url,
            data: {
                action: 'cd__student_control_details_download_students_info',
                users_ids
            }
        },
    )
}

