
<div class="hproduct" itemscope itemtype="http://schema.org/Product">
    <div class="row-fluid">
        {if $item.image}
        <div class="span3">
            {a title=$item.title href=$item.image rel=$item.id class="photo gallery thumbnail"}
                {thumb width=200 height=200 alt=$item.title src=$item.thumb name=$item.image class="img-rounded" color="ffffff"}
            {/a}
            <div class="vspacer5">&nbsp;</div>
            {foreach $item->Gallery as $img}{if not $img.main}<a title="{$item.title}" href="{$img.image}" class="gallery" rel="{$item.id}">{thumb src=$img.thumb name=$img.image width=50 height=50 alt=$item.title}</a>{/if}{/foreach}
        </div>
        {/if}

        <div class="span9">
            <div class="b-catalog-product-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                <span>Цена:</span>
                <span class="b-product-price" itemprop="price">{$item.price|string_format:"%.2f"}</span>
                <span>{$item.currency}</span>
            </div>

            {if $item.articul}<div>Артикул: <span>{$item.articul}</span></div>{/if}

            {if $item->Manufacturer}
            <div class="b-catalog-product-properties-item">
                <div class="b-catalog-product-properties-key">{t}Manufacturer{/t}:</div>
                <div class="b-catalog-product-properties-val">{$item->Manufacturer->name}</div>
            </div>{/if}

            <div class="b-product-properties">
                {foreach from=$properties[$item->getId()] key="pkey" item="pitem"}
                    {if $pitem}
                    <div class="b-product-properties-item">
                        <div class="b-product-properties-key">{$pkey}:</div>
                        <div class="b-product-properties-val"><strong>{$pitem}</strong></div>
                    </div>
                    {/if}
                {/foreach}
                {foreach $item->Properties as $prop}
                    {if $prop->value}
                    <div class="b-product-properties-item">
                        <div class="b-product-properties-key">{$prop->name}:</div>
                        <div class="b-product-properties-val">{$prop->value} {$prop->unit}</div>
                    </div>{/if}
                {/foreach}
            </div>

            {if $item.text}<div class="b-product-desc" itemprop="description">{$item.text}</div>{/if}

            {include file="basket/add.tpl"}

        </div>
    </div>

    {comment product=$item request=$request}

</div>


