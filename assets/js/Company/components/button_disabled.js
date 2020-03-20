$('form').on('submit', function() {
    $('form button').prop('disabled', true);
});

$('form').ready(function() {
    $('form button').prop('disabled', false);
});