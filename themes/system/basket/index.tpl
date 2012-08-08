{form action="basket" method="post"}

    <table class="basket-table">
    <tr>
        <th>{t cat="basket"}#{/t}</th>
        <th>{t cat="basket"}Name{/t}</th>
        <th>{t cat="basket"}Details{/t}</th>
        <th width="100">{t cat="basket"}Price{/t}</th>
        <th width="100">{t cat="basket"}Count{/t}</th>
        <th width="100">{t cat="basket"}Sum{/t}</th>
        <th>{t cat="basket"}Delete{/t}</th>
    </tr>
    {foreach from=$all_product key="key" item="item"}
    <tr>
        <td>{counter}.</td>
        <td><span>{$item.name}</span></td>
        <td>{$item.details|nl2br}</td>
        <td class="right">{$item.price|number_format}</td>
        <td class="right">Шт <input type="text" class="b-catalog-buy-count" name="basket_counts[{$item.id}]" value="{$item.count}" /></td>
        <td class="right">{$item.summa|default:"0"|number_format}</td>
        <td class="center"><input type="checkbox" class="checkbox" name="basket_del[{$item.id}]" value="{$key}" /></td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="7"><em>{t cat="basket"}No products{/t}</em></td>
    </tr>
    {/foreach}
    <tr>
        <td></td>
        <td><b>{t cat="basket"}In total{/t}:</b></td>
        <td></td>
        <td></td>
        <td class="right"><b>{$all_count}</b></td>
        <td class="right"><b>{$all_summa|number_format}</b></td>
        <td></td>
    </tr>
    </table>

    <div>
        <input type="submit" class="submit" name="recalculate" value="{t cat="basket"}Calculate{/t}" />
        <input type="submit" class="submit" name="do_order" value="{t cat="basket"}DoOrder{/t}" />
    </div>

{/form}

{* 
    Если страницу открыл не гость
 *}
{if $auth->currentUser()->perm != $smarty.const.USER_GUEST}
<p>&nbsp;</p>
<p>{t cat="basket"}Quick Links{/t}:</p>

<ul>
    <li><a {href url="users/cabinet"}>{t cat="basket"}User panel{/t}</a></li>
    <li><a {href url="order"}>{t cat="basket"}Order list{/t}</a></li>
</ul>
{/if}