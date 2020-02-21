// manage add abd remove fields to edit profile 

$(document).ready(function() {
    var $wrapper = $('.js-profile-wrapper');

    $wrapper.on('click', '.js-profile-add', function(e) {

        e.preventDefault();

        // select wright wrapper
        var id = $(this).attr('data-url');
        $wrappers = $('#' + id);

        // Get the data-prototype explained earlier
        var prototype = $wrappers.data('prototype');
        // get the new index
        var index = $wrappers.data('index');
        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newForm = prototype.replace(/__name__/g, index);
        // increase the index with one for the next item
        $wrappers.data('index', index + 1);
        // Display the form in the page before the "new" link
        $(this).before(newForm);
        
        // date picker
        $(document).ready(function() {
            $('.js-datepicker').datepicker({
                format: 'dd-mm-yyyy'
            });
        });

        // $('input[type="checkbox"]').off('click', test());

        // function test2(c) {
        //     var closest = $(c).closest('.language-card');
        //     $(closest).children('.date-finish').toggleClass('none');
        // }

        // $('input[type="checkbox"]').change(function() {
        //     $('input[type="checkbox"]').trigger('click');
        // });
    

    });

   
    $wrapper.on('click', '.js-remove-data', function(e) {
        e.preventDefault();
        $(this).closest('.js-remove-profile-item')
            .fadeOut()
            .remove();
    });

});
