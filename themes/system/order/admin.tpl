{form action="admin/order" method="get"}
<p><strong>Настройка фильтра</strong></p>

<p>
    Номер
    <input name="number" value="{$request->get("number")}" />
    Дата
    <input name="date" value="{$request->get("date")}" class="datepicker" />
    Аккаунт
    <input name="user" value="{$request->get("user")}" />

    <input type="submit" value="Фильтровать" />
    <a class="button" {href url="admin/order"}>Сбросить фильтр</a>
</p>

{/form}

<p></p>

<table class="table table-striped table-bordered table-condensed">
<thead>
    <tr>
        <th>№</th>
        <th>Дата</th>
        <th>Аккаунт</th>
        <th>Статус</th>
        {*<th>Строк</th>*}
        <th>Позиций</th>
        <th>Сумма</th>
    </tr>
</thead>
<tbody>
    {foreach from=$orders item="order"}
    <tr>
        <td>{a controller="order" action="admin" id=$order.id}Заказ №{$order.id}{/a}</td>
        <td>{$order.date|date_format:"%x (%H:%M)"}</td>
        <td>{$order->User->email}</td>
        <td>{if $order->Status}{$order->Status->name}{/if}</td>
        {*<td>{$order.pos_num}</td>*}
        <td>{$order->Count}</td>
        <td>{if $order->Positions}{$order->Count * $order->Positions->price}{/if}</td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="56">Ничего не найдено</td>
    </tr>
    {/foreach}
</tbody>
</table>

<p>{$paging.html}</p>