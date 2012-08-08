<div class="b-product">

        <div class="b-product">

            <div class="b-product-image">
                {if $item.middle}
                    <a title="{$item.title}" href="{$item.image}" rel="{$item.id}">
                        <img src="{$item.middle}" alt="{$item.title}">
                    </a>
                {else}
                    <div class="b-catalog-product-nothumb">Нет изображения</div>
                {/if}

            {*{foreach from=$item.gallery item="img"}*}
                {*<a title="{$img.title}" href="{$img.image}" rel="{$item.id}">*}
                    {*<img src="{$img.thumb}" alt="{$img.title}">*}
                {*</a>*}
            {*{foreachelse}*}
                {*<div class="b-product-noimage">{t}Image not found{/t}</div>*}
            {*{/foreach}*}
            </div>

            <div class="b-product-block">

            {if $item.articul}<p>Артикул: <strong>{$item.articul}</strong></p>{/if}

            <p>
                Цена: <strong class="b-product-price">{$item.price|string_format:"%.2f"}</strong> {$item.currency}
            </p>

            {if $item->Manufacturer}<p>{t}Manufacturer{/t}: <strong>{$item->Manufacturer->name}</strong></p>{/if}

            {if count($properties[$item->getId()]) > 0}
            <div class="b-product-properties">
                {foreach from=$properties[$item->getId()] key="pkey" item="pitem"}
                    {if $pitem}
                    <div class="b-product-properties-item">
                        <div class="b-product-properties-key">{$pkey}:</div>
                        <div class="b-product-properties-val"><strong>{$pitem}</strong></div>
                    </div>
                    {/if}
                {/foreach}
            </div>
            {/if}

            {if $item.text}<div class="b-product-desc">{$item.text}</div>{/if}

            {if $parent}<p><a {href url=$parent->url}>&uarr; На уровень вверх</a></p>{/if}
            </div>

            <div class="b-product-basket">
                    {$item.item}
                    <input type="text" name="basket_prod_count" class="b-product-basket-count" value="1" />
                    <input type="button"
                           class="submit basket_add"
                           data-product="{$item.name}"
                           data-price="{$item.price}"
                           data-id="{$item.id}"
                           value="В корзину" />
            </div>

        </div>


</div>
