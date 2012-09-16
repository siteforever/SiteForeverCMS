{*<h3>*}
    {*<a {href controller="banner" action="admin"}> </a> &rarr; {$cat.name}*}
{*</h3>*}
<ul class="breadcrumb">
    <li>{a controller="banner" action="admin"}Категории{/a} > </li>
    <li>{$cat->name}</li>
</ul>

<table class="table table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>Наименование</th>
        <th width="120">Показов</th>
        <th width="120">Переходов</th>
        <th width="120">Править</th>
        <th width="120">Удалить</th>
    </tr>
    </thead>
{foreach from=$banners item="item"}
    <tr>
        <td width="20">{$item->id}</td>
        <td width="20"><p class="page">{$item->name}</p></td>
        <td width="20">{if $item->count_show}{$item->count_show}{else}0{/if}</td>
        <td width="20">{if $item->count_click}{$item->count_click}{else}0{/if}</td>
        <td>
            <a class="edit" {href controller="banner" action="edit" id=$item.id} title="Править баннер">
                {icon name="pencil" title="Править"}</a>
        </td>
        <td>
            <a {href controller="banner" action="del" id=$item.id} class="do_delete">{icon name="delete" title="Удалить"}</a>
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
