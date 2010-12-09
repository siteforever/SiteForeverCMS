<form action="{link url="admin/order"}" method="post">
<p><strong>Настройка фильтра</strong></p>
<table style="border-collapse:separate; border-spacing: 2px;">
<tr>
    <td>Номер</td>
    <td><input name="number" value="{$smarty.post.number}" /></td>
    <td></td>
</tr>
<tr>
    <td>Дата</td>
    <td><input name="date" value="{$smarty.post.date}" class="datepicker" /></td>
    <td></td>
</tr>
<tr>
    <td>Аккаунт</td>
    <td><input name="user" value="{$smarty.post.user}" /></td>
    <td></td>
</tr>
{*<tr>
    <td>Товар</td>
    <td><input name="trade" value="{$smarty.post.trade}" /></td>
    <td></td>
</tr>*}
<tr>
    <td></td>
    <td><input type="submit" value="Фильтровать" /></td>
    <td><a {href url="admin/order"}>Сбросить фильтр</a></td>
</tr>
</table>
</form>

<p></p>

<table class="dataset fullWidth">
<tr>
    <th>№</th>
    <th>Аккаунт</th>
    <th>Статус</th>
    <th>Строк</th>
    <th>Позиций</th>
    <th>Сумма</th>
</tr>
{foreach from=$orders item="order"}
<tr>
    <td><a {href url="admin/order" num=$order.id}>Заказ №{$order.id}</a>
        <small>от {$order.date|date_format:"%x"}</small></td>
    <td>{$order.email}</td>
    <td>{$order.status_value}</td>
    <td>{$order.pos_num}</td>
    <td>{$order.count}</td>
    <td>{$order.summa}</td>
</tr>
{/foreach}
</table>

<p>{$paging.html}</p>