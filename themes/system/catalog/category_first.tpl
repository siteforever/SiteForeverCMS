<ul>
{foreach from=$list item="cat"}
    <li><a {href id=$cat->id}>{$cat->name}</a></li>
{/foreach}
</ul>