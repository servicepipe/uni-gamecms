const RCON_SHOP_ADMIN_CONTROLLER = '../modules_extra/rcon_shop/actions/admin/index.php';

function addCategory() {
	ajax({
		controller: RCON_SHOP_ADMIN_CONTROLLER,
		data: {
			addCategory: true,
			title: $('#category_title').val(),
			server: $('#server').val()
		},
		success: () => {
			loadCategories();
			loadCategoriesOptions();
		},
		inputs: {
			title: 'category_title'
		},
		progress: true
	});
}

function loadCategories() {
	ajax({
			controller: RCON_SHOP_ADMIN_CONTROLLER,
			data: {
				loadCategories: true,
				server: $('#server').val()
			},
			success: (result) => {
				$('#categories').html(result.data);
			}
		}
	);
}

function loadCategoriesOptions() {
	ajax({
			controller: RCON_SHOP_ADMIN_CONTROLLER,
			data: {
				loadCategoriesOptions: true,
				server: $('#server').val()
			},
			success: (result) => {
				$('#category').html(result.data);
				loadProducts();
			}
		}
	);
}

function removeCategory(id) {
	if (!confirm(
		'Вся информация, связанная с данной категорией будет удалена. Продолжить?')) {
		return;
	}

	ajax(
		{
			controller: RCON_SHOP_ADMIN_CONTROLLER,
			data: {
				removeCategory: true,
				id: id
			},
			success: () => {
				$('#category' + id).remove();
			},
			progress: true
		}
	);
}

function updateCategory(id) {
	ajax(
		{
			controller: RCON_SHOP_ADMIN_CONTROLLER,
			data: {
				updateCategory: true,
				id: id,
				title: $('#category_title' + id).val()
			},
			inputs: {
				title: 'category_title' + id
			},
			progress: true
		}
	);
}

function addCommandParam(name = '', title = '') {
	let paramsInput = $('#command-params-count');
	let paramId = paramsInput.val();

	$('#command-params').append(
		'<div class="row mb-10" id="command-param' + paramId + '">' +
		'	<div class="col-md-5">' +
		'		<input name="param-name' + paramId + '" id="param-name' + paramId + '" value="' + name + '" class="form-control w-100" placeholder="Пример: {nick}">' +
		'	</div>' +
		'	<div class="col-md-5">' +
		'		<input name="param-title' + paramId + '" id="param-title' + paramId + '" value="' + title + '" class="form-control w-100" placeholder="Пример: Ник">' +
		'	</div>' +
		'	<div class="col-md-2">' +
		'		<button class="btn btn-default btn-block" onClick="removeCommandParam(' + paramId + ');">' +
		'			Удалить' +
		'		</button>' +
		'	</div>' +
		'</div>'
	);

	paramsInput.val(paramId * 1 + 1);
}

function removeCommandParam(paramId) {
	$('#command-param' + paramId).remove();
}

function addTarif(price = '', title = '', command = '') {
	let tarifsCountInput = $('#tarifs-count');
	let tarifId = tarifsCountInput.val();

	$('#tarifs').append(
		'<div class="row mb-10" id="tarif' + tarifId + '">' +
		'	<div class="col-md-2">' +
		'		<input name="tarif-price' + tarifId + '" id="tarif-price' + tarifId + '" value="' + price + '" class="form-control w-100" placeholder="Пример: 20">' +
		'	</div>' +
		'	<div class="col-md-3">' +
		'		<input name="tarif-title' + tarifId + '" id="tarif-title' + tarifId + '" value="' + title + '" class="form-control w-100" placeholder="Пример: 100 кредитов">' +
		'	</div>' +
		'	<div class="col-md-5">' +
		'		<input name="tarif-command' + tarifId + '" id="tarif-command' + tarifId + '" value="' + command + '" class="form-control w-100" placeholder="Пример: amx_give gold 100 {steam}">' +
		'	</div>' +
		'	<div class="col-md-2">' +
		'		<button class="btn btn-default btn-block" onClick="removeTarif(' + tarifId + ');">' +
		'			Удалить' +
		'		</button>' +
		'	</div>' +
		'</div>'
	);

	tarifsCountInput.val(tarifId * 1 + 1);
}

function removeTarif(paramId) {
	$('#tarif' + paramId).remove();
}

function saveProduct(id = 0) {
	let params = {};

	for (let [name, value] of (new FormData($('#command-params').get(0)))) {
		params[name] = value;
	}

	let tarifs = {};

	for (let [name, value] of (new FormData($('#tarifs').get(0)))) {
		tarifs[name] = value;
	}

	ajax(
		{
			controller: RCON_SHOP_ADMIN_CONTROLLER,
			data: {
				saveProduct: true,
				id: id,
				category: $('#category').val(),
				status: $('#status').val(),
				title: $('#title').val(),
				image: $('#image')[0].files[0],
				isHasTarifs: $('#is-has-tarifs').val(),
				'tarif-price': $('#tarif-price').val(),
				'tarif-command': $('#tarif-command').val(),
				description: tinymce.get('description').getContent(),
				...params,
				...tarifs
			},
			inputs: {
				isHasTarifs: 'is-has-tarifs'
			},
			progress: true,
			processData: false,
			success: () => {
				if(id === 0) {
					loadProducts();
				}
			}
		}
	);
}

function removeProduct(id) {
	ajax({
			controller: RCON_SHOP_ADMIN_CONTROLLER,
			data: {
				removeProduct: true,
				id: id
			},
			success: () => {
				$('#product' + id).remove();
			}
		}
	);
}

function loadProducts() {
	ajax({
			controller: RCON_SHOP_ADMIN_CONTROLLER,
			data: {
				loadProducts: true,
				category: $('#category').val()
			},
			success: (result) => {
				if(result.data) {
					$('#products').html(result.data);
				} else {
					$('#products').empty();
				}
			}
		}
	);
}

function loadBuys() {
	ajax({
			controller: RCON_SHOP_ADMIN_CONTROLLER,
			data: {
				loadBuys: true,
				server: $('#server').val(),
				category: $('#category').val(),
				limit: $('#limit').val(),
			},
			success: (result) => {
				$('#operations').html(result.data);
			}
		}
	);
}