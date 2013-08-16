<table class="table table-bordered">
<tr>
    <th class="span2">№</th>
    <th class="span2">Дата</th>
    <th class="span2>Статус</th>
    <th class="span2">Количество</th>
    <th class="span2">Сумма</th>
</tr>
{foreach from=$list item="item"}
<tr>
    <td><a {href url="order" item=$item.id}>Заказ {$item.id}</a></td>
    <td>{$item.date|date_format:"%x"}</td>
    <td>{$item.status_value|default:"&mdash;"}</td>
    <td class="right">{$item.count}</td>
    <td class="right">{$item.summa|number_format}</td>
</tr>
{foreachelse}
<tr>
    <td colspan="5">Нет заказов</td>
</tr>
{/foreach}
</table>

<p>&nbsp;</p>

<p>Быстрый переход:</p>

<ul>
    <li><a {href url="user/cabinet"}>Кабинет пользователя</a></li>
    <li><a {href url="basket"}>Корзина</a></li>
</ul>
