<div class="block dark fadeIn">
    {if('{completed}' == '1')}
		<svg version="1.1" xmlns="http://www.w3.org/2000/svg" style="width: 0;height: 0">
			<filter id="blur">
				<feGaussianBlur stdDeviation="3"/>
			</filter>
		</svg>
		<div class="completed_donate">
			<div class="centered">
				<span class="text">Сбор окончен!</span>
                {if('{showlist}' == '1' && '{curr_amount}' != 0)}
					<div id="dw_users_tab_btn" role="tablist" aria-multiselectable="true">
						<div class="card">
							<div class="card-header" role="tab" id="dw_users" data-toggle="collapse" data-parent="#dw_users_tab_btn" href="#dw_users_tab" aria-expanded="true" aria-controls="dw_users_tab">
								<h5 class="mb-0">Спасибо этим ребятам:</h5>
							</div>
							<div id="dw_users_tab" class="collapse" role="tabpanel" aria-labelledby="dw_users">
								<div class="card-block scroll-body">
									<div id="dw_users">
                                        {donations}
									</div>
								</div>
							</div>
						</div>
					</div>
                {/if}
			</div>
		</div>
		<style>
			.dw_content {
				-webkit-filter: blur(3px);
				-moz-filter: blur(3px);
				-o-filter: blur(3px);
				-ms-filter: blur(3px);
				filter: blur(3px);
				filter: progid:DXImageTransform.Microsoft.Blur(PixelRadius='3');
				pointer-events: none;
			}

			#dw_users a div {
				font-size: 14px !important;
			}

			@media (max-width: 992px) {
				#dw_users_tab_btn {
					max-width: 300px;
					padding-left: 20px;
				}
			}
		</style>
    {/if}
	<div class="vertical-center-line dw_content">
		<div class="row">
			<div class="col-lg-6">
				<div class="block_head">Пожертвование проекту</div>
				<div class="t-a-c">
					<div class="m-b-10">Цель: <span id="dw_target" class="dw_target-sum">{target_desc}</span></div>
					Собрано: <span id="dw_sum" class="dw_target-sum">{curr_amount}Р из {target_amount}Р</span>
				</div>
				<div class="c100 center p{curr_barpercent}">
					<span>{curr_percent}%</span>
					<div class="slice">
						<div class="bar"></div>
						<div class="fill"></div>
					</div>
				</div>
                {if('{completed}' == '0')}
					<div class="form-group">
						<div class="input-group">
                            {if('{comments}' == '1')}
								<input type="number" class="form-control" id="dw_amount" required="" minlength="1" maxlength="5" min="0" max="999999" autocomplete="off" placeholder="₽" style="max-width: 15%;padding: 0" {if('{completed}' == '1')}disabled{/if}>
								<input type="text" class="form-control" id="dw_comment" required="" maxlength="60" autocomplete="off" placeholder="Комментарий" {if('{completed}' == '1')}disabled{/if}>
                            {else}
								<input type="number" class="form-control" id="dw_amount" required="" minlength="1" maxlength="5" min="0" max="999999" autocomplete="off" placeholder="₽" style="padding: 0" {if('{completed}' == '1')}disabled{/if}>
                            {/if}
							<div class="input-group-prepend">
								<button id="send_button_donate" class="btn btn-outline-primary" type="button" onclick="dw_donate()" {if('{completed}' == '1')}disabled{/if}>Помочь</button>
							</div>
						</div>
						<div id="dw_result"></div>
					</div>
                {/if}
                {if('{showlist}' == '1' && '{curr_amount}' != 0)}
					<div id="dw_users_tab_btn" role="tablist" aria-multiselectable="true">
						<div class="card">
							<div class="card-header" role="tab" id="dw_users" data-toggle="collapse" data-parent="#dw_users_tab_btn" href="#dw_users_tab" aria-expanded="true" aria-controls="dw_users_tab">
                                {if('{limit}' > 0)}
									<h5 class="mb-0">Последние меценаты</h5>
                                {else}
									<h5 class="mb-0">Меценаты</h5>
                                {/if}
							</div>
							<div id="dw_users_tab" class="collapse" role="tabpanel" aria-labelledby="dw_users">
								<div class="card-block scroll-body">
									<div id="dw_users">
                                        {donations}
									</div>
								</div>
							</div>
						</div>
					</div>
                {/if}
			</div>
			<div class="col-lg-6">
				<div class="block_head">Описание</div>
				<div id="dw_text" class="dw_text">
					Здравствуй, любимый игрок! От <b>твоего пожертвования</b> зависит судьба
					<strong>нашего</strong> проекта!<br><br>
					Наш проект существует продолжительное время, мы интенсивно работаем над его улучшением и общим видом.
					Многие заблуждаются, когда говорят, что проект может прожить без вложений. Это не так.
					Именно сегодня мы обьявляем сбор <b>{target_amount}₽</b> на
					<b>{target_desc}</b>{if('{autostop}' == '2')} до <b>{stopdate}</b>{/if}.<br><br>
					Все средства пойдут на развитие проекта,
					<strong>каждый взнос будет в истории проекта</strong>, спасибо!
				</div>
			</div>
		</div>
	</div>
</div>
