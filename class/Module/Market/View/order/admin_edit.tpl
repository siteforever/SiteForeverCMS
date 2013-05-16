
<p>{a controller="order" action="admin"}&laquo; Список заказов{/a}</p>


{form action="order/admin" method="post" class="well form-inline"}
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
{/form}

<hr />

<p>Имя: {$order.fname}</p>
<p>Фамилия: {$order.lname}</p>
<p>Email: <a href="mailto:{$order.email}">{$order.email}</a></p>
<p>Телефон: {$order.phone}</p>
<p>Адрес: {$order.address}</p>
<p>Комментарий: {$order.comment}</p>

<hr />

<p><b>Позиции:</b></p>

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

