<table class="table table-striped table-bordered table-hover table-condensed">
<thead>
    <tr>
        <th width="30">&nbsp;</th>
        <th>{t cat="gallery"}Name{/t}</th>
    </tr>
</thead>
<tbody>
    {foreach from=$categories item="cat"}
    <tr>
        <td class="middle">#{$cat.id}</td>
        <td class="middle media">
            {a controller="gallery" action="list" id=$cat.id class="pull-left"}
                {thumb src=$cat.image width=57 height=57 class="media-object"}
            {/a}
            <div class="media-body">
                <h3>{a controller="gallery" action="list" id=$cat.id rel=$cat.id}{$cat.name}{/a}</h3>
                <small>{a controller="gallery" action="editcat" id=$cat.id class="editCat" title=$this->t('Edit')}
                    {icon name="pencil" title=$this->t('Edit')} {t}Edit{/t}{/a}
                {a controller="gallery" action="delcat" id=$cat.id class="do_delete" title=$this->t('Edit')}
                    {icon name="delete" title=$this->t('Delete')} {t}Delete{/t}{/a}</small>
            </div>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="6">Ничего не найдено</td>
    </tr>
    {/foreach}
</tbody>
</table>
{*
<p>
    {a class="realias btn" controller="gallery" action="realias" htmlTarget="_blank"}
        {icon name="arrow_refresh"} Пересчитать алиасы изображений
    {/a}
</p>
*}

{modal id="editCat"}
