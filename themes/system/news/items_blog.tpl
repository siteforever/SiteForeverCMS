{if $cat.show_content}
    {$page.content}
{/if}

{if $cat.show_list}
    {foreach from=$list item="item"}
    <div>
        <div><strong>{$item.date|date_format:"%x"}</strong></div>
        <p><a href="{$item->getAlias()}">{$item.title|default:$item.name}</a></p>
        <div>{$item.notice}</div>
        <div class="right"><a href="{$item->getAlias()}">подробнее...</a></div>
    </div>
    {foreachelse}
    <div>
        В этом разделе пока нет материалов
    </div>
    {/foreach}

    {if $paging.html}<hr />{$paging.html}{/if}
{/if}