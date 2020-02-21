/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../../css/student/homepage.scss';

// console log test 
// import Message from './components/test';
// console.log(Message);

// app script
// import Test from './components/test/class';
// import './components/test/event';

// JQuery 
import $ from 'jquery';

global.$ = $;
global.jQuery = $; 

// global.$ = $;
// global.jQuery = $; 

// bootstrap 
import 'bootstrap';
import 'admin-lte';
// import 'bootstrap-datepicker';

// main scripts 
import './component/edit-profile.js';
import './component/timeline.js';
import './component/buttons.js';
import './component/edit.js';
import './component/message.js';


