<tr id="product{id}">
    <td>
        <a href="../admin/rcon_shop_product?id={id}">{title}</a>
    </td>
    <td>
        {if('{status}' == '1')}
            Продается
        {else}
            Не продается
        {/if}
    </td>
    <td>
        {if('{isHasTarifs}' == '1')}
            Есть
        {else}
            Нет
        {/if}
    </td>
    <td>
        <a onclick="removeProduct({id})" class="c-p">Удалить</a>
    </td>
</tr>