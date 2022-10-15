{if('{exists}' == '1')}
	{if('{finished}' == '1')}
		<div class="block sortition">
			<div class="block_head">
				Розыгрыш завершен
			</div>

			<div class="row">
				<div class="col-md-2"></div>
				<div class="col-md-8">
					<h6>Победитель(и)</h6>
					<div id="winners">
						<script>get_winners();</script>
					</div>
				</div>
				<div class="col-md-2"></div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-2"></div>
				<div class="col-md-8">
					<h4>Призы розыгрыша</h4>
					<div id="prizes">
						<script>get_prizes();</script>
					</div>
				</div>
				<div class="col-md-2"></div>
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

			<a href="../sortition" class="read-more">Подробнее...</a>
		</div>
	{/if}
</div>
{else}
	<div class="block sortition">
		<div class="block_head">
			Розыгрыш
		</div>
		<div class="noty-block info mb-0">
			Активных розыгрышей нет
		</div>
	</div>
{/if}