<div class="page">
	<div class="col-md-12">
		<div class="block">
			<div class="block_head">
				Выберите сервер
			</div>
			<select class="form-control" onchange="server_change();" id="server">
				{servers}
			</select>
		</div>
	</div>
	<div class="col-md-6">
		<form class="block" id="service">
			<div class="block_head">
				Добавить услугу
			</div>

			<select class="form-control mt-10" name="sale" id="sale">
				<option value="1">Продажа: Включена</option>
				<option value="2">Продажа: Выключена</option>
			</select>
			<input class="form-control mt-10" type="text" maxlength="255" id="name" name="name" placeholder="Название услуги (как в плагине)" autocomplete="off">
			<br>
			<textarea id="text" class="form-control maxMinW100" rows="5">Описание</textarea>
			<button class="btn2 mt-10" onclick="bk_add_service();" type="button">Добавить</button>
		</form>
	</div>
	<div class="col-md-6">
		<form class="block" id="tarif">
			<div class="block_head">
				Добавить тариф
			</div>
			Выберите услугу:
			<select class="form-control" id="services"></select>
			<input class="form-control mt-10" type="text" maxlength="7" name="time" id="time" placeholder="Время (в днях, 0 - навсегда)" autocomplete="off">
			<input class="form-control mt-10" type="text" maxlength="6" name="price" id="price" placeholder="Цена (в рублях)" autocomplete="off">
			<button class="btn2 mt-10" onclick="bk_add_tarif();" type="button">Добавить</button>
		</form>
	</div>

	<div class="col-md-12 mt-10" id="all_services">
		<center><img src="{site_host}templates/admin/img/loader.gif"></center>
	</div>
</div>
<script>
	function server_change() {
		var server = $('#server').val();
		location.href = '../admin/bk_services?server='+server;
	}
	init_tinymce('text', '{{md5($conf->code)}}', 'lite');
	bk_load_services(1, 'services', '{{md5($conf->code)}}');
	bk_load_services(2, 'all_services', '{{md5($conf->code)}}');
</script>