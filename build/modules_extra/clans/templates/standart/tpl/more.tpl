<div class="col-lg-9 order-is-first clans more">
	<div class="cover" data-src="{cover}"></div>
	<div class="details">
		<div class="position-absolute d-flex align-items-center">
			<a data-lightbox="1" href="{logotype}">
				<img src="{logotype}">
			</a>
			<div class="content">
				<p>{name}</p>
				<span>{status}</span>
			</div>
		</div>
		
		{if(isset($_SESSION['id']))}
		<div class="d-flex justify-content-end mb-2">
			<a class="btn btn-sm btn-default mr-2" href="/clans">Список кланов</a>
			<div id="clanbtn">{btn}</div>
		</div>
		{/if}
	</div>
	
	<div class="block">
		<ul class="players">
			{list}
		</ul>
	</div>
</div>

<div class="col-lg-3 order-is-last">
	{if(IsFlags($_SESSION['id'], '{cid}', 'b'))}
	<a href="/clans/shop" class="btn btn-default w-100 mb-4">Магазин</a>
	{/if}
	
	{if(IsFlags($_SESSION['id'], '{cid}', 'a'))}
	<div class="block">
		<h5 class="block_head">Управление</h5>
		{if(IsFlags($_SESSION['id'], '{cid}', 'u'))}
		<button class="btn btn-primary w-100" data-toggle="modal" data-target="#role" data-cid="{cid}">Ролями</button>
		{/if}
		
		{if(IsFlags($_SESSION['id'], '{cid}', 'b'))}
		<button class="btn btn-secondary w-100" data-toggle="modal" data-target="#settings" data-cid="{cid}">Настройками</button>
		{/if}
		
		<button class="btn btn-warning w-100" data-toggle="modal" data-target="#applications" data-cid="{cid}">
			Заявками
			{if('{waits}' > 0)}
			(+{waits})
			{/if}
		</button>
	</div>
	{/if}
	
	<div class="block">
		<h5 class="block_head">О клане</h5>
		<ul class="info">
			<li>
				<img src="{chief-avatar}">
				<div class="content">
					<p>{chief}</p>
					<span>Глава клана</span>
				</div>
			</li>
			<li>
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
					  <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path>
				</svg>
				<div class="content">
					<p>{players} из {max_players}</p>
					<span>Участники клана</span>
				</div>
			</li>
			<li>
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16">
					<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z"/>
				</svg>
				<div class="content">
					<p>{rating}</p>
					<span>Рейтинг клана</span>
				</div>
			</li>
			{if(IsFlags($_SESSION['id'], '{cid}', 'u'))}
			<li>
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-coin" viewBox="0 0 16 16">
					<path d="M5.5 9.511c.076.954.83 1.697 2.182 1.785V12h.6v-.709c1.4-.098 2.218-.846 2.218-1.932 0-.987-.626-1.496-1.745-1.76l-.473-.112V5.57c.6.068.982.396 1.074.85h1.052c-.076-.919-.864-1.638-2.126-1.716V4h-.6v.719c-1.195.117-2.01.836-2.01 1.853 0 .9.606 1.472 1.613 1.707l.397.098v2.034c-.615-.093-1.022-.43-1.114-.9H5.5zm2.177-2.166c-.59-.137-.91-.416-.91-.836 0-.47.345-.822.915-.925v1.76h-.005zm.692 1.193c.717.166 1.048.435 1.048.91 0 .542-.412.914-1.135.982V8.518l.087.02z"/>
					<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
					<path d="M8 13.5a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11zm0 .5A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/>
				</svg>
				<div class="content">
					<p>{balance}</p>
					<span>Баланс клана</span>
				</div>
			</li>
			{/if}
		</ul>
	</div>
</div>

<!--[ Модальки ]-->
{if(IsFlags($_SESSION['id'], '{cid}', 'u'))}
<div class="modal fade" id="role" tabindex="-1" role="dialog" aria-labelledby="role">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="role">Управление Ролями</h4>
			</div>
			<div class="modal-body" id="list_role"></div>
		</div>
	</div>
</div>
{/if}

{if(IsFlags($_SESSION['id'], '{cid}', 'b'))}
<div class="modal fade" id="settings" tabindex="-1" role="dialog" aria-labelledby="settings">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="settings">Настройки</h4>
			</div>
			<div class="modal-body">
				<label class="mb-0">Логотип</label>
				<form id="form_change_logotype" class="input-group">
					<input type="hidden" name="cid" value="{cid}">
					<input name="image" class="form-control" type="file" accept="image/jpeg,image/png,image/gif">
					
					<span class="input-group-btn">
						<button class="btn btn-primary">Изменить</button>
					</span>
				</form>
				
				<label class="mb-0 mt-2">Обложка</label>
				<form id="form_change_cover" class="input-group">
					<input type="hidden" name="cid" value="{cid}">
					<input name="image" class="form-control" type="file" accept="image/jpeg,image/png,image/gif">
					
					<span class="input-group-btn">
						<button class="btn btn-primary">Изменить</button>
					</span>
				</form>
				
				<label class="mb-0 mt-2">Статус</label>
				<div class="input-group">
					<input type="text" class="form-control" placeholder="Напишите текст..." value="{status}" id="status">
					
					<span class="input-group-btn">
						<button class="btn btn-primary" type="button" onclick="ChangeStatus({cid});">Изменить</button>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
{/if}

{if(IsFlags($_SESSION['id'], '{cid}', 'a'))}
<div class="modal fade" id="applications" tabindex="-1" role="dialog" aria-labelledby="applications">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="applications">Заявки на вступление</h4>
			</div>
			<div class="modal-body">
				<ul class="applications" id="list_applications"></ul>
			</div>
		</div>
	</div>
</div>
{/if}