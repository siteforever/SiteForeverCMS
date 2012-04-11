
<h2>Галерея: {$category.name} <a {href controller="gallery" action="editcat" id=$category.id}>{icon name="pencil" title="Править"}</a></h2>

<p><a {href url="admin/gallery"}>&laquo; Вернуться к списку категорий</a></p>
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
                    <a {href controller="gallery" action="editimg" id=$img.id} class="gallery_picture_edit">
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
                    <a {href controller="gallery" action="deleteImage"  id=$img.id} class="gallery_picture_delete">
                        {icon name="delete" title="Удалить"}
                    </a>
                </div>

                <div class="gallery_name" rel="{$img.id}">
                    {$img.name} {icon name="pencil" title="Править"}
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
    <a {href url="admin/gallery"}>&laquo; Вернуться к списку категорий</a>
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


<script type="text/javascript">
    $(function() {
        // Сортировочность
        $("#gallery").sortable({
            stop: function(event, ui) {
                var positions = [];
                $(this).find('li').each(function(){
                    positions.push($(this).attr('rel'));
                });
                $.post('/?route=admin/gallery', { positions: positions });
            }
        });
        $("#gallery").disableSelection();

        // Редактирование названия
        $('#gallery').find('div.gallery_name').click(function(){
            var val  = $(this).find('input').val();
            var name = $(this).find('input').attr('name');
            $(this).html("<input type='text' name='"+name+"' value='"+val+"' rel='"+val+"' />")
                .find('input').focus();
            $(this).find('input').blur(function(){ gallery_edit_name_apply(this) })
                .keypress(function( event ){
                    if (event.keyCode == '13') {
                        gallery_edit_name_apply( this );
                    }
                    if (event.keyCode == '27') {
                        gallery_edit_name_restore( this );
                    }
                });
        });


        var action  = '';
        // Правка данных об изображении
        $('a.gallery_picture_edit').each(function(){
            $(this).click(function(){
                action = $(this).attr('href');
                if ( 0 == $('#gallery_picture_edit').length ) {
                    $('<div id="gallery_picture_edit" />').appendTo('div.l-content');
                    $('#gallery_picture_edit').dialog({
                        autoOpen        : false,
                        modal           : true,
                        draggable       : false,
                        width           : 740,
                        title           : 'Правка информации',
                        open            : function() {
//                            wysiwyg.init();
                            return true;
                        },
                        buttons         : {
                            'Закрыть'   : function() {
                                $(this).dialog('close');
                                return true;
                            },
                            'Сохранить' : function() {
                                $(this).find('form').ajaxSubmit({
                                    url     : action,
                                    success : function(response) {
                                        $.showBlock(response);
                                        $.hideBlock(2000);
                                        return true;
                                    },
                                    error: function () {
                                        $.showBlock('Данные не сохранены');
                                        $.hideBlock(2000);
                                        return true;
                                    }
                                    //target  : '#gallery_picture_edit'
                                });
                                $(this).dialog('close');
                                $.showBlock('Отправка...');
                                return true;
                            }
                        }
                    }).hide();
                }

                $(window).bind('close', function(){ return false; });

                $.showBlock('Загрузка...');
                $.post($(this).attr('href'), function( data ){
                    $('#gallery_picture_edit').html(data).dialog('open');
                    $.hideBlock();
                    return true;
                });
                
                return false;
            });
        });


        // Удаление изображений
        $('a.gallery_picture_delete').click(function(){
            if ( confirm('Действительно хотите удалить?') ) {
                var href = $(this).attr('href');
                $.post( href, function(data) {
                    try {
                        if ( data.errno == '0' ) {
                            var elem = $('#gallery').find('li[rel='+data.id+']');
                            $(elem).fadeOut(500);
                            setTimeout(function(){
                                $(elem).remove();
                            }, 1000);
                        }
                    } catch(e) { alert(e.message) };
                }, 'json');
            }
            return false;
        });

        // Переключение активности изображения
        $('a.gallery_picture_switch').click(function(){
            $.post($(this).attr('href'), function(data){
                try {
                    if ( data.errno == '0' ) {
                        var elem = $('#gallery li[rel='+data.id+'] a.gallery_picture_switch' );
                        $(elem).html(data.img);
                    }
                    else {
                        alert( data.error );
                    }
                } catch(e) { alert(e.message) };
            }, 'json');
            return false;
        });

        // Создание мультизагрузки
        var reserv_img = $("div.newimage:last").clone();
        $("#add_image").click(function(){
            $(reserv_img).clone().appendTo("#load_images");
            return false;
        });
        $("#send_images").click(function(){
            $("#load_images").submit();
            return false;
        });
    });

    // Редактировать название и применить
    var gallery_edit_name_apply = function( obj )
    {
        var val  = $(obj).val();
        var rel  = $(obj).attr('rel');
        var name = $(obj).attr('name');
        var id = $(obj).parent().attr('rel');
        if ( id && val != rel ) {
            $.post('/?route=admin/gallery', { editimage: id, name: val });
        }
        $(obj).replaceWith(val+"{icon name="pencil" title="Править"} <input type='hidden' name='"+name+"' value='"+val+"' />");
    }

    // Редактировать название и отменить
    var gallery_edit_name_restore = function( obj )
    {
        var val  = $(obj).attr('rel');
        var name = $(obj).attr('name');
        $(obj).replaceWith(val+"{icon name="pencil" title="Править"} <input type='hidden' name='"+name+"' value='"+val+"' />");
    }
</script>