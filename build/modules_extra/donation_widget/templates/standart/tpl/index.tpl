<div class="col-lg-9 order-is-first">
    {if ("{started}" == "1")}
		<div id="dw_donations">
			<script>dw_donations();</script>
		</div>
    {else}
		<div class="block">
			<div class="block_head">Пожертвования</div>
			<div class="empty-element">Пожертвования закрыты.</div>
		</div>
    {/if}
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