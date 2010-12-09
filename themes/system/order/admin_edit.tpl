<form action="{link url="admin/order" num=$order.id}" method="post">

    <p>Заказ <b>№ {$order.id}</b> от {$order.date|date_format:"%x"}</p>
    <p>Статус: <select name="new_status">
        {foreach from=$statuses item="item" key="key"}
        <option value="{$key}"{if $key == $order.status} selected{/if}>{$item}</option>
        {/foreach}
        </select>

        <input type="submit" class="button" value="Сохранить" />
    </p>

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

    <table class="dataset">
    <tr>
        <th>№</th>
        <th>Наименование</th>
        <th>Артикул</th>
        <th>Цена</th>
        <th>Количество</th>
        <th>Сумма</th>
    </tr>
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
    <tr>
        <td></td>
        <td><b>Итого:</b></td>
        <td></td>
        <td></td>
        <td><b>{$count}</b></td>
        <td><b>{$summa}</b></td>
    </tr>
    </table>



</form>

<p><a {href url="admin/order"}>&lt; Список заказов</a></p>

