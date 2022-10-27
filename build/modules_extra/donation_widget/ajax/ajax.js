function dw_donations() {
	var token = $('#token').val();
	$.ajax({
		type: 'POST',
		url: '../modules_extra/donation_widget/ajax/actions.php',
		data: 'phpaction=1&load_donations=1&token=' + token,

		success: function (html) {
			$('#dw_donations').empty();
			$('#dw_donations').append(html);
		}
	});
}

function dw_donate() {
	NProgress.start();
	var token = $('#token').val();
	var amount = $('#dw_amount').val();
	amount = encodeURIComponent(amount);

	if ($('#dw_comment').length) {
		var comment = $('#dw_comment').val();
		comment = encodeURIComponent(comment);
	} else {
		comment = '';
	}

	$('#send_button_donate').addClass('disabled');
	$('#send_button_donate').attr('onclick', '');
	$.ajax({
		type: 'POST',
		url: '../modules_extra/donation_widget/ajax/actions.php',
		data: 'phpaction=1&donate=1&token=' + token + '&amount=' + amount + '&comment=' + comment,
		dataType: 'json',
		success: function (result) {
			NProgress.done();
			if (result.status == 2) {
				setTimeout(show_error, 500);
				if (result.info != '') {
					$('#dw_result').empty();
					$('#dw_result').append('<p class="text-danger">' + result.info + '</p>');
				}
				setTimeout(
					function () {
						$('#dw_result').fadeOut();
					},
					2000
				);
				setTimeout(dw_donations, 3000);
			}
			if (result.status == 3) {
				setTimeout(show_ok, 500);
				$('#dw_result').empty();
				$('#dw_result').append('<p class="text-success">' + result.info + '</p>');
				$('#balance').empty();
				$('#balance').append(result.shilings);
				setTimeout(
					function () {
						$('#dw_result').fadeOut();
					},
					4000
				);
				setTimeout(dw_donations, 5000);
			}
		}
	});
}

function dw_edit_raising(field) {
	var token = $('#token').val();
	var id = $('#dw_raising').val();
	var value = $('#dw_' + field).val();
	id = encodeURIComponent(id);
	value = encodeURIComponent(value);

	if (id == 0) {
		$('#dw_edit_' + field + '_result').html('<p class="text-danger">Выберите сбор!</p>').show();
		setTimeout(
			function () {
				$('#dw_edit_' + field + '_result').fadeOut();
			},
			2000
		);
		return;
	}

	$.ajax({
		type: 'POST',
		url: '../modules_extra/donation_widget/ajax/actions_a.php',
		data: 'phpaction=1&edit_value=1&token=' + token + '&field=' + field + '&value=' + value + '&id=' + id,
		success: function (html) {
			$('#dw_edit_' + field + '_result').html(html).show();
			setTimeout(
				function () {
					$('#dw_edit_' + field + '_result').fadeOut();
				},
				2000
			);
			setTimeout(dw_load_raisings, 1000);
		}
	});
}

function dw_load_raisings() {
	var token = $('#token').val();
	$.ajax({
		type: 'POST',
		url: '../modules_extra/donation_widget/ajax/actions_a.php',
		data: 'phpaction=1&load_raisings=1&token=' + token,
		success: function (html) {
			$('#dw_raising').html(html);
		}
	});
}

function dw_load_raising_info() {
	var token = $('#token').val();
	var id = $('#dw_raising').val();
	id = encodeURIComponent(id);
	$.ajax({
		type: 'POST',
		url: '../modules_extra/donation_widget/ajax/actions_a.php',
		data: 'phpaction=1&load_raising_info=1&token=' + token + '&id=' + id,
		dataType: 'json',
		success: function (result) {
			if (result.status == 1) {
				$('#dw_message').val(result.message);
				$('#dw_target').val(result.target);
				$('#dw_stopdate').val(result.stopdate);
			}
			if (result.status == 2) {
				$('#dw_message').empty();
				$('#dw_target').empty();
				$('#dw_stopdate').empty();
				$('#dw_edit_raising_result').empty();
				$('#dw_edit_raising_result').append('<p class="text-danger">' + result.info + '</p>').show();
				setTimeout(
					function () {
						$('#dw_edit_raising_result').fadeOut();
					},
					2000
				);
			}
		}
	});
}

function dw_raising_act(action) {
	var token = $('#token').val();
	var id = $('#dw_raising').val();
	id = encodeURIComponent(id);
	$.ajax({
		type: 'POST',
		url: '../modules_extra/donation_widget/ajax/actions_a.php',
		data: 'phpaction=1&raising_act=1&token=' + token + '&action=' + action + '&id=' + id,
		success: function (html) {
			$('#dw_edit_raising_result').html(html).show();
			setTimeout(
				function () {
					$('#dw_edit_raising_result').fadeOut();
				},
				2000
			);
			setTimeout(dw_load_raisings, 1000);
		}
	});
}

function dw_change_value(field) {
	var value = $('#dw_' + field).val();
	dw_change_config_value(field, value);

	$('#dw_edit_' + field + '_result').empty();
	$('#dw_edit_' + field + '_result').append('<p class="text-success">Изменено!</p>').show();

	setTimeout(
		function () {
			$('#dw_edit_' + field + '_result').fadeOut();
		},
		2000
	);

	if (field == 'raising') {
		setTimeout(dw_load_raisings, 1000);
	}
}

function dw_change_config_value(attr, value) {
	var token = $('#token').val();
	value = encodeURIComponent(value);
	attr = encodeURIComponent(attr);
	$.ajax({
		type: "POST",
		url: '../modules_extra/donation_widget/ajax/actions_a.php',
		data: "phpaction=1&token=" + token + "&change_config_value=1&attr=" + attr + "&value=" + value,

		success: function (html) {}
	});
}