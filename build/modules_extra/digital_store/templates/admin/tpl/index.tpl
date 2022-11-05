<div class="page">
	<div class="block">
		<div class="block_head">
			Настройка категорий товаров
		</div>

		<div class="row">
			<div class="col-lg-6">
				<b>Добавить категорию</b>
				<input type="text" class="form-control mt-10" maxlength="256" id="category_name" placeholder="Введите название">
				<button class="btn btn-default mt-10" onclick="add_category();">Добавить</button>
			</div>
			<div class="col-lg-6">
				<b>Категории:</b>
				<div id="categories" class="mt-10"></div>
			</div>
		</div>
	</div>

	<div class="block">
		<div class="block_head">
			Категория продуктов
		</div>
		<select id="category" class="form-control" onchange="load_products();"></select>
	</div>

	<div class="block">
		<div class="block_head">
			Добавить продукт
		</div>

		<input type="text" class="form-control" maxlength="256" id="name" placeholder="Введите название">
		<input type="text" class="form-control mt-10" maxlength="11" id="price" placeholder="Введите цену">
		<input type="file" class="input-file w-100 mt-10 mb-10" maxlength="256" id="image">
		<textarea id="description" class="form-control maxMinW100" rows="5">Введите описание</textarea>

		<button class="btn btn-default mt-10" onclick="product_action();">Добавить</button>
	</div>

	<div id="products" class="row"></div>

	<script>
        var tiny_code = "{{md5($conf->code)}}";
        load_categories(1);
	</script>
</div>