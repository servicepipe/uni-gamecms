{if(!empty($servers))}
	<div class="block">
		<div class="block_head">
			Сервера
		</div>
		<div class="vertical-navigation">
			<ul>
				{for($l = 0; $l < count($servers); $l++)}
					<li {if($serverId == $servers[$l]->id)} class="active" {/if}>
						<a href="../shop?server={{$servers[$l]->id}}">
							{{$servers[$l]->name}}
						</a>
					</li>
				{/for}
			</ul>
		</div>
	</div>
{/if}

{if(!empty($categories))}
	<div class="block">
		<div class="block_head">
			Категории
		</div>
		<div class="vertical-navigation">
			<ul>
				{for($l = 0; $l < count($categories); $l++)}
					<li {if($categoryId == $categories[$l]->id)} class="active" {/if}>
						<a href="../shop?server={{$serverId}}&category={{$categories[$l]->id}}">
							{{$categories[$l]->title}}
						</a>
					</li>
				{/for}
			</ul>
		</div>
	</div>
{/if}