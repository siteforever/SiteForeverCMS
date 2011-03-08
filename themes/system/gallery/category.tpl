{$page.content}

<div id="siteforever_gallery_{$category.id}">
    <ul class="gallery_list">
    {foreach from=$rows item="img"}
        <li style="width: {$category.thumb_width}px; height: {$category.thumb_height}px;">
        {if $category.target != '_none'}
            {if $category.target == '_gallery' && $img.link == ''}
            <a href="{$img.image}" class="gallery" title="{$img.name}" rel="gallery" target="_blank">
            {elseif $category.target == '_self'}
            <a {href img=$img.id}>
            {else}
            <a href="{$img.link|default:$img.image}" title="{$img.name}" target="{$category.target}">
            {/if}
        {/if} {* _none *}
            <img src="{$img.thumb}" alt="{$img.name}" width="{$category.thumb_width}" height="{$category.thumb_height}" />
        {if $category.target != '_none'}</a>{/if} {* _none *}
        {if $img.name}<div>{$img.name}</div>{/if}
        </li>
    {/foreach}
    </ul>
    <div class="clear"></div>
</div>

{if $paging.count}
    <p>{$paging.html}</p>
{/if}