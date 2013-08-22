{function recursiveSitemap level=0 parent=0 data=array()}
    {if $level>=0 && isset( $data[$parent] )}
        <ul>
            {$first = true}
            {foreach from=$data[$parent] item="item"}
                {if !$item->hidden}
                    <li>{if $item->controller != "page" || !$item->link}
                            {a href=$item->alias nofollow=$item.nofollow}{$item->name}{/a}
                        {/if}
                        {if $level >= 0 && isset($data[$item->parent]) && $data[$item->parent]}
                            {recursiveSitemap data=$data parent=$item->id level=$level-1}
                        {/if}
                    </li>
                    {$first = false}
                {/if}
            {/foreach}
        </ul>
    {/if}
{/function}

<h1>{$request->getTitle()}</h1>

<div class="sfcms-sitemap">
    {recursiveSitemap data=$data level=$level parent=$parent}
</div>
