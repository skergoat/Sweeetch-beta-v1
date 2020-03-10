$('.time-icon').mouseover(function() {
    var data = $(this).attr('data-url');
    var text;

    switch(data) {
        case 'first': 
        text = 'Acceptez ou refusez la candidature';
        break;

        case 'second': 
        text = 'Signature du contrat d\'alternance';
        break;

        case 'third': 
        text = 'Recrutement terminé';
        break;
    }

    $('.message-first').css('transition', '0.5s').css('opacity', '1');
    $('.timeline-header').text(text);
});

$('.time-icon').mouseout(function() {
    $('.message-first').css('transition', '0.5s').css('opacity', '0');
});