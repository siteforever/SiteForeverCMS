<div class="catalog_order">
    {t cat="catalog"}Sort{/t}
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
{form action="basket" method="post"}

    <div class="well">

        <div class="row">
            <div class="span2">
                {a href=$item->url class="thumbnail"}
                    {thumb width=150 height=150 alt=$item.title src=$item.image class="img-rounded" color="f6f6f6"}
                {/a}
            </div>
            <div class="span5">
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
        </div>


        <div class="b-product-basket">
            {$item.item}
            <div class="input-append">
                <input type="text" name="basket_prod_count" class="b-product-basket-count span1" value="1">
                <input type="button" class="btn basket_add" value="В корзину"
                       data-product="{$item.name}"
                       data-price="{$item.price}"
                       data-id="{$item.id}">
            </div>
        </div>

    </div>

{/form}
{foreachelse}
    {if count($cats) == 0}
    <p>Товаров не найдено</p>
    {/if}
{/foreach}

<p>{$paging.html}</p>