$(function () {
    $("#example1").DataTable({
        "paging": true,
        "lengthChange": false,
        "pageLength": 5,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "language": {
        "emptyTable": "Aucun inscrit pour le moment",
        "paginate": {
            "previous": "Précédent",
            'next':'Suivant'
            }
        }
    });

    $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "pageLength": 5,
        "searching": false,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "language": {
        "emptyTable": "Aucun inscrit pour le moment",
        "paginate": {
            "previous": "Précédent",
            'next':'Suivant'
            }
        }
    });

    $('#example3').DataTable({
        "paging": true,
        "lengthChange": false,
        "pageLength": 5,
        "searching": false,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "language": {
        "emptyTable": "Aucun inscrit pour le moment",
        "paginate": {
            "previous": "Précédent",
            'next':'Suivant'
            }
        }
    });   
});