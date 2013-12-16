{function recursiveMenu level=0 parent=0 data=array()}
{if $level>=0 && isset( $data[$parent] )}
<ul class="level{$level} parent{$parent}">
{$first = 1}
{foreach from=$data[$parent] item="item"}{if !$item.hidden}
    <li class="item{$item.id}{if $item.active} active{/if}{if $first} first{/if}">
    {a href=$item.alias nofollow=$item.nofollow}{$item.name}{/a}
    {if $level >= 0 && isset( $data[ $item.parent ] ) && $data[ $item.parent ]}
        {recursiveMenu data=$data parent=$item.id level=$level-1}
    {/if}</li>
    {$first = 0}
{/if}{/foreach}
</ul>
{/if}
{/function}

{recursiveMenu data=$parents level=$level parent=$parent}
