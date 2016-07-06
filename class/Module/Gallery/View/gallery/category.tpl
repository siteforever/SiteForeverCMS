<h1>{$page.title}</h1>
{$page.content}
<div id="siteforever_gallery_{$category.id}">
{if $rows->count()}
    <ul class="gallery_list">
    {foreach from=$rows item="img"}
        <li>{strip}
        {if $category.target != '_none'}
            {if $category.target == '_gallery' && $img.link == ''}
                <a href="{$img.image}" class="gallery" title="{$img.name}" rel="gallery" target="_blank">
            {elseif $category.target == '_self'}
                <a href="{link url=$img->url alias=$img->alias}">
            {else}
                <a href="{$img.image}" title="{$img.name}" target="{$category.target}">
            {/if}
        {/if}
        {thumb src=$img.image alt=$img.name width=$category.thumb_width height=$category.thumb_height method=$category.thumb_method color=$category.color}
        {if $category.target != '_none'}</a>{/if} {* _none *}
        {if $img.name}
            {if $category.target == '_self'}
                <div class="text-center">{a href=$img->url}{$img.name}{/a}</div>
            {else}
                <div class="text-center">{$img.name}</div>
            {/if}
        {/if}
        {/strip}</li>
    {/foreach}
    </ul>
    <div class="clear"></div>
{else}
    <p>{'image.not.found'|trans|ucfirst}</p>
{/if}
</div>

{if $paging.count}
    <p>{$paging.html}</p>
{/if}
