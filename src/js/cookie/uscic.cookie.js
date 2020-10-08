function updateCookie(cookiename, value) {
    var cookievalue = $.cookie(cookiename);
    var incookie = 0;
    if (cookievalue !== undefined) {

        if (cookievalue == "") {
            var arr = [];
        }
        else {
            var arr = cookievalue.split("-");
        }
        var index = jQuery.inArray(value, arr);
        if (index < 0) {
            incookie = 1;
            arr.push(value);
        }
        else {
            arr.splice(index, 1);
        }
        //alert(arr.length);

        if (arr.length == 0) {
            $.removeCookie(cookiename);
        }
        else {
            $.cookie(cookiename, arr.join("-"));
        }
    }
    else {
        incookie = 1;
        $.cookie(cookiename, value);
    }
    return incookie;
}

function clearCookie(cookiename) {
    $.removeCookie(cookiename);
}