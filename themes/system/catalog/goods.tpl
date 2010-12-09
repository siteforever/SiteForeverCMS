{$breadcrumbs}

{if $category.sort_view}
<div class="catalog_order">
    Сортировать
    <select class="catalog_select_order">
        {foreach from=$order_list item="ord" key="ord_key"}
        <option value="{$ord_key}" {if $order_val eq $ord_key}selected="selected"{/if}>{$ord}</option>
        {/foreach}
    </select>
</div>
{/if}

<div class="clear"></div>

{if $cats}
<ul class="b-cat-list">
    {foreach from=$cats item="cat"}
    <li><a {href url="catalog" cat=$cat.id}>{$cat.name} {*({$cat.sub_count})*}</a></li>
    {/foreach}
</ul>
{/if}


{foreach from=$list item="item"}
<form action="/basket/" method="post">
    <table class="b-catalog-product fullWidth">
      <tr>
        <td width="110">
            {if $item.image}
                <a {href cat=$item.id}>
                    <img class="left" src="{$item.thumb}" alt="{$item.name}" bprder="0" width="100" height="100" />
                </a>
            {else}
                <div class="b-catalog-product-noimage"><br /><br />Нет изображения</div>
            {/if}
        </td>
        <td width="450">
            <p class="b-catalog-product-title"><a {href cat=$item.id}>{$item.name}</a></p>
            <div class="b-catalog-articul">Артикул <big>{$item.articul}</big></div>
            {* PROPERTIES *}
            {if count($item.properties) > 0}
                <table class="b-catalog-product-properties">
                {foreach from=$item.properties key="pkey" item="pitem" }
                <tr>
                    <td width="100">{$pkey}</td>
                    <td width="10"></td>
                    <td width="200">{$pitem}</td>
                </tr>
                {/foreach}
                </table>
            {/if}
            <div class="b-catalog-product-desc">{$item.text}</div>
        </td>
      </tr>
      <tr>
        <td colspan="2">
            <input type="hidden" name="basket_prod_id" value="{$item.id}" />
            <div class="b-catalog-price">
                Цена <big>{if $user->getPermission() == $smarty.const.USER_WHOLE && $item.price2 > 0}
                    {$item.price2|string_format:"%.2f"}
                {else}
                    {$item.price1|string_format:"%.2f"}
                {/if}</big> {$item.currency}
            </div>
            <div class="b-catalog-inbasket">
                {$item.item} <input type="text" name="basket_prod_count" class="b-catalog-buy-count" value="1" />
                <input type="submit" class="submit" value="В КОРЗИНУ" />
            </div>
            <div class="clear"></div>
        </td>
      </tr>
    </table>
</form>

<hr class="b-catalog-separator" />

{foreachelse}
    {if count($cats) == 0}
    <p>Товаров не найдено</p>
    {/if}
{/foreach}

<p>{$paging.html}</p>