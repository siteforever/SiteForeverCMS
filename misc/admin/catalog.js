/**
 * Скрипты для каталога
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
define([
    "jquery",
    "siteforever",
    "module/modal",
    "i18n",
    "jui"
],function($, $s, Modal, i18n){

    return {
        "behavior" : {

            "a.edit" : {
                "click" : function( event, node ) {
                    $.get( $(node).attr('href'), $.proxy( function( response ){
                        this.Modal.title("Правка ").body( response).show();
                    }, this ) );
                    return false;
                }
            },

            // Switch on/off page
            "a.order_hidden" : {
                "click" : function ( event, node ) {
                    $.get( $( node ).attr('href'), function ( data ) {
                        $( node ).replaceWith( data );
                    });
                    return false;
                }
            },

            'a.do_delete' : {
                'click' : function( event, node ) {
                    try {
                        if (confirm(i18n('Want to delete?'))) {
                            $.post( $(node).attr('href'), $.proxy(function( response ){
                                if( ! response.error ) {
                                    $(node).parents('tr').remove();
                                }
                            }, this), 'json' );
                        }
                    } catch (e) {
                        console.error(e.message);
                    }
                    return false;
                }
            },

            '#catalog_save_position' : {
                "click" : function( event, node ){
                    $s.alert('Сохранение...');
                    var pos = [];
                    $('input.trade_pos').each( function(){
                        pos.push( { key: $(this).attr('rel'), val: $(this).val() } );
                    });
                    $.post( $( node ).attr('href'), { "save_pos": pos }, function( data ){
                        document.location.reload();
                    });
                }
            },

            'a.catalog_switch' : {
                "click" : function( event, node ){
                    $.post( $( node ).attr('href'), function( data ) {
                        if ( data.error == '0' ) {
                            $( node ).attr( 'href', data.href ).html( data.img );
                        }
                    }, 'json');
                    return false;
                }
            },

            // Фильтрация товаров
            '#goods_filter_select' : {
                "click" : function( event, node ){
                    var href = window.location.href;
                    href = href.replace(/\/$/, '').replace(/(\/goods_filter=[^\/]+?)*$/, '');
                    if ( $('#goods_filter').val() != '' ) {
                        href += '/' + 'goods_filter=' + $('#goods_filter').val();
                    }
                    window.location.href = href;
                }
            },

            // Отмена фильтрации
            '#goods_filter_cancel' : {
                "click" : function( event, node ){
                    var href = window.location.href;
                    href = href.replace(/\/$/, '').replace(/(\/goods_filter=[^\/]+?)*$/, '');
                    window.location.href = href;
                }
            },

            /**
             * GALLERY
             */
            'a.gallery-item-add' : {
                "click" : function( event, node ){
                    if ( ! $('#gallery_dialog').length ) {
                        $('<div id="gallery_dialog"/>').appendTo('body')
                            .dialog( this.galleryUploadDialog )
                            .dialog( "option", "close", $.proxy( function() {
                                $.get( $('div.a-gallery').data('url'), function( response ) {
                                    $('div.a-gallery').replaceWith( response );
                                });
                            }, this ));
                    }
                    $('#gallery_dialog').html($s.i18n('Loading...')).dialog('open');
                    $.get( $( node ).attr('href'), $.proxy( function ( response ) {
                        $('#gallery_dialog').html( response );
                    }, this) );
                    return false;
                }
            },

            // удалить изображение
            'a.del_gallery_image' : {
                "click" :  function( event, node ){
                    if ( ! confirm('Действительно хотите удалить изображение?') ) {
                        return false;
                    }
                    try {
                        $s.alert('Удаление');
                        $.get( $(node).attr('href'), function ( response ) {
                            if ( response.error ) {
                                $s.alert(response.msg);
                                return;
                            }
                            $('div.a-gallery').replaceWith(response.msg);
                            $s.alert.close();
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
                    $.get($(node).attr('href'), function(response){
                        $('div.a-gallery:first').replaceWith(response);
                    });
                    return false;
                }
            }
        },

        "init" : function(){
            this.Modal = new Modal('CatalogEdit');
        },


        "galleryUploadDialog" : {
            autoOpen : false,
            title : "Добавить изображения",
            buttons : {
                "Загрузить" : function() {
                    $(this).find('form').ajaxSubmit({
                        target : '#gallery_dialog',
                        success : function() {
                            setTimeout($.proxy(function(){
                                $(this).dialog('close');
                            },this), 1000);
                        }
                    });
                },
                "Закрыть" : function() {
                    $(this).dialog('close');
                }
            }
        }

    }
});