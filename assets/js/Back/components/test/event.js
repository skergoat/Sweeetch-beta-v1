import Test from './class';

// $('div').click(function(e) {

//     var id = $(this).attr('id');

//     var test = new Test(id);
//     test.click();

// })

$('form').submit(function(e) {

    e.preventDefault();

    var entity = $(this);

    var test = new Test(entity);
    test.click();

});