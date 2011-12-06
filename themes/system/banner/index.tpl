<a {href controller="banner" action="redirectbanner" id=$banner.id} target="{$banner->target}" >
    {*<img src="{$banner->path}" alt="{$banner->name}">*}
    {$banner->content}
</a>
