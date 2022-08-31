function loadContent(action = 'programs') {
    const result_block = jQuery('.cd__tabs_content');
    let user_id = jQuery('.cd__tabs').data('user_id');

    if (!user_id) {
        user_id = 1
    }

    switch (action) {
        case 'programs':

            cd__content_request(user_id, 'cd__get_director_programs_list').then(
                res => result_block.html(res)
            )
            break;
        case 'students_control':
            cd__content_request(user_id, 'cd__get_students_control_programs_list').then(
                res => result_block.html(res)
            )
            break;
        case 'keys':
            cd__content_request(user_id, 'cd__get_keys_programs_list').then(
                res => result_block.html(res)
            )
            break;
        case 'profile':
            cd__content_request(user_id, 'cd__get_profile').then(
                res => result_block.html(res)
            )
            break;
    }
    jQuery('.cd__tabs_nav_item').each(function () {

        if (jQuery(this).data('tab') === action) {
            jQuery(this).addClass('active');
        }
    })

}

jQuery(document).on('click', '.cd__tabs_nav_item', function () {
    if ('URLSearchParams' in window) {
        var searchParams = new URLSearchParams(window.location.search);
        searchParams.set("tab", jQuery(this).data('tab'));
        window.location.search = searchParams.toString();
    }
})

jQuery(document).ready(function () {
    const tabName = getUrlParameter('tab');
    if (!tabName || tabName === 'programs') {
        loadContent()
    } else {
        loadContent(tabName);
    }
})

function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return false;
}





