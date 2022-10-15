{if('{exists}' == '1')}
	{if('{finished}' == '1')}
		<div class="block sortition">
			<div class="block_head">
				Розыгрыш завершен, победитель(и)
			</div>
			<div class="winners">
				<div id="winners">
					<script>get_winners();</script>
				</div>
			</div>
		</div>
	{else}
		<div class="block sortition">
			<div class="block_head">
				{name}
			</div>

			<div class="ending">
				<div id="ending">
					<script>get_ending_time(1);</script>
				</div>
			</div>

			{if('{declared}' != '0')}
				{if('{participants}' != '0')}
					<h3>Вы приняли участие в розыгрыше и входите в список участников: {declared_participants}/{participants}</h3>
				{else}
					<h3>Вы приняли участие в розыгрыше!</h3>
				{/if}
			{else}
				{if('{participants}' != '0' && '{participants}' == '{declared_participants}')}
					<h3>Принять участие в розыгрыше невозможно! Все места заняты: {declared_participants}/{participants}</h3>
				{else}
					{if(is_auth())}
						{if('{participants}' != '0')}
							<h3>Успей принять участие! Количество мест ограничено: {declared_participants}/{participants}</h3>
						{/if}
						<button class="participate" type="button" onclick="participate();">
							Принять участие {if('{price}' != '0')} - {price} {/if}
						</button>
						<div id="participate_result" class="mt-10"></div>
					{/if}
				{/if}
			{/if}
		</div>
	{/if}

	<div class="block sortition">
		<div class="block_head">
			Призы
		</div>
		<div id="prizes">
			<script>get_prizes();</script>
		</div>
	</div>

	{if('{show_participants}' == '1')}
		<div class="block sortition">
			<div class="block_head">
				Участие в розыгрыше приняли
			</div>
			<div id="participants">
				<script>get_participants();</script>
			</div>
		</div>
	{/if}
</div>
{else}
	<div class="block sortition">
		<div class="block_head">
			Розыгрыш
		</div>
		<div class="noty-block info mb-0 mb-5">
			Активных розыгрышей нет
		</div>
	</div>
{/if}