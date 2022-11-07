function sk_load_data(func, block){
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/shop_key/ajax/actions.php",
		data: "phpaction=1&"+func+"=1&token="+token,

		success: function(html) {
			$("#"+block).html(html);
		}
	});
}
function sk_edit_server(id, clean){
	NProgress.start();
	var token = $('#token').val();
	var data = $('#serv_'+id).serialize();
	$.ajax({
		type: "POST",
		url: "../modules_extra/shop_key/ajax/actions.php",
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
function sk_load_tarifs(type){
	var token = $('#token').val();
	var id = $('#server').val();
	id = encodeURIComponent(id);
	$.ajax({
		type: "POST",
		url: "../modules_extra/shop_key/ajax/actions.php",
		data: "phpaction=1&load_tarifs=1&token="+token+"&id="+id+"&type="+type,

		success: function(html) {
			$("#tarifs"+type).html(html);
		}
	});
}
function sk_add_tarif(){
	NProgress.start();
	var token = $('#token').val();
	var server = $('#server').val();
	var number = $('#number').val();
	var price = $('#price').val();
	var type = $('#type').val();
	server = encodeURIComponent(server);
	number = encodeURIComponent(number);
	price = encodeURIComponent(price);
	type = encodeURIComponent(type);
	$.ajax({
		type: "POST",
		url: "../modules_extra/shop_key/ajax/actions.php",
		data: "phpaction=1&add_tarif=1&token="+token+"&server="+server+"&number="+number+"&price="+price+"&type="+type,
		dataType: "json",

		success: function(result) {
			if(result.status == 1){
				NProgress.done();
				setTimeout(show_ok, 500);
				sk_load_tarifs(type)
			} else {
				NProgress.done();
				setTimeout(show_error, 500); 
				show_input_error(result.input,result.reply,null);
			}
		}
	});
}
function sk_edit_tarif(id){
	NProgress.start();
	var token = $('#token').val();
	var number = $('#number'+id).val();
	var price = $('#price'+id).val();
	number = encodeURIComponent(number);
	price = encodeURIComponent(price);
	$.ajax({
		type: "POST",
		url: "../modules_extra/shop_key/ajax/actions.php",
		data: "phpaction=1&edit_tarif=1&token="+token+"&number="+number+"&price="+price+"&id="+id,
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
function sk_dell_tarif(id){
	NProgress.start();
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/shop_key/ajax/actions.php",
		data: "phpaction=1&dell_tarif=1&token="+token+"&id="+id,

		success: function(html) {
			$("#tarif"+id).fadeOut();
			NProgress.done();
			setTimeout(show_ok, 500);
		}
	});
}


function sk_get_services(id){
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/shop_key/ajax/actions.php",
		data: "phpaction=1&get_services=1&token="+token+"&id="+id,
		dataType: "json",

		success: function(result) {
			if(result.status == 1){
				$("#store_services").html(result.data);
			}
		}
	});
}
function sk_get_tarifs(type){
	var token = $('#token').val();
	var server = $("#store_server").val();
	id = encodeURIComponent(server);
	type = encodeURIComponent(type);
	$.ajax({
		type: "POST",
		url: "../modules_extra/shop_key/ajax/actions.php",
		data: "phpaction=1&get_tarifs=1&token="+token+"&id="+id+"&type="+type,
		dataType: "json",

		success: function(result) {
			if(result.status == 1){
				$("#store_tarifs").html(result.data);
			}
		}
	});
}
function shop_key(){
	NProgress.start();
	var token = $('#token').val();
	var server = $('#store_server').val();
	var tarif = $('#store_tarifs').val();
	server = encodeURIComponent(server);
	tarif = encodeURIComponent(tarif);

	$.ajax({
		type: "POST",
		url: "../modules_extra/shop_key/ajax/actions.php",
		data: "phpaction=1&shop_key=1&token="+token+"&server="+server+"&tarif="+tarif,
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
					$("#buy_result").html('<p class="text-danger">'+result.info+'</p>');
				}
			}
			if(result.status == 3){
				setTimeout(show_ok, 500); 
				$("#buy_service_area").html('<div class="bs-callout bs-callout-success transition_h_2">'+result.info+'</div>');
				$("#balance").html(result.shilings);
			}
			if(result.status == 4){
				show_stub();
			}
		}
	});
}