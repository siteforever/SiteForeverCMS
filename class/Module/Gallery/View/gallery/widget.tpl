<ul>
{foreach $list as $item}
<li>
    <a href="{$item.image}" target="_blank" class="gallery">
        {thumb src=$item.image width=120 height=90}
    </a>
</li>
{/foreach}
</ul>