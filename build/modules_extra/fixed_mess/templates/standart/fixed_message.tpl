<style>
	#chat #chat_messages .chat_message .fixed_message,
	#chat #messages .chat_message .fixed_message {
		color: #1a1c20;
		position: absolute;
		top: 20px;
		font-size: 11px;
		cursor: pointer;
	}

	#chat #chat_messages .chat_message .fixed_message,
	#chat #messages .chat_message .fixed_message {
		right: 45px;
	}

	.fixed_chat_message {
    		position: relative;
    		overflow: hidden;
    		padding: 10px;
    		border-radius: 5px;
	}
	.fixed_chat_message .user_img {
		float: left;
		border-radius: 50%;
		height: 42px;
		width: 42px;
		padding: 2px;
		background-color: #2F3553;
	}
	.fixed_chat_message .message {
		word-wrap: break-word;
		margin-left: 60px;
		position: relative;
		overflow: hidden;
		font-size: 13px;
		line-height: 18px;
	}
	.fixed_chat_message .message .info {
		display: block;
		margin-bottom: 3px;
		height: 16px;
	}
	.fixed_chat_message .message .info .author {
		font-size: 13px;
		color: #45688E;
		font-weight: normal;
		float: left;
		position: relative;
		cursor: pointer;
	}
	.fixed_chat_message .message .info .date {
		font-size: 15px;
		font-weight: 600;
		position: relative;
		float: right;
		height: 20px;
		width: 20px;
		text-align: center;
	}
	.fixed_chat_message textarea {
		margin-top: 15px;
	}
</style>
<div class="fixed_chat_message" id="message_id_{id}" style="background:#313346;">
    <a href="../profile?id={user_id}" title="{gp_name}">
        <img class="user_img" src="{avatar}" alt="{login}">
    </a>
    <div class="message">
        <div class="info">
			{if(strripos("{gp_rights}", "d") !== false)}
			<div style="position: absolute;left: 630px;">
				<span onclick="fixed_chat_message('{id}', '2');" tooltip="yes" data-placement="left" title="Открепить" class="fa fa-chevron-down fixed_message"></span>
				<span id="edit_message_{id}" onclick="edit_chat_message('{id}', this);" tooltip="yes" data-placement="left" title="Редактировать" class="m-icon icon-pencil edit_message"></span>
				<span onclick="dell_chat_message('{id}');" tooltip="yes" data-placement="left" title="Удалить" class="fa fa-trash dell_message"></span>
			</div>
			{/if}
            <div class="author" onclick="treatment('{login}');" title="{gp_name}" style="color: {gp_color}">{login}</div>
            <div class="date" tooltip="yes" data-placement="left" title="" data-original-title="Сообщение закреплено" style="background:#77312D;border-radius:3px;color:#baa69c;"><i class="fa fa-paperclip" aria-hidden="true"></i></div>
        </div>
        <div id="message_text_{id}" style="padding-top:10px;">
             <span style="color:#5CD65C;">{text}</span>
        </div>
		{if(strripos("{gp_rights}", "d") !== false)}
			<textarea id="message_text_e_{id}" class="form-control disp-n">{text}</textarea>
		{/if}
    </div>
</div>