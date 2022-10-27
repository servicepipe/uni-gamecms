<div class="page">
	<div class="col-md-6">
		<div class="block">
			<div class="block_head">Настройки виджета</div>
			<b>Включение виджета</b>
			<div class="form-group">
				<div class="btn-group" data-toggle="buttons">
					<label class="btn btn-default {dw_enabled_act}" onclick="dw_change_config_value('enabled','1');">
						<input type="radio">
						Включить
					</label>

					<label class="btn btn-default {dw_enabled_act2}" onclick="dw_change_config_value('enabled','2');">
						<input type="radio">
						Выключить
					</label>
				</div>
				<br>
				<small class="f-r c-868686">Виджет будет отображаться только если сбор создан и выбран</small><br>
			</div>
			<hr>
			<b>Система комментариев</b>
			<div class="form-group">
				<div class="btn-group" data-toggle="buttons">
					<label class="btn btn-default {dw_comm_act}" onclick="dw_change_config_value('comments','1');">
						<input type="radio">
						Включить
					</label>

					<label class="btn btn-default {dw_comm_act2}" onclick="dw_change_config_value('comments','2');">
						<input type="radio">
						Выключить
					</label>
				</div>
			</div>
			<hr>
			<b>Остановка сбора после набора необходимой суммы</b>
			<div class="form-group">
				<div class="btn-group" data-toggle="buttons">
					<label class="btn btn-default {dw_autostop_act}" onclick="dw_change_config_value('autostop','1');">
						<input type="radio">
						Включить
					</label>

					<label class="btn btn-default {dw_autostop_act2}" onclick="dw_change_config_value('autostop','2');">
						<input type="radio">
						Выключить
					</label>
				</div>
				<br>
				<small class="f-l c-868686">Если выключено - сбор будет длиться до выставленной даты</small><br>
			</div>
			<hr>
			<b>Список меценатов</b>
			<div class="form-group">
				<div class="btn-group" data-toggle="buttons">
					<label class="btn btn-default {dw_list_act}" onclick="dw_change_config_value('showlist','1');">
						<input type="radio">
						Включить
					</label>

					<label class="btn btn-default {dw_list_act2}" onclick="dw_change_config_value('showlist','2');">
						<input type="radio">
						Выключить
					</label>
				</div>
				<div class="input-group mt-10">
					<span class="input-group-btn">
						<button class="btn btn-default" type="button" onclick="dw_change_value('listlimit');">Изменить</button>
					</span>
					<input type="number" class="form-control" id="dw_listlimit" min="0" max="16" autocomplete="off" placeholder="Введите число" value="{listlimit}">
					<small class="f-r c-868686">Количество меценатов в списке (0 - будут выведены все меценаты)</small><br>
				</div>
				<div id="dw_edit_listlimit_result" class="mt-10"></div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="block">
			<div class="block_head">Настройки сбора</div>
			<b>Текущий сбор</b>
			<div class="form-group">
				<select class="form-control" id="dw_raising" onchange="dw_load_raising_info();">
					<script>dw_load_raisings();</script>
				</select>
				<div class="input-group mt-10">
					<span class="input-group-btn">
						<button class="btn btn-default" type="button" onclick="dw_change_value('raising');">Изменить</button>
						<button class="btn btn-default" type="button" onclick="dw_raising_act(1);">Добавить</button>
						<button class="btn btn-default" type="button" onclick="dw_raising_act(2);">Удалить</button>
					</span>
				</div>
				<div id="dw_edit_raising_result" class="mt-10"></div>
			</div>
			<hr>
			<b>Описание цели</b>
			<div class="form-group">
				<div class="input-group mt-10">
					<span class="input-group-btn">
						<button class="btn btn-default" type="button" onclick="dw_edit_raising('message');">Изменить</button>
					</span>
					<input type="text" class="form-control" id="dw_message" maxlength="64" autocomplete="off" placeholder="Введите название цели. Пример - Покупка плагинов" value="{message}">
				</div>
				<div id="dw_edit_message_result" class="mt-10"></div>
			</div>
			<hr>
			<b>Необходимое количество денег</b>
			<div class="form-group">
				<div class="input-group mt-10">
					<span class="input-group-btn">
						<button class="btn btn-default" type="button" onclick="dw_edit_raising('target');">Изменить</button>
					</span>
					<input type="number" class="form-control" id="dw_target" min="0" max="999999" autocomplete="off" placeholder="Необходимое количество денег для достижения цели" value="{target}">
				</div>
				<div id="dw_edit_target_result" class="mt-10"></div>
			</div>
			<hr>
			<b>Дата окончания сборов</b> <small>(смотрите в настройки модуля)</small>
			<div class="form-group">
				<div class="input-group mt-10">
					<span class="input-group-btn">
						<button class="btn btn-default" type="button" onclick="dw_edit_raising('stopdate');">Изменить</button>
					</span>
					<input type="text" class="form-control" id="dw_stopdate" maxlength="24" autocomplete="off" placeholder="Выберите дату и время окончания сбора" value="{stopdate}">
					<script>
						$('#dw_stopdate').datetimepicker({
							showButtonPanel: false,
							timeOnlyShowDate: false
						});
					</script>
				</div>
				<div id="dw_edit_stopdate_result" class="mt-10"></div>
			</div>
		</div>
	</div>
</div>