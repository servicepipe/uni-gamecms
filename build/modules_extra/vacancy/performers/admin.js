$uri_vacancy_admin = "/modules_extra/vacancy/performers/actions/admin.php";

$(function() {
	$("select[name='server']").bind('change', function() {
		let sid = $("select[name='server']").val();
		
		send_post($uri_vacancy_admin, serializeform(new FormData, {
			GetDataList: 1,
			sid: sid
		}), (result) => {
			if(result.alert == 'success') {
				GetDataList(sid);
				return;
			}
			
			push(result.message, result.alert);
		});
	});
	

});

function SendConfigs(key, value) {
	send_post($uri_vacancy_admin, serializeform(new FormData, {
		SetConfigs: 1,
		key: key,
		value: value
	}), (result) => {
		push(result.message, result.alert);
	});
}

function GetDataList(sid) {
	send_post($uri_vacancy_admin, serializeform(new FormData, {
		GetDataList: 1,
		sid: sid
	}), (result) => {
		if(result.alert == 'success') {
			$("#fields").html(result.fields);
			$("#vacancy").html(result.vacancy);
			ReBindEvents();
			
			return;
		}
		
		push(result.message, result.alert);
	});
}

function IsValidMessage(text) {
	if(!text || text == '') {
		return false;
	}
	
	return true;
}

function ReBindEvents() {
	$("button[data-vacancy]").bind('click', function() {
		send_post($uri_vacancy_admin, serializeform(new FormData, {
			RemoveVacancy: 1,
			vid: $(this).data('vacancy'),
			sid: $("select[name='server']").val()
		}), (result) => {
			if(result.alert) {
				push(result.message, result.alert);
				return;
			}
		
			$("#vacancy").html(result.content);
			ReBindEvents();
		});
	});
			
	$("#form_add_vacancy").submit(function(e) {
		e.preventDefault();
		$("#b_vacancy").prop("disabled", true);
		
		send_post($uri_vacancy_admin, serializeform(new FormData(this), {
			addVacancy: 1,
			sid: $("select[name='server']").val()
		}), (result) => {
			$("#b_vacancy").prop("disabled", false);
		
			if(result.alert) {
				push(result.message, result.alert);
				return;
			}
		
			$("#vacancy").html(result.content);
			ReBindEvents();
		});
	});
	
	$("button[data-field]").bind('click', function() {
		send_post($uri_vacancy_admin, serializeform(new FormData, {
			RemoveField: 1,
			fid: $(this).data('field'),
			sid: $("select[name='server']").val()
		}), (result) => {
			if(result.alert) {
				push(result.message, result.alert);
				return;
			}
			
			$("#fields").html(result.content);
			ReBindEvents();
		});
	});
	
	$("#form_add_field").submit(function(e) {
		e.preventDefault();
		$("#b_field").prop("disabled", true);
		
		send_post($uri_vacancy_admin, serializeform(new FormData(this), {
			addField: 1,
			sid: $("select[name='server']").val()
		}), (result) => {
			$("#b_field").prop("disabled", false);
		
			if(result.alert) {
				push(result.message, result.alert);
				return;
			}
		
			$("#fields").html(result.content);
			ReBindEvents();
		});
	});
}