function get_subjects(case_id){
	case_id = case_id || 0;

	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&get_subjects=1&token="+token+"&case="+case_id,

		success: function(html) {
			$('#subjects_'+case_id).html(html);
			calculate_chance_sum(case_id);
		}
	});
}
function calculate_chance_sum(case_id) {
	case_id = case_id || 0;
	var sum = 0;

	$('#subjects_'+case_id+' > div').each(function(){
		sum = sum + Number($(this).find('[id ^= "chance_"]').val());
	});

	$('#chance_sum_'+case_id).html(sum+'%');
	if(sum > 100) {
		$('#chance_sum_'+case_id).attr("class", "text-danger");
	} else if(sum == 100) {
		$('#chance_sum_'+case_id).attr("class", "text-success");
	} else {
		$('#chance_sum_'+case_id).attr("class", "");
	}
}
function dell_subject(case_id, place) {
	case_id = case_id || 0;

	$('#subjects_'+case_id+' #subject_'+place).remove();
	calculate_chance_sum(case_id);
}
function get_subject_line(case_id, place){
	case_id = case_id || 0;

	var token = $('#token').val();
	var count = $('#subjects_'+case_id+' #count_'+place).val();
	var type = $('#subjects_'+case_id+' #type_'+place).val();
	count = encodeURIComponent(count);
	type = encodeURIComponent(type);
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&get_subject_line=1&token="+token+"&count="+count+"&type="+type+"&place="+place+"&case_id="+case_id,

		success: function(html) {
			$('#subjects_'+case_id+' #services_'+place).append(html);
		}
	});
}
function dell_subject_line(case_id, place, id) {
	case_id = case_id || 0;

	$('#subjects_'+case_id+' #subject_line_'+place+'_'+id).remove();
	$('#subjects_'+case_id+' #subject_type_'+place+'_'+id).remove();
}

function get_services_subject(case_id, place, id, type){
	case_id = case_id || 0;

	var token = $('#token').val();
	var server = $('#subjects_'+case_id+' #server'+place+'_'+id).val();
	server = encodeURIComponent(server);
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&get_services_subject=1&token="+token+"&server="+server+"&type="+type+"&case_id="+case_id,

		success: function(html) {
			$('#subjects_'+case_id+' #service'+place+'_'+id).html(html);

			setTimeout(function() {
				get_tarifs_subject(case_id, place, id, type);
			}, 500);
		}
	});
}
function get_tarifs_subject(case_id, place, id, type){
	case_id = case_id || 0;

	var token = $('#token').val();
	var service = $('#subjects_'+case_id+' #service'+place+'_'+id).val();
	service = encodeURIComponent(service);
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&get_tarifs_subject=1&token="+token+"&service="+service+"&type="+type+"&case_id="+case_id,

		success: function(html) {
			$('#subjects_'+case_id+' #tarif'+place+'_'+id).html(html);
		}
	});
}
function get_services_subject2(case_id, place, id){
	case_id = case_id || 0;

	var token = $('#token').val();
	var server = $('#subjects_'+case_id+' #server'+place+'_'+id).val();
	server = encodeURIComponent(server);
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&get_services_subject2=1&token="+token+"&server="+server,

		success: function(html) {
			$('#subjects_'+case_id+' #tarif'+place+'_'+id).html(html);
		}
	});
}
function save_case(case_id){
	NProgress.start();

	var data = {}
	case_id = case_id || 0;
	data['save_case'] = '1';
	data['case_id'] = case_id;
	data['name'] = $('#name_'+case_id).val();
	data['price'] = $('#price_'+case_id).val();
	data['image'] = $('#image_'+case_id).val();
	var subjects = $('#subjects_'+case_id).serialize();
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: create_material(data)+"&"+subjects,
		dataType: "json",

		success: function(result) {
			NProgress.done();
			if(result.status == 1){
				setTimeout(show_ok, 500);
				get_cases();
			} else if(result.status == 2){
				setTimeout(show_error, 500);
				if(result.input == undefined) {
					alert(result.reply);
				} else if(result.reply != undefined) {
					show_input_error(result.input,result.reply,null);
				}
			} else if(result.status == 3) {
				$('#chance_sum_noty_'+case_id).addClass('text-danger');
				setTimeout(function() {
					$('#chance_sum_noty_'+case_id).removeClass('text-danger');
				}, 2000);
			}
		}
	});
}
function get_cases_images(case_id){
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&token="+token+"&get_cases_images=1&case_id="+case_id,

		success: function(html) {
			$('#cases_images_modal').modal('show');
			$("#cases_images").html(html);
		}
	});
}
function set_case_image(case_id, id) {
	var url = $("#case_image_"+id+" > span > a:nth-child(1)").attr("data-image-url");

	$('#image_'+case_id).val(id);
	$('#case_'+case_id+'_image').val(id);
	$('#case_'+case_id+'_image').attr("href", url);
	$('#case_'+case_id+'_image > img').attr("src", url);
	$('#cases_images_modal').modal('hide');
}
function dell_case_image(id){
	id = id || 0;

	if(confirm('Вы уверены?')){
		var token = $('#token').val();
		$.ajax({
			type: "POST",
			url: "../modules_extra/cases/ajax/actions.php",
			data: "phpaction=1&dell_case_image=1&token="+token+"&id="+id,

			success: function(html) {
				$("#case_image_"+id).fadeOut();
			}
		});
	}
}
function up_case(case_id){
	case_id = case_id || 0;

	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&up_case=1&token="+token+"&case_id="+case_id,
		dataType: "json",

		success: function(result) {
			NProgress.done();
			if(result.status == 1){
				setTimeout(show_ok, 500);
				get_cases();
			} else {
				setTimeout(show_error, 500);
			}
		}
	});
}
function down_case(case_id){
	case_id = case_id || 0;

	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&down_case=1&token="+token+"&case_id="+case_id,
		dataType: "json",

		success: function(result) {
			NProgress.done();
			if(result.status == 1){
				setTimeout(show_ok, 500);
				get_cases();
			} else {
				setTimeout(show_error, 500);
			}
		}
	});
}
function dell_case(case_id){
	case_id = case_id || 0;

	if(confirm('Вы уверены?')){
		var token = $('#token').val();
		$.ajax({
			type: "POST",
			url: "../modules_extra/cases/ajax/actions.php",
			data: "phpaction=1&dell_case=1&token="+token+"&case_id="+case_id,

			success: function(html) {
				$("#case_"+case_id).fadeOut();
			}
		});
	}
}
function get_cases() {
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&get_cases=1&token="+token,

		success: function(html) {
			$("#cases").html(html);
		}
	});
}

function load_cases() {
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&load_cases=1&token="+token,

		success: function(html) {
			$("#cases").html(html);
			move_modals();
		}
	});
}
function load_subjects(case_id) {
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&load_subjects=1&token="+token+"&case_id="+case_id,

		success: function(html) {
			$("#subjects").html(html);
			move_modals();
		}
	});
}
function get_random(min, max) {
	return Math.floor(Math.random() * (max - min + 1)) + min;
}
function load_roulette(case_id) {
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&load_roulette=1&token="+token+"&case_id="+case_id,

		success: function(html) {
			$("#roulette").html(html);

			rouletter_options = {}
			rouletter = $('div#roulette');
			rouletter.roulette(rouletter_options);	
		}
	});
}
function open_case(case_id) {
	var token = $('#token').val();

	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&open_case=1&token="+token+"&case_id="+case_id,
		dataType: "json",

		success: function(result) {
			if(rouletter == undefined) {
				rouletter_options = {}
				rouletter = $('div#roulette');
				rouletter.roulette(rouletter_options);	
			}
			
			if(result.status == 1) {
				rouletter_options = {
					stopImageNumber : result.item,
					duration: get_random(5, 6),
					startCallback : function() {
						$('#balance').html(result.shilings);
						$('#open-case').attr('disabled', 'true');
					},
					stopCallback : function($stopElm) {
						$('#open-case').removeAttr('disabled');
						show_prize(result.win_id);
						audio_i = 0;
					}
				}
				rouletter.roulette('option', rouletter_options);
				rouletter.roulette('start');
			} else {
				$('#open_case_result_area').html(result.data);
				$('#open_case_result').modal('show');
			}
		}
	});
}
function show_prize(id) {
	$("#prize").clone().appendTo("#hidden_modals"); 
	$("#prize").remove();

	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&show_prize=1&token="+token+"&id="+id,

		success: function(html) {
			setTimeout(function() {
				$('#prize_area').html(html);
				$('#prize').modal('show');
				$('.modal-backdrop').attr('style', 'opacity: 0.8; -webkit-filter: blur(0);-moz-filter: blur(0);-o-filter: blur(0);-ms-filter: blur(0);filter: blur(0);');
				$("body > div:not(#hidden_modals)").addClass('modal-backdrop-blur');
				play_case_sound('drop');
			}, 250);
			$('#prize').on('hide.bs.modal', function (e) {
				$('.modal-backdrop').attr('style', '');
				$("body > div:not(#hidden_modals)").removeClass('modal-backdrop-blur');
			})
		}
	});
}
function get_my_cases() {
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&get_my_cases=1&token="+token,

		success: function(html) {
			$('#my_cases_area').html(html);
			$('#my_cases').modal('show');
		}
	});
}
function get_open_cases(load_val){
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&get_open_cases=1&token="+token+"&load_val="+load_val,

		success: function(html) {
			if(load_val == 'first'){
				$("#open_cases").html(html);
			} else {
				dell_block("loader"+load_val);
				$("#open_cases").append(html);
			}
		}
	});
}

function get_case_banner() {
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: "phpaction=1&get_case_banner=1&token="+token,

		success: function(html) {
			$('#case_banner').html(html);
		}
	});
}

function set_cookie(name, value, expires, path, domain, secure) {
	document.cookie = name + "=" + escape(value) + ((expires) ? "; expires=" + expires : "") + ((path) ? "; path=" + path : "") + ((domain) ? "; domain=" + domain : "") + ((secure) ? "; secure" : "");
}
function get_cookie(name) {
	var cookie = " " + document.cookie;
	var search = " " + name + "=";
	var str = null;
	var offset = 0;
	var end = 0;
	if (cookie.length > 0) {
		offset = cookie.indexOf(search);
		if (offset != -1) {
			offset += search.length;
			end = cookie.indexOf(";", offset)
			if (end == -1) {
				end = cookie.length;
			}
			str = unescape(cookie.substring(offset, end));
		}
	}
	return(str);
}

let audio = []
audio_i = 0;
cases_roulette_sound = 1;

function roulette_sound() {
	if($('#sound-point').hasClass('sound-on')) {
		$('#sound-point').removeClass('sound-on');
		$('#sound-point').addClass('sound-off');

		set_cookie('roulette_sound', 'off');
		cases_roulette_sound = 2;
	} else if($('#sound-point').hasClass('sound-off')) {
		$('#sound-point').removeClass('sound-off');
		$('#sound-point').addClass('sound-on');

		set_cookie('roulette_sound', 'on');
		cases_roulette_sound = 1;
	}
}
function play_case_sound(type) {
	audio_i++;
	if(cases_roulette_sound == 1) {
		audio[audio_i] = new Audio();
		audio[audio_i].volume = 0.05;
		if(type == 'scroll') {
			audio[audio_i].src = '../modules_extra/cases/ajax/scroll.wav';
		}
		if(type == 'drop') {
			audio[audio_i].src = '../modules_extra/cases/ajax/drop.wav';
		}
		audio[audio_i].play();
	}
}