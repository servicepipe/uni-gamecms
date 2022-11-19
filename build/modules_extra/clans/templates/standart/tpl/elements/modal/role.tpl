<div class="input-group">
	<input class="form-control" value="{name}" readonly>
	
	<select class="form-control" id="groups{id}">
		{groups}
	</select>
	
	<span class="input-group-btn">
		<button class="btn btn-primary" type="button" onclick="ChangeRole('{uid}', '{cid}', '{id}');">Изменить</button>
	</span>
</div>