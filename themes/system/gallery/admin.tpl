<table class="dataset fullWidth">
<tr>
    <th width="30"></th>
    <th></th>
    <th>Наименование</th>
    <th>Средняя картинка</th>
    <th>Миниатюра</th>
    <th></th>
</tr>
{foreach from=$categories item="cat"}
<tr>
    <td class="middle">#{$cat.id}</td>
    <td class="middle">
        <img width="100" src="{$cat.thumb}" alt=""/>
    </td>
    <td class="middle">
        <a {href controller="gallery" action="list" id=$cat.id}>{$cat.name}</a>
    </td>
    <td class="middle">{$cat.middle_width} x {$cat.middle_height}</td>
    <td class="middle">{$cat.thumb_width} x {$cat.thumb_height}</td>
    <td class="middle">
        {a controller="gallery" action="editcat" id=$cat.id}{icon name="pencil" title="Править"}{/a}
        {a controller="gallery" action="delcat" id=$cat.id class="do_delete"}{icon name="delete" title="Удалить"}{/a}
    </td>
</tr>
{foreachelse}
<tr>
    <td colspan="6">Ничего не найдено</td>
</tr>
{/foreach}
</table>
<p>
    <a class="realias button" {href controller="gallery" action="realias"}>
        {icon name="arrow_refresh"} Пересчитать алиасы изображений
    </a>
</p>