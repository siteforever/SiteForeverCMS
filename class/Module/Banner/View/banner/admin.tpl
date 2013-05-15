<table class="table table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>{t}Name{/t}</th>
        <th width="120">{t}Edit{/t}</th>
        <th width="120">{t}Delete{/t}</th>
    </tr>
    </thead>
{foreach from=$categories item="item"}
    <tr>
        <td width="20">{$item->id}</td>
        <td width="20"><p class="page"><a {href controller="banner" action="cat" id=$item->id}>{$item->name}</a></p>
        </td>
        <td>
            <a class="cat_add" {href controller="banner" action="savecat" id=$item->id} title="{t}Edit category{/t}">
                {icon name="pencil" title="Править"}</a>
        </td>
        <td>
            <a {href  controller="banner" action="delcat" id=$item.id}
                    class="do_delete">{icon name="delete" title="Удалить"}</a>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="6">{t}There are no sections{/t}</td>
    </tr>
{/foreach}
</table>
<p class="page">{if isset($paging)}{$paging.html}{/if}</p>
<a class="cat_add btn" title="{t}Add category{/t}" {href controller="banner" action="savecat"}>
{icon name="add" title="Добавить"}
{t}Add category{/t}</a>
