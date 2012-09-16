{*{form action="basket" method="post" class="form-horizontal ajax-validate"}*}

{if $all_count == 0}

<p>В корзине нет товаров</p>

{else}

{form form=$form}

    <table class="table">
    <thead>
        <tr>
            <th>{t cat="basket"}#{/t}</th>
            <th>{t cat="basket"}Name{/t}</th>
            <th>{t cat="basket"}Details{/t}</th>
            <th width="100">{t cat="basket"}Price{/t}</th>
            <th width="100">{t cat="basket"}Count{/t}</th>
            <th width="100">{t cat="basket"}Sum{/t}</th>
            <th>{t cat="basket"}Delete{/t}</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$all_product key="key" item="item"}
        <tr data-id="{$item.id}">
            <td>{counter}.</td>
            <td><span>{$item.name}</span></td>
            <td>{$item.details|nl2br}</td>
            <td class="right basket-price">{$item.price|number_format}</td>
            <td class="right span2 basket-count">
                <div class="input-append">
                    <input type="text" class="basket-count input-mini" name="basket_counts[{$item.id}]" value="{$item.count}">
                    <span class="add-on">Шт.</span>
                </div>
            </td>{$sum = $item.count*$item.price}
            <td class="right basket-sum">{$sum|default:"0"|number_format}</td>
            <td class="center">
                <input type="checkbox" class="checkbox" name="basket_del[{$item.id}]" value="{$key}" />
            </td>
        </tr>
        {foreachelse}
        <tr>
            <td colspan="7"><em>{t cat="basket"}No products{/t}</em></td>
        </tr>
        {/foreach}
        <tr id="deliveryRow">
            <td>&nbsp;</td>
            <td>{t cat="delivery"}Delivery{/t}</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td class="right basket-sum">{if $delivery}{$delivery.cost|number_format}{/if}</td>
            <td>&nbsp;</td>
        </tr>
    </tbody>
    <tfoot>
        <tr id="totalRow">
            <td></td>
            <td><b>{t cat="basket"}In total{/t}:</b></td>
            <td></td>
            <td></td>
            <td class="right basket-count"><b>{$all_count}</b></td>
            {if $delivery}{$basketSum = $all_summa + $delivery.cost}
            {else}{$basketSum = $all_summa}{/if}
            <td class="right basket-sum"><b>{$basketSum|number_format}</b></td>
            <td><input type="submit" class="btn" id="recalculate" name="recalculate" value="{t cat="basket"}Calculate{/t}" /></td>
        </tr>
    </tfoot>
    </table>


    <hr>

    <div class="row-fluid">
        <div class="span6">
            <div class="well">
                <h3>Контактные данные</h3>

                {$form->htmlFieldWrapped('fname')}
                {$form->htmlFieldWrapped('lname')}
                {$form->htmlFieldWrapped('email')}
                {$form->htmlFieldWrapped('phone')}
                {$form->htmlFieldWrapped('address')}
                {$form->htmlFieldWrapped('comment')}
            </div>
        </div>
        <div class="span6">
            <div class="well" id="delivery">
                <h3>Выбор способа доставки</h3>
                <div class="control-group" data-field-name="delivery_id">
                    {$form->htmlField('delivery_id')}
                </div>
            </div>
            <div class="well" id="payment">
                <h3>Выбор способа оплаты</h3>
                <div class="control-group" data-field-name="payment_id">
                    {$form->htmlField('payment_id')}
                </div>
            </div>
            <div class="well">
                <a href="http://market.yandex.ru/addresses.xml?callback={$host}&type=json">
                    {*<img src="http://cards2.yandex.net/hlp-get/5814/png/3.png" alt=""></a>*}
                    <img src="http://cards2.yandex.net/hlp-get/4412/png/4.png" alt=""></a>
                <input type="submit" class="btn" id="do_order" name="do_order" value="{t cat="basket"}DoOrder{/t}" />
            </div>
        </div>
    </div>

{/form}

{/if}

{* 
    Если страницу открыл не гость
 *}
{*{if $auth->currentUser()->perm != $smarty.const.USER_GUEST}*}
{*<p>&nbsp;</p>*}
{*<p>{t cat="basket"}Quick Links{/t}:</p>*}

{*<ul>*}
    {*<li><a {href url="users/cabinet"}>{t cat="basket"}User panel{/t}</a></li>*}
    {*<li><a {href url="order"}>{t cat="basket"}Order list{/t}</a></li>*}
{*</ul>*}
{*{/if}*}