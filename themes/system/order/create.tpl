    <p>Вы готовы заказать товары из корзины?</p>

    <ul>
    {foreach from=$products item="prod"}
        <li>{$prod.name} ( {$prod.count} шт. по {$prod.price} Р. )</li>
    {/foreach}
    </ul>

    <p>
        <a {href url="order/create" complete="yes"}>Сделать заказ</a> |
        <a {href url="basket"}>Перейти в корзину</a>
    </p>

   {*<form method="post">
     <input type="submit" class="submit" name="complete" value="Заказать" />
   </form>*}