{form action="order/admin" method="get" class="well"}
<p><strong>Настройка фильтра</strong></p>
<p>
    <div class="input-append">
        <input class="input-medium" type="text" name="number"
               value="{$request->get("number")}" placeholder="Номер">
        <input class="input-medium filterDate"  type="date" name="date"
               value="{$request->get("date")}" class="datepicker" placeholder="Дата dd.mm.yyyy">
        <input class="input-medium filterEmail" type="text" name="user"
               value="{$request->get("user")}" placeholder="Аккаунт">
        <input type="submit" class="btn" value="Фильтровать" />
        {a class="btn" controller="order" action="admin"}Сбросить фильтр{/a}
    </div>
</p>

{/form}

<p></p>

<table class="table table-striped">
<thead>
    <tr>
        <th>№</th>
        <th>Дата</th>
        <th>Аккаунт</th>
        <th>Статус</th>
        <th>Оплачен</th>
        <th>Позиций</th>
        <th>Сумма</th>
    </tr>
</thead>
<tbody>
    {foreach from=$orders item="order"}
    <tr>
        <td>{a controller="order" action="admin" id=$order.id}Заказ №{$order.id}{/a}</td>
        <td><a href="#" class="filterDate" title="Фильтровать" data-filter="{$order.date|date_format:"%d.%m.%Y"}">
            {$order.date|date_format:"%x (%H:%M)"}</a></td>
        <td><a href="mailto:{$order->email}" class="filterEmail" title="Фильтровать">{$order->email}</a></td>
        <td>{if $order->Status}{$order->Status->name}{/if}</td>
        <td>{if $order.paid}{icon name="money" title=$this->t('Yes')}{else}&mdash;{/if}</td>
        <td>{$order->Positions->count()}</td>
        <td>{if $order->Positions}{$order->Positions->sum('sum')}{/if}</td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="56">Ничего не найдено</td>
    </tr>
    {/foreach}
</tbody>
</table>

<p>{$paging.html}</p>
