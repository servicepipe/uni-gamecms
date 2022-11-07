<div class="page">
	<div class="row">
		<div class="col-md-6">
			<form class="block" id="tarif">
				<div class="block_head">
					Добавить тариф
				</div>
				<select id="type" class="form-control mt-10">
					{for($i=1;$i<=count($services_data);$i++)}
						<option value="{{$i}}">{{$services_data[$i]['name']}}</option>
					{/for}
				</select>
				<input class="form-control mt-10" type="text" maxlength="50" name="number" id="number" placeholder="Значение" autocomplete="off">
				<input class="form-control mt-10" type="number" maxlength="6" name="price" id="price" placeholder="Цена (в рублях)" autocomplete="off">
				<button class="btn2 mt-10" onclick="sk_add_tarif();" type="button">Добавить</button>
			</form>
		</div>
		<div class="col-md-6">
			<div class="block">
				<div class="block_head">
					Выберите сервер
				</div>
				<select class="form-control" onchange="server_change();" id="server">
					{servers}
				</select>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-3">
			<div class="block">
				<div class="block_head">
					{{$services_data['1']['name']}}
				</div>
				<table class="table table-bordered table-condensed mb-0">
					<thead>
						<tr>
							<td>#</td>
							<td>Значение</td>
							<td>Цена</td>
							<td>Действие</td>
						</tr>
					</thead>
					<tbody id="tarifs1">
						<tr>
							<td colspan="10">
								<center><img src="{site_host}templates/admin/img/loader.gif"></center>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-md-3">
			<div class="block">
				<div class="block_head">
					{{$services_data['2']['name']}}
				</div>
				<table class="table table-bordered table-condensed mb-0">
					<thead>
						<tr>
							<td>#</td>
							<td>Значение</td>
							<td>Цена</td>
							<td>Действие</td>
						</tr>
					</thead>
					<tbody id="tarifs2">
						<tr>
							<td colspan="10">
								<center><img src="{site_host}templates/admin/img/loader.gif"></center>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-md-3">
			<div class="block">
				<div class="block_head">
					{{$services_data['3']['name']}}
				</div>
				<table class="table table-bordered table-condensed mb-0">
					<thead>
						<tr>
							<td>#</td>
							<td>Значение</td>
							<td>Цена</td>
							<td>Действие</td>
						</tr>
					</thead>
					<tbody id="tarifs3">
						<tr>
							<td colspan="10">
								<center><img src="{site_host}templates/admin/img/loader.gif"></center>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-md-3">
			<div class="block">
				<div class="block_head">
					{{$services_data['4']['name']}}
				</div>
				<table class="table table-bordered table-condensed mb-0">
					<thead>
						<tr>
							<td>#</td>
							<td>Значение</td>
							<td>Цена</td>
							<td>Действие</td>
						</tr>
					</thead>
					<tbody id="tarifs4">
						<tr>
							<td colspan="10">
								<center><img src="{site_host}templates/admin/img/loader.gif"></center>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script>
	function server_change() {
		var server = $('#server').val();
		location.href = '../admin/sk_services?server='+server;
	}
	sk_load_tarifs(1);
	sk_load_tarifs(2);
	sk_load_tarifs(3);
	sk_load_tarifs(4);
</script>