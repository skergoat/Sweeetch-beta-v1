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

alert('sdfsdfsdfdsfdsfsdf');


