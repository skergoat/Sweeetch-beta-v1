// css
import "../../css/company/homepage.scss";

// JQuery 
import $ from 'jquery';

global.$ = $;
global.jQuery = $; 

// bootstrap 
import 'bootstrap';
import 'admin-lte';

// assets > js > components 
import '../components/button_disabled.js'; // disable button when form is submitted 
import '../components/edit.js'; // edit sections 
import '../components/file.js'; // file name 
import '../components/message.js'; // success or error message 
import '../components/table.js'; // dataTable 

// tinymce 
tinymce.init({
    selector: 'textarea'
});

// datepicker 
$(document).ready(function() {
    $('.js-datepicker').datepicker({
        format: 'dd-mm-yyyy'
    });
});