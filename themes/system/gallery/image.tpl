<style>
    .sf_gallery_panel {
        margin-bottom: 10px;
    }
    .sf_gallery_pred {
        float: left;
    }
    .sf_gallery_next {
        text-align: right;
    }
</style>

<div class="sf_gallery_panel">
    {if $pred}<div class="sf_gallery_pred"><a href="/{$pred->getAddr()}">&laquo; Пред.</a></div>{/if}
    {if $next}<div class="sf_gallery_next"><a href="/{$next->getAddr()}">След. &raquo;</a></div>{/if}
    <div class="clear"></div>
</div>


<table>
    <tr>
        <td style="padding-right: 25px;">
            <a href="{$image->image}" class="gallery">
                <img src="{$image->middle}" alt="{$image->name}">
            </a>
        </td>
        <td style="vertical-align: top;">
            {$image->description|nl2br}
        </td>
    </tr>
</table>


