{include file="config.tpl"}

{if($monitoringType == 0)}
    <script>
        $('#online_users').after('<div class="block_head mt-4">На игровых серверах</div>\
    <div class="progress servers-online-line">\
        <div class="progress-val">{global_now}/{global_max}</div>\
        <div class="progress-bar bg-{color}" role="progressbar" style="width: {percentage}%;" aria-valuenow="{percentage}" aria-valuemin="0" aria-valuemax="100"></div>\
    </div>');
        $('#servers > .server:nth-last-child(2)').addClass('online-line-fix');
    </script>
{else}
    <tr>
        <td colspan="10">
            <div class="progress servers-online-line">
                <div class="progress-val">{global_now}/{global_max}</div>
                <div class="progress-bar bg-{color}" role="progressbar" style="width: {percentage}%;" aria-valuenow="{percentage}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </td>
    </tr>
{/if}