$uri = "/modules_extra/money_transfer/performers/actions.php";

$(function() {
	let uid = $("#money_transfer").data("user");
	
	if(uid) {
		getButton(uid);
	}
});

function getButton(uid) {
	send_post($uri, serializeform(new FormData, {getButton: 1, uid: uid}), (result) => {
		$("#money_transfer").html(result.content);
		$("a[data-user-id]").bind('click', function() {
			$.confirm({
				title: "Введите сумму перевода",
				content: '<input type="text" placeholder="Сумма перевода" class="count form-control">',
				buttons: {
					formSubmit: {
						text: "Перевести",
						btnClass: 'btn-blue',
						action: function () {
							var count = this.$content.find('.count').val();
							send_post($uri, serializeform(new FormData, {
								transfer: 1,
								uid: $("a[data-user-id]").data('user-id'),
								count: count
							}), (result) => {
								push(result.message, result.alert);
							});
						}
					},
					cancel: {
						text: "Отмена"
					}
				},
				onContentReady: function () {
					var jc = this;
					this.$content.find('form').on('submit', function (e) {
						e.preventDefault();
						jc.$$formSubmit.trigger('click');
					});
				}
			});
		});
	});
}