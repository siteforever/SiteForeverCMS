<table class="table table-striped table-bordered table-hover table-condensed">
<thead>
    <tr>
        <th width="30">&nbsp;</th>
        <th class="span1">&nbsp;</th>
        <th>{t cat="gallery"}Name{/t}</th>
    </tr>
</thead>
<tbody>
    {foreach from=$categories item="cat"}
    <tr>
        <td class="middle">#{$cat.id}</td>
        <td class="middle">
            {a controller="gallery" action="list" id=$cat.id}
                {thumb src=$cat.image width=57 height=57}{/a}
        </td>
        <td class="middle">
            <p><big>{a controller="gallery" action="list" id=$cat.id rel=$cat.id}{$cat.name}{/a}</big></p>
            <small>{a controller="gallery" action="editcat" id=$cat.id class="editCat" title=t('Edit')}
                {icon name="pencil" title=t('Edit')} {t}Edit{/t}{/a}
            {a controller="gallery" action="delcat" id=$cat.id class="do_delete" title=t('Edit')}
                {icon name="delete" title=t('Delete')} {t}Delete{/t}{/a}</small>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="6">Ничего не найдено</td>
    </tr>
    {/foreach}
</tbody>
</table>
<p>
    {a class="realias btn" controller="gallery" action="realias" htmlTarget="_blank"}
        {icon name="arrow_refresh"} Пересчитать алиасы изображений
    {/a}
</p>

{modal id="editCat"}