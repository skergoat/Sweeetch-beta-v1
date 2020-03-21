// disable button when submit form 
$('form').on('submit', function() {
    $('form button').prop('disabled', true);
});
