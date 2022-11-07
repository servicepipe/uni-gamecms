function addModalViewer() {
	NProgress.start();
	var data = {};
	data['add_modal_viewer'] = '1';
	data['textTitle'] = $('#textTitle').val();
	data['textMessage'] = $('#textMessage').val();
	data['valueTimelife'] = $('#valueTimelife').val();
	data['valueAuth'] = $('#valueAuth').val();
	
	$.ajax({
		type: "POST",
		url: "../modules_extra/modal_viewer/ajax/actions.php",
		data: create_material(data),
		success: function(html) {
			NProgress.done();
			$("#resultAddModalViewer").html(html);
		}
	});
}