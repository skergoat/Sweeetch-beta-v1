/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../../css/front/homepage.scss';

// console log test 
import Message from './components/test';
console.log(Message);

// JQuery 
import $ from 'jquery';

global.$ = $;
global.jQuery = $; 

// bootstrap 
import 'bootstrap';

// animate
import 'animate.css';

// main scripts 
import './components/contactform.js';
import './components/message.js';
import './components/find.js';
import './components/page.js';
import './components/success.js';
import './components/file.js';

$(document).scroll(function() {
    $('#header.header-scrolled').css({'padding':'5px 0', 'height':'90px'});
});

// url = window.location.pathname;

// switch(url) {
//     case '/student/new/':
//     $('#student').prop("checked", true);
//     break;

//     case '/company/new':
//     $('#company').prop("checked", true);
//     break;

//     case '/school/new':
//     $('#school').prop("checked", true);
//     break;
// }

// $('.signup').on('change', function() {

//     var id = $(this).attr('id');

//     switch(id) {
//         case 'student':
//         window.location.href = '/student/new';
//         break;

//         case 'company':
//         window.location.href = '/company/new';
//         break;

//         case 'school':
//         window.location.href = '/school/new';
//         break;
//     }
// });
