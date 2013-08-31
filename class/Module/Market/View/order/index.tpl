<h1>{$this->t('order','My orders')}</h1>

{if $list}
<table class="table table-bordered">
<tr>
    <th class="span2">№</th>
    <th class="span2">Дата</th>
    <th class="span2">Статус</th>
</tr>
{foreach from=$list item="item"}
<tr>
    <td>{a href=$item.url}Заказ {$item.id}{/a}</td>
    <td>{$item.date|date_format:"%x"}</td>
    <td>{$item.status_value|default:"&mdash;"}</td>
</tr>
{/foreach}
</table>
{else}
<p>Нет заказов</p>
{/if}

<p>Быстрый переход:</p>

<ul>
    <li><a {href url="user/cabinet"}>Кабинет пользователя</a></li>
    <li><a {href url="basket"}>Корзина</a></li>
</ul>
