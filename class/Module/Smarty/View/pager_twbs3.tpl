<ul class="pagination pagination-sm">
    {foreach $p as $item}
        {if is_numeric($item)}
        <li class="active"><span>{$item} <span class="sr-only">(current)</span></span></li>
        {else}
        <li>{$item}</li>
        {/if}
    {/foreach}
</ul>
