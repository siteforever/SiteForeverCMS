<div class="pagination pagination-small">
<ul>
    {foreach $p as $item}
        {if is_numeric($item)}
        <li class="active"><span>{$item}</span></li>
        {else}
        <li><span>{$item}</span></li>
        {/if}
    {/foreach}
</ul>
</div>
