<h3>{$order->fname} {$order->lname}, Вы оформили заказ №{$order->id}</h3>

    {*<h4>Вы готовы заказать товары из корзины?</h4>*}

    <ul>
    {foreach from=$products item="prod"}
        <li><b>{$prod.articul}</b> <i>{$prod.details}</i> ( {$prod.count} шт. по {$prod.price} Р. )</li>
    {/foreach}
    </ul>

    {if $delivery}
    <h4>{t cat="delivery"}Delivery{/t}</h4>
    <ul>
        <li>Способ доставки: {$delivery->name}</li>
        <li>Стоимость: {$delivery->cost} Р.</li>
    </ul>
    {/if}

    {if $payment}
    <h4>{t}Payment{/t}</h4>
    <p>Способ оплаты: {$payment->name}</p>
    {/if}

    <h4>Итого: {$sum} Р.</h4>
    {if $robokassa}<a class="btn btn-success" href="{$robokassa->getLink(true)}">Перейти к оплате</a>
        <script type="text/javascript">
            setTimeout(function(){ window.location.href = "{$robokassa->getLink(true)}"; }, 5000);
        </script>
    {/if}


    {if ! $robokassa}
        <p>В ближайшее время наш менеджер свяжется с Вами.</p>
    {/if}

    {*<p>*}
        {*{a href="order/create" complete="yes" class="btn"}Сделать заказ{/a}*}
        {*{a href="basket" class="btn"}Вернуться в корзину{/a}*}
    {*</p>*}

   {*<form method="post">
     <input type="submit" class="submit" name="complete" value="Заказать" />
   </form>*}