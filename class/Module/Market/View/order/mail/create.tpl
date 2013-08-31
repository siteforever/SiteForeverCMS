{block name="title"}
<p>Здравствуйте, {$order->getEmptorName()}</p>
<p>Вы оформили заказ на сайте {$sitename}</p>
{/block}

{block name="body"}
    <ul>
        <li>Номер заказа: {$order_n}</li>
        <li>Дата: {$date}</li>
        <li>Открыть <a href="{$ord_link}" target="_blank">заказ на сайте</a></li>
    </ul>

    <table>
        <tr>
            <th>Наименование</th>
            <th>Цена</th>
            <th>Количество</th>
            <th>Сумма</th>
        </tr>
        {foreach from=$positions item="pos"}
        <tr>
            <td>{$pos.name}</td>
            <td>{$pos.price}</td>
            <td>{$pos.count}</td>
            <td>{$pos.price * $pos.count}</td>
        </tr>
        {/foreach}
    </table>

    {if $delivery}
    <h3>Доставка</h3>
    <ul>
        <li>{$delivery->getObject()->name}</li>
        <li>Стоимость: {$delivery->cost($sum)}</li>
        <li>Адрес: {$order->address}</li>
    </ul>
    {/if}

    <p><strong>Итого:</strong></p>
    <ul>
        <li>Товаров: {$total_count}</li>
        <li>Сумма к оплате: {$total_summa}</li>
    </ul>

{/block}
