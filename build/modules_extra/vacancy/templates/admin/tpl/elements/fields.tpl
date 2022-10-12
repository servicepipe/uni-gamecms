<form id="form_add_field">
	<div class="row">
		<div class="col-lg-3 col-sm-12"><input class="form-control" type="text" name="title" placeholder="Заголовок" autocomplete="off"></div>
		<div class="col-lg-3 col-sm-12"><input class="form-control" type="text" name="code" placeholder="Кодовое имя" autocomplete="off"></div>
		<div class="col-lg-3 col-sm-12"><input class="form-control" type="text" name="placeholder" placeholder="Подсказка" autocomplete="off"></div>
		<div class="col-lg-3 col-sm-12"><input id="b_field" class="btn btn-primary w-100" type="submit" value="Добавить"></div>
	</div>
	
</form>

<table class="table mt-4 table-responsive">
	<thead>
		<tr>
			<th><b>Наименование</b></th>
			<th><b>Кодовое имя</b></th>
			<th><b>Подсказка</b></th>
			<th style="width:5%;"><b>Действия</b></th>
		</tr>
	</thead>
	
	<tbody>
		{fields-list}
	</tbody>
</table>