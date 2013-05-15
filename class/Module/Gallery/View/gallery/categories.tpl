{if $categories}
    <ul>
        {foreach from=$categories item="obj"}
        <li>
            <div><a {href controller="gallery" action="index" id=$obj->id}>{$obj->name}</a></div>
        </li>
        {/foreach}
    </ul>
{else}
{t}Subcategories not found{/t}
{/if}
