<div class="col-lg-9 order-is-first">
    <div class="block block-search">
        <div class="block_head">Демо записи</div>
        {if('{error}' == '')}
            <div class="input-search">
                <i class="fas fa-search" onclick="search_demo({server})"></i>
                <input type="text" class="form-control" id="search_demo" placeholder="Введите название карты">
                <script> set_enter('#search_demo', 'search_demo({server})'); </script>
            </div>

            <div id="demos">
                <div class="loader"></div>
                <script>load_demos("{start}", "{server}", 0);</script>
            </div>
        {else}
            {if('{error}' == 'empty')}
                <div class="empty-element">
                    Сервера не привязаны к источникам информации.
                </div>
            {else}
                <div class="empty-element">
                    {error}
                </div>
            {/if}
        {/if}
    </div>

    <div id="pagination2">{pagination}</div>
</div>

<div class="col-lg-3 order-is-last">
    {if('{error}' == '')}
        <div class="block">
            <div class="block_head">
                Сервера
            </div>
            <div class="vertical-navigation">
                <ul>
                    {servers}
                </ul>
            </div>
        </div>
    {/if}

    {if(is_auth())}
        {include file="/home/navigation.tpl"}
        {include file="/home/sidebar_secondary.tpl"}
    {else}
        {include file="/index/authorization.tpl"}
        {include file="/index/sidebar_secondary.tpl"}
    {/if}
</div>