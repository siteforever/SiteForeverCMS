{form action="admin/order" method="get"}
<p><strong>Настройка фильтра</strong></p>

<p>
    Номер
    <input name="number" value="{$request->get("number")}" />
    Дата
    <input name="date" value="{$request->get("date")}" class="datepicker" />
    Аккаунт
    <input name="user" value="{$request->get("user")}" />

    <input type="submit" value="Фильтровать" />
    <a class="button" {href url="admin/order"}>Сбросить фильтр</a>
</p>

{/form}

<p></p>

<table class="dataset fullWidth">
<tr>
    <th>№</th>
    <th>Аккаунт</th>
    <th>Статус</th>
    {*<th>Строк</th>*}
    <th>Позиций</th>
    <th>Сумма</th>
</tr>
{foreach from=$orders item="order"}
<tr>
    <td><a {href url="admin/order" num=$order.id}>Заказ №{$order.id}</a>
        <small>от {$order.date|date_format:"%x"}</small></td>
    <td>{$order.email}</td>
    <td>{$order.statusObj.name}</td>
    {*<td>{$order.pos_num}</td>*}
    <td>{$order.count}</td>
    <td>{$order.positions.count * $order.positions.price}</td>
</tr>
{foreachelse}
<tr>
    <td colspan="56">Ничего не найдено</td>
</tr>
{/foreach}
</table>

<p>{$paging.html}</p>