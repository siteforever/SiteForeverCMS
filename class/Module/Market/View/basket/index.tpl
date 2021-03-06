{if $all_count == 0}
<p>В корзине нет товаров</p>
{else}
{form form=$form class="form-horizontal"}
    <table class="table">
    <thead>
        <tr>
            <th>{t cat="basket"}#{/t}</th>
            <th>{t cat="basket"}Name{/t}</th>
            <th>{t cat="basket"}Details{/t}</th>
            <th class="span1">{t cat="basket"}Price{/t}</th>
            <th class="span1">{t cat="basket"}Count{/t}</th>
            <th class="span1">{t cat="basket"}Sum{/t}</th>
            <th>{t cat="basket"}Delete{/t}</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$all_product key="key" item="item"}
        <tr data-key="{$key}">
            <td>{counter}.</td>
            <td>{a href=$item.obj.url}{$item.obj.name}{/a}</td>
            <td>{$item.details|nl2br}</td>
            <td class="right basket-price">{$item.price|number_format}</td>
            <td class="right basket-count">
                <input type="text" class="basket-count form-control input-sm" name="basket_counts[{$item.id}]" value="{$item.count}">
            </td>
            <td class="right basket-sum">
                {$sum = $item.count*$item.price}
                {$sum|default:"0"|number_format}
            </td>
            <td class="center">
                <input type="checkbox" class="checkbox" name="basket_del[{$key}]" value="1" />
            </td>
        </tr>
        {foreachelse}
        <tr>
            <td colspan="7"><em>{t cat="basket"}No products{/t}</em></td>
        </tr>
        {/foreach}
    </tbody>
    <tfoot>
        <tr id="deliveryRow">
            <td>&nbsp;</td>
            <td>{t cat="delivery"}Delivery{/t}</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td class="right basket-sum">{if $delivery}{$delivery->cost()|number_format}{/if}</td>
            <td>&nbsp;</td>
        </tr>
        <tr id="totalRow">
            <td></td>
            <td><b>{t cat="basket"}In total{/t}:</b></td>
            <td></td>
            <td></td>
            <td class="right basket-count"><b>{$all_count}</b></td>
            {if $delivery}{$basketSum = $all_summa + $delivery->cost()}
            {else}{$basketSum = $all_summa}{/if}
            <td class="right basket-sum"><b>{$basketSum|number_format}</b></td>
            <td><input type="submit" class="btn" id="recalculate" name="recalculate" value="{t cat="basket"}Calculate{/t}" /></td>
        </tr>
    </tfoot>
    </table>

    <hr>

    {$form->htmlFieldWrapped('agreement')}


    <div class="well">
        <h3>Контактные данные</h3>
        {$form->htmlFieldWrapped('fname')}
        {$form->htmlFieldWrapped('lname')}
        {$form->htmlFieldWrapped('email')}
        {$form->htmlFieldWrapped('phone')}
        {$form->htmlFieldWrapped('zip')}
        {$form->htmlFieldWrapped('country')}
        {$form->htmlFieldWrapped('city')}
        {$form->htmlFieldWrapped('metro')}
        {$form->htmlFieldWrapped('address')}
        {$form->htmlFieldWrapped('comment')}
    </div>
    <div class="well" id="delivery">
        {*<h3>Выбор способа доставки</h3>*}
        {$form->htmlFieldWrapped('delivery_id')}
        {*<div class="control-group" data-field-name="delivery_id">*}
            {*{$form->htmlField('delivery_id')}*}
            {*{$form->htmlError('delivery_id')}*}
        {*</div>*}
    </div>
    <div class="well" id="payment">
        {*<h3>Выбор способа оплаты</h3>*}
        {$form->htmlFieldWrapped('payment_id')}
        {*<div class="control-group" data-field-name="payment_id">*}
            {*{$form->htmlField('payment_id')}*}
            {*{$form->htmlError('payment_id')}*}
        {*</div>*}
    </div>
    <div class="well">
        <a href="http://market.yandex.ru/addresses.xml?callback={$host}&type=json">
            {*<img src="http://cards2.yandex.net/hlp-get/5814/png/3.png" alt=""></a>*}
            <img src="http://cards2.yandex.net/hlp-get/4412/png/4.png" alt=""></a>
        <input type="submit" class="btn" id="do_order" name="do_order" value="{t cat="basket"}DoOrder{/t}" />
    </div>
{/form}
{/if}

{*
    Если страницу открыл не гость
 *}
{if $auth->getPermission() > $smarty.const.USER_GUEST}
    <p>&nbsp;</p>
    <p>{t cat="basket"}Quick Links{/t}:</p>
    <ul>
        <li><a {href url="user/cabinet"}>{t cat="basket"}User panel{/t}</a></li>
        <li><a {href url="order"}>{t cat="basket"}Order list{/t}</a></li>
    </ul>
{/if}
