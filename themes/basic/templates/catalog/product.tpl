{$breadcrumbs}

<div class="b-product">
    <form action="{link url="basket"}" method="post">

        {*<div class="b-catalog-articul float_right">Артикул: <big>{$product.articul}</big></div>*}

        <h1>{$product.name}</h1>

        <div class="b-product-info">
            <div class="b-product-image">
                {if $product.image != ''}
                <a href="{$product.image}" target="_blank">
                    <img src="{$product.image}" width="200" class="float_left" alt="{$product.name}" />
                </a>
                {else}
                <p>Нет изображения</p>
                {/if}
            </div>

            <div class="b-product-details">

                {* PROPERTIES *}
                {if count($properties) > 0}
                    <table class="b-catalog-product-properties">
                    {foreach from=$properties key="pkey" item="pitem" }
                    <tr>
                        <td width="100">{$pkey}</td>
                        <td width="10"></td>
                        <td width="200">{$pitem}</td>
                    </tr>
                    {/foreach}
                    </table>
                {/if}
                {if trim($product.text|strip_tags) != ''}
                <div class="b-description">
                    <p><strong>Описание</strong></p>

                    <div>
                    {$product.text}
                    </div>
                </div>
                {/if}

            </div>

            <div class="clear"></div>
        </div>

        {if count($gallery) > 1}
            <ul class="b-product-gallery">
                {foreach from=$gallery item="item"}
                <li><a href="{$item.image}" class="gallery" rel="image_group" target="_blank" ">
                    <img src="{$item.thumb}" alt="" />
                </a></li>
                {/foreach}
            </ul>
        {/if}

<p><a {href cat=$product.parent}>Вернуться к списку</a></p>
{*
        <p>&nbsp;</p>

        <table class="b-catalog-product">
          <tr>
            <td colspan="2">
                <input type="hidden" name="basket_prod_id" value="{$product.id}" />
                <div class="b-catalog-goback"><a {href url="catalog" cat=$product.parent}>Вернуться к списку</a></div>
                <div class="b-catalog-price">
                    Цена <big>{if $user->getPermission() == $smarty.const.USER_WHOLE && $product.price2 > 0}
                        {$product.price2|string_format:"%.2f"}
                    {else}
                        {$product.price1|string_format:"%.2f"}
                    {/if}</big> {$product.currency}
                </div>
                <div class="b-catalog-inbasket">
                    {$product.item} <input type="text" name="basket_prod_count" class="b-catalog-buy-count" value="1" />
                    <input type="submit" class="submit" value="В КОРЗИНУ" />
                </div>
                <div class="clear"></div>
            </td>
          </tr>
        </table>
*}
    </form>
</div>
