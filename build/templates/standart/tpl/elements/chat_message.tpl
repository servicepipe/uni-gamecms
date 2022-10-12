<?
	$frame = trading()->get_resource_active(3, '{user_id}');
?>

<div class="chat_message" id="message_id_{id}">
	<a href="../profile?id={user_id}" title="{gp_name}">
		{if(isset($frame))}
			<div class="playground ml-2">
				<div class="frame">
					<img class="rounded-0" src="/files/playground/{{$frame}}">
				</div>

				<img class="rounded-0" src="<?=convert_avatar('{user_id}');?>">
			</div>
		{else}
			<img src="<?=convert_avatar('{user_id}');?>" class="rounded-circle ml-2">
		{/if}
	</a>
	<div class="message">
		<div class="info">
			{if($very->is_very('{user_id}'))}
				<div class="author" onclick="treatment('{login}');" title="{gp_name}" style="color: {gp_color}">{login} </div><?echo $very->get_very_style('standart');?>
			{else}
				<div class="author" onclick="treatment('{login}');" title="{gp_name}" style="color: {gp_color}">{login}</div>
			{/if}
			
			<div class="date" tooltip="yes" data-placement="left" title="{date_full}">{date_short}</div>
			{if(strripos("{gp_rights}", "d") !== false)}
				<span onclick="fixed_chat_message('{id}', '1');" tooltip="yes" data-placement="left" title="Закрепить" class="fas fa-paperclip" style="margin-right: 15px;position: absolute;top: 13px;right: 17px;"></span>
				<span onclick="dell_chat_message('{id}');" tooltip="yes" data-placement="left" title="Удалить" class="m-icon icon-trash dell_message"></span>
				<span id="edit_message_{id}" onclick="edit_chat_message('{id}', this);" tooltip="yes" data-placement="left" title="Редактировать" class="m-icon icon-pencil edit_message"></span>
			{/if}
		</div>
		<div id="message_text_{id}" class="with_code">
			{text}
		</div>
		{if(strripos("{gp_rights}", "d") !== false)}
			<textarea id="message_text_e_{id}" class="form-control disp-n">{text}</textarea>
		{/if}
	</div>
</div>