<ul class="breadcrumb">
    <li>{a controller="gallery" action="admin"}{t cat="gallery"}Gallery{/t}{/a}</li>
    <li>{$category.name}</li>
</ul>

<ul id="gallery">
{foreach from=$images item="img"}
<li class="ui-state-default" rel="{$img.id}">

    <div style="width: 200px; height: 200px; background: #999;">
        {thumb src=$img.image width=200 height=200 alt=$img.name}
    </div>

    <div class="gallery_float_layer">
        <div class="gallery_control">
            <a {href controller="gallery" action="edit" id=$img.id} class="gallery_picture_edit">
                {icon name="picture_edit" title="Изменить"}
            </a>
            <a {href controller="gallery" action="switchimg" id=$img.id} class="gallery_picture_switch">
                {if $img.hidden}
                    {icon name="lightbulb_off" title="Выкл"}
                {else}
                    {icon name="lightbulb" title="Вкл"}
                {/if}
            </a>
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

<form id="load_images" action="{link url="gallery/list"}" method="post" enctype="multipart/form-data" class="well form-horizontal">
    <div class="newimage">
        <div class="row">
            <div class="col-xs-2">
                <label>Наименование: <input type="text" name="name[]" class="form-control input-sm"></label>
            </div>
            <div class="col-xs-2">
                <label>Файл: <input type="file" name="image[]" multiple></label>
            </div>
        </div>
    </div>
</form>

<p>
    <button id="add_image" class="btn btn-default">{icon name="picture_add"} Добавить</button>
    <button id="send_images" class="btn btn-default">{icon name="picture_save"} Отправить</button>
</p>

{modal id="editImage"}


<style type="text/css">
    #load_images {
        clear: both;
    }
    #gallery {
        list-style-type: none;
        margin: 0;
        padding: 0;
        clear: both;
    }
    #gallery li.ui-state-default {
        margin: 0 20px 20px 0;
        padding: 20px 20px 45px 20px;
        float: left;
        font-size: 1em;
        text-align: center;
        overflow: hidden;
    }
    #gallery div.gallery_float_layer {
        position:   relative;
        width:      200px;
        height:     200px;
        margin-top: -200px;
        font-size: 100%;
    }
    #gallery div.gallery_control {
        height:     200px;
        text-align:right;
        margin-bottom: 5px;
    }
    #gallery div.gallery_name {
        cursor: pointer;
        color: #000;
        height: 25px;
    }
</style>
