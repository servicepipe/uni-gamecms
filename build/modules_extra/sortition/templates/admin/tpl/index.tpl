<script src="{site_host}templates/admin/js/timepicker/timepicker.js"></script>
<script src="{site_host}templates/admin/js/timepicker/jquery-ui-timepicker-addon.js"></script>
<script src="{site_host}templates/admin/js/timepicker/jquery-ui-timepicker-addon-i18n.min.js"></script>
<script src="{site_host}templates/admin/js/timepicker/jquery-ui-sliderAccess.js"></script>

<div class="page">
	<div class="col-md-12">
		<div class="block">
			<div class="block_head">
				Параметры розыгрыша
			</div>
			<div class="row">
				<div class="col-md-6">
					<b>Название розыгрыша</b>
					<input type="text" class="form-control" id="name" placeholder="Название" maxlength="255" value="{name}">
					<br>

					<b>Цена участия</b>
					<input type="number" class="form-control" id="price" placeholder="Цена" maxlength="9" value="{price}">
					<small class="f-r c-868686">0 - бесплатное участие</small>
					<br>

					<b>Количество участников</b>
					<input type="number" class="form-control" id="participants" placeholder="Количество" maxlength="9" value="{participants}">
					<small class="f-r c-868686">0 - неограниченное количество участников</small>
					<br>

					<b>Отображение участников</b>
					<select id="show_participants" class="form-control">
						<option value="1" {if('{show_participants}' == '1')} selected {/if}>Отображать</option>
						<option value="2" {if('{show_participants}' == '2')} selected {/if}>Не отображать</option>
					</select>
					<br>

					<b>Сколько дней с момента регистрации аккаунта должно пройти, чтобы была возможность принять участие в розыгрыше</b>
					<input type="number" class="form-control" id="how_old" placeholder="Количество дней" maxlength="3" value="{how_old}">
					<small class="f-r c-868686">0 - не имеет значения</small>
					<br>

					<b>Когда завершить розыгрыш</b>
					<select id="end_type" class="form-control" onchange="end_type_change();">
						<option value="1" {if('{end_type}' == '1')} selected {/if}>В определенное время</option>
						<option value="2" {if('{end_type}' == '2')} selected {/if}>По достижению необходимого количества участников</option>
					</select>
					<br>

					<div id="date_aera">
						<b>Дата окончания</b>
						<input onclick="$('.ui-datepicker-current').remove();$('.ui-datepicker-current2').remove();" class="form-control" type="text" id="ending" value="{ending}">
					</div>
				</div>
				<div class="col-md-6">
					<b>Приз</b>
					<input type="hidden" id="own_prize" value="{own_prize}">
					<section>
						<div class="tabs tabs-style-topline">
							<nav>
								<ul>
									<li id="own_prize_1" onclick="$('#own_prize').val('2');"><a href="#section-topline-1"><span>Автоматическая выдача</span></a></li>
									<li id="own_prize_2" onclick="$('#own_prize').val('1');"><a href="#section-topline-2"><span>Свой приз</span></a></li>
								</ul>
							</nav>
							<div class="content-wrap">
								<section id="section-topline-1">
									<form id="prizes">
										<script>get_prizes_adm();</script>
									</form>
									<input type="hidden" value="0" id="place_count">
									{if('{finished}' == '2')}
										<button class="btn2 mt-10" onclick="add_place();" type="button">Добавить призы для <i id="place_i">#</i> победителя</button>
									{/if}
								</section>
								<section id="section-topline-2">
									<b>Укажите количество победителей</b>
									<input type="number" class="form-control" id="count_of_winners" placeholder="Количество победителей" maxlength="2" value="{count_of_winners}">
									<br>
									<b>Введите описание</b>
									<textarea id="text" class="form-control" rows="5">{text}</textarea>
									<small class="f-r c-868686">Данный приз(ы) потребуется выдавать вручную</small>
								</section>
							</div>
						</div>
					</section>
				</div>
			</div>

			<hr>

			{if('{exists}' == '2')}
				<button class="btn2" onclick="save_sortition(1);" type="button">Создать розыгрыш</button>
			{else}
				{if('{finished}' == '2')}
				<button class="btn2" onclick="save_sortition(2);" type="button">Изменить розыгрыш</button>
				{/if}
				<button class="btn2 btn-cancel" onclick="dell_sortition();" type="button">Удалить розыгрыш</button>
			{/if}
		</div>
	</div>
	<div class="col-md-6">
		<div class="block">
			<div class="block_head">
				Участники {if('{price}' != '0')}| Общий сбор с конкурса: {bank}{/if}
			</div>
			<table class="table table-bordered mb-0">
				<thead>
					<tr>
						<td>#</td>
						<td>Пользователь</td>
						<td>Взнос</td>
					</tr>
				</thead>
				<tbody id="participants_list">
					<tr>
						<td colspan="10">
							<center><img src="{site_host}templates/admin/img/loader.gif"></center>
							<script>load_participants_list(1);</script>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-md-6">
		<div class="block">
			<div class="block_head">
				Победитель
			</div>
			<table class="table table-bordered mb-0">
				<thead>
					<tr>
						<td>#</td>
						<td>Пользователь</td>
						<td>Место</td>
						<td>Взнос</td>
					</tr>
				</thead>
				<tbody id="winner">
					<tr>
						<td colspan="10">
							<center><img src="{site_host}templates/admin/img/loader.gif"></center>
							<script>load_participants_list(2);</script>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script src="{site_host}templates/admin/js/tabs.js"></script>
<script>
	(function() { [].slice.call( document.querySelectorAll( '.tabs' ) ).forEach( function( el ) { new CBPFWTabs( el ); }); })();
	if('{own_prize}' == 2) {
		$('#own_prize_1').trigger('click');
	} else {
		$('#own_prize_2').trigger('click');
	}

	init_tinymce('text', '{{md5($conf->code)}}', 'lite');

	function end_type_change(id) {
		if($('#end_type option:selected').val() == 1) {
			$('#date_aera').fadeIn();
		} else {
			$('#date_aera').fadeOut();
		}
	}
	end_type_change();
	$('#ending').datetimepicker({
		timeInput: true,
		timeFormat: "HH:mm",
		onSelect: function() {
			setTimeout(function() {
				$('.ui-datepicker-current').remove();
				$('.ui-datepicker-current2').remove();
			}, 200);
		}
	});
	function add_place() {
		var places = $('#place_i').text();
		$('#prizes').append('<div id="prizes_div_'+places+'" class="prize-block">\
								<b>Приз(ы) для '+places+' победителя (<a onclick="dell_place('+places+')" class="c-p">Удалить</a>)</b>\
								<input type="hidden" value="0" id="prize_count_'+places+'">\
								<div id="prizes_'+places+'"></div>\
								<div class="input-group">\
									<span class="input-group-btn">\
										<button class="btn btn-default" type="button" onclick="get_prize_line('+places+');">Добавить</button>\
									</span>\
									<select class="form-control" id="prize_type_'+places+'">\
									{if($prize_types[1] == 1)}\
									<option value="1">Услугу</option>\
									{/if}\
									{if($prize_types[2] == 1)}\
									<option value="2">Денежный приз</option>\
									{/if}\
									{if($prize_types[3] == 1)}\
									<option value="3">Скидку</option>\
									{/if}\
									{if($prize_types[4] == 1)}\
									<option value="4">Приз из shop_key</option>\
									{/if}\
									{if($prize_types[5] == 1)}\
									<option value="5">Приз из buy_key</option>\
									{/if}\
									{if($prize_types[6] == 1)}\
									<option value="6">Приз из vip_key_ws</option>\
									{/if}\
									{if($prize_types[7] == 1)}\
									<option value="7">Поинты</option>\
									{/if}\
									{if($prize_types[8] == 1)}\
									<option value="8">Опыт</option>\
									{/if}\
									</select>\
								</div>\
							</div>');

		places = Number(places) + 1;
		$('#place_i').html(places);
		$('#place_count').html(places);
	}
</script>