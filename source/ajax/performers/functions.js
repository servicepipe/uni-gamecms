function toasty(e = "success", t = "") {
    push(t, e);
}
function push(message, type = "info") {
	var toast = new Toasty({
		classname: "toast",
		transition: "slideLeftRightFade",
		insertBefore: false,
		progressBar: true,
		enableSounds: true
	});

	switch(type) {
		case "success":
			toast.success(message);
		break;

		case "error":
			toast.error(message);
		break;

		case "danger":
			toast.error(message);
		break;

		case "warning":
			toast.warning(message);
		break;

		default:
			toast.info(message);
		break;
	}
}
function url() {
    return get_url();
}
function get_url() {
    return "https://" + location.host + "/";
}
function send_post(site, form, callback, method = "POST") {
	form.append("phpaction", "1");
	form.append("token", $("#token").val());

	$.ajax({
		type: method,
		url: site,
		processData: false,
		contentType: false,
		data: form,
		dataType: "json",
		success: function(result) {
			callback(result);
		}
	});
}
function show_input_error(e, t, r) {
    null == r && (r = 2e3), null == t && (t = "");
    let a = $("#" + e);
    a.next(".error_message").remove(),
        a.addClass("input_error"),
        a.after("<div class='error_message'>" + t + "</div>"),
        99999 === r
            ? a.attr("disabled", "")
            : setTimeout(function () {
                  a.removeClass("input_error"), a.next(".error_message").fadeOut(0);
              }, r);
}
function show_input_success(e, t, r) {
    null == t && (t = "");
    let a = $("#" + e);
    a.next(".success_message").remove(),
        a.addClass("input_success"),
        a.after("<div class='success_message'>" + t + "</div>"),
        setTimeout(function () {
            a.removeClass("input_success"), a.next(".success_message").fadeOut(0);
        }, r);
}
function show_ok() {
    50 < (window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop)
        ? ($(".result_ok_b").fadeIn(),
          setTimeout(function () {
              $(".result_ok_b").fadeOut();
          }, 1500))
        : ($(".result_ok").fadeIn(),
          setTimeout(function () {
              $(".result_ok").fadeOut();
          }, 1500));
}
function show_error() {
    50 < (window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop)
        ? ($(".result_error_b").fadeIn(),
          setTimeout(function () {
              $(".result_error_b").fadeOut();
          }, 1500))
        : ($(".result_error").fadeIn(),
          setTimeout(function () {
              $(".result_error").fadeOut();
          }, 1500));
}
function scrollToBox(e) {
    $("html, body").animate({ scrollTop: $(e).offset().top + "px" }, { duration: 500, easing: "swing" });
}
function reset_page() {
    location.reload();
}
function go_to(e) {
    location.href = e;
}
function send_value(e, t) {
    document.getElementById(e).value = t;
}
function stop_button(e, t) {
    let r = $(e);
    var a = r.val(),
        s = r.attr("onclick");
    r.addClass("disabled"),
        r.attr("onclick", ""),
        r.val("Отправлено"),
        setTimeout(function () {
            r.removeClass("disabled"), r.attr("onclick", s), r.val(a);
        }, t);
}
function clean_tiny(e) {
    tinymce.get(e).setContent("");
}
function focus_input(e) {
    let t = $("#" + e);
    0 < t.size() && t.focus();
}
function play_sound(e, t) {
    (audio = new Audio()), (audio.src = e), (audio.volume = t), (audio.autoplay = !0);
}
function set_cookie(e, t, r, a, s, n) {
    document.cookie = e + "=" + escape(t) + (r ? "; expires=" + r : "") + (a ? "; path=" + a : "") + (s ? "; domain=" + s : "") + (n ? "; secure" : "");
}
function get_cookie(e) {
    let t = " " + document.cookie;
    var r = " " + e + "=";
    let a = null;
    e = 0;
    let s = 0;
    return 0 < t.length && -1 != (e = t.indexOf(r)) && ((e += r.length), (s = t.indexOf(";", e)), -1 == s && (s = t.length), (a = unescape(t.substring(e, s)))), a;
}
function dell_block(e) {
    $("#" + e).remove();
}
function set_enter(input, func) {
    $(input).keydown(function (event) {
        13 != event.which || event.shiftKey || (event.preventDefault(), eval(func));
    });
}
function send_form(form, func) {
    $(form).submit(function (event) {
        event.preventDefault(), eval(func);
    });
}
function create_material(e, t = 0) {
    (e.phpaction = 1), (e.token = $("#token").val());
    let r = "";
    return (
        0 === t
            ? ($.each(e, function (e, t) {
                  r = r + e + "=" + encodeURIComponent(t) + "&";
              }),
              r.substring(0, r.length - 1))
            : ((r = new FormData()),
              $.each(e, function (e, t) {
                  r.append(e, t);
              })),
        r
    );
}
function show_stub(e = "Авторизуйтесь, чтобы выполнить действие") {
    NProgress.start(), NProgress.done(), setTimeout(show_error, 500), show_noty("Down", "info", "<a>" + e + "</a>", 2e3);
}
function setImagePreview(t, r) {
    if (t.files && t.files[0]) {
        let e = new FileReader();
        (e.onload = function (e) {
            document.querySelector(r).setAttribute("src", e.target.result);
        }),
            e.readAsDataURL(t.files[0]);
    }
}
function empty(e) {
    return "" === e || " " === e || 0 === e || "0" === e || null === e || !1 === e || e === {} || e === [];
}
function ajax(parameters) {
    parameters.hasOwnProperty("data") || (parameters.data = {}),
        parameters.hasOwnProperty("inputs") || (parameters.inputs = {}),
        parameters.hasOwnProperty("dataType") || (parameters.dataType = "json"),
        parameters.hasOwnProperty("progress") || (parameters.progress = !1),
        parameters.hasOwnProperty("inputs") || (parameters.inputs = {}),
        parameters.progress && NProgress.start();
    let materialType = 0;
    parameters.hasOwnProperty("processData") && !1 === parameters.processData && (materialType = 1);
    let ajax = {
        type: "POST",
        url: parameters.controller,
        data: create_material(parameters.data, materialType),
        success: (result) => {
            parameters.progress && (NProgress.done(), setTimeout(show_ok, 500)), result.alert && alert(result.alert), result.evalJs && eval(result.evalJs), parameters.hasOwnProperty("success") && parameters.success(result);
        },
        error: (e) => {
            if ((parameters.progress && (NProgress.done(), setTimeout(show_error, 500)), "json" === parameters.dataType)) {
                let t = $.parseJSON(e.responseText);
                if (t.hasOwnProperty("errors"))
                    for (var r in t.errors)
                        if (t.errors.hasOwnProperty(r)) {
                            let e = r;
                            parameters.inputs.hasOwnProperty(r) && (e = parameters.inputs[r]), show_input_error(e, t.errors[r]);
                        }
                t.alert && alert(t.alert), parameters.hasOwnProperty("error") && parameters.error(t);
            }
        },
    };
    "json" === parameters.dataType && (ajax.dataType = parameters.dataType), parameters.hasOwnProperty("processData") && !1 === parameters.processData && ((ajax.contentType = !1), (ajax.processData = !1)), $.ajax(ajax);
}
function href(e) {
    location.href = e;
}
function preview(e, t) {
    $(e).bind("change", function () {
        this.files[0] && fr(this.files[0], t);
    });
}
function fr(e, t) {
    var r = new FileReader();
    $(r).bind("load", function () {
        $(t).css("background-image", "url('" + r.result + "')");
    }),
        r.readAsDataURL(e);
}
function serializeform(e, t) {
    for (var r in t) e.append(r, t[r]);
    return e;
}
