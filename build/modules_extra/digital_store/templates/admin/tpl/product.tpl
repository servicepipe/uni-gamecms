<div class="page">
	<div class="block">
		<div class="block_head">
			Добавить содержимое
		</div>

		<b>Введите текст, который отобразится покупателю</b>
		<input type="hidden" value="{product_id}" id="product">
		<textarea id="content" class="form-control maxMinW100" rows="5">Введите текст</textarea>
		<button class="btn btn-default mt-10" onclick="product_key_action();">Добавить</button>
	</div>

	<div class="block">
		<div class="block_head">
			Содержимое
		</div>

		<div id="product_keys"></div>
	</div>

	<script>
        var tiny_code = "{{md5($conf->code)}}";
        load_product_keys();
	</script>
</div>