
import "../../css/company/homepage.scss";

// JQuery 
import $ from 'jquery';

global.$ = $;
global.jQuery = $; 

// bootstrap 
import 'bootstrap';
import 'admin-lte';

// import ClassicEditor from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';
// import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment'; 

// ClassicEditor
//     .create( document.querySelector( '#offers_description' ))
//     .then( editor => {
//         console.log( 'Editor was initialized', editor );
//     } )
//     .catch( error => {
//         console.error( error.stack );
//     } );

import './components/edit.js';
import './components/message.js';

