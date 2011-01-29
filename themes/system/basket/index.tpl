
<form action="/basket" method="post" >

    <table class="basket-table">
    <tr>
        <th>№</th>
        <th>Наименование</th>
        <th>Детали</th>
        <th width="100">Цена</th>
        <th width="100">Количество</th>
        <th width="100">Сумма</th>
        <th>Удалить</th>
    </tr>
    {foreach from=$all_product key="key" item="item"}
    <tr>
        <td>{counter}.</td>
        <td><span>{$item.name}</span></td>
        <td>{$item.details|nl2br}</td>
        <td class="right">{$item.price|number_format}</td>
        <td class="right">Шт <input type="text" class="b-catalog-buy-count" name="basket_counts[{$key}]" value="{$item.count}" /></td>
        <td class="right">{$item.summa|number_format}</td>
        <td class="center"><input type="checkbox" name="basket_del[{$key}]" value="{$key}" /></td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="7"><em>Нет товаров</em></td>
    </tr>
    {/foreach}
    <tr>
        <td></td>
        <td><b>Итого:</b></td>
        <td></td>
        <td></td>
        <td class="right"><b>{$all_count}</b></td>
        <td class="right"><b>{$all_summa|number_format}</b></td>
        <td></td>
    </tr>
    </table>

    <div>
        <input type="submit" class="submit" name="recalculate" value="Пересчитать" />
        <input type="submit" class="submit" name="do_order" value="Заказать" />
    </div>

</form>

{* 
    Если страницу открыл не гость
 *}
{if App::$user->perm != $smarty.const.USER_GUEST}
<p>&nbsp;</p>
<p>Быстрый переход:</p>

<ul>
    <li><a {href url="users/cabinet"}>Кабинет пользователя</a></li>
    <li><a {href url="order"}>Список заказов</a></li>
</ul>
{/if}