<h2>{"Order #"|lang} {$order.id}</h2>
<p>Дата заказа: {$order.date|date_format:"%x"}</p>

{$total_sum = 0}
<table class="table table-bordered">
<tr>
    <th class="span5">Наименование</th>
    <th class="span4">Детали</th>
    <th class="span1">Цена</th>
    <th class="span1">Количество</th>
    <th class="span1">Сумма</th>
</tr>
{foreach from=$list item="item"}
<tr>
    <td>{a url=$item.Product.url}{$item.Product.name}{/a}</td>
    <td>{$item.details|nl2br}</td>
    <td class="right">{$item.price|number_format}</td>
    <td class="right">{$item.count}</td>
    <td class="right">{($item.price * $item.count)|number_format}</td>
    {$total_sum = $total_sum + $item.price * $item.count}
</tr>
{foreachelse}
<tr>
    <td colspan="5">Нет заказанных товаров</td>
</tr>
{/foreach}
<tr>
    <td><b>Итого:</b></td>
    <td></td>
    <td></td>
    <td class="right"><b>{$all_count}</b></td>
    <td class="right"><b>{$total_sum|number_format}</b></td>
</tr>
</table>

<ul>
    <li>
        <a {href url="order" cancel=$order.id} class="confirm">Отменить заказ</a>
    </li>
    <li>
        <a {href url="order"}>Список заказов</a>
    </li>
</ul>
