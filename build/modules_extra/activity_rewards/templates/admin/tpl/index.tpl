<style>
	#rewards .card {
		display: block;
		padding: 10px;
		border: 1px solid RGB(221, 221, 221);
		border-radius: 3px;
		margin-bottom: 10px;
	}

	#rewards .card .card-header {
		padding: 10px;
		margin: -10px -10px 10px;
		background: rgb(247, 247, 247);
		border-bottom: 1px solid #DDD;
	}

	#rewards .card .card-header .btn {
		float: right;
		margin-top: -5px;
		opacity: 0.7;
	}

	#rewards .card p {
		margin-bottom: 0;
	}

	#rewards .card .form-control {
		margin-bottom: 10px;
	}
</style>

<div class="page">
	<div class="col-md-12">
		<div class="block">
			<div class="block_head">Общие настройки</div>

			<b>Выдавать повторно уже выданные призы, если у пользователя сбрасывался прогресс</b>
			<select class="form-control" id="is-re-issue">
				<option {if('{isReIssue}' == '0')} selected {/if} value="0">Не выдавать</option>
				<option {if('{isReIssue}' == '1')} selected {/if} value="1">Выдавать</option>
			</select>
			<br>
			<b>Кому будут выдаваться призы</b>
			<select class="form-control" id="is-need-money-activity" onchange="onChangeMoneyActivity();">
				<option {if('{isNeedMoneyActivity}' == '0')} selected {/if} value="0">Всем</option>
				<option {if('{isNeedMoneyActivity}' == '1')} selected {/if} value="1">Тем, кто пополнял баланс</option>
			</select>

			<div id="amount-of-money-area" style="display: none">
				<br>
				<b>На какую сумму необходимо пополнить баланс</b>
				<input type="number" class="form-control" value="{amountOfMoney}" id="amount-of-money">
			</div>

			<script>
              function onChangeMoneyActivity () {
                if ($('#is-need-money-activity').val() === '0') {
                  $('#amount-of-money-area').fadeOut(0);
                } else {
                  $('#amount-of-money-area').fadeIn(0);
                }
              }

              onChangeMoneyActivity();
			</script>

			<button class="btn2 mt-10" onclick="saveActivityRewardsConfig();" type="button">Сохранить</button>
		</div>

		<div class="block">
			<div class="block_head">Награды</div>

			<form id="rewards">
				<script>getRewards();</script>
			</form>

			<input type="hidden" value="0" id="rewards-last-id">
			<button class="btn2 mt-10" onclick="addReward();" type="button">Добавить</button>

			<hr>

			<button class="btn2" onclick="saveRewards();" type="button">Сохранить</button>
		</div>
	</div>
</div>

<script>
  function addReward () {
    let rewardsLastId = $('#rewards-last-id').val();
    rewardsLastId = Number(rewardsLastId) + 1;

    $('#rewards').append(
      '<div class="card" id="reward' + rewardsLastId + '">\
			<div class="card-header">\
				<span>Награда</span>\
                <a class="btn btn-danger btn-sm" onclick="dellReward(' + rewardsLastId + ')">Удалить</a>\
            </div>\
            <div class="card-body">\
				<p class="card-text">Количество дней, которое пользователь должен заходить подряд</p>\
				<input class="form-control" name="day-in-row' + rewardsLastId + '" id="day-in-row' + rewardsLastId + '" placeholder="Количество дней" type="number">\
				<p class="card-text">Тип награды</p>\
				<select class="form-control" id="type' + rewardsLastId + '" name="type' + rewardsLastId + '" onchange="getRewardLine(' + rewardsLastId + ')">\
					{if($rewardsTypes[1] == 1)}\
						<option value="1">Услугу</option>\
					{/if}\
					{if($rewardsTypes[2] == 1)}\
						<option value="2" selected>Денежный приз</option>\
					{/if}\
					{if($rewardsTypes[3] == 1)}\
						<option value="3">Скидку</option>\
					{/if}\
					{if($rewardsTypes[4] == 1)}\
						<option value="4">Приз из shop_key (Riko)</option>\
					{/if}\
					{if($rewardsTypes[5] == 1)}\
						<option value="5">Приз из buy_key (Riko)</option>\
					{/if}\
					{if($rewardsTypes[6] == 1)}\
						<option value="6">Приз из vip_key (Riko)</option>\
					{/if}\
					{if($rewardsTypes[7] == 1)}\
						<option value="7">Приз из vip_key (MyArena)</option>\
					{/if}\
				</select>\
				<p class="card-text">Награда</p>\
				<div class="input-group w-100" id="reward-line' + rewardsLastId + '"></div>\
            </div>\
        </div>'
    );

    getRewardLine(rewardsLastId);
    $('#rewards-last-id').val(rewardsLastId);
  }
</script>