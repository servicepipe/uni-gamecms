<div class="input-group" id="category{id}">
	<span class="input-group-btn">
		<button class="btn btn-default" type="button" onclick="updateCategory({id});">Изменить</button>
	</span>
    <div class="input-group-btn" data-toggle="buttons">
        <button class="btn btn-default" type="button" onclick="removeCategory({id});">Удалить</button>
    </div>
    <input type="text" class="form-control" id="category_title{id}" maxlength="255" value="{title}">
</div>