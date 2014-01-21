<h1>{$request->getTitle()}</h1>

{if $query}
<div>{"You searched for"|lang:"page"}: <b>&laquo;{$query}&raquo;</b></div>
<hr>
{/if}
<form class="form-search" action="{link url="search"}">
    <div class="input-append">
        <label>{"Type your query"|lang:"page":[":first"=>1]}:
        <input type="text" name="query"
               placeholder="{"Search this site"|lang:"page"}" class="input-large search-query"
               value="{$query}"></label>
        <button type="submit" class="btn"><i class="icon-search"></i></button>
    </div>
</form>
{if isset($error)}
    <div class="alert alert-error">{$error}</div>
{elseif (isset($result))}
    <hr>
    {counter start=$paging->from print=false}
    {foreach $result as $item}
    <h4>{counter}.
        {a href=$item.alias htmlTitle=$item.title htmlData-placement="top" htmlRel="tooltip"}
        {$item.title|truncate:100|hl:$query}{/a}</h4>
    <div>{$item.content|strip_tags|truncate:200|hl:$query}</div>
    {foreachelse}
    <div>{"Nothing was found"|lang:"page"}</div>
    {/foreach}

    {if $paging->html}
    <div class="paging">{$paging->html}</div>
    {/if}
{/if}
