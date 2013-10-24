/**
 * Управление галереей каталога
 * @trait
 * @author: keltanas
 * @link http://siteforever.ru
 */

define("admin/catalog/gallery",[
    "jquery",
    "i18n",
    "module/alert",
    "jui",
    "jquery/jquery.form"
], function( $, i18n, $alert ){
    return {
        "behavior" : {

            'input.a-gallery-file' : {
                "change" : function( event, node ) {
                    $alert('Загрузка',0,'div.a-gallery');
                    this.sortable("destroy");
                    $(node).parents('form').ajaxSubmit({
                        "url" : '/cataloggallery/upload',
                        "iframe" : true,
                        "success" :$.proxy(function( response ) {
                            $('div.a-gallery').replaceWith(response);
                            this.sortable();
                            $alert.close();
                        },this)
                    });
                }
            },

            // удалить изображение
            'a.del_gallery_image' : {
                "click" :  function( event, node ){
                    if ( ! confirm('Действительно хотите удалить изображение?') ) {
                        return false;
                    }
                    try {
                        $alert('Удаление',0,'div.a-gallery');
                        $.get( $(node).attr('href'), function ( response ) {
                            if ( response.error ) {
                                $alert(response.msg,0,'div.a-gallery');
                                return;
                            }
                            $('div.a-gallery').replaceWith(response.msg);
                            $alert.close();
                        },'json');
                    } catch (e) {
                        console.error(e.message );
                    }
                    return false;
                }
            },

            // сделать изображение главным
            'a.main_gallery_image' : {
                "click" : function( event, node ){
                    $alert('Сохранение',0,'div.a-gallery');
                    $.get($(node).attr('href'), function(response){
                        $('div.a-gallery:first').replaceWith(response);
                        $alert.close();
                    });
                    return false;
                }
            }
        },

        'sortable' : function(cmd){
            var $items = $('div.a-gallery>ul');
//            $items.sortable({'connectWith':'div.a-gallery>ul'});
            if (!cmd) {
                $items.sortable({
                    'update' : function( event, ui ) {
                        var positions = [];
                        $(this).find('li').each(function(){
                            positions.push($(this).data('id'));
                        });
//                    console.log('update', positions);
                        $.post($('div.a-gallery').data('url'), {'positions':positions});
                    }
                });
            } else {
                $items.sortable(cmd);
            }
            $items.disableSelection();
        }
    }
});
