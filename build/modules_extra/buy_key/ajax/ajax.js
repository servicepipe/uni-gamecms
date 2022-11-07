function bk_load_data(func, block){
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/buy_key/ajax/actions.php",
		data: "phpaction=1&"+func+"=1&token="+token,

		success: function(html) {
			$("#"+block).html(html);
		}
	});
}
function bk_edit_server(id, clean){
	NProgress.start();
	var token = $('#token').val();
	var data = $('#serv_'+id).serialize();
	$.ajax({
		type: "POST",
		url: "../modules_extra/buy_key/ajax/actions.php",
		data: "phpaction=1&edit_server=1&token="+token+"&id="+id+"&clean="+clean+"&"+data,

		success: function(html) {
			NProgress.done();
			$("#edit_serv_result"+id).html(html);
			if(clean == 1) {
				$('#serv_'+id).trigger('reset');
			}
		}
	});
}
function bk_load_services(type, block, code){
	if(type == 2) {
		if(typeof tinymce != "undefined") {
			tinymce.remove();
		}
		init_tinymce('text', code, 'lite');
	}
	var token = $('#token').val();
	var id = $('#server').val();
	id = encodeURIComponent(id);
	$.ajax({
		type: "POST",
		url: "../modules_extra/buy_key/ajax/actions.php",
		data: "phpaction=1&load_services=1&token="+token+"&id="+id+"&type="+type,

		success: function(html) {
			$("#"+block).html(html);
		}
	});
}
function bk_add_service(){
	NProgress.start();
	var token = $('#token').val();
	var data = $('#service').serialize();
	var text = tinymce.get("text").getContent();
	text = $.trim(text);
	var server = $('#server').val();
	server = encodeURIComponent(server);
	$.ajax({
		type: "POST",
		url: "../modules_extra/buy_key/ajax/actions.php",
		data: "phpaction=1&add_service=1&token="+token+"&text="+text+"&server="+server+"&"+data,
		dataType: "json",

		success: function(result) {
			if(result.status == 1){
				NProgress.done();
				setTimeout(show_ok, 500);
				bk_load_services(1, 'services');
				bk_load_services(2, 'all_services');
			} else {
				NProgress.done();
				setTimeout(show_error, 500); 
				show_input_error(result.input,result.reply,null);
			}
		}
	});
}
function bk_edit_service(id){
	NProgress.start();
	var token = $('#token').val();
	var data = $('#form_service'+id).serialize();
	var text = tinymce.get("text"+id).getContent();
	text = $.trim(text);
	var server = $('#server').val();
	server = encodeURIComponent(server);
	$.ajax({
		type: "POST",
		url: "../modules_extra/buy_key/ajax/actions.php",
		data: "phpaction=1&edit_service=1&token="+token+"&id="+id+"&text="+text+"&server="+server+"&"+data,
		dataType: "json",

		success: function(result) {
			if(result.status == 1){
				NProgress.done();
				setTimeout(show_ok, 500);
				bk_load_services(1, 'services');
			} else {
				NProgress.done();
				setTimeout(show_error, 500); 
				show_input_error(result.input,result.reply,null);
			}
		}
	});
}
function bk_dell_service(id){
	NProgress.start();
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/buy_key/ajax/actions.php",
		data: "phpaction=1&dell_service=1&token="+token+"&id="+id,

		success: function(html) {
			$("#service"+id).fadeOut();
			bk_load_services(1, 'services');

			NProgress.done();
			setTimeout(show_ok, 500);
		}
	});
}
function bk_up_service(id){
	NProgress.start();
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/buy_key/ajax/actions.php",
		data: "phpaction=1&up_service=1&token="+token+"&id="+id,
		dataType: "json",

		success: function(result) {
			NProgress.done();
			if(result.status == 1){
				bk_load_services(2, 'all_services');
				setTimeout(show_ok, 500); 
			} else {
				setTimeout(show_error, 500); 
			}
		}
	});
}
function bk_down_service(id){
	NProgress.start();
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/buy_key/ajax/actions.php",
		data: "phpaction=1&down_service=1&token="+token+"&id="+id,
		dataType: "json",

		success: function(result) {
			NProgress.done();
			if(result.status == 1){
				bk_load_services(2, 'all_services');
				setTimeout(show_ok, 500); 
			} else {
				setTimeout(show_error, 500); 
			}
		}
	});
}
function bk_add_tarif(){
	NProgress.start();
	var token = $('#token').val();
	var service = $('#services').val();
	var time = $('#time').val();
	var price = $('#price').val();
	service = encodeURIComponent(service);
	time = encodeURIComponent(time);
	price = encodeURIComponent(price);
	$.ajax({
		type: "POST",
		url: "../modules_extra/buy_key/ajax/actions.php",
		data: "phpaction=1&add_tarif=1&token="+token+"&service="+service+"&time="+time+"&price="+price,
		dataType: "json",

		success: function(result) {
			if(result.status == 1){
				NProgress.done();
				setTimeout(show_ok, 500);
				bk_load_services(2, 'all_services');
			} else {
				NProgress.done();
				setTimeout(show_error, 500); 
				show_input_error(result.input,result.reply,null);
			}
		}
	});
}
function bk_edit_tarif(id){
	NProgress.start();
	var token = $('#token').val();
	var time = $('#time'+id).val();
	var price = $('#price'+id).val();
	time = encodeURIComponent(time);
	price = encodeURIComponent(price);
	$.ajax({
		type: "POST",
		url: "../modules_extra/buy_key/ajax/actions.php",
		data: "phpaction=1&edit_tarif=1&token="+token+"&time="+time+"&price="+price+"&id="+id,
		dataType: "json",

		success: function(result) {
			if(result.status == 1){
				NProgress.done();
				setTimeout(show_ok, 500); 
			} else {
				NProgress.done();
				setTimeout(show_error, 500); 
				show_input_error(result.input+id,result.reply,null);
			}
		}
	});
}
function bk_dell_tarif(id){
	NProgress.start();
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/buy_key/ajax/actions.php",
		data: "phpaction=1&dell_tarif=1&token="+token+"&id="+id,

		success: function(html) {
			$("#tarif"+id).fadeOut();
			NProgress.done();
			setTimeout(show_ok, 500);
		}
	});
}


function bk_get_services(id){
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/buy_key/ajax/actions.php",
		data: "phpaction=1&get_services=1&token="+token+"&id="+id,
		dataType: "json",

		success: function(result) {
			if(result.status == 1){
				$("#store_services").html(result.data);
			}
		}
	});
}
function bk_get_tarifs(id){
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/buy_key/ajax/actions.php",
		data: "phpaction=1&get_tarifs=1&token="+token+"&id="+id,
		dataType: "json",

		success: function(result) {
			if(result.status == 1){
				$("#store_tarifs").html(result.data);
				$("#store_service_info").html(result.text);
			}
		}
	});
}
function buy_key(){
	NProgress.start();
	var token = $('#token').val();
	var server = $('#store_server').val();
	var service = $('#store_services').val();
	var tarif = $('#store_tarifs').val();
	server = encodeURIComponent(server);
	service = encodeURIComponent(service);
	tarif = encodeURIComponent(tarif);

	$.ajax({
		type: "POST",
		url: "../modules_extra/buy_key/ajax/actions.php",
		data: "phpaction=1&buy_key=1&token="+token+"&server="+server+"&service="+service+"&tarif="+tarif,
		dataType: "json",
		success: function(result) {
			NProgress.done();
			if(result.status == 1){
				setTimeout(show_ok, 500);
				if(result.info != ''){
					$("#buy_result").html('<div class="bs-callout bs-callout-success transition_h_2"><p>'+result.info+'</p></div>');
				}
			}
			if(result.status == 2){
				setTimeout(show_error, 500);
				if(result.info != ''){
					$("#buy_result").html('<p class="danger">'+result.info+'</p>');
				}
			}
			if(result.status == 3){
				setTimeout(show_ok, 500); 
				$("#buy_service_area").html('<div class="bs-callout bs-callout-success transition_h_2">'+result.info+'</div>');
				$("#balance").html(result.shilings);
			}
		}
	});
}
function bk_on_buying(){
	var status = $('#store_checbox').attr("data-status");
	if(status == '2'){
		$('#store_checbox').attr("data-status", "1");
		$('#store_buy_btn').removeClass('disabled');
		$('#store_buy_btn').attr('onclick', 'buy_key();');
	} else {
		$('#store_checbox').attr("data-status", "2");
		$('#store_buy_btn').addClass('disabled');
		$('#store_buy_btn').attr('onclick', '');
	}
}