<h1>{$page.title}</h1>

{$page.content}

{if isset($subpages)}
    {foreach from=$subpages item="obj"}
    <div>
        <h3><a href="{$obj->getAlias()}">{$obj->title|default:$obj->name}</a></h3>
        {$obj->notice}
    </div>
    {/foreach}
{/if}
