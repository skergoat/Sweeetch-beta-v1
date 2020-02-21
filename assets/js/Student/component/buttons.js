
// add true to :checked
$(document).ready(function() {
    $('input[type="radio"]:checked').addClass('true');
});

// toggle checked 
$('input[type="radio"]').on('click', function() {

    if(!$(this).hasClass('true')) { 

        // check
        $(this).addClass('true')
                .prop('checked', true);
        
        // date end fade out
        var closest = $(this).closest('.language-card');
        $(closest).children('.date-finish').fadeOut();

    }
    else {
        $(this).removeClass('true').prop('checked', false);

        // date end fade out
        var closest = $(this).closest('.language-card');
        $(closest).children('.date-finish').fadeIn();
    }

});