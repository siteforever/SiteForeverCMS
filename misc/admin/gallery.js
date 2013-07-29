/**
 * Управление админкой галереи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
define("admin/gallery", [
    "jquery",
    "wysiwyg",
    "module/modal",
    "i18n",
    "module/alert",
    "siteforever",
    "jui"
], function($, wysiwyg, Modal, i18n, $alert){
    return {
        "behavior" : {
            "#gallery div.gallery_name" : {
                // Редактирование названия
                "click" : function ( event, node ) {
                    event.stopPropagation();
                    var val  = $(node).find('input').val(),
                        name = $(node).find('input').attr('name');

                    $(node).find('span').hide().next().hide();

                    $("<input type='text' name='"+name+"' value='"+val+"' data-old='"+val+"'>").prependTo(node).focus();

                    $('input:text', node)
                        .blur( this.nameApply )
                        .click(function(event){ return false; })
                        .keypress($.proxy( function( event ){
                            if (event.keyCode == '13') {
                                this.nameApply.call( $('input:text', node)[0] );
                            }
                            if (event.keyCode == '27') {
                                this.nameCancel.call( $('input:text', node)[0] );
                            }
                        }, this));
                    return false;
                }
            },
            "a.do_delete" : {
                "click" : function( event, node ) {
                    return confirm(i18n('Want to delete?'));
                }
            },
            "a.gallery_picture_edit" : {
                "click" : function( event, node ) {
                    $.post( $(node).attr('href'), $.proxy( function( response ){
                        this.editImage.title('Правка информации').body( response ).show();
                    }, this ));
                    return false;
                }
            },
            "a.gallery_picture_delete" : {
                // Удаление изображений
                "click" : function( event, node ) {
                    if ( confirm('Действительно хотите удалить?') ) {
                        $.post( $(node).attr('href'), function(data) {
                            try {
                                if ( data.error == '0' ) {
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
                }
            },
            "a.gallery_picture_switch" : {
                // Переключение активности изображения
                "click" : function( event, node ) {
                    $.post( $(node).attr('href'), function(response){
                        try {
                            if ( response.error == '0' && response.id ) {
                                $('#gallery li[rel=' + response.id + '] a.gallery_picture_switch' ).html( response.img );
                            } else {
                                $alert( response.msg );
                            }
                        } catch(e) { $alert(e.message) }
                    }, 'json');
                    return false;
                }
            },
            "#add_image" : {
                "click" : function( event, node ) {
                    $(this.reservImg).clone().appendTo("#load_images");
                    return false;
                }
            },
            "#send_images" : {
                "click" : function( event, node ) {
                    $("#load_images").submit();
                    return false;
                }
            },

            "a.editCat" : {
                "click" : function( event, node ) {
                    $.get( $(node).attr('href'), $.proxy( function( response ) {
                        this.editCat.title( $(node).attr('title') ).body( response ).show();
                    }, this ));
                    return false;
                }
            }
        },

        "init" : function() {

            // Сортировка
            $("#gallery").sortable({
                update: function(event, ui) {
                    var positions = [];
                    $(this).find('li').each(function(){
                        positions.push($(this).attr('rel'));
                    });
                    $.post('/?route=gallery/admin', { positions: positions });
                }
            }).disableSelection();

            this.editImage = new Modal('editImage');
            this.editImage.onSave( this.onSave );

            // Создание мультизагрузки
            this.reservImg = $("div.newimage:last").clone();

            // Управление списком галерей
            this.editCat = new Modal('editCat');
            this.editCat.onSave( this.onSave );
        },

        /**
         * Редактировать название и применить
         */
        nameApply: function () {
            var val = $( this ).val(),
                old = $( this ).attr( 'data-old' ),
                id = $( this).parent().attr( 'rel' );
            if ( id && val != old ) {
                $.post( '/?route=gallery/admin', { editimage: id, name: val } );
            }
            $( this ).parent().find( 'span' ).text( val ).show().next().show().next().val( val );
            $( this ).remove();
        },

        /**
         * Редактировать название и отменить
         */
        nameCancel: function () {
            var val = $( this ).attr( 'data-old' );
            $( this ).parent().find( 'span' ).text( val ).show().next().show();
            $( this ).remove();
        },

        /**
         * Созранить данные об изображении
         */
        onSave: function() {
            $('form', this.domnode).ajaxSubmit({
                dataType:"json",
                success: $.proxy(function( response ){
                    if ( ! response.error ) {
                        this.msgSuccess( response.msg, 1500);
                        var domName = $('#gallery').find('li[rel='+response.id+']').find('div.gallery_name');
                        $('span', domName).text(response.name);
                        $('input.gallery_name_field', domName).val(response.name);
                    } else {
                        this.msgError( response.msg );
                    }
                },this)
            });
        },

        /**
         * Сохранить данные об категории
         */
        onSaveCat: function() {
            $('form', this.domnode).ajaxSubmit({
                dataType:"json",
                success: $.proxy(function( response ){
                    if ( ! response.error ) {
                        this.msgSuccess( response.msg, 1500);
                        $('a[rel='+response.id+']').text( response.name );
                    } else {
                        this.msgError( response.msg );
                    }
                },this)
            });
        }
    }
});
