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
    <div class="sf_gallery_pred">{if $pred}<a {href url=$pred->url}>&laquo; {$pred->name}</a>{else}&nbsp;{/if}</div>
    <div class="sf_gallery_next">{if $next}<a {href url=$next->url}>{$next->name} &raquo;</a>{else}&nbsp;{/if}</div>
</div>

<table>
    <tr>
        <td style="padding-right: 25px;">
            <a href="{$image->image}" class="gallery">
                <img src="{$image->middle}" alt="{$image->name}">
            </a>
        </td>
        <td style="vertical-align: top;">
            {$image->description}
        </td>
    </tr>
</table>

<div class="clear"></div>
