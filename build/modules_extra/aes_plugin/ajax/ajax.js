function load_aes_list(start,server,name,param){
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/aes_plugin/ajax/actions.php",
		data: "phpaction=1&load_aes_list=1&token="+token+"&start="+start+"&server="+server+"&name="+name,

		success: function(html) {
			$("#list").empty();
			$("#list").append(html);
		}
	});
}
function search_ban(server){
	var name = token = $('#search_ban').val();
	load_aes_list(0, server, name);
	if(name == ''){
		$('#pagination1').show();
		$('#pagination2').show();
	} else {
		$('#pagination1').hide();
		$('#pagination2').hide();
	}
}
function aes_load_servers(){
	var token = $('#token').val();
	$.ajax({
		type: "POST",
		url: "../modules_extra/aes_plugin/ajax/actions.php",
		data: "phpaction=1&load_servers=1&token="+token,

		success: function(html) {
			$("#servers").empty();
			$("#servers").append(html);
		}
	});
}
function aes_edit_server(id, clean){
	NProgress.start();
	var token = $('#token').val();
	var aes_host = $('#aes_host'+id).val();
	var aes_user = $('#aes_user'+id).val();
	var aes_pass = $('#aes_pass'+id).val();
	var aes_db = $('#aes_db'+id).val();
	var aes_table = $('#aes_table'+id).val();
	var aes_code = $('#aes_code'+id).val();
	aes_host = encodeURIComponent(aes_host);
	aes_user = encodeURIComponent(aes_user);
	aes_pass = encodeURIComponent(aes_pass);
	aes_db = encodeURIComponent(aes_db);
	aes_table = encodeURIComponent(aes_table);
	aes_code = encodeURIComponent(aes_code);
	$.ajax({
		type: "POST",
		url: "../modules_extra/aes_plugin/ajax/actions.php",
		data: "phpaction=1&edit_server=1&token="+token+"&aes_host="+aes_host+"&aes_user="+aes_user+"&aes_pass="+aes_pass+"&aes_db="+aes_db+"&aes_table="+aes_table+"&id="+id+"&clean="+clean+"&aes_code="+aes_code,

		success: function(html) {
			NProgress.done();
			$("#edit_serv_result"+id).empty();
			$("#edit_serv_result"+id).append(html);
			if(clean == 1) {
				$('#aes_host'+id).val('');
				$('#aes_user'+id).val('');
				$('#aes_pass'+id).val('');
				$('#aes_db'+id).val('');
				$('#aes_table'+id).val('');
				$('#aes_code'+id).val(0);
			}
		}
	});
}