<div class="page">
	<div class="col-md-12">
		<div class="block">
			<div class="block_head">
				Прогресс пользователей
			</div>

			<div class="bs-callout bs-callout-info mt-10 mb-10">
				<h5>Если пользователь пропускает хотя бы один день и не заходит на сайт - его прогресс сбрасывается</h5>
				<button class="btn2 btn-cancel" onclick="dellActivityRewards();" type="button">Сбросить прогресс всех пользователей</button>
			</div>

			<div class="table-responsive mb-0">
				<table class="table table-bordered v-m">
					<thead>
					<tr>
						<td>#</td>
						<td>Пользователь</td>
						<td>Дней подряд</td>
						<td>Полученные награды</td>
						<td>Последняя активность</td>
					</tr>
					</thead>
					<tbody id="progress"></tbody>
				</table>
			</div>
			<script>getActivityRewardsProgress('first');</script>
		</div>
	</div>
</div>