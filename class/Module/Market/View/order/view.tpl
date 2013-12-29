<h3>{$order->emptorName}, Ваш заказ №{$order->id}</h3>

{*<h4>Вы готовы заказать товары из корзины?</h4>*}

<table class="table">
    <tr>
        <th></th>
        <th>Наименование</th>
        <th>Детали</th>
        <th>Количество</th>
    </tr>
{foreach from=$positions item="pos"}
    <tr>
        <td>{if $pos->Product}{thumb src=$pos->Product->image width="100" height="auto"}{/if}</td>
        <td>{if $pos->Product}{a href=$pos->Product->url}{$pos->Product->name}{/a}{/if}</td>
        <td><i>{$pos.details}</i></td>
        <td>{$pos.count} шт.<br>по {$pos.price} р.</td>
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
    {if $delivery->getObject()}<li>Способ доставки: {$delivery->getObject()->name}</li>{/if}
    <li>Адрес доставки: {$order->address}</li>
    <li>Стоимость: {$delivery->cost()} Р.</li>
</ul>
{/if}

{if $order->Payment}
    <h4>{t}Payment{/t}</h4>
    <p>Способ оплаты: {$order->Payment->name}</p>
    {if $order->paid}
        <p>Оплачено!</p>
    {else}
        {if $payment}{$payment->render()}{/if}
        <p>В ближайшее время наш менеджер свяжется с Вами.</p>
    {/if}
{/if}

<h4>Итого: {$sum} р.</h4>
{if $this->auth->isLogged()}
<hr>
<p>
    {a href="order" complete="yes" class="btn"}Мои заказы{/a}
    {a href="basket" class="btn"}Моя корзина{/a}
</p>
{/if}
