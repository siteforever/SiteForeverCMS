<h2>Галерея: {$category.name} <a {href controller="gallery" action="editcat" id=$category.id}>{icon name="pencil" title="Править"}</a></h2>

<p>
    <a {href url="gallery/admin"} class="button">{icon name="arrow_left"} Вернуться к списку категорий</a>
</p>
<br />

<table>
<tr>
    <td>

        <ul id="gallery">
        {foreach from=$images item="img"}
        <li class="ui-state-default" rel="{$img.id}">

            <div style="width: {$category.thumb_width}px; height: {$category.thumb_height}px; background: #999;">
                <img rel="{$img.id}" src="{$img.thumb}"
                     title="{$img.name}" alt="{$img.name}"
                     style="width: {$category.thumb_width}px; height: {$category.thumb_height}px;" />
            </div>
            
            <div class="gallery_float_layer">
                <div class="gallery_control">
{*                    <a {href editimg=$img.id} class="gallery_picture_edit">*}
                    <a {href controller="gallery" action="edit" id=$img.id} class="gallery_picture_edit">
                        {icon name="picture_edit" title="Изменить"}
                    </a>
                    <a {href controller="gallery" action="admin" switchimg=$img.id} class="gallery_picture_switch">
                        {if $img.hidden}
                            {icon name="lightbulb_off" title="Выкл"}
                        {else}
                            {icon name="lightbulb" title="Вкл"}
                        {/if}
                    </a>
{*                    <a {href url="admin" controller="gallery" action="deleteImage"  id=$img.id} class="gallery_picture_delete">*}
                    <a {href controller="gallery" action="delete"  id=$img.id} class="gallery_picture_delete">
                        {icon name="delete" title="Удалить"}
                    </a>
                </div>

                <div class="gallery_name" rel="{$img.id}">
                    <span>{$img.name}</span> {icon name="pencil" title="Править"}
                    <input type="hidden" name="edit_names[{$img.id}]" class="gallery_name_field" value="{$img.name}" />
                </div>
            </div>
        </li>
        {/foreach}
        </ul>


    </td>
</tr>
<tr>
    <td>

{*        <form id="load_images" action="{link viewcat=$category.id}" method="post" enctype="multipart/form-data">*}
        <form id="load_images" action="{link}" method="post" enctype="multipart/form-data">
        <div class="newimage">
            Наименование: <input type="text" name="name[]" />
            Файл: <input type="file" name="image[]" />
        </div>
        </form>

        <br />
        <p>
            <button id="add_image">{icon name="picture_add"} Добавить</button> |
            <button id="send_images">{icon name="picture_save"} Отправить</button>
        </p>

    </td>
</tr>
</table>

<br />
<p>
    <a {href url="gallery/admin"} class="button">{icon name="arrow_left"} Вернуться к списку категорий</a>
</p>



<style type="text/css">
    #gallery {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }
    #gallery li.ui-state-default {
        margin: 0 20px 20px 0;
        padding: 20px 20px 45px 20px;
        float: left;
        width: {$category.thumb_width}px;
        height: {$category.thumb_height}px;
        font-size: 1em;
        text-align: center;
        overflow: hidden;
    }
    #gallery div.gallery_float_layer {
        position:   relative;
        width:      {$category.thumb_width}px;
        height:     {$category.thumb_height}px;
        margin-top: -{$category.thumb_height}px;
        font-size: 100%;
    }
    #gallery div.gallery_float_layer input {
        width: 80%;
    }
    #gallery div.gallery_control {
        height:     {$category.thumb_height}px;
        text-align:right;
        margin-bottom: 5px;
    }
    #gallery div.gallery_name {
        cursor: pointer;
        color: #000;
        height: 25px;
    }
</style>