<div class="page">
	<div class="col-md-8">
		<div class="block">
			<div class="block_head">Список доступных скинов</div>
			<table class="table w-100">
				<thead>
					<tr>
						<th>#</th>
						<th>Наименование</th>
						<th>Стоимость</th>
						<th>Действия</th>
					</tr>
				</thead>
				<tbody>{skins_list}</tbody>
			</table>
		</div>
	</div>
	<div class="col-md-4">
		<div class="block">
			<div class="block_head">Добавить новый скин</div>
			
			<label for="server">Сервер</label>
			<select class="form-control" id="server">
				{servers}
			</select>
			
			<div class="form-group">
				<label for="name">Название скина</label>
				<input type="text" class="form-control" placeholder="Название скина" id="name">
			</div>			
			
			<div class="form-group">
				<label for="price">Стоимость скина</label>
				<input type="text" class="form-control" placeholder="50" id="price">
			</div>
			
			<div class="form-group">
				<label for="model">Наименование модели кт</label>
				<input type="text" class="form-control" placeholder="leet" id="model_ct">
			</div>
			
			<div class="form-group">
				<label for="model">Наименование модели т</label>
				<input type="text" class="form-control" placeholder="leet" id="model_t">
			</div>
			
			<div class="form-group">
				<label for="image">Изображение</label>
				<input type="file" class="form-control" id="image" accept="image/*">
			</div>
			<hr>
			<button class="btn btn-primary" OnClick="add_skin();" id="add">Добавить</button>
			<div id="result_add_skin"></div>
		</div>
		
		<div class="block">
			<div class="block_head">Инструкция</div>
			<small>Данный модуль работает с плагином: <a href="/modules_extra/skins_store/uploads/documentation/amxx_gamecms_skins.zip" download>GameCMS Skins</a><br>
			Наименование модели выставляется из доступных моделей в файле <span class="text-warning">gamecms_skins.ini</span> на сервере.
			</small>
		</div>
	</div>
</div>
<script src="../../../modules_extra/skins_store/ajax/admin.js?v={cache}"></script>