<div class="b-product">

    <form action="/basket/" method="post">

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

            {if $item.articul}<div class="b-product-articul">Артикул <big>{$item.articul}</big></div>{/if}

                <div class="b-product-price">
                    Цена <big>{if $user->perm == $smarty.const.USER_WHOLE && $item.price2 > 0}
                    {$item.price2|string_format:"%.2f"}
                {else}
                    {$item.price1|string_format:"%.2f"}
                {/if}</big> {$item.currency}
                </div>

            {if count($properties[$item->getId()]) > 0}
            <div class="b-product-properties">
                {foreach from=$properties[$item->getId()] key="pkey" item="pitem"}
                    {if $pitem}
                    <div class="b-product-properties-item">
                        <div class="b-product-properties-key">{$pkey}</div>
                        <div class="b-product-properties-val">{$pitem}</div>
                    </div>
                    {/if}
                {/foreach}
            </div>
            {/if}

            {if $item.text}<div class="b-product-desc">{$item.text}</div>{/if}
            </div>

            <div class="b-product-basket">
                {$item.item}
                <input type="text" name="basket_prod_count" class="b-product-basket-count" value="1" />
                <input type="submit" class="submit" value="В корзину" />
            </div>

        </div>

    </form>

    <p><a {href id=$item.parent}>&laquo; Вернуться к списку</a></p>
    
</div>
