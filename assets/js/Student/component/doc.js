// go and back 
function back() {
    $('#return-page').click(function() {
        let data = $('#return-page').attr('data-url');
        $('#input').prop("checked", false);
        $('#test-1 #container-ajax').load('/student/load/' + data, further);
    }); 
}

function further() {
    $('#following-page').click(function() {
        let data = $('#following-page').attr('data-url');
        $('#input').prop( "checked", false);
        $('#test-1 #container-ajax').load('/student/load/' + data, back);
    });
}

// load new page 
further();

// stop program 
$('#input').change(function() {
    let data = $(this).attr('data-url');
    $.ajax({
        type: "POST",
        url: '/student/stop/' + data,
    });
    $('a.close-modal').click();
}); 