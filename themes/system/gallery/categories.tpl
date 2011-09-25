
<ul>
{foreach from=$categories item="obj"}
    <li>
        <div><a {href controller="gallery" action="index" id=$obj->id}>{$obj->name}</a></div>
    </li>
{/foreach}
</ul>
