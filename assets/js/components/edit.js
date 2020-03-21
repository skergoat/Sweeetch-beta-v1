// show edit account or profile sections 
$('.nav-link.edit-items').click(function() {
    var id = $(this).attr('id');
    sessionStorage.setItem('test', '#' + id);
});

$(document).ready(function() {
    var id = sessionStorage.getItem('test');
   
    if(id == null) {
        id = '#custom-content-below-home-tab';
    }

    $(id).click().addClass('active');
});  
