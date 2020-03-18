
$(function () {
    $("#example1").DataTable({
        "paging": true,
        "pageLength": 5,
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "language": {
        "emptyTable": "Aucune inscription pour le moment",
        "paginate": {
            "previous": "Précédent",
            'next':'Suivant'
            }
        }
    });   
});