<h1>{$page.title}</h1>

<div class="catalog_order">{strip}
    <h4>{t cat="catalog"}Sort{/t}</h4>
    <div class="catalog_select_order btn-group">
        {foreach from=$order_list item="ord" key="ord_key"}
        {if $order_val eq $ord_key}
            <button class="btn btn-default">{$ord|ucfirst|trans:[]:"catalog"} {if $order_val eq $ord_key}↕{/if}</button>
        {else}
            <a href="?order={$ord_key}" class="btn btn-default">{$ord|ucfirst|trans:[]:"catalog"}</a>
        {/if}
        {/foreach}
    </div>
    <div class="clearfix"></div>
{/strip}</div>
{*<div class="clear"></div>*}

{if $cats}
<h4>{'categories'|trans|ucfirst}</h4>
<ul class="b-cat-list">{strip}
    {foreach from=$cats item="cat"}
    <li><a {href url=$cat->url}>{$cat->name} {*({$cat.sub_count})*}</a></li>
    {/foreach}
{/strip}</ul>
{/if}


<form action="{link url="basket"}" method="post">
{foreach from=$list item="item"}
<div class="row">
    <div class="col-md-3">{strip}
        {a href=$item->url class="thumbnail"}
            {thumb width=150 height=150 alt=$item.title src=$item.thumb name=$item.image class="img-rounded" color="f6f6f6"}
        {/a}
    {/strip}</div>
    <div class="col-md-9">
        <h3 class="b-catalog-product-title">{a href=$item->url}{$item.name}{/a}</h3>

        <div class="b-catalog-product-price">{strip}
            <span class="b-product-price">{$item.price|string_format:"%.2f"}</span>
            <span class="b-product-currency">{$item.currency|default:"RUR"}</span>
            <div class="b-product-sale">
                {if $item->isSale()}<span class="hot">Sale</span>{else}<span class="cool">Not sale</span>{/if}
                {if $item.salePrice}<span class="sale">{$item.salePrice|string_format:"%.2f"} {$item.currency|default:"RUR"}</span>{/if}
            </div>
        {/strip}</div>

        {if $item.articul}{strip}<div class="b-catalog-product-articul">
            <span>{'Articul'|trans:[]:'catalog'}</span>
            <span>{$item.articul}</span>
        </div>{/strip}{/if}

        {if $item->Manufacturer}{strip}
        <div class="row">
            <div class="col-xs-3">{t}Manufacturer{/t}:</div>
            <div class="col-xs-3">{$item->Manufacturer->name}</div>
        </div>
        {/strip}{/if}

        {if count($properties[$item->getId()]) > 0}
            {foreach from=$properties[$item->getId()] key="pkey" item="pitem"}
                {if $pitem}
                <div class="row">
                    <div class="col-xs-3">{$pkey}:</div>
                    <div class="col-xs-3">{$pitem}</div>
                </div>
                {/if}
            {/foreach}
            {foreach $item->Properties as $prop}
                {if $prop->value}
                <div class="row">
                    <div class="col-xs-3">{$prop->name}:</div>
                    <div class="col-xs-3">{$prop->value} {$prop->unit}</div>
                </div>{/if}
            {/foreach}
        {/if}
{*

        <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-3"></div>
        </div>
*}

        {if $item.text}<div class="b-catalog-product-desc">{$item.text}</div>{/if}

        {include file="basket/add.tpl"}

    </div>
</div>
<hr/>
{foreachelse}
    <p>{'Products not found'|trans:[]:'catalog'}</p>
{/foreach}
</form>

{$paging.html}
