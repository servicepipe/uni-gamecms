$(document).ready(function(){
	setTimeout(function(){$('#show_modal_viewer').modal('show');}, 1000);

    var token = $('#token').val();
    $.ajax({
        type: "POST",
        url: "../modules_extra/modal_viewer/ajax/actions.php",
        data: "phpaction=1&load_modal_viewer=1&token=" + token,
		cache: false,
        success: function (html) {
            $("#modal_viewer").html(html);
        }
    });
});

function Editor_modal(pid) {
	var texts = $("#_texts" + pid).val();
	$("#title" + pid).html("<input id=\"_title" + pid + "\" type=\"text\" class=\"form-control\" value=\"" + $("#title" + pid).html() + "\">");
	$("#texts" + pid).html("<input id=\"_texts" + pid + "\" type=\"text\" class=\"form-control\" value=\"" + $("#texts" + pid).html() + "\">");
	
	$("#Editor_modal" + pid).removeAttr("onclick");
	$("#onIcon" + pid).toggleClass("glyphicon-edit glyphicon-floppy-save");
	
	$("#Editor_modal" + pid).unbind("click");
	$("#Editor_modal" + pid).bind("click", function() {
		Save_modal(pid);
	});
}

$uri = url() + "modules_extra/modal_viewer/ajax/actions.php";

function Save_modal(pid) {
	var title = $("#_title" + pid).val();
	var texts = $("#_texts" + pid).val();
	
	send_post($uri, serializeform(new FormData, {
		edit_modal: 1,
		pid: pid,
		title: title,
		texts: texts,
	}), (result) => {
		if(result.alert == 'success') {
			$("#title" + pid).html(title);
			$("#texts" + pid).html(texts);
			
			$("#onIcon" + pid).toggleClass("glyphicon-floppy-save glyphicon-edit");
			$("#Editor_modal" + pid).unbind("click");
			$("#Editor_modal" + pid).bind("click", function() {
				Editor_modal(pid);
			});
			
			return;
		}
		
		push(result.message, result.alert);
	});
}

function remove_modal(index) {
 
    if (confirm("Вы действительно хотите удалить?")) {
        var form_data = new FormData();
        form_data.append("phpaction", "1");
        form_data.append("token", $("#token").val());
        form_data.append("remove_modal", "1");
        form_data.append("pid", index);
        $.ajax({
            type: "POST",
            url: "../modules_extra/modal_viewer/ajax/actions.php",
            processData: false,
            contentType: false,
            data: form_data,
            dataType: "json",
            success: function (result) {               
				window.location.reload();
            },
        });
    }
}