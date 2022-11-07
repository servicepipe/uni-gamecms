function load_demos(start, server, map) {
    var token = $('#token').val();
    $.ajax({
        type: "POST",
        url: "../modules_extra/demos/ajax/actions.php",
        data: "phpaction=1&load_demos=1&token=" + token + "&start=" + start + "&server=" + server + "&map=" + map,

        success: function (html) {
            $("#demos").html(html);
        }
    });
}

function search_demo(server) {
    var map = $('#search_demo').val();
    load_demos(0, server, map);
    if (map === '') {
        $('#pagination1').show();
        $('#pagination2').show();
    } else {
        $('#pagination1').hide();
        $('#pagination2').hide();
    }
}

function demos_load_servers() {
    var token = $('#token').val();
    $.ajax({
        type: "POST",
        url: "../modules_extra/demos/ajax/actions.php",
        data: "phpaction=1&load_servers=1&token=" + token,

        success: function (html) {
            $("#servers").html(html);
        }
    });
}

function selectWorkMethod(serverId) {
    let method = $('#work_method' + serverId).val();

    $('#work-methods-' + serverId + ' > div').each(function () {
        $(this).fadeOut(0);
    });

    $('#work-methods-' + serverId + ' > div[data-work-method-' + method + ']').each(function () {
        $(this).fadeIn(0);
    });
}

function demos_edit_server(id, clean) {
    NProgress.start();

    var data = {};
    data['edit_server'] = 1;
    data['server_id'] = id;
    data['clean'] = clean;
    data['url'] = $('#url' + id).val();
    data['shelf_life'] = $('#shelf_life' + id).val();
    data['work_method'] = $('#work_method' + id).val();
    data['swu_key'] = $('#swu_key' + id).val();
    data['hltv_url'] = $('#hltv_url' + id).val();
    data['db_host'] = $('#db_host' + id).val();
    data['db_user'] = $('#db_user' + id).val();
    data['db_pass'] = $('#db_pass' + id).val();
    data['db_db'] = $('#db_db' + id).val();
    data['db_table'] = $('#db_table' + id).val();
    data['db_code'] = $('#db_code' + id).val();
    data['ftp_host'] = $('#ftp_host' + id).val();
    data['ftp_login'] = $('#ftp_login' + id).val();
    data['ftp_pass'] = $('#ftp_pass' + id).val();
    data['ftp_port'] = $('#ftp_port' + id).val();
    data['ftp_string'] = $('#ftp_string' + id).val();

    $.ajax({
        type: "POST",
        url: "../modules_extra/demos/ajax/actions.php",
        data: create_material(data),
        dataType: "json",

        success: function (result) {
            NProgress.done();

            if (result.status == 1) {
                setTimeout(show_ok, 500);
            }
            if (result.status == 2) {
                if (result.data != '' && result.data != undefined) {
                    show_input_error(result.input + id, result.data, null);
                }
                if (result.alert != '' && result.alert != undefined) {
                    alert(result.alert);
                }
                setTimeout(show_error, 500);
            }

            if (clean == 1) {
                $('#url' + id).val('');
                $('#shelf_life' + id).val('');
                $('#hltv_url' + id).val('');
                $('#swu_key' + id).val('');
                $('#db_host' + id).val('');
                $('#db_user' + id).val('');
                $('#db_pass' + id).val('');
                $('#db_db' + id).val('');
                $('#db_table' + id).val('');
                $('#db_code' + id).val(0);
                $('#ftp_host' + id).val('');
                $('#ftp_login' + id).val('');
                $('#ftp_pass' + id).val('');
                $('#ftp_port' + id).val('');
                $('#ftp_string' + id).val('');
            }
        }
    });
}