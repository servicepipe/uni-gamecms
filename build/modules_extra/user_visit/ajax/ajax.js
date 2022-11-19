function user_visit(id){
	var token=$('#token').val();
	$.ajax({
		type:"POST",
		url: "../modules_extra/user_visit/ajax/actions.php",
		data:"phpaction=1&user_visit=1&token="+token+"&id="+id,
		success:function(html){
			$("#users_visit").append(html);
		}
	});
}

function get_user_visit(id){
	var token=$('#token').val();
	$.ajax({
		type:"POST",
		url: "../modules_extra/user_visit/ajax/actions.php",
		data:"phpaction=1&get_user_visit=1&token="+token+"&id="+id,
		success:function(html){
			$("#users_visit").html(html);
		}
	});
}