<p>Вы искали: <b>{$query}</b></p>
<hr>
{form class="form-search" action="search"}
    <div class="input-append">
        <label>Введите фразу:
        <input type="text" name="query"
               placeholder="Поиск по сайту" class="input-large search-query"
               value="{$query}"></label>
        <button type="submit" class="btn"><i class="icon-search"></i></button>
    </div>
{/form}
<hr>
{if isset( $error )}
    {$error}
{else}
    {foreach $result as $item}
    <h4>{counter}.
        {a href=$item.alias htmlTitle=$item.title htmlData-placement="top" htmlRel="tooltip"}
        {$item.title|truncate:100}{/a}</h4>
    <p>{$item.content|truncate:200}</p>
    {foreachelse}
    <div>Ничего не найдено</div>
    {/foreach}
{/if}