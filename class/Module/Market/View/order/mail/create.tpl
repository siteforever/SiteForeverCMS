<html>
<body>
{block name="title"}
<p>Здравствуйте, {$order->getEmptorName()}</p>
<p>Вы оформили заказ на сайте {$sitename}</p>
{/block}

{block name="body"}
    <ul>
        <li>Номер заказа: {$order.id}</li>
        <li>Дата: {date('H:i d.m.Y')}</li>
        <li>Открыть <a href="{$request->getSchemeAndHttpHost()}{$order.url}" target="_blank">заказ на сайте</a></li>
    </ul>

    <table width="100%" border="1">
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

    <h4>Контактные данные</h4>
    <ul>
        <li>Имя: {$order->emptorName}</li>
        <li>Email: {$order->email}</li>
        <li>Телефон: {$order->phone}</li>
    </ul>

    {if $order->comment}
        <h4>Комментарий</h4>
        <p>{$order->comment|strip_tags|trim|nl2br}</p>
    {/if}

    {if $delivery}
        <h4>{t cat="delivery"}Delivery{/t}</h4>
        <ul>
            <li>Способ доставки: {$delivery->getObject()->name}</li>
            <li>Адрес доставки: {$order->address}</li>
            <li>Стоимость: {$delivery->cost()} Р.</li>
        </ul>
    {/if}

    {if $order->Payment}
        <h4>{t}Payment{/t}</h4>
        <p>Способ оплаты: {$order->Payment->name}</p>
        {if $payment}{$payment->render()}{/if}
        <p>В ближайшее время наш менеджер свяжется с Вами.</p>
    {/if}

    <p><strong>Итого:</strong></p>
    <ul>
        <li>Товаров: {$basket->getCount()}</li>
        <li>Сумма к оплате: {$basket->getSum() + $delivery->cost()}</li>
    </ul>
{/block}
</body>
</html>
