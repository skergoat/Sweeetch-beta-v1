var pathname = window.location.pathname;

if(pathname.match("/offers/page/[2-9]")) {

    if($(".card").length == 0) {

        var arr = pathname.split("/");
        var int = parseInt(arr[3]);
        var route = int - 1;
        window.location.href = "/offers/page/" + route;
    }
}