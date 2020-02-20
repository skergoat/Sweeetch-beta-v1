$('input[type="radio"]').on('click', function() {

    if(!$(this).hasClass('true')) { 
        $(this).addClass('true').prop('checked', true);
    }
    else {
        $(this).removeClass('true').prop('checked', false);
    }

});