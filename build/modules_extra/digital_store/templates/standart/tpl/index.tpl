<div class="col-lg-9 order-is-first">
    <div class="row">
		{products}
	</div>
</div>

<div class="col-lg-3 order-is-last">
        <div class="block">
            <div class="block_head">
                Категории
            </div>
            <div class="vertical-navigation">
                <ul>
                    {categories}
                </ul>
            </div>
        </div>

    {if(is_auth())}
        {include file="/home/navigation.tpl"}
        {include file="/home/sidebar_secondary.tpl"}
    {else}
        {include file="/index/authorization.tpl"}
        {include file="/index/sidebar_secondary.tpl"}
    {/if}
</div>