
<h2>Заказ <b>№ {$order.id}</b> от {$order.date|date_format:"%x"}</h2>

<form action="{link url="admin/order" num=$order.id}" method="post" class="well form-inline">
    <label for="new_status" class="control-label">Статус</label>
    <select name="new_status" id="new_status">
    {foreach from=$statuses item="item" key="key"}
        <option value="{$item->id}"{if $item->id == $order.status} selected{/if}>{$item->name}</option>
    {/foreach}
    </select>
    <input type="submit" class="btn" value={t}Save{/t}>
</form>

<hr />

<p>Фамилия: {$user.fname}</p>
<p>Имя: {$user.lname}</p>
<p>Email: <a href="mailto:{$user.email}">{$user.email}</a></p>
<p>Наименование: {$user.name}</p>
<p>Телефон: {$user.phone}</p>
<p>Факс: {$user.fax}</p>
<p>ИНН: {$user.inn}</p>
<p>КПП: {$user.kpp}</p>
<p>Адрес: {$user.address}</p>
<p>Статус:
    {if $user.status == $smarty.const.USER_USER}Покупатель{/if}
    {if $user.status == $smarty.const.USER_WHOLE}Оптовый покупатель{/if}
</p>


<hr />

<p><b>Позиции:</b></p>

<table class="table table-striped table-bordered table-condensed">
<thead>
    <tr>
        <th>№</th>
        <th>Наименование</th>
        <th>Артикул</th>
        <th>Цена</th>
        <th>Количество</th>
        <th>Сумма</th>
    </tr>
</thead>
<tbody>
    {foreach from=$positions item="pos"}
    <tr>
        <td>{counter}</td>
        <td>{$pos.name}</td>
        <td>{$pos.articul}</td>
        <td>{$pos.price}</td>
        <td>{$pos.count}</td>
        <td>{$pos.summa}</td>
    </tr>
    {/foreach}
</tbody>
<tfoot>
    <tr>
        <td></td>
        <td><b>Итого:</b></td>
        <td></td>
        <td></td>
        <td><b>{$count}</b></td>
        <td><b>{$summa}</b></td>
    </tr>
</tfoot>
</table>


<p><a {href url="admin/order"}>&lt; Список заказов</a></p>

