<h3>Список категорий баннеров</h3>

<table class="catalog_data dataset fullWidth">
    <tr>
        <th>ID</th>
        <th>Наименование</th>
        <th width="120">Править</th>
        <th width="120">Удалить</th>
    </tr>
{foreach from=$categories item="item"}
    <tr>
        <td width="20">{$item->id}</td>
        <td width="20"><p class="page"><a {href controller="banner" action="cat" id=$item->id}>{$item->name}</a></p>
        </td>
        <td>
            <a class="cat_add" {href controller="banner" action="editcat" id=$item->id}>{icon name="pencil" title="Править"}</a>
        </td>
        <td>
            <a {href  controller="banner" action="delcat" id=$item.id}
                    class="do_delete">{icon name="delete" title="Удалить"}</a>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="6">Пока нет разделов</td>
    </tr>
{/foreach}
</table>
<p class="page">{if isset($paging)}{$paging.html}{/if}</p>
<a class="cat_add" {href controller="banner" action="editcat"}>Добавить категорию</a>
