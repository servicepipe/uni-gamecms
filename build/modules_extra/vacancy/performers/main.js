$uri_vacancy = "/modules_extra/vacancy/performers/actions/main.php";

$(function() {
	$("div[data-vacancy]").bind('click', function() {
		href('/vacancy/index?id=' + $(this).data('vacancy'));
	});
	
	$("select[name='server']").bind('change', function() {
		send_post($uri_vacancy, serializeform(new FormData, {
			GetVacancies: 1,
			sid: $("select[name='server']").val()
		}), (result) => {
			$("select[name='vacancy']").html(result.content);
			$("#custom").html(result.custom);
		});
	});
	
	$("#form_vacancy_message").submit(function(e) {
		e.preventDefault();
		
		if(!IsValidMessage($("textarea[name='message']").val())) {
			push("Сначала введите текст.", "warning");
			return;
		}
		
		send_post($uri_vacancy, serializeform(new FormData(this), {
			SendMessage: 1
		}), (result) => {
			if(result.alert == 'warning' || result.alert == 'error') {
				push(result.message, result.alert);
				return;
			}
			
			$("textarea[name='message']").val("");
			$(".messages").html(result.content);
		});
	});
	
	$("#form_vacancy_send").submit(function(e) {
		e.preventDefault();
		$("button[type='submit']").prop("disabled", true);
		$varBlock = true;
		
		send_post($uri_vacancy, serializeform(new FormData(this), {
			addVacansy: 1
		}), (result) => {
			if(result.alert == 'success') {
				return href("/vacancy/index?id=" + result.id);
			}
			
			push(result.message, result.alert);
			$("button[type='submit']").prop("disabled", false);
			$varBlock = false;
		});
	});
	
	$("#readyrules").bind('change', function() {
		if(!$varBlock) {
			$("button[type='submit']").prop("disabled", this.checked ? false : true);
		}
	});
	
	$("button[data-vacancy-success]").bind('click', function() {
		send_post($uri_vacancy, serializeform(new FormData, {
			VacancySuccess: 1,
			vid: $(this).data('vacancy-success')
		}), (result) => {
			if(result.alert == 'success') {
				return location.reload();
			}
			
			push(result.message, result.alert);
		});
	});
	
	$("button[data-vacancy-rejection]").bind('click', function() {
		$.confirm({
			title: 'Причина отказа',
			content: '<input type="text" placeholder="Причина отказа" class="reason form-control" required>',
			buttons: {
				close: {
					text: 'Отмена'
				},
				success: {
					text: 'Отказать',
					btnClass: 'btn-danger',
					action: function() {
						var reason = this.$content.find('.reason').val();
						
						if(IsValidMessage(reason)) {
							send_post($uri_vacancy, serializeform(new FormData, {
								VacancyRejection: 1,
								vid: $("button[data-vacancy-rejection]").data('vacancy-rejection'),
								reason: reason
							}), (result) => {
								if(result.alert == 'success') {
									return location.reload();
								}
								
								push(result.message, result.alert);
							});
							
							return;
						}
						
						push("Вы не указали причину Отказа.", "warning");
					}
				}
			}
		});
	});
	
	$(".monitoring-table").remove();
	$(".footer").remove();
});

$varBlock = false;

function IsValidMessage(text) {
	if(!text || text == '') {
		return false;
	}
	
	return true;
}