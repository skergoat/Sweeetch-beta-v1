$(document).ready(function() {

    function getQueryParams(qs) {
        qs = qs.split("+").join(" ");
        var params = {},
            tokens,
            re = /[?&]?([^=]+)=([^&]*)/g;

        while (tokens = re.exec(qs)) {
            params[decodeURIComponent(tokens[1])]
                = decodeURIComponent(tokens[2]);
        }

        return params;
    }

    var $_GET = getQueryParams(document.location.search);

    if($_GET.domain == null) {
        $('#domain').val("tous");
    }
    else {
        $('#domain').val($_GET.domain);
    }    
}); 