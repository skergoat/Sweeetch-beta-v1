// homepage template js and css

// css
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
import 'admin-lte';

// animate
import 'animate.css';

// assets > js > components
import '../components/message.js';  // success or error message
import '../components/file.js'; // file name 

// ./components
// import './components/page.js'; // offers page number 
import './components/contactform.js'; // template scripts 
import './components/find.js'; // template scripts 
// import './components/success.js';

// navbar scroll 
$(document).scroll(function() {
    $('#header.header-scrolled').css({'padding':'5px 0', 'height':'90px'});
});
