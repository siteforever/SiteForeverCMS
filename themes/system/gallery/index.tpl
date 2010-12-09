{$page.content}

<ul class="gallery_list">
{foreach from=$rows item="img"}
    <li style="width: {$category.thumb_width}px; height: {$category.thumb_height}px;">
        <a href="{$img.image}" class="gallery" title="{$img.name}" rel="gallery" target="_blank">
            <img src="{$img.thumb}" alt="{$img.name}" width="{$category.thumb_width}" height="{$category.thumb_height}" />
        </a>
    </li>
{/foreach}
</ul>

<div class="clear"></div>
<p>{$paging.html}</p>