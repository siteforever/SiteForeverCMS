<div class="catalog_order">
    Сортировать
    <select class="catalog_select_order">
        {foreach from=$order_list item="ord" key="ord_key"}
        <option value="{$ord_key}" {if $order_val eq $ord_key}selected="selected"{/if}>{$ord}</option>
        {/foreach}
    </select>
</div>

{*<div class="clear"></div>*}

{if $cats}
<ul class="b-cat-list">
    {foreach from=$cats item="cat"}
    <li><a {href url=$cat->url}>{$cat->name} {*({$cat.sub_count})*}</a></li>
    {/foreach}
</ul>
{/if}


{foreach from=$list item="item"}
<form action="/basket/" method="post">

    <div class="b-catalog-product">

        <div class="b-catalog-product-thumb">
            {if $item->thumb}
                <a {href url=$item->url}>
                    <img class="left" src="{$item->thumb}" alt="{$item->name}" border="0" width="150" height="150" />
                </a>
            {else}
                <div class="b-catalog-product-nothumb">Нет изображения</div>
            {/if}
        </div>

        <div class="b-catalog-product-block">

            <div class="b-catalog-product-title"><a {href url=$item->url}>{$item.name}</a></div>

            {if $item.articul}<div class="b-catalog-product-articul">Артикул <strong>{$item.articul}</strong></div>{/if}

            <div class="b-catalog-product-price">
                Цена <strong class="b-product-price">{$item.price|string_format:"%.2f"}</strong> {$item.currency}
            </div>

            {if $item->Manufacturer}<p>{t}Manufacturer{/t}: <strong>{$item->Manufacturer->name}</strong></p>{/if}

            {if count($properties[$item->getId()]) > 0}
            <div class="b-catalog-product-properties">
                {foreach from=$properties[$item->getId()] key="pkey" item="pitem"}
                    {if $pitem}
                    <div class="b-catalog-product-properties-item">
                        <div class="b-catalog-product-properties-key">{$pkey}:</div>
                        <div class="b-catalog-product-properties-val"><strong>{$pitem}</strong></div>
                    </div>
                    {/if}
                {/foreach}
            </div>
            {/if}

            {if $item.text}<div class="b-catalog-product-desc">{$item.text}</div>{/if}

        </div>

        <div class="b-product-basket">
            {$item.item} <input type="text" name="basket_prod_count" class="b-product-basket-count" value="1" />
            <input type="button"
                   class="submit basket_add"
                   data-product="{$item.name}"
                   data-price="{$item.price}"
                   data-id="{$item.id}"
                   value="В корзину" />
        </div>

    </div>

</form>
{foreachelse}
    {if count($cats) == 0}
    <p>Товаров не найдено</p>
    {/if}
{/foreach}

<p>{$paging.html}</p>