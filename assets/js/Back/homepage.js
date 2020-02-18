/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "../../css/back/homepage.scss";

// console log test
// import Message from './components/test';
// console.log(Message);

// app script
// import Test from './components/test/class';
// import './components/test/event';

// JQuery
import $ from "jquery";

global.$ = $;
global.jQuery = $;

// bootstrap
import "bootstrap";
import 'admin-lte';

// var name;
// var messageText = "Bonjour, Nous avons detecte un probleme avec votre compte. Il semblerait que les documents suivants ne soient pas valides :";
// var messageEnd = "Veuillez les modifier au plus vite pour beneficier de toutes les fonctionnalites de votre compte. Bien Cordialement, l'equipe Sweeetch";

// show / hide warning form
$(".warning").click(function(e) {
  e.preventDefault();

  var id = $(this).attr("data-url");
  var url = '/sendwarning/' + id;
  var email = $('.email-' + id).text();
  // $("#message").html(messageText + messageEnd);
  $("#email").val(email);
  $("#warning-form").attr('action', url);
  $("#form-show").removeClass("hidden");

});

// $('input:checkbox').change(function() {

//   messageText = "";
//   var messageText = "Bonjour, Nous avons detecte un probleme avec votre compte. Il semblerait que les documents ne soient pas valides : ";

//   $('.check-documents').each(function() {
//     if($(this).prop("checked") == true){ 
//       var name = $(this).attr('name');
//       messageText = messageText + name + ', '
//     }
//   });

//   messageText = messageText + messageEnd; 
//   $("#message").val(messageText);

// });




