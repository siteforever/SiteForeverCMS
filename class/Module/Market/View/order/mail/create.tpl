{block name="title"}
<p>Здравствуйте, {$order->getEmptorName()}</p>
<p>Вы оформили заказ на сайте {$config->get('sitename')}</p>
{/block}

{block name="body"}
    <ul>
        <li>Номер заказа: {$order.id}</li>
        <li>Дата: {date('H:i d.m.Y')}</li>
        <li>Открыть <a href="{$request->getHttpHost()}{$order.url}" target="_blank">заказ на сайте</a></li>
    </ul>

    <table>
        <tr>
            <th>Наименование</th>
            <th>Цена</th>
            <th>Количество</th>
            <th>Сумма</th>
        </tr>
        {foreach from=$order->Positions item="pos"}
        <tr>
            <td>{$pos.Product.name}</td>
            <td>{$pos.price}</td>
            <td>{$pos.count}</td>
            <td>{$pos.sum}</td>
        </tr>
        {/foreach}
    </table>

    {if $delivery}
    <h3>Доставка</h3>
    <ul>
        <li>{$delivery->getObject()->name}</li>
        <li>Стоимость: {$delivery->cost($basket->getSum())}</li>
        <li>Адрес: {$order->address}</li>
    </ul>
    {/if}

    <p><strong>Итого:</strong></p>
    <ul>
        <li>Товаров: {$basket->getCount()}</li>
        <li>Сумма к оплате: {$basket->getSum() + $delivery->cost()}</li>
    </ul>

{/block}
