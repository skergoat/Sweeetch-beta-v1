
// add true to :checked
$(document).ready(function() {
    $('input[type="radio"]:checked').addClass('true');
});

// toggle checked 
$('input[type="radio"]').on('click', function() {

    if(!$(this).hasClass('true')) { 

         $(this).addClass('true')
                .prop('checked', true);
        
        var closest = $(this).closest('.language-card');
        
        $(closest).children('.date-finish').fadeOut();

    }
    else {
        $(this).removeClass('true').prop('checked', false);
    }

});