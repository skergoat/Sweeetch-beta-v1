$('.browse').click(function() {
    var id = $(this).attr('data-url');
    $('#' + id).click();

    $('#' + id).change(function(e){
        var fileName = e.target.files[0].name;

        $('#browse-' + id).text(fileName);
    });   
});  
