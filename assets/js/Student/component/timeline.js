$('.time-icon').mouseover(function() {
    var data = $(this).attr('data-url');
    var text;

    switch(data) {
        case 'first': 
        text = 'Embauche';
        break;

        case 'second': 
        text = 'Traitement du dossier';
        break;

        case 'third': 
        text = 'Début de la mission';
        break;
    }

    $('.message-first').css('transition', '0.5s').css('opacity', '1');
    $('.timeline-header').text(text);
});

$('.time-icon').mouseout(function() {
    $('.message-first').css('transition', '0.5s').css('opacity', '0');
});