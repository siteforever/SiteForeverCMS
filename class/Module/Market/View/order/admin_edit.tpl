
<p>{a controller="order" action="admin"}&laquo; Список заказов{/a}</p>


<form action="{link url="order/admin"}" method="post" class="well form-inline">
    <input type="hidden" name="id" value="{$order->id}">
    <label for="new_status" class="control-label">Статус:&nbsp;</label>
    <div class="input-append">
        <select name="new_status" id="new_status" data-url="{link url="order/status" id=$order->id}">
        {foreach from=$statuses item="item" key="key"}
            <option value="{$item->id}"{if $item->id == $order.status} selected{/if}>{$item->name}</option>
        {/foreach}
        </select>
        <span class="new_status_result"></span>
        {*<input type="submit" class="btn" value={t}Save{/t}>*}
    </div>
</form>

<h4>Контактные данные</h4>
<p>Имя: {$order.fname}</p>
<p>Фамилия: {$order.lname}</p>
<p>Email: <a href="mailto:{$order.email}">{$order.email}</a></p>
<p>Телефон: {$order.phone}</p>
<p>Адрес: {$order.address}</p>
<p>Комментарий: {$order.comment}</p>

{if $delivery}
    <h4>{t cat="delivery"}Delivery{/t}</h4>
    <ul>
        <li>Способ доставки: {if $delivery->getObject()}{$delivery->getObject()->name}{/if}</li>
        <li>Адрес доставки: {$order->address}</li>
        <li>Стоимость: {$delivery->cost()} Р.</li>
    </ul>
{/if}

{if $order->Payment}
    <h4>{t}Payment{/t}</h4>
    <p>Способ оплаты: {$order->Payment->name}</p>
    {if $order->paid}
        <p>Оплачено!</p>
    {/if}
{/if}

<hr />

<h4>Позиции:</h4>

<table class="table table-striped">
<thead>
    <tr>
        <th>№</th>
        <th>Артикул/Наименование</th>
        <th>Цена</th>
        <th>Количество</th>
        <th>Сумма</th>
    </tr>
</thead>
<tbody>
    {foreach from=$positions item="pos"}
    <tr>
        <td>{counter}</td>
        <td>{$pos.articul} {$pos.name}</td>
        <td>{$pos.price}</td>
        <td>{$pos.count}</td>
        <td>{$pos.sum}</td>
    </tr>
    {/foreach}
</tbody>
<tfoot>
    <tr>
        <td colspan="2"><b>Итого:</b></td>
        <td></td>
        <td><b>{$count}</b></td>
        <td><b>{$summa}</b></td>
    </tr>
</tfoot>
</table>

<h4>Итого к оплате: {$summa + $delivery->cost()}</h4>
