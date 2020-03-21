// show tables
$(function () {   
    $("#dash1").DataTable({
        "paging": true,
        "pageLength": 5,
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "language": {
        "emptyTable": "Aucun candidat pour l'instant",
        "paginate": {
            "previous": "Précédent",
            'next':'Suivant'
            }
        }
    });   

    $("#dash2").DataTable({
        "paging": true,
        "pageLength": 5,
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "language": {
        "emptyTable": "Aucun recrutement en cours",
        "paginate": {
            "previous": "Précédent",
            'next':'Suivant'
            }
        }
    });
    
    $("#dash3").DataTable({
        "paging": true,
        "pageLength": 5,
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "language": {
        "emptyTable": "Aucune attente d'acceptation",
        "paginate": {
            "previous": "Précédent",
            'next':'Suivant'
            }
        }
    });

    $("#dash4").DataTable({
        "paging": true,
        "pageLength": 5,
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "language": {
        "emptyTable": "Aucune recrutement terminé",
        "paginate": {
            "previous": "Précédent",
            'next':'Suivant'
            }
        }
    });

    $("#dash5").DataTable({
        "paging": true,
        "pageLength": 5,
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "language": {
        "emptyTable": "Aucune recrutement terminé",
        "paginate": {
            "previous": "Précédent",
            'next':'Suivant'
            }
        }
    });
});