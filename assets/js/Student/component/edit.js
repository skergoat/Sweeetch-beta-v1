$('.nav-link.edit-items').click(function() {
    var id = $(this).attr('id');
    sessionStorage.setItem('test', '#' + id);
    // sessionStorage.setItem('height',  $('.show').css('height'));
});

$(document).ready(function() {
    var id = sessionStorage.getItem('test');
    // var height = sessionStorage.getItem('height');
    
    if(id == null) {
        id = '#custom-content-below-home-tab';
    }

    $(id).click().addClass('active');
    // $('.show').css('height', height + 'px');
});  
