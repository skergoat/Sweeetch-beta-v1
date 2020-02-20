$('.time-icon').mouseover(function() {
    var data = $(this).attr('data-url');
    $('.message-' + data).css('transition', '0.5s').css('opacity', '1');
});

$('.time-icon').mouseout(function() {
    var data = $(this).attr('data-url');
    $('.message-' + data).css('transition', '0.5s').css('opacity', '0');
});