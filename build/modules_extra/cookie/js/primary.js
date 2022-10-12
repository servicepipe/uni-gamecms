function getCookie(name) {
	var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
	return matches ? decodeURIComponent(matches[1]) : undefined;
}

function closeCookie() {
	if(navigator.cookieEnabled !== false) {
		var cookie_date = new Date();
		cookie_date.setYear(cookie_date.getFullYear() + 1);
		document.cookie = "access_cookie=on;path=;/expires=" + cookie_date.toUTCString();
	}

	$("#cookie").attr("style", "display: none !important");
}

$(document).ready(function() {
	if(navigator.cookieEnabled !== false) {
		if(getCookie("access_cookie") != "on") {
			$.get("../modules_extra/cookie/style.tpl", function(data) {
				$("body").append(data);
			});
		}
	}
});