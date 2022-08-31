jQuery(document).ready(function(){
    jQuery(document).on('click', '.cd__modal_toggler', function (){
        jQuery('.cd__modal_wrapper').slideToggle();
    })
})



/* Копировать код в буфер обмена
**************************************************************************/

jQuery(document).ready(function(){
    jQuery(document).on('click', '[data-action="cd__copy_key_to_clipboard"]', function(){
        const resultBlock = jQuery(this).parent().find('.cd__key_copy_result');
        jQuery('.cd__key_copy_result').html('');
        navigator.clipboard.writeText(jQuery(this).data('text')).then(
            function() {
                resultBlock.html('Код успешно скопирован в буфер обмена');
                /* clipboard successfully set */

            },
            function() {
                /* clipboard write failed */
                window.alert('Opps! Your browser does not support the Clipboard API')
            }
        )
    })
})

/***********************************************************************/



jQuery(document).on('click', '[data-action="toggle__add_new_student_form"]', function (){
    jQuery('.cd__add_new_student_form').slideToggle();
})
jQuery(document).on('click', '.cd__hidden_form_toggler', function (){
    jQuery(this).parent().find('.cd__hidden_form_box').slideToggle();
})
jQuery(document).on('change', '[data-action="cd__table_select_all"]', function (){
    const table = jQuery(this).closest('.cd__table');
    const isChecked = jQuery(this).prop('checked');
    table.find('[data-action="cd__select_item"]').prop('checked', isChecked);
    table.find('[data-action="cd__select_item"]').prop('disabled', isChecked);
});

jQuery(document).on('change', '.cd__chapters_list_item_input ', function (){
    const parentId = jQuery(this).data('parent_id');


    const neighbours = jQuery(`[data-parent_id="${parentId}"]`);
    let checkboxStatus = false;
    neighbours.each(function(){
        if(!jQuery(this).hasClass('cd__checkbox_status_4') &&
            jQuery(this).prop('checked')){
            checkboxStatus = true;
        }
    })

    if(checkboxStatus){
        jQuery(`[data-parent_id="${parentId}"].cd__checkbox_status_4`).prop('checked', true);
    }else if(jQuery(this).hasClass('cd__checkbox_status_4')){
        if(!jQuery(this).prop('checked')){
            jQuery(this).closest('.cd__chapters_list_items').find('.cd__checkbox_status_4').prop('checked', false);
        }else{
            jQuery(this).closest('.cd__chapters_list_items').find('.cd__checkbox_status_4').prop('checked', true);
        }
    }
});


/* Табы форм регистрации и входа в аккаунт
************************************************************************/

jQuery(document).on('click', '.scbt__account_tabs', function (e){
    const target = jQuery(e.target);
    if(target.hasClass('scbt__account_tab')){
        const index = target.index();

        jQuery('.scbt__account_form').removeClass('active');
        jQuery('.scbt__account_tab').removeClass('active');

        target.addClass('active');
        jQuery('.scbt__account_form').eq(index).addClass('active');
    }
})

/***********************************************************************/



/* Маски inputmask
************************************************************************/

jQuery(document).ready(function(){
    jQuery(".cd__user_login_input").inputmask({ regex: "^[A-Za-z0-9]+$" });
    jQuery(".cd__reg_form_snils_input").inputmask("999-999-999 99");
});



/*
************************************************************************/



/* Справочная информация открытие блока с текстом (создание программы - список курсов)
************************************************************************/

jQuery(document).on('click', '.cd__chapters_list_item_description', function (e){
    jQuery(this).find('.cd__chapters_list_item_description_content').slideToggle();

    /*
************************************************************************/
})