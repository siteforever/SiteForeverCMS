{if $cat.show_content}
    {$page.content}
{/if}

{if $cat.show_list}
    <ul>
    {foreach from=$list item="item"}
        <li><a {href url=$item.link doc=$item.id}>{$item.title}</a></li>
    {foreachelse}
        <li>В этом разделе пока нет материалов</li>
    {/foreach}
    </ul>

    <hr />
    {$paging.html}
{/if}