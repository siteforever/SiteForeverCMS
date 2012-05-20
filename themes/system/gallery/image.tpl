<style type="text/css">
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
    <div class="sf_gallery_pred">{if $pred}<a href="/{$pred->getAddr()}">&laquo; Пред.</a>{else}&nbsp;{/if}</div>
    <div class="sf_gallery_next">{if $next}<a href="/{$next->getAddr()}">След. &raquo;</a>{else}&nbsp;{/if}</div>
</div>

<table>
    <tr>
        <td style="padding-right: 25px;">
            <a href="{$image->image}" class="gallery">
                <img src="{$image->middle}" alt="{$image->name}">
            </a>
        </td>
        <td style="vertical-align: top;">
            {str_replace("><br />",">",$image->description|nl2br)}
        </td>
    </tr>
</table>

<div class="clear"></div>
