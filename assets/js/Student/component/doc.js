// load new page 
$('#following-page').click(function() {
    let data = $('#following-page').attr('data-url');
    $('#input').prop( "checked", false);
    $('#test-1 #container-ajax').load('/student/load/' + data);
});
// stop program 
$('#input').change(function() {
    let data = $(this).attr('data-url');
    $.ajax({
        type: "POST",
        url: '/student/stop/' + data,
    });
    $('a.close-modal').click();
}); 