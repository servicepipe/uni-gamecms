function select_server() {
	var token = $("#token").val();
	var server_id = $("#skins_server").val();
	
	$.ajax({
		type: "POST",
		url: "../../../modules_extra/skins_store/ajax/actions.php",
		data: "phpactions=1&token=" + token + "&load_skins_list=1&server_id=" + server_id,
		dataType: "json",
		success: function(result) {
			if(result.status == 1) {
				$("#skins_list").html(result.body);
			}
		}
	});
}

function select_skin() {
	var token = $("#token").val();
	var skin_id = $("#skins").val();
	
	$.ajax({
		type: "POST",
		url: "../../../modules_extra/skins_store/ajax/actions.php",
		data: "phpactions=1&token=" + token + "&load_skin_info=1&skin_id=" + skin_id,
		dataType: "json",
		success: function(result) {
			if(result.status == 1) {
				$("#skins_info").html(result.info);
				$("#buy").removeAttr("disabled");
				$("#buy").text("Купить за " + result.price + " руб.");
			}
		}
	});
}

function buy() {
	var token = $("#token").val();
	var skin_id = $("#skins").val();
	var nickname = $("#buy_nick").val();
	var password = $("#password").val();
	
	$.ajax({
		type: "POST",
		url: "../../../modules_extra/skins_store/ajax/actions.php",
		data: "phpactions=1&token=" + token + "&buy=1&skin_id=" + skin_id + "&nickname=" + nickname + "&password=" + password,
		dataType: "json",
		success: function(result) {
			$("#result_buy").html("<div class='alert alert-" + result.type + " alert-dismissible fade show' role='alert'>" + result.message + "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
		}
	});
}