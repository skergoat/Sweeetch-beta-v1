
import "../../css/company/homepage.scss";

// JQuery 
import $ from 'jquery';

global.$ = $;
global.jQuery = $; 

// bootstrap 
import 'bootstrap';
import 'admin-lte';

import './components/edit.js';
import './components/message.js';
import './components/button_disabled.js';
import './components/table.js';
import './components/file.js';

tinymce.init({
    selector: 'textarea'
});

$(document).ready(function() {
    $('.js-datepicker').datepicker({
        format: 'dd-mm-yyyy'
    });
});