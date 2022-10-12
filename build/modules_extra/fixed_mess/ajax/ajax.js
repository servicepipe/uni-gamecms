function chat_load_fixed_message() {
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/fixed_mess/ajax/actions.php",
		data: "phpaction=1&token=" + token + "&chat_load_fixed_message=1",
		success: function (html) {
			if (Number(html) != 2) {
				$('#fixed_message').html(html);
			}
		}
	});
}
function fixed_chat_message(id, key) {
	NProgress.start();
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/fixed_mess/ajax/actions.php",
		data: "phpaction=1&token=" + token + "&fixed_chat_message=1&id="+ id +"&key="+ key,
		dataType:"json",
		success:function(result){
			if(result.status==0){
				NProgress.done();
				setTimeout(show_error, 500);
				console.log('У вас нет прав!');
			}
			if(result.status==1){
				chat_load_fixed_message();
				NProgress.done();
				setTimeout(show_ok, 500);
			}
			if(result.status==2){
				$("#message_id_" + id).fadeOut();
				NProgress.done();
				setTimeout(show_ok, 500);
			}
		}
	});
}
