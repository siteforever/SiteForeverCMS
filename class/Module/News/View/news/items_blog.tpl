<h1>{$page.title}</h1>

{if $cat.show_content}
    {$page.content}
{/if}

{if $cat.show_list}
    {foreach from=$list item="item"}
    <article>
        <div class="well">
            <h4>{a href=$item->url}{$item.title|default:$item.name}{/a}</h4>
            <p><strong>{$item.date|date_format:"%x"}</strong></p>
            <div>{$item.notice}</div>
            <div class="right">{a href=$item->url}подробнее...{/a}</div>
        </div>
    </article>
    {foreachelse}
    <div>
        В этом разделе пока нет материалов
    </div>
    {/foreach}

    {if $paging.html}<hr />{$paging.html}{/if}
{/if}