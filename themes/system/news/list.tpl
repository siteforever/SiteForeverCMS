<p><a {href url="news/admin"}>Категории материалов</a>
&gt; {$cat.name} <a {href controller="news" action="catedit" id=$cat.id}>{icon name="pencil"}</a>
&gt; <a {href controller="news" action="edit" cat=$cat.id}>Создать материал</a></p>

<table class="dataset fullWidth">
<tr>
    <th width="20">#</th>
    <th>Наименование</th>
    <th>Дата</th>
    <th>Свойства</th>
</tr>
{foreach from=$list item="item"}
<tr>
    <td>{$item.id}</td>
    <td><a {href controller="news" action="edit" id=$item.id}>{$item.name|truncate:100}</a></td>
    <td>{$item.date|date_format:"%x"}</td>
    <td>
        {if $item.hidden}{icon name="lightbulb_off" title="Выкл"}{else}{icon name="lightbulb" title="Вкл"}{/if}
        {if $item.protected}{icon name="lock" title="Закрыто"}{/if}
        <a {href controller="news" action="delete" id=$item.id} class="do_delete">{icon name="delete" title="Удалить"}</a>
    </td>
</tr>
{foreachelse}
<tr>
    <td colspan="4">Ничего не найдено</td>
</tr>
{/foreach}
</table>

<p>&nbsp;</p>
{$paging.html}