/*
* Операции с категориями
* */
function load_categories(type) {
    type = type || 0;

    var data = {};
	data['phpaction'] = '1';
    data['load_categories'] = '1';
    data['phpaction'] = '1';
    data['token'] = $("#token").val();
	
    $.ajax({
        type: "POST",
        url: "../modules_extra/digital_store/ajax/actions.php",
        data: create_material(data),
        dataType: "json",

        success: function (result) {
            $("#categories").html(result.data);
            // noinspection JSUnresolvedVariable
            $("#category").html(result.data_2);

            if(type == 1) {
                load_products();
            }
        }
    });
}

function add_category() {
    var data = {};
	data['phpaction'] = '1';
    data['add_category'] = '1';
    data['name'] = $("#category_name").val();
	data['token'] = $("#token").val();
	
    $.ajax({
        type: "POST",
        url: "../modules_extra/digital_store/ajax/actions.php",
        data: create_material(data),
        dataType: "json",

        success: function (result) {
            if (result.status == '1') {
                load_categories();
            } else {
                show_input_error(result.input, result.data);
            }
        }
    });
}

// noinspection JSUnusedGlobalSymbols
function dell_category(id) {
    if (confirm("Вся информация, связанная с данной категорией будет удалена. Удалить?")) {
        var data = {};
		data['phpaction'] = '1';
        data['dell_category'] = '1';
        data['id'] = id;
		data['token'] = $("#token").val();
		
        $.ajax({
            type: "POST",
            url: "../modules_extra/digital_store/ajax/actions.php",
            data: create_material(data),

            success: function () {
                reset_page();
            }
        });
    }
}

/*
* Операции с продуктами
* */
function load_products() {
    if(typeof tinymce != "undefined") {
        tinymce.remove();
    }

    init_tinymce('description', tiny_code, 'full');

    var data = {};
	data['phpaction'] = '1';
    data['load_products'] = '1';
    data['category'] = $("#category").val();
	data['token'] = $("#token").val();
	
    $.ajax({
        type: "POST",
        url: "../modules_extra/digital_store/ajax/actions.php",
        data: create_material(data),

        success: function (html) {
            $("#products").html(html);
        }
    });
}

function product_action(index) {
	NProgress.start();
	
	index = index || '';
	
	var fd = new FormData;
	fd.append('phpaction', '1');
	fd.append('token', $("#token").val());
	
	fd.append('product_action', '1');
	fd.append('id', index);
	
	fd.append('category', $("#category").val());
	fd.append('name', $("#name" + index).val());
	fd.append('price', $("#price" + index).val());
	fd.append('image', $("#image" + index)[0].files[0]);
	fd.append('description', tinymce.get("description" + index).getContent());
	
	$.ajax({
		type: "POST",
		url: "../modules_extra/digital_store/ajax/actions.php",
		processData: false,
		contentType: false,
		data: fd,
		dataType: 'json',
		success: function(result) {
			NProgress.done();
			
            if(result.status == '1') {
                load_products();
                setTimeout(show_ok, 500);
            }
			else {
                show_input_error(result.input+index, result.data);
                setTimeout(show_error, 500);
            }
		}
	});
}

function dell_product(id) {
    if (confirm("Вся информация, связанная с данным продуктом будет удалена. Удалить?")) {
        var data = {};
		data['phpaction'] = '1';
        data['dell_product'] = '1';
        data['id'] = id;
		data['token'] = $("#token").val();
		
        $.ajax({
            type: "POST",
            url: "../modules_extra/digital_store/ajax/actions.php",
            data: create_material(data),

            success: function () {
                load_products();
            }
        });
    }
}


/*
* Операции с содержимым продукта
* */
function load_product_keys() {
    if(typeof tinymce != "undefined") {
        tinymce.remove();
    }

    init_tinymce('content', tiny_code, 'full');

    var data = {};
	data['phpaction'] = '1';
    data['load_product_keys'] = '1';
    data['product'] = $('#product').val();
	data['token'] = $("#token").val();
	
    $.ajax({
        type: "POST",
        url: "../modules_extra/digital_store/ajax/actions.php",
        data: create_material(data),

        success: function (html) {
            $("#product_keys").html(html);
        }
    });
}

function product_key_action(id) {
    NProgress.start();
    id = id || '';

    var data = {};
	data['phpaction'] = '1';
    data['product_key_action'] = '1';
    data['id'] = id;
    data['product'] = $('#product').val();
    data['content'] = tinymce.get("content"+id).getContent();
	data['token'] = $("#token").val();
	
    $.ajax({
        type: "POST",
        url: "../modules_extra/digital_store/ajax/actions.php",
        data: create_material(data),
        dataType: "json",

        success: function (result) {
            NProgress.done();
            if (result.status == '1') {
                if(id == '') {
                    load_product_keys();
                }
                setTimeout(show_ok, 500);
            } else {
                show_input_error(result.input+id, result.data);
                setTimeout(show_error, 500);
            }
        }
    });
}

function dell_product_key(id) {
    if (confirm("Уверены?")) {
        var data = {};
		data['phpaction'] = '1';
        data['dell_product_key'] = '1';
        data['id'] = id;
		data['token'] = $("#token").val();
		
        $.ajax({
            type: "POST",
            url: "../modules_extra/digital_store/ajax/actions.php",
            data: create_material(data),

            success: function () {
                $('#product_key'+id).fadeOut();
            }
        });
    }
}

/*
 * Загрузка магазина
 */
function laod_digital_store(category) {

    var data = {};
	data['phpaction'] = '1';
    data['laod_digital_store'] = '1';
    data['category'] = category;
	data['token'] = $("#token").val();
	
    $.ajax({
        type: "POST",
        url: "../modules_extra/digital_store/ajax/actions.php",
        data: create_material(data),

        success: function (html) {
            $("#digital_store").html(html);
        }
    });
}

function buy_product_key(product, price) {
    if (confirm("С вашего баланса будет списано "+price+" рублей, продолжить?")) {
        var data = {};
		data['phpaction'] = '1';
        data['buy_product_key'] = '1';
        data['product'] = product;
		data['token'] = $("#token").val();
		
        $.ajax({
            type: "POST",
            url: "../modules_extra/digital_store/ajax/actions.php",
            data: create_material(data),
            dataType: "json",

            success: function (result) {
                $('#buy_modal_data').html(result.data);
                $('#buy_modal').modal('show');
                if (result.status == '1') {
                    $('#balance').html(result.shilings);
                    $('#keys_count').html(result.count);
                }
            }
        });
    }
}

function load_sales(load_val) {
    var data = {};
	data['phpaction'] = '1';
    data['load_sales'] = '1';
    data['load_val'] = load_val;
	data['token'] = $("#token").val();
	
    $.ajax({
        type: "POST",
        url: "../modules_extra/digital_store/ajax/actions.php",
        data: create_material(data),

        success: function (html) {
            if (load_val == 'first') {
                $("#sales").html(html);
            } else {
                dell_block("loader" + load_val);
                $("#sales").append(html);
            }
        }
    });
}