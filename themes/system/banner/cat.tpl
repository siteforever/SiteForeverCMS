<h3>
    <a {href controller="banner" action="admin"}>Список категорий баннеров </a> &rarr; {$cat.name}
</h3>

<table class="catalog_data dataset fullWidth">
    <tr>
        <th>ID</th>
        <th>Наименование</th>
        <th width="120">Количество показов</th>
        <th width="120">Количество переходов</th>
        <th width="120">Править</th>
        <th width="120">Удалить</th>
    </tr>
{foreach from=$banners item="item"}
    <tr>
        <td width="20">{$item->id}</td>
        <td width="20"><p class="page">{$item->name}</p></td>
        <td width="20">{if $item->count_show}{$item->count_show}{else}0{/if}</td>
        <td width="20">{if $item->count_click}{$item->count_click}{else}0{/if}</td>
        <td>
            <a class="ban_add" {href controller="banner" action="edit" id=$item.id} title="Править баннер">{icon name="pencil" title="Править"}</a>
        </td>
        <td>
            <a {href controller="banner" action="del" id=$item.id}>{icon name="delete" title="Удалить"}</a>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="6">Пока нет разделов</td>
    </tr>
{/foreach}
</table>
<p><a class="ban_add button" {href controller="banner" action="edit" cat=$cat->id} title="Добавить баннер">
    {icon name="picture_add"} Добавить
</a></p>
<p class="page">{$paging.html}</p>
