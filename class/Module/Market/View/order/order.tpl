<h2>Заказ № {$order.id}</h2>
<p>Дата заказа: {$order.date|date_format:"%x"}</p>

<table class="basket-table">
<tr>
    <th></th>
    <th>Наименование</th>
    <th>Артикул</th>
    <th>Детали</th>
    <th width="80">Цена</th>
    <th width="80">Количество</th>
    <th width="80">Сумма</th>
</tr>
{foreach from=$list item="item"}
<tr>
    <td>{counter}</td>
    <td><span>{$item.name}</span></td>
    <td>{$item.articul}</td>
    <td>{$item.details|nl2br}</td>
    <td class="right">{$item.price|number_format}</td>
    <td class="right">{$item.count}</td>
    <td class="right">{$item.summa|number_format}</td>
</tr>
{foreachelse}
<tr>
    <td colspan="7">Нет заказанных товаров</td>
</tr>
{/foreach}
<tr>
    <td></td>
    <td><b>Итого:</b></td>
    <td></td>
    <td></td>
    <td></td>
    <td class="right"><b>{$all_count}</b></td>
    <td class="right"><b>{$all_summa|number_format}</b></td>
</tr>
</table>

<ul>
    <li>
        <a {href url="orderpdf" order_id=$order.id} target="_blank">Скачать счет в PDF для печати</a>
    </li>
    <li>
        <a {href url="order" cancel=$order.id} class="confirm">Отменить заказ</a>
    </li>
    <li>
        <small><a {href url="order"}>Список заказов</a></small>
    </li>
</ul>
