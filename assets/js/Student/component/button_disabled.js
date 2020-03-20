$('form').on('submit', function() {
    $('form button').prop('disabled', true);
    $('form button').mouseover(function() {
        alert('Le bouton est désactivé lors de la soumission du formulaire. Pour le reéctiver, rechargez la page.');
    });
});