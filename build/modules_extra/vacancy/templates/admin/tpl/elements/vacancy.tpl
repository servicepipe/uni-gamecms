<form id="form_add_vacancy">
	<div class="row">
		<div class="col-lg-8 col-sm-12"><input class="form-control" type="text" name="title" placeholder="Наименование" autocomplete="off"></div>
		<div class="col-lg-4 col-sm-12"><input id="b_vacancy" class="btn btn-primary w-100" type="submit" value="Добавить"></div>
	</div>
</form>

<table class="table mt-4 table-responsive">
	<thead>
		<tr>
			<th><b>Наименование</b></th>
			<th style="width:5%;"><b>Действия</b></th>
		</tr>
	</thead>
	
	<tbody>
		{vacancy-list}
	</tbody>
</table>