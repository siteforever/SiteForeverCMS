<table class="dataset fullWidth">
<tr>
    <th width="250">Шаблон</th>
    <th>Описание</th>
    <th width="150">Изменен</th>
</tr>
{foreach from=$list item="t"}
<tr>
    <td><a {href url="admin/templates" edit=$t.id}>{$t.name}</a></td>
    <td>{$t.description}</td>
    <td>{$t.update|date_format:"%X (%x)"}</td>
</tr>
{foreachelse}
<tr>
    <td colspan="3">Не найдено шаблонов</td>
</tr>
{/foreach}
</table>