<div class="page">
	<div class="row">
		<div class="col-md-3">
			<div class="block">
				<div class="block_head">
					Добавить продукт
				</div>

				<label for="title">Название</label>
				<input type="text" class="form-control" maxlength="255" id="title" placeholder="Введите название" value="{title}">

				<label for="status" class="mt-10">Продажа</label>
				<select class="form-control" id="status">
					<option {if('{status}' == '1')} selected {/if} value="1">Продается</option>
					<option {if('{status}' == '2')} selected {/if} value="2">Не продается</option>
				</select>

				<label for="image" class="mt-10">Изображение</label>
				<img id="image-preview" src="{image}" alt="{title}">
				<input type="file" class="input-file w-100" maxlength="255" id="image" onchange="setImagePreview(this, '#image-preview')">

				<label for="is-has-tarifs" class="mt-10">Наличие тарифов</label>
				<select class="form-control" id="is-has-tarifs" onchange="if($(this).val() == 1) { $('#has-tarifs').fadeIn(0); $('#has-not-tarifs').fadeOut(0) } else { $('#has-tarifs').fadeOut(0); $('#has-not-tarifs').fadeIn(0) }">
					<option {if('{isHasTarifs}' == '2')} selected {/if} value="2">Не имеет тарифов</option>
					<option {if('{isHasTarifs}' == '1')} selected {/if} value="1">Имеет тарифы</option>
				</select>

				<div id="has-not-tarifs" {if('{isHasTarifs}' == '1')} style="display:none;" {/if}>
					<label for="tarif-price" class="mt-10">Цена</label>
					<input type="text" class="form-control" maxlength="11" id="tarif-price" placeholder="Введите цену" value="{{$tarifs[0]->price}}">

					<label for="tarif-command" class="mt-10">Команда</label>
					<input type="text" class="form-control" maxlength="512" id="tarif-command" placeholder="Введите rcon команду" value="{{$tarifs[0]->command}}">
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="block">
				<div class="block_head">
					Описание
				</div>

				<textarea id="description" class="form-control maxMinW100" rows="5">{description}</textarea>
			</div>
		</div>
		<div class="col-md-5">
			<div class="block">
				<div class="block_head">
					Параметры для ввода пользователем
				</div>

				<label for="command">Параметры для ввода пользователем</label>
				<div class="bs-callout bs-callout-info mb-10">
					<p>
						Укажите здесь параметры, которые необходимо будет ввести пользователю при покупке услуги.
						В поле "Переменная" необходимо ввести то кодовое слово, которое будет фигурировать в поле "Команда",
						на его место будет подставляться введеное пользователем значение. При вводе переменной {steamid} - введеное
						пользователем значение будет проверяться на корректно введеный STEAM ID
					</p>
				</div>

				<input type="hidden" id="command-params-count" value="0">

				<form id="command-params" class="mb-10"></form>

				<button type="button" class="btn btn-default" onclick="addCommandParam();">
					Добавить
				</button>
			</div>
		</div>
		<div class="col-md-9">
			<div class="block" id="has-tarifs"  {if('{isHasTarifs}' == '2')} style="display:none;" {/if}>
				<div class="block_head">
					Тарифы
				</div>

				<div class="bs-callout bs-callout-info mb-10">
					<p>
						При наличии тарифов каждой цене будет соответствовать своя rcon команда,
						благодаря этому вы можете указать прямо в команде параметр, от которого будет зависеть
						цена
					</p>
				</div>

				<input type="hidden" id="tarifs-count" value="0">

				<div class="row">
					<div class="col-md-2">
						<b>Цена</b>
					</div>
					<div class="col-md-3">
						<b>Название</b>
					</div>
					<div class="col-md-5">
						<b>Команда</b>
					</div>
				</div>

				<form id="tarifs" class="mb-10"></form>

				<button type="button" class="btn btn-default" onclick="addTarif();">
					Добавить
				</button>
			</div>
		</div>
	</div>

	<button class="btn2 btn-lg mt-10" onclick="saveProduct({id});">Сохранить</button>
</div>

<script>
	init_tinymce('description', '{{md5($conf->code)}}', 'full');

	{for($l = 0; $l < count($commandParams); $l++)}
		addCommandParam('{{$commandParams[$l]->name}}', '{{$commandParams[$l]->title}}');
	{/for}

	{if('{isHasTarifs}' == '1')}
		{for($l = 0; $l < count($tarifs); $l++)}
			addTarif('{{$tarifs[$l]->price}}', '{{$tarifs[$l]->title}}', '{{$tarifs[$l]->command}}');
		{/for}
	{else}
		addTarif();
	{/if}
</script>