<div class="b-product">

        <div class="b-product">

            <div class="b-product-image">
            {foreach from=$item.gallery item="img"}
                <a title="{$img.title}" href="{$img.image}" rel="{$item.id}">
                    <img src="{$img.thumb}" alt="{$img.title}" width="150" height="150">
                </a>
            {foreachelse}
                <div class="b-product-noimage">{t}Image not found{/t}</div>
            {/foreach}
            </div>

            <div class="b-product-block">

            <p><a {href id=$item.parent}>&laquo; Вернуться к списку</a></p>

            {if $item.articul}<p>Артикул: <strong>{$item.articul}</strong></p>{/if}

            <p>
                Цена: <strong>{if $user->perm == $smarty.const.USER_WHOLE && $item.price2 > 0}
                    {$item.price2|string_format:"%.2f"}
                {else}
                    {$item.price1|string_format:"%.2f"}
                {/if}</strong> {$item.currency}
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
            </div>

            <div class="b-product-basket">
                <form action="/basket/" method="post">
                    {$item.item}
                    <input type="text" name="basket_prod_count" class="b-product-basket-count" value="1" />
                    <input type="submit" class="submit" value="В корзину" />
                </form>
            </div>

        </div>


</div>
