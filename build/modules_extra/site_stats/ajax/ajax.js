function get_site_stats(type){
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/site_stats/ajax/actions.php",
		data: "phpaction=1&site_stats=1&token="+token+"&type="+type,

		success: function(html) {
			$("#site_stats").html(html);
		}
	});
}