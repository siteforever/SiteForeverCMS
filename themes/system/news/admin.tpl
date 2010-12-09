<p><a {href url="admin/news"}>Категории материалов</a>
&gt; {$cat.name}
&gt; <a {href url="admin/news" newsedit="0" cat=$cat.id}>Создать материал</a></p>
<table class="dataset fullWidth">
<tr>
    <th>id</th>
    <th>Наименование</th>
    <th>Дата</th>
    <th>Свойства</th>
</tr>
{foreach from=$list item="item"}
<tr>
    <td>{$item.id}</td>
    <td><a {href url="admin/news" newsedit=$item.id}>{$item.name|truncate:100}</a></td>
    <td>{$item.date|date_format:"%x"}</td>
    <td>
        {if $item.hidden}{icon name="lightbulb_off" title="Выкл"}{else}{icon name="lightbulb" title="Вкл"}{/if}
        {if $item.protected}{icon name="lock" title="Закрыто"}{/if}
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