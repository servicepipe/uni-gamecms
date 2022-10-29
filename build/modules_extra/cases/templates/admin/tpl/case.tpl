<div class="page">
	<div class="col-md-12">
		<div class="block">
			<div class="block_head">
				Редактирование кейса
			</div>
			<div class="row">
				<div class="col-md-6">
					<b>Название кейса</b>
					<input type="text" class="form-control mt-5" value="{name}" id="name_{id}" placeholder="Название" maxlength="256">
					<br>

					<b>Цена открытия</b>
					<input type="text" class="form-control" value="{price}" id="price_{id}" placeholder="Цена" maxlength="9">
					<br>

					<p><b>Изображение кейса</b></p>
					<input type="hidden" class="form-control" id="image_{id}" placeholder="Номер" maxlength="512" value="{image_id}">
					<a id="case_{id}_image" href="../{image}" class="thumbnail case-image" data-lightbox="{id}">
						<img src="../{image}" class="thumbnail-img">
					</a><br>
					<button class="btn2 btn-cancel mt-5" onclick="get_cases_images({id});" type="button">Выбрать другое изображение</button>
				</div>
				<div class="col-md-6">
					<b>Предметы кейса</b>
					<form id="subjects_{id}" class="mt-5">
						<script>get_subjects({id});</script>
					</form>
					<input type="hidden" value="0" id="subject_count_{id}">

					<div class="bs-callout bs-callout-info mt-10">
						<p id="chance_sum_noty_{id}">Сумма шансов выпадения всех предметов должна равняться 100%, сейчас: <b id="chance_sum_{id}">0</b></p>
					</div>
					<button class="btn2 mt-10" onclick="add_subject({id});" type="button">Добавить предмет</button>
				</div>
			</div>

			<hr>

			<button class="btn2 btn-lg" onclick="save_case({id});" type="button">Сохранить</button>
		</div>
	</div>
</div>
<div id="cases_images_modal" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Изображения кейсов</h4>
			</div>
			<div class="modal-body">
				<p><b>Загрузить свое изображение</b></p>
				<form enctype="multipart/form-data" id="img_form">
					<input type="hidden" name="token" value="{token}">
					<input type="hidden" name="load_case_image" value="1">
					<input type="hidden" name="phpaction" value="1">
					<input type="hidden" name="case_id" value="{id}">
					
					<div class="form-group file-load">
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit">Загрузить</button>
							</span>
							<input class="form-control" type="file" accept="image/*" name="image">
						</div>
						<div id="img_result"></div>
					</div>
				</form>
				<hr>
				<div id="cases_images"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<script>
$("#img_form").submit(function (event){
	NProgress.start();
	event.preventDefault();
	var data = new FormData($('#img_form')[0]);
	$.ajax({
		type: "POST",
		url: "../modules_extra/cases/ajax/actions.php",
		data: data,
		contentType: false,
		processData: false,
	}).done(function (html) {
		$("#img_result").empty();
		$("#img_result").append(html);
		$('#img_form')[0].reset();
	});
	NProgress.done();
});
function add_subject(case_id) {
	case_id = case_id || 0;

	var place = $('#subject_count_'+case_id).val();
	$('#subjects_'+case_id).append('<div id="subject_'+place+'" class="subject-block">\
										<b>Предмет (<a onclick="dell_subject('+case_id+', '+place+')" class="c-p">Удалить</a>)</b>\
										<input type="hidden" value="0" id="count_'+place+'">\
										<input class="form-control" name="chance_'+place+'" id="chance_'+place+'" placeholder="Шанс выпадения (0 - 100%)" value="" type="number" onchange="calculate_chance_sum('+case_id+');">\
										<div id="services_'+place+'"></div>\
										<div class="input-group">\
											<span class="input-group-btn">\
												<button class="btn btn-default" type="button" onclick="get_subject_line('+case_id+', '+place+');">Добавить</button>\
											</span>\
											<select class="form-control" id="type_'+place+'">\
											{if($subjects_types[1] == 1)}\
											<option value="1">Услугу</option>\
											{/if}\
											{if($subjects_types[2] == 1)}\
											<option value="2">Денежный приз</option>\
											{/if}\
											{if($subjects_types[3] == 1)}\
											<option value="3">Скидку</option>\
											{/if}\
											{if($subjects_types[4] == 1)}\
											<option value="4">Приз из shop_key</option>\
											{/if}\
											{if($subjects_types[5] == 1)}\
											<option value="5">Приз из buy_key</option>\
											{/if}\
											{if($subjects_types[6] == 1)}\
											<option value="6">Приз из vip_key_ws</option>\
											{/if}\
											</select>\
										</div>\
									</div>');
	$('#subject_count_'+case_id).val(Number(place) + 1);
}
</script>