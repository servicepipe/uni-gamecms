<div class="col-lg-9 order-is-first">
	<div id="sortition">
		<div class="loader"></div>
		<script>get_sortition();</script>
	</div>
</div>

<div class="col-lg-3 order-is-last">
	{if(is_auth())}
		{include file="/home/navigation.tpl"}
		{include file="/home/sidebar_secondary.tpl"}
	{else}
		{include file="/index/authorization.tpl"}
		{include file="/home/sidebar_secondary.tpl"}
	{/if}
</div>