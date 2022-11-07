<div class="page">
	<div class="col-md-12">
		<div class="block">
			<div class="block_head">
				Добавление нового окна
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="textTitle">Заголовок окна</label>
					<input type="text" class="form-control" id="textTitle">
				</div>
				<div class="form-group">
					<label for="textMessage">Сообщение</label>
					<textarea class="form-control" id="textMessage"></textarea>
				</div>
				<br>
			</div>
			
			<div class="col-md-6">
				<label>Показывать</label>
				<select class="form-control form-control-lg" id="valueTimelife">
					<option value="0">Постоянно</option>
					<option value="3600">Раз в час</option>
					<option value="86400">Раз в день</option>
					<option value="604800">Раз в неделю</option>
					<option value="2592000">Раз в месяц</option>
				</select>
			</div>
			
			<div class="col-md-6">
				<label>Кому показывать</label>
				<select class="form-control form-control-lg" id="valueAuth">
					<option value="1">Всем</option>
					<option value="2">Авторизованным</option>
					<option value="3">Гостям</option>
				</select>
			</div>
			
			<button type="button" class="btn btn-primary btn-block" onclick="addModalViewer();">Добавить</button>
			<div id="resultAddModalViewer"></div>
		</div>
		
		<div class="block">
			<div class="block_head">
				Список уже имеющихся окон
			</div>
			{list_modal_viewer}
		</div>
	</div>
</div>
<script src="/modules_extra/modal_viewer/templates/admin/js/ajax.js"></script>