<table class="dataset fullWidth">
<tr>
    <th width="30"></th>
    <th width="50%">Наименование</th>
    <th width="25%">Средняя картинка</th>
    <th width="25%">Миниатюра</th>
</tr>
{foreach from=$categories item="cat"}
<tr>
    <td>#{$cat.id}</td>
    <td>
        <a {href controller="gallery" action="viewcat" id=$cat.id}>{$cat.name}</a>
{*        <a {href editcat=$cat.id}>{icon name="pencil" title="Править"}</a>*}
        <a {href controller="gallery" action="editcat" id=$cat.id}>{icon name="pencil" title="Править"}</a>
{*        <a {href delcat=$cat.id} class="do_delete">{icon name="delete" title="Удалить"}</a>*}
        <a {href  controller="gallery" action="delcat" id=$cat.id} class="do_delete">{icon name="delete" title="Удалить"}</a>
    </td>
    <td>{$cat.middle_width} x {$cat.middle_height}</td>
    <td>{$cat.thumb_width} x {$cat.thumb_height}</td>
</tr>
{foreachelse}
<tr>
    <td colspan="4">Ничего не найдено</td>
</tr>
{/foreach}
</table>
{*<p>{icon name="add"} <a {href newcat="1"}>Добавить категорию</a></p>*}
<p>
    <a {href controller="gallery" action="editcat"} class="button">
        {icon name="add"} Добавить категорию
    </a>
    <a class="realias button" {href controller="gallery" action="realias"}>
        {icon name="arrow_refresh"} Пересчитать алиасы изображений
    </a>
</p>