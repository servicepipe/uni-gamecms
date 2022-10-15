function get_sortition() {
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/sortition/ajax/actions.php",
		data: "phpaction=1&get_sortition=1&token="+token,

		success: function(html) {
			$("#sortition").html(html);
		}
	});
}
function get_sortition_lite() {
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/sortition/ajax/actions.php",
		data: "phpaction=1&get_sortition_lite=1&token="+token,

		success: function(html) {
			$("#sortition").html(html);
		}
	});
}
function get_ending_time(type) {
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/sortition/ajax/actions.php",
		data: "phpaction=1&get_ending_time=1&token="+token,

		success: function(html) {
			$("#ending").html(html);
			if(type == 1) {
				window.sortition_interval = setInterval( function() { get_ending_time(2); }, 60000);
			}
		}
	});
}
function get_prizes() {
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/sortition/ajax/actions.php",
		data: "phpaction=1&get_prizes=1&token="+token,

		success: function(html) {
			$("#prizes").html(html);
		}
	});
}
function get_participants() {
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/sortition/ajax/actions.php",
		data: "phpaction=1&get_participants=1&token="+token,

		success: function(html) {
			$("#participants").html(html);
		}
	});
}
function get_winners() {
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/sortition/ajax/actions.php",
		data: "phpaction=1&get_winners=1&token="+token,

		success: function(html) {
			$("#winners").html(html);
		}
	});
}
function participate() {
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/sortition/ajax/actions.php",
		data: "phpaction=1&participate=1&token="+token,

		success: function(html) {
			$("#participate_result").html(html);
		}
	});
}


function get_prize_line(place){
	var token = $('#token').val();
	var prize_count = $('#prize_count_'+place).val();
	prize_count = encodeURIComponent(prize_count);

	var prize_type = $('#prize_type_'+place).val();
	prize_type = encodeURIComponent(prize_type);
	$.ajax({
		type: "POST",
		url: "../modules_extra/sortition/ajax/actions.php",
		data: "phpaction=1&get_prize_line=1&token="+token+"&prize_count="+prize_count+"&prize_type="+prize_type+"&place="+place,

		success: function(html) {
			$("#prizes_"+place).append(html);
		}
	});
}
function get_prizes_adm(){
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/sortition/ajax/actions.php",
		data: "phpaction=1&get_prizes_adm=1&token="+token,

		success: function(html) {
			$("#prizes").html(html);
		}
	});
}
function get_services_prize(place, id, type){
	var token = $('#token').val();
	var server = $('#server'+place+'_'+id).val();
	server = encodeURIComponent(server);
	$.ajax({
		type: "POST",
		url: "../modules_extra/sortition/ajax/actions.php",
		data: "phpaction=1&get_services_prize=1&token="+token+"&server="+server+"&type="+type,

		success: function(html) {
			$("#service"+place+"_"+id).html(html);

			setTimeout(function() {
				get_tarifs_prize(place, id, type);
			}, 500);
		}
	});
}
function get_tarifs_prize(place, id, type){
	var token = $('#token').val();
	var service = $('#service'+place+'_'+id).val();
	service = encodeURIComponent(service);
	$.ajax({
		type: "POST",
		url: "../modules_extra/sortition/ajax/actions.php",
		data: "phpaction=1&get_tarifs_prize=1&token="+token+"&service="+service+"&type="+type,

		success: function(html) {
			$("#tarif"+place+"_"+id).html(html);
		}
	});
}
function get_services_prize2(place, id){
	var token = $('#token').val();
	var server = $('#server'+place+'_'+id).val();
	server = encodeURIComponent(server);
	$.ajax({
		type: "POST",
		url: "../modules_extra/sortition/ajax/actions.php",
		data: "phpaction=1&get_services_prize2=1&token="+token+"&server="+server,

		success: function(html) {
			$("#tarif"+place+"_"+id).html(html);
		}
	});
}
function dell_prize_line(place, id) {
	$('#prize_line_'+place+'_'+id).remove();
	$('#prizetype_'+place+'_'+id).remove();
}
function dell_place(place) {
	$('#prizes_div_'+place).remove();
}
function load_participants_list(type){
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/sortition/ajax/actions.php",
		data: "phpaction=1&load_participants_list=1&token="+token+"&type="+type,

		success: function(html) {
			if(type == 1) {
				var block = 'participants_list';
			} else {
				var block = 'winner';
			}
			$("#"+block).html(html);
		}
	});
}
function dell_participant(id){
	if(confirm('Вы уверены?')){
		var token = $('#token').val();
		$.ajax({
			type: "POST",
			url: "../modules_extra/sortition/ajax/actions.php",
			data: "phpaction=1&dell_participant=1&token="+token+"&id="+id,

			success: function(html) {
				$('#participant'+id).fadeOut();
			}
		});
	}
}
function save_sortition(type){
	NProgress.start();

	var data = {}
	data['save_sortition'] = '1';
	data['type'] = type;
	data['name'] = $('#name').val();
	data['price'] = $('#price').val();
	data['participants'] = $('#participants').val();
	data['show_participants'] = $('#show_participants').val();
	data['how_old'] = $('#how_old').val();
	data['ending'] = $('#ending').val();
	data['own_prize'] = $('#own_prize').val();
	data['text'] = $.trim(tinymce.get("text").getContent());
	data['end_type'] = $('#end_type').val();
	data['count_of_winners'] = $('#count_of_winners').val();
	var prize = $('#prizes').serialize();
	$.ajax({
		type: "POST",
		url: "../modules_extra/sortition/ajax/actions.php",
		data: create_material(data)+"&"+prize,
		dataType: "json",

		success: function(result) {
			NProgress.done();
			if(result.status == 1){
				setTimeout(show_ok, 500);
				reset_page();
			} else {
				setTimeout(show_error, 500);
				if(result.reply != undefined) {
					show_input_error(result.input,result.reply,null);
				}
			}
		}
	});
}
function dell_sortition(){
	if(confirm('Вы уверены?')){
		var token = $('#token').val();
		$.ajax({
			type: "POST",
			url: "../modules_extra/sortition/ajax/actions.php",
			data: "phpaction=1&dell_sortition=1&token="+token,

			success: function(html) {
				reset_page();
			}
		});
	}
}