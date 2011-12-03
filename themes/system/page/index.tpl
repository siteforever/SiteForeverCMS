{$page.content}

{foreach from=$subpages item="obj"}
<div>
    <h3><a href="{$obj->getAlias()}">{$obj->title|default:$obj->name}</a></h3>
    {$obj->content|truncate:300}
</div>
{/foreach}