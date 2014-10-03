<form action="{link url='order/admin'}" method="get" class="well form-inline">
    <div class="form-group">
        <strong>Настройка фильтра</strong>
    </div>
    <div class="form-group">
        <input class="form-control" type="text" name="number" value="{$request->get("number")}" placeholder="Номер">
    </div>

    <div class="form-group">
        <input class="form-control filterDate datepicker" type="date" name="date" value="{$request->get("date")}" placeholder="Дата dd.mm.yyyy">
    </div>
    <div class="form-group">
        <input class="form-control filterEmail" type="text" name="user" value="{$request->get("user")}" placeholder="Аккаунт">
    </div>
    <div class="form-group">
        <input class="form-control filterEmail" type="text" name="user" value="{$request->get("user")}" placeholder="Аккаунт">
    </div>
    <div class="form-group">
        <button class="btn btn-default" type="submit">Фильтровать</button>
        {a class="btn btn-link" controller="order" action="admin"}Сбросить фильтр{/a}
    </div>
</form>

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
        <td colspan="7">Ничего не найдено</td>
    </tr>
    {/foreach}
</tbody>
</table>

<p>{$paging.html}</p>
