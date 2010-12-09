<table class="dataset fullWidth">
<tr>
    <th>ID</th>
    <th>Название</th>
    <th>Описание</th>
    <th>Статей</th>
    <th>Параметры</th>
</tr>
{foreach from=$list item="item"}
<tr>
    <td>{$item.id}</td>
    <td>
        <a {href url="admin/news" catid=$item.id}>{$item.name}</a>
        <a {href url="admin/news" catedit=$item.id}>{icon name="pencil" title="Правка"}</a>
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
<p><a {href url="admin/news" catedit="0"}>Создать новый раздел</a></p>