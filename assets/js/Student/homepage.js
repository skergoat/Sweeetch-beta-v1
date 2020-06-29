/** student profil js and css */

// css
import '../../css/student/homepage.scss';

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
import '../components/message.js';  // success or error message 
import '../components/table.js'; // dataTable 

// ./components
import './component/edit-profile.js'; // edit languages or studies 
import './component/timeline.js'; // timeline 
import './component/doc.js'; // timeline 
// import './component/buttons.js'; 

// dataTables
$(function () {
    $("#applies1").DataTable({
        "paging": true,
        "lengthChange": false,
        "pageLength": 5,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "language": {
        "emptyTable": "Aucune candidature pour le moment",
        "paginate": {
            "previous": "Précédent",
            'next':'Suivant'
            }
        }
    });

    $('#applies2').DataTable({
        "paging": true,
        "lengthChange": false,
        "pageLength": 5,
        "searching": false,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "language": {
        "emptyTable": "Aucun recrutement pour le moment",
        "paginate": {
            "previous": "Précédent",
            'next':'Suivant'
            }
        }
    });   
});