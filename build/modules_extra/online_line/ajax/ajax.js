function get_servers(){
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/online_line/ajax/actions.php",
		data: "phpaction=1&get_servers=1&token="+token+"&type=1",

		success: function(html) {
			$("#servers").html(html);
		}
	});
}
function get_servers2(){
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/online_line/ajax/actions.php",
		data: "phpaction=1&get_servers=1&token="+token+"&type=2",

		success: function(html) {
			$("#servers").html(html);
		}
	});
}