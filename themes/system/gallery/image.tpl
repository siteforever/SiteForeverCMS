<p>
    <a href="{$image->image}" class="gallery">
        <img src="{$image->middle}" alt="{$image->name}">
    </a>
</p>

<p>{$image->description}</p>

<p>
    {if $pred}<a {href img=$pred->id}>&laquo; Пред.</a>{/if}
    {if $next}<a {href img=$next->id}>След. &raquo;</a>{/if}
</p>