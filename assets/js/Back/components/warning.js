
$(".warning").click(function(e) {
    e.preventDefault();
  
    var id = $(this).attr("data-url");
    var action = $("#warning-form").attr('action');
    var email = $('.email-' + id).text();
  
    $("#email").val(email);
    $("#warning-form").attr('action', action + '/' + id);
    $("#form-show").removeClass("hidden");
  
});
  