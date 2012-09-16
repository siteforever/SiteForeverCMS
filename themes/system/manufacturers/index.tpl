{$page->content}

<ul>
    {foreach from=$items item="obj"}
    <li>{$obj->name}</li>
    {/foreach}
</ul>