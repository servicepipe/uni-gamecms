<?php $profile = Users::getUserData($pdo, $_SESSION['id']); 
	if($profile->steam_id === 0 || $profile->nick === '---')echo '
	<div class="block">
		<div>
		<div class="form-group">
					<label>
						<h4>
							<p class="color-fix">Никнейм</p>
						</h4>
					</label>
					<div class="input-group">
						<div class="input-group-prepend">
							<button class="btn btn-outline-primary" type="button" onclick="edit_user_nick();">Изменить</button>
						</div>
						<input type="text" class="form-control" id="user_nick" maxlength="30" autocomplete="off" value=" '.$profile->nick.' " placeholder="Введите свой ник">
					</div>
					<div id="edit_user_nick_result"></div>
				</div>

				<div class="form-group">
					<label>
						<h4>
							<p class="color-fix">
							Steam ID
							<button data-toggle="modal" title="Правила чата" class="btn btn-outline-dark btn-sm" href="#steamid">
							<i class="fa fa-info-circle" style="position: relative; left: 1px;" aria-hidden="true"></i>
							</button></p>							
						</h4> 
					</label>
					<div class="input-group">
						<div class="input-group-prepend">
							<button class="btn btn-outline-primary" type="button" onclick="edit_user_steam_id();">Изменить</button>
						</div>
						<input type="text" class="form-control" id="user_steam_id" maxlength="32" autocomplete="off" value=" ' .$profile->steam_id. ' " placeholder="Введите свой Steam ID">
					</div>
					<div id="edit_user_steam_id_result"></div>
				</div>			
		</div>	
	</div>';
?>

<script>$('steamid').modal('hide');</script>

	<div id="steamid" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<font style="word-break: break-all">
					Необходимо зайти на игровой сервер, после чего вписать в консоли сервера слово <span style="color:#e67e23;">status</span>, далее находим там себя, и копируем свой <span style="color:#3598db;">SteamID</span>
					<br><br>					
					<img src="../files/other/steamid.png" class="img-fluid" alt="status" style="width: 100%;border-radius: 10px;">
					<br><br>
					Далее заходим в настройках своего <a href="../settings" target="_blank">профиля</a> и указываем его там, именно так как показано на примере
					<center>
					<a href="../settings" target="_blank"><img src="../files/other/steamid_profile_wydget.png" class="img-fluid" alt="set_prefix"></a>				
					</center>
					И нажимаем кнопку <span style="color:#e67e23;">Изменить</span>					
					</font>
				</div>
			</div>
		</div>
	</div>	