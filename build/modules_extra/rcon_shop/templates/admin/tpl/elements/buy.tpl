<tr>
	<td>{id}</td>
	<td>
		<a target="_blank" href="../admin/rcon_shop_product?id={product_id}">{title}</a>
	</td>
	<td>
		-{price}
	</td>
	<td>
		<a target="_blank" href="../admin/edit_user?id={user_id}">
			<img src="../{user_avatar}" alt="{user_login}"> {user_login}
		</a>
	</td>
	<td>
		{date}
	</td>
	<td>
		<a class="btn btn-default btn-sm" data-toggle="modal" data-target="#info{id}">
			Открыть
		</a>

		<div class="modal fade" id="info{id}">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Запрос/Ответ</h4>
					</div>
					<div class="modal-body with_code">
						<b>Команда</b>
						<pre>{command}</pre>

						<b>Ответ</b>
						<pre>{answer}</pre>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
					</div>
				</div>
			</div>
		</div>
	</td>
</tr>
