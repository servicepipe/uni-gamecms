$uri_actions = "/modules_extra/progression/performers/actions/main.php";

$(function() {
	if($('.progression').length > 0) {
		send_post($uri_actions, serializeform(new FormData, {
			GetProgressive: 1,
			uid: $(".progression").data("index")
		}), (result) => {
			$(".progression").html(result);
			
			if($('.progress[data-position]').length > 0) {
				var position = $(".progress[data-position]").data("position");
				$(".progress[data-position]").css("width", position + "%");
			}
		});
	}
});