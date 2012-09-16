{function recursiveNavMenu level=1 parent=0 data=array()}
    {if $level>=0 && isset( $data[$parent] )}
        <ul class="level{$level} parent{$parent}{if $level==1} nav{else} dropdown-menu{/if}">
            {$first = 1}
            {foreach from=$data[$parent] item="item"}
                {if !$item->hidden}
                    {if $level == 1 && isset( $data[ $item->id ] ) && count( $data[ $item->id ] )}
                        <li class="item{$item->id}{if $request->get('route')==$item->alias} active{/if}{if $first} first{/if} dropdown">
                            {a class="dropdown-toggle" htmlData-toggle="dropdown"}
                                {$item->title} <b class="caret"></b>{/a}
                            {recursiveNavMenu data=$data parent=$item->id level=$level-1}
                        </li>
                    {else}
                        <li class="item{$item->id}{if $request->get('route')==$item->alias} active{/if}{if $first} first{/if}">
                            {a href=$item->alias}{$item->title}{/a}
                        </li>
                    {/if}
                    {$first = 0}
                {/if}
            {/foreach}
        </ul>
    {/if}
{/function}

{if $level>1}{$level = 1}{/if}
{recursiveNavMenu data=$parents level=$level parent=$parent}