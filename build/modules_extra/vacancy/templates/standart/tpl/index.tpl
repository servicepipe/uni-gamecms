<div class="col-lg-9 order-is-first">
	{vacancy}
	<center class="mt-4">
		{pagination}
	</center>
</div>

<div class="col-lg-3 order-is-last">
	{if(isset($_SESSION['id']))}
	<a class="btn btn-primary w-100" href="/vacancy/create">Подать заявку</a>
	{/if}
	<div class="block">
		<div class="block_head">
			Список серверов
		</div>
		<div class="vertical-navigation">
			<ul>{servers}</ul>
		</div>
	</div>
</div>