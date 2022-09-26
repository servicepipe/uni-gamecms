<div class="col-lg-9 order-is-first">
	<div class="block new">
		<div class="block_head">
			{name}
		</div>
			
		<div class="with_code">
			{text}
		</div>
		<br />
		<p class="news_page">
			<span>Просмотров: {views}</span><br>
			<span>Дата создания: {date}</span><br>
			<span>Категория: <a href="../news/?class={class_id}">{class}</a></span><br>
			<span>Автор: <a href="../profile?id={author_id} ">{author_login}</a></span>
		</p>
		<!-- Социальные кнопки -->
		<script src="https://yastatic.net/share2/share.js"></script>
			<div class="ya-share2" data-curtain data-size="s" data-shape="round" data-limit="17" data-services="messenger,vkontakte,odnoklassniki,telegram,twitter,viber,whatsapp,moimir,skype,tumblr,evernote,linkedin,lj,blogger,delicious,digg"></div>
		<!-- Социальные кнопки -->
	</div>

	<div class="block">
		<div class="block_head">
			Комментарии
		</div>

		{if(is_auth())}
		<textarea id="text" maxlength="500"></textarea>
		<div class="smile_input_forum mt-3">
			<input id="send_btn" class="btn btn-primary" type="button" onclick="send_new_comment({id});" value="Отправить">
			<div id="smile_btn" class="smile_btn" data-container="body" data-toggle="popover" data-placement="top" data-content="empty"></div>
		</div>
		<script>
			$(document).ready(function() {
				init_tinymce("text", "lite", "{file_manager_theme}", "{file_manager}", "{{md5($conf->code)}}");
				get_smiles('#smile_btn');
			});

			$('#smile_btn').popover({ html: true, animation: true, trigger: "click" });
			$('#smile_btn').on('show.bs.popover', function () {
				$(document).mouseup(function (e) {
					var container = $(".popover-body");
					if (container.has(e.target).length === 0){
						$('#smile_btn').popover('hide');
						selected = 'gcms_smiles';
					}
				});
			});

			function set_sticker(elem){
				var text = "sticker"+$(elem).attr("src");
				text = encodeURIComponent(text);
				send_new_comment({id}, text);
				$('#smile_btn').popover('hide'); selected = 'gcms_smiles';
			}
			function set_smile(elem){
				var smile =  "<img src=\""+$(elem).attr("src")+"\" class=\"g_smile\" height=\"20px\" width=\"20px\">";
				tinymce.activeEditor.insertContent(smile);
				$('#smile_btn').popover('hide');
				selected = 'gcms_smiles';
			}
		</script>
		{/if}

		<div id="comments" class="mt-3">
			<div class="loader"></div>
			<script>load_new_comments("{id}");</script>
		</div>
	</div>
</div>

<div class="col-lg-3 order-is-last">
	{if(is_worthy("q"))}
	<div class="block">
		<a href="../news/change_new?id={id}" class="btn btn-outline-primary btn-xl">Редактировать</a>
		<a href="#" onclick="dell_new('{id}','2');" class="btn btn-outline-primary btn-xl mt-2">Удалить</a>
	</div>
	{/if}

	{if(is_auth())}
		{include file="/home/navigation.tpl"}
	{else}
		{include file="/index/authorization.tpl"}
	{/if}

	<div class="block">
		<div class="block_head">
			Последние новости
		</div>
		<div id="news">
			{func Widgets:others_news('{id}', '5')}
		</div>
	</div>
</div>