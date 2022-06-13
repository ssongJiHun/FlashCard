var setCookie = function (name, value, exp) {
    var date = new Date();
    date.setTime(date.getTime() + exp * 24 * 60 * 60 * 1000);
    document.cookie = name + '=' + value + ';expires=' + date.toUTCString() + ';path=/';
};
var getCookie = function (name) {
    var value = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
    return value ? value[2] : null;
};

window.onload = function () {
    let isAccess = false;
    if (getCookie('access_id') === 'true')
        isAccess = true;
    while (!isAccess) {
        if (prompt("password", '') === '1111') {
            isAccess = true;
            setCookie("access_id", "true", 1);
        }
    }
}
