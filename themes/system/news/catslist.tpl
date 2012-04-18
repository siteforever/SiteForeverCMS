<table class="dataset fullWidth">
<tr>
    <th width="20">#</th>
    <th>Название</th>
    <th>Описание</th>
    <th>Статей</th>
    <th>Параметры</th>
</tr>
{foreach from=$list item="item"}
<tr>
    <td>{$item.id}</td>
    <td>
{*        <a {href url="admin/news" catid=$item.id}>{$item.name}</a>*}
        <a {href controller="news" action="newslist" catid=$item.id}>{$item.name}</a>
{*        <a {href url="admin/news" catedit=$item.id}>{icon name="pencil" title="Правка"}</a>*}
        <a {href controller="news" action="catedit" id=$item.id}>{icon name="pencil" title="Правка"}</a>
{*        <a {href url="admin/news" catdel=$item.id}>{icon name="delete" title="Удалить"}</a>*}
        <a {href controller="news" action="catdelete" catdel=$item.id}>{icon name="delete" title="Удалить"}</a>
    </td>
    <td>{$item.description}</td>
    <td>{$item.news_count}</td>
    <td>
        {if $item.hidden}{icon name="lightbulb_off" title="Выкл"}{else}{icon name="lightbulb" title="Вкл"}{/if}
        {if $item.protected}{icon name="lock" title="Закрыто"}{/if}
    </td>
</tr>
{foreachelse}
<tr>
    <td colspan="5">Ничего не найдено</td>
</tr>
{/foreach}
</table>
<p></p>
<p><a class="button" {href controller="news" action="catedit" id="0"}>
    {icon name="add"} Создать новый раздел</a></p>