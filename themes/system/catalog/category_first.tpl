<ul>
{foreach from=$list item="cat"}
    <li><a {href cat=$cat->id}>{$cat->name}</a></li>
{/foreach}
</ul>