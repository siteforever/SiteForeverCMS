<div class="pagination1 pagination-small1">
<ul>
    {foreach $p as $item}
        {if is_numeric($item)}
        <li class="active"><span>{$item}</span></li>
        {else}
        <li>{$item}</li>
        {/if}
    {/foreach}
</ul>
</div>
