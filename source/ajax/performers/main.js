$(document).ajaxComplete(function (event, xhr) {
    if (xhr.status === 301 || xhr.responseText.indexOf("?attempt=") + 1 || xhr.responseText.indexOf("Доступно только авторизованным") + 1) {
        reset_page();
    }
    if (xhr.responseText.indexOf("Gag: it is forbidden to perform this action") + 1 || xhr.responseText.indexOf("ForbiddenWord: prohibited content found") + 1) {
        show_stub("Отправка сообщения заблокирована");
    }
    if (xhr.responseText.indexOf("Flood: pass a bot check") + 1) {
        var key = xhr.responseText;
        key = key.substr(key.search(/\[/));
        key = key.slice(0, -1);
        key = key.slice(1);
        show_check(key);
    }
});
function show_check(key) {
    var captcha_modal =
        '<div id="captcha_modal" class="modal fade">       <div class="modal-dialog modal-sm">        <div class="modal-content">         <div class="modal-header">          <h4 class="modal-title">Проверка</h4>         </div>         <div class="modal-body" style="padding: 30px;">          <div style="transform:scale(0.75);-webkit-transform:scale(0.75);transform-origin:0 0;-webkit-transform-origin:0 0;" data-theme="light" class="g-recaptcha clearfix" data-sitekey="' +
        key +
        '"></div>          <script src="https://www.google.com/recaptcha/api.js?hl=ru"></script>          <div id="bot_check_result"></div>          <button type="submit" class="btn btn-default" onclick="bot_check();">Отправить</button>         </div>        </div>       </div>      </div>';
    $("body").append(captcha_modal);
    $("#captcha_modal").modal("show");
}
function bot_check() {
    var captcha = null;
    if (typeof grecaptcha != "undefined") {
        captcha = grecaptcha.getResponse();
    }
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&bot_check=1&captcha=" + captcha,
        success: function (html) {
            if (typeof grecaptcha != "undefined") {
                grecaptcha.reset();
            }
            $("#bot_check_result").html(html);
        },
    });
}
function get_vk_auth_link() {
    $.ajax({
        type: "POST",
        url: "../ajax/fast_actions.php",
        data: "get_vk_auth_link=1",
        dataType: "json",
        success: function (result) {
            $("a#vk_link").attr("href", result.url);
        },
    });
}
function attach_user_vk() {
    $.ajax({
        type: "POST",
        url: "../ajax/fast_actions.php",
        data: "attach_user_vk=1",
        dataType: "json",
        success: function (result) {
            $("#vk_link").attr("href", result.url);
        },
    });
}
function unset_vk() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&unset_vk=1",
        success: function (html) {
            $("#unset_vk_result").html(html);
        },
    });
}
function get_steam_auth_link() {
    $.ajax({
        type: "POST",
        url: "../ajax/fast_actions.php",
        data: "get_steam_auth_link=1",
        dataType: "json",
        success: function (result) {
            $("a#steam_link").attr("href", result.url);
        },
    });
}
function attach_user_steam() {
    $.ajax({
        type: "POST",
        url: "../ajax/fast_actions.php",
        data: "attach_user_steam=1",
        dataType: "json",
        success: function (result) {
            $("#steam_link").attr("href", result.url);
        },
    });
}
function unset_steam() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&unset_steam=1",
        success: function (html) {
            $("#unset_steam_result").html(html);
        },
    });
}
function get_fb_auth_link() {
    $.ajax({
        type: "POST",
        url: "../ajax/fast_actions.php",
        data: "get_fb_auth_link=1",
        dataType: "json",
        success: function (result) {
            $("a#fb_link").attr("href", result.url);
        },
    });
}
function attach_user_fb() {
    $.ajax({
        type: "POST",
        url: "../ajax/fast_actions.php",
        data: "attach_user_fb=1",
        dataType: "json",
        success: function (result) {
            $("#fb_link").attr("href", result.url);
        },
    });
}
function unset_fb() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&unset_fb=1",
        success: function (html) {
            $("#unset_fb_result").html(html);
        },
    });
}
function show_reg_modal(type) {
    $("#api_auth").modal("show");
    $("#api_reg_btn").attr("onclick", 'reg_by_api("' + type + '");');
}
function reg_by_api(type) {
    var email = $("#api_email").val();
    email = encodeURIComponent(email);
    $.ajax({
        type: "POST",
        url: "../ajax/fast_actions.php",
        data: "reg_by_api=1&email=" + email + "&type=" + type,
        dataType: "json",
        success: function (result) {
            $("#result_api_reg").html(result.data);
        },
    });
}
function user_login() {
    var data = {};
    data["user_login"] = "1";
    data["login"] = $("#user_loginn").val();
    data["password"] = $("#user_password").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: create_material(data),
        success: function (html) {
            $("#result").fadeIn();
            $("#result").html(html);
        },
    });
}
function user_exit() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&user_exit=1",
        success: function () {
            location.reload();
        },
    });
}
function registration() {
    var captcha = null;
    if (typeof grecaptcha != "undefined") {
        captcha = grecaptcha.getResponse();
    }
    var token = $("#token").val();
    var login = $("#reg_login").val();
    var password = $("#reg_password").val();
    var password2 = $("#reg_password2").val();
    var email = $("#reg_email").val();
    login = encodeURIComponent(login);
    password = encodeURIComponent(password);
    password2 = encodeURIComponent(password2);
    email = encodeURIComponent(email);
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: "phpaction=1&token=" + token + "&registration=1&login=" + login + "&password=" + password + "&password2=" + password2 + "&email=" + email + "&captcha=" + captcha,
        success: function (html) {
            if (typeof grecaptcha != "undefined") {
                grecaptcha.reset();
            }
            $("#result2").html(html);
        },
    });
}
function send_new_pass() {
    var captcha = null;
    if (typeof grecaptcha != "undefined") {
        captcha = grecaptcha.getResponse();
        if (captcha == "") {
            captcha = grecaptcha.getResponse(recaptcha_2);
        }
    }
    var token = $("#token").val();
    var email = $("#email_2").val();
    email = encodeURIComponent(email);
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: "phpaction=1&send_new_pass=1&token=" + token + "&email=" + email + "&captcha=" + captcha,
        success: function (html) {
            if (typeof grecaptcha != "undefined") {
                grecaptcha.reset();
                grecaptcha.reset(recaptcha_2);
            }
            $("#result3").html(html);
        },
    });
}
function edit_user_vk() {
    var token = $("#token").val();
    var user_vk = $("#user_vk").val();
    user_vk = encodeURIComponent(user_vk);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&edit_user_vk=1&user_vk=" + user_vk,
        success: function (html) {
            $("#edit_user_vk_result").html(html);
        },
    });
}
function edit_user_fb() {
    var token = $("#token").val();
    var user_fb = $("#user_fb").val();
    user_fb = encodeURIComponent(user_fb);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&edit_user_fb=1&user_fb=" + user_fb,
        success: function (html) {
            $("#edit_user_fb_result").html(html);
        },
    });
}
function edit_user_login() {
    var token = $("#token").val();
    var user_login = $("#user_login").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&edit_user_login=1&user_login=" + user_login,
        success: function (html) {
            $("#edit_user_login_result").html(html);
        },
    });
}
function editUserRoute() {
    let data = {};
    data["editUserRoute"] = true;
    data["route"] = $("#user_route").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: create_material(data),
        success: function (html) {
            $("#edit_user_route_result").html(html);
        },
    });
}
function edit_user_name() {
    var token = $("#token").val();
    var user_name = $("#user_name").val();
    user_name = encodeURIComponent(user_name);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&edit_user_name=1&user_name=" + user_name,
        success: function (html) {
            $("#edit_user_name_result").html(html);
        },
    });
}
function edit_user_nick() {
    var token = $("#token").val();
    var user_nick = $("#user_nick").val();
    user_nick = encodeURIComponent(user_nick);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&edit_user_nick=1&user_nick=" + user_nick,
        success: function (html) {
            $("#edit_user_nick_result").html(html);
        },
    });
}
function edit_user_steam_id() {
    var token = $("#token").val();
    var user_steam_id = $("#user_steam_id").val();
    user_steam_id = encodeURIComponent(user_steam_id);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&edit_user_steam_id=1&user_steam_id=" + user_steam_id,
        success: function (html) {
            $("#edit_user_steam_id_result").html(html);
        },
    });
}
function edit_user_birth() {
    var token = $("#token").val();
    var birth_day = $("#birth_day").val();
    var birth_month = $("#birth_month").val();
    var birth_year = $("#birth_year").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&edit_user_birth=1&birth_day=" + birth_day + "&birth_month=" + birth_month + "&birth_year=" + birth_year,
        success: function (html) {
            $("#edit_user_birth_result").html(html);
        },
    });
}
function edit_user_skype() {
    var token = $("#token").val();
    var user_skype = $("#user_skype").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&edit_user_skype=1&user_skype=" + user_skype,
        success: function (html) {
            $("#edit_user_skype_result").html(html);
        },
    });
}
function edit_user_discord() {
    var token = $("#token").val();
    var user_discord = $("#user_discord").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&edit_user_discord=1&user_discord=" + user_discord,
        success: function (html) {
            $("#edit_user_discord_result").html(html);
        },
    });
}
function edit_user_telegram() {
    var token = $("#token").val();
    var user_telegram = $("#user_telegram").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&edit_user_telegram=1&user_telegram=" + user_telegram,
        success: function (html) {
            $("#edit_user_telegram_result").html(html);
        },
    });
}
function edit_first_user_password() {
    var token = $("#token").val();
    var user_password = $("#first_user_password").val();
    var user_password2 = $("#first_user_password2").val();
    user_password = encodeURIComponent(user_password);
    user_password2 = encodeURIComponent(user_password2);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&edit_first_user_password=1&user_password=" + user_password + "&user_password2=" + user_password2,
        success: function (html) {
            $("#edit_first_user_password_result").html(html);
        },
    });
}
function edit_user_password() {
    var token = $("#token").val();
    var user_old_password = $("#user_old_password").val();
    var user_password = $("#user_password").val();
    var user_password2 = $("#user_password2").val();
    user_old_password = encodeURIComponent(user_old_password);
    user_password = encodeURIComponent(user_password);
    user_password2 = encodeURIComponent(user_password2);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&edit_user_password=1&user_old_password=" + user_old_password + "&user_password=" + user_password + "&user_password2=" + user_password2,
        success: function (html) {
            $("#edit_user_password_result").html(html);
        },
    });
}
function edit_signature() {
    var token = $("#token").val();
    var signature = tinymce.get("signature").getContent();
    signature = $.trim(signature);
    signature = encodeURIComponent(signature);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&edit_signature=1&signature=" + signature,
        success: function (html) {
            $("#edit_signature_result").html(html);
        },
    });
}
function search_login(start) {
    var data = {};
    data["search_login"] = "1";
    data["login"] = $("#search_login").val();
    data["group"] = $("#groups").val();
    data["start"] = start;
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: create_material(data),
        success: function (html) {
            $("#users").html(html);
            if (data["login"] == "") {
                $("#pagination1").show();
                $("#pagination2").show();
            } else {
                $("#pagination1").hide();
                $("#pagination2").hide();
            }
        },
    });
}
function load_friends(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&load_friends=1&id=" + id,
        success: function (html) {
            $("#friends").html(html);
        },
    });
}
function search_friend(id) {
    var token = $("#token").val();
    var login = $("#search_login").val();
    login = encodeURIComponent(login);
    if (login == "") {
        load_friends(id);
    } else {
        $.ajax({
            type: "POST",
            url: "../ajax/actions_a.php",
            data: "phpaction=1&token=" + token + "&load_friends=1&id=" + id + "&login=" + login,
            success: function (html) {
                $("#friends").html(html);
            },
        });
    }
}
function load_friend_requests(type) {
    if (type != "un") {
        type = "in";
    }
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&load_friend_requests=1&type=" + type,
        success: function (html) {
            $("#" + type + "friends").html(html);
        },
    });
}
function load_col_infriends() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&load_col_infriends=1",
        success: function (html) {
            $("#col_infriends").html(html);
        },
    });
}
function add_new_friend(id, callback) {
    let token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&add_new_friend=1&id=" + id,
        dataType: "json",
        success: function (result) {
            callback(result.message);
        },
    });
}
function cancel_friend(id) {
    let token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&cancel_friend=1&id=" + id,
        dataType: "json",
        success: function (result) {
            callback(result.message);
        },
    });
}
function reject_friend(id) {
    let token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&reject_friend=1&id=" + id,
        dataType: "json",
        success: function (result) {
            callback(result.message);
            load_col_infriends();
        },
    });
}
function take_friend(id) {
    let token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&take_friend=1&id=" + id,
        dataType: "json",
        success: function (result) {
            callback(result.message);
            load_col_infriends();
        },
    });
}
function dell_friend(id) {
    if (confirm("Вы уверены?")) {
        let token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_a.php",
            data: "phpaction=1&token=" + token + "&dell_friend=1&id=" + id,
            dataType: "json",
            success: function (result) {
                callback(result.message);
                load_col_infriends();
            },
        });
    }
}
function chat_first_messages() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/chat_actions.php",
        data: "phpaction=1&token=" + token + "&chat_first_messages=1",
        success: function (html) {
            $("#chat_messages").html(html);
            window.chat_interval = setInterval(chat_get_messages, 5000);
            var block = document.getElementById("chat_messages");
            block.scrollTop = block.scrollHeight;
            setTimeout(function () {
                var block = document.getElementById("chat_messages");
                block.scrollTop = block.scrollHeight;
            }, 500);
        },
    });
}
function chat_send_message(text) {
    if ($("#stop_sending").val() == "0") {
        clearInterval(chat_interval);
        $("#stop_sending").val("1");
        $("#send_button").addClass("disabled");
        $("#send_button").attr("onclick", "");
        let token = $("#token").val();
        let message_text = "";
        if (text != undefined) {
            message_text = text;
        } else {
            message_text = encodeURIComponent($("#message_input").val());
        }
        $.ajax({
            type: "POST",
            url: "../ajax/chat_actions.php",
            data: "phpaction=1&token=" + token + "&chat_send_message=1&message_text=" + message_text,
            success: function () {
                if (text == undefined) {
                    $("#message_input").val("");
                }
                chat_get_messages(1);
                setTimeout(function () {
                    $("#send_button").text("Отправить");
                    $("#send_button").removeClass("disabled");
                    $("#send_button").attr("onclick", "chat_send_message();");
                    $("#stop_sending").val("0");
                }, 3000);
            },
        });
    }
}
function chat_get_messages(status) {
    var token = $("#token").val();
    var last_mess = $("#last_mess").val();
    $.ajax({
        type: "POST",
        url: "../ajax/chat_data.php",
        data: "phpaction=1&token=" + token + "&get_messages=1&last_mess=" + last_mess,
        success: function (html) {
            if (Number(html) != 2) {
                $("#chat_messages").append(html);
                $('[tooltip="yes"]').tooltip();
                setTimeout(function () {
                    var block = document.getElementById("chat_messages");
                    var height = block.scrollHeight - block.scrollTop;
                    if (height < 800) {
                        block.scrollTop = block.scrollHeight;
                    }
                }, 200);
                if (status != 1) {
                    play_sound("../ajax/sound/new_mess.mp3", 0.8);
                } else {
                    window.chat_interval = setInterval(chat_get_messages, 5000);
                }
            }
        },
    });
}
function chat_load_messages() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/chat_actions.php",
        data: "phpaction=1&token=" + token + "&chat_load_messages=1&load_val=" + load_val,
        success: function (html) {
            if (Number(html) != 2) {
                height = document.getElementById("chat_messages").scrollHeight - document.getElementById("chat_messages").scrollTop;
                $("#chat_messages").prepend(html);
                $('[tooltip="yes"]').tooltip();
                height2 = document.getElementById("chat_messages").scrollHeight - height;
                document.getElementById("chat_messages").scrollTop = height2;
                load_val = $("#load_val").val();
            }
        },
    });
}
function dell_chat_message(id) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_b.php",
        data: "phpaction=1&token=" + token + "&dell_chat_message=1&id=" + id,
        success: function () {
            $("#message_id_" + id).fadeOut();
            NProgress.done();
            setTimeout(show_ok, 500);
        },
    });
}
function edit_chat_message(id) {
    $("#message_text_" + id).addClass("disp-n");
    $("#message_text_e_" + id).removeClass("disp-n");
    $("#edit_message_" + id).removeClass("icon-pencil");
    $("#edit_message_" + id).addClass("icon-ok");
    $("#edit_message_" + id).attr("onclick", "save_chat_message(" + id + ")");
    $("#edit_message_" + id).attr("title", "Сохранить");
}
function save_chat_message(id) {
    NProgress.start();
    var token = $("#token").val();
    var text = $("#message_text_e_" + id).val();
    text = encodeURIComponent(text);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_b.php",
        data: "phpaction=1&token=" + token + "&save_chat_message=1&id=" + id + "&text=" + text,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                $("#message_text_e_" + id).addClass("disp-n");
                $("#message_text_" + id).removeClass("disp-n");
                $("#edit_message_" + id).removeClass("icon-ok");
                $("#edit_message_" + id).addClass("icon-pencil");
                $("#edit_message_" + id).attr("onclick", "edit_chat_message(" + id + ")");
                $("#edit_message_" + id).attr("title", "Редактировать");
                $("#message_text_" + id).html(result.text);
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
            }
        },
    });
}
function treatment(nick) {
    $("#message_input").focus();
    var text = $("#message_input").val();
    $("#message_input").val(text + nick + ", ");
}
function load_companions() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/pm_actions.php",
        data: "phpaction=1&token=" + token + "&load_companions=1",
        success: function (html) {
            $("#companions").html(html);
        },
    });
}
function create_dialog(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/pm_actions.php",
        data: "phpaction=1&token=" + token + "&create_dialog=1&id=" + id,
        dataType: "json",
        success: function (result) {
            if (result.status === 1) {
                if (result.dialogId === 0) {
                    load_dialogs();
                } else {
                    open_dialog(result.dialogId);
                }
            }
            if (result.status === 3) {
                $("#place_for_messages").html(result.data);
            }
            if (result.message !== "") {
                show_input_error("text", result.message, 99999);
            }
            history.pushState("", "", "messages?create_id=" + id);
            $("#back_btn").fadeIn();
            scrollToBox("#place_for_messages");
        },
    });
}
function send_first_message(id, text) {
    var token = $("#token").val();
    var message_text = "";
    if (text != undefined) {
        message_text = text;
    } else {
        message_text = encodeURIComponent($("#text").val());
    }
    $.ajax({
        type: "POST",
        url: "../ajax/pm_actions.php",
        data: "phpaction=1&token=" + token + "&send_first_message=1&message_text=" + message_text + "&id=" + id,
        dataType: "json",
        success: function (result) {
            if (result.status == "1") {
                open_dialog(result.id);
            } else {
                show_input_error("text", result.message, 99999);
            }
        },
    });
}
function open_dialog(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/pm_actions.php",
        data: "phpaction=1&token=" + token + "&open_dialog=1&id=" + id,
        success: function (html) {
            $("#place_for_messages").html(html);
            var redirect = "messages?id=" + id;
            history.pushState("", "", redirect);
            $("#back_btn").fadeIn();
            var block = document.getElementById("messages");
            block.scrollTop = block.scrollHeight;
            setTimeout(function () {
                var block = document.getElementById("messages");
                block.scrollTop = block.scrollHeight;
            }, 500);
        },
    });
}
function get_messages(status, id) {
    var token = $("#token").val();
    var last_mess = $("#last_mess").val();
    $.ajax({
        type: "POST",
        url: "../ajax/pm_messages.php",
        data: "phpaction=1&token=" + token + "&get_messages=1&last_mess=" + last_mess + "&id=" + id,
        success: function (html) {
            if (Number(html) != 2) {
                $("#messages").append(html);
                $('[tooltip="yes"]').tooltip();
                setTimeout(function () {
                    var block = document.getElementById("messages");
                    height = block.scrollHeight - block.scrollTop;
                    if (height < 800) {
                        block.scrollTop = block.scrollHeight;
                    }
                }, 200);
                if (status != 1) {
                    play_sound("../ajax/sound/new_mess.mp3", 0.8);
                } else {
                    window.pm_interval = setInterval("get_messages('2','" + id + "')", 5000);
                }
            }
        },
    });
}
function send_message(id, text) {
    if ($("#stop_sending").val() == "0") {
        clearInterval(pm_interval);
        var token = $("#token").val();
        var message_text = "";
        if (text != undefined) {
            message_text = text;
        } else {
            message_text = encodeURIComponent($("#text").val());
        }
        $.ajax({
            type: "POST",
            url: "../ajax/pm_actions.php",
            data: "phpaction=1&token=" + token + "&send_message=1&message_text=" + message_text + "&id=" + id,
            dataType: "json",
            success: function (result) {
                if (result.status == "1") {
                    if (text == undefined) {
                        $("#text").val("");
                    }
                    get_messages(1, id);
                    $("#send_button").addClass("disabled");
                    $("#send_button").text("Отправлено");
                    $("#send_button").attr("onclick", "");
                    $("#stop_sending").val("1");
                    setTimeout("$('#send_button').text('Отправить')", 1000);
                    setTimeout("$('#send_button').removeClass('disabled')", 1000);
                    setTimeout("$('#send_button').attr('onclick', 'send_message(" + id + ");');", 1000);
                    setTimeout("$('#stop_sending').val('0');", 1000);
                } else {
                    if ($("#text").val() != "") {
                        show_input_error("text", result.message, null);
                    }
                }
            },
        });
    }
}
function load_messages(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/pm_actions.php",
        data: "phpaction=1&token=" + token + "&load_messages=1&load_val=" + load_val + "&id=" + id,
        success: function (html) {
            if (Number(html) != 2) {
                var height = document.getElementById("messages").scrollHeight - document.getElementById("messages").scrollTop;
                $("#messages").prepend(html);
                $('[tooltip="yes"]').tooltip();
                document.getElementById("messages").scrollTop = document.getElementById("messages").scrollHeight - height;
                load_val = $("#load_val").val();
            }
        },
    });
}
function load_dialogs() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/pm_actions.php",
        data: "phpaction=1&token=" + token + "&load_dialogs=1",
        success: function (html) {
            $("#place_for_messages").html(html);
            var redirect = "messages";
            history.pushState("", "", redirect);
            $("#back_btn").fadeOut();
        },
    });
}
function dell_dialog(id) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/pm_actions.php",
        data: "phpaction=1&token=" + token + "&dell_dialog=1&id=" + id,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                $("#" + id).fadeOut();
                NProgress.done();
                setTimeout(show_ok, 500);
            } else {
                NProgress.done();
                setTimeout(show_error, 500);
            }
        },
    });
}
var have_mess = 0;
function check_news() {
    $.ajax({
        type: "POST",
        url: "../ajax/news_checker.php",
        data: "check_news=1",
        dataType: "json",
        success: function (result) {
            if (result.status1 == 1) {
                show_noty("", "info", '<a href="../friends" title="Перейти">Заявок в друзья: ' + result.val0 + "</a>", "");
            }
            if (result.status2 == 1 && window.location.pathname != "/messages") {
                show_noty("", "info", '<a href="../messages" title="Перейти">Непрочитанных диалогов: <span id="inmess2_val">' + result.val2 + "</span></a>", "");
                have_mess = 1;
                if (result.val1 > 0) {
                    play_sound("../ajax/sound/new_mess.mp3", 0.8);
                }
            }
        },
    });
    if (window.location.pathname != "/messages") {
        var check_mess = setInterval(check_messages, 30000);
    }
}
function check_messages(id, status) {
    $.ajax({
        type: "POST",
        url: "../ajax/pm_checker.php",
        data: "check_messages=1&id=" + id,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                if (status == 1) {
                    if (result.val1 > 0) {
                        play_sound("../ajax/sound/new_mess.mp3", 0.8);
                        show_noty("Down", "info", "<a>У Вас появился непрочитанный диалог</a>", 5000);
                        clearInterval(check_mess);
                        load_dialogs();
                    }
                }
                if (status == 2) {
                    if (result.val1 > 0) {
                        play_sound("../ajax/sound/new_mess.mp3", 0.8);
                        show_noty("Down", "info", "<a>У Вас появился непрочитанный диалог</a>", 5000);
                    }
                }
                if (status == undefined) {
                    if (have_mess == 0) {
                        show_noty("Down", "info", '<a href="../messages" title="Перейти">Непрочитанных диалогов: <span id="inmess2_val">' + result.val2 + "</span></a>", "");
                        have_mess = 1;
                    } else {
                        $("#inmess2_val").text(result.val2);
                    }
                    if (result.val1 > 0) {
                        play_sound("../ajax/sound/new_mess.mp3", 0.8);
                    }
                }
            }
        },
    });
}
function dell_user(id, on_page, type) {
    if (confirm("Вы уверены?")) {
        type = type || "none";
        NProgress.start();
        var token = $("#token").val();
        if (type == "none") {
            type = $("#clear_type").val();
            if (type == undefined) {
                type = 1;
            }
        }
        $.ajax({
            type: "POST",
            url: "../ajax/actions_z.php",
            data: "phpaction=1&dell_user=1&token=" + token + "&id=" + id + "&type=" + type,
            dataType: "json",
            success: function (result) {
                if (result.status == 1) {
                    if (on_page == 1) {
                        if (type == 1) {
                            go_to("../admin/users");
                        }
                        if (type == 2) {
                            reset_page();
                        }
                    }
                    dell_block(id);
                    NProgress.done();
                    setTimeout(show_ok, 500);
                } else {
                    NProgress.done();
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function dell_multi_account_relation(id, id_second) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&dell_multi_account_relation=1&token=" + token + "&id=" + id + "&id_second=" + id_second,
        success: function () {
            NProgress.done();
            setTimeout(show_ok, 500);
        },
    });
}
function change_value(table, attr, value, id) {
    var token = $("#token").val();
    value = encodeURIComponent(value);
    $.ajax({ type: "POST", url: "../ajax/actions_z.php", data: "phpaction=1&token=" + token + "&change_value=1&table=" + table + "&attr=" + attr + "&value=" + value + "&id=" + id, success: function (html) {} });
}
function admin_change_group(id) {
    NProgress.start();
    var token = $("#token").val();
    var group = $("#user_group").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&admin_change_group=1&token=" + token + "&id=" + id + "&group=" + group,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                NProgress.done();
                setTimeout(show_ok, 500);
            } else {
                NProgress.done();
                setTimeout(show_error, 500);
            }
        },
    });
}
function admin_change_login(id) {
    NProgress.start();
    var token = $("#token").val();
    var user_login = $("#user_login").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&admin_change_login=1&token=" + token + "&id=" + id + "&user_login=" + user_login,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                NProgress.done();
                setTimeout(show_ok, 500);
            } else {
                NProgress.done();
                $("#login_result").html(result.data);
                setTimeout(show_error, 500);
            }
        },
    });
}
function admin_change_password(id) {
    NProgress.start();
    var token = $("#token").val();
    var user_password = $("#user_password").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&admin_change_password=1&token=" + token + "&id=" + id + "&user_password=" + user_password,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                NProgress.done();
                setTimeout(show_ok, 500);
            } else {
                NProgress.done();
                $("#password_result").html(result.data);
                setTimeout(show_error, 500);
            }
        },
    });
}
function editUserRouteByAdmin(id) {
    NProgress.start();
    let data = {};
    data["editUserRouteByAdmin"] = true;
    data["id"] = id;
    data["route"] = $("#user_route").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status === 1) {
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
            }
            if (result.data) {
                $("#route_result").html(result.data);
            } else {
                $("#route_result").html("");
            }
        },
    });
}
function admin_change_name(id) {
    NProgress.start();
    var name = $("#user_name").val();
    change_value("users", "name", name, id);
    NProgress.done();
    setTimeout(show_ok, 500);
}
function admin_change_nick(id) {
    NProgress.start();
    var token = $("#token").val();
    var user_nick = $("#user_nick").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&admin_change_nick=1&token=" + token + "&id=" + id + "&user_nick=" + user_nick,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                NProgress.done();
                setTimeout(show_ok, 500);
            } else {
                NProgress.done();
                setTimeout(show_error, 500);
                if (result.data != "" || result.data != undefined) {
                    alert(result.data);
                }
            }
        },
    });
}
function admin_change_steam_id(id) {
    NProgress.start();
    var steam_id = $("#user_steam_id").val();
    change_value("users", "steam_id", steam_id, id);
    NProgress.done();
    setTimeout(show_ok, 500);
}
function admin_change_skype(id) {
    NProgress.start();
    var skype = $("#user_skype").val();
    change_value("users", "skype", skype, id);
    NProgress.done();
    setTimeout(show_ok, 500);
}
function admin_change_telegram(id) {
    NProgress.start();
    var telegram = $("#user_telegram").val();
    change_value("users", "telegram", telegram, id);
    NProgress.done();
    setTimeout(show_ok, 500);
}
function admin_change_discord(id) {
    NProgress.start();
    let discord = $("#user_discord").val();
    change_value("users", "discord", discord, id);
    NProgress.done();
    setTimeout(show_ok, 500);
}
function admin_change_gag(id) {
    NProgress.start();
    let gag = $("#user_gag").val();
    change_value("users", "gag", gag, id);
    NProgress.done();
    setTimeout(show_ok, 500);
}
function admin_change_email(id) {
    NProgress.start();
    var email = $("#user_email").val();
    change_value("users", "email", email, id);
    NProgress.done();
    setTimeout(show_ok, 500);
}
function admin_change_vk(id) {
    NProgress.start();
    var token = $("#token").val();
    var vk = $("#user_vk").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&token=" + token + "&admin_change_vk=1&user_vk=" + vk + "&id=" + id,
        success: function (html) {
            if (html == "") {
                NProgress.done();
                setTimeout(show_ok, 500);
            } else {
                alert(html);
                NProgress.done();
                setTimeout(show_error, 500);
            }
        },
    });
}
function admin_change_fb(id) {
    NProgress.start();
    var token = $("#token").val();
    var fb = $("#user_fb").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&token=" + token + "&admin_change_fb=1&user_fb=" + fb + "&id=" + id,
        success: function (html) {
            if (html == "") {
                NProgress.done();
                setTimeout(show_ok, 500);
            } else {
                alert(html);
                NProgress.done();
                setTimeout(show_error, 500);
            }
        },
    });
}
function admin_change_signature(id) {
    NProgress.start();
    var token = $("#token").val();
    var signature = tinymce.get("signature").getContent();
    signature = $.trim(signature);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&token=" + token + "&admin_change_signature=1&signature=" + signature + "&id=" + id,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
                if (result.data != "" || result.data != undefined) {
                    alert(result.data);
                }
            }
        },
    });
}
function admin_activate_user(id) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&token=" + token + "&admin_activate_user=1&id=" + id,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                setTimeout(show_ok, 500);
                $("#activate_user").html("<p class='text-success'>Пользователь активирован</p>");
                setTimeout(function () {
                    $("#activate_user").fadeOut();
                }, 2000);
            } else {
                setTimeout(show_error, 500);
                if (result.data != "" || result.data != undefined) {
                    alert(result.data);
                }
            }
        },
    });
}
function admin_change_birth(id) {
    NProgress.start();
    var token = $("#token").val();
    var birth_day = $("#birth_day").val();
    var birth_month = $("#birth_month").val();
    var birth_year = $("#birth_year").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&token=" + token + "&admin_change_birth=1&birth_day=" + birth_day + "&birth_month=" + birth_month + "&birth_year=" + birth_year + "&id=" + id,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
                if (result.data != "" || result.data != undefined) {
                    alert(result.data);
                }
            }
        },
    });
}
function add_new() {
    NProgress.start();
    var token = $("#token").val();
    var img = $("#input_img").val();
    var classs = $("#class").val();
    var name = $("#name").val();
    var short_text = $("#short_text").val();
    var date = $("#publish_date").val();
    var text = tinymce.get("text").getContent();
    text = $.trim(text);
    text = encodeURIComponent(text);
    classs = encodeURIComponent(classs);
    name = encodeURIComponent(name);
    short_text = encodeURIComponent(short_text);
    date = encodeURIComponent(date);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_b.php",
        data: "phpaction=1&token=" + token + "&add_new=1&img=" + img + "&class=" + classs + "&name=" + name + "&short_text=" + short_text + "&text=" + text + "&date=" + date,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                $("#create_btn").addClass("disabled");
                $("#create_btn").text("Сохранение...");
                setTimeout("$('#create_btn').text('Готово')", 500);
                setTimeout("$('#create_btn').text('Сохранить')", 1000);
                setTimeout("$('#create_btn').removeClass('disabled')", 1000);
                NProgress.done();
                setTimeout(show_ok, 500);
                setTimeout(function () {
                    document.location.href = "../news/new?id=" + result.id;
                }, 1500);
            } else {
                NProgress.done();
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply, null);
            }
        },
    });
}
function change_new(id) {
    NProgress.start();
    var token = $("#token").val();
    var img = $("#input_img").val();
    var classs = $("#class").val();
    var name = $("#name").val();
    var short_text = $("#short_text").val();
    var date = $("#publish_date").val();
    var text = tinymce.get("text").getContent();
    text = $.trim(text);
    text = encodeURIComponent(text);
    classs = encodeURIComponent(classs);
    name = encodeURIComponent(name);
    short_text = encodeURIComponent(short_text);
    date = encodeURIComponent(date);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_b.php",
        data: "phpaction=1&token=" + token + "&change_new=1&img=" + img + "&class=" + classs + "&name=" + name + "&short_text=" + short_text + "&text=" + text + "&date=" + date + "&id=" + id,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                $("#create_btn").addClass("disabled");
                $("#create_btn").text("Сохранение...");
                setTimeout("$('#create_btn').text('Готово')", 500);
                setTimeout("$('#create_btn').text('Изменить')", 1000);
                setTimeout("$('#create_btn').removeClass('disabled')", 1000);
                NProgress.done();
                setTimeout(show_ok, 500);
                setTimeout(function () {
                    document.location.href = "new?id=" + result.id;
                }, 1500);
            } else {
                NProgress.done();
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply, null);
            }
        },
    });
}
function dell_new(id, type) {
    if (confirm("Вы уверены?")) {
        NProgress.start();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_c.php",
            data: "phpaction=1&token=" + token + "&dell_new=1&id=" + id,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    setTimeout(show_ok, 500);
                    if (type == 1) {
                        $("#new" + id).fadeOut();
                    } else {
                        location.href = "index";
                    }
                } else {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function load_new_comments(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: "phpaction=1&token=" + token + "&load_new_comments=1&id=" + id,
        success: function (html) {
            $("#comments").html(html);
        },
    });
}
function send_new_comment(id, txt) {
    NProgress.start();
    var token = $("#token").val();
    var text = tinymce.get("text").getContent();
    text = $.trim(text);
    text = encodeURIComponent(text);
    if (txt != undefined) {
        text = txt;
    }
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&send_new_comment=1&text=" + text + "&id=" + id,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                stop_button("#send_btn", 1000);
                clean_tiny("text");
                load_new_comments(id);
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply, null);
            }
        },
    });
}
function dell_new_comment(id) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_b.php",
        data: "phpaction=1&token=" + token + "&dell_new_comment=1&id=" + id,
        success: function () {
            $("#message_id_" + id).fadeOut();
            NProgress.done();
            setTimeout(show_ok, 500);
        },
    });
}
function treatment_comment(nick) {
    nick = nick + ", ";
    tinymce.activeEditor.insertContent(nick);
}
function add_section() {
    NProgress.start();
    var data = {};
    data["add_section"] = "1";
    var sec_data = $("#section_settings").serialize();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: create_material(data) + "&" + sec_data,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                $("#add_section").modal("hide");
                setTimeout(show_ok, 500);
                load_sections();
            } else {
                setTimeout(show_error, 500);
                if (result.data != "" && result.data != undefined) {
                    show_input_error(result.input, result.data, null);
                }
            }
        },
    });
}
function edit_section(id) {
    NProgress.start();
    var data = {};
    data["edit_section"] = "1";
    data["id"] = id;
    var sec_data = $("#section_settings" + id).serialize();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: create_material(data) + "&" + sec_data,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                NProgress.done();
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
                if (result.data != "" && result.data != undefined) {
                    show_input_error(result.input + id, result.data, null);
                }
            }
        },
    });
}
function load_sections() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_c.php",
        data: "phpaction=1&load_sections=1&token=" + token,
        success: function (html) {
            $("#sections").html(html);
        },
    });
}
function load_sections_list() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_c.php",
        data: "phpaction=1&load_sections_list=1&token=" + token,
        success: function (html) {
            $("#sections_list").html(html);
        },
    });
}
function load_forums_list(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_b.php",
        data: "phpaction=1&load_forums_list=1&token=" + token + "&id=" + id,
        success: function (html) {
            $("#forums_list").html(html);
        },
    });
}
function up_section(id) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&up_section=1&token=" + token + "&id=" + id,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                load_sections();
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
            }
        },
    });
}
function down_section(id) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&down_section=1&token=" + token + "&id=" + id,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                load_sections();
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
            }
        },
    });
}
function add_forum() {
    NProgress.start();
    var token = $("#token").val();
    var name = $("#forum_name").val();
    var description = $("#forum_description").val();
    var section = $("#forum_sections").val();
    var img = $("#forum_img").val();
    name = encodeURIComponent(name);
    description = encodeURIComponent(description);
    section = encodeURIComponent(section);
    img = encodeURIComponent(img);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_c.php",
        data: "phpaction=1&add_forum=1&token=" + token + "&name=" + name + "&description=" + description + "&section=" + section + "&img=" + img,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                $("#add_forum").modal("hide");
                setTimeout(show_ok, 500);
                load_forums(section);
            } else {
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply);
            }
        },
    });
}
function edit_forum(id) {
    NProgress.start();
    var token = $("#token").val();
    var name = $("#forum_name" + id).val();
    var description = $("#forum_description" + id).val();
    name = encodeURIComponent(name);
    description = encodeURIComponent(description);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_c.php",
        data: "phpaction=1&edit_forum=1&token=" + token + "&name=" + name + "&description=" + description + "&id=" + id,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                $("#edit_forum_btn" + id).attr("onclick", "");
                $("#edit_forum_btn" + id).attr("class", "btn btn-default disabled");
                setTimeout(function () {
                    $("#edit_forum_btn" + id).attr("onclick", 'edit_forum("' + id + '");');
                    $("#edit_forum_btn" + id).attr("class", "btn btn-default");
                }, 500);
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
                show_input_error(result.input + id, result.reply);
            }
        },
    });
}
function up_forum(id, id2) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_c.php",
        data: "phpaction=1&up_forum=1&token=" + token + "&id=" + id + "&id2=" + id2,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                load_forums(id2);
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
            }
        },
    });
}
function down_forum(id, id2) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_c.php",
        data: "phpaction=1&down_forum=1&token=" + token + "&id=" + id + "&id2=" + id2,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                load_forums(id2);
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
            }
        },
    });
}
function dell_section(id) {
    if (confirm("Вы уверены? Все форумы и сообщения данного раздела будут удалены!")) {
        NProgress.start();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_z.php",
            data: "phpaction=1&dell_section=1&token=" + token + "&id=" + id,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    setTimeout(show_ok, 500);
                    $("#section_" + id).fadeOut();
                } else {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function dell_forum(id) {
    if (confirm("Вы уверены?")) {
        NProgress.start();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_c.php",
            data: "phpaction=1&dell_forum=1&token=" + token + "&id=" + id,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    alert("Рекомендуется выполнить переподсчет рейтинга и спасибок пользователей в админ центре.");
                    setTimeout(show_ok, 500);
                    $("#forum" + id).fadeOut();
                } else {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function dell_topic(id, forum) {
    if (confirm("Вы уверены?")) {
        NProgress.start();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_c.php",
            data: "phpaction=1&dell_topic=1&token=" + token + "&id=" + id,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    setTimeout(show_ok, 500);
                    setTimeout(function () {
                        document.location.href = "forum?id=" + forum;
                    }, 1500);
                } else {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function dell_answer(id) {
    if (confirm("Вы уверены?")) {
        NProgress.start();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_b.php",
            data: "phpaction=1&dell_answer=1&token=" + token + "&id=" + id,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    setTimeout(show_ok, 500);
                    $("#answer_" + id).fadeOut();
                } else {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function load_forums(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_c.php",
        data: "phpaction=1&load_forums=1&token=" + token + "&id=" + id,
        success: function (html) {
            $("#forums" + id).html(html);
        },
    });
}
function add_topic(id) {
    NProgress.start();
    var token = $("#token").val();
    var name = $("#name").val();
    var text = tinymce.get("text").getContent();
    var img = $("#topic_img").val();
    text = encodeURIComponent(text);
    name = encodeURIComponent(name);
    img = encodeURIComponent(img);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&add_topic=1&token=" + token + "&name=" + name + "&text=" + text + "&img=" + img + "&id=" + id,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                setTimeout(show_ok, 500);
                $("#create_btn").addClass("disabled");
                $("#create_btn").attr("onclick", "");
                $("#create_btn").text("Создано");
                setTimeout(function () {
                    document.location.href = "topic?id=" + result.id;
                }, 1500);
            } else {
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply, null);
            }
        },
    });
}
function edit_topic(id, type) {
    type = type || "";
    NProgress.start();
    var token = $("#token").val();
    var name = $("#name").val();
    var text = tinymce.get("text").getContent();
    var img = $("#topic_img").val();
    text = $.trim(text);
    text = encodeURIComponent(text);
    name = encodeURIComponent(name);
    img = encodeURIComponent(img);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_b.php",
        data: "phpaction=1&edit_topic=1&token=" + token + "&name=" + name + "&text=" + text + "&img=" + img + "&id=" + id,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                setTimeout(show_ok, 500);
                if (type == "") {
                    $("#create_btn").addClass("disabled");
                    $("#create_btn").text("Сохранение...");
                    setTimeout("$('#create_btn').text('Готово')", 500);
                    setTimeout("$('#create_btn').text('Изменить')", 1000);
                    setTimeout(function () {
                        document.location.href = "topic?id=" + id;
                    }, 1500);
                }
            } else {
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply, null);
            }
        },
    });
}
function edit_message(id) {
    NProgress.start();
    var token = $("#token").val();
    var text = tinymce.get("text").getContent();
    text = $.trim(text);
    text = encodeURIComponent(text);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_b.php",
        data: "phpaction=1&edit_message=1&token=" + token + "&text=" + text + "&id=" + id,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                $("#create_btn").addClass("disabled");
                setTimeout("$('#create_btn').text('Готово..')", 500);
                setTimeout("$('#create_btn').text('Изменить')", 1000);
                setTimeout("$('#create_btn').removeClass('disabled')", 1000);
                setTimeout(show_ok, 500);
                setTimeout("document.location.href = document.referrer", 1500);
            }
            if (result.status == 2) {
                setTimeout(show_error, 500);
            }
            if (result.status == 3) {
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply, null);
            }
        },
    });
}
function edit_topic_forum(id) {
    NProgress.start();
    var token = $("#token").val();
    var forum = $("#forums").val();
    forum = encodeURIComponent(forum);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_b.php",
        data: "phpaction=1&edit_topic_forum=1&token=" + token + "&forum=" + forum + "&id=" + id,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                $("#create_btn2").addClass("disabled");
                $("#create_btn2").text("Сохранение...");
                setTimeout("$('#create_btn2').text('Готово')", 500);
                setTimeout("$('#create_btn2').text('Изменить')", 1000);
                setTimeout("$('#create_btn2').removeClass('disabled')", 1000);
                NProgress.done();
                setTimeout(show_ok, 500);
            } else {
                NProgress.done();
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply, null);
            }
        },
    });
}
function edit_topic_status(id) {
    NProgress.start();
    var token = $("#token").val();
    var status = $("#status").val();
    status = encodeURIComponent(status);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_b.php",
        data: "phpaction=1&edit_topic_status=1&token=" + token + "&status=" + status + "&id=" + id,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                $("#create_btn3").addClass("disabled");
                $("#create_btn3").text("Сохранение...");
                setTimeout("$('#create_btn3').text('Готово')", 500);
                setTimeout("$('#create_btn3').text('Изменить')", 1000);
                setTimeout("$('#create_btn3').removeClass('disabled')", 1000);
                NProgress.done();
                setTimeout(show_ok, 500);
            } else {
                NProgress.done();
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply, null);
            }
        },
    });
}
function send_answer(id) {
    NProgress.start();
    var token = $("#token").val();
    var text = tinymce.get("text").getContent();
    text = $.trim(text);
    text = encodeURIComponent(text);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&send_answer=1&token=" + token + "&text=" + text + "&id=" + id,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                stop_button("#send_btn", 1000);
                clean_tiny("text");
                setTimeout(show_ok, 500);
                document.location.href = "topic?id=" + id + "&page=last";
            } else {
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply, null);
            }
        },
    });
}
function get_servers() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: "phpaction=1&get_servers=1&token=" + token,
        success: function (html) {
            $("#servers").html(html);
        },
    });
}
function get_players(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: "phpaction=1&get_players=1&token=" + token + "&id=" + id,
        success: function (html) {
            $("#server-players" + id).html(html);
        },
    });
}
function load_tickets(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&load_tickets=1&token=" + token + "&id=" + id,
        success: function (html) {
            $("#tickets").html(html);
        },
    });
}
function load_open_tickets() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_c.php",
        data: "phpaction=1&load_open_tickets=1&token=" + token,
        success: function (html) {
            $("#open_tickets").html(html);
        },
    });
}
function load_close_tickets(load_val) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_c.php",
        data: "phpaction=1&load_close_tickets=1&token=" + token + "&load_val=" + load_val,
        success: function (html) {
            if (load_val == "first") {
                $("#close_tickets").html(html);
            } else {
                dell_block("loader_" + load_val);
                $("#close_tickets").append(html);
            }
        },
    });
}
function add_ticket() {
    NProgress.start();
    var token = $("#token").val();
    var name = $("#name").val();
    var file = $("#loaded_file").val();
    var text = tinymce.get("text").getContent();
    text = $.trim(text);
    name = encodeURIComponent(name);
    text = encodeURIComponent(text);
    file = encodeURIComponent(file);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&add_ticket=1&token=" + token + "&name=" + name + "&text=" + text + "&file=" + file,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                setTimeout(show_ok, 500);
                setTimeout(function () {
                    document.location.href = "ticket?id=" + result.id;
                }, 2000);
            } else {
                if (result.status == 3) {
                    $("#ticket_result").html('<small style="color: #B74747">* Вы можете создавать тикет раз в ' + result.ticket_interval + " час(-а/-ов).</small><br>");
                    setTimeout(show_error, 500);
                } else {
                    setTimeout(show_error, 500);
                    show_input_error(result.input, result.reply);
                }
            }
        },
    });
}
function close_ticket(id) {
    if (confirm("Вы уверены?")) {
        NProgress.start();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_a.php",
            data: "phpaction=1&close_ticket=1&token=" + token + "&id=" + id,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    setTimeout(show_ok, 500);
                    $("#status").html("Закрыт");
                    $("#add_ticket_answer").html('<span class="empty-element">Тикет закрыт</span>');
                    $("#status").removeClass("label-info");
                    $("#status").addClass("label-success");
                } else {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function load_ticket_answers(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&load_ticket_answers=1&token=" + token + "&id=" + id,
        success: function (html) {
            $("#answers").html(html);
        },
    });
}
function dell_ticket_answer(id) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_c.php",
        data: "phpaction=1&dell_ticket_answer=1&token=" + token + "&id=" + id,
        success: function () {
            $("#message_id_" + id).fadeOut();
            NProgress.done();
            setTimeout(show_ok, 500);
        },
    });
}
function load_users_comments(id, load_val) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: "phpaction=1&load_users_comments=1&token=" + token + "&id=" + id + "&load_val=" + load_val,
        success: function (html) {
            if (load_val == "first") {
                $("#comments").html(html);
            } else {
                dell_block("loader" + load_val);
                $("#comments").append(html);
            }
        },
    });
}
function send_user_comment(id, txt) {
    NProgress.start();
    var token = $("#token").val();
    var text = tinymce.get("text").getContent();
    text = $.trim(text);
    text = encodeURIComponent(text);
    if (txt != undefined) {
        text = txt;
    }
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&send_user_comment=1&token=" + token + "&text=" + text + "&id=" + id,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                stop_button("#send_btn", 1000);
                clean_tiny("text");
                load_users_comments(id, "first");
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
                show_noty("Down", "error", "<a>" + result.reply + "</a>", "3000");
            }
        },
    });
}
function dell_user_comment(id) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&dell_user_comment=1&token=" + token + "&id=" + id,
        success: function () {
            $("#message_id_" + id).fadeOut();
            NProgress.done();
            setTimeout(show_ok, 500);
        },
    });
}
function refill_balance(type) {
    NProgress.start();
    var token = $("#token").val();
    var number = $("#number_" + type).val();
    number = encodeURIComponent(number);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_m.php",
        data: "phpaction=1&refill_balance=1&token=" + token + "&number=" + number + "&type=" + type,
        success: function (html) {
            $("#balance_result_" + type).html(html);
        },
    });
}
function get_operations(page = 1) {
    let token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_m.php",
        data: "phpaction=1&get_operations=1&token=" + token + "&page=" + page,
        success: function (html) {
            if (page === 1) {
                $("#operations").html(html);
            } else {
                dell_block("loader" + page);
                $("#operations").append(html);
            }
        },
    });
}
function get_services(id) {
    var data = {};
    data["get_services"] = "1";
    data["id"] = id;
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                $("#store_services").html(result.data);
                get_tarifs(result.service);
            }
        },
    });
}
function get_tarifs(id) {
    var data = {};
    data["get_tarifs"] = "1";
    data["id"] = id;
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                $("#store_tarifs").html(result.data);
                $("#store_service_info").html(result.text);
            }
        },
    });
}
function get_server_store(id) {
    var data = {};
    data["get_server_store"] = "1";
    data["id"] = id;
    data["type"] = 1;
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: create_material(data),
        success: function (html) {
            $("#store_server_info").html(html);
        },
    });
}
function change_store_bind_type(type) {
    if (!$("#player_nick").hasClass("disp-n")) {
        $("#player_nick").addClass("disp-n");
    }
    if (!$("#player_steam_id").hasClass("disp-n")) {
        $("#player_steam_id").addClass("disp-n");
    }
    if (!$("#player_pass").hasClass("disp-n")) {
        $("#player_pass").addClass("disp-n");
    }
    if (type == 1) {
        $("#player_nick").removeClass("disp-n");
        $("#player_pass").removeClass("disp-n");
    }
    if (type == 2) {
        $("#player_steam_id").removeClass("disp-n");
    }
    if (type == 3) {
        $("#player_pass").removeClass("disp-n");
        $("#player_steam_id").removeClass("disp-n");
    }
}
function change_admin_bind_type(type, id) {
    if (!$("#input_name" + id).hasClass("disp-n")) {
        $("#input_name" + id).addClass("disp-n");
    }
    if (!$("#input_pass" + id).hasClass("disp-n")) {
        $("#input_pass" + id).addClass("disp-n");
    }
    if (type == 1) {
        $("#input_name" + id).removeClass("disp-n");
        $("#input_pass" + id).removeClass("disp-n");
    }
    if (type == 2) {
        $("#input_name" + id).removeClass("disp-n");
    }
    if (type == 3) {
        $("#input_name" + id).removeClass("disp-n");
        $("#input_pass" + id).removeClass("disp-n");
    }
}
function on_buying() {
    var status = $("#store_checbox").attr("data-status");
    if (status == "2") {
        $("#store_checbox").attr("data-status", "1");
        $("#store_buy_btn").removeClass("disabled");
        $("#store_buy_btn").attr("onclick", "buy_service();");
    } else {
        $("#store_checbox").attr("data-status", "2");
        $("#store_buy_btn").addClass("disabled");
        $("#store_buy_btn").attr("onclick", "");
    }
    $("#store_buy_btn").focus();
}
function buy_service(check1, check2) {
    $("#store_buy_btn").attr("onclick", "");
    var data = {};
    data["buy_service"] = "1";
    data["server"] = $("#store_server").val();
    data["service"] = $("#store_services").val();
    data["tarifs"] = $("#store_tarifs").val();
    data["type"] = $("#store_type").val();
    data["nick"] = $("#player_nick").val();
    data["pass"] = $("#player_pass").val();
    data["steam_id"] = $("#player_steam_id").val();
    data["check1"] = check1;
    data["check2"] = check2;
    $.ajax({
        type: "POST",
        url: "../ajax/actions_m.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                setTimeout(show_ok, 500);
                $("#buy_service_area").html('<div class="bs-callout bs-callout-success transition_h_2">' + result.data + "</div>");
                $("#balance").html(result.shilings);
            }
            if (result.status == 2) {
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply, null);
                reset_buying(1);
            }
            if (result.status == 3) {
                setTimeout(show_error, 500);
                $("#buy_result").html("<p class='text-danger'>" + result.data + "</p>");
                reset_buying(1);
            }
            if (result.status == 4) {
                $("#buy_result").html("<p class='text-danger'>На сервере уже имеется услуга, прикрепленная к данному игровому аккаунту. Желаете совместить услуги?</p>");
                $("#store_answer_btn").removeClass("disp-n");
                $("#store_answer_btn").attr("onclick", "reset_buying();");
                $("#store_buy_btn").html("Да");
                $("#store_buy_btn").attr("onclick", "buy_service(1,0);");
            }
            if (result.status == 5) {
                $("#buy_result").html('<p class="text-danger">Вам предложено изменить группу на "' + result.group + '". Принять предложение?</p>');
                $("#store_answer_btn").removeClass("disp-n");
                $("#store_answer_btn").attr("onclick", "buy_service(1,2);");
                $("#store_buy_btn").html("Да");
                $("#store_buy_btn").attr("onclick", "buy_service(1,1);");
            }
        },
    });
}
function reset_buying(type) {
    if (type != 1) {
        $("#buy_result").empty();
    }
    $("#store_answer_btn").addClass("disp-n");
    $("#store_answer_btn").attr("onclick", "");
    $("#store_buy_btn").attr("onclick", "buy_service();");
    $("#store_buy_btn").html("Купить");
}
function buy_unban(id, server) {
    if (confirm("Вы действительно хотите купить разбан?")) {
        NProgress.start();
        var token = $("#token").val();
        id = encodeURIComponent(id);
        server = encodeURIComponent(server);
        $.ajax({
            type: "POST",
            url: "../ajax/actions_m.php",
            data: "phpaction=1&buy_unban=1&token=" + token + "&id=" + id + "&server=" + server,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    setTimeout(show_ok, 500);
                    $("#buy_unban_btn" + id).empty();
                    $("#bid" + id).attr("class", "success");
                    $("#baninfo" + id).html('<div class="bs-callout bs-callout-success"><h4>Поздравляем!</h4><p>Разбан успешно куплен!</p></div>');
                }
                if (result.status == 2) {
                    setTimeout(show_error, 500);
                    if (result.info != "" || result.info != undefined) {
                        alert(result.info);
                    }
                }
            },
        });
    }
}
function give_money(id) {
    NProgress.start();
    var money = prompt("Сколько?: ", "100");
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&give_money=1&token=" + token + "&id=" + id + "&money=" + money,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                NProgress.done();
                setTimeout(show_ok, 500);
                $("#money").text(result.res);
            } else {
                NProgress.done();
                setTimeout(show_error, 500);
            }
        },
    });
}
function pick_up_money(id) {
    NProgress.start();
    var money = prompt("Сколько?: ", "100");
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&pick_up_money=1&token=" + token + "&id=" + id + "&money=" + money,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                NProgress.done();
                setTimeout(show_ok, 500);
                $("#money").text(result.res);
            } else {
                NProgress.done();
                setTimeout(show_error, 500);
            }
        },
    });
}
function take_proc(id) {
    NProgress.start();
    var proc = prompt("Сколько %?: ", "5");
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&take_proc=1&token=" + token + "&id=" + id + "&proc=" + proc,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                NProgress.done();
                setTimeout(show_ok, 500);
                $("#proc").text(result.res);
            } else {
                NProgress.done();
                setTimeout(show_error, 500);
            }
        },
    });
}
function add_ban() {
    NProgress.start();
    var token = $("#token").val();
    var server = $("#server").val();
    var bid_db = $("#bid_db").val();
    var nick_db = $("#nick_db").val();
    var reason_db = $("#reason_db").val();
    var nick = $("#nick").val();
    var reason = $("#reason").val();
    var screens = $("#images-load-result-value").val();
    var demo = $("#demo").val();
    var text = tinymce.get("text").getContent();
    text = $.trim(text);
    server = encodeURIComponent(server);
    nick = encodeURIComponent(nick);
    reason = encodeURIComponent(reason);
    text = encodeURIComponent(text);
    screens = encodeURIComponent(screens);
    demo = encodeURIComponent(demo);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data:
            "phpaction=1&add_ban=1&token=" +
            token +
            "&server=" +
            server +
            "&nick=" +
            nick +
            "&reason=" +
            reason +
            "&text=" +
            text +
            "&screens=" +
            screens +
            "&demo=" +
            demo +
            "&bid_db=" +
            bid_db +
            "&nick_db=" +
            nick_db +
            "&reason_db=" +
            reason_db,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                $("#create_btn").addClass("disabled");
                $("#create_btn").text("Добавление...");
                $("#create_btn").attr("onclick", "");
                setTimeout("$('#create_btn').text('Готово')", 500);
                setTimeout(show_ok, 500);
                setTimeout(function () {
                    document.location.href = "ban?id=" + result.id;
                }, 2000);
            } else {
                if (result.status == 3) {
                    $("#result").html('<small style="color: #B74747">* Вы можете создавать заявку раз в 24 часа.</small><br>');
                    setTimeout(show_error, 500);
                } else {
                    setTimeout(show_error, 500);
                    show_input_error(result.input, result.reply);
                }
            }
        },
    });
}
function select_ban_type(type) {
    $("#search_ban_res").empty();
    $("#dop").attr("class", "disp-n");
    $("#none").attr("class", "disp-n");
    $("#db").attr("class", "disp-n");
    if (type == 0 || type == 1) {
        $("#dop").attr("class", "disp-b");
        $("#none").attr("class", "disp-b");
    }
    if (type == 2 || type == 3 || type == 4 || type == 5) {
        $("#db").attr("class", "disp-b");
    }
}
function dell_ban(id) {
    if (confirm("Вы уверены?")) {
        NProgress.start();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_c.php",
            data: "phpaction=1&token=" + token + "&dell_ban=1&id=" + id,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    setTimeout(show_ok, 500);
                    location.href = "index";
                } else {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function load_ban_comments(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: "phpaction=1&token=" + token + "&load_ban_comments=1&id=" + id,
        success: function (html) {
            $("#comments").html(html);
        },
    });
}
function send_ban_comment(id) {
    NProgress.start();
    var token = $("#token").val();
    var text = tinymce.get("text").getContent();
    text = $.trim(text);
    text = encodeURIComponent(text);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&send_ban_comment=1&text=" + text + "&id=" + id,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                clean_tiny("text");
                stop_button("#send_btn", 1000);
                load_ban_comments(id);
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply, null);
            }
        },
    });
}
function dell_ban_comment(id) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_b.php",
        data: "phpaction=1&token=" + token + "&dell_ban_comment=1&id=" + id,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                $("#message_id_" + id).fadeOut();
                setTimeout(show_ok, 500);
            }
            if (result.status == 2) {
                setTimeout(show_error, 500);
            }
        },
    });
}
function close_ban(id, type, bid) {
    if (confirm("Вы уверены?")) {
        NProgress.start();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_c.php",
            data: "phpaction=1&close_ban=1&token=" + token + "&id=" + id + "&type=" + type + "&bid=" + bid,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    setTimeout(show_ok, 500);
                    $("#closed").html('<p><span class="m-icon icon-user"></span> <b>Рассмотрел: </b><a href="../profile?id=' + result.closed + '">' + result.closed_a + "</a></p>");
                    $("#status").removeClass("label-default");
                    $("#status").removeClass("label-success");
                    $("#status").removeClass("label-danger");
                    if (type == 1) {
                        $("#status").addClass("label-success");
                        $("#status").html("Разбанен");
                        if (bid != "0") {
                            alert("Игрок разбанен автоматически!");
                        } else {
                            alert("Разбаньте игрока вручную.");
                        }
                    } else {
                        $("#status").addClass("label-danger");
                        $("#status").html("Не разбанен");
                    }
                }
                if (result.status == 2) {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function close_ban2(server, bid) {
    if (confirm("Вы уверены?")) {
        NProgress.start();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_c.php",
            data: "phpaction=1&close_ban2=1&token=" + token + "&server=" + server + "&bid=" + bid,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    $("#buy_unban_btn" + bid).empty();
                    $("#bid" + bid).attr("class", "success");
                    $("#baninfo" + bid).html('<div class="bs-callout bs-callout-success"><h4>Выполнено!</h4><p>Игрок разбанен!</p></div>');
                }
                if (result.status == 2) {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function send_ticket_answer(id) {
    NProgress.start();
    var token = $("#token").val();
    var text = tinymce.get("text").getContent();
    text = $.trim(text);
    text = encodeURIComponent(text);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&send_ticket_answer=1&token=" + token + "&text=" + text + "&id=" + id,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                stop_button("#send_btn", 1000);
                clean_tiny("text");
                load_ticket_answers(id);
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply, null);
            }
        },
    });
}
function dell_ticket(id) {
    if (confirm("Вы уверены?")) {
        NProgress.start();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_c.php",
            data: "phpaction=1&token=" + token + "&dell_ticket=1&id=" + id,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    setTimeout(show_ok, 500);
                    location.href = "index";
                } else {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function load_banlist(start, server, name) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: "phpaction=1&load_banlist=1&token=" + token + "&start=" + start + "&server=" + server + "&name=" + name,
        success: function (html) {
            $("#banlist").html(html);
        },
    });
}
function search_ban_application(server) {
    var token = $("#token").val();
    var ban = $("#search_ban").val();
    ban = encodeURIComponent(ban);
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: "phpaction=1&search_ban=1&token=" + token + "&ban=" + ban + "&server=" + server,
        success: function (html) {
            $("#baninfo").html(html);
            $("#ban").modal("show");
        },
    });
}
function find_bans() {
    NProgress.start();
    var token = $("#token").val();
    var ban = $("#nick_db").val();
    var server = $("#server").val();
    ban = encodeURIComponent(ban);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&find_bans=1&token=" + token + "&ban=" + ban + "&server=" + server,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                $("#search_ban_res_full").empty();
                $("#dop").attr("class", "disp-n");
                $("#search_ban_res_min").html(result.data);
                $("#bans_table").show();
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
                $("#search_ban_res_min").html(result.data);
            }
        },
    });
}
function search_ban2(ban, server) {
    NProgress.start();
    var token = $("#token").val();
    ban = encodeURIComponent(ban);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&search_ban2=1&token=" + token + "&ban=" + ban + "&server=" + server,
        success: function (html) {
            $("#bans_table").hide();
            $("#search_ban_res_full").html(html);
            NProgress.done();
            setTimeout(show_ok, 500);
        },
    });
}
function get_smiles(id, type) {
    if ($(id).attr("data-content") == "empty") {
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/chat_data.php",
            data: "phpaction=1&token=" + token + "&get_smiles=1&type=" + type,
            success: function (html) {
                $(id).attr("data-content", html);
            },
        });
    }
}
function buy_stickers() {
    if (confirm("Вы действительно хотите купить стикер?")) {
        NProgress.start();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_m.php",
            data: "phpaction=1&buy_stickers=1&token=" + token,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    setTimeout(show_ok, 500);
                    $.ajax({
                        type: "POST",
                        url: "../ajax/chat_data.php",
                        data: "phpaction=1&token=" + token + "&get_smiles=1",
                        success: function (html) {
                            $(".popover-content").html(html);
                            $(".popover-body").html(html);
                        },
                    });
                }
                if (result.status == 2) {
                    setTimeout(show_error, 500);
                    if (result.info != "") {
                        alert(result.info);
                    }
                }
            },
        });
    }
}
var selected = "gcms_smiles";
function open_sticker(select) {
    if (selected != select) {
        $("#" + select).addClass("disp-b");
        $("#" + selected).addClass("disp-n");
        $("#" + select).removeClass("disp-n");
        $("#" + selected).removeClass("disp-b");
        selected = select;
    }
}
function thank(id, type) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&thank=1&token=" + token + "&id=" + id + "&type=" + type,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                var div_text = "";
                if (type != 0) {
                    $("#thanks_0").removeClass("disp-n");
                    $("#thanks_0").addClass("disp-b");
                    div_text = $("#thanks_0").text();
                    if (div_text == "") {
                        $("#thanks_0").append('Спасибо сказали: <a href="../profile?id=' + result.idd + '">' + result.login + "</a>&nbsp");
                    } else {
                        $("#thanks_0").append(',&nbsp<a href="../profile?id=' + result.idd + '">' + result.login + "</a>&nbsp");
                    }
                } else {
                    $("#thanks_" + id).removeClass("disp-n");
                    $("#thanks_" + id).addClass("disp-b");
                    div_text = $("#thanks_" + id).text();
                    if (div_text == "") {
                        $("#thanks_" + id).append('Спасибо сказали: <a href="../profile?id=' + result.idd + '">' + result.login + "</a>&nbsp");
                    } else {
                        $("#thanks_" + id).append(',&nbsp<a href="../profile?id=' + result.idd + '">' + result.login + "</a>&nbsp");
                    }
                }
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
            }
        },
    });
}
function answer(id, user, link) {
    var text = $("#text_" + id).html();
    text = '<blockquote><b><a href="' + link + "#answer_" + id + '">' + user + " писал:</a></b><br>" + text + "</blockquote><br>";
    tinymce.activeEditor.insertContent(text);
    document.location.href = "#send_answer";
}
function click_cote() {
    $("#cote").attr("onclick", "");
    $("#cote img").attr("src", "../ajax/sound/cote2.gif");
    play_sound("../ajax/sound/cote.mp3", 0.8);
    setTimeout(function () {
        $("#cote img").attr("src", "../ajax/sound/cote1.gif");
    }, 3100);
    setTimeout(function () {
        $("#cote").attr("onclick", "click_cote();");
    }, 3500);
}
function on_im(val) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&on_im=1&token=" + token + "&val=" + val,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
            }
        },
    });
}
function on_ip_protect(val) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&on_ip_protect=1&token=" + token + "&val=" + val,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
            }
        },
    });
}
function on_email_notice(val) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&on_email_notice=1&token=" + token + "&val=" + val,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
            }
        },
    });
}
function dell_notification(id) {
    NProgress.start();
    var token = $("#token").val();
    id = encodeURIComponent(id);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&dell_notification=1&id=" + id,
        success: function () {
            $("#" + id).fadeOut();
            NProgress.done();
            setTimeout(show_ok, 500);
        },
    });
}
function close_notification(id) {
    var token = $("#token").val();
    id = encodeURIComponent(id);
    if ($("#notifications>div").length == 1) {
        hide_notifications();
    } else {
        $.ajax({
            type: "POST",
            url: "../ajax/actions_a.php",
            data: "phpaction=1&token=" + token + "&close_notification=1&id=" + id,
            success: function () {
                $("#" + id).fadeOut(500);
                setTimeout(function () {
                    dell_block(id);
                }, 600);
            },
        });
    }
}
function hide_notifications() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&close_notifications=1",
        success: function () {
            $("#notifications_line").fadeOut();
            $("#notifications").fadeOut();
        },
    });
}
function dell_notifications() {
    if (confirm("Вы уверены?")) {
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_a.php",
            data: "phpaction=1&token=" + token + "&dell_notifications=1",
            success: function () {
                reset_page();
            },
        });
    }
}
function load_stats(start, server, name, param) {
    if (param != 1) {
        NProgress.start();
    }
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: "phpaction=1&load_stats=1&token=" + token + "&start=" + start + "&server=" + server + "&name=" + name,
        success: function (html) {
            $("#stats").html(html);
            if (param != 1) {
                NProgress.done();
                setTimeout(show_ok, 500);
            }
        },
    });
}
function load_wstats(id, server, authid) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: "phpaction=1&load_wstats=1&token=" + token + "&id=" + id + "&server=" + server + "&authid=" + authid,
        success: function (html) {
            $("#wstats" + id).html(html);
        },
    });
}
function load_mstats(id, server, authid) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: "phpaction=1&load_mstats=1&token=" + token + "&id=" + id + "&server=" + server + "&authid=" + authid,
        success: function (html) {
            $("#mstats" + id).html(html);
        },
    });
}
function hide_profile_box() {
    if ($("#profile_box").hasClass("disp-n")) {
        $("#profile_box").slideDown(400);
        $("#profile_box").removeClass("disp-n");
    } else {
        $("#profile_box").slideUp(400);
        $("#profile_box").addClass("disp-n");
    }
}
function load_muts(start, server, name) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: "phpaction=1&load_muts=1&token=" + token + "&start=" + start + "&server=" + server + "&name=" + name,
        success: function (html) {
            $("#muts").html(html);
        },
    });
}
function close_mute(server, bid) {
    if (confirm("Вы уверены?")) {
        NProgress.start();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_c.php",
            data: "phpaction=1&close_mute=1&token=" + token + "&server=" + server + "&bid=" + bid,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    $("#buy_unmute_btn" + bid).empty();
                    $("#bid" + bid).attr("class", "success");
                    $("#muteinfo" + bid).html('<div class="bs-callout bs-callout-success"><h4>Выполнено!</h4><p>Игрок разбанен!</p></div>');
                }
                if (result.status == 2) {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function buy_unmute(id, server) {
    if (confirm("Вы действительно хотите купить размут?")) {
        NProgress.start();
        var token = $("#token").val();
        id = encodeURIComponent(id);
        server = encodeURIComponent(server);
        $.ajax({
            type: "POST",
            url: "../ajax/actions_m.php",
            data: "phpaction=1&buy_unmute=1&token=" + token + "&id=" + id + "&server=" + server,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    setTimeout(show_ok, 500);
                    $("#buy_unmute_btn" + id).empty();
                    $("#bid" + id).attr("class", "success");
                    $("#muteinfo" + id).html('<div class="bs-callout bs-callout-success"><h4>Поздравляем!</h4><p>Размут успешно куплен!</p></div>');
                }
                if (result.status == 2) {
                    setTimeout(show_error, 500);
                    if (result.info != "" || result.info != undefined) {
                        alert(result.info);
                    }
                }
            },
        });
    }
}
function get_admin_info2(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: "phpaction=1&get_admin_info=1&token=" + token + "&id=" + id,
        success: function (html) {
            $("#admin_info" + id).html(html);
        },
    });
}
function get_user_srotes() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_m.php",
        data: "phpaction=1&get_user_srotes=1&token=" + token,
        success: function (html) {
            $("#my_stores").html(html);
        },
    });
}
function get_stores_info(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_m.php",
        data: "phpaction=1&get_stores_info=1&token=" + token + "&id=" + id,
        success: function (html) {
            $("#store_info" + id).html(html);
        },
    });
}
function edit_store(id, type) {
    NProgress.start();
    var token = $("#token").val();
    var param = "";
    if (type == "type") {
        param = $("#store_type_" + id).val();
    }
    if (type == "name") {
        param = $("#player_name_" + id).val();
        var original = $("#player_name_" + id).val();
    }
    if (type == "pass") {
        param = $("#player_pass_" + id).val();
    }
    param = encodeURIComponent(param);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_m.php",
        data: "phpaction=1&edit_store=1&token=" + token + "&id=" + id + "&param=" + param + "&type=" + type,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                setTimeout(show_ok, 500);
                if (type == "name") {
                    $("#new_name_" + id).html(original);
                }
            } else {
                setTimeout(show_error, 500);
                alert(result.reply);
            }
        },
    });
}
function start_srote(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_m.php",
        data: "phpaction=1&start_srote=1&token=" + token + "&id=" + id,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                reset_page();
            } else {
                if (result.data != "" && result.data != undefined) {
                    alert(result.data);
                }
            }
        },
    });
}
function buy_extend(id, id2) {
    $("#extend_btn" + id).attr("onclick", "");
    NProgress.start();
    var time = $("#extend_time" + id).val();
    var token = $("#token").val();
    time = encodeURIComponent(time);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_m.php",
        data: "phpaction=1&buy_extend=1&token=" + token + "&id=" + id + "&time=" + time,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                NProgress.done();
                setTimeout(show_ok, 500);
                $("#balance").html(result.shilings);
                get_stores_info(id2);
            } else {
                if (result.data != "" && result.data != undefined) {
                    alert(result.data);
                }
                $("#extend_btn" + id).attr("onclick", "buy_extend(" + id + ");");
                NProgress.done();
                setTimeout(show_error, 500);
            }
        },
    });
}
function get_return(id) {
    if (confirm("Вы уверены? Деньги будут возвращены на баланс, а услуга удалена.")) {
        $("#return" + id).attr("onclick", "");
        NProgress.start();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_m.php",
            data: "phpaction=1&get_return=1&token=" + token + "&id=" + id,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    setTimeout(show_ok, 500);
                    $("#" + id).fadeOut();
                    if (result.id != 0) {
                        $("#admin" + result.id).fadeOut();
                        $("#store_modal" + result.id).modal("hide");
                    }
                    $("#balance").html(result.shilings);
                } else {
                    $("#return" + id).attr("onclick", "get_return(" + id + ");");
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function show_tarifs(id) {
    if ($("#extend_block" + id).is(":visible")) {
        $("#extend_block" + id).fadeOut();
    } else {
        $("#extend_block" + id).fadeIn();
    }
}
function load_servers_admins() {
    var token = $("#token").val();
    var id = $("#store_server").val();
    id = encodeURIComponent(id);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&load_servers_admins=1&token=" + token + "&id=" + id,
        success: function (html) {
            $("#admins").html(html);
        },
    });
}
function get_admin_info(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&get_admin_info=1&token=" + token + "&id=" + id,
        success: function (html) {
            $("#admin_info" + id).html(html);
        },
    });
}
function edit_admin(id, type) {
    NProgress.start();
    var data = {};
    data["edit_admin"] = "1";
    data["type"] = type;
    data["id"] = id;
    if (type == "type") {
        data["param"] = $("#store_type_" + id).val();
    }
    if (type == "name") {
        data["param"] = $("#player_name_" + id).val();
    }
    if (type == "pass") {
        data["param"] = $("#player_pass_" + id).val();
    }
    if (type == "user_id") {
        data["param"] = $("#player_user_id_" + id).val();
    }
    if (type == "comment") {
        data["param"] = $("#player_comment_" + id).val();
    }
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                setTimeout(show_ok, 500);
                if (type == "user_id" || type == "name") {
                    load_edit_admin_result(id);
                }
            } else {
                setTimeout(show_error, 500);
                alert(result.reply);
            }
        },
    });
}
function load_edit_admin_result(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&load_edit_admin_result=1&token=" + token + "&id=" + id,
        dataType: "json",
        success: function (result) {
            $("#new_name_" + id).html(result.name);
            $("#new_user_" + id).html(result.user);
            $("#new_services_" + id).html(result.services);
        },
    });
}
function stop_adm(id, cause = null, link = null) {
    if (empty(cause)) {
        cause = prompt("Причина (обязательно)", "");
    }
    if (!empty(cause)) {
        if (empty(link)) {
            link = prompt("Ссылка на доказательства (не обязательно)", "");
        }
        let price = "";
        do {
            price = prompt("Стоимость разблокировки (обязательно)", "100");
        } while (empty(price));
        if (empty(price)) {
            price = 100;
        }
        let data = {};
        data["stop_adm"] = true;
        data["id"] = id;
        data["cause"] = cause;
        data["link"] = link;
        data["price"] = price;
        $.ajax({
            type: "POST",
            url: "../ajax/actions_z.php",
            data: create_material(data),
            dataType: "json",
            success: function (result) {
                if (result.status === 1) {
                    $("#admin" + id).addClass("danger");
                    setTimeout(function () {
                        get_admin_info(id);
                    }, 500);
                } else {
                    if (result.data !== "" && result.data !== undefined) {
                        alert(result.data);
                    }
                }
            },
        });
    } else {
        NProgress.done();
        setTimeout(show_error, 500);
    }
}
function start_adm(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&start_adm=1&token=" + token + "&id=" + id,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                setTimeout(function () {
                    $("#admin" + id).removeClass("danger");
                    get_admin_info(id);
                }, 500);
            } else {
                if (result.data != "" && result.data != undefined) {
                    alert(result.data);
                }
            }
        },
    });
}
function pause_admin(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&pause_admin=1&token=" + token + "&id=" + id,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                setTimeout(function () {
                    $("#admin" + id).addClass("warning");
                    get_admin_info(id);
                }, 500);
            } else {
                if (result.data != "" && result.data != undefined) {
                    alert(result.data);
                }
            }
        },
    });
}
function resume_admin(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&resume_admin=1&token=" + token + "&id=" + id,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                setTimeout(function () {
                    $("#admin" + id).removeClass("warning");
                    get_admin_info(id);
                }, 500);
            } else {
                if (result.data != "" && result.data != undefined) {
                    alert(result.data);
                }
            }
        },
    });
}
function add_admin(check1, check2) {
    $("#store_buy_btn").attr("onclick", "");
    var data = {};
    data["add_admin"] = "1";
    data["server"] = $("#store_server").val();
    data["service"] = $("#store_services").val();
    data["tarifs"] = $("#store_tarifs").val();
    data["type"] = $("#store_type").val();
    data["nick"] = $("#player_nick").val();
    data["pass"] = $("#player_pass").val();
    data["steam_id"] = $("#player_steam_id").val();
    data["user_id"] = $("#player_user_id").val();
    data["check1"] = check1;
    data["check2"] = check2;
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                setTimeout(show_error, 500);
                $("#add_result").html("<p class='text-success'>" + result.data + "</p>");
                reset_admin_adding(1);
                load_servers_admins();
            }
            if (result.status == 2) {
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply, null);
                reset_admin_adding(1);
            }
            if (result.status == 3) {
                setTimeout(show_error, 500);
                $("#add_result").html("<p class='text-danger'>" + result.data + "</p>");
                reset_admin_adding(1);
            }
            if (result.status == 4) {
                $("#add_result").html("<p class='text-danger'>На сервере уже имеется услуга, прикрепленная к данному игровому аккаунту. Желаете совместить услуги?</p>");
                $("#store_answer_btn").removeClass("disp-n");
                $("#store_answer_btn").attr("onclick", "reset_admin_adding();");
                $("#store_buy_btn").html("Да");
                $("#store_buy_btn").attr("onclick", "add_admin(1,0);");
            }
            if (result.status == 5) {
                $("#add_result").html('<p class="text-danger">Предложено изменить группу пользователя на "' + result.group + '". Принять предложение?</p>');
                $("#store_answer_btn").removeClass("disp-n");
                $("#store_answer_btn").attr("onclick", "add_admin(1,2);");
                $("#store_buy_btn").html("Да");
                $("#store_buy_btn").attr("onclick", "add_admin(1,1);");
            }
        },
    });
}
function reset_admin_adding(type) {
    if (type != 1) {
        $("#add_result").empty();
    }
    $("#store_answer_btn").addClass("disp-n");
    $("#store_answer_btn").attr("onclick", "");
    $("#store_buy_btn").attr("onclick", "add_admin();");
    $("#store_buy_btn").html("Выдать");
}
function dell_admin(id, isConfirm = false) {
    if (isConfirm === false) {
        isConfirm = confirm("Вы уверены?");
    }
    if (isConfirm) {
        NProgress.start();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_z.php",
            data: "phpaction=1&dell_admin=1&token=" + token + "&id=" + id,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    setTimeout(show_ok, 500);
                    $("#admin" + id).fadeOut();
                    $("#admin_modal" + id).modal("hide");
                } else {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function change_admin_days(id, id2) {
    NProgress.start();
    var date = $("#date_end" + id).val();
    var token = $("#token").val();
    date = encodeURIComponent(date);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&change_admin_days=1&token=" + token + "&id=" + id + "&date=" + date,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                NProgress.done();
                setTimeout(show_ok, 500);
                get_admin_info(id2);
                $("#ui-datepicker-div").fadeOut();
            } else {
                NProgress.done();
                setTimeout(show_error, 500);
            }
        },
    });
}
function change_admin_flags(id, id2, type) {
    NProgress.start();
    var token = $("#token").val();
    var flags = "";
    if (type == "none") {
        flags = "none";
    } else {
        flags = $("#service_flags" + id).val();
        flags = encodeURIComponent(flags);
    }
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&change_admin_flags=1&token=" + token + "&id=" + id + "&flags=" + flags,
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                NProgress.done();
                setTimeout(show_ok, 500);
                if (type == "none") {
                    get_admin_info(id2);
                }
            } else {
                NProgress.done();
                setTimeout(show_error, 500);
            }
        },
    });
}
function dell_admin_service(id) {
    if (confirm("Вы уверены?")) {
        NProgress.start();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "../ajax/actions_z.php",
            data: "phpaction=1&dell_admin_service=1&token=" + token + "&id=" + id,
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    setTimeout(show_ok, 500);
                    $("#" + id).fadeOut();
                    if (result.dell == 1) {
                        $("#admin" + result.id).fadeOut();
                        $("#admin_modal" + result.id).modal("hide");
                    } else {
                        load_edit_admin_result(result.id);
                    }
                } else {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function set_admin_date_forever(id, block) {
    block = block || "#date_end" + id;
    $(block).val("00.00.0000 00:00");
}
function get_services_adm(id) {
    var data = {};
    data["get_services"] = "1";
    data["id"] = id;
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                $("#store_services").html(result.data);
                get_tarifs_adm(result.service);
            }
        },
    });
}
function get_tarifs_adm(id) {
    var data = {};
    data["get_tarifs"] = "1";
    data["id"] = id;
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            if (result.status == 1) {
                $("#store_tarifs").html(result.data);
            }
        },
    });
}
function add_service_to_admin(id) {
    var name = $("#player_name_" + id).val();
    var pass = $("#player_pass_" + id).val();
    var type = $("#store_type_" + id).val();
    var user_id = $("#player_user_id_" + id).val();
    $("#player_type").val(type);
    show_input_success("type", null, 1000);
    if (user_id != "" && user_id != undefined) {
        $("#player_user_id").val(user_id);
        show_input_success("player_user_id", null, 1000);
    } else {
        show_input_error("player_user_id", null, 1000);
    }
    change_store_bind_type(type);
    $("#store_type").val(type);
    show_input_success("store_type", null, 1000);
    if (type == 1) {
        $("#player_nick").val(name);
        $("#player_pass").val(pass);
        show_input_success("player_nick", null, 1000);
        show_input_success("player_pass", null, 1000);
    }
    if (type == 2) {
        $("#player_steam_id").val(name);
        show_input_success("player_steam_id", null, 1000);
    }
    if (type == 3) {
        $("#player_steam_id").val(name);
        $("#player_pass").val(pass);
        show_input_success("player_steam_id", null, 1000);
        show_input_success("player_pass", null, 1000);
    }
    $("#" + id).fadeOut();
    $("html, body")
        .stop()
        .animate({ scrollTop: $("#add_admin_area").offset().top }, 1500, "easeInOutExpo");
}
function get_referrals() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&get_referrals=1",
        success: function (html) {
            $("#referrals_body").html(html);
        },
    });
}
function get_ref_profit() {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&get_ref_profit=1",
        success: function (html) {
            $("#profit_body").html(html);
        },
    });
}
function get_user_shilings_operations(id) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: "phpaction=1&get_user_shilings_operations=1&token=" + token + "&id=" + id + "&type=2",
        success: function (html) {
            $("#operations").html(html);
        },
    });
}
function doCommandOnPlayer(commandId, params, codedNick, serverId) {
    NProgress.start();
    let data = {};
    data["doCommandOnPlayer"] = true;
    data["commandId"] = commandId;
    data["nick"] = codedNick;
    params.forEach(function (item) {
        if (item.name !== "nick") {
            data[item.name] = prompt(item.title, "");
        }
    });
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            NProgress.done();
            alert(result.data);
            if (result.status === 1) {
                get_players(serverId);
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
            }
        },
    });
}
function doRconCommandOnPlayer(commandId, params, codedNick, serverId) {
    let data = {};
    data["nick"] = codedNick;
    params = JSON.parse(params);
    params.forEach(function (item) {
        if (item.name !== "nick") {
            data[item.name] = prompt(item.title, "");
        }
    });
    doRconCommand(commandId, data, (result) => {
        alert(result.data);
        get_players(serverId);
    });
}
function doRconCommandOnServer(commandId, params, serverId) {
    let data = {};
    params = JSON.parse(params);
    params.forEach(function (item) {
        data[item.name] = prompt(item.title, "");
    });
    doRconCommand(commandId, data, (result) => {
        $("#server-management-command-sending-result" + serverId).html(result.data);
        $("#server-management-command-sending-result" + serverId).fadeIn();
    });
}
function doRconCommand(commandId, params = {}, onEndCallback = () => {}) {
    NProgress.start();
    let data = params;
    data["doRconCommand"] = true;
    data["commandId"] = commandId;
    $.ajax({
        type: "POST",
        url: "../ajax/actions_z.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status === 1) {
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
            }
            onEndCallback(result);
        },
    });
}
function dell_event(id) {
    NProgress.start();
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_b.php",
        data: "phpaction=1&token=" + token + "&dell_event=1&id=" + id,
        success: function () {
            $("#event" + id).fadeOut();
            NProgress.done();
            setTimeout(show_ok, 500);
        },
    });
}
function init_tinymce(id, type, skin, file_manager, code) {
    if (skin != "oxide" && skin != "oxide-dark") {
        skin = "oxide";
    }
    if (type != "lite" && type != "full" && type != "forum") {
        type = "lite";
    }
    if (file_manager != "responsivefilemanager") {
        file_manager = "";
    }
    var plugins = "";
    var menubar = true;
    if (type == "lite") {
        plugins = "link image charmap print preview hr anchor pagebreak" + " visualchars fullscreen insertdatetime media nonbreaking save table " + "directionality paste textpattern " + file_manager;
        toolbar = "undo redo removeformat | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify " + "| bullist numlist | outdent indent blockquote | link unlink anchor " + "| image media " + file_manager;
        menubar = false;
    }
    if (type == "full") {
        plugins =
            "lists link image charmap print preview hr anchor pagebreak searchreplace " +
            "visualblocks visualchars fullscreen insertdatetime media nonbreaking save table " +
            "directionality paste textpattern codesample spoiler " +
            file_manager;
        toolbar =
            "undo redo removeformat | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify " +
            "| fontsizeselect | searchreplace | bullist numlist | outdent indent blockquote | link unlink anchor " +
            "| image media " +
            file_manager +
            " | codesample spoiler-add | insertdatetime | forecolor backcolor " +
            "| hr | subscript superscript | charmap | fullscreen | ltr rtl";
        menubar = true;
    }
    if (type == "forum") {
        plugins =
            "lists link image charmap print preview hr anchor pagebreak searchreplace " +
            "visualblocks visualchars fullscreen insertdatetime media nonbreaking save table " +
            "directionality paste textpattern codesample spoiler " +
            file_manager;
        toolbar =
            "undo redo removeformat | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify " +
            "| fontsizeselect | searchreplace | bullist numlist | outdent indent blockquote | link unlink anchor " +
            "| image media " +
            file_manager +
            " | codesample spoiler-add | insertdatetime | forecolor backcolor " +
            "| hr | subscript superscript | charmap | fullscreen | ltr rtl";
        menubar = true;
    }
    if (typeof tinymce != "undefined") {
        if (file_manager == "") {
            tinymce.init({
                selector: "#" + id,
                height: 300,
                language: "ru",
                plugins: [plugins],
                paste_convert_headers_to_strong: true,
                paste_strip_class_attributes: "all",
                paste_remove_spans: true,
                paste_remove_styles: true,
                toolbar1: toolbar,
                image_advtab: true,
                menubar: menubar,
                skin: skin,
                toolbar_items_size: "small",
            });
        } else {
            tinymce.init({
                selector: "#" + id,
                height: 300,
                language: "ru",
                plugins: [plugins],
                paste_convert_headers_to_strong: true,
                paste_strip_class_attributes: "all",
                paste_remove_spans: true,
                paste_remove_styles: true,
                toolbar1: toolbar,
                image_advtab: true,
                menubar: menubar,
                skin: skin,
                toolbar_items_size: "small",
                external_filemanager_path: "../modules/editors/tinymce/filemanager/",
                filemanager_title: "Файловый менеджер",
                external_plugins: { filemanager: "filemanager/plugin.min.js" },
                filemanager_access_key: code + "*user",
            });
        }
    }
}
function section_access(type, id) {
    id = id || "";
    if (type == "all") {
        $("#access" + id + ">label").removeClass("active");
        $("#access" + id + ">label>input").removeAttr("checked");
    } else {
        $("#access" + id + ">#access_all" + id).removeClass("active");
        $("#access" + id + ">#access_all" + id + ">input").removeAttr("checked");
    }
}
function activate_voucher() {
    var token = $("#token").val();
    var voucher_key = $("#voucher_key").val();
    voucher_key = encodeURIComponent(voucher_key);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_m.php",
        data: "phpaction=1&token=" + token + "&activate_voucher=1&voucher_key=" + voucher_key,
        success: function (html) {
            $("#activate_result").html(html);
        },
    });
}
function get_vk_profile_info(vk_api, img_area, name_area, vk) {
    if (vk_api == 0) {
        vk_api = vk.substr(2);
    }
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/fast_actions.php",
        data: "phpaction=1&get_vk_profile_info=1&token=" + token + "&vk_api=" + vk_api,
        dataType: "json",
        success: function (result) {
            if (result.avatar == "none") {
                $(img_area).remove();
                $(name_area).html(vk_api);
            } else {
                $(img_area).attr("src", result.avatar);
                $(img_area).attr("alt", result.first_name + " " + result.last_name);
                $(name_area).html(result.first_name + " " + result.last_name);
            }
        },
    });
}
function get_user_steam_info(steam_api) {
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/fast_actions.php",
        data: "phpaction=1&get_user_steam_info=1&token=" + token + "&steam_api=" + steam_api,
        dataType: "json",
        success: function (result) {
            $("#steam_user img").attr("src", result.avatar);
            $("#steam_user img").attr("alt", result.login);
            $("#steam_user span").html(result.login);
        },
    });
}
function get_fb_profile_info(fb_api, fb_id, link_area, img_area, name_area) {
    if (fb_api == 0) {
        fb_api = fb_id;
    }
    var token = $("#token").val();
    $.ajax({
        type: "POST",
        url: "../ajax/fast_actions.php",
        data: "phpaction=1&get_fb_profile_info=1&token=" + token + "&fb_api=" + fb_api,
        dataType: "json",
        success: function (result) {
            if (result.login == "none") {
                $(img_area).remove();
                $(name_area).html(fb_api);
            } else {
                $(img_area).attr("src", "https://graph.facebook.com/" + fb_api + "/picture?type=large");
                $(img_area).attr("alt", result.login);
                $(name_area).html(result.login);
            }
            if (fb_id != "0") {
                $(link_area).attr("href", "https://www.facebook.com/profile.php?id=" + fb_id);
            }
        },
    });
}
function search_mute(server) {
    var name = $("#search_mute").val();
    load_muts(0, server, name);
    if (name == "") {
        $("#pagination2").show();
    } else {
        $("#pagination2").hide();
    }
}
function search_stats(server) {
    var name = $("#search_stats").val();
    load_stats(0, server, name);
    if (name == "") {
        $("#pagination2").show();
    } else {
        $("#pagination2").hide();
    }
}
function search_ban(server) {
    var name = $("#search_ban").val();
    load_banlist(0, server, name);
    if (name == "") {
        $("#pagination2").show();
    } else {
        $("#pagination2").hide();
    }
}
function change_ban_end(id, server) {
    NProgress.start();
    var data = {};
    data["change_ban_end"] = "1";
    data["bid"] = id;
    data["server"] = server;
    data["date"] = $("#ban_end_input" + id).val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_c.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                setTimeout(show_ok, 500);
                $("#ban_length" + id).html(result.length);
                $("#ban_length_full" + id).html(result.length);
                $("#ban_length_full" + id).attr("class", "text-" + result.class);
                $("#ban_end" + id).html(result.ends);
                $("#bid" + id).attr("class", result.class);
                $("#ban_end" + id).html(result.ends);
                if (result.disp == 1) {
                    $("#unban_btns" + id).fadeIn();
                    $("#unban_btn" + id).fadeIn();
                } else {
                    $("#unban_btns" + id).fadeOut();
                    $("#unban_btn" + id).fadeOut();
                }
                $("#buy_unban_btn" + id).fadeOut();
                $("#ban_closed" + id).fadeOut();
                $("#ui-datepicker-div").fadeOut();
            } else {
                setTimeout(show_error, 500);
            }
        },
    });
}
function change_mute_end(id, server) {
    NProgress.start();
    var data = {};
    data["change_mute_end"] = "1";
    data["bid"] = id;
    data["server"] = server;
    data["date"] = $("#mute_end_input" + id).val();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_c.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status == 1) {
                setTimeout(show_ok, 500);
                $("#mute_length" + id).html(result.length);
                $("#mute_length_full" + id).html(result.length);
                $("#mute_length_full" + id).attr("class", "text-" + result.class);
                $("#mute_end" + id).html(result.ends);
                $("#bid" + id).attr("class", result.class);
                $("#mute_end" + id).html(result.ends);
                if (result.disp == 1) {
                    $("#unmute_btns" + id).fadeIn();
                    $("#unmute_btn" + id).fadeIn();
                } else {
                    $("#unmute_btns" + id).fadeOut();
                    $("#unmute_btn" + id).fadeOut();
                }
                $("#buy_unmute_btn" + id).fadeOut();
                $("#mute_closed" + id).fadeOut();
                $("#ui-datepicker-div").fadeOut();
            } else {
                setTimeout(show_error, 500);
            }
        },
    });
}
function dell_user_stats(id, server) {
    if (confirm("Вы уверены?")) {
        NProgress.start();
        var data = {};
        data["dell_user_stats"] = "1";
        data["id"] = id;
        data["server"] = server;
        $.ajax({
            type: "POST",
            url: "../ajax/actions_c.php",
            data: create_material(data),
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status == 1) {
                    setTimeout(show_ok, 500);
                    $("#modal" + id).modal("hide");
                    $("#modal" + id).fadeOut();
                } else {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function set_current_time() {
    $("#publish_date").datetimepicker("setDate", new Date());
    $("#ui-datepicker-div").fadeOut();
}
function admin_change_prefix(id) {
    NProgress.start();
    var prefix = $("#user_prefix").val();
    change_value("users", "prefix", prefix, id);
    NProgress.done();
    setTimeout(show_ok, 500);
}
function edit_user_prefix() {
    var token = $("#token").val();
    var user_prefix = $("#user_prefix").val();
    user_prefix = encodeURIComponent(user_prefix);
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: "phpaction=1&token=" + token + "&edit_user_prefix=1&user_prefix=" + user_prefix,
        success: function (html) {
            $("#edit_user_prefix_result").empty();
            $("#edit_user_prefix_result").append(html);
        },
    });
}
function addToBlackList(userId, onSuccess = function () {}) {
    let data = {};
    data["addToBlackList"] = true;
    data["userId"] = userId;
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            alert(result.message);
            if (result.status === 1) {
                onSuccess();
            }
        },
    });
}
function removeFromBlackList(userId, onSuccess = function () {}) {
    let data = {};
    data["removeFromBlackList"] = true;
    data["userId"] = userId;
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            alert(result.message);
            if (result.status === 1) {
                onSuccess();
            }
        },
    });
}
function getBlackList() {
    let data = {};
    data["getBlackList"] = true;
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: create_material(data),
        success: function (result) {
            $("#black-list-content").html(result);
        },
    });
}
function findTheAccused() {
    let data = {};
    data["findTheAccused"] = true;
    data["server_id"] = $("#server_id").val();
    data["accused"] = $("#accused").val();
    $("#accused-info").fadeOut();
    $("#additional-info").fadeOut();
    $("#find-result-table").fadeIn();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: create_material(data),
        success: function (result) {
            $("#find-result").html(result);
        },
    });
}
function setTheAccused(id) {
    let data = {};
    data["setTheAccused"] = true;
    data["adminId"] = id;
    $("#find-result-table").fadeOut();
    $("#additional-info").fadeIn();
    $("#accused-info").fadeIn();
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: create_material(data),
        success: function (result) {
            $("#accused-info").html(result);
        },
    });
}
function addComplaint(btn) {
    NProgress.start();
    let data = {};
    data["addComplaint"] = true;
    data["accusedId"] = $("#accused-id").val();
    data["screens"] = $("#images-load-result-value").val();
    data["demo"] = $("#demo").val();
    data["description"] = $.trim(tinymce.get("description").getContent());
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status === 1) {
                $(btn).attr("onclick", "");
                setTimeout(show_ok, 500);
                setTimeout(function () {
                    document.location.href = "complaint?id=" + result.id;
                }, 2000);
            } else {
                $("#result").html("");
                if (result.input === "none") {
                    $("#result").html('<p class="text-danger">' + result.reply + "</p>");
                } else {
                    show_input_error(result.input, result.reply);
                }
                setTimeout(show_error, 500);
            }
        },
    });
}
function closeComplaint(id, adminId) {
    let sentence = $("#sentence").val();
    if (sentence == 1 || sentence == 2 || sentence == 3 || sentence == 4) {
        if (!confirm("Вы уверены?")) {
            return;
        }
        let data = {};
        data["closeComplaint"] = true;
        data["sentence"] = sentence;
        data["id"] = id;
        $.ajax({
            type: "POST",
            url: "../ajax/actions_c.php",
            data: create_material(data),
            dataType: "json",
            success: function (result) {
                if (sentence == 3) {
                    dell_admin(adminId, true);
                }
                if (sentence == 4) {
                    stop_adm(adminId, "Жалоба", window.location.href);
                }
                if (result.status === 1) {
                    setTimeout(show_ok, 500);
                    alert(result.answer);
                    reset_page();
                } else {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
    if (sentence == 5) {
        removeComplaint(id);
    }
}
function removeComplaint(id) {
    if (confirm("Вы уверены?")) {
        NProgress.start();
        let data = {};
        data["removeComplaint"] = true;
        data["id"] = id;
        $.ajax({
            type: "POST",
            url: "../ajax/actions_c.php",
            data: create_material(data),
            dataType: "json",
            success: function (result) {
                NProgress.done();
                if (result.status === 1) {
                    setTimeout(show_ok, 500);
                    location.href = "index";
                } else {
                    setTimeout(show_error, 500);
                }
            },
        });
    }
}
function loadComplaintComments(id) {
    let data = {};
    data["loadComplaintComments"] = true;
    data["id"] = id;
    $.ajax({
        type: "POST",
        url: "../ajax/actions.php",
        data: create_material(data),
        success: function (html) {
            $("#comments").html(html);
        },
    });
}
function sendComplaintComment(id) {
    NProgress.start();
    let data = {};
    data["sendComplaintComment"] = true;
    data["id"] = id;
    data["text"] = $.trim(tinymce.get("text").getContent());
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status === 1) {
                clean_tiny("text");
                stop_button("#send_btn", 1000);
                loadComplaintComments(id);
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply, null);
            }
        },
    });
}
function removeComplaintComment(id) {
    NProgress.start();
    let data = {};
    data["removeComplaintComment"] = true;
    data["id"] = id;
    $.ajax({
        type: "POST",
        url: "../ajax/actions_b.php",
        data: create_material(data),
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status === 1) {
                $("#message_id_" + id).fadeOut();
                setTimeout(show_ok, 500);
            }
            if (result.status === 2) {
                setTimeout(show_error, 500);
            }
        },
    });
}
function loadImages(folder) {
    NProgress.start();
    let data = {};
    data["loadImages"] = true;
    data["counter"] = $("#image-loader-counter").val();
    data["image"] = $("#image")[0].files[0];
    data["folder"] = folder;
    $.ajax({
        type: "POST",
        url: "../ajax/actions_a.php",
        data: create_material(data, 1),
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (result) {
            NProgress.done();
            if (result.status === 1) {
                $("#imgs").append('<a class="thumbnail" data-lightbox="1" href="../' + result.image + '">' + '<img class="thumbnail-img" src="../' + result.image + '" />' + "</a>");
                let resultValues = $("#images-load-result-value");
                resultValues.val(resultValues.val() + result.image + ";");
                let counter = $("#counter");
                counter.val(counter.val() + 1);
                setTimeout(show_ok, 500);
            } else {
                $("#load-image-result").html('<p class="text-danger">' + result.content + "</p>");
                setTimeout(show_error, 500);
            }
        },
    });
}
function edit_user_status() {
    var message = $("#ti_status").html();
    $("#status_user").html("<div id='edit-status-message'><input id='new_status' value='" + message + "' class='form-control'><i id='save-button' onclick='save_user_status();' class='fas fa-check text-primary'></i></div>");
}
function save_user_status() {
    var form_data = new FormData();
    form_data.append("save_user_status", "1");
    form_data.append("message", $("#new_status").val());
    send_post(get_url() + "ajax/actions_a.php", form_data, function (result) {
        if (result.alert == "success") {
            $("#status_user").html("<small id='ti_status' style='position: unset; float:left; cursor:pointer;' onclick='edit_user_status();'>" + result.message + "</small>");
        }
    });
}
function getTermPrefixes() {
    var form_data = new FormData();
    form_data.append("getTermPrefixes", "1");
    form_data.append("id_server", $("#serv option:selected").val());
    send_post(get_url() + "ajax/actions_a.php", form_data, function (result) {
        $("#term").html(result.message);
    });
}
function buyPrefix() {
    var form_data = new FormData();
    form_data.append("buyPrefix", "1");
    form_data.append("id_server", $("#serv option:selected").val());
    form_data.append("id_term", $("#term option:selected").val());
    form_data.append("type_bind", $("#binding option:selected").val());
    form_data.append("nickname", $("#player_nick").val());
    form_data.append("steamid", $("#player_steam_id").val());
    form_data.append("password", $("#player_pass").val());
    form_data.append("prefix", $("#player_prefix").val());
    send_post(get_url() + "ajax/actions_a.php", form_data, function (result) {
        if (result.alert == "error") {
            $("#buy_result").html(result.message);
        } else {
            $("#buy_area").html(result);
        }
    });
}
