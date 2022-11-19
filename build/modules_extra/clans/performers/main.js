$uri_noty_clans = "/modules_extra/clans/performers/actions/main.php";

function ChangeStatus(cid) {
	send_post($uri_noty_clans, serializeform(new FormData, {ChangeStatus: 1, message: $("#status").val(), cid: cid}), (result) => {
		if(result.alert == 'success') {
			location.reload();
		}
		else {
			push(result.message, result.alert);
		}
	});
}

function ChangeRole(uid, cid, id) {
	var group = $("#groups" + id).val();
	
	if(group == '1') {
		$.confirm({
			title: 'Смена главы Клана',
			content: 'Вы действительно хотите сменить Главу клана?',
			type: 'blue',
			typeAnimated: true,
			buttons: {
				confirm: {
					text: 'Да',
					btnClass: 'btn-blue',
					action: function() {
						send_post($uri_noty_clans, serializeform(new FormData, {
							ChangeRole: 1, group: group, uid: uid, cid: cid, id: id
						}), (result) => {
							$("#list_role").html(result);
						});
					}
				},
				close: {
					text: 'Отмена'
				}
			}
		});
	}
	else {
		send_post($uri_noty_clans, serializeform(new FormData, {
			ChangeRole: 1, group: group, uid: uid, cid: cid, id: id
		}), (result) => {
			$("#list_role").html(result);
		});
	}
}

function accept(id, cid) {
	send_post($uri_noty_clans, serializeform(new FormData, {
		accept: 1, id: id, cid: cid
	}), (result) => {
		if(result.alert) {
			push(result.message, result.alert);
		}
		else {
			$("#list_applications").html(result);
		}
	});
}

function deny(id, cid) {
	send_post($uri_noty_clans, serializeform(new FormData, {
		deny: 1, id: id, cid: cid
	}), (result) => {
		if(result.alert) {
			push(result.message, result.alert);
		}
		else {
			$("#list_applications").html(result);
		}
	});
}

$(function() {
	preimage("bg");
	
	$("svg[data-like]").click(function() {
		var uid =  $(this).data('like');
		
		$.confirm({
			title: 'Поднятие рейтинга',
			content: 'Вы действительно хотите поставить Лайк этому Пользователю?',
			type: 'blue',
			typeAnimated: true,
			buttons: {
				confirm: {
					text: 'Да',
					btnClass: 'btn-blue',
					action: function() {
						send_post($uri_noty_clans, serializeform(new FormData, {GiveLike: 1, uid: uid}), (result) => {
							switch(result.alert) {
								case 'info': {
									setTimeout("location.reload();", 400);
									break;
								}
								case 'success': {
									setTimeout("location.reload();", 400);
									break;
								}
							}
							
							push(result.message, result.alert);
						});
					}
				},
				close: {
					text: 'Отмена'
				}
			}
		});
	});
	
	$(".shop").click(function() {
		send_post($uri_noty_clans, serializeform(new FormData, {BuyItem: 1, id: $(this).data('id')}), (result) => {
			if(result.alert) {
				push(result.message, result.alert);
			}
		});
	});
	
	$("button[data-target='#role']").click(function() {
		send_post($uri_noty_clans, serializeform(new FormData, {
			roles: 1, cid: $(this).data('cid')
		}), (result) => {
			if(result.alert) {
				push(result.message, result.alert);
			}
			else {
				$("#list_role").html(result);
			}
		});
	});
	
	$("button[data-target='#applications']").click(function() {
		send_post($uri_noty_clans, serializeform(new FormData, {
			applications: 1, cid: $(this).data('cid')
		}), (result) => {
			$("#list_applications").html(result);
		});
	});
	
	$("#form_clan_create").submit(function(e) {
		e.preventDefault();
		
		send_post($uri_noty_clans, serializeform(new FormData(this), {Create: 1}), (result) => {
			if(result.alert == 'success') {
				location.href = '/clans?id=' + result.id;
			}
			else {
				push(result.message, result.alert);
			}
		});
	});
	
	$("#form_change_logotype").submit(function(e) {
		e.preventDefault();
		
		send_post($uri_noty_clans, serializeform(new FormData(this), {ChangeLogotype: 1}), (result) => {
			if(result.alert == 'success') {
				location.reload();
			}
			else {
				push(result.message, result.alert);
			}
		});
	});
	
	$("#form_change_cover").submit(function(e) {
		e.preventDefault();
		
		send_post($uri_noty_clans, serializeform(new FormData(this), {ChangeCover: 1}), (result) => {
			if(result.alert == 'success') {
				location.reload();
			}
			else {
				push(result.message, result.alert);
			}
		});
	});
	
	$("button[data-clan]").on('click', function() {
		var ClanId = $(this).data('clan');
		
		send_post($uri_noty_clans, serializeform(new FormData, {OnButtonClan: 1, ClanId: ClanId}), (result) => {
			if(result.alert == 'success') {
				setTimeout('location.reload();', 500);
			}
			else {
				push(result.message, result.alert);
			}
		});
	});
	
	$(".monitoring-table").remove();
	$(".footer").remove();
});

function IsValidMessage(text) {
	if(!text || text == '') {
		return false;
	}
	
	return true;
}