<a href="../profile?id={id}">
	<img src="../{avatar}" alt="{login}">
	<div>
		<span style="color: {color};" title="{name}">{login}</span>
		<p>
			<span><i class="fas fa-ruble-sign"></i> {amount}</span>
            {if('{comments}' == '1')}
				<span title="Комментарий"><i class="fas fa-envelope"></i> {comment}</span>
            {/if}
		</p>
	</div>
</a>
