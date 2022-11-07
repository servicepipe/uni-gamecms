function add_skin() {
	NProgress.start();
	var fd = new FormData();
	
	fd.append("phpactions", "1");
	fd.append("add_skin", "1");
	fd.append("token", $("#token").val());
	fd.append("name", $("#name").val());
	fd.append("price", $("#price").val());
	fd.append("server", $("#server").val());
	fd.append("model_ct", $("#model_ct").val());
	fd.append("model_t", $("#model_t").val());
	fd.append("image", $('#image').prop('files')[0]);
	
	$("#add").attr("disabled", "disabled");
	
	$.ajax({
		type: "POST",
		url: "../../../modules_extra/skins_store/ajax/actions_admin.php",
		processData: false,
		contentType: false,
		data: fd,
		dataType: "json",
		success: function(result) {
			NProgress.done();
			$("#add").removeAttr("disabled");
			
			if(result.status == 1) {
				window.location.reload();
				$("#result_add_skin").html("<span class=\"text-success\">" + result.message + "</span>");
			}
			else {
				$("#result_add_skin").html("<span class=\"text-danger\">" + result.message + "</span>");
			}
		}
	});
}

function del(index) {
	var token = $("#token").val();
	
	$.ajax({
		type: "POST",
		url: "../../../modules_extra/skins_store/ajax/actions_admin.php",
		data: "phpactions=1&token=" + token + "&del=1&index=" + index,
		dataType: "json",
		success: function(result) {
			if(result.status == 1) {
				setTimeout(function() {
					window.location.reload();
				}, 500);
			}
		}
	});
}