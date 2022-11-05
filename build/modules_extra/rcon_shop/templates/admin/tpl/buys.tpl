<div class="page">
	<div class="block">
		<div class="block_head">
			Покупки товаров
		</div>

		<div class="row">
			<div class="col-md-4">
				<select class="form-control" id="server" onchange="go_to('../admin/rcon_shop_buys?server=' + $(this).val())">
					{servers}
				</select>
			</div>
			<div class="col-md-4">
				<select class="form-control" id="category" onchange="loadBuys()">
					{categories}
				</select>
			</div>
			<div class="col-md-4">
				<select class="form-control" id="limit" onchange="loadBuys()">
					<option value="10">10</option>
					<option value="50">50</option>
					<option value="100">100</option>
					<option value="500">500</option>
					<option value="0">Все</option>
				</select>
			</div>
		</div>

		<div class="table-responsive mb-0 mt-10">
			<table class="table table-bordered">
				<thead>
					<tr>
						<td>#</td>
						<td>Продукт</td>
						<td>Сумма</td>
						<td>Пользователь</td>
						<td>Дата</td>
						<td>Запрос/Ответ</td>
					</tr>
				</thead>
				<tbody id="operations">
					<tr>
						<td colspan="10">
							<script>
								loadBuys();
							</script>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>