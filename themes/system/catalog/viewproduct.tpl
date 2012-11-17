
<div class="hproduct" itemscope itemtype="http://schema.org/Product">
    <div class="row">
        <div class="span2">
            {a title=$item.title href=$item.image rel=$item.id class="photo gallery"}
                {if $item.image}
                    {thumb width=200 height=200 alt=$item.title src=$item.thumb name=$item.image class="img-rounded" color="ffffff"}
                    {*<img class="img-rounded" src="{$item.middle}" alt="{$item.title}">*}
                {else}
                    <span>{t cat="No image"}{/t}</span>
                {/if}
            {/a}

        {foreach $item->Gallery as $img}
            {if not $img.main}
                <a title="{$item.title}" href="{$img.image}" class="gallery" rel="{$item.id}">
                    {thumb src=$img.thumb name=$img.image width=50 height=50 alt=$item.title}
                </a>
            {/if}
        {/foreach}
        </div>

        <div class="span6">
            {if $item.articul}<div>Артикул: <span>{$item.articul}</span></div>{/if}
            <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                Цена: <span class="b-product-price" itemprop="price">
                    {$item.price|string_format:"%.2f"}</span>
                {$item.currency}
            </div>

            {if $item->Manufacturer}<div>
                {t}Manufacturer{/t}:
                <span class="brand">{$item->Manufacturer->name}</span>
            </div>{/if}

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

            {if $item.text}<div class="b-product-desc" itemprop="description">{$item.text}</div>{/if}
        </div>
    </div>

    <div class="b-product-basket">
            {$item.item}
        <div class="input-append">
            <input type="text" name="basket_prod_count" class="b-product-basket-count" value="1" />
            <input type="button"
                   class="btn basket_add"
                   data-product="{$item.name}"
                   data-price="{$item.price}"
                   data-id="{$item.id}"
                   value="В корзину" />
        </div>
    </div>

</div>


