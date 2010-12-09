<?php /* Smarty version 2.6.26, created on 2010-10-14 18:52:44
         compiled from system:gallery/admin_images.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'href', 'system:gallery/admin_images.tpl', 2, false),array('function', 'icon', 'system:gallery/admin_images.tpl', 2, false),array('function', 'link', 'system:gallery/admin_images.tpl', 41, false),)), $this); ?>

<h2>Галерея: <?php echo $this->_tpl_vars['category']['name']; ?>
 <a <?php echo smarty_function_href(array('editcat' => $this->_tpl_vars['category']['id']), $this);?>
><?php echo smarty_function_icon(array('name' => 'pencil','title' => "Править"), $this);?>
</a></h2>

<p>
    <a <?php echo smarty_function_href(array('url' => "admin/gallery"), $this);?>
>&laquo; Вернуться к списку категорий</a>
</p>
<br />

<table>
<tr>
    <td>

        <ul id="gallery">
        <?php $_from = $this->_tpl_vars['images']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['img']):
?>
        <li class="ui-state-default" rel="<?php echo $this->_tpl_vars['img']['id']; ?>
">

            <img rel="<?php echo $this->_tpl_vars['img']['id']; ?>
" src="<?php echo $this->_tpl_vars['img']['thumb']; ?>
" title="<?php echo $this->_tpl_vars['img']['name']; ?>
" alt="<?php echo $this->_tpl_vars['img']['name']; ?>
" width="<?php echo $this->_tpl_vars['category']['thumb_width']; ?>
" height="<?php echo $this->_tpl_vars['category']['thumb_height']; ?>
" />

            <div class="gallery_float_layer">
                <div class="gallery_control">
                    <a <?php echo smarty_function_href(array('editimg' => $this->_tpl_vars['img']['id']), $this);?>
 class="gallery_picture_edit"><?php echo smarty_function_icon(array('name' => 'picture_edit','title' => "Изменить"), $this);?>
</a>
                    <a <?php echo smarty_function_href(array('switchimg' => $this->_tpl_vars['img']['id']), $this);?>
 class="gallery_picture_switch"><?php if ($this->_tpl_vars['img']['hidden']): ?><?php echo smarty_function_icon(array('name' => 'lightbulb_off','title' => "Выкл"), $this);?>
<?php else: ?><?php echo smarty_function_icon(array('name' => 'lightbulb','title' => "Вкл"), $this);?>
<?php endif; ?></a>
                    <a <?php echo smarty_function_href(array('delimg' => $this->_tpl_vars['img']['id']), $this);?>
 class="gallery_picture_delete"><?php echo smarty_function_icon(array('name' => 'delete','title' => "Удалить"), $this);?>
</a>
                </div>

                <div class="gallery_name" rel="<?php echo $this->_tpl_vars['img']['id']; ?>
">
                    <?php echo $this->_tpl_vars['img']['name']; ?>
 <?php echo smarty_function_icon(array('name' => 'pencil','title' => "Править"), $this);?>

                    <input type="hidden" name="edit_names[<?php echo $this->_tpl_vars['img']['id']; ?>
]" class="gallery_name_field" value="<?php echo $this->_tpl_vars['img']['name']; ?>
" />
                </div>
            </div>
        </li>
        <?php endforeach; endif; unset($_from); ?>
        </ul>


    </td>
</tr>
<tr>
    <td>

        <form id="load_images" action="<?php echo smarty_function_link(array('viewcat' => $this->_tpl_vars['category']['id']), $this);?>
" method="post" enctype="multipart/form-data">
        <div class="newimage">
            Наименование: <input type="text" name="name[]" />
            Файл: <input type="file" name="image[]" />
        </div>
        </form>

        <br />
        <p>
            <button id="add_image"><?php echo smarty_function_icon(array('name' => 'picture_add'), $this);?>
 Добавить</button> |
            <button id="send_images"><?php echo smarty_function_icon(array('name' => 'picture_save'), $this);?>
 Отправить</button>
        </p>

    </td>
</tr>
</table>

<br />
<p>
    <a <?php echo smarty_function_href(array('url' => "admin/gallery"), $this);?>
>&laquo; Вернуться к списку категорий</a>
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
        width: <?php echo $this->_tpl_vars['category']['thumb_width']; ?>
px;
        height: <?php echo $this->_tpl_vars['category']['thumb_height']; ?>
px;
        font-size: 1em;
        text-align: center;
        overflow: hidden;
    }
    #gallery div.gallery_float_layer {
        position:   relative;
        width:      <?php echo $this->_tpl_vars['category']['thumb_width']; ?>
px;
        height:     <?php echo $this->_tpl_vars['category']['thumb_height']; ?>
px;
        margin-top: -<?php echo $this->_tpl_vars['category']['thumb_height']; ?>
px;
        /*verflow:   hidden;*/
        font-size: 100%;
    }
    #gallery div.gallery_float_layer input {
        width: 80%;
    }
    #gallery div.gallery_control {
        height:     <?php echo $this->_tpl_vars['category']['thumb_height']; ?>
px;
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
<?php echo '
    $(function() {
        // Сортировочность
        $("#gallery").sortable({
            stop: function(event, ui) {
                var positions = [];
                $(this).find(\'li\').each(function(){
                    positions.push($(this).attr(\'rel\'));
                });
                $.post(\'/?route=admin/gallery\', {positions: positions});
            }
        });
        $("#gallery").disableSelection();

        // Эффекты
        /*
        $("#gallery").find(\'div.gallery_float_layer\').css(\'opacity\', \'0.4\');
        $("#gallery li").hover(function(){
            $(this).find(\'div.gallery_float_layer\').fadeTo(200, 1);
        },function(){
            $(this).find(\'div.gallery_float_layer\').fadeTo(200, 0.4);
        });
        */

        // Редактирование названия
        $(\'#gallery\').find(\'div.gallery_name\').click(function(){
            var val  = $(this).find(\'input\').val();
            var name = $(this).find(\'input\').attr(\'name\');
            $(this).html("<input type=\'text\' name=\'"+name+"\' value=\'"+val+"\' rel=\'"+val+"\' />")
                .find(\'input\').focus();
            $(this).find(\'input\').blur(function(){gallery_edit_name_apply(this);})
                .keypress(function( event ){
                    if (event.keyCode == \'13\') {
                        gallery_edit_name_apply( this );
                    }
                    if (event.keyCode == \'27\') {
                        gallery_edit_name_restore( this );
                    }
                });
        });


        // Правка данных об изображении
        $(\'a.gallery_picture_edit\').click(function(){
            var action = $(this).attr(\'href\');
            if ( $(\'#gallery_picture_edit\').length == 0 ) {
                $(\'<div id="gallery_picture_edit" />\').appendTo(\'div.l-content\');
                $(\'#gallery_picture_edit\').dialog({
                        autoOpen        : false,
                        modal           : true,
                        draggable       : true,
                        width           : 740,
                        title           : \'Правка информации\',
                        buttons         : {
                                \'Закрыть\'   : function() {
                                    $(this).dialog(\'close\');
                                },
                                \'Сохранить\' : function() {
                                    $(this).find(\'form\').ajaxSubmit({
                                        url     : action,
                                        target  : \'#gallery_picture_edit\'
                                        /*success : function(){
                                            //$(\'#gallery_picture_edit\').dialog(\'close\');
                                        }*/
                                    });
                                }
                        }
                    }).hide();
            };

            $(window).bind(\'close\', function(){return false;});

            $.showBlock(\'Загрузка...\');
            $.post($(this).attr(\'href\'), function( data ){
                $.hideBlock();
                $(\'#gallery_picture_edit\').html(data).dialog(\'open\');
            });
            return false;
        });


        // Удаление изображений
        $(\'a.gallery_picture_delete\').click(function(){

            if ( confirm(\'Действительно хотите удалить?\') ) {

                var href = $(this).attr(\'href\');
                $.post( href, function(data){
                    try {
                        if ( data.error == \'0\' ) {
                            var elem = $(\'#gallery li[rel=\'+data.id+\']\');
                            $(elem).fadeOut(500);
                            setTimeout(function(){
                                $(elem).remove();
                            }, 1000);
                        }
                    } catch(e) { alert(e.message) };
                }, \'json\');
                return false;
            }
        });

        // Переключение активности изображения
        $(\'a.gallery_picture_switch\').click(function(){
            $.post($(this).attr(\'href\'), function(data){
                try {
                    if ( data.error == \'0\' ) {
                        var elem = $(\'#gallery li[rel=\'+data.id+\'] a.gallery_picture_switch\' );
                        $(elem).html(data.img);
                    }
                } catch(e) {alert(e.message);};
            }, \'json\');
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
        var rel  = $(obj).attr(\'rel\');
        var name = $(obj).attr(\'name\');
        var id = $(obj).parent().attr(\'rel\');
        if ( id && val != rel ) {
            $.post(\'/?route=admin/gallery\', {editimage: id, name: val});
        }
        $(obj).replaceWith(val+"'; ?>
 <?php echo smarty_function_icon(array('name' => 'pencil','title' => "Править"), $this);?>
<?php echo '<input type=\'hidden\' name=\'"+name+"\' value=\'"+val+"\' />");
    }

    // Редактировать название и отменить
    var gallery_edit_name_restore = function( obj )
    {
        var val  = $(obj).attr(\'rel\');
        var name = $(obj).attr(\'name\');
        $(obj).replaceWith(val+"'; ?>
 <?php echo smarty_function_icon(array('name' => 'pencil','title' => "Править"), $this);?>
<?php echo '<input type=\'hidden\' name=\'"+name+"\' value=\'"+val+"\' />");
    }

'; ?>

</script>